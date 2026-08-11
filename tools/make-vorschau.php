<?php
/**
 * Erzeugt die statische Vorschau aus der laufenden Testinstanz.
 *
 * Die Seiten werden genau so abgeholt, wie WordPress sie ausliefert, und
 * anschließend auf relative Pfade umgeschrieben. Dadurch kann die Vorschau
 * niemals anders aussehen als die spätere Website – es gibt keinen zweiten
 * Codeweg, der auseinanderlaufen könnte.
 *
 * Aufruf:  LZ_WORK=… LZ_PORT=8321 php tools/make-vorschau.php
 */

$repo = dirname( __DIR__ );
$work = getenv( 'LZ_WORK' );
$port = getenv( 'LZ_PORT' ) ?: '8321';
$basis = 'http://127.0.0.1:' . $port;

if ( ! $work ) {
	fwrite( STDERR, "LZ_WORK muss gesetzt sein.\n" );
	exit( 1 );
}

$site = $work . '/site';
$ziel = $work . '/vorschau';

// Alte Vorschau restlos entfernen.
if ( is_dir( $ziel ) ) {
	$eisen = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $ziel, FilesystemIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::CHILD_FIRST
	);

	foreach ( $eisen as $eintrag ) {
		$eintrag->isDir() ? rmdir( $eintrag->getPathname() ) : unlink( $eintrag->getPathname() );
	}

	rmdir( $ziel );
}

mkdir( $ziel, 0777, true );

/** Seiten: Adresse auf der Testinstanz => Dateiname in der Vorschau. */
$seiten = array(
	'/'                    => 'index.html',
	'/programm/'           => 'programm.html',
	'/die-erzaehlenden/'   => 'die-erzaehlenden.html',
	'/ueber-lindenzauber/' => 'ueber-lindenzauber.html',
	'/sponsoren/'          => 'foerderer.html',
	'/kontakt/'            => 'kontakt.html',
	'/impressum/'          => 'impressum.html',
	'/datenschutz/'        => 'datenschutz.html',
	'/gibt-es-nicht/'      => '404.html',
);

/**
 * Kopiert einen Ordner mitsamt Inhalt.
 */
function kopiere( $von, $nach ) {
	if ( ! is_dir( $von ) ) {
		return;
	}

	@mkdir( $nach, 0777, true );

	foreach ( scandir( $von ) as $eintrag ) {
		if ( '.' === $eintrag || '..' === $eintrag ) {
			continue;
		}

		$q = $von . '/' . $eintrag;
		$z = $nach . '/' . $eintrag;

		is_dir( $q ) ? kopiere( $q, $z ) : copy( $q, $z );
	}
}

echo "== Dateien kopieren ==\n";
kopiere( $site . '/wp-content/themes/lindenzauber/assets', $ziel . '/dateien/theme/assets' );
copy( $site . '/wp-content/themes/lindenzauber/style.css', $ziel . '/dateien/theme/style.css' );
kopiere( $site . '/wp-content/uploads/lz', $ziel . '/dateien/bilder' );

// Von WordPress erzeugte Bildgrößen (z. B. das Logo im Kopfbereich).
foreach ( glob( $site . '/wp-content/uploads/*/*/*' ) as $bild ) {
	if ( is_file( $bild ) ) {
		@mkdir( $ziel . '/dateien/bilder', 0777, true );
		copy( $bild, $ziel . '/dateien/bilder/' . basename( $bild ) );
	}
}


echo "== Grafiken in die Stylesheets einbetten ==\n";

/*
 * Wie bei den Schriften: Beim Öffnen per Doppelklick verweigert der Browser
 * das Laden von Masken-Grafiken aus Nachbardateien. Für die Vorschau werden
 * die SVG-Dateien deshalb direkt in die Stylesheets geschrieben.
 */
$css_dateien = array(
	$ziel . '/dateien/theme/style.css'             => 'assets/img/',
	$ziel . '/dateien/theme/assets/css/blocks.css' => '../img/',
);

