// Prüft den Seiten-Import unter *Design → Lindenzauber*.
//
// Hier wird nicht die PHP-Funktion aufgerufen, sondern der Weg gegangen, den
// Cedric geht: anmelden, Paket hochladen, Vorschau lesen, bestätigen. Danach
// wird in der Datenbank nachgesehen, ob wirklich das passiert ist, was in der
// Vorschau stand – und was ausdrücklich *nicht* passieren durfte.
//
// Läuft absichtlich auf einer frisch aufgesetzten Instanz, also vor
// wp-test-inhalte.sh. Damit ist es der echte Erstfall.
//
//   node tools/import-check.mjs <url> <benutzer> <passwort> <wp-ordner> <wp-cli.phar> <paket.zip>
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import { execFileSync } from 'node:child_process';

const [
	basis = 'http://127.0.0.1:8321',
	benutzer = 'admin',
	passwort = 'lindenzauber',
	ordner,
	cli,
	paket,
] = process.argv.slice(2);

if (!ordner || !cli || !paket) {
	console.log('Aufruf: node tools/import-check.mjs <url> <benutzer> <passwort> <wp-ordner> <wp-cli.phar> <paket.zip>');
	process.exit(2);
}

const wp = (...args) =>
	execFileSync('php', [cli, '--path=' + ordner, '--allow-root', ...args], { encoding: 'utf8' }).trim();

// Für Abfragen, bei denen "gibt es nicht" eine mögliche Antwort ist. WP-CLI
// bricht dann mit einem Fehler ab – und eine gescheiterte Prüfung soll als
// Befund dastehen, nicht das ganze Werkzeug mitreißen.
const wpLeise = (...args) => {
	try {
		return wp(...args);
	} catch (nichts) {
		return '';
	}
};

let fehler = 0;
const sagen = (t, ok, extra) => {
	if (!ok) fehler++;
	console.log((ok ? 'OK    ' : 'FEHLER') + '  ' + t + (extra ? '  → ' + extra : ''));
};

// Zwei Seiten, die es vorher schon gab: eine, die weichen soll, und eine, die
// das Paket ausdrücklich in Ruhe lässt. Beides muss der Import auseinanderhalten.
const veraltet = wp('post', 'create', '--post_type=page', '--post_title=Lindenzauber 2025',
	'--post_name=veraltet', '--post_status=publish', '--porcelain',
	'--post_content=<!-- wp:paragraph --><p>Alter Stand.</p><!-- /wp:paragraph -->');
wp('post', 'create', '--post_type=page', '--post_title=Datenschutz', '--post_name=datenschutz',
	'--post_status=publish', '--porcelain',
	'--post_content=<!-- wp:paragraph --><p>Juristisch erzeugt, bleibt unangetastet.</p><!-- /wp:paragraph -->');

const b = await chromium.launch();
const s = await b.newPage({ viewport: { width: 1400, height: 1200 } });

await s.goto(basis + '/wp-login.php', { waitUntil: 'networkidle' });
await s.fill('#user_login', benutzer);
await s.fill('#user_pass', passwort);
await s.click('#wp-submit');
await s.waitForLoadState('networkidle');

const seite = basis + '/wp-admin/themes.php?page=lindenzauber';

/** Ein Paket hochladen und die Vorschau auslesen. */
async function hochladen(datei) {
	await s.goto(seite, { waitUntil: 'networkidle' });
	await s.setInputFiles('input[name="lz_paket"]', datei);
	await s.click('form[enctype] button[type="submit"]');
	await s.waitForLoadState('networkidle');

	return s.evaluate(() => ({
		meldung: document.querySelector('.notice-error')?.textContent.replace(/\s+/g, ' ').trim() || null,
		vorhaben: [...document.querySelectorAll('.lz-vorhaben tbody tr')].map((tr) => ({
			was: tr.querySelector('td:first-child strong')?.textContent.trim(),
			titel: tr.querySelector('td:nth-child(2) strong')?.textContent.trim(),
		})),
		fremde: [...document.querySelectorAll('.lz-fremde tbody tr')].map((tr) => ({
			slug: tr.querySelector('.description')?.textContent.trim(),
			titel: tr.querySelector('label strong')?.textContent.trim(),
		})),
		angehakt: [...document.querySelectorAll('.lz-fremde input[type="checkbox"]')].filter((i) => i.checked).length,
	}));
}

/* ------------------------------------------- 1. Falsches Paket wird erkannt */
const falsch = await hochladen('dist/lindenzauber.zip');
sagen(
	'Theme-Paket statt Inhalte-Paket wird abgewiesen',
	!!falsch.meldung && falsch.meldung.includes('seiten.json') && falsch.vorhaben.length === 0,
	falsch.meldung ? falsch.meldung.slice(0, 70) + '…' : 'keine Meldung'
);

/* ---------------------------------------------------- 2. Vorschau auf leer */
const erste = await hochladen(paket);
const neu = erste.vorhaben.filter((v) => v.was === 'neu');
sagen('Vorschau listet die Seiten des Pakets', erste.vorhaben.length === 8, erste.vorhaben.length + ' Einträge');
sagen('auf einer leeren Website ist alles neu', neu.length === erste.vorhaben.length);
sagen('nichts fehlt im Paket', !erste.vorhaben.some((v) => v.was === 'fehlt'));

const fremdslugs = erste.fremde.map((f) => f.slug);
sagen('fremde Seite wird zum Stilllegen angeboten', fremdslugs.includes('/veraltet/'), fremdslugs.join(' '));
sagen('Datenschutz wird geschützt', !fremdslugs.includes('/datenschutz/'));
sagen('nichts ist vorangehakt', erste.angehakt === 0);

