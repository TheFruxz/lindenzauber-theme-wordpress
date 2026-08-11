<?php
/**
 * Die Seite *Design → Lindenzauber*.
 *
 * Zwei Dinge stehen hier:
 *
 * 1. Eine Einrichtungs-Liste, die von selbst weiß, was noch fehlt. Kein
 *    Abhaken von Hand – jeder Punkt fragt WordPress, ob er erledigt ist.
 * 2. Der Text für /llms.txt zum Bearbeiten. Leer bedeutet: aus den Eckdaten
 *    erzeugen. Damit ist auch diese Datei nicht fest eingebaut.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Die Schritte der Einrichtung mit ihrem aktuellen Stand.
 *
 * @return array Jeder Eintrag: fertig, titel, text, link, link_text.
 */
function lz_einrichtung_schritte() {
	$schritte = array();

	/* ----------------------------------------------------------- Logo */
	$schritte[] = array(
		'fertig'    => has_custom_logo(),
		'titel'     => __( 'Logo im Kopfbereich', 'lindenzauber' ),
		'text'      => __( 'Das Lindenblatt-Signet erscheint links oben neben dem Schriftzug.', 'lindenzauber' ),
		'link'      => admin_url( 'customize.php?autofocus[control]=custom_logo' ),
		'link_text' => __( 'Logo wählen', 'lindenzauber' ),
	);

	/* -------------------------------------------------- Symbol im Tab */
	$schritte[] = array(
		'fertig'    => has_site_icon(),
		'titel'     => __( 'Symbol für den Browser-Tab', 'lindenzauber' ),
		'text'      => __( 'Ein quadratisches Bild ab 512 × 512 Pixel. Solange keines gesetzt ist, zeigt das Theme ein goldenes Lindenblatt – Ihr eigenes Signet ist schöner.', 'lindenzauber' ),
		'link'      => admin_url( 'customize.php?autofocus[section]=title_tagline' ),
		'link_text' => __( 'Symbol wählen', 'lindenzauber' ),
	);

	/* --------------------------------------------- Termine noch aktuell */
	$start   = lz_eckdaten( 'tag1_start' );
	$stempel = $start ? strtotime( $start ) : false;
	$vorbei  = $stempel && $stempel < current_time( 'timestamp' );

	$schritte[] = array(
		'fertig'    => ! $vorbei,
		'titel'     => __( 'Termine des Festes', 'lindenzauber' ),
		'text'      => $vorbei
			? __( 'Der hinterlegte Termin liegt in der Vergangenheit. Bitte die Eckdaten auf das nächste Fest umstellen – Fußzeile und die Angaben für Google hängen daran.', 'lindenzauber' )
			: __( 'Datum, Uhrzeit, Ort und Kontakt stehen an einer Stelle und versorgen von dort die Fußzeile und die Angaben für Google.', 'lindenzauber' ),
		'link'      => admin_url( 'customize.php?autofocus[section]=lz_eckdaten' ),
		'link_text' => __( 'Eckdaten durchsehen', 'lindenzauber' ),
	);

	/* ----------------------------------------------- Bild fürs Teilen */
	$schritte[] = array(
		'fertig'    => (int) get_theme_mod( 'lz_teilen_bild', 0 ) > 0,
		'titel'     => __( 'Vorschaubild beim Teilen', 'lindenzauber' ),
		'text'      => __( 'Dieses Bild erscheint, wenn jemand einen Link in WhatsApp, Signal oder Facebook einfügt. Am besten das Plakat.', 'lindenzauber' ),
		'link'      => admin_url( 'customize.php?autofocus[section]=lz_teilen' ),
		'link_text' => __( 'Bild wählen', 'lindenzauber' ),
	);

	/* ------------------------------------------------------ Startseite */
	$schritte[] = array(
		'fertig'    => 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) > 0,
		'titel'     => __( 'Startseite festgelegt', 'lindenzauber' ),
		'text'      => __( 'Die Website soll eine feste Startseite zeigen, nicht die Liste der Beiträge.', 'lindenzauber' ),
		'link'      => admin_url( 'options-reading.php' ),
		'link_text' => __( 'Einstellen', 'lindenzauber' ),
	);

	/* ---------------------------------------------------------- Menüs */
	$schritte[] = array(
		'fertig'    => has_nav_menu( 'primary' ),
		'titel'     => __( 'Hauptmenü zugewiesen', 'lindenzauber' ),
		'text'      => __( 'Start · Programm · Die Erzählenden · Über Lindenzauber · Förderer. Ohne Zuweisung zeigt das Theme eine sinnvolle Ersatzliste – kaputt sieht es nie aus.', 'lindenzauber' ),
		'link'      => admin_url( 'nav-menus.php?action=locations' ),
		'link_text' => __( 'Menü zuweisen', 'lindenzauber' ),
	);

	$schritte[] = array(
		'fertig'    => has_nav_menu( 'legal' ),
		'titel'     => __( 'Fußmenü zugewiesen', 'lindenzauber' ),
		'text'      => __( 'Kontakt · Impressum · Datenschutz.', 'lindenzauber' ),
		'link'      => admin_url( 'nav-menus.php?action=locations' ),
		'link_text' => __( 'Menü zuweisen', 'lindenzauber' ),
	);

	/* ------------------------------------------------- Förderer-Band */
	$schritte[] = array(
		'fertig'    => is_active_sidebar( 'lz-foerderband' ),
		'titel'     => __( 'Förderer im Fußbereich', 'lindenzauber' ),
		'text'      => __( 'Die Logos erscheinen als helle Kacheln im Fußbereich – auf allen Seiten außer der Startseite, wo sie weiter oben groß stehen. Ist der Bereich leer, wird nichts angezeigt.', 'lindenzauber' ),
		'link'      => admin_url( 'widgets.php' ),
		'link_text' => __( 'Logos einfügen', 'lindenzauber' ),
	);

	/* ----------------------------------------------------- Das Plakat */
	$startseite = (int) get_option( 'page_on_front' );
	$platzhalter = false;

	if ( $startseite > 0 ) {
		$inhalt      = get_post_field( 'post_content', $startseite );
		$platzhalter = is_string( $inhalt ) && false !== strpos( $inhalt, 'platzhalter-plakat' );
	}

	$schritte[] = array(
		'fertig'    => $startseite > 0 && ! $platzhalter,
		'titel'     => __( 'Plakat eingesetzt', 'lindenzauber' ),
		'text'      => __( 'Auf der Startseite steht im Abschnitt „Das Plakat zum Lindenzauber“ noch der goldene Rahmen als Platzhalter. Bild anklicken → Ersetzen.', 'lindenzauber' ),
		'link'      => $startseite > 0 ? get_edit_post_link( $startseite, 'raw' ) : admin_url( 'edit.php?post_type=page' ),
		'link_text' => __( 'Startseite bearbeiten', 'lindenzauber' ),
	);

	/* -------------------------------------------------------- Auszüge */
	$ohne = array();

	foreach ( get_pages( array( 'post_status' => 'publish' ) ) as $seite ) {
		if ( '' === trim( (string) $seite->post_excerpt ) ) {
			$ohne[] = $seite;
		}
	}

	$schritte[] = array(
		'fertig'    => empty( $ohne ),
		'titel'     => __( 'Kurzbeschreibungen der Seiten', 'lindenzauber' ),
		'text'      => empty( $ohne )
			? __( 'Jede Seite hat einen Auszug. Der erscheint bei Google unter dem Titel.', 'lindenzauber' )
			: sprintf(
				/* translators: %s: Aufzählung der Seiten ohne Auszug */
				__( 'Ohne Auszug nimmt die Website die ersten Sätze der Seite – die enden dann mitten im Satz. Es fehlt bei: %s. Zu finden beim Bearbeiten rechts unter „Seite → Auszug“.', 'lindenzauber' ),
				implode( ', ', wp_list_pluck( $ohne, 'post_title' ) )
			),
		'link'      => admin_url( 'edit.php?post_type=page' ),
		'link_text' => __( 'Seiten öffnen', 'lindenzauber' ),
	);

	/* ------------------------------------------------------- llms.txt */
	$regeln = get_option( 'rewrite_rules' );

	$schritte[] = array(
		'fertig'    => is_array( $regeln ) && isset( $regeln['^llms\.txt$'] ),
		'titel'     => __( 'Adresse /llms.txt erreichbar', 'lindenzauber' ),
		'text'      => __( 'Die Kurzfassung der Website für Suchmaschinen und KI-Werkzeuge. Fehlt die Adresse, müssen die Adressregeln einmal erneuert werden.', 'lindenzauber' ),
		'link'      => admin_url( 'options-permalink.php' ),
		'link_text' => __( 'Adressregeln erneuern', 'lindenzauber' ),
	);

	return $schritte;
}