foreach ( $css_dateien as $css_pfad => $prefix ) {
	if ( ! file_exists( $css_pfad ) ) {
		continue;
	}

	$css = file_get_contents( $css_pfad );

	$css = preg_replace_callback(
		'#url\(\s*["\']?' . preg_quote( $prefix, '#' ) . '([a-z0-9._-]+\.svg)["\']?\s*\)#i',
		static function ( $treffer ) use ( $ziel ) {
			$bild = $ziel . '/dateien/theme/assets/img/' . $treffer[1];

			if ( ! file_exists( $bild ) ) {
				return $treffer[0];
			}

			return 'url("data:image/svg+xml;base64,' . base64_encode( file_get_contents( $bild ) ) . '")';
		},
		$css
	);

	file_put_contents( $css_pfad, $css );
	echo '  ' . basename( $css_pfad ) . ': ' . round( strlen( $css ) / 1024 ) . " KB\n";
}

echo "== Seiten abholen ==\n";

// ignore_errors, damit auch die 404-Seite geholt werden kann.
$kontext = stream_context_create( array( 'http' => array( 'ignore_errors' => true, 'timeout' => 30 ) ) );

$gefunden_css = array();

foreach ( $seiten as $adresse => $datei ) {
	$html = @file_get_contents( $basis . $adresse, false, $kontext );

	if ( false === $html ) {
		fwrite( STDERR, "nicht erreichbar: $adresse\n" );
		exit( 1 );
	}

	// Style-Dateien von WordPress selbst einsammeln (Block-Bibliothek usw.).
	// Die Theme-Dateien bleiben außen vor: sie behalten ihren Ordner, damit
	// die Verweise auf ../img/… darin weiter stimmen.
	if ( preg_match_all( '#href=[\'"](http://127\.0\.0\.1:' . $port . '/[^\'"]+\.css[^\'"]*)[\'"]#', $html, $treffer ) ) {
		foreach ( $treffer[1] as $url ) {
			if ( false !== strpos( $url, '/wp-content/themes/lindenzauber/' ) ) {
				continue;
			}

			$gefunden_css[ $url ] = true;
		}
	}

	file_put_contents( $ziel . '/__roh__' . $datei, $html );
	echo '  ' . str_pad( $datei, 26 ) . strlen( $html ) . " Zeichen\n";
}

echo "== Stylesheets von WordPress sichern ==\n";
@mkdir( $ziel . '/dateien/wp', 0777, true );
$css_karte = array();
$nr = 0;

foreach ( array_keys( $gefunden_css ) as $url ) {
	$inhalt = @file_get_contents( $url );

	if ( false === $inhalt ) {
		continue;
	}

	$nr++;
	$name = 'wp-' . $nr . '-' . preg_replace( '/[^a-z0-9]+/i', '-', basename( parse_url( $url, PHP_URL_PATH ), '.css' ) );
	$name = substr( $name, 0, 60 ) . '.css';

	// Verweise innerhalb der Datei (Schriften, Bilder) absolut lassen ist
	// nicht möglich – die Kern-Stylesheets brauchen aber keine.
	file_put_contents( $ziel . '/dateien/wp/' . $name, $inhalt );
	$css_karte[ $url ] = 'dateien/wp/' . $name;
	echo '  ' . $name . "\n";
}


echo "== Schriften einbetten ==\n";

/*
 * Beim Öffnen per Doppelklick (file://) lädt Chrome keine Schriftdateien –
 * eine Sicherheitsregel des Browsers, die sich nicht umgehen lässt. Für die
 * Vorschau werden die beiden Schriften deshalb direkt in die Seite gelegt.
 * Auf der echten Website bleibt es bei normalen Dateien.
 */
$schriftenordner = $site . '/wp-content/themes/lindenzauber/assets/fonts';
$latin     = 'U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD';
$latin_ext = 'U+0100-02BA, U+02BD-02C5, U+02C7-02CC, U+02CE-02D7, U+02DD-02FF, U+0304, U+0308, U+0329, U+1D00-1DBF, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF';

