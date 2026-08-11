<?php
/**
 * Menüs – mit Notfall-Liste, solange im Backend noch keines zugewiesen ist.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adresse einer Seite anhand ihres Permalink-Kürzels.
 *
 * @param string $kuerzel Kürzel der Seite, z. B. "programm".
 * @return string
 */
function lz_seiten_adresse( $kuerzel ) {
	$seite = get_page_by_path( $kuerzel );

	if ( $seite instanceof WP_Post ) {
		return get_permalink( $seite );
	}

	return home_url( '/' . $kuerzel . '/' );
}

/**
 * Gibt eine einfache Linkliste aus.
 *
 * @param array $eintraege Kürzel => Beschriftung.
 */
function lz_notfall_liste( $eintraege ) {
	echo '<ul class="menu">';

	foreach ( $eintraege as $kuerzel => $beschriftung ) {
		$adresse = ( '' === $kuerzel ) ? home_url( '/' ) : lz_seiten_adresse( $kuerzel );
		$aktiv   = ( '' === $kuerzel && is_front_page() ) || ( '' !== $kuerzel && is_page( $kuerzel ) );

		printf(
			'<li class="menu-item%1$s"><a href="%2$s">%3$s</a></li>',
			$aktiv ? ' current-menu-item' : '',
			esc_url( $adresse ),
			esc_html( $beschriftung )
		);
	}

	echo '</ul>';
}

/**
 * Notfall-Hauptmenü.
 */
function lz_hauptmenue_notfall() {
	lz_notfall_liste(
		array(
			''                   => __( 'Start', 'lindenzauber' ),
			'programm'           => __( 'Programm', 'lindenzauber' ),
			'die-erzaehlenden'   => __( 'Die Erzählenden', 'lindenzauber' ),
			'ueber-lindenzauber' => __( 'Über Lindenzauber', 'lindenzauber' ),
			'foerderer'          => __( 'Förderer', 'lindenzauber' ),
		)
	);
}

/**
 * Notfall-Fußmenü. Kontakt, Impressum und Datenschutz gehören in die Fußzeile.
 */
function lz_fussmenue_notfall() {
	lz_notfall_liste(
		array(
			'kontakt'     => __( 'Kontakt', 'lindenzauber' ),
			'impressum'   => __( 'Impressum', 'lindenzauber' ),
			'datenschutz' => __( 'Datenschutz', 'lindenzauber' ),
		)
	);
}
