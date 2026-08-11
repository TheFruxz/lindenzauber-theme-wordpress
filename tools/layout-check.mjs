// Findet die Sorte Fehler, die man beim Durchscrollen spürt, aber schlecht
// beschreiben kann: fehlende Abstände, doppelte Abstände, Flächen die
// klickbar aussehen aber keine sind, Text der in seinem Abschnitt nach oben
// rutscht, seitlicher Überlauf, zu schwacher Kontrast, Leerraum hinter dem
// Fußbereich und Bilder, deren eigener Grund nicht zur Kachel darunter passt.
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

	/* ------------------------------------------- 6. Hinter dem Fußbereich */
	// Der Fußbereich ist das Ende der Seite. Was danach noch Höhe hat – eine
	// Deko-Ebene, ein überstehender Verlauf – erscheint als unerklärlicher
	// leerer Streifen unter der Website.
	const fuss = document.querySelector('.site-footer');

	if (fuss) {
		const oben = window.scrollY;
		const fussUnten = fuss.getBoundingClientRect().bottom + oben;
		const ende = document.documentElement.scrollHeight;

		// Nur sinnvoll, wenn die Seite überhaupt länger als das Fenster ist.
		if (ende > window.innerHeight + 4 && ende - fussUnten > 4) {
			befunde.push({
				art: 'Leerraum hinter dem Fußbereich',
				wo: 'Seite',
				mass: `${px(ende - fussUnten)} px über das Seitenende hinaus`,
			});
		}

		// Und wer verursacht ihn? Gemessen wird, was man sieht: ein Element,
		// das ein Vorfahre abschneidet, ragt zwar im Layout hinaus, ist aber
		// nirgends sichtbar – das ist kein Befund, sondern der Sinn von
		// "overflow: clip".
		const sichtbaresEnde = (el) => {
			let unten = el.getBoundingClientRect().bottom;
			let k = el.parentElement;

			while (k && k !== document.documentElement) {
				const s = getComputedStyle(k);

				if (s.overflowY !== 'visible') {
					unten = Math.min(unten, k.getBoundingClientRect().bottom);
				}

				k = k.parentElement;
			}

			return unten;
		};

		const taeter = new Set();

		document.querySelectorAll('.site, .site *').forEach((el) => {
			if (el === fuss || el.contains(fuss) || fuss.contains(el)) return;
			if (!sichtbar(el)) return;
			if (getComputedStyle(el).position === 'fixed') return;

			const unten = sichtbaresEnde(el) + oben;

			if (unten > fussUnten + 2) {
				const name = beschreibe(el).split(' „')[0];

				if (!taeter.has(name)) {
					taeter.add(name);
					befunde.push({
						art: 'Element ragt unter den Fußbereich',
						wo: name,
						mass: `${px(unten - fussUnten)} px zu tief`,
					});
				}
			}
		});
	}

	/* ------------------------------------------ 7. Farbbruch Bild ⟷ Kachel */
	// Logos bringen oft einen eigenen deckenden Grund mit. Stimmt der nicht
	// haargenau mit der Kachel darunter überein, sieht man ein helleres
	// Rechteck im Rahmen – der Eindruck ist sofort unsauber.
	const grundVoll = (el) => {
		const stapel = [];
		let k = el;

		while (k && k !== document.documentElement) {
			const f = zahl(getComputedStyle(k).backgroundColor);
			const a = f.length >= 4 ? f[3] : 1;

			if (f.length >= 3 && a > 0.001) stapel.push([f[0], f[1], f[2], a]);
			if (f.length >= 3 && a >= 0.999) break;

			k = k.parentElement;
		}

		// Von hinten nach vorn übereinanderlegen, Grundfarbe der Seite zuerst.
		let r = 8;
		let g = 15;
		let b = 30;

		for (let i = stapel.length - 1; i >= 0; i--) {
			const [fr, fg, fb, fa] = stapel[i];
			r = fr * fa + r * (1 - fa);
			g = fg * fa + g * (1 - fa);
			b = fb * fa + b * (1 - fa);
		}

		return [r, g, b];
	};

	const eckfarben = (bild) => {
		const w = bild.naturalWidth;
		const h = bild.naturalHeight;

		if (!bild.complete || w < 6 || h < 6) return null;

		const flaeche = document.createElement('canvas');
		flaeche.width = 1;
		flaeche.height = 1;
		const stift = flaeche.getContext('2d', { willReadFrequently: true });
		const punkte = [
			[1, 1],
			[w - 2, 1],
			[1, h - 2],
			[w - 2, h - 2],
		];
		const werte = [];

		for (const [sx, sy] of punkte) {
			stift.clearRect(0, 0, 1, 1);

			try {
				stift.drawImage(bild, sx, sy, 1, 1, 0, 0, 1, 1);
				const d = stift.getImageData(0, 0, 1, 1).data;
				werte.push([d[0], d[1], d[2], d[3] / 255]);
			} catch (fehler) {
				return null; // fremde Herkunft – nicht auslesbar
			}
		}

		return werte;
	};

	const abstandRGB = (a, b) =>
		Math.max(Math.abs(a[0] - b[0]), Math.abs(a[1] - b[1]), Math.abs(a[2] - b[2]));

	// Eine sichtbare Umrandung ist eine bewusst gesetzte Kante. Dann ist der
	// Wechsel zur Umgebung gewollt und kein Versehen – anders als bei einem
	// Logo, das ohne Kante auf einer Kachel liegt.
	const hatRand = (el) => {
		const s = getComputedStyle(el);
		const stark = parseFloat(s.borderTopWidth || 0) >= 1;
		const farbe = zahl(s.borderTopColor);
		const sichtbarerRand = farbe.length >= 3 && (farbe[3] === undefined || farbe[3] > 0.05);

		return stark && s.borderTopStyle !== 'none' && sichtbarerRand;
	};

	document.querySelectorAll('img').forEach((bild) => {
		if (!sichtbar(bild)) return;
		if (hatRand(bild)) return;
		if (bild.parentElement && hatRand(bild.parentElement)) return;

		const e = eckfarben(bild);
		if (!e) return;

		// Durchsichtige Ecken sind gewollt – freigestellte Logos, Ornamente.
		if (e.some((f) => f[3] < 0.95)) return;

		// Nur flächige Ränder prüfen. Ein Foto hat in jeder Ecke etwas
		// anderes; dort ist ein Unterschied zur Kachel selbstverständlich.
		if (e.some((f) => abstandRGB(f, e[0]) > 4)) return;

		const kachel = grundVoll(bild.parentElement || bild);
		const unterschied = abstandRGB(e[0], kachel);

		if (unterschied > 6) {
			const rahmen = bild.closest('figure, li, .wp-block-column') || bild;

			befunde.push({
				art: 'Farbbruch Bild ⟷ Kachel',
				wo:
					beschreibe(rahmen).split(' „')[0] +
					' ← ' +
					(bild.currentSrc || bild.src).split('/').pop(),
				mass:
					`Bild rgb(${e[0].slice(0, 3).map(px).join(',')}) ` +
					`vs. Grund rgb(${kachel.map(px).join(',')})`,
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