$schriften = array(
	array( 'lato-300-latin.woff2',            'Lato',        'normal', 300, $latin ),
	array( 'lato-300-latin-ext.woff2',        'Lato',        'normal', 300, $latin_ext ),
	array( 'lato-400-latin.woff2',            'Lato',        'normal', 400, $latin ),
	array( 'lato-400-latin-ext.woff2',        'Lato',        'normal', 400, $latin_ext ),
	array( 'lato-400-italic-latin.woff2',     'Lato',        'italic', 400, $latin ),
	array( 'lato-400-italic-latin-ext.woff2', 'Lato',        'italic', 400, $latin_ext ),
	array( 'lato-700-latin.woff2',            'Lato',        'normal', 700, $latin ),
	array( 'lato-700-latin-ext.woff2',        'Lato',        'normal', 700, $latin_ext ),
	array( 'great-vibes-400-latin.woff2',     'Great Vibes', 'normal', 400, $latin ),
	array( 'great-vibes-400-latin-ext.woff2', 'Great Vibes', 'normal', 400, $latin_ext ),
);

$schriften_css = '';

foreach ( $schriften as $s ) {
	list( $datei, $familie, $stil, $gewicht, $bereich ) = $s;
	$pfad = $schriftenordner . '/' . $datei;

	if ( ! file_exists( $pfad ) ) {
		continue;
	}

	$schriften_css .= sprintf(
		"@font-face{font-family:'%s';font-style:%s;font-weight:%d;font-display:swap;unicode-range:%s;src:url(data:font/woff2;base64,%s) format('woff2');}",
		$familie,
		$stil,
		$gewicht,
		$bereich,
		base64_encode( file_get_contents( $pfad ) )
	);
}

echo '  ' . round( strlen( $schriften_css ) / 1024 ) . " KB eingebettet\n";

echo "== Adressen umschreiben ==\n";

$verweise = array(
	'/'                    => 'index.html',
	'/programm/'           => 'programm.html',
	'/die-erzaehlenden/'   => 'die-erzaehlenden.html',
	'/ueber-lindenzauber/' => 'ueber-lindenzauber.html',
	'/sponsoren/'          => 'foerderer.html',
	'/kontakt/'            => 'kontakt.html',
	'/impressum/'          => 'impressum.html',
	'/datenschutz/'        => 'datenschutz.html',
	'/fotogalerie/'        => 'index.html',
);

$hinweis = "\n<!-- Statische Vorschau des Lindenzauber-Themes. Erzeugt aus einer echten "
	. "WordPress-Installation, damit Vorschau und spätere Website identisch aussehen. -->\n";

