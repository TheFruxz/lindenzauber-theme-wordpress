// Prüft jede Datei in inhalte/ gegen Gutenberg selbst.
//
// Gutenberg vergleicht beim Öffnen die gespeicherte HTML-Ausgabe mit dem,
// was der Block heute erzeugen würde. Stimmt das nicht überein, erscheint
// im Editor „Dieser Block enthält unerwarteten oder ungültigen Inhalt“ –
// und genau das wollen wir nie. Dieses Skript findet solche Stellen, bevor
// jemand die Seite öffnet, und nennt Block und Zeile.
//
//   node tools/block-check.mjs <basis-url> <benutzer> <passwort> <datei...>
import { readFileSync } from 'node:fs';
import { basename } from 'node:path';
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';

const [basis, benutzer, passwort, ...dateien] = process.argv.slice(2);

if (!basis || !dateien.length) {
	console.error('Aufruf: node tools/block-check.mjs <basis-url> <benutzer> <passwort> <datei...>');
	process.exit(1);
}

const browser = await chromium.launch();
const seite = await browser.newPage({ viewport: { width: 1400, height: 900 } });

await seite.goto(`${basis}/wp-login.php`, { waitUntil: 'domcontentloaded' });
await seite.fill('#user_login', benutzer);
await seite.fill('#user_pass', passwort);
await Promise.all([seite.waitForNavigation({ waitUntil: 'domcontentloaded' }), seite.click('#wp-submit')]);

// Irgendein Editor-Bildschirm, damit die Block-Bibliothek geladen ist.
await seite.goto(`${basis}/wp-admin/post-new.php?post_type=page`, { waitUntil: 'domcontentloaded' });
await seite.waitForFunction(() => window.wp && wp.blocks && wp.blocks.parse, { timeout: 60000 });
await seite.waitForTimeout(2000);

let fehler = 0;

for (const datei of dateien) {
	const inhalt = readFileSync(datei, 'utf8');

	const bericht = await seite.evaluate((html) => {
		const schlecht = [];
		let gesamt = 0;

		const lauf = (bloecke, pfad) => {
			bloecke.forEach((b, i) => {
				gesamt += 1;
				const hier = pfad ? `${pfad} › ${b.name || 'roh'}[${i}]` : `${b.name || 'roh'}[${i}]`;

				if (b.name === 'core/freeform' || b.name === null) {
					schlecht.push({ art: 'Kein Block (roher HTML-Text)', wo: hier });
				} else if (b.name === 'core/html') {
					schlecht.push({ art: 'Individuelles HTML – nur im Code-Editor bearbeitbar', wo: hier });
				} else if (b.isValid === false) {
					schlecht.push({ art: 'Ungültig: Markup passt nicht zum Block', wo: hier });
				}

				if (b.innerBlocks && b.innerBlocks.length) lauf(b.innerBlocks, hier);
			});
		};

		lauf(wp.blocks.parse(html), '');

		return { gesamt, schlecht };
	}, inhalt);

	const status = bericht.schlecht.length === 0 ? 'OK ' : 'FEHLER';

	if (bericht.schlecht.length) fehler += 1;

	console.log(`${status}  ${basename(datei).padEnd(30)} Blöcke: ${String(bericht.gesamt).padStart(3)}`);
	bericht.schlecht.forEach((s) => console.log(`        ! ${s.art}  –  ${s.wo}`));
}

await browser.close();

console.log(
	fehler === 0
		? '\nAlle Inhalte sind reine Kern-Blöcke und im visuellen Editor vollständig bearbeitbar.'
		: `\n${fehler} Datei(en) mit Problemen.`
);

process.exit(fehler === 0 ? 0 : 1);
