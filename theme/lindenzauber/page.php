<?php
/**
 * Vorlage für einzelne Seiten.
 *
 * @package Lindenzauber
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class( 'entry' ); ?>>

		<?php if ( ! is_front_page() ) : ?>
			<header class="entry-header">
				<span class="entry-header__eyebrow"><?php bloginfo( 'name' ); ?></span>
				<h1><?php the_title(); ?></h1>
				<?php lz_ornament(); ?>
			</header>
		<?php endif; ?>

		<div class="entry-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<nav class="page-links">' . esc_html__( 'Seiten:', 'lindenzauber' ) . ' ',
					'after'  => '</nav>',
				)
			);
			?>
		</div>

	</article>

	<?php
endwhile;

get_footer();
