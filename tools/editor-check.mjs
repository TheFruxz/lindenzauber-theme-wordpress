// Prüft im echten Block-Editor, ob alle Blöcke sauber erkannt werden.
//
// Das ist der eigentliche Abnahmetest für „nie wieder Code-Editor“:
// Wenn Gutenberg einen Block nicht wiedererkennt, zeigt es die Warnung
// „Dieser Block enthält unerwarteten oder ungültigen Inhalt“ – und genau
// dann müsste jemand doch wieder in den Code-Editor.
//
//   node tools/editor-check.mjs <basis-url> <benutzer> <passwort> <seiten-id...>
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';

const [basis, benutzer, passwort, ...ids] = process.argv.slice(2);

if (!basis || !ids.length) {
	console.error('Aufruf: node tools/editor-check.mjs <basis-url> <benutzer> <passwort> <id...>');
	process.exit(1);
}

const browser = await chromium.launch();
const seite = await browser.newPage({ viewport: { width: 1600, height: 1000 } });

await seite.goto(`${basis}/wp-login.php`, { waitUntil: 'domcontentloaded' });
await seite.fill('#user_login', benutzer);
await seite.fill('#user_pass', passwort);
await Promise.all([seite.waitForNavigation({ waitUntil: 'domcontentloaded' }), seite.click('#wp-submit')]);

let fehler = 0;

for (const id of ids) {
	await seite.goto(`${basis}/wp-admin/post.php?post=${id}&action=edit`, { waitUntil: 'domcontentloaded' });

	// Auf den Editor-Rahmen warten.
	await seite.waitForSelector('iframe[name="editor-canvas"], .block-editor-block-list__layout', { timeout: 45000 });
	await seite.waitForTimeout(3500);

	const rahmen = seite.frames().find((f) => f.name() === 'editor-canvas') || seite.mainFrame();

	const ergebnis = await rahmen.evaluate(() => {
		const warnungen = [...document.querySelectorAll('.block-editor-warning')].map((w) =>
			(w.textContent || '').trim().slice(0, 120)
		);
		const roh = [...document.querySelectorAll('.wp-block-html, .block-library-html__edit')].length;
		const bloecke = document.querySelectorAll('.block-editor-block-list__block').length;
		return { warnungen, roh, bloecke };
	});

	const titel = await seite.title();
	const status = ergebnis.warnungen.length === 0 && ergebnis.roh === 0 ? 'OK ' : 'FEHLER';

	if (status === 'FEHLER') fehler += 1;

	console.log(
		`${status}  Seite ${id}  Blöcke: ${String(ergebnis.bloecke).padStart(3)}  ` +
			`Warnungen: ${ergebnis.warnungen.length}  Roh-HTML-Blöcke: ${ergebnis.roh}  – ${titel.split(' ‹ ')[0]}`
	);

	ergebnis.warnungen.forEach((w) => console.log('        ! ' + w));
}

await browser.close();
console.log(fehler === 0 ? '\nAlle Seiten sind im visuellen Editor vollständig bearbeitbar.' : `\n${fehler} Seite(n) mit Problemen.`);
process.exit(fehler === 0 ? 0 : 1);
