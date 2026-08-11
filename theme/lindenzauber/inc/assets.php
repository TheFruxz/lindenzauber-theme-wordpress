<?php
/**
 * Stylesheets und Skripte.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Liefert eine Versionsnummer anhand des Änderungsdatums der Datei.
 * So sehen Besucherinnen und Besucher nach einer Änderung sofort das Neue.
 *
 * @param string $relativ Pfad innerhalb des Themes, z. B. "assets/css/blocks.css".
 * @return string
 */
function lz_version( $relativ ) {
	$pfad = LZ_DIR . '/' . ltrim( $relativ, '/' );

	return file_exists( $pfad ) ? (string) filemtime( $pfad ) : LZ_VERSION;
}

/**
 * Website: Stylesheets und Skripte einbinden.
 */
function lz_assets() {
	wp_enqueue_style(
		'lindenzauber',
		get_stylesheet_uri(),
		array(),
		lz_version( 'style.css' )
	);

	wp_enqueue_style(
		'lindenzauber-bloecke',
		LZ_URI . '/assets/css/blocks.css',
		array( 'lindenzauber' ),
		lz_version( 'assets/css/blocks.css' )
	);

	wp_enqueue_script(
		'lindenzauber-nav',
		LZ_URI . '/assets/js/nav.js',
		array(),
		lz_version( 'assets/js/nav.js' ),
		array( 'strategy' => 'defer', 'in_footer' => true )
	);

	wp_enqueue_script(
		'lindenzauber-mehr',
		LZ_URI . '/assets/js/mehr-anzeigen.js',
		array(),
		lz_version( 'assets/js/mehr-anzeigen.js' ),
		array( 'strategy' => 'defer', 'in_footer' => true )
	);

	wp_localize_script(
		'lindenzauber-mehr',
		'lzMehrTexte',
		array(
			'mehr'    => __( 'Mehr anzeigen', 'lindenzauber' ),
			'weniger' => __( 'Weniger anzeigen', 'lindenzauber' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'lz_assets' );

/**
 * Editor: dieselbe Gestaltung wie auf der Website.
 */
function lz_editor_assets() {
	add_editor_style(
		array(
			'assets/css/blocks.css',
			'assets/css/editor.css',
		)
	);
}
add_action( 'after_setup_theme', 'lz_editor_assets' );

/**
 * Die beiden Schriften früh laden, damit im Kopfbereich nichts nachspringt.
 */
function lz_font_preload() {
	$dateien = array(
		'assets/fonts/lato-400-latin.woff2',
		'assets/fonts/great-vibes-400-latin.woff2',
	);

	foreach ( $dateien as $datei ) {
		if ( ! file_exists( LZ_DIR . '/' . $datei ) ) {
			continue;
		}

		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( LZ_URI . '/' . $datei )
		);
	}
}
add_action( 'wp_head', 'lz_font_preload', 2 );
