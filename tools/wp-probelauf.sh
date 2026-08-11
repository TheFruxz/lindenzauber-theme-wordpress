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

echo "== Bilder der bestehenden Website holen =="
# Auf lindenzauber.de liegen sie – die Inhalte verweisen darauf.
ZIEL="$SITE/wp-content/uploads/lz"
mkdir -p "$ZIEL"
grep -ho '/wp-content/uploads/[^"]*' "$REPO"/inhalte/*.html | sort -u | while read -r pfad; do
	name="$(basename "$pfad")"
	[ -f "$ZIEL/$name" ] || curl -sSL --max-time 60 -o "$ZIEL/$name" "$QUELLE$pfad" 2>/dev/null || true
done

echo "== 2. Seiten importieren =="
# Die zwei Seiten, die es auf der bestehenden Website schon gibt: die eine
# soll weichen, die andere ausdrücklich nicht. Genau wie im Ernstfall.
$WP post create --post_type=page --post_title="Lindenzauber 2025" --post_name=veraltet \
	--post_status=publish \
	--post_content='<!-- wp:paragraph --><p>Alter Stand.</p><!-- /wp:paragraph -->' > /dev/null
$WP post create --post_type=page --post_title="Datenschutz" --post_name=datenschutz \
	--post_status=publish --post_excerpt="Datenschutzerklärung für lindenzauber.de." \
	--post_content='<!-- wp:paragraph --><p>Die bestehende Datenschutzerklärung bleibt unverändert.</p><!-- /wp:paragraph -->' > /dev/null

# Genau der Weg aus SETUP.md, nur ohne Mausklicks: das ausgelieferte Paket
# lesen, die Vorschau erzeugen, alles Fremde stilllegen, ausführen.
$WP eval '
$paket = lz_import_auspacken( "'"$REPO"'/dist/lindenzauber-inhalte.zip" );
if ( is_wp_error( $paket ) ) { WP_CLI::error( $paket->get_error_message() ); }
$vorschau = lz_import_vorschau( $paket["plan"], $paket["pfad"] );
foreach ( $vorschau["vorhaben"] as $p ) { WP_CLI::log( sprintf( "  %-14s %s", $p["was"], $p["titel"] ) ); }
foreach ( $vorschau["fremde"] as $f ) { WP_CLI::log( "  stilllegen     " . $f["titel"] ); }
foreach ( lz_import_ausfuehren( $paket["plan"], $paket["pfad"], wp_list_pluck( $vorschau["fremde"], "id" ) ) as $m ) {
	WP_CLI::log( "  → " . $m );
}
lz_import_aufraeumen( $paket["pfad"] );
'

# Die Inhalte zeigen auf /wp-content/uploads/<jahr>/<monat>/ – auf der echten
# Website liegen die Bilder dort. In der Probe liegen sie in uploads/lz.
$WP search-replace --regex '/wp-content/uploads/[0-9]{4}/[0-9]{2}/' '/wp-content/uploads/lz/' \
	--all-tables-with-prefix --quiet > /dev/null 2>&1 || true

echo "== 3.–6. Logo, Symbol, Vorschaubild =="
if [ -f "$ZIEL/cropped-cropped-Logo-freigestellt.png" ]; then
	LOGO=$($WP media import "$ZIEL/cropped-cropped-Logo-freigestellt.png" --porcelain 2>/dev/null || true)
	[ -n "$LOGO" ] && $WP theme mod set custom_logo "$LOGO" > /dev/null
	[ -n "$LOGO" ] && $WP theme mod set lz_teilen_bild "$LOGO" > /dev/null
	[ -n "$LOGO" ] && $WP option update site_icon "$LOGO" > /dev/null
fi

echo "== 8. Menüs zuweisen =="
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

$WP rewrite flush --hard > /dev/null 2>&1

echo
echo "== Was der Import hinterlassen hat =="
$WP post list --post_type=page --post_status=publish,draft --fields=post_name,post_status,post_title

echo
echo "== Stand der Einrichtungsliste =="
$WP eval 'foreach ( lz_einrichtung_schritte() as $s ) { printf( "  %s %s\n", $s["fertig"] ? "erledigt " : "OFFEN    ", $s["titel"] ); }'

echo
echo "== fertig: http://127.0.0.1:$PORT =="
