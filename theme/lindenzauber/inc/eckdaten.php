<?php
/**
 * Eckdaten des Festes – Termine, Ort, Kontakt.
 *
 * Diese Angaben stehen bewusst nicht im Quelltext, sondern unter
 * "Design → Customizer → Lindenzauber: Eckdaten". Sie versorgen die Fußzeile
 * und die Angaben für Google (strukturierte Daten). Damit stimmt beides auch
 * im nächsten Jahr, ohne dass jemand eine Datei anfassen muss.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alle Felder mit ihren Voreinstellungen.
 *
 * @return array
 */
function lz_eckdaten_felder() {
	return array(
		// Samstag.
		'tag1_titel'   => array( 'Ein Abend voller Märchen', 'Samstag: Überschrift' ),
		'tag1_start'   => array( '2026-09-26 19:00', 'Samstag: Beginn (JJJJ-MM-TT SS:MM)' ),
		'tag1_ende'    => array( '2026-09-26 21:30', 'Samstag: Ende (JJJJ-MM-TT SS:MM)' ),
		'tag1_zeile'   => array( 'Samstag, 26. September 2026, 19 Uhr', 'Samstag: Zeile für die Fußzeile' ),
		'tag1_text'    => array( 'Märchenabend für Erwachsene und Jugendliche. Frei erzählte Märchen, Eintritt frei.', 'Samstag: kurze Beschreibung' ),
		'tag1_fuerwen' => array( 'Erwachsene und Jugendliche', 'Samstag: für wen' ),

		// Sonntag.
		'tag2_titel'   => array( 'Märchentag für Familien', 'Sonntag: Überschrift' ),
		'tag2_start'   => array( '2026-09-27 13:00', 'Sonntag: Beginn (JJJJ-MM-TT SS:MM)' ),
		'tag2_ende'    => array( '2026-09-27 17:00', 'Sonntag: Ende (JJJJ-MM-TT SS:MM)' ),
		'tag2_zeile'   => array( 'Sonntag, 27. September 2026, 13–17 Uhr', 'Sonntag: Zeile für die Fußzeile' ),
		'tag2_text'    => array( 'Märchentag für Familien mit Kindern ab 3 Jahren. Erzählungen um 13.30, 14.30, 15.30 und 16.30 Uhr, dazu Kaffee und Kuchen. Eintritt frei.', 'Sonntag: kurze Beschreibung' ),
		'tag2_fuerwen' => array( 'Familien mit Kindern ab 3 Jahren', 'Sonntag: für wen' ),
		'tag2_alter'   => array( '3-', 'Sonntag: Mindestalter für Suchmaschinen (z. B. „3-“)' ),

		// Ort.
		'ort_name'     => array( 'Kindergarten KinderReich', 'Ort: Name' ),
		'ort_strasse'  => array( 'Bürgermeister-Lienhop-Straße 1A', 'Ort: Straße und Hausnummer' ),
		'ort_plz'      => array( '27211', 'Ort: Postleitzahl' ),
		'ort_stadt'    => array( 'Bassum', 'Ort: Stadt' ),
		'ort_breite'   => array( '52.8517566', 'Ort: Breitengrad (für Karten und Suchmaschinen)' ),
		'ort_laenge'   => array( '8.7355695', 'Ort: Längengrad' ),

		// Eintritt und Kontakt.
		'eintritt'     => array( 'Eintritt frei · keine Anmeldung nötig', 'Hinweis zum Eintritt' ),
		'kontakt_name' => array( 'Brigitta Wortmann', 'Kontakt: Name' ),
		'kontakt_mail' => array( 'info@lindenzauber.de', 'Kontakt: E-Mail' ),
		'kontakt_tel'  => array( '0173 769 99 63', 'Kontakt: Telefonnummer (wie sie angezeigt wird)' ),
		'kontakt_tel_link' => array( '+491737699963', 'Kontakt: Telefonnummer zum Anrufen (+49…)' ),

		// Veranstalter.
		'veranstalter'     => array( 'Mütter-Kinder-Zentrum Bassum e. V.', 'Veranstalter: Name' ),
		'veranstalter_url' => array( 'https://www.muekize-bassum.de/', 'Veranstalter: Website' ),

		// Verweise.
		'link_maerchentruhe' => array( 'https://www.diemaerchentruhe.de/', 'Website „Die Märchentruhe“ von Brigitta Wortmann' ),

		// Fußzeile.
		'fusszeile_hinweis' => array( 'Diese Website lädt keine externen Schriften, Karten oder Skripte.', 'Fußzeile: Hinweis ganz unten (leer lassen = weglassen)' ),
	);
}

/**
 * Liest eine Eckdaten-Angabe.
 *
 * @param string $schluessel Feldname.
 * @return string
 */
