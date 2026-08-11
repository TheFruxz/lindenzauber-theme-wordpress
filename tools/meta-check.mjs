// Prüft, was Suchmaschinen und Sprachmodelle von der Website zu sehen
// bekommen: Titel, Beschreibung, Teilen-Vorschau, Symbol im Browser-Tab,
// strukturierte Daten und die llms.txt.
//
// Und eine Trennung, die hier ausdrücklich gewollt ist: Die Herkunft des
// Themes steht als Kommentar im Quelltext, aber in KEINER maschinenlesbaren
// Angabe über das Fest. Sonst gäbe eine Zusammenfassung irgendwann "die
// Website wurde von … entwickelt" als Teil der Beschreibung des Festes aus.
// Das wird hier bei jedem Durchlauf nachgehalten.
//
//   node tools/meta-check.mjs <basis-url> [pfad...]
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';

const basis = (process.argv[2] || 'http://127.0.0.1:8321').replace(/\/$/, '');
const eigene = process.argv.slice(3);
const pfade = eigene.length
	? eigene
	: ['/', '/programm/', '/die-erzaehlenden/', '/ueber-lindenzauber/', '/sponsoren/', '/kontakt/', '/impressum/'];

// Die Werkstatt darf im Quelltext stehen, nicht in den Angaben über das Fest.
const WERKSTATT = /fruxz/i;

const browser = await chromium.launch();
let gesamt = 0;

for (const pfad of pfade) {
	const befunde = [];
	const seite = await browser.newPage({ viewport: { width: 1280, height: 900 } });
	const antwort = await seite.goto(basis + pfad, { waitUntil: 'networkidle', timeout: 60000 });
	const quelltext = await antwort.text();

	const d = await seite.evaluate(() => {
		const inhalt = (auswahl) => {
			const el = document.querySelector(auswahl);
			return el ? el.getAttribute('content') || '' : null;
		};

		return {
			titel: document.title,
			beschreibung: inhalt('meta[name="description"]'),
			ogTitel: inhalt('meta[property="og:title"]'),
			ogBesch: inhalt('meta[property="og:description"]'),
			ogBild: inhalt('meta[property="og:image"]'),
			ogBildAlt: inhalt('meta[property="og:image:alt"]'),
			ogUrl: inhalt('meta[property="og:url"]'),
			ogTyp: inhalt('meta[property="og:type"]'),
			twKarte: inhalt('meta[name="twitter:card"]'),
			autor: inhalt('meta[name="author"]'),
			symbol: document.querySelectorAll('link[rel~="icon"], link[rel="apple-touch-icon"]').length,
			canonical: !!document.querySelector('link[rel="canonical"]'),
			ld: [...document.querySelectorAll('script[type="application/ld+json"]')].map((s) => s.textContent),
		};
	});

	/* ------------------------------------------------ Titel und Text */
	if (!d.titel || d.titel.length < 10) befunde.push(`Titel fehlt oder ist zu kurz („${d.titel}")`);
	if (d.titel && d.titel.length > 70) befunde.push(`Titel ist ${d.titel.length} Zeichen lang (über 70 wird abgeschnitten)`);
	if (!d.beschreibung) befunde.push('Keine Beschreibung');
	else if (d.beschreibung.length < 50) befunde.push(`Beschreibung ist nur ${d.beschreibung.length} Zeichen lang`);

	/* ------------------------------------------------ Teilen-Vorschau */
	for (const [name, wert] of Object.entries({
		'og:title': d.ogTitel,
		'og:description': d.ogBesch,
		'og:url': d.ogUrl,
		'og:type': d.ogTyp,
		'twitter:card': d.twKarte,
	})) {
		if (!wert) befunde.push(`${name} fehlt`);
	}

	if (d.ogBild && !d.ogBildAlt) befunde.push('og:image ohne og:image:alt');
	if (!d.canonical) befunde.push('Kein canonical-Verweis');
	if (d.symbol < 1) befunde.push('Kein Symbol für den Browser-Tab');

	/* --------------------------------------------- Strukturierte Daten */
	if (!d.ld.length) {
		befunde.push('Keine strukturierten Daten');
	}

	let hatFest = false;

	d.ld.forEach((roh, i) => {
		let daten;

		try {
			daten = JSON.parse(roh);
		} catch (fehler) {
			befunde.push(`Strukturierte Daten ${i + 1} sind kein gültiges JSON`);
			return;
		}

		if (!daten['@context']) befunde.push(`Strukturierte Daten ${i + 1} ohne @context`);

		const knoten = daten['@graph'] || [daten];

		knoten.forEach((k) => {
			if (!k['@type']) befunde.push('Knoten ohne @type in den strukturierten Daten');

			if (k['@type'] === 'Festival' || k['@type'] === 'Event') {
				hatFest = true;

				for (const feld of ['name', 'startDate', 'location', 'organizer']) {
					if (!k[feld]) befunde.push(`Veranstaltung ohne ${feld}`);
				}
			}
		});

		if (WERKSTATT.test(roh)) {
			befunde.push('Die Theme-Herkunft steht in den strukturierten Daten – dort gehört sie nicht hin');
		}
	});

	if (d.ld.length && !hatFest) befunde.push('Keine Veranstaltung in den strukturierten Daten');

	/* ------------------------------------------- Trennung Werk / Werkstatt */
	if (d.autor && WERKSTATT.test(d.autor)) {
		befunde.push('Die Theme-Herkunft steht in meta name="author"');
	}

	const sichtbarerText = await seite.evaluate(() => document.body.innerText);

	if (WERKSTATT.test(sichtbarerText)) {
		befunde.push('Die Theme-Herkunft steht im sichtbaren Text der Seite');
	}

	const kommentare = quelltext.match(/<!--[\s\S]*?-->/g) || [];

	if (!kommentare.some((k) => WERKSTATT.test(k))) {
		befunde.push('Der Hinweis auf die Theme-Herkunft fehlt im Quelltext');
	}

	gesamt += befunde.length;
	console.log(`${befunde.length === 0 ? 'OK    ' : 'BEFUND'} ${pfad.padEnd(24)} ${befunde.length || ''}`);
	befunde.forEach((b) => console.log(`        · ${b}`));

	await seite.close();
}

