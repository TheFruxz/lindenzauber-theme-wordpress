<?php
/**
 * Kopfbereich.
 *
 * @package Lindenzauber
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#inhalt"><?php esc_html_e( 'Zum Inhalt springen', 'lindenzauber' ); ?></a>

<div class="site">

	<?php lz_szene(); ?>

	<header class="site-header">
		<div class="site-header__inner">

			<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php
				if ( has_custom_logo() ) {
					$logo_id = (int) get_theme_mod( 'custom_logo' );
					echo wp_get_attachment_image(
						$logo_id,
						'full',
						false,
						array(
							'class'    => 'custom-logo',
							'alt'      => '',
							'loading'  => 'eager',
							'decoding' => 'async',
						)
					);
				}
				?>

				<span class="site-brand__text">
					<span class="site-brand__name"><?php bloginfo( 'name' ); ?></span>
					<span class="site-brand__tag"><?php echo esc_html( lz_untertitel() ); ?></span>
				</span>
			</a>

			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="hauptmenue">
				<span class="nav-toggle__bars" aria-hidden="true"></span>
				<span class="nav-toggle__label"><?php esc_html_e( 'Menü', 'lindenzauber' ); ?></span>
			</button>

			<nav class="main-nav" id="hauptmenue" aria-label="<?php esc_attr_e( 'Hauptmenü', 'lindenzauber' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => '',
						'menu_class'     => 'menu',
						'depth'          => 1,
						'fallback_cb'    => 'lz_hauptmenue_notfall',
					)
				);
				?>
			</nav>

		</div>
	</header>

	<main class="site-main" id="inhalt">
