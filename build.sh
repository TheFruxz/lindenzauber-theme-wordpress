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

if [ -z "${LZ_WORK:-}" ] || [ ! -f "${LZ_WORK}/wordpress.zip" ]; then
	echo
	echo "Hinweis: LZ_WORK ist nicht gesetzt (oder unvollständig)."
	echo "Die statische Vorschau wurde nicht neu gebaut."
	exit 0
fi

echo "== Testinstanz aufsetzen =="
bash tools/wp-test-setup.sh > /dev/null
LZ_REPO="$REPO" bash tools/wp-test-inhalte.sh > /dev/null
LZ_REPO="$REPO" bash tools/wp-test-medien.sh > /dev/null

echo "== Vorschau erzeugen =="
php tools/make-vorschau.php > /dev/null

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
