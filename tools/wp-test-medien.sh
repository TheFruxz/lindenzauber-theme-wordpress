#!/usr/bin/env bash
# Holt die Bilder, auf die die Inhalte verweisen, in die Testinstanz und
# schreibt die Verweise auf lokale Adressen um. Damit sehen Screenshots und
# statische Vorschau genauso aus wie die spätere Website.
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORK="${LZ_WORK:?LZ_WORK muss gesetzt sein}"
SITE="$WORK/site"
ZIEL="$SITE/wp-content/uploads/lz"
WP="php $WORK/wp-cli.phar --path=$SITE --allow-root"

mkdir -p "$ZIEL" "$WORK/inhalte-lokal"

echo "== Bilder holen =="
grep -ho 'https://lindenzauber\.de/wp-content/uploads/[^"]*' "$REPO"/inhalte/*.html \
	| sort -u \
	| while read -r url; do
		name="$(basename "$url")"
		if [ ! -f "$ZIEL/$name" ]; then
			echo "  $name"
			curl -sS --max-time 60 -o "$ZIEL/$name" "$url" || echo "  (nicht erreichbar: $url)"
		fi
	done

cp "$REPO/medien/anfahrt-kinderreich.png" "$ZIEL/anfahrt-kinderreich.png"

echo "== Inhalte auf lokale Bilder umschreiben =="
for datei in "$REPO"/inhalte/*.html; do
	name="$(basename "$datei")"
	sed -e 's#https://lindenzauber\.de/wp-content/uploads/[0-9]*/[0-9]*/#/wp-content/uploads/lz/#g' \
		-e 's#https://lindenzauber\.de/wp-content/uploads/#/wp-content/uploads/lz/#g' \
		"$datei" > "$WORK/inhalte-lokal/$name"
done


echo "== Seiten aktualisieren =="
akt() {
	local slug="$1" datei="$2"
	local id
	id=$($WP post list --post_type=page --name="$slug" --field=ID --format=ids)
	$WP post update "$id" "$WORK/inhalte-lokal/$datei" > /dev/null
}

akt startseite         01-startseite.html
akt programm           02-programm.html
akt die-erzaehlenden   03-die-erzaehlenden.html
akt ueber-lindenzauber 04-ueber-lindenzauber.html
akt sponsoren          05-foerderer.html
akt kontakt            06-kontakt.html
akt impressum          07-impressum.html
akt fotogalerie        08-fotogalerie.html

echo "== Förderer-Band =="
sed 's#https://lindenzauber\.de/wp-content/uploads/[0-9]*/[0-9]*/#/wp-content/uploads/lz/#g' \
	"$REPO/inhalte/09-foerderband-widget.html" > "$WORK/foerderband-lokal.txt"
for w in $($WP widget list lz-foerderband --fields=id --format=csv 2>/dev/null | tail -n +2); do
	$WP widget delete "$w" > /dev/null 2>&1 || true
done
$WP widget add block lz-foerderband --content="$(cat "$WORK/foerderband-lokal.txt")" > /dev/null

echo "== Logo setzen =="
if [ -f "$ZIEL/cropped-cropped-Logo-freigestellt.png" ]; then
	LOGO_ID=$($WP media import "$ZIEL/cropped-cropped-Logo-freigestellt.png" --porcelain 2>/dev/null || true)
	[ -n "$LOGO_ID" ] && $WP theme mod set custom_logo "$LOGO_ID" > /dev/null
fi

echo "== fertig =="
