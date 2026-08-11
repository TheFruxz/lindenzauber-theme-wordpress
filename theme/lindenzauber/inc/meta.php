<?php
/**
 * Angaben für Suchmaschinen und für die Vorschau beim Teilen.
 *
 * Die Beschreibung einer Seite kommt aus dem Feld "Auszug" im Editor
 * (rechte Seitenleiste → Seite → Auszug). Ist dort nichts eingetragen,
 * werden die ersten Sätze der Seite verwendet. So bleibt alles ohne
 * zusätzliches Plugin und ohne Code bearbeitbar.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Beschreibung der aktuell angezeigten Seite.
 *
 * @return string
 */
function lz_beschreibung() {
	if ( is_front_page() ) {
		$objekt = get_queried_object();

		if ( $objekt instanceof WP_Post && has_excerpt( $objekt ) ) {
			return lz_kuerzen( get_the_excerpt( $objekt ) );
		}

		$standard = sprintf(
			/* translators: 1: Titel Samstag, 2: Zeile Samstag, 3: Titel Sonntag, 4: Zeile Sonntag, 5: Ort, 6: Hinweis zum Eintritt */
			__( '%1$s am %2$s und %3$s am %4$s – %5$s. %6$s', 'lindenzauber' ),
			lz_eckdaten( 'tag1_titel' ),
			lz_eckdaten( 'tag1_zeile' ),
			lz_eckdaten( 'tag2_titel' ),
			lz_eckdaten( 'tag2_zeile' ),
			lz_adresse(),
			lz_eckdaten( 'eintritt' )
		);

		$beschreibung = get_bloginfo( 'description' );

		return lz_kuerzen( $beschreibung ? $beschreibung . ' ' . $standard : $standard );
	}

	if ( is_singular() ) {
		$objekt = get_queried_object();

		if ( $objekt instanceof WP_Post ) {
			if ( has_excerpt( $objekt ) ) {
				return lz_kuerzen( get_the_excerpt( $objekt ) );
			}

			$text = wp_strip_all_tags( strip_shortcodes( $objekt->post_content ) );

			if ( '' !== trim( $text ) ) {
				return lz_kuerzen( $text );
			}
		}
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$text = wp_strip_all_tags( get_the_archive_description() );

		if ( '' !== trim( $text ) ) {
			return lz_kuerzen( $text );
		}
	}

	return lz_kuerzen( get_bloginfo( 'description' ) );
}

/**
 * Kürzt einen Text auf eine für Suchmaschinen sinnvolle Länge.
 *
 * @param string $text Ausgangstext.
 * @return string
 */
function lz_kuerzen( $text ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $text ) ) );

	if ( '' === $text ) {
		return '';
	}

	return wp_html_excerpt( $text, 300, '…' );
}

/**
 * Das Bild für die Vorschau beim Teilen.
 *
 * @return string URL oder leerer String.
 */
function lz_teilen_bild() {
	if ( is_singular() && has_post_thumbnail() ) {
		$bild = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

		if ( is_array( $bild ) && ! empty( $bild[0] ) ) {
			return $bild[0];
		}
	}

	$id = (int) get_theme_mod( 'lz_teilen_bild', 0 );

	if ( $id > 0 ) {
		$bild = wp_get_attachment_image_src( $id, 'full' );

		if ( is_array( $bild ) && ! empty( $bild[0] ) ) {
			return $bild[0];
		}
	}

	$logo_id = (int) get_theme_mod( 'custom_logo', 0 );

	if ( $logo_id > 0 ) {
		$bild = wp_get_attachment_image_src( $logo_id, 'full' );

		if ( is_array( $bild ) && ! empty( $bild[0] ) ) {
			return $bild[0];
		}
	}

	return '';
}

/**
 * Meta-Angaben im Kopfbereich ausgeben.
 */
function lz_meta_tags() {
	$beschreibung = lz_beschreibung();
	$titel        = is_front_page() ? get_bloginfo( 'name' ) . ' – ' . __( 'Märchenfest in Bassum', 'lindenzauber' ) : wp_get_document_title();
	$adresse      = is_singular() && ! is_front_page() ? get_permalink() : home_url( '/' );
	$bild         = lz_teilen_bild();

	echo "\n";

	if ( '' !== $beschreibung ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $beschreibung ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $beschreibung ) );
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $beschreibung ) );
	}

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $titel ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular( 'post' ) ? 'article' : 'website' );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $adresse ) );

	if ( '' !== $bild ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $bild ) );
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $bild ) );
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );
	} else {
		printf( '<meta name="twitter:card" content="summary">' . "\n" );
	}

	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $titel ) );
	echo '<meta name="theme-color" content="#0b1224">' . "\n";
}
add_action( 'wp_head', 'lz_meta_tags', 1 );

/**
 * Strukturierte Daten: die beiden Veranstaltungen.
 *
 * Die Angaben stammen aus den Eckdaten im Customizer, nicht aus dem Quelltext.
 */
function lz_event_schema() {
	if ( ! is_front_page() ) {
		return;
	}

	$ort = array(
		'@type'   => 'Place',
		'name'    => lz_eckdaten( 'ort_name' ),
		'address' => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => lz_eckdaten( 'ort_strasse' ),
			'postalCode'      => lz_eckdaten( 'ort_plz' ),
			'addressLocality' => lz_eckdaten( 'ort_stadt' ),
			'addressCountry'  => 'DE',
		),
	);

	$veranstalter = array(
		'@type' => 'Organization',
		'name'  => lz_eckdaten( 'veranstalter' ),
		'url'   => lz_eckdaten( 'veranstalter_url' ),
	);

	$angebot = array(
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'availability'  => 'https://schema.org/InStock',
		'url'           => home_url( '/' ),
	);

	$bild = lz_teilen_bild();

	$termine = array(
		array( 'tag1_titel', 'tag1_start', 'tag1_ende', 'tag1_text' ),
		array( 'tag2_titel', 'tag2_start', 'tag2_ende', 'tag2_text' ),
	);

	foreach ( $termine as $termin ) {
		$start = lz_iso_datum( lz_eckdaten( $termin[1] ) );

		// Ohne verwertbares Startdatum lieber gar keine Angabe machen.
		if ( '' === $start ) {
			continue;
		}

		$event = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'Event',
			'name'                => get_bloginfo( 'name' ) . ' – ' . lz_eckdaten( $termin[0] ),
			'startDate'           => $start,
			'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
			'eventStatus'         => 'https://schema.org/EventScheduled',
			'description'         => lz_eckdaten( $termin[3] ),
			'location'            => $ort,
			'organizer'           => $veranstalter,
			'offers'              => $angebot,
			'isAccessibleForFree' => true,
		);

		$ende = lz_iso_datum( lz_eckdaten( $termin[2] ) );

		if ( '' !== $ende ) {
			$event['endDate'] = $ende;
		}

		if ( '' !== $bild ) {
			$event['image'] = $bild;
		}

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode( $event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		);
	}
}
add_action( 'wp_head', 'lz_event_schema', 5 );
