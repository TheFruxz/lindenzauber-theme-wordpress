// Findet die Sorte Fehler, die man beim Durchscrollen spürt, aber schlecht
// beschreiben kann: fehlende Abstände, doppelte Abstände, Flächen die
// klickbar aussehen aber keine sind, Text der in seinem Abschnitt nach oben
// rutscht, seitlicher Überlauf und zu schwacher Kontrast.
//
//   node tools/layout-check.mjs <basis-url> [pfad...]
//
// Ohne Pfade werden die üblichen Seiten geprüft, in 390 px, 768 px und 1440 px.
import { chromium } from '/opt/node22/lib/node_modules/playwright/index.mjs';

const basis = process.argv[2] || 'http://127.0.0.1:8321';
const seiten = process.argv.slice(3);
const pfade = seiten.length
	? seiten
	: ['/', '/programm/', '/die-erzaehlenden/', '/ueber-lindenzauber/', '/sponsoren/', '/kontakt/', '/impressum/'];
const breiten = [390, 768, 1440];

const pruefung = () => {
	const befunde = [];
	const px = (n) => Math.round(n);

	const beschreibe = (el) => {
		const klasse = (el.className || '').toString().split(/\s+/).filter(Boolean).slice(0, 3).join('.');
		const text = (el.textContent || '').trim().replace(/\s+/g, ' ').slice(0, 42);
		return `${el.tagName.toLowerCase()}${klasse ? '.' + klasse : ''}${text ? ` „${text}…"` : ''}`;
	};

	const sichtbar = (el) => {
		const s = getComputedStyle(el);
		const r = el.getBoundingClientRect();
		return s.display !== 'none' && s.visibility !== 'hidden' && r.height > 0 && r.width > 0;
	};

	/* -------------------------------------------------- 1. Rhythmusbrüche */
	// Zwei aufeinanderfolgende Blöcke, zwischen denen praktisch kein Abstand
	// steht. Das ist der Fehler „Überschrift klebt am Folgeblock".
	const container = document.querySelectorAll(
		'.is-layout-flow, .is-layout-constrained, .lz-mehr__inhalt, .entry-content'
	);

	container.forEach((box) => {
		const kinder = [...box.children].filter(sichtbar);

		for (let i = 1; i < kinder.length; i++) {
			const vorher = kinder[i - 1];
			const jetzt = kinder[i];

			// Absolut positionierte Deko zählt nicht.
			if (getComputedStyle(jetzt).position === 'absolute') continue;
			if (!jetzt.textContent.trim() && !jetzt.querySelector('img, svg')) continue;
			if (!vorher.textContent.trim() && !vorher.querySelector('img, svg')) continue;

			// Abschnitte stoßen bewusst aneinander – ihr Innenabstand macht
			// die Luft. Nur Inhalt innerhalb eines Abschnitts wird geprüft.
			const istAbschnitt = (el) =>
				el.matches('.is-style-lz-band, .is-style-lz-panel, .is-style-lz-hero');

			if (istAbschnitt(vorher) || istAbschnitt(jetzt)) continue;

			// Innenabstand zählt als Luft: ein Block darf seinen Abstand auch
			// über padding statt über margin mitbringen.
			const sv = getComputedStyle(vorher);
			const sj = getComputedStyle(jetzt);
			const luft =
				jetzt.getBoundingClientRect().top -
				vorher.getBoundingClientRect().bottom +
				parseFloat(sj.paddingTop || 0) +
				parseFloat(sv.paddingBottom || 0);

			// Bewusst enge Paare setzen einen eigenen Rand. Wo gar keiner
			// gesetzt ist, war der Abstand versehentlich weg – nur das ist
			// ein Befund.
			if (parseFloat(sj.marginTop || 0) >= 4) continue;

			if (luft < 6) {
				befunde.push({
					art: 'Rhythmusbruch',
					wo: `${beschreibe(vorher)}  ⟶  ${beschreibe(jetzt)}`,
					mass: `${px(luft)} px Abstand`,
				});
			}
		}
	});

	/* ------------------------------------- 2. Doppelte Abstände / Außermitte */
	document.querySelectorAll('.is-style-lz-band, .is-style-lz-panel').forEach((abschnitt) => {
		const kinder = [...abschnitt.children].filter(sichtbar);
		if (!kinder.length) return;

		const a = abschnitt.getBoundingClientRect();
		const oben = kinder[0].getBoundingClientRect().top - a.top;
		const unten = a.bottom - kinder[kinder.length - 1].getBoundingClientRect().bottom;
		const groesser = Math.max(oben, unten);
		const kleiner = Math.min(oben, unten);

		// Ein Abschnitt soll oben und unten ähnlich viel Luft haben.
		if (groesser - kleiner > 48 && groesser > kleiner * 1.5) {
			befunde.push({
				art: 'Abschnitt außermittig',
				wo: beschreibe(abschnitt),
				mass: `oben ${px(oben)} px, unten ${px(unten)} px`,
			});
		}
	});

	/* ------------------------------------------------- 3. Klickbare Flächen */
	document.querySelectorAll('a[href], button').forEach((el) => {
		if (!sichtbar(el)) return;

		if (getComputedStyle(el).cursor !== 'pointer') {
			befunde.push({
				art: 'Klickfläche ohne Zeiger',
				wo: beschreibe(el),
				mass: getComputedStyle(el).cursor,
			});
		}
	});

	// Kacheln, die wie ein Knopf aussehen, müssen sich auch so anfühlen.
	document.querySelectorAll('.is-style-lz-foerderer').forEach((el) => {
		if (!sichtbar(el)) return;

		if (getComputedStyle(el).cursor !== 'pointer') {
			befunde.push({ art: 'Kachel ohne Zeiger', wo: beschreibe(el), mass: getComputedStyle(el).cursor });
		}

		// Deckt der Link wirklich die ganze Kachel ab?
		const r = el.getBoundingClientRect();
		const mitte = document.elementFromPoint(r.left + r.width / 2, r.top + 24);

		if (mitte && !mitte.closest('a')) {
			befunde.push({
				art: 'Kachel oben nicht anklickbar',
				wo: beschreibe(el),
				mass: `dort liegt ${beschreibe(mitte)}`,
			});
		}
	});

	/* ------------------------------------------------------- 4. Seitlich raus */
	const breite = document.documentElement.clientWidth;

	if (document.documentElement.scrollWidth > breite + 1) {
		befunde.push({
			art: 'Seitlicher Überlauf',
			wo: 'Seite',
			mass: `${document.documentElement.scrollWidth} statt ${breite} px`,
		});
	}

	/* ---------------------------------------------------------- 5. Kontrast */
	const zahl = (farbe) => (farbe.match(/[\d.]+/g) || []).map(Number);

	const leuchte = (rgb) =>
		rgb
			.slice(0, 3)
			.map((v) => {
				const c = v / 255;
				return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
			})
			.reduce((s, v, i) => s + v * [0.2126, 0.7152, 0.0722][i], 0);

	const grund = (el) => {
		let k = el;
		while (k && k !== document.documentElement) {
			const f = zahl(getComputedStyle(k).backgroundColor);
			if (f.length >= 3 && (f[3] === undefined || f[3] > 0.6)) return f;
			k = k.parentElement;
		}
		return [8, 15, 30];
	};

	document.querySelectorAll('p, li, h1, h2, h3, h4, a, span, strong, figcaption').forEach((el) => {
		if (!sichtbar(el)) return;
		if (!el.textContent.trim()) return;
		// Die Banderole trägt ihren goldenen Grund auf einer Pseudo-Ebene;
		// der Hintergrund lässt sich von außen nicht auslesen. Dunkelbraun
		// auf Gold ist sehr kontrastreich – hier würde nur falsch gemeldet.
		if (el.closest('.is-style-lz-banderole')) return;
		if ([...el.children].some((k) => k.textContent.trim())) return; // nur Blätter

		const s = getComputedStyle(el);
		const vg = zahl(s.color);
		if (vg.length < 3 || (vg[3] !== undefined && vg[3] < 0.5)) return;

		const hg = grund(el);
		const l1 = leuchte(vg);
		const l2 = leuchte(hg);
		const verhaeltnis = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);

		const groesse = parseFloat(s.fontSize);
		const fett = parseInt(s.fontWeight, 10) >= 700;
		const gross = groesse >= 24 || (groesse >= 18.66 && fett);
		const noetig = gross ? 3 : 4.5;

		if (verhaeltnis < noetig) {
			befunde.push({
				art: 'Kontrast zu schwach',
				wo: beschreibe(el),
				mass: `${verhaeltnis.toFixed(2)} : 1 (nötig ${noetig})`,
			});
		}
	});

	return befunde;
};

