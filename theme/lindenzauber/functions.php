<?php
/**
 * Lindenzauber – Einstiegspunkt des Themes.
 *
 * Die eigentliche Logik liegt in inc/. Diese Datei lädt nur.
 * Es werden keine externen Schriften, Skripte oder Dienste eingebunden.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Die Version steht nur an einer Stelle: im Kopf der style.css. Von dort holt
// sie sich das Theme, damit sie beim Aktualisieren nicht auseinanderlaufen kann –
// sie hängt an den Adressen von CSS und JavaScript und sorgt dafür, dass Browser
// nach einer Änderung nicht die alte Fassung aus dem Zwischenspeicher zeigen.
define( 'LZ_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );
define( 'LZ_DIR', get_template_directory() );
define( 'LZ_URI', get_template_directory_uri() );

require_once LZ_DIR . '/inc/setup.php';
require_once LZ_DIR . '/inc/assets.php';
require_once LZ_DIR . '/inc/template-tags.php';
require_once LZ_DIR . '/inc/eckdaten.php';
require_once LZ_DIR . '/inc/block-styles.php';
require_once LZ_DIR . '/inc/patterns.php';
require_once LZ_DIR . '/inc/meta.php';
require_once LZ_DIR . '/inc/llms.php';
require_once LZ_DIR . '/inc/menus.php';

if ( is_admin() ) {
	require_once LZ_DIR . '/inc/einrichtung.php';
}
