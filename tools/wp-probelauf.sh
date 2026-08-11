#!/usr/bin/env bash
# Probelauf der Einrichtung: geht SETUP.md von vorn bis hinten durch, auf
# einer frisch aufgesetzten WordPress-Instanz und mit dem fertigen
# dist/lindenzauber.zip – also genau dem Paket, das ausgeliefert wird.
#
# Der Unterschied zu wp-test-setup.sh: dort wird der Quellordner kopiert.
# Hier wird das ZIP installiert wie beim Hochladen im Backend. Fehlt eine
# Datei im Paket, fällt es nur hier auf.
#
#   LZ_WORK=/pfad/zum/arbeitsordner bash tools/wp-probelauf.sh
#
# Erwartet im Arbeitsordner: wordpress.zip, sqlite.zip, wp-cli.phar.
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORK="${LZ_WORK:?LZ_WORK muss gesetzt sein}"
PORT="${LZ_PORT:-8322}"
SITE="$WORK/probe"
WP="php $WORK/wp-cli.phar --path=$SITE --allow-root"
QUELLE="${LZ_QUELLE:-https://lindenzauber.de}"

echo "== 0. Frische WordPress-Instanz =="
pkill -f "php -S 127.0.0.1:$PORT" 2>/dev/null || true
rm -rf "$SITE"
mkdir -p "$SITE"
unzip -q -o "$WORK/wordpress.zip" -d "$WORK/entpackt"
cp -a "$WORK/entpackt/wordpress/." "$SITE/"
unzip -q -o "$WORK/sqlite.zip" -d "$SITE/wp-content/plugins"
cp "$SITE/wp-content/plugins/sqlite-database-integration/db.copy" "$SITE/wp-content/db.php"
sed -i "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#$SITE/wp-content/plugins/sqlite-database-integration#g" "$SITE/wp-content/db.php"
sed -i "s#{SQLITE_PLUGIN}#sqlite-database-integration/load.php#g" "$SITE/wp-content/db.php"

cat > "$SITE/wp-config.php" <<PHP
<?php
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', '' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
define( 'AUTH_KEY', 'probe' );
define( 'SECURE_AUTH_KEY', 'probe' );
define( 'LOGGED_IN_KEY', 'probe' );
define( 'NONCE_KEY', 'probe' );
define( 'AUTH_SALT', 'probe' );
define( 'SECURE_AUTH_SALT', 'probe' );
define( 'LOGGED_IN_SALT', 'probe' );
define( 'NONCE_SALT', 'probe' );
\$table_prefix = 'wp_';
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_HOME', 'http://127.0.0.1:$PORT' );
define( 'WP_SITEURL', 'http://127.0.0.1:$PORT' );
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once ABSPATH . 'wp-settings.php';
PHP

( cd "$SITE" && setsid php -S "127.0.0.1:$PORT" -t "$SITE" \
	> "$WORK/probe-server.log" 2>&1 < /dev/null & )
sleep 2

$WP core install --url="http://127.0.0.1:$PORT" --title="Lindenzauber" \
	--admin_user=admin --admin_password=lindenzauber \
	--admin_email=test@example.invalid --skip-email > /dev/null
$WP option update blogdescription "Märchenfest in Bassum" > /dev/null
$WP rewrite structure '/%postname%/' --hard > /dev/null 2>&1
$WP language core install de_DE > /dev/null 2>&1 || true
$WP site switch-language de_DE > /dev/null 2>&1 || true

echo "== 1. Theme aus dist/lindenzauber.zip installieren =="
$WP theme install "$REPO/dist/lindenzauber.zip" --force --activate

echo "== 2.–4. Logo, Eckdaten, Vorschaubild =="
# Die Bilder der bestehenden Website holen – auf lindenzauber.de sind sie da.
ZIEL="$SITE/wp-content/uploads/lz"
mkdir -p "$ZIEL"
grep -ho '/wp-content/uploads/[^"]*' "$REPO"/inhalte/*.html | sort -u | while read -r pfad; do
	name="$(basename "$pfad")"
	[ -f "$ZIEL/$name" ] || curl -sSL --max-time 60 -o "$ZIEL/$name" "$QUELLE$pfad" 2>/dev/null || true
done

if [ -f "$ZIEL/cropped-cropped-Logo-freigestellt.png" ]; then
	LOGO=$($WP media import "$ZIEL/cropped-cropped-Logo-freigestellt.png" --porcelain 2>/dev/null || true)
	[ -n "$LOGO" ] && $WP theme mod set custom_logo "$LOGO" > /dev/null
	[ -n "$LOGO" ] && $WP theme mod set lz_teilen_bild "$LOGO" > /dev/null
	[ -n "$LOGO" ] && $WP option update site_icon "$LOGO" > /dev/null
fi

echo "== 5. Seiteninhalte einfügen =="
mkdir -p "$WORK/probe-inhalte"
for datei in "$REPO"/inhalte/*.html; do
	sed 's#/wp-content/uploads/[0-9]*/[0-9]*/#/wp-content/uploads/lz/#g' \
		"$datei" > "$WORK/probe-inhalte/$(basename "$datei")"
