// Prüft die Seite *Design → Lindenzauber* im Backend: die Einrichtungs-Liste
// und das Feld für den Text unter /llms.txt.
//
// Das Backend wird von den anderen Werkzeugen nicht angefasst – dabei ist es
// der Teil, den Brigitta täglich sieht. Hier wird deshalb wirklich geklickt:
// Text einsetzen, bearbeiten, speichern, wieder zurücksetzen, und jedes Mal
// nachgesehen, was unter /llms.txt tatsächlich ausgeliefert wird.
//
//   node tools/admin-check.mjs <url> <benutzer> <passwort> [bild-ordner]
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';

const [basis = 'http://127.0.0.1:8321', benutzer = 'admin', passwort = 'lindenzauber', bilder] =
	process.argv.slice(2);
const b = await chromium.launch();
const s = await b.newPage({ viewport: { width: 1400, height: 1000 } });
let fehler = 0;
const sagen = (t, ok, extra) => { if (!ok) fehler++; console.log((ok ? 'OK    ' : 'FEHLER') + '  ' + t + (extra ? '  → ' + extra : '')); };

// Anmelden
await s.goto(basis + '/wp-login.php', { waitUntil: 'networkidle' });
await s.fill('#user_login', benutzer);
await s.fill('#user_pass', passwort);
await s.click('#wp-submit');
await s.waitForLoadState('networkidle');
sagen('Angemeldet', s.url().includes('wp-admin'));

// Hinweis im Backend
const hinweis = await s.evaluate(() => {
  const n = [...document.querySelectorAll('.notice')].find(x => /Lindenzauber/.test(x.textContent));
  return n ? n.textContent.replace(/\s+/g, ' ').trim() : null;
});
sagen('Hinweis erscheint', !!hinweis, hinweis);

// Menüeintrag unter Design
await s.goto(basis + '/wp-admin/themes.php', { waitUntil: 'networkidle' });
const eintrag = await s.evaluate(() => {
  const a = [...document.querySelectorAll('#menu-appearance a')].find(x => /Lindenzauber/.test(x.textContent));
  return a ? a.textContent.replace(/\s+/g, ' ').trim() : null;
});
sagen('Eintrag unter Design', !!eintrag, eintrag);

// Die Seite selbst
await s.goto(basis + '/wp-admin/themes.php?page=lindenzauber', { waitUntil: 'networkidle' });
const liste = await s.evaluate(() => [...document.querySelectorAll('.lz-schritte tbody tr')].map(tr => ({
  fertig: tr.querySelector('td span[aria-hidden]')?.textContent.trim() === '✔',
  titel: tr.querySelector('strong')?.textContent.trim(),
})));
sagen('Checkliste vorhanden', liste.length >= 10, liste.length + ' Punkte');
console.log('        ' + liste.map(z => (z.fertig ? '✔ ' : '• ') + z.titel).join('\n        '));

// Platzhalter des Textfeldes zeigt den erzeugten Text
const platz = await s.getAttribute('textarea[name="lz_llms_text"]', 'placeholder');
sagen('Platzhalter zeigt den automatischen Text', !!platz && platz.includes('## Termine'), platz ? platz.slice(0, 40) + '…' : '–');
sagen('Feld ist leer (automatisch)', (await s.inputValue('textarea[name="lz_llms_text"]')) === '');

// Automatischen Text einsetzen
await s.click('button[name="lz_vorschlag"]');
await s.waitForLoadState('networkidle');
const eingesetzt = await s.inputValue('textarea[name="lz_llms_text"]');
sagen('Automatischer Text wird eingesetzt', eingesetzt.includes('## Kontakt'), eingesetzt.length + ' Zeichen');

// Bearbeiten und speichern
await s.fill('textarea[name="lz_llms_text"]', eingesetzt.replace('## Hinweise', '## Von Brigitta ergänzt\n\nBitte Kuchenspenden vorher anmelden.\n\n## Hinweise'));
await s.click('button.button-primary');
await s.waitForLoadState('networkidle');
sagen('Speichern bestätigt', (await s.textContent('.notice')).includes('Gespeichert'));

// Wird der eigene Text ausgeliefert?
const eigen = await (await s.request.get(basis + '/llms.txt')).text();
sagen('Eigener Text wird ausgeliefert', eigen.includes('Kuchenspenden'));
sagen('Seite meldet „eigener Text"', (await s.textContent('.lz-einrichtung')).includes('eigener Text'));