/* ----------------------------------------------------------- llms.txt */
{
	const befunde = [];
	const seite = await browser.newPage();
	const antwort = await seite.goto(basis + '/llms.txt', { waitUntil: 'domcontentloaded' });
	const text = await antwort.text();

	if (antwort.status() !== 200) {
		befunde.push(`llms.txt antwortet mit ${antwort.status()}`);
	} else {
		const typ = (antwort.headers()['content-type'] || '').toLowerCase();

		if (!typ.includes('text/plain')) befunde.push(`llms.txt kommt als ${typ} statt text/plain`);
		if (text.length < 200) befunde.push(`llms.txt ist nur ${text.length} Zeichen lang`);
		if (!/^#\s/m.test(text)) befunde.push('llms.txt hat keine Überschrift');
		if (WERKSTATT.test(text)) befunde.push('Die Theme-Herkunft steht in der llms.txt – dort gehört sie nicht hin');

		for (const pflicht of ['## Termine', '## Ort', '## Kontakt']) {
			if (!text.includes(pflicht)) befunde.push(`llms.txt ohne Abschnitt „${pflicht}"`);
		}
	}

	gesamt += befunde.length;
	console.log(`${befunde.length === 0 ? 'OK    ' : 'BEFUND'} ${'/llms.txt'.padEnd(24)} ${befunde.length || ''}`);
	befunde.forEach((b) => console.log(`        · ${b}`));

	await seite.close();
}

await browser.close();
console.log(gesamt === 0 ? '\nKeine Befunde.' : `\n${gesamt} Befund(e).`);
process.exit(gesamt === 0 ? 0 : 1);
