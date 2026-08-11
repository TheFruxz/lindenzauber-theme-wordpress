// Erzeugt screenshot.jpg – das Bild, das WordPress unter *Design → Themes*
// neben dem Theme zeigt. Ohne diese Datei steht dort ein graues Feld mit
// dem Theme-Namen; jemand, der drei Themes vergleicht, sieht nichts.
//
// WordPress erwartet 1200 × 900. Aufgenommen wird der Kopfbereich der
// Startseite – also genau das, was das Theme ausmacht.
//
//   node tools/make-screenshot.mjs [url]
//
// Läuft nicht im Build mit: dafür müsste die Testinstanz stehen, bevor das
// Theme-Paket gebaut wird. Die Datei liegt deshalb im Repo und wird von Hand
// erneuert, wenn sich die Gestaltung deutlich ändert.
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const repo = resolve(dirname(fileURLToPath(import.meta.url)), '..');
// JPEG statt PNG: als PNG wog das Bild 530 KB und hätte das Theme-Paket
// mehr als verdoppelt. Ein Nachthimmel mit weichen Verläufen ist genau der
// Fall, für den PNG ungeeignet ist. WordPress akzeptiert beides.
const ziel = resolve(repo, 'theme/lindenzauber/screenshot.jpg');
const url = process.argv[2] || 'http://127.0.0.1:8321/';

const browser = await chromium.launch();
const seite = await browser.newPage({ viewport: { width: 1200, height: 900 } });

await seite.goto(url, { waitUntil: 'networkidle', timeout: 60000 });
await seite.waitForTimeout(600);
await seite.screenshot({
	path: ziel,
	type: 'jpeg',
	quality: 88,
	clip: { x: 0, y: 0, width: 1200, height: 900 },
});

await browser.close();
console.log(`screenshot.jpg  1200×900  aus ${url}`);
