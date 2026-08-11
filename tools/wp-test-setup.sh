#!/usr/bin/env bash
# Richtet eine lokale WordPress-Instanz mit SQLite ein, installiert das Theme
# und legt alle Seiten mit den Inhalten aus inhalte/ an.
#
# Nur zum Testen und zum Erzeugen der statischen Vorschau – nichts davon
# wird ausgeliefert.
set -euo pipefail

REPO="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WORK="${LZ_WORK:?LZ_WORK muss gesetzt sein}"
SITE="$WORK/site"
PORT="${LZ_PORT:-8321}"
WP="php $WORK/wp-cli.phar --path=$SITE --allow-root"

echo "== WordPress entpacken =="
rm -rf "$SITE"
mkdir -p "$SITE"
unzip -q -o "$WORK/wordpress.zip" -d "$WORK/entpackt"
cp -a "$WORK/entpackt/wordpress/." "$SITE/"

echo "== SQLite-Anbindung =="
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
define( 'AUTH_KEY', 'lindenzauber-test' );
define( 'SECURE_AUTH_KEY', 'lindenzauber-test' );
define( 'LOGGED_IN_KEY', 'lindenzauber-test' );
define( 'NONCE_KEY', 'lindenzauber-test' );
define( 'AUTH_SALT', 'lindenzauber-test' );
define( 'SECURE_AUTH_SALT', 'lindenzauber-test' );
define( 'LOGGED_IN_SALT', 'lindenzauber-test' );
define( 'NONCE_SALT', 'lindenzauber-test' );
\$table_prefix = 'wp_';
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_HOME', 'http://127.0.0.1:$PORT' );
define( 'WP_SITEURL', 'http://127.0.0.1:$PORT' );
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once ABSPATH . 'wp-settings.php';
PHP

echo "== Server starten =="
pkill -f "php -S 127.0.0.1:$PORT" 2>/dev/null || true
# Wichtig: alle Kanäle vom Skript trennen. Sonst hält der Server die
# Ausgabe-Pipe offen und ein aufrufendes "| tail" käme nie zum Ende.
( cd "$SITE" && setsid php -S "127.0.0.1:$PORT" -t "$SITE" \
	> "$WORK/server.log" 2>&1 < /dev/null & )
sleep 2

echo "== WordPress installieren =="
$WP core install \
	--url="http://127.0.0.1:$PORT" \
	--title="Lindenzauber" \
	--admin_user=admin \
	--admin_password=lindenzauber \
	--admin_email=test@example.invalid \
	--skip-email

$WP option update blogdescription "Märchenfest in Bassum"
$WP rewrite structure '/%postname%/' --hard
$WP language core install de_DE || true
$WP site switch-language de_DE || true

echo "== Theme einspielen =="
rm -rf "$SITE/wp-content/themes/lindenzauber"
cp -a "$REPO/theme/lindenzauber" "$SITE/wp-content/themes/lindenzauber"
$WP theme activate lindenzauber

echo "== fertig: http://127.0.0.1:$PORT =="
