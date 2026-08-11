<?php
/**
 * Vorlage für einzelne Beiträge.
 *
 * @package Lindenzauber
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class( 'entry' ); ?>>
		<header class="entry-header">
			<span class="entry-header__eyebrow"><?php echo esc_html( get_the_date() ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php lz_ornament(); ?>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>

	<?php
endwhile;

get_footer();
