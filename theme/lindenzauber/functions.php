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

define( 'LZ_VERSION', '3.0.0' );
define( 'LZ_DIR', get_template_directory() );
define( 'LZ_URI', get_template_directory_uri() );

require_once LZ_DIR . '/inc/setup.php';
require_once LZ_DIR . '/inc/assets.php';
require_once LZ_DIR . '/inc/template-tags.php';
require_once LZ_DIR . '/inc/eckdaten.php';
require_once LZ_DIR . '/inc/block-styles.php';
require_once LZ_DIR . '/inc/patterns.php';
require_once LZ_DIR . '/inc/meta.php';
require_once LZ_DIR . '/inc/menus.php';