foreach ( $seiten as $adresse => $datei ) {
	$html = file_get_contents( $ziel . '/__roh__' . $datei );
	unlink( $ziel . '/__roh__' . $datei );

	// 1. Stylesheets von WordPress.
	foreach ( $css_karte as $url => $lokal ) {
		$html = str_replace( $url, $lokal, $html );
	}

	// 2. Theme-Dateien.
	$html = str_replace( $basis . '/wp-content/themes/lindenzauber/', 'dateien/theme/', $html );
	$html = str_replace( '/wp-content/themes/lindenzauber/', 'dateien/theme/', $html );

	// 3. Bilder.
	$html = str_replace( $basis . '/wp-content/uploads/lz/', 'dateien/bilder/', $html );
	$html = str_replace( '/wp-content/uploads/lz/', 'dateien/bilder/', $html );
	$html = preg_replace( '#(?:' . preg_quote( $basis, '#' ) . ')?/wp-content/uploads/[0-9]{4}/[0-9]{2}/#', 'dateien/bilder/', $html );

	// 4. Interne Verweise auf die HTML-Dateien.
	uksort(
		$verweise,
		static function ( $a, $b ) {
			return strlen( $b ) <=> strlen( $a );
		}
	);

	foreach ( $verweise as $von => $nach ) {
		// Vollständige Adressen (aus Menü und Kopfbereich).
		$html = str_replace( 'href="' . $basis . $von . '"', 'href="' . $nach . '"', $html );
		$html = str_replace( 'href="' . rtrim( $basis . $von, '/' ) . '"', 'href="' . $nach . '"', $html );
		$html = str_replace( 'href="' . $basis . $von . '#', 'href="' . $nach . '#', $html );

		// Verweise ohne Domain, wie sie in den Seiteninhalten stehen.
		$html = str_replace( 'href="' . $von . '"', 'href="' . $nach . '"', $html );
		$html = str_replace( 'href="' . $von . '#', 'href="' . $nach . '#', $html );
	}

	// 5. Was von WordPress übrig bleibt, zeigt auf die Startseite der Vorschau.
	$html = str_replace( 'href="' . $basis . '/"', 'href="index.html"', $html );
	$html = preg_replace( '#href="' . preg_quote( $basis, '#' ) . '/[^"]*"#', 'href="index.html"', $html );

	// 6. Restliche absolute Adressen (Skripte, Feeds) neutralisieren.
	$html = str_replace( $basis . '/wp-includes/', 'dateien/wp/', $html );
	$html = preg_replace( '#<link[^>]+rel=[\'"](?:alternate|EditURI|wlwmanifest|pingback|https://api\.w\.org/)[\'"][^>]*>#i', '', $html );
	$html = preg_replace( '#<script[^>]*src="' . preg_quote( $basis, '#' ) . '[^"]*"[^>]*></script>#i', '', $html );

	// Schriften: Vorlade-Verweise entfernen und den Schriftblock ersetzen.
	$html = preg_replace( '#<link rel="preload"[^>]*\.woff2[^>]*>\s*#i', '', $html );
	$html = preg_replace(
		'#<style id=[\'"]wp-fonts-local[\'"]>.*?</style>#is',
		'<style id="lz-schriften">' . $schriften_css . '</style>',
		$html,
		1
	);

	$html = str_replace( '</body>', $hinweis . '</body>', $html );

	file_put_contents( $ziel . '/' . $datei, $html );
	echo '  ' . $datei . "\n";
}

// Die beiden Theme-Skripte gehören zur Vorschau dazu.
@mkdir( $ziel . '/dateien/theme/assets/js', 0777, true );

foreach ( array( 'nav.js', 'mehr-anzeigen.js' ) as $skript ) {
	copy(
		$site . '/wp-content/themes/lindenzauber/assets/js/' . $skript,
		$ziel . '/dateien/theme/assets/js/' . $skript
	);
}

// Skripte wieder einhängen – oben wurden alle absoluten Skript-Tags entfernt.
foreach ( $seiten as $datei ) {
	$pfad = $ziel . '/' . $datei;
	$html = file_get_contents( $pfad );
	$html = str_replace(
		'</body>',
		"<script>window.lzMehrTexte={mehr:\"Mehr anzeigen\",weniger:\"Weniger anzeigen\"};</script>\n"
		. "<script src=\"dateien/theme/assets/js/nav.js\"></script>\n"
		. "<script src=\"dateien/theme/assets/js/mehr-anzeigen.js\"></script>\n</body>",
		$html
	);
	file_put_contents( $pfad, $html );
}

file_put_contents(
	$ziel . '/LIESMICH.txt',
	"Lindenzauber – statische Vorschau\n"
	. "=================================\n\n"
	. "Zum Anschauen: die Datei index.html doppelklicken. Es wird kein Server\n"
	. "und kein WordPress gebraucht.\n\n"
	. "Was das hier ist\n"
	. "----------------\n"
	. "Alle Seiten, so wie WordPress sie mit dem Lindenzauber-Theme ausliefert –\n"
	. "nur als feste HTML-Dateien. Gedacht zum Anschauen und Herzeigen.\n\n"
	. "Was das hier NICHT ist\n"
	. "----------------------\n"
	. "Das Theme selbst. Das liegt daneben als lindenzauber.zip und wird in\n"
	. "WordPress unter Design -> Themes -> Theme hochladen installiert.\n\n"
	. "Noch offen\n"
	. "----------\n"
	. "Auf der Startseite steht beim Plakat noch ein Platzhalter (goldener Rahmen\n"
	. "mit Lindenblatt). Sobald das Plakat in der Mediathek liegt, wird es im\n"
	. "Editor mit einem Klick eingesetzt – siehe SETUP.md, Schritt 6.\n"
);

echo "\nfertig: $ziel\n";