function lz_eckdaten( $schluessel ) {
	$felder = lz_eckdaten_felder();

	if ( ! isset( $felder[ $schluessel ] ) ) {
		return '';
	}

	$wert = get_theme_mod( 'lz_' . $schluessel, $felder[ $schluessel ][0] );

	return is_string( $wert ) ? trim( $wert ) : '';
}

/**
 * Die vollständige Adresse als eine Zeile.
 *
 * @param string $trenner Trennzeichen.
 * @return string
 */
function lz_adresse( $trenner = ', ' ) {
	$teile = array_filter(
		array(
			lz_eckdaten( 'ort_name' ),
			lz_eckdaten( 'ort_strasse' ),
			trim( lz_eckdaten( 'ort_plz' ) . ' ' . lz_eckdaten( 'ort_stadt' ) ),
		)
	);

	return implode( $trenner, $teile );
}

/**
 * Der Untertitel des Festes – „Märchenfest in Bassum“.
 *
 * Er steht dort, wo WordPress ihn ohnehin führt: unter *Einstellungen →
 * Allgemein → Untertitel*. Von dort speist er den Schriftzug im Kopfbereich,
 * den Titel im Browser-Tab und die Angaben für Suchmaschinen. Vorher stand
 * derselbe Satz an vier Stellen im Quelltext – bei einer Änderung wäre die
 * Website mit sich selbst uneins gewesen.
 *
 * @return string
 */
function lz_untertitel() {
	$untertitel = trim( (string) get_bloginfo( 'description' ) );

	return '' !== $untertitel ? $untertitel : __( 'Märchenfest in Bassum', 'lindenzauber' );
}

/**
 * Der Verweis auf die Karte, aus den Koordinaten gebaut.
 *
 * @return string Leerer String, wenn keine Koordinaten hinterlegt sind.
 */
function lz_kartenlink() {
	$breite = lz_eckdaten( 'ort_breite' );
	$laenge = lz_eckdaten( 'ort_laenge' );

	if ( ! is_numeric( $breite ) || ! is_numeric( $laenge ) ) {
		return '';
	}

	return sprintf(
		'https://www.openstreetmap.org/?mlat=%1$s&mlon=%2$s#map=17/%1$s/%2$s',
		rawurlencode( $breite ),
		rawurlencode( $laenge )
	);
}

/**
 * Wandelt "2026-09-26 19:00" in das von Google erwartete Format um.
 *
 * @param string $wert Datum und Uhrzeit.
 * @return string Leerer String, wenn der Wert nicht lesbar ist.
 */
function lz_iso_datum( $wert ) {
	$wert = trim( (string) $wert );

	if ( '' === $wert ) {
		return '';
	}

	try {
		$zone = new DateTimeZone( wp_timezone_string() );
		$zeit = new DateTime( $wert, $zone );
	} catch ( Exception $e ) {
		return '';
	}

	return $zeit->format( 'c' );
}

/**
 * Die Felder im Customizer anlegen.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function lz_customizer( $wp_customize ) {
	$wp_customize->add_panel(
		'lz_panel',
		array(
			'title'       => __( 'Lindenzauber', 'lindenzauber' ),
			'description' => __( 'Termine, Ort und Kontakt. Diese Angaben erscheinen in der Fußzeile und werden an Suchmaschinen weitergegeben.', 'lindenzauber' ),
			'priority'    => 20,
		)
	);

	$wp_customize->add_section(
		'lz_eckdaten',
		array(
			'title' => __( 'Eckdaten des Festes', 'lindenzauber' ),
			'panel' => 'lz_panel',
		)
	);

	foreach ( lz_eckdaten_felder() as $schluessel => $angaben ) {
		list( $standard, $beschriftung ) = $angaben;

		$wp_customize->add_setting(
			'lz_' . $schluessel,
			array(
				'default'           => $standard,
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			'lz_' . $schluessel,
			array(
				'label'   => $beschriftung,
				'section' => 'lz_eckdaten',
				'type'    => in_array( $schluessel, array( 'tag1_text', 'tag2_text' ), true ) ? 'textarea' : 'text',
			)
		);
	}

	// Bild für die Vorschau beim Teilen (WhatsApp, Facebook, Signal …).
	$wp_customize->add_section(
		'lz_teilen',
		array(
			'title'       => __( 'Vorschaubild beim Teilen', 'lindenzauber' ),
			'description' => __( 'Dieses Bild erscheint, wenn jemand einen Link zur Website in WhatsApp, Facebook oder Signal einfügt. Am besten das Plakat.', 'lindenzauber' ),
			'panel'       => 'lz_panel',
		)
	);

	$wp_customize->add_setting(
		'lz_teilen_bild',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'lz_teilen_bild',
			array(
				'label'     => __( 'Bild auswählen', 'lindenzauber' ),
				'section'   => 'lz_teilen',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'lz_customizer' );
