<?php
/**
 * Allgemeine Vorlage (Beiträge, Archive, Suche).
 *
 * @package Lindenzauber
 */

get_header();
?>

<header class="entry-header">
	<span class="entry-header__eyebrow"><?php bloginfo( 'name' ); ?></span>
	<h1>
		<?php
		if ( is_search() ) {
			printf(
				/* translators: %s: Suchbegriff */
				esc_html__( 'Suche nach %s', 'lindenzauber' ),
				esc_html( get_search_query() )
			);
		} elseif ( is_home() ) {
			esc_html_e( 'Neues vom Lindenzauber', 'lindenzauber' );
		} else {
			echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
		}
		?>
	</h1>
	<?php lz_ornament(); ?>
</header>

<div class="entry-content">
	<div class="lz-postlist">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<p class="lz-postmeta"><?php echo esc_html( get_the_date() ); ?></p>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
				<?php
			endwhile;

			the_posts_pagination(
				array(
					'prev_text' => esc_html__( 'Zurück', 'lindenzauber' ),
					'next_text' => esc_html__( 'Weiter', 'lindenzauber' ),
				)
			);
		else :
			?>
			<p>
				<?php esc_html_e( 'Hier ist im Moment nichts zu finden.', 'lindenzauber' ); ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Zurück zur Startseite', 'lindenzauber' ); ?></a>
			</p>
			<?php
		endif;
		?>
	</div>
</div>

<?php
get_footer();
