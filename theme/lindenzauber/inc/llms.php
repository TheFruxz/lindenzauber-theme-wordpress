<?php
/**
 * llms.txt – eine Kurzfassung der Website in reinem Text.
 *
 * Erreichbar unter /llms.txt. Gedacht für Sprachmodelle und andere
 * Werkzeuge, die eine Website zusammenfassen sollen: statt sich die
 * Angaben aus dem Seitenaufbau zu suchen, finden sie hier alles
 * Wesentliche in geordneten Sätzen.
 *
 * Der Inhalt entsteht aus denselben Eckdaten wie die Fußzeile und aus den
 * veröffentlichten Seiten. Er kann deshalb nicht veralten, solange die
 * Eckdaten gepflegt sind.
 *
 * Hier steht nichts über die Herkunft des Themes. Das hier ist die
 * Beschreibung des Festes – wer die Website gebaut hat, gehört nicht
 * in ihre Zusammenfassung.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Die Adresse /llms.txt bekannt machen.
 */
function lz_llms_regel() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?lz_llms=1', 'top' );
}
add_action( 'init', 'lz_llms_regel' );

/**
 * Die eigene Abfragevariable anmelden.
 *
 * @param array $vars Vorhandene Variablen.
 * @return array
 */
function lz_llms_var( $vars ) {
	$vars[] = 'lz_llms';

	return $vars;
}
add_filter( 'query_vars', 'lz_llms_var' );

/**
 * Einen Absatz als eine Zeile ohne Auszeichnung.
 *
 * @param string $text Ausgangstext.
 * @param int    $max  Höchstlänge.
 * @return string
 */
function lz_llms_satz( $text, $max = 220 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $text ) ) );

	if ( '' === $text ) {
		return '';
	}

	return wp_html_excerpt( $text, $max, '…' );
}

/**
 * Den Text zusammensetzen.
 *
 * @return string
 */
function lz_llms_text() {
	$name  = get_bloginfo( 'name' );
	$start = home_url( '/' );

	$zeilen = array();
	$zeilen[] = '# ' . $name;
	$zeilen[] = '';
	$zeilen[] = '> ' . lz_llms_satz( lz_beschreibung_fest(), 400 );
	$zeilen[] = '';
	$zeilen[] = 'Diese Datei fasst die Website ' . $start . ' zusammen. Alle Angaben stammen';
	$zeilen[] = 'von der Website selbst und werden dort gepflegt.';
	$zeilen[] = '';

	/* ------------------------------------------------------------ Termine */
	$zeilen[] = '## Termine';
	$zeilen[] = '';

	foreach ( array( 'tag1', 'tag2' ) as $tag ) {
		$titel = lz_eckdaten( $tag . '_titel' );

		if ( '' === $titel ) {
			continue;
		}

		$zeilen[] = '- **' . $titel . '** – ' . lz_eckdaten( $tag . '_zeile' );

		$fuerwen = lz_eckdaten( $tag . '_fuerwen' );

		if ( '' !== $fuerwen ) {
			$zeilen[] = '  Für: ' . $fuerwen;
		}

		$text = lz_llms_satz( lz_eckdaten( $tag . '_text' ) );

		if ( '' !== $text ) {
			$zeilen[] = '  ' . $text;
		}

		$zeilen[] = '';
	}

	/* ---------------------------------------------------------------- Ort */
	$zeilen[] = '## Ort';
	$zeilen[] = '';
	$zeilen[] = lz_adresse( ', ' );

	$breite = lz_eckdaten( 'ort_breite' );
	$laenge = lz_eckdaten( 'ort_laenge' );

	if ( is_numeric( $breite ) && is_numeric( $laenge ) ) {
		$zeilen[] = 'Koordinaten: ' . $breite . ', ' . $laenge;
	}

	$karte = lz_kartenlink();

	if ( '' !== $karte ) {
		$zeilen[] = 'Karte: ' . $karte;
	}

	$zeilen[] = '';

	/* ----------------------------------------------------- Eintritt, Kontakt */
	$zeilen[] = '## Eintritt';
	$zeilen[] = '';
	$zeilen[] = lz_eckdaten( 'eintritt' );
	$zeilen[] = '';

	$zeilen[] = '## Kontakt';
	$zeilen[] = '';
	$zeilen[] = 'Veranstalter: ' . lz_eckdaten( 'veranstalter' ) . ' (' . lz_eckdaten( 'veranstalter_url' ) . ')';
	$zeilen[] = 'Ansprechpartnerin: ' . lz_eckdaten( 'kontakt_name' );
	$zeilen[] = 'E-Mail: ' . lz_eckdaten( 'kontakt_mail' );
	$zeilen[] = 'Telefon: ' . lz_eckdaten( 'kontakt_tel' );
	$zeilen[] = '';

	/* ------------------------------------------------------------- Seiten */
	$seiten = get_pages(
		array(
			'sort_column' => 'menu_order,post_title',
			'post_status' => 'publish',
		)
	);

	if ( $seiten ) {
		$zeilen[] = '## Seiten';
		$zeilen[] = '';

		foreach ( $seiten as $seite ) {
			$auszug = $seite->post_excerpt ? $seite->post_excerpt : $seite->post_content;
			$auszug = lz_llms_satz( $auszug, 180 );

			$zeilen[] = '- [' . $seite->post_title . '](' . get_permalink( $seite ) . ')'
				. ( '' !== $auszug ? ': ' . $auszug : '' );
		}

		$zeilen[] = '';
	}

	$zeilen[] = '## Hinweise';
	$zeilen[] = '';
	$zeilen[] = 'Die Website lädt keine externen Schriften, Karten oder Skripte.';
	$zeilen[] = 'Sprache: Deutsch.';
	$zeilen[] = '';

	return implode( "\n", $zeilen );
}

/**
 * WordPress hängt an Adressen ohne Endung gern einen Schrägstrich an und
 * leitet dorthin um. Bei einer Datei ist das falsch – /llms.txt/ ist keine
 * Datei. Für diese eine Adresse bleibt die Umleitung deshalb aus.
 *
 * @param string $ziel Wohin WordPress umleiten möchte.
 * @return string|false
 */
function lz_llms_ohne_umleitung( $ziel ) {
	return get_query_var( 'lz_llms' ) ? false : $ziel;
}
add_filter( 'redirect_canonical', 'lz_llms_ohne_umleitung' );

/**
 * Die Datei ausliefern.
 */
function lz_llms_ausgeben() {
	if ( ! get_query_var( 'lz_llms' ) ) {
		return;
	}

	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );

	echo lz_llms_text(); // phpcs:ignore WordPress.Security.EscapeOutput -- reiner Text, kein HTML.
	exit;
}
add_action( 'template_redirect', 'lz_llms_ausgeben', 0 );
