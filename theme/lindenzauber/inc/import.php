<?php
/**
 * Seiten aus einem Paket einspielen.
 *
 * Unter *Design → Lindenzauber* lässt sich das Inhalte-Paket hochladen. Der
 * Import legt die Seiten an oder bringt vorhandene auf den neuen Stand,
 * trägt die Kurzbeschreibungen ein, setzt die Startseite und füllt das
 * Förderer-Band. Was in der Anleitung acht Handgriffe waren, ist damit einer.
 *
 * Zwei Schritte, und das mit Absicht: Erst wird gezeigt, was passieren
 * würde – Seite für Seite –, und erst nach Bestätigung wird etwas geändert.
 * Der Import fasst fremde Seiten nur an, wenn das ausdrücklich angehakt wird.
 *
 * Was im Paket steht, steht nur dort: Slug, Titel, Auszug und Status kommen
 * aus der beiliegenden seiten.json. Im Theme ist nichts davon fest verdrahtet.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Darf die angemeldete Person Seiten einspielen?
 *
 * "unfiltered_html" ist der entscheidende Punkt: Ohne dieses Recht filtert
 * WordPress beim Speichern die HTML-Kommentare heraus – und genau die sind
 * bei Blöcken der Inhalt. Es entstünde eine Seite voller kaputter Blöcke.
 * Lieber gar nicht anfangen als das.
 *
 * @return true|string true oder der Grund, warum nicht.
 */
function lz_import_erlaubt() {
	if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'publish_pages' ) ) {
		return __( 'Dafür fehlen die Rechte. Der Import ist Administratorinnen und Administratoren vorbehalten.', 'lindenzauber' );
	}

	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return __( 'Dieses Benutzerkonto darf kein rohes HTML speichern. WordPress würde dabei die Blockangaben entfernen und die Seiten unbrauchbar machen. Bitte mit einem Administratorkonto einspielen.', 'lindenzauber' );
	}

	return true;
}

/**
 * Das hochgeladene Paket auspacken und den Bauplan lesen.
 *
 * @param string $datei Pfad zur hochgeladenen ZIP-Datei.
 * @return array|WP_Error array( 'pfad' => …, 'plan' => … )
 */
