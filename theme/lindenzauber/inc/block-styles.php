<?php
/**
 * Block-Stile.
 *
 * Jeder Eintrag hier erscheint im Editor rechts in der Seitenleiste unter
 * "Stile" als anklickbare Karte. Damit lässt sich das ganze Aussehen der
 * Seite ohne eine Zeile Code einstellen – der eigentliche Zweck dieses
 * Themes. Die zugehörige Gestaltung steht in assets/css/blocks.css.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alle Stile registrieren.
 */
function lz_block_stile() {
	$stile = array(

		// Abschnitte.
		'core/group' => array(
			'lz-hero'   => __( 'Kopfbereich (Sternenhimmel)', 'lindenzauber' ),
			'lz-band'   => __( 'Abschnitt', 'lindenzauber' ),
			'lz-panel'  => __( 'Abschnitt abgesetzt', 'lindenzauber' ),
			'lz-rahmen' => __( 'Kasten mit Goldrahmen', 'lindenzauber' ),
			'lz-tafel'  => __( 'Ablauf-Tafel', 'lindenzauber' ),
		),

		// Überschriften.
		'core/heading' => array(
			'lz-festtitel'      => __( 'Festtitel (Schwungschrift)', 'lindenzauber' ),
			'lz-zeichen-mond'   => __( 'Zeichen: Mond & Sterne', 'lindenzauber' ),
			'lz-zeichen-familie' => __( 'Zeichen: Familie', 'lindenzauber' ),
			'lz-zeichen-ort'    => __( 'Zeichen: Ortsmarke', 'lindenzauber' ),
			'lz-zeichen-zeit'   => __( 'Zeichen: Uhr', 'lindenzauber' ),
			'lz-zeichen-blatt'  => __( 'Zeichen: Lindenblatt', 'lindenzauber' ),
		),

		// Absätze.
		'core/paragraph' => array(
			'lz-ueberzeile' => __( 'Überzeile', 'lindenzauber' ),
			'lz-lead'       => __( 'Einleitungssatz', 'lindenzauber' ),
			'lz-untertitel' => __( 'Untertitel (gesperrt)', 'lindenzauber' ),
			'lz-zeitangabe' => __( 'Zeitangabe', 'lindenzauber' ),
			'lz-rolle'      => __( 'Rolle / Kurzbeschreibung', 'lindenzauber' ),
			'lz-banderole'  => __( 'Banderole (Eintritt frei)', 'lindenzauber' ),
			'lz-hinweis'    => __( 'Hinweis mit Lindenblatt', 'lindenzauber' ),
		),

		// Spalten.
		'core/column' => array(
			'lz-karte'     => __( 'Info-Karte', 'lindenzauber' ),
			'lz-festtag'   => __( 'Festtag-Karte', 'lindenzauber' ),
			'lz-foerderer' => __( 'Förderer-Kachel', 'lindenzauber' ),
			'lz-blattfeld' => __( 'Platzhalter mit Lindenblatt', 'lindenzauber' ),
		),

		'core/columns' => array(
			'lz-portraet' => __( 'Erzähler-Porträt (Bild wechselt die Seite)', 'lindenzauber' ),
		),

		// Listen.
		'core/list' => array(
			'lz-chips'      => __( 'Zeiten als Chips', 'lindenzauber' ),
			'lz-zeitstrahl' => __( 'Zeiten als Zeitstrahl', 'lindenzauber' ),
			'lz-kacheln'    => __( 'Kacheln', 'lindenzauber' ),
			'lz-punkte'     => __( 'Punkte mit Lindenblatt', 'lindenzauber' ),
			'lz-fakten'     => __( 'Fakten (Stichwort + Text)', 'lindenzauber' ),
		),

		// Bilder und Zubehör.
		'core/image' => array(
			'lz-plakat' => __( 'Plakat', 'lindenzauber' ),
			'lz-rahmen' => __( 'Goldrahmen', 'lindenzauber' ),
		),

		'core/gallery' => array(
			'lz-nachtraster' => __( 'Nacht-Raster', 'lindenzauber' ),
		),

		'core/separator' => array(
			'lz-ornament' => __( 'Ornament', 'lindenzauber' ),
		),

		'core/quote' => array(
			'lz-maerchen' => __( 'Märchenzitat', 'lindenzauber' ),
		),

		'core/button' => array(
			'lz-leise' => __( 'Dezent (Textlink)', 'lindenzauber' ),
		),
	);

	foreach ( $stile as $block => $eintraege ) {
		foreach ( $eintraege as $name => $beschriftung ) {
			register_block_style(
				$block,
				array(
					'name'  => $name,
					'label' => $beschriftung,
				)
			);
		}
	}
}
add_action( 'init', 'lz_block_stile' );
