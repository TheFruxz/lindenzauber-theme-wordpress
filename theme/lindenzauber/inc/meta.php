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
 * Der Satz, der das Fest beschreibt – aus den Eckdaten gebaut.
 *
 * Wird für die Startseite gebraucht und für die llms.txt, die außerhalb
 * jeder Seite ausgeliefert wird und deshalb keinen Beitrag zum Kürzen hat.
 *
 * @return string
 */
function lz_beschreibung_fest() {
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

		return lz_beschreibung_fest();
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

	// Werkzeug-Angabe, wie WordPress sie für sich selbst ausgibt.
	printf(
		'<meta name="generator" content="%s">' . "\n",
		esc_attr( sprintf( 'Lindenzauber-Theme %s · fruxz.dev', LZ_VERSION ) )
	);
}
add_action( 'wp_head', 'lz_meta_tags', 1 );

/**
 * Maße und Alternativtext des Vorschaubildes.
 *
 * Steht getrennt, weil dafür der Anhang nachgeschlagen werden muss – das
 * lohnt nur, wenn es überhaupt ein Bild gibt.
 */
function lz_teilen_bild_details() {
	$bild = lz_teilen_bild();

	if ( '' === $bild ) {
		return;
	}

	$id = attachment_url_to_postid( $bild );

	if ( $id > 0 ) {
		$daten = wp_get_attachment_image_src( $id, 'full' );

		if ( is_array( $daten ) && ! empty( $daten[1] ) && ! empty( $daten[2] ) ) {
			printf( '<meta property="og:image:width" content="%d">' . "\n", (int) $daten[1] );
			printf( '<meta property="og:image:height" content="%d">' . "\n", (int) $daten[2] );
		}

		$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	} else {
		$alt = '';
	}

	if ( '' === $alt ) {
		$alt = sprintf(
			/* translators: %s: Name der Website */
			__( 'Plakat zum %s', 'lindenzauber' ),
			get_bloginfo( 'name' )
		);
	}

	printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $alt ) );
	printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $alt ) );
}
add_action( 'wp_head', 'lz_teilen_bild_details', 2 );

/**
 * Der Ort als strukturierte Angabe, mit Koordinaten und Kartenverweis.
 *
 * @return array
 */
function lz_schema_ort() {
	$ort = array(
		'@type'   => 'Place',
		'@id'     => home_url( '/#ort' ),
		'name'    => lz_eckdaten( 'ort_name' ),
		'address' => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => lz_eckdaten( 'ort_strasse' ),
			'postalCode'      => lz_eckdaten( 'ort_plz' ),
			'addressLocality' => lz_eckdaten( 'ort_stadt' ),
			'addressCountry'  => 'DE',
		),
	);

	$breite = lz_eckdaten( 'ort_breite' );
	$laenge = lz_eckdaten( 'ort_laenge' );

	if ( is_numeric( $breite ) && is_numeric( $laenge ) ) {
		$ort['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) $breite,
			'longitude' => (float) $laenge,
		);
	}

	$karte = lz_kartenlink();

	if ( '' !== $karte ) {
		$ort['hasMap'] = $karte;
	}

	return $ort;
}

/**
 * Der Veranstalter als strukturierte Angabe.
 *
 * @return array
 */
function lz_schema_veranstalter() {
	$organisation = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#veranstalter' ),
		'name'  => lz_eckdaten( 'veranstalter' ),
	);

	$url = lz_eckdaten( 'veranstalter_url' );

	if ( '' !== $url ) {
		$organisation['url'] = $url;
	}

	$kontakt = array_filter(
		array(
			'@type'       => 'ContactPoint',
			'contactType' => __( 'Auskunft zum Fest', 'lindenzauber' ),
			'name'        => lz_eckdaten( 'kontakt_name' ),
			'email'       => lz_eckdaten( 'kontakt_mail' ),
			'telephone'   => lz_eckdaten( 'kontakt_tel_link' ),
		)
	);

	if ( count( $kontakt ) > 2 ) {
		$organisation['contactPoint'] = $kontakt;
	}

	return $organisation;
}

/**
 * Ein einzelner Festtag als Untertermin.
 *
 * @param string $nummer      "tag1" oder "tag2".
 * @param array  $gemeinsames Ort, Veranstalter, Angebot, Bild.
 * @return array Leeres Array, wenn kein brauchbares Datum hinterlegt ist.
 */
