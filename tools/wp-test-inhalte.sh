#!/usr/bin/env bash
# Legt alle Seiten mit den Inhalten aus inhalte/ an und setzt die Menüs.
# Nur für die lokale Testinstanz.
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORK="${LZ_WORK:?LZ_WORK muss gesetzt sein}"
SITE="$WORK/site"
WP="php $WORK/wp-cli.phar --path=$SITE --allow-root"

seite() { # slug titel datei status
	local slug="$1" titel="$2" datei="$3" status="${4:-publish}"
	local id
	id=$($WP post list --post_type=page --name="$slug" --field=ID --format=ids 2>/dev/null || true)

	if [ -n "$id" ]; then
		$WP post update "$id" --post_title="$titel" --post_status="$status" "$datei"
	else
		id=$($WP post create --post_type=page --post_title="$titel" --post_name="$slug" \
			--post_status="$status" --porcelain "$datei")
	fi

	echo "$id"
}

echo "== Seiten anlegen =="
START=$(seite startseite       "Lindenzauber"        "$REPO/inhalte/01-startseite.html")
seite programm                 "Programm"            "$REPO/inhalte/02-programm.html" > /dev/null
seite die-erzaehlenden         "Die Erzählenden"     "$REPO/inhalte/03-die-erzaehlenden.html" > /dev/null
seite ueber-lindenzauber       "Über Lindenzauber"   "$REPO/inhalte/04-ueber-lindenzauber.html" > /dev/null
seite sponsoren                "Förderer"            "$REPO/inhalte/05-foerderer.html" > /dev/null
seite kontakt                  "Kontakt"             "$REPO/inhalte/06-kontakt.html" > /dev/null
seite impressum                "Impressum"           "$REPO/inhalte/07-impressum.html" > /dev/null
seite fotogalerie              "Fotogalerie"         "$REPO/inhalte/08-fotogalerie.html" draft > /dev/null

# Datenschutz bleibt inhaltlich unverändert; für den Test genügt ein Platzhalter.
if [ -z "$($WP post list --post_type=page --name=datenschutz --field=ID --format=ids 2>/dev/null || true)" ]; then
	$WP post create --post_type=page --post_title="Datenschutz" --post_name=datenschutz \
		--post_status=publish --post_content='<!-- wp:heading --><h2 class="wp-block-heading">Datenschutzerklärung</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Die bestehende Datenschutzerklärung bleibt unverändert. Sie wird vom Theme nur neu gestaltet.</p><!-- /wp:paragraph -->' > /dev/null
fi

echo "== Startseite festlegen =="
$WP option update show_on_front page
$WP option update page_on_front "$START"

echo "== Menüs =="
$WP menu create "Hauptmenü" 2>/dev/null || true
$WP menu create "Rechtliches" 2>/dev/null || true

# Vorhandene Einträge entfernen, damit ein erneuter Lauf nichts verdoppelt.
for m in hauptmenue rechtliches; do
	for item in $($WP menu item list "$m" --field=db_id --format=ids 2>/dev/null || true); do
		$WP menu item delete "$item" > /dev/null 2>&1 || true
	done
done

$WP menu item add-custom hauptmenue "Start" "/" > /dev/null
for s in programm die-erzaehlenden ueber-lindenzauber sponsoren; do
	id=$($WP post list --post_type=page --name="$s" --field=ID --format=ids)
	$WP menu item add-post hauptmenue "$id" > /dev/null
done

for s in kontakt impressum datenschutz; do
	id=$($WP post list --post_type=page --name="$s" --field=ID --format=ids)
	$WP menu item add-post rechtliches "$id" > /dev/null
done

$WP menu location assign hauptmenue primary
$WP menu location assign rechtliches legal

echo "== Förderer-Band =="
php -r '
$work = getenv("LZ_WORK");
$repo = getenv("LZ_REPO");
$inhalt = file_get_contents($repo . "/inhalte/09-foerderband-widget.html");
file_put_contents($work . "/foerderband.txt", $inhalt);
'
$WP widget reset --all > /dev/null 2>&1 || true
$WP widget add block lz-foerderband --content="$(cat "$WORK/foerderband.txt")" > /dev/null

echo "== Auszüge für die Metadaten =="
$WP post update "$START" --post_excerpt="Das große Märchenfest in Bassum: Märchenabend für Erwachsene am Samstag, 26. September 2026, und Märchentag für Familien am Sonntag, 27. September 2026, im Kindergarten KinderReich. Eintritt frei, keine Anmeldung nötig." > /dev/null

echo "== fertig =="
