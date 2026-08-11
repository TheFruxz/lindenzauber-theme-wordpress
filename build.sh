#!/usr/bin/env bash
#
# Baut die beiden auslieferbaren Pakete:
#
#   dist/lindenzauber.zip           – das Theme zum Installieren in WordPress
#   dist/lindenzauber-vorschau.zip  – die statische Vorschau zum Anschauen
#
# Für die Vorschau wird eine lokale WordPress-Instanz gebraucht. Sie wird
# automatisch aufgesetzt, wenn LZ_WORK auf einen Arbeitsordner zeigt, in dem
# wordpress.zip, sqlite.zip und wp-cli.phar liegen. Fehlt das, wird nur das
# Theme-Paket gebaut.
#
#   bash build.sh              nur das Theme
#   LZ_WORK=/pfad bash build.sh    Theme und Vorschau
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$REPO"

mkdir -p dist

echo "== Grafiken und Muster erzeugen =="
python3 tools/make-svg.py > /dev/null
node tools/make-icon.mjs > /dev/null
php tools/make-patterns.php > /dev/null

echo "== PHP prüfen =="
fehler=0
while IFS= read -r datei; do
	php -l "$datei" > /dev/null || { echo "  SYNTAXFEHLER: $datei"; fehler=1; }
done < <(find theme tools -name '*.php')
[ "$fehler" -eq 0 ] || exit 1
echo "  in Ordnung"

echo "== Theme-Paket =="
rm -f dist/lindenzauber.zip
( cd theme && zip -q -r "../dist/lindenzauber.zip" lindenzauber \
	-x '*.DS_Store' -x '__MACOSX/*' )
echo "  dist/lindenzauber.zip ($(du -h dist/lindenzauber.zip | cut -f1))"

echo "== Inhalte-Paket =="
# Die Seiteninhalte gehören zur Auslieferung wie das Theme selbst – sonst
# lädt man zwei ZIPs herunter und muss die Inhalte trotzdem einzeln aus dem
# Repository fischen.
rm -f dist/lindenzauber-inhalte.zip
( cd inhalte && zip -q -r "../dist/lindenzauber-inhalte.zip" . -x '*.DS_Store' )
echo "  dist/lindenzauber-inhalte.zip ($(du -h dist/lindenzauber-inhalte.zip | cut -f1))"

if [ -z "${LZ_WORK:-}" ] || [ ! -f "${LZ_WORK}/wordpress.zip" ]; then
	echo
	echo "Hinweis: LZ_WORK ist nicht gesetzt (oder unvollständig)."
	echo "Die statische Vorschau wurde nicht neu gebaut."
	exit 0
fi

echo "== Testinstanz aufsetzen =="
bash tools/wp-test-setup.sh > /dev/null

# Der Import läuft auf der noch leeren Instanz – das ist der echte Erstfall.
# Er legt die Seiten an; die folgenden Schritte setzen nur noch Menüs und
# holen die Bilder. Bricht der Import, bricht der Bau.
echo "== Seiten-Import prüfen =="
if ! node tools/import-check.mjs "http://127.0.0.1:8321" admin lindenzauber \
	"$LZ_WORK/site" "$LZ_WORK/wp-cli.phar" "dist/lindenzauber-inhalte.zip"; then
	echo "  Der Seiten-Import hat Befunde."
	exit 1
fi

LZ_REPO="$REPO" bash tools/wp-test-inhalte.sh > /dev/null
LZ_REPO="$REPO" bash tools/wp-test-medien.sh > /dev/null

# Abstände, Klickflächen, Kontrast, Überlauf, Bilder – die Fehlerklassen, die
# Brigitta und Cedric gemeldet haben. Bis hierher war das ein Werkzeug, das man
# von Hand aufrufen musste; ein Rückfall hätte den Bau nicht aufgehalten.
echo "== Gestaltung prüfen =="
if ! node tools/layout-check.mjs "http://127.0.0.1:8321"; then
	echo "  Die Gestaltung hat Befunde."
	exit 1
fi

echo "== Vorschau erzeugen =="
php tools/make-vorschau.php > /dev/null

# Titel, Beschreibungen, strukturierte Daten, llms.txt – und die Trennung
# zwischen dem, was über das Fest ausgesagt wird, und dem Hinweis auf die
# Werkstatt, der nur im Quelltext stehen darf.
echo "== Metadaten prüfen =="
if ! node tools/meta-check.mjs "http://127.0.0.1:8321"; then
	echo "  Die Metadaten haben Befunde."
	exit 1
fi

# Das Backend prüft sonst niemand – dabei ist es der Teil, den Brigitta
# täglich sieht. Hier wird die Einrichtungsseite wirklich durchgeklickt.
echo "== Backend prüfen =="
if ! node tools/admin-check.mjs "http://127.0.0.1:8321" admin lindenzauber; then
	echo "  Die Einrichtungsseite hat Befunde."
	exit 1
fi

# Die Vorschau entsteht durch Umschreiben der WordPress-Seiten. Dabei kann
# etwas kaputtgehen, das auf der WordPress-Seite selbst läuft – einmal war
# nav.js doppelt eingebunden und das Handy-Menü damit tot. Deshalb wird das
# Paket geprüft, bevor es eingepackt wird.
echo "== Vorschau prüfen =="
if ! node tools/vorschau-check.mjs "$LZ_WORK/vorschau"; then
	echo "  Die Vorschau hat Befunde – nicht ausliefern."
	exit 1
fi

echo "== Vorschau-Paket =="
rm -f dist/lindenzauber-vorschau.zip
( cd "$LZ_WORK" && zip -q -r "$REPO/dist/lindenzauber-vorschau.zip" vorschau )
echo "  dist/lindenzauber-vorschau.zip ($(du -h dist/lindenzauber-vorschau.zip | cut -f1))"

echo
echo "fertig."