function lz_schema_tag( $nummer, $gemeinsames ) {
	$start = lz_iso_datum( lz_eckdaten( $nummer . '_start' ) );

	// Ohne verwertbares Startdatum lieber gar keine Angabe machen.
	if ( '' === $start ) {
		return array();
	}

	$event = array(
		'@type'               => 'Event',
		'@id'                 => home_url( '/#' . $nummer ),
		'name'                => lz_eckdaten( $nummer . '_titel' ),
		'startDate'           => $start,
		'description'         => lz_eckdaten( $nummer . '_text' ),
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'inLanguage'          => 'de',
		'isAccessibleForFree' => true,
		'location'            => array( '@id' => $gemeinsames['ort']['@id'] ),
		'organizer'           => array( '@id' => $gemeinsames['veranstalter']['@id'] ),
		'offers'              => $gemeinsames['angebot'],
	);

	$ende = lz_iso_datum( lz_eckdaten( $nummer . '_ende' ) );

	if ( '' !== $ende ) {
		$event['endDate'] = $ende;
	}

	$fuerwen = lz_eckdaten( $nummer . '_fuerwen' );

	if ( '' !== $fuerwen ) {
		$event['audience'] = array(
			'@type'        => 'Audience',
			'audienceType' => $fuerwen,
		);
	}

	$alter = lz_eckdaten( $nummer . '_alter' );

	if ( '' !== $alter ) {
		$event['typicalAgeRange'] = $alter;
	}

	if ( '' !== $gemeinsames['bild'] ) {
		$event['image'] = $gemeinsames['bild'];
	}

	return $event;
}

/**
 * Strukturierte Daten für die ganze Website.
 *
 * Ein zusammenhängender Graph statt loser Einzelangaben: das Fest mit beiden
 * Tagen als Unterterminen, der Ort mit Koordinaten, der Veranstalter mit
 * Kontakt, dazu die Website und – auf Unterseiten – die Seite selbst mit
 * ihrem Weg dorthin. Alles kommt aus den Eckdaten im Customizer.
 *
 * Hier steht bewusst nichts über die Herkunft des Themes. Diese Angaben sind
 * die Beschreibung des Festes, nicht die der Website-Werkstatt.
 */
function lz_schema() {
	$ort          = lz_schema_ort();
	$veranstalter = lz_schema_veranstalter();
	$bild         = lz_teilen_bild();

	$angebot = array(
		'@type'         => 'Offer',
		'price'         => '0',
		'priceCurrency' => 'EUR',
		'availability'  => 'https://schema.org/InStock',
		'url'           => home_url( '/' ),
	);

	$gemeinsames = compact( 'ort', 'veranstalter', 'angebot', 'bild' );

	$tage = array_filter(
		array(
			lz_schema_tag( 'tag1', $gemeinsames ),
			lz_schema_tag( 'tag2', $gemeinsames ),
		)
	);

	$graph = array(
		array(
			'@type'      => 'WebSite',
			'@id'        => home_url( '/#website' ),
			'url'        => home_url( '/' ),
			'name'       => get_bloginfo( 'name' ),
			'inLanguage' => 'de',
			'publisher'  => array( '@id' => $veranstalter['@id'] ),
		),
		$veranstalter,
		$ort,
	);

	if ( $tage ) {
		$starts = array_column( $tage, 'startDate' );
		$enden  = array_column( $tage, 'endDate' );

		$fest = array(
			'@type'               => 'Festival',
			'@id'                 => home_url( '/#fest' ),
			'name'                => get_bloginfo( 'name' ),
			'alternateName'       => __( 'Märchenfest in Bassum', 'lindenzauber' ),
			'description'         => lz_beschreibung(),
			'url'                 => home_url( '/' ),
			'startDate'           => min( $starts ),
			'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
			'eventStatus'         => 'https://schema.org/EventScheduled',
			'inLanguage'          => 'de',
			'isAccessibleForFree' => true,
			'location'            => array( '@id' => $ort['@id'] ),
			'organizer'           => array( '@id' => $veranstalter['@id'] ),
			'offers'              => $angebot,
			'subEvent'            => $tage,
			'keywords'            => __( 'Märchen, Erzählkunst, Familienfest, Bassum, Lindenzauber', 'lindenzauber' ),
		);

		if ( $enden ) {
			$fest['endDate'] = max( $enden );
		}

		if ( '' !== $bild ) {
			$fest['image'] = $bild;
		}

		$graph[] = $fest;
	}

	// Auf Unterseiten zusätzlich die Seite selbst und der Weg dorthin.
	if ( is_singular() && ! is_front_page() ) {
		$graph[] = array(
			'@type'      => 'WebPage',
			'@id'        => get_permalink(),
			'url'        => get_permalink(),
			'name'       => get_the_title(),
			'description' => lz_beschreibung(),
			'inLanguage' => 'de',
			'isPartOf'   => array( '@id' => home_url( '/#website' ) ),
			'about'      => array( '@id' => home_url( '/#fest' ) ),
		);

		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => get_bloginfo( 'name' ),
					'item'     => home_url( '/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => get_the_title(),
				),
			),
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			array(
				'@context' => 'https://schema.org',
				'@graph'   => array_values( $graph ),
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		)
	);
}
add_action( 'wp_head', 'lz_schema', 5 );

/**
 * Die Herkunft des Themes – als Kommentar im Quelltext.
 *
 * Bewusst nur hier und nicht in den strukturierten Daten: dort steht, worum
 * es auf der Website geht, und das ist das Fest und sein Veranstalter.
 */
function lz_signatur() {
	printf(
		"\n<!--\n  %s\n  %s\n  %s\n-->\n",
		'Lindenzauber – Theme ' . LZ_VERSION,
		'Entwickelt von Fruxz · https://fruxz.dev/',
		'Inhalte und Veranstaltung: ' . lz_eckdaten( 'veranstalter' )
	);
}
add_action( 'wp_head', 'lz_signatur', 0 );