/**
 * Wie viele Schritte noch offen sind.
 *
 * @return int
 */
function lz_einrichtung_offen() {
	$offen = 0;

	foreach ( lz_einrichtung_schritte() as $schritt ) {
		if ( ! $schritt['fertig'] ) {
			$offen++;
		}
	}

	return $offen;
}

/**
 * Die Seite im Menü anlegen.
 */
function lz_seite_anlegen() {
	$offen = lz_einrichtung_offen();

	add_theme_page(
		__( 'Lindenzauber – Einrichtung', 'lindenzauber' ),
		$offen > 0
			? sprintf( 'Lindenzauber <span class="update-plugins count-%1$d"><span class="update-count">%1$d</span></span>', $offen )
			: 'Lindenzauber',
		'edit_theme_options',
		'lindenzauber',
		'lz_seite_ausgeben'
	);
}
add_action( 'admin_menu', 'lz_seite_anlegen' );

/**
 * Das hochgeladene Paket annehmen und die Vorschau vorbereiten.
 *
 * Zwischen Vorschau und Ausführung liegt ein zweiter Klick. Damit der
 * ausgepackte Ordner so lange auffindbar bleibt, ohne dass sein Pfad durchs
 * Formular wandert – das ließe sich von außen setzen –, steht er unter einer
 * Zufallskennung in einem Zwischenspeicher.
 */
