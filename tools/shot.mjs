// Screenshot-Helfer.
//   node tools/shot.mjs <url> <ziel.png> [breite] [hoehe|full] [css-auswahl]
//
// Vor der Aufnahme wird einmal durch die Seite gescrollt, damit auch die
// Bilder geladen sind, die WordPress mit loading="lazy" ausliefert.
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';

const [url, out, w = '1440', h = 'full', sel] = process.argv.slice(2);

if (!url || !out) {
	console.error('Aufruf: node tools/shot.mjs <url> <ziel.png> [breite] [hoehe|full] [css-auswahl]');
	process.exit(1);
}

const browser = await chromium.launch();
const page = await browser.newPage({
	viewport: { width: Number(w), height: h === 'full' ? 1000 : Number(h) },
	deviceScaleFactor: 1,
});

const probleme = [];
page.on('requestfailed', (r) => probleme.push(`FEHLGESCHLAGEN ${r.url()} – ${r.failure()?.errorText}`));
page.on('response', (r) => { if (r.status() >= 400) probleme.push(`${r.status()} ${r.url()}`); });
page.on('pageerror', (e) => probleme.push(`JS-FEHLER ${e.message}`));

await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 });

// Durch die Seite scrollen, damit nachgeladene Bilder erscheinen.
await page.evaluate(async () => {
	const schritt = window.innerHeight;
	const ende = document.body.scrollHeight;
	for (let y = 0; y < ende; y += schritt) {
		window.scrollTo(0, y);
		await new Promise((r) => setTimeout(r, 120));
	}
	window.scrollTo(0, 0);
	await Promise.all(
		Array.from(document.images)
			.filter((i) => !i.complete)
			.map((i) => new Promise((r) => { i.onload = i.onerror = r; }))
	);
});
await page.waitForTimeout(500);

if (sel) {
	await page.locator(sel).first().screenshot({ path: out });
} else {
	await page.screenshot({ path: out, fullPage: h === 'full' });
}

await browser.close();

if (probleme.length) {
	console.log('PROBLEME:');
	probleme.slice(0, 20).forEach((p) => console.log('  ' + p));
}

console.log('geschrieben:', out);
