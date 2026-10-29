<?php
/**
 * Fallback template for pages, posts, publications, archives, search and 404.
 * Same frame as the designed templates: index column + main.
 *
 * @package twentytwentyfive-child
 */

get_header();
?>

<div class="cc-wrap cc-grid cc-page">
	<div class="cc-index" id="site-index">
		<?php cc_index_nav(); ?>
	</div>

	<main id="main" class="cc-main">
		<?php if ( is_singular() && have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h1 class="cc-page-title"><?php the_title(); ?></h1>
					<?php if ( is_singular( array( 'post', 'publication' ) ) ) : ?>
						<p class="cc-label" style="margin-top:calc(-1 * var(--wp--preset--spacing--40))"><?php echo esc_html( cc_item_type( get_the_ID() ) . ' · ' . cc_item_year( get_the_ID() ) ); ?></p>
					<?php endif; ?>
					<div class="cc-content">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; ?>

		<?php elseif ( is_404() ) : ?>
			<h1 class="cc-page-title"><?php esc_html_e( 'Not found', 'twentytwentyfive-child' ); ?></h1>
			<div class="cc-content">
				<p><?php esc_html_e( 'This page does not exist — or exists only as a representation elsewhere. Try the index or a search.', 'twentytwentyfive-child' ); ?></p>
			</div>

		<?php else : ?>
			<h1 class="cc-page-title">
				<?php
				if ( is_search() ) {
					/* translators: %s: search query */
					printf( esc_html__( 'Search: %s', 'twentytwentyfive-child' ), esc_html( get_search_query() ) );
				} elseif ( is_home() ) {
					esc_html_e( 'Notes', 'twentytwentyfive-child' );
				} else {
					echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
				}
				?>
			</h1>
			<?php if ( have_posts() ) : ?>
				<ol class="cc-rows" style="grid-column:auto">
					<?php
					while ( have_posts() ) :
						the_post();
						cc_item_row( get_the_ID() );
					endwhile;
					?>
				</ol>
				<?php
				the_posts_pagination(
					array(
						'prev_text' => __( 'Newer', 'twentytwentyfive-child' ),
						'next_text' => __( 'Older', 'twentytwentyfive-child' ),
					)
				);
				?>
			<?php else : ?>
				<p><?php esc_html_e( 'Nothing found.', 'twentytwentyfive-child' ); ?></p>
			<?php endif; ?>
		<?php endif; ?>
	</main>
</div>

<?php
get_footer();
