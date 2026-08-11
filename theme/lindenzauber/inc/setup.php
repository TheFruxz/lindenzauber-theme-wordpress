<?php
/**
 * Grundeinstellungen des Themes.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Was das Theme kann.
 */
function lz_setup() {
	load_theme_textdomain( 'lindenzauber', LZ_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'custom-line-height' );
	add_theme_support( 'appearance-tools' );
	add_theme_support( 'link-color' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 240,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-brand__name', 'site-brand__tag' ),
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Hauptmenü (oben)', 'lindenzauber' ),
			'legal'   => __( 'Fußzeile: Kontakt, Impressum, Datenschutz', 'lindenzauber' ),
		)
	);

	// Vorschaubild für die Porträts der Erzählenden.
	add_image_size( 'lz-portrait', 720, 900, true );
}
add_action( 'after_setup_theme', 'lz_setup' );

/**
 * Der Förderer-Bereich im Fußbereich.
 *
 * Bewusst als Widget-Bereich: dort greift der normale Block-Editor, die Logos
 * lassen sich also mit Bild-Blöcken tauschen, verlinken und neu anordnen –
 * ohne eine Datei anzufassen. Ist der Bereich leer, wird er nicht ausgegeben;
 * es entsteht also nie ein halb fertiger Streifen. Auf der Startseite bleibt
 * er ebenfalls aus, weil die Förderer dort weiter oben groß gezeigt werden.
 */
function lz_widget_bereiche() {
	register_sidebar(
		array(
			'name'          => __( 'Förderer (Fußbereich)', 'lindenzauber' ),
			'id'            => 'lz-foerderband',
			'description'   => __( 'Die Logos der Förderer im Fußbereich, auf hellen Kacheln im Raster. Am besten ein Spalten-Block mit vier Bild-Blöcken, jedes Bild mit der Website des Förderers verlinkt. Auf der Startseite wird dieser Bereich nicht angezeigt – dort stehen die Förderer weiter oben in groß.', 'lindenzauber' ),
			'before_widget' => '<div id="%1$s" class="lz-sponsorband__widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="screen-reader-text">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'lz_widget_bereiche' );

/**
 * Maximale Inhaltsbreite (für eingebettete Medien).
 */
function lz_content_width() {
	$GLOBALS['content_width'] = 1216;
}
add_action( 'after_setup_theme', 'lz_content_width', 0 );

/**
 * Zusätzliche Klassen am <body>, damit die Vorlagen unterscheidbar sind.
 *
 * @param array $classes Bestehende Klassen.
 * @return array
 */
function lz_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'page-startseite';

		return $classes;
	}

	// Seiten, die ein eigenes Layout mitbringen, bekommen keine schmale Textspalte.
	$gestaltet = apply_filters(
		'lz_gestaltete_seiten',
		array( 'programm', 'die-erzaehlenden', 'ueber-lindenzauber', 'sponsoren', 'foerderer', 'kontakt', 'fotogalerie', 'rueckblick' )
	);

	$objekt = get_queried_object();

	if ( is_page() && $objekt instanceof WP_Post && in_array( $objekt->post_name, $gestaltet, true ) ) {
		$classes[] = 'page-gestaltet';
	} else {
		$classes[] = 'page-schlicht';
	}

	return $classes;
}
add_filter( 'body_class', 'lz_body_classes' );

/**
 * Titel im Browser-Tab: auf der Startseite kurz und sprechend.
 *
 * @param array $parts Titelbestandteile.
 * @return array
 */
function lz_document_title( $parts ) {
	if ( is_front_page() ) {
		$parts['title']   = get_bloginfo( 'name' ) . ' – ' . lz_untertitel();
		$parts['tagline'] = '';
	}

	return $parts;
}
add_filter( 'document_title_parts', 'lz_document_title' );

/**
 * Der Emoji-Nachlader von WordPress wird nicht gebraucht und spart Ladezeit.
 */
function lz_ohne_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 'lz_ohne_emoji' );

/**
 * Ein Symbol für den Browser-Tab, solange keines eingestellt ist.
 *
 * WordPress verwaltet das Website-Icon selbst (Customizer → Website-
 * Informationen → Website-Icon) und gibt dann eigene Verweise aus. Ein Theme
 * kann das nicht übernehmen – wohl aber einspringen, solange dort nichts
 * hinterlegt ist. Sobald Brigitta ein eigenes Icon setzt, gilt ihres.
 */
function lz_favicon() {
	if ( has_site_icon() ) {
		return;
	}

	printf(
		'<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
		esc_url( LZ_URI . '/assets/img/favicon.svg' )
	);
	printf(
		'<link rel="apple-touch-icon" href="%s">' . "\n",
		esc_url( LZ_URI . '/assets/img/apple-touch-icon.png' )
	);
}
add_action( 'wp_head', 'lz_favicon', 3 );

/**
 * Beim Aktivieren des Themes die Adressregeln erneuern.
 *
 * Sonst wäre /llms.txt bis zum nächsten Speichern der Permalink-Einstellungen
 * nicht erreichbar – und niemand käme von allein darauf, dort nachzusehen.
 */
function lz_regeln_erneuern() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'lz_regeln_erneuern' );
