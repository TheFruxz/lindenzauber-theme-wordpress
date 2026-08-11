<?php
/**
 * Vorlage für nicht gefundene Seiten.
 *
 * @package Lindenzauber
 */

get_header();
?>

<header class="entry-header">
	<span class="entry-header__eyebrow"><?php esc_html_e( 'Seite nicht gefunden', 'lindenzauber' ); ?></span>
	<h1><?php esc_html_e( 'Diese Seite hat sich im Zauberwald verlaufen', 'lindenzauber' ); ?></h1>
	<?php lz_ornament(); ?>
</header>

<div class="entry-content">
	<div class="lz-postlist">
		<p><?php esc_html_e( 'Die aufgerufene Adresse gibt es nicht (mehr). Diese Wege führen weiter:', 'lindenzauber' ); ?></p>
		<ul>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Startseite', 'lindenzauber' ); ?></a></li>
			<li><a href="<?php echo esc_url( lz_seiten_adresse( 'programm' ) ); ?>"><?php esc_html_e( 'Programm', 'lindenzauber' ); ?></a></li>
			<li><a href="<?php echo esc_url( lz_seiten_adresse( 'die-erzaehlenden' ) ); ?>"><?php esc_html_e( 'Die Erzählenden', 'lindenzauber' ); ?></a></li>
			<li><a href="<?php echo esc_url( lz_seiten_adresse( 'kontakt' ) ); ?>"><?php esc_html_e( 'Kontakt', 'lindenzauber' ); ?></a></li>
		</ul>
	</div>
</div>

<?php
get_footer();
