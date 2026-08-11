<?php
/**
 * Vorlagen ("Muster") für den Editor.
 *
 * WordPress liest die Dateien im Ordner patterns/ selbstständig ein.
 * Hier wird nur die eigene Kategorie angelegt, damit sie im Einfügen-Menü
 * unter "Lindenzauber" gebündelt erscheinen.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Eigene Kategorie im Einfügen-Menü.
 */
function lz_pattern_kategorie() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'lindenzauber',
		array(
			'label'       => __( 'Lindenzauber', 'lindenzauber' ),
			'description' => __( 'Fertige Abschnitte im Look des Plakats. Einfügen und einfach die Texte überschreiben.', 'lindenzauber' ),
		)
	);
}
add_action( 'init', 'lz_pattern_kategorie', 9 );

/**
 * Kern-Muster von WordPress.org ausblenden – sie passen nicht zum Fest
 * und machen das Einfügen-Menü unübersichtlich.
 */
function lz_fremde_muster_aus() {
	remove_theme_support( 'core-block-patterns' );
}
add_action( 'after_setup_theme', 'lz_fremde_muster_aus', 20 );