/* ------------------------------------------------------- 3. Durchführen */
for (const kasten of await s.locator('.lz-fremde input[type="checkbox"]').all()) {
	await kasten.check();
}
await s.click('form:has(input[name="lz_import_los"]) button.button-primary');
await s.waitForLoadState('networkidle');

const bericht = (await s.textContent('.notice')).replace(/\s+/g, ' ').trim();
sagen('Import meldet sich fertig', bericht.includes('Import abgeschlossen'), bericht.slice(0, 90) + '…');

const zustand = () =>
	JSON.parse(
		wp(
			'post',
			'list',
			'--post_type=page',
			'--post_status=publish,draft',
			'--fields=ID,post_name,post_status,post_title',
			'--format=json'
		)
	);

let seiten = zustand();
const finde = (slug) => seiten.find((p) => p.post_name === slug);

for (const slug of ['startseite', 'programm', 'die-erzaehlenden', 'ueber-lindenzauber', 'sponsoren', 'kontakt', 'impressum']) {
	sagen('Seite /' + slug + '/ ist veröffentlicht', finde(slug)?.post_status === 'publish');
}
sagen('Fotogalerie bleibt Entwurf', finde('fotogalerie')?.post_status === 'draft');

const leer = seiten
	.filter((p) => p.post_status === 'publish' && p.post_name !== 'datenschutz')
	.filter((p) => wp('post', 'get', String(p.ID), '--field=post_excerpt') === '');
sagen('jede eingespielte Seite hat einen Auszug', leer.length === 0, leer.map((p) => p.post_name).join(', '));

const vorn = Number(wp('option', 'get', 'page_on_front'));
sagen(
	'Startseite ist festgelegt',
	wp('option', 'get', 'show_on_front') === 'page' && vorn === finde('startseite')?.ID,
	'#' + vorn
);

sagen(
	'Förderer-Band ist gefüllt',
	wp('eval', 'echo is_active_sidebar( "lz-foerderband" ) ? "ja" : "nein";') === 'ja'
);

const still = finde('alt-veraltet');
sagen('fremde Seite ist stillgelegt', !!still && still.post_status === 'draft', still ? still.post_title : '–');
sagen(
	'alter Adressname ist gemerkt',
	wpLeise('post', 'meta', 'get', String(veraltet), '_lz_alter_slug') === 'veraltet'
);
sagen(
	'stillgelegte Seite ist nicht mehr aufrufbar',
	(await s.request.get(basis + '/veraltet/')).status() === 404
);
sagen('Datenschutz blieb unberührt', finde('datenschutz')?.post_status === 'publish');

// Die Blockangaben müssen den Weg durch WordPress unbeschadet überstehen –
// ohne die HTML-Kommentare wäre im Editor jeder Block kaputt.
const inhalt = wpLeise('post', 'get', String(finde('startseite')?.ID), '--field=post_content');
sagen(
	'Blockangaben sind erhalten geblieben',
	inhalt.includes('<!-- wp:group') && inhalt.includes('<!-- /wp:group -->'),
	inhalt.length + ' Zeichen'
);

/* -------------------------------------- 4. Zweiter Lauf ändert die Adressen nicht */
const vorher = Object.fromEntries(seiten.map((p) => [p.post_name, p.ID]));
const zweite = await hochladen(paket);
sagen(
	'zweiter Lauf ersetzt statt neu anzulegen',
	zweite.vorhaben.length === 8 && zweite.vorhaben.every((v) => v.was === 'wird ersetzt'),
	[...new Set(zweite.vorhaben.map((v) => v.was))].join(', ')
);
sagen(
	'stillgelegte Seite taucht nicht wieder auf',
	!zweite.fremde.some((f) => f.slug === '/alt-veraltet/')
);

await s.click('form:has(input[name="lz_import_los"]) button.button-primary');
await s.waitForLoadState('networkidle');

seiten = zustand();
const gewandert = seiten.filter((p) => vorher[p.post_name] && vorher[p.post_name] !== p.ID);
sagen('Seiten behalten ihre Kennung', gewandert.length === 0, gewandert.map((p) => p.post_name).join(', '));
sagen(
	'der alte Stand steht unter Revisionen',
	Number(wp('post', 'list', '--post_type=revision', '--post_parent=' + vorn, '--format=count')) > 0
);
sagen(
	'Förderer-Band wird nicht verdoppelt',
	Number(wp('eval', 'echo count( (array) get_option( "sidebars_widgets" )["lz-foerderband"] );')) === 1
);

// Nach jedem Lauf darf im Temp-Verzeichnis nichts liegenbleiben. Genau das
// war einmal nicht so: ausgepackt wird beim Hochladen, weggeräumt erst nach
// dem Bestätigen – zwei Aufrufe, und im zweiten fehlte die Dateisystem-Anmeldung.
sagen(
	'keine ausgepackten Pakete liegengeblieben',
	wp('eval', 'echo count( (array) glob( trailingslashit( get_temp_dir() ) . "lz-import-*", GLOB_ONLYDIR ) );') === '0'
);

/* ------------------------------------------------------------ Aufräumen */
for (const p of zustand()) {
	if (p.ID === veraltet || /^(alt-)?(veraltet|datenschutz|beispielseite|sample-page)$/.test(p.post_name)) {
		wpLeise('post', 'delete', String(p.ID), '--force');
	}
}

await b.close();
console.log(fehler === 0 ? '\nSeiten-Import in Ordnung.' : `\n${fehler} Fehler.`);
process.exit(fehler ? 1 : 0);