function lz_import_annehmen() {
	if ( ! isset( $_POST['lz_import_form'] ) ) {
		return;
	}

	check_admin_referer( 'lz_import' );

	$erlaubt = lz_import_erlaubt();

	if ( true !== $erlaubt ) {
		add_settings_error( 'lindenzauber', 'import_recht', $erlaubt, 'error' );

		return;
	}

	if ( empty( $_FILES['lz_paket']['tmp_name'] ) || ! is_uploaded_file( $_FILES['lz_paket']['tmp_name'] ) ) {
		add_settings_error( 'lindenzauber', 'import_datei', __( 'Es wurde keine Datei ausgewählt.', 'lindenzauber' ), 'error' );

		return;
	}

	$paket = lz_import_auspacken( $_FILES['lz_paket']['tmp_name'] );

	if ( is_wp_error( $paket ) ) {
		add_settings_error( 'lindenzauber', 'import_paket', $paket->get_error_message(), 'error' );

		return;
	}

	$kennung = wp_generate_password( 20, false );
	set_transient( 'lz_import_' . $kennung, $paket, HOUR_IN_SECONDS );

	$GLOBALS['lz_import_kennung']  = $kennung;
	$GLOBALS['lz_import_vorschau'] = lz_import_vorschau( $paket['plan'], $paket['pfad'] );
}
add_action( 'load-appearance_page_lindenzauber', 'lz_import_annehmen' );

/**
 * Den bestätigten Import ausführen.
 */