function lz_import_auspacken( $datei ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';

	if ( ! WP_Filesystem() ) {
		return new WP_Error( 'dateisystem', __( 'Auf das Dateisystem lässt sich nicht zugreifen.', 'lindenzauber' ) );
	}

	lz_import_altlasten();

	$ordner = trailingslashit( get_temp_dir() ) . 'lz-import-' . wp_generate_password( 12, false );
	$fertig = unzip_file( $datei, $ordner );

	if ( is_wp_error( $fertig ) ) {
		return $fertig;
	}

	$bauplan = $ordner . '/seiten.json';

	if ( ! file_exists( $bauplan ) ) {
		lz_import_aufraeumen( $ordner );

		return new WP_Error(
			'kein_bauplan',
			__( 'In dem Paket fehlt die Datei seiten.json. Das ist vermutlich nicht das Inhalte-Paket – gemeint ist lindenzauber-inhalte.zip, nicht das Theme.', 'lindenzauber' )
		);
	}

	$plan = json_decode( file_get_contents( $bauplan ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions

	if ( ! is_array( $plan ) || empty( $plan['seiten'] ) ) {
		lz_import_aufraeumen( $ordner );

		return new WP_Error( 'bauplan_kaputt', __( 'Die seiten.json im Paket ist nicht lesbar.', 'lindenzauber' ) );
	}

	return array(
		'pfad' => $ordner,
		'plan' => $plan,
	);
}

/**
 * Temporären Ordner wieder entfernen.
 *
 * Das Dateisystem wird hier selbst angemeldet und nicht als bereits vorhanden
 * vorausgesetzt: Ausgepackt wird beim Hochladen, weggeräumt erst nach dem
 * Bestätigen – das sind zwei Aufrufe. Im zweiten stand $wp_filesystem noch auf
 * null, die Bedingung war still falsch und der Ordner blieb liegen.
 *
 * @param string $ordner Pfad.
 */
function lz_import_aufraeumen( $ordner ) {
	global $wp_filesystem;

	if ( ! $ordner || false === strpos( $ordner, 'lz-import-' ) ) {
		return;
	}

	if ( ! $wp_filesystem ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}

	if ( $wp_filesystem ) {
		$wp_filesystem->delete( $ordner, true );
	}
}

/**
 * Liegengebliebene Ordner früherer Uploads entfernen.
 *
 * Wer ein Paket hochlädt und die Vorschau danach einfach wegklickt, lässt den
 * ausgepackten Ordner stehen: der Zwischenspeicher läuft von selbst ab, der
 * Ordner nicht. Beim nächsten Hochladen wird deshalb weggeräumt, was älter
 * als zwei Stunden ist – länger kann keine Vorschau gültig sein.
 */
function lz_import_altlasten() {
	$muster = trailingslashit( get_temp_dir() ) . 'lz-import-*';
	$grenze = time() - 2 * HOUR_IN_SECONDS;

	foreach ( (array) glob( $muster, GLOB_ONLYDIR ) as $ordner ) {
		if ( filemtime( $ordner ) < $grenze ) {
			lz_import_aufraeumen( $ordner );
		}
	}
}

/**
 * Was würde der Import tun?
 *
 * @param array $plan   Inhalt der seiten.json.
 * @param string $pfad  Ordner mit den entpackten Dateien.
 * @return array
 */
function lz_import_vorschau( $plan, $pfad ) {
	$vorhaben  = array();
	$angefasst = array();

	foreach ( $plan['seiten'] as $eintrag ) {
		$name = isset( $eintrag['datei'] ) ? basename( $eintrag['datei'] ) : '';
		$slug = isset( $eintrag['slug'] ) ? sanitize_title( $eintrag['slug'] ) : '';

		if ( '' === $name || '' === $slug || ! file_exists( $pfad . '/' . $name ) ) {
			$vorhaben[] = array(
				'was'   => 'fehlt',
				'titel' => $name ? $name : __( '(ohne Dateinamen)', 'lindenzauber' ),
				'text'  => __( 'Datei fehlt im Paket – wird übersprungen.', 'lindenzauber' ),
			);

			continue;
		}

		$seite       = get_page_by_path( $slug, OBJECT, 'page' );
		$angefasst[] = $slug;

		$vorhaben[] = array(
			'was'     => $seite ? 'aktualisieren' : 'anlegen',
			'titel'   => isset( $eintrag['titel'] ) ? $eintrag['titel'] : $slug,
			'slug'    => $slug,
			'id'      => $seite ? (int) $seite->ID : 0,
			'text'    => $seite
				? __( 'Vorhandene Seite bekommt den neuen Inhalt. Adresse und Menüeinträge bleiben; der alte Stand steht danach unter „Revisionen“.', 'lindenzauber' )
				: __( 'Wird neu angelegt.', 'lindenzauber' ),
			'eintrag' => $eintrag,
		);
	}

	// Alles andere, was veröffentlicht ist und nicht ausdrücklich bleiben soll.
	$behalten = isset( $plan['behalten'] ) ? array_map( 'sanitize_title', (array) $plan['behalten'] ) : array();
	$fremde   = array();

	foreach ( get_pages( array( 'post_status' => 'publish' ) ) as $seite ) {
		if ( in_array( $seite->post_name, $angefasst, true ) || in_array( $seite->post_name, $behalten, true ) ) {
			continue;
		}

		$fremde[] = array(
			'id'    => (int) $seite->ID,
			'slug'  => $seite->post_name,
			'titel' => $seite->post_title,
		);
	}

	return array(
		'vorhaben' => $vorhaben,
		'fremde'   => $fremde,
		'behalten' => $behalten,
		'widgets'  => isset( $plan['widgets'] ) ? $plan['widgets'] : array(),
	);
}

/**
 * Den Import ausführen.
 *
 * @param array  $plan       Inhalt der seiten.json.
 * @param string $pfad       Ordner mit den entpackten Dateien.
 * @param array  $stilllegen IDs der Seiten, die stillgelegt werden sollen.
 * @return array Meldungen.
 */
function lz_import_ausfuehren( $plan, $pfad, $stilllegen ) {
	$meldungen = array();
	$startseite = 0;

	foreach ( $plan['seiten'] as $eintrag ) {
		$name = isset( $eintrag['datei'] ) ? basename( $eintrag['datei'] ) : '';
		$slug = isset( $eintrag['slug'] ) ? sanitize_title( $eintrag['slug'] ) : '';

		if ( '' === $name || '' === $slug || ! file_exists( $pfad . '/' . $name ) ) {
			continue;
		}

		$inhalt = file_get_contents( $pfad . '/' . $name ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		$seite  = get_page_by_path( $slug, OBJECT, 'page' );

		$daten = array(
			'post_type'    => 'page',
			'post_name'    => $slug,
			'post_title'   => isset( $eintrag['titel'] ) ? sanitize_text_field( $eintrag['titel'] ) : $slug,
			'post_content' => $inhalt,
			'post_status'  => isset( $eintrag['status'] ) && 'draft' === $eintrag['status'] ? 'draft' : 'publish',
		);

		if ( ! empty( $eintrag['auszug'] ) ) {
			$daten['post_excerpt'] = sanitize_textarea_field( $eintrag['auszug'] );
		}

		if ( $seite ) {
			$daten['ID'] = (int) $seite->ID;

			// Eine Seite, die von Hand auf Entwurf gesetzt wurde, soll durch
			// den Import nicht plötzlich wieder öffentlich werden.
			if ( 'publish' !== $seite->post_status && 'publish' === $daten['post_status'] ) {
				$daten['post_status'] = $seite->post_status;
			}

			$id = wp_update_post( $daten, true );
		} else {
			$id = wp_insert_post( $daten, true );
		}

		if ( is_wp_error( $id ) ) {
			$meldungen[] = sprintf(
				/* translators: 1: Seitentitel, 2: Fehlermeldung */
				__( '%1$s konnte nicht gespeichert werden: %2$s', 'lindenzauber' ),
				$daten['post_title'],
				$id->get_error_message()
			);

			continue;
		}

		$meldungen[] = sprintf(
			/* translators: %s: Seitentitel */
			$seite ? __( '%s aktualisiert.', 'lindenzauber' ) : __( '%s angelegt.', 'lindenzauber' ),
			$daten['post_title']
		);

		if ( ! empty( $eintrag['startseite'] ) ) {
			$startseite = (int) $id;
		}
	}

	/* ------------------------------------------------------- Startseite */
	if ( $startseite > 0 ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $startseite );
		$meldungen[] = __( 'Startseite festgelegt.', 'lindenzauber' );
	}

	/* --------------------------------------------------- Förderer-Band */
	foreach ( (array) ( isset( $plan['widgets'] ) ? $plan['widgets'] : array() ) as $widget ) {
		$name    = isset( $widget['datei'] ) ? basename( $widget['datei'] ) : '';
		$bereich = isset( $widget['bereich'] ) ? sanitize_key( $widget['bereich'] ) : '';

		if ( '' === $name || '' === $bereich || ! file_exists( $pfad . '/' . $name ) ) {
			continue;
		}

		// Nur füllen, wenn dort noch nichts steht – vorhandene Logos werden
		// nicht überschrieben.
		if ( is_active_sidebar( $bereich ) ) {
			$meldungen[] = __( 'Förderer-Band war schon gefüllt und bleibt, wie es ist.', 'lindenzauber' );

			continue;
		}

		if ( lz_import_widget( $bereich, file_get_contents( $pfad . '/' . $name ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
			$meldungen[] = __( 'Förderer-Band im Fußbereich eingefügt.', 'lindenzauber' );
		}
	}

	/* --------------------------------------------------------- Stilllegen */
	foreach ( $stilllegen as $id ) {
		$id    = (int) $id;
		$seite = $id > 0 ? get_post( $id ) : null;

		if ( ! $seite || 'page' !== $seite->post_type ) {
			continue;
		}

		// Den alten Slug merken, damit sich das zurückdrehen lässt.
		update_post_meta( $id, '_lz_alter_slug', $seite->post_name );

		wp_update_post(
			array(
				'ID'          => $id,
				'post_status' => 'draft',
				'post_name'   => 'alt-' . $seite->post_name,
			)
		);

		$meldungen[] = sprintf(
			/* translators: %s: Seitentitel */
			__( '„%s“ stillgelegt: steht jetzt auf Entwurf und ist nicht mehr aufrufbar.', 'lindenzauber' ),
			$seite->post_title
		);
	}

	flush_rewrite_rules();

	return $meldungen;
}

/**
 * Einen Blockinhalt als Widget in einen Bereich legen.
 *
 * @param string $bereich Kennung des Widget-Bereichs.
 * @param string $inhalt  Blockcode.
 * @return bool
 */
function lz_import_widget( $bereich, $inhalt ) {
	$blocks = get_option( 'widget_block', array() );
	$naechste = 1;

	foreach ( array_keys( $blocks ) as $schluessel ) {
		if ( is_numeric( $schluessel ) ) {
			$naechste = max( $naechste, (int) $schluessel + 1 );
		}
	}

	$blocks[ $naechste ] = array( 'content' => $inhalt );
	$blocks['_multiwidget'] = 1;
	update_option( 'widget_block', $blocks );

	$bereiche = get_option( 'sidebars_widgets', array() );

	if ( ! isset( $bereiche[ $bereich ] ) || ! is_array( $bereiche[ $bereich ] ) ) {
		$bereiche[ $bereich ] = array();
	}

	$bereiche[ $bereich ][] = 'block-' . $naechste;
	update_option( 'sidebars_widgets', $bereiche );

	return true;
}