// Zurücksetzen
await s.click('button[name="lz_zuruecksetzen"]');
await s.waitForLoadState('networkidle');
const zurueck = await (await s.request.get(basis + '/llms.txt')).text();
sagen('Zurücksetzen stellt den automatischen Text her', !zurueck.includes('Kuchenspenden') && zurueck.includes('## Termine'));
sagen('Feld wieder leer', (await s.inputValue('textarea[name="lz_llms_text"]')) === '');

// Adressregeln erneuern
await s.click('button[name="lz_regeln"]');
await s.waitForLoadState('networkidle');
sagen('Adressregeln erneuern bestätigt', (await s.textContent('.notice')).includes('Adressregeln'));
sagen('llms.txt danach noch da', (await (await s.request.get(basis + '/llms.txt')).text()).includes('# Lindenzauber'));

/* -------------------------------------------------------------------------
   Die Website mit der Werkzeugleiste von WordPress.

   Angemeldet legt WordPress eine feste Leiste über die Seite. Der Kopfbereich
   klebt ebenfalls oben – ohne Versatz schiebt sich die Leiste beim Scrollen
   über das Menü. Aufgefallen ist das erst auf der echten Website, weil alle
   bisherigen Prüfungen abgemeldet liefen.
   ------------------------------------------------------------------------- */
for (const breite of [1400, 900, 700, 390]) {
	await s.setViewportSize({ width: breite, height: 900 });
	await s.goto(basis + '/programm/', { waitUntil: 'networkidle' });
	await s.evaluate(() => window.scrollTo({ top: 900, behavior: 'instant' }));
	await s.waitForTimeout(350);

	const lage = await s.evaluate(() => {
		const leiste = document.querySelector('#wpadminbar');
		const kopf = document.querySelector('.site-header');

		if (!leiste || !kopf) return null;

		const l = leiste.getBoundingClientRect();
		const k = kopf.getBoundingClientRect();
		const punkte = [...document.querySelectorAll('.main-nav a, .nav-toggle, .site-brand')]
			.filter((el) => el.getBoundingClientRect().width > 0)
			.map((el) => {
				const r = el.getBoundingClientRect();
				return { name: el.textContent.replace(/\s+/g, ' ').trim().slice(0, 18) || el.className, oben: r.top };
			});

		return {
			leiste: { unten: l.bottom, fest: getComputedStyle(leiste).position === 'fixed' },
			kopf: { oben: k.top },
			verdeckt: punkte.filter((p) => p.oben < l.bottom && l.bottom > 0),
		};
	});

	if (!lage) {
		sagen(`${breite} px: Werkzeugleiste vorhanden`, false, 'nicht gefunden');
		continue;
	}

	// Unter 600 px lässt WordPress die Leiste mitscrollen – dann ist sie weg.
	const stoert = lage.leiste.fest && lage.leiste.unten > 0;

	sagen(
		`${breite} px: Kopfbereich liegt unter der Werkzeugleiste`,
		!stoert || lage.kopf.oben >= lage.leiste.unten - 1,
		`Leiste endet bei ${Math.round(lage.leiste.unten)}, Kopf beginnt bei ${Math.round(lage.kopf.oben)}`
	);
	sagen(
		`${breite} px: nichts vom Menü wird verdeckt`,
		lage.verdeckt.length === 0,
		lage.verdeckt.map((p) => p.name).join(', ')
	);
}

// Und die Gegenprobe: abgemeldet darf sich nichts verschoben haben.
const gast = await b.newContext();
const g = await gast.newPage({ viewport: { width: 1400, height: 900 } });
await g.goto(basis + '/programm/', { waitUntil: 'networkidle' });
await g.evaluate(() => window.scrollTo({ top: 900, behavior: 'instant' }));
await g.waitForTimeout(350);
const ohne = await g.evaluate(() => ({
	leiste: !!document.querySelector('#wpadminbar'),
	kopf: Math.round(document.querySelector('.site-header').getBoundingClientRect().top),
}));
sagen('abgemeldet: keine Werkzeugleiste', !ohne.leiste);
sagen('abgemeldet: Kopfbereich klebt weiter ganz oben', ohne.kopf === 0, ohne.kopf + ' px');
await gast.close();

await s.setViewportSize({ width: 1400, height: 1000 });

if (bilder) await s.screenshot({ path: bilder + '/einrichtung.png', fullPage: true });
await b.close();
console.log(fehler === 0 ? '\nEinrichtungsseite in Ordnung.' : `\n${fehler} Fehler.`);
process.exit(fehler ? 1 : 0);
