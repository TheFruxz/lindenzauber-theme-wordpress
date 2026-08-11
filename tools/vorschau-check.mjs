// Prüft das gebaute Vorschau-Paket – also genau die Dateien, die zum
// Anschauen rausgehen. Die Vorschau entsteht aus einer echten
// WordPress-Instanz und wird dabei umgeschrieben; beim Umschreiben kann
// etwas kaputtgehen, das auf der WordPress-Seite selbst tadellos läuft.
//
// Genau das ist passiert: nav.js landete zweimal in jeder Seite. Zwei
// Kopien heißen zwei Klick-Behandler am Menüknopf – das Menü öffnete und
// schloss sich im selben Klick. In der Vorschau war das Handy-Menü tot,
// auf der WordPress-Seite nicht. Deshalb prüft das hier nicht nur, ob
// alles da ist, sondern ob das Menü sich wirklich bedienen lässt.
//
//   node tools/vorschau-check.mjs <ordner-mit-index.html>
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';
import { readdirSync } from 'node:fs';
import { resolve } from 'node:path';

const ordner = resolve(process.argv[2] || 'dist/vorschau');
const seiten = readdirSync(ordner)
	.filter((d) => d.endsWith('.html'))
	.sort();

if (!seiten.length) {
	console.error(`Keine HTML-Dateien in ${ordner}`);
	process.exit(2);
}

const browser = await chromium.launch();
let gesamt = 0;

for (const datei of seiten) {
	const befunde = [];
	const seite = await browser.newPage({ viewport: { width: 390, height: 760 } });

	seite.on('requestfailed', (r) =>
		befunde.push(`Datei fehlt: ${r.url().split('/').slice(-1)[0]}`)
	);
	seite.on('pageerror', (f) => befunde.push(`JavaScript-Fehler: ${String(f).slice(0, 80)}`));

	await seite.goto(`file://${ordner}/${datei}`, { waitUntil: 'networkidle' });
	await seite.waitForTimeout(250);

	/* -------------------------------------------- Jedes Skript genau einmal */
	const doppelt = await seite.evaluate(() => {
		const zaehler = {};
		[...document.scripts].forEach((s) => {
			if (!s.src) return;
			const name = s.src.split('/').pop().split('?')[0];
			zaehler[name] = (zaehler[name] || 0) + 1;
		});
		return Object.entries(zaehler).filter(([, n]) => n > 1);
	});

	doppelt.forEach(([name, n]) => befunde.push(`${name} ist ${n}× eingebunden`));

	/* ------------------------------------------------------- Schrift geladen */
	const schrift = await seite.evaluate(async () => {
		await document.fonts.ready;
		return document.fonts.check('1em "Great Vibes"');
	});

	if (!schrift) befunde.push('Schwungschrift „Great Vibes" wird nicht geladen');

	/* ------------------------------------------------- Bilder wirklich da */
	const leer = await seite.evaluate(() =>
		[...document.images]
			.filter((b) => !b.complete || !b.naturalWidth)
			.map((b) => b.src.split('/').pop())
	);

	leer.forEach((b) => befunde.push(`Bild lädt nicht: ${b}`));

	/* ------------------------------------------ Menü lässt sich bedienen */
	const hatSchalter = await seite.evaluate(() => {
		const s = document.querySelector('.nav-toggle');
		return !!s && getComputedStyle(s).display !== 'none';
	});

	if (!hatSchalter) {
		befunde.push('Kein sichtbarer Menüknopf bei 390 px');
	} else {
		await seite.click('.nav-toggle');
		await seite.waitForTimeout(500);

		const auf = await seite.evaluate(() => {
			const n = document.querySelector('.main-nav');
			const s = getComputedStyle(n);
			const erster = n.querySelector('a[href]');
			return {
				offen: n.classList.contains('is-open'),
				sichtbar: s.visibility === 'visible' && parseFloat(s.opacity) > 0.5,
				gemeldet: document.querySelector('.nav-toggle').getAttribute('aria-expanded'),
				linkTrifft: erster
					? (() => {
							const r = erster.getBoundingClientRect();
							const t = document.elementFromPoint(r.left + r.width / 2, r.top + r.height / 2);
							return !!(t && t.closest('a[href]'));
					  })()
					: false,
			};
		});

		if (!auf.offen || !auf.sichtbar) befunde.push('Menüknopf öffnet das Menü nicht');
		if (auf.gemeldet !== 'true') befunde.push(`aria-expanded steht auf "${auf.gemeldet}" statt "true"`);
		if (auf.offen && !auf.linkTrifft) befunde.push('Menüeinträge sind nicht anklickbar');

		// Fehlt der Seite ein Text, stünde der Knopf ohne oder mit
		// "undefined" da – das ist schon einmal passiert.
		const aufschrift = await seite.evaluate(() => {
			const l = document.querySelector('.nav-toggle__label');
			return l ? l.textContent.trim() : '';
		});

		if (!aufschrift || aufschrift === 'undefined') {
			befunde.push(`Menüknopf ohne Aufschrift ("${aufschrift}")`);
		}

		// Ein Klick auf einen Eintrag muss das Menü wieder schließen.
		if (auf.offen && auf.linkTrifft) {
			const zu = await seite.evaluate(() => {
				document.querySelector('.main-nav a[href]').dispatchEvent(
					new MouseEvent('click', { bubbles: true, cancelable: true })
				);
				return !document.querySelector('.main-nav').classList.contains('is-open');
			});

			if (!zu) befunde.push('Klick auf einen Eintrag schließt das Menü nicht');
		}

		// Und die Seite darf danach nicht festgesetzt bleiben.
		await seite.waitForTimeout(200);
		const fest = await seite.evaluate(() => document.body.classList.contains('lz-fest'));

		if (fest) befunde.push('Seite bleibt nach dem Schließen festgesetzt');
	}

	/* ----------------------------------------------- Ende der Seite sauber */
	const rest = await seite.evaluate(() => {
		const f = document.querySelector('.site-footer');
		if (!f) return 0;
		return Math.round(
			document.documentElement.scrollHeight - (f.getBoundingClientRect().bottom + window.scrollY)
		);
	});

	if (rest > 4) befunde.push(`${rest} px Leerraum hinter dem Fußbereich`);

	gesamt += befunde.length;
	console.log(`${befunde.length === 0 ? 'OK    ' : 'BEFUND'} ${datei.padEnd(24)} ${befunde.length || ''}`);
	befunde.forEach((b) => console.log(`        · ${b}`));

	await seite.close();
}

await browser.close();
console.log(gesamt === 0 ? '\nVorschau in Ordnung.' : `\n${gesamt} Befund(e).`);
process.exit(gesamt === 0 ? 0 : 1);
