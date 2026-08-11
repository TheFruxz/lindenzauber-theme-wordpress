<?php
/**
 * Fußbereich mit dem weißen Förderer-Band wie am Fuß des Plakats.
 *
 * Die Logos kommen aus dem Widget-Bereich "Förderer-Band". Dort lassen sie
 * sich mit dem normalen Block-Editor tauschen, ergänzen oder verlinken –
 * unter "Design → Widgets".
 *
 * @package Lindenzauber
 */

?>
	</main><!-- .site-main -->

	<footer class="site-footer">

		<?php
		/*
		 * Die Förderer stehen im Fußbereich – auf jeder Seite, als eigener
		 * Bereich mit Raster. Nur auf der Startseite nicht: dort werden sie
		 * weiter oben groß gezeigt, und zweimal untereinander wäre doppelt.
		 */
		if ( ! is_front_page() && is_active_sidebar( 'lz-foerderband' ) ) :
			?>
			<section class="site-footer__foerderer" aria-label="<?php esc_attr_e( 'Förderer', 'lindenzauber' ); ?>">
				<p class="site-footer__foerderer-titel"><?php esc_html_e( 'Mit freundlicher Unterstützung von', 'lindenzauber' ); ?></p>
				<?php dynamic_sidebar( 'lz-foerderband' ); ?>
			</section>
		<?php endif; ?>

		<div class="site-footer__grid">

			<div>
				<?php echo lz_blatt(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- feste SVG-Ausgabe aus dem Theme. ?>
				<h2><?php bloginfo( 'name' ); ?></h2>
				<p>
					<?php echo esc_html( lz_eckdaten( 'tag1_titel' ) ); ?><br>
					<?php echo esc_html( lz_eckdaten( 'tag1_zeile' ) ); ?>
				</p>
				<p>
					<?php echo esc_html( lz_eckdaten( 'tag2_titel' ) ); ?><br>
					<?php echo esc_html( lz_eckdaten( 'tag2_zeile' ) ); ?>
				</p>
				<p><?php echo esc_html( lz_eckdaten( 'eintritt' ) ); ?></p>
			</div>

			<div>
				<h2><?php esc_html_e( 'Veranstaltungsort', 'lindenzauber' ); ?></h2>
				<p>
					<?php echo esc_html( lz_eckdaten( 'ort_name' ) ); ?><br>
					<?php echo esc_html( lz_eckdaten( 'ort_strasse' ) ); ?><br>
					<?php echo esc_html( trim( lz_eckdaten( 'ort_plz' ) . ' ' . lz_eckdaten( 'ort_stadt' ) ) ); ?>
				</p>
			</div>

			<div>
				<h2><?php esc_html_e( 'Kontakt', 'lindenzauber' ); ?></h2>
				<?php if ( lz_eckdaten( 'kontakt_name' ) ) : ?>
					<p><?php echo esc_html( lz_eckdaten( 'kontakt_name' ) ); ?></p>
				<?php endif; ?>
				<?php if ( lz_eckdaten( 'kontakt_mail' ) ) : ?>
					<p>
						<a href="mailto:<?php echo esc_attr( antispambot( lz_eckdaten( 'kontakt_mail' ) ) ); ?>">
							<?php echo esc_html( antispambot( lz_eckdaten( 'kontakt_mail' ) ) ); ?>
						</a>
					</p>
				<?php endif; ?>
				<?php if ( lz_eckdaten( 'kontakt_tel' ) ) : ?>
					<p>
						<a href="tel:<?php echo esc_attr( lz_eckdaten( 'kontakt_tel_link' ) ); ?>">
							<?php echo esc_html( lz_eckdaten( 'kontakt_tel' ) ); ?>
						</a>
						<?php esc_html_e( '(auch WhatsApp)', 'lindenzauber' ); ?>
					</p>
				<?php endif; ?>
			</div>

			<div class="site-footer__legal">
				<h2><?php esc_html_e( 'Rechtliches', 'lindenzauber' ); ?></h2>
				<nav aria-label="<?php esc_attr_e( 'Fußmenü', 'lindenzauber' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'legal',
							'container'      => '',
							'menu_class'     => 'menu',
							'depth'          => 1,
							'fallback_cb'    => 'lz_fussmenue_notfall',
						)
					);
					?>
				</nav>
				<p>
					<?php esc_html_e( 'Veranstalter:', 'lindenzauber' ); ?><br>
					<?php if ( lz_eckdaten( 'veranstalter_url' ) ) : ?>
						<a href="<?php echo esc_url( lz_eckdaten( 'veranstalter_url' ) ); ?>" rel="noopener">
							<?php echo esc_html( lz_eckdaten( 'veranstalter' ) ); ?>
						</a>
					<?php else : ?>
						<?php echo esc_html( lz_eckdaten( 'veranstalter' ) ); ?>
					<?php endif; ?>
				</p>
			</div>

		</div>

		<div class="site-footer__bottom">
			<span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
			<?php
			// Bearbeitbar in den Eckdaten. Der Satz ist eine Zusage über die
			// Website – wird später ein Plugin eingebaut, das doch etwas
			// nachlädt, muss man ihn ändern oder löschen können, ohne dafür
			// eine Datei anzufassen. Leeres Feld heißt: Zeile fällt weg.
			$lz_hinweis = lz_eckdaten( 'fusszeile_hinweis' );

			if ( '' !== $lz_hinweis ) :
				?>
				<span><?php echo esc_html( $lz_hinweis ); ?></span>
			<?php endif; ?>
		</div>
	</footer>

</div><!-- .site -->

<?php wp_footer(); ?>
</body>
</html>
