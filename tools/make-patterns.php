<?php
/**
 * Erzeugt aus den Seiteninhalten die Block-Muster des Themes.
 *
 * Die Dateien in inhalte/ sind die einzige Quelle. So können Muster im
 * Editor und Copy-und-Paste-Vorlage nie auseinanderlaufen.
 *
 * Aufruf:  php tools/make-patterns.php
 */

$repo     = dirname( __DIR__ );
$quelle   = $repo . '/inhalte';
$ziel     = $repo . '/theme/lindenzauber/patterns';

@mkdir( $ziel, 0777, true );

$seiten = array(
	'01-startseite.html' => array(
		'datei'        => 'seite-startseite.php',
		'titel'        => 'Seite: Startseite',
		'slug'         => 'lindenzauber/seite-startseite',
		'beschreibung' => 'Die komplette Startseite: Kopfbereich mit Sternenhimmel, die beiden Festtage, Plakat, gute Gründe, Erzählende und Förderer.',
	),
	'02-programm.html' => array(
		'datei'        => 'seite-programm.php',
		'titel'        => 'Seite: Programm',
		'slug'         => 'lindenzauber/seite-programm',
		'beschreibung' => 'Die Programmseite: Samstagabend und Sonntagnachmittag als erkennbare Geschwister, dazu Ort und Anfahrt mit Karte.',
	),
	'03-die-erzaehlenden.html' => array(
		'datei'        => 'seite-die-erzaehlenden.php',
		'titel'        => 'Seite: Die Erzählenden',
		'slug'         => 'lindenzauber/seite-die-erzaehlenden',
		'beschreibung' => 'Sechs Porträts. Das Bild wechselt automatisch die Seite, lange Texte bekommen einen Schalter „Mehr anzeigen“.',
	),
	'04-ueber-lindenzauber.html' => array(
		'datei'        => 'seite-ueber-lindenzauber.php',
		'titel'        => 'Seite: Über Lindenzauber',
		'slug'         => 'lindenzauber/seite-ueber-lindenzauber',
		'beschreibung' => 'Die beiden Tage getrennt dargestellt, alle Eckdaten kompakt und die Beteiligten als drei Karten.',
	),
	'05-foerderer.html' => array(
		'datei'        => 'seite-foerderer.php',
		'titel'        => 'Seite: Förderer',
		'slug'         => 'lindenzauber/seite-foerderer',
		'beschreibung' => 'Vier große Förderer-Kacheln, die komplett anklickbar sind.',
	),
	'06-kontakt.html' => array(
		'datei'        => 'seite-kontakt.php',
		'titel'        => 'Seite: Kontakt',
		'slug'         => 'lindenzauber/seite-kontakt',
		'beschreibung' => 'Direkte Wege zur Kontaktaufnahme, dazu Ansprechpartnerin, Veranstalter und Presse.',
	),
	'07-impressum.html' => array(
		'datei'        => 'seite-impressum.php',
		'titel'        => 'Seite: Impressum',
		'slug'         => 'lindenzauber/seite-impressum',
		'beschreibung' => 'Pflichtangaben mit dem Mütter-Kinder-Zentrum Bassum e. V. als Träger.',
	),
	'08-fotogalerie.html' => array(
		'datei'        => 'seite-fotogalerie.php',
		'titel'        => 'Seite: Fotogalerie',
		'slug'         => 'lindenzauber/seite-fotogalerie',
		'beschreibung' => 'Vorbereitete Rückblick-Seite mit Bildergalerie im Nachtlook.',
	),
	'09-foerderband-widget.html' => array(
		'datei'        => 'foerderband.php',
		'titel'        => 'Förderer-Band (für die Fußzeile)',
		'slug'         => 'lindenzauber/foerderband',
		'beschreibung' => 'Die vier Logos für den Widget-Bereich „Förderer-Band“ am Fuß der Seite.',
		'ohne_seite'   => true,
	),
);

foreach ( $seiten as $name => $angaben ) {
	$pfad = $quelle . '/' . $name;

	if ( ! file_exists( $pfad ) ) {
		fwrite( STDERR, "fehlt: $pfad\n" );
		exit( 1 );
	}

	$inhalt = rtrim( file_get_contents( $pfad ) );

	$kopf  = "<?php\n/**\n";
	$kopf .= ' * Title: ' . $angaben['titel'] . "\n";
	$kopf .= ' * Slug: ' . $angaben['slug'] . "\n";
	$kopf .= " * Categories: lindenzauber\n";
	$kopf .= ' * Description: ' . $angaben['beschreibung'] . "\n";

	if ( empty( $angaben['ohne_seite'] ) ) {
		$kopf .= " * Block Types: core/post-content\n";
		$kopf .= " * Post Types: page\n";
	}

	$kopf .= " * Inserter: yes\n";
	$kopf .= " *\n * Diese Datei wird von tools/make-patterns.php erzeugt.\n";
	$kopf .= " * Quelle: inhalte/" . $name . "\n";
	$kopf .= " *\n * @package Lindenzauber\n */\n\n?>\n";

	file_put_contents( $ziel . '/' . $angaben['datei'], $kopf . $inhalt . "\n" );
	echo str_pad( $angaben['datei'], 34 ) . strlen( $inhalt ) . " Zeichen\n";
}

echo "fertig.\n";