done

seite() { # slug titel datei auszug status
	local id
	id=$($WP post create --post_type=page --post_title="$2" --post_name="$1" \
		--post_status="${5:-publish}" --post_excerpt="$4" --porcelain \
		"$WORK/probe-inhalte/$3")
	echo "$id"
}

START=$(seite startseite "Lindenzauber" 01-startseite.html \
	"Märchenfest in Bassum am 26. und 27. September 2026: Märchenabend für Erwachsene, Märchentag für Familien. Eintritt frei, keine Anmeldung nötig.")
seite programm "Programm" 02-programm.html \
	"Samstagabend für Erwachsene, Sonntagnachmittag für Familien – alle Zeiten, der Ablauf mit den vier Erzählräumen und die Anfahrt zum Kindergarten KinderReich." > /dev/null
seite die-erzaehlenden "Die Erzählenden" 03-die-erzaehlenden.html \
	"Sechs Erzählerinnen und Erzähler, sechs Geschichtenwelten: wer beim Lindenzauber erzählt, wie sie erzählen und wann Sie wen hören." > /dev/null
seite ueber-lindenzauber "Über Lindenzauber" 04-ueber-lindenzauber.html \
	"Wie aus einer Idee ein Märchenfest wurde: die Menschen dahinter, die beiden Festtage und alles Wichtige in Kürze." > /dev/null
seite sponsoren "Förderer" 05-foerderer.html \
	"Vier Förderer machen den Lindenzauber möglich – deshalb ist der Eintritt an beiden Tagen frei." > /dev/null
seite kontakt "Kontakt" 06-kontakt.html \
	"Fragen zum Lindenzauber? Brigitta Wortmann ist per E-Mail, Telefon und WhatsApp erreichbar." > /dev/null
seite impressum "Impressum" 07-impressum.html \
	"Pflichtangaben nach § 5 DDG: Anbieter, Kontakt und inhaltlich Verantwortliche für die Website lindenzauber.de." > /dev/null
seite fotogalerie "Fotogalerie" 08-fotogalerie.html \
	"Bilder vom Lindenzauber." draft > /dev/null

$WP post create --post_type=page --post_title="Datenschutz" --post_name=datenschutz \
	--post_status=publish --post_excerpt="Datenschutzerklärung für lindenzauber.de." \
	--post_content='<!-- wp:paragraph --><p>Die bestehende Datenschutzerklärung bleibt unverändert.</p><!-- /wp:paragraph -->' > /dev/null

$WP option update show_on_front page > /dev/null
$WP option update page_on_front "$START" > /dev/null

echo "== 7. Menüs zuweisen =="
$WP menu create "Hauptmenü" > /dev/null 2>&1 || true
$WP menu create "Rechtliches" > /dev/null 2>&1 || true
$WP menu item add-custom hauptmenue "Start" "/" > /dev/null
for s in programm die-erzaehlenden ueber-lindenzauber sponsoren; do
	$WP menu item add-post hauptmenue "$($WP post list --post_type=page --name="$s" --field=ID --format=ids)" > /dev/null
done
for s in kontakt impressum datenschutz; do
	$WP menu item add-post rechtliches "$($WP post list --post_type=page --name="$s" --field=ID --format=ids)" > /dev/null
done
$WP menu location assign hauptmenue primary > /dev/null
$WP menu location assign rechtliches legal > /dev/null

echo "== 8. Förderer im Fußbereich =="
sed 's#/wp-content/uploads/[0-9]*/[0-9]*/#/wp-content/uploads/lz/#g' \
	"$REPO/inhalte/09-foerderband-widget.html" > "$WORK/probe-foerderband.txt"
$WP widget add block lz-foerderband --content="$(cat "$WORK/probe-foerderband.txt")" > /dev/null

$WP rewrite flush --hard > /dev/null 2>&1

echo
echo "== Stand der Einrichtungsliste =="
$WP eval 'foreach ( lz_einrichtung_schritte() as $s ) { printf( "  %s %s\n", $s["fertig"] ? "erledigt " : "OFFEN    ", $s["titel"] ); }'

echo
echo "== fertig: http://127.0.0.1:$PORT =="