function lz_import_starten() {
	if ( ! isset( $_POST['lz_import_los'] ) ) {
		return;
	}

	check_admin_referer( 'lz_import_los' );

	$erlaubt = lz_import_erlaubt();

	if ( true !== $erlaubt ) {
		add_settings_error( 'lindenzauber', 'import_recht', $erlaubt, 'error' );

		return;
	}

	$kennung = isset( $_POST['lz_kennung'] ) ? sanitize_key( wp_unslash( $_POST['lz_kennung'] ) ) : '';
	$paket   = $kennung ? get_transient( 'lz_import_' . $kennung ) : false;

	if ( ! is_array( $paket ) || empty( $paket['pfad'] ) ) {
		add_settings_error(
			'lindenzauber',
			'import_abgelaufen',
			__( 'Das hochgeladene Paket ist nicht mehr da. Bitte noch einmal hochladen.', 'lindenzauber' ),
			'error'
		);

		return;
	}

	$stilllegen = isset( $_POST['lz_stilllegen'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['lz_stilllegen'] ) ) : array();
	$meldungen  = lz_import_ausfuehren( $paket['plan'], $paket['pfad'], $stilllegen );

	lz_import_aufraeumen( $paket['pfad'] );
	delete_transient( 'lz_import_' . $kennung );

	add_settings_error(
		'lindenzauber',
		'import_fertig',
		'<strong>' . esc_html__( 'Import abgeschlossen.', 'lindenzauber' ) . '</strong><br>' . implode( '<br>', array_map( 'esc_html', $meldungen ) ),
		'updated'
	);
}
add_action( 'load-appearance_page_lindenzauber', 'lz_import_starten' );

/**
 * Speichern, Zurücksetzen und Adressregeln erneuern.
 */
