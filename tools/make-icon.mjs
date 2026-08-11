// Rendert das favicon.svg zu apple-touch-icon.png.
//
// Apple-Geräte legen das Symbol auf den Startbildschirm und nehmen dafür kein
// SVG. 180 px ist die Größe, die iOS erwartet; alles andere skaliert es
// sichtbar unsauber.
//
//   node tools/make-icon.mjs
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import { readFileSync, writeFileSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const repo = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const quelle = resolve(repo, 'theme/lindenzauber/assets/img/favicon.svg');
const ziel = resolve(repo, 'theme/lindenzauber/assets/img/apple-touch-icon.png');
const KANTE = 180;

const svg = readFileSync(quelle, 'utf-8');

const browser = await chromium.launch();
const seite = await browser.newPage({
	viewport: { width: KANTE, height: KANTE },
	deviceScaleFactor: 1,
});

await seite.setContent(
	`<!doctype html><meta charset="utf-8">
	<style>html,body{margin:0;padding:0;width:${KANTE}px;height:${KANTE}px;overflow:hidden}
	svg{display:block;width:${KANTE}px;height:${KANTE}px}</style>${svg}`,
	{ waitUntil: 'load' }
);
await seite.waitForTimeout(200);

const bild = await seite.screenshot({ omitBackground: false });
writeFileSync(ziel, bild);

await browser.close();
console.log(`apple-touch-icon.png  ${KANTE}×${KANTE}  ${bild.length} B`);
