<?php
/**
 * Erzeugt das Kartenbild für die Programmseite aus OpenStreetMap-Kacheln.
 *
 * Das Bild wird einmal erzeugt und liegt danach als Datei im Repo unter
 * medien/. Auf der Website entsteht dadurch kein einziger Aufruf an einen
 * fremden Server – wichtig für den Datenschutz.
 *
 * Namensnennung: © OpenStreetMap-Mitwirkende, Lizenz ODbL.
 * https://www.openstreetmap.org/copyright
 *
 * Aufruf:  php tools/make-karte.php
 */

$lat  = 52.8517566;
$lon  = 8.7355695;
$zoom = 16;
$spalten = 4; // Kacheln waagerecht
$zeilen  = 3; // Kacheln senkrecht
$kachel  = 256;

$ziel = __DIR__ . '/../medien/anfahrt-kinderreich.png';
@mkdir( dirname( $ziel ), 0777, true );

/** Rechnet Längengrad in eine (gebrochene) Kachelnummer um. */
function x_kachel( $lon, $zoom ) {
	return ( $lon + 180 ) / 360 * pow( 2, $zoom );
}

/** Rechnet Breitengrad in eine (gebrochene) Kachelnummer um. */
function y_kachel( $lat, $zoom ) {
	$rad = deg2rad( $lat );

	return ( 1 - log( tan( $rad ) + 1 / cos( $rad ) ) / M_PI ) / 2 * pow( 2, $zoom );
}

$xf = x_kachel( $lon, $zoom );
$yf = y_kachel( $lat, $zoom );

$x0 = (int) floor( $xf ) - (int) floor( ( $spalten - 1 ) / 2 );
$y0 = (int) floor( $yf ) - (int) floor( ( $zeilen - 1 ) / 2 );

$breite = $spalten * $kachel;
$hoehe  = $zeilen * $kachel;

$bild = imagecreatetruecolor( $breite, $hoehe );

$kontext = stream_context_create(
	array(
		'http' => array(
			'header'  => "User-Agent: lindenzauber-theme-build/1.0 (einmaliger Kartenbau; kontakt: info@lindenzauber.de)\r\n",
			'timeout' => 30,
		),
	)
);

for ( $sx = 0; $sx < $spalten; $sx++ ) {
	for ( $sy = 0; $sy < $zeilen; $sy++ ) {
		$tx  = $x0 + $sx;
		$ty  = $y0 + $sy;
		$url = sprintf( 'https://tile.openstreetmap.org/%d/%d/%d.png', $zoom, $tx, $ty );

		echo "lade $url\n";
		$daten = @file_get_contents( $url, false, $kontext );

		if ( false === $daten ) {
			fwrite( STDERR, "Kachel nicht erreichbar: $url\n" );
			exit( 1 );
		}

		$teil = imagecreatefromstring( $daten );
		imagecopy( $bild, $teil, $sx * $kachel, $sy * $kachel, 0, 0, $kachel, $kachel );
		imagedestroy( $teil );
		usleep( 400000 ); // freundlich zum Kachelserver
	}
}

// Position des Kindergartens im zusammengesetzten Bild.
$px = (int) round( ( $xf - $x0 ) * $kachel );
$py = (int) round( ( $yf - $y0 ) * $kachel );

// Nachtblauer Schleier, damit die Karte zum Rest der Seite passt.
$schleier = imagecreatetruecolor( $breite, $hoehe );
imagefill( $schleier, 0, 0, imagecolorallocate( $schleier, 14, 24, 48 ) );
imagecopymerge( $bild, $schleier, 0, 0, 0, 0, $breite, $hoehe, 40 );
imagedestroy( $schleier );

// Goldene Ortsmarke.
$gold      = imagecolorallocate( $bild, 240, 200, 104 );
$goldhell  = imagecolorallocate( $bild, 251, 239, 199 );
$dunkel    = imagecolorallocate( $bild, 12, 20, 40 );

imagesetthickness( $bild, 3 );

// Tropfenform als Kreis plus Spitze.
imagefilledellipse( $bild, $px, $py - 26, 34, 34, $dunkel );
imagefilledellipse( $bild, $px, $py - 26, 28, 28, $gold );
imagefilledpolygon(
	$bild,
	array( $px - 11, $py - 16, $px + 11, $py - 16, $px, $py + 4 ),
	$gold
);
imagefilledellipse( $bild, $px, $py - 26, 11, 11, $dunkel );

// Kleiner Lichtschein um die Marke.
for ( $r = 46; $r > 30; $r -= 2 ) {
	imagesetthickness( $bild, 1 );
	imageellipse( $bild, $px, $py - 26, $r, $r, $goldhell );
}

// Auf ein ruhiges Querformat zuschneiden, die Ortsmarke bleibt mittig.
$ziel_breite = 940;
$ziel_hoehe  = 560;
$links = max( 0, min( $breite - $ziel_breite, (int) round( $px - $ziel_breite / 2 ) ) );
$oben  = max( 0, min( $hoehe - $ziel_hoehe, (int) round( ( $py - 26 ) - $ziel_hoehe / 2 ) ) );

$zuschnitt = imagecrop( $bild, array( 'x' => $links, 'y' => $oben, 'width' => $ziel_breite, 'height' => $ziel_hoehe ) );

if ( false !== $zuschnitt ) {
	imagedestroy( $bild );
	$bild = $zuschnitt;
}

// Namensnennung ins Bild schreiben – so geht sie nie verloren.
$hinweis = mb_convert_encoding( '(c) OpenStreetMap-Mitwirkende', 'ISO-8859-1', 'UTF-8' );
$b = imagesx( $bild );
$h = imagesy( $bild );
imagefilledrectangle( $bild, $b - 214, $h - 24, $b, $h, imagecolorallocatealpha( $bild, 0, 0, 0, 60 ) );
imagestring( $bild, 2, $b - 208, $h - 19, $hinweis, imagecolorallocate( $bild, 240, 240, 240 ) );

imagepng( $bild, $ziel, 8 );
imagedestroy( $bild );

echo "geschrieben: $ziel (" . filesize( $ziel ) . " Byte)\n";