function lz_seite_verarbeiten() {
	if ( ! isset( $_POST['lz_form'] ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	check_admin_referer( 'lz_speichern' );

	if ( isset( $_POST['lz_zuruecksetzen'] ) ) {
		remove_theme_mod( 'lz_llms_text' );
		add_settings_error( 'lindenzauber', 'zurueck', __( 'Der Text wird wieder automatisch aus den Eckdaten erzeugt.', 'lindenzauber' ), 'updated' );

		return;
	}

	if ( isset( $_POST['lz_regeln'] ) ) {
		flush_rewrite_rules();
		add_settings_error( 'lindenzauber', 'regeln', __( 'Die Adressregeln wurden erneuert.', 'lindenzauber' ), 'updated' );

		return;
	}

	// "Automatischen Text einsetzen" füllt nur das Feld – gespeichert wird
	// erst mit dem Speichern-Knopf. So kann man noch daran arbeiten.
	if ( isset( $_POST['lz_vorschlag'] ) ) {
		return;
	}

	$text = isset( $_POST['lz_llms_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['lz_llms_text'] ) ) : '';

	if ( '' === trim( $text ) ) {
		remove_theme_mod( 'lz_llms_text' );
	} else {
		set_theme_mod( 'lz_llms_text', $text );
	}

	add_settings_error( 'lindenzauber', 'gespeichert', __( 'Gespeichert.', 'lindenzauber' ), 'updated' );
}
add_action( 'load-appearance_page_lindenzauber', 'lz_seite_verarbeiten' );

/**
 * Die Seite ausgeben.
 */
function lz_seite_ausgeben() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$schritte  = lz_einrichtung_schritte();
	$offen     = lz_einrichtung_offen();
	$gespeichert = trim( (string) get_theme_mod( 'lz_llms_text', '' ) );
	$automatisch = '' === $gespeichert;

	// Nach "Automatischen Text einsetzen" steht der Vorschlag im Feld.
	$im_feld = isset( $_POST['lz_vorschlag'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nur Anzeige; geprüft wird beim Speichern.
		? lz_llms_standardtext()
		: $gespeichert;

	?>
	<div class="wrap lz-einrichtung">
		<h1><?php esc_html_e( 'Lindenzauber', 'lindenzauber' ); ?></h1>

		<?php settings_errors( 'lindenzauber' ); ?>

		<p class="description" style="max-width:46em;font-size:14px;">
			<?php esc_html_e( 'Auf dieser Seite steht, was zur Einrichtung noch fehlt – und der Text, den Suchmaschinen und KI-Werkzeuge über das Fest lesen. Die Termine selbst werden im Customizer gepflegt.', 'lindenzauber' ); ?>
		</p>

		<h2 style="margin-top:2em;">
			<?php
			if ( $offen > 0 ) {
				printf(
					/* translators: %d: Anzahl offener Schritte */
					esc_html( _n( 'Einrichtung – noch %d Schritt', 'Einrichtung – noch %d Schritte', $offen, 'lindenzauber' ) ),
					(int) $offen
				);
			} else {
				esc_html_e( 'Einrichtung – alles steht', 'lindenzauber' );
			}
			?>
		</h2>

		<table class="widefat striped lz-schritte" style="max-width:60em;">
			<tbody>
			<?php foreach ( $schritte as $schritt ) : ?>
				<tr>
					<td style="width:2.5em;text-align:center;font-size:18px;padding-top:14px;">
						<?php echo $schritt['fertig'] ? '<span style="color:#1a7f37;" aria-hidden="true">✔</span>' : '<span style="color:#996800;" aria-hidden="true">•</span>'; ?>
						<span class="screen-reader-text">
							<?php echo $schritt['fertig'] ? esc_html__( 'Erledigt', 'lindenzauber' ) : esc_html__( 'Offen', 'lindenzauber' ); ?>
						</span>
					</td>
					<td>
						<strong><?php echo esc_html( $schritt['titel'] ); ?></strong><br>
						<span class="description"><?php echo esc_html( $schritt['text'] ); ?></span>
					</td>
					<td style="width:14em;text-align:right;">
						<?php if ( ! $schritt['fertig'] && $schritt['link'] ) : ?>
							<a class="button" href="<?php echo esc_url( $schritt['link'] ); ?>"><?php echo esc_html( $schritt['link_text'] ); ?></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<h2 style="margin-top:2.5em;"><?php esc_html_e( 'Seiten importieren', 'lindenzauber' ); ?></h2>

		<?php
		$vorschau = isset( $GLOBALS['lz_import_vorschau'] ) ? $GLOBALS['lz_import_vorschau'] : null;

		if ( $vorschau ) :
			// Schritt 2: zeigen, was passieren würde.
			?>
			<form method="post" action="">
				<?php wp_nonce_field( 'lz_import_los' ); ?>
				<input type="hidden" name="lz_import_los" value="1">
				<input type="hidden" name="lz_kennung" value="<?php echo esc_attr( $GLOBALS['lz_import_kennung'] ); ?>">

				<p class="description" style="max-width:46em;">
					<?php esc_html_e( 'Das Paket ist gelesen. Bitte durchsehen – geändert wird erst mit dem Knopf unten.', 'lindenzauber' ); ?>
				</p>

				<table class="widefat striped lz-vorhaben" style="max-width:60em;">
					<tbody>
					<?php foreach ( $vorschau['vorhaben'] as $punkt ) : ?>
						<tr>
							<td style="width:9em;">
								<?php
								$farben = array(
									'anlegen'       => array( '#1a7f37', __( 'neu', 'lindenzauber' ) ),
									'aktualisieren' => array( '#0a4b78', __( 'wird ersetzt', 'lindenzauber' ) ),
									'fehlt'         => array( '#996800', __( 'fehlt', 'lindenzauber' ) ),
								);
								$farbe = isset( $farben[ $punkt['was'] ] ) ? $farben[ $punkt['was'] ] : array( '#666', $punkt['was'] );
								printf(
									'<strong style="color:%s">%s</strong>',
									esc_attr( $farbe[0] ),
									esc_html( $farbe[1] )
								);
								?>
							</td>
							<td>
								<strong><?php echo esc_html( $punkt['titel'] ); ?></strong><br>
								<span class="description"><?php echo esc_html( $punkt['text'] ); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<?php if ( ! empty( $vorschau['fremde'] ) ) : ?>
					<h3 style="margin-top:2em;"><?php esc_html_e( 'Seiten, die nicht zum Paket gehören', 'lindenzauber' ); ?></h3>
					<p class="description" style="max-width:46em;">
						<?php esc_html_e( 'Diese Seiten bleiben unangetastet. Wer angehakt wird, kommt auf Entwurf und ist danach nicht mehr aufrufbar – gelöscht wird nichts, und der alte Adressname wird gemerkt, damit sich das zurückdrehen lässt.', 'lindenzauber' ); ?>
					</p>

					<table class="widefat striped lz-fremde" style="max-width:60em;">
						<tbody>
						<?php foreach ( $vorschau['fremde'] as $fremd ) : ?>
							<tr>
								<td style="width:3em;text-align:center;">
									<input type="checkbox" name="lz_stilllegen[]" value="<?php echo esc_attr( $fremd['id'] ); ?>"
										id="lz-still-<?php echo esc_attr( $fremd['id'] ); ?>">
								</td>
								<td>
									<label for="lz-still-<?php echo esc_attr( $fremd['id'] ); ?>">
										<strong><?php echo esc_html( $fremd['titel'] ); ?></strong>
										<span class="description">/<?php echo esc_html( $fremd['slug'] ); ?>/</span>
									</label>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<p style="margin-top:1.5em;">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Jetzt importieren', 'lindenzauber' ); ?></button>
					<a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=lindenzauber' ) ); ?>"><?php esc_html_e( 'Abbrechen', 'lindenzauber' ); ?></a>
				</p>
			</form>
		<?php else : ?>
			<p class="description" style="max-width:46em;">
				<?php esc_html_e( 'Das Inhalte-Paket hochladen – die Seiten werden angelegt oder auf den neuen Stand gebracht, die Kurzbeschreibungen eingetragen, die Startseite gesetzt und das Förderer-Band gefüllt. Vorhandene Seiten behalten ihre Adresse und ihre Menüeinträge; der alte Stand bleibt unter „Revisionen“ erhalten.', 'lindenzauber' ); ?>
			</p>

			<?php $erlaubt = lz_import_erlaubt(); ?>

			<?php if ( true !== $erlaubt ) : ?>
				<p><em><?php echo esc_html( $erlaubt ); ?></em></p>
			<?php else : ?>
				<form method="post" action="" enctype="multipart/form-data">
					<?php wp_nonce_field( 'lz_import' ); ?>
					<input type="hidden" name="lz_import_form" value="1">

					<p>
						<input type="file" name="lz_paket" accept=".zip,application/zip" required>
						<button type="submit" class="button"><?php esc_html_e( 'Paket lesen', 'lindenzauber' ); ?></button>
					</p>
					<p class="description">
						<?php esc_html_e( 'Gemeint ist lindenzauber-inhalte.zip – nicht das Theme-Paket.', 'lindenzauber' ); ?>
					</p>
				</form>
			<?php endif; ?>
		<?php endif; ?>

		<h2 style="margin-top:2.5em;"><?php esc_html_e( 'Text für Suchmaschinen und KI', 'lindenzauber' ); ?></h2>

		<p class="description" style="max-width:46em;">
			<?php
			printf(
				/* translators: %s: Verweis auf die Datei */
				esc_html__( 'Unter %s liegt eine Kurzfassung der Website in reinem Text. Werkzeuge, die eine Website zusammenfassen sollen, lesen zuerst dort nach.', 'lindenzauber' ),
				'<a href="' . esc_url( home_url( '/llms.txt' ) ) . '" target="_blank" rel="noopener"><code>/llms.txt</code></a>'
			);
			?>
		</p>

		<form method="post" action="">
			<?php wp_nonce_field( 'lz_speichern' ); ?>
			<input type="hidden" name="lz_form" value="1">

			<p>
				<strong>
					<?php
					echo $automatisch
						? esc_html__( 'Zurzeit: automatisch aus den Eckdaten.', 'lindenzauber' )
						: esc_html__( 'Zurzeit: eigener Text.', 'lindenzauber' );
					?>
				</strong>
				<span class="description">
					<?php esc_html_e( 'Bleibt das Feld leer, entsteht der Text bei jedem Aufruf neu aus Terminen, Ort und Kontakt – er kann dann nicht veralten. Steht etwas darin, wird genau das ausgeliefert.', 'lindenzauber' ); ?>
				</span>
			</p>

			<textarea name="lz_llms_text" rows="22" style="width:100%;max-width:60em;font-family:monospace;font-size:13px;"
				placeholder="<?php echo esc_attr( lz_llms_standardtext() ); ?>"><?php echo esc_textarea( $im_feld ); ?></textarea>

			<p class="description" style="max-width:46em;">
				<?php esc_html_e( 'Im leeren Feld steht blass der Text, der gerade ausgeliefert wird.', 'lindenzauber' ); ?>
			</p>

			<p>
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Speichern', 'lindenzauber' ); ?></button>
				<button type="submit" name="lz_vorschlag" value="1" class="button"><?php esc_html_e( 'Automatischen Text zum Bearbeiten einsetzen', 'lindenzauber' ); ?></button>
				<?php if ( ! $automatisch ) : ?>
					<button type="submit" name="lz_zuruecksetzen" value="1" class="button"><?php esc_html_e( 'Wieder automatisch', 'lindenzauber' ); ?></button>
				<?php endif; ?>
				<button type="submit" name="lz_regeln" value="1" class="button"><?php esc_html_e( 'Adressregeln erneuern', 'lindenzauber' ); ?></button>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Ein Hinweis im Backend, solange etwas fehlt.
 *
 * Er verschwindet von selbst, sobald alle Schritte erledigt sind – und lässt
 * sich vorher wegklicken, ohne wiederzukommen.
 */
function lz_hinweis() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$bildschirm = get_current_screen();

	// Auf der Einrichtungsseite selbst steht die Liste ohnehin.
	if ( $bildschirm && 'appearance_page_lindenzauber' === $bildschirm->id ) {
		return;
	}

	if ( get_user_meta( get_current_user_id(), 'lz_hinweis_weg', true ) ) {
		return;
	}

	$offen = lz_einrichtung_offen();

	if ( $offen < 1 ) {
		return;
	}

	$weg = wp_nonce_url( add_query_arg( 'lz_hinweis', 'weg' ), 'lz_hinweis_weg' );

	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Lindenzauber', 'lindenzauber' ); ?></strong> –
			<?php
			printf(
				/* translators: %d: Anzahl offener Schritte */
				esc_html( _n( 'noch %d Schritt bis alles steht.', 'noch %d Schritte bis alles steht.', $offen, 'lindenzauber' ) ),
				(int) $offen
			);
			?>
			<a href="<?php echo esc_url( admin_url( 'themes.php?page=lindenzauber' ) ); ?>"><?php esc_html_e( 'Zur Einrichtung', 'lindenzauber' ); ?></a>
			&nbsp;·&nbsp;
			<a href="<?php echo esc_url( $weg ); ?>"><?php esc_html_e( 'nicht mehr anzeigen', 'lindenzauber' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'lz_hinweis' );

/**
 * Den Hinweis dauerhaft wegklicken.
 */
function lz_hinweis_wegklicken() {
	if ( ! isset( $_GET['lz_hinweis'] ) || 'weg' !== $_GET['lz_hinweis'] ) {
		return;
	}

	check_admin_referer( 'lz_hinweis_weg' );

	if ( current_user_can( 'edit_theme_options' ) ) {
		update_user_meta( get_current_user_id(), 'lz_hinweis_weg', 1 );
	}

	wp_safe_redirect( remove_query_arg( array( 'lz_hinweis', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'lz_hinweis_wegklicken' );
