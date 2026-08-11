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
const liste = await s.evaluate(() => [...document.querySelectorAll('.lz-einrichtung tbody tr')].map(tr => ({
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

if (bilder) await s.screenshot({ path: bilder + '/einrichtung.png', fullPage: true });
await b.close();
console.log(fehler === 0 ? '\nEinrichtungsseite in Ordnung.' : `\n${fehler} Fehler.`);
process.exit(fehler ? 1 : 0);