const browser = await chromium.launch();
let gesamt = 0;

for (const breite of breiten) {
	for (const pfad of pfade) {
		const seite = await browser.newPage({ viewport: { width: breite, height: 900 } });
		await seite.goto(basis + pfad, { waitUntil: 'networkidle', timeout: 60000 });

		// Durchscrollen, damit nachgeladene Bilder da sind und Höhen stimmen.
		await seite.evaluate(async () => {
			for (let y = 0; y < document.body.scrollHeight; y += 700) {
				window.scrollTo(0, y);
				await new Promise((r) => setTimeout(r, 60));
			}
			window.scrollTo(0, 0);
		});
		await seite.waitForTimeout(400);

		const befunde = await seite.evaluate(pruefung);

		// Gleiche Befunde nur einmal melden.
		const gesehen = new Set();
		const einmalig = befunde.filter((b) => {
			const s = b.art + '|' + b.wo + '|' + b.mass;
			if (gesehen.has(s)) return false;
			gesehen.add(s);
			return true;
		});

		gesamt += einmalig.length;

		console.log(
			`${einmalig.length === 0 ? 'OK    ' : 'BEFUND'} ${String(breite).padStart(4)} px  ${pfad.padEnd(22)} ${einmalig.length || ''}`
		);
		einmalig.forEach((b) => console.log(`        · ${b.art}: ${b.wo}  [${b.mass}]`));

		await seite.close();
	}
}

await browser.close();
console.log(gesamt === 0 ? '\nKeine Befunde.' : `\n${gesamt} Befund(e).`);
process.exit(gesamt === 0 ? 0 : 1);
