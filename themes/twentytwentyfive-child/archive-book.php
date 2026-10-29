<?php
/**
 * Books index. DESIGN.md §5 "Books index row", board "v2 Books".
 *
 * @package twentytwentyfive-child
 */

get_header();

$cc_order = cc_book_order();
$cc_years = array();
if ( have_posts() ) {
	foreach ( $wp_query->posts as $cc_p ) {
		$cc_y = (int) get_post_meta( $cc_p->ID, 'year', true );
		if ( $cc_y ) {
			$cc_years[] = $cc_y;
		}
	}
}
$cc_count = (int) $wp_query->post_count;
$cc_base  = get_post_type_archive_link( 'book' );
$cc_sorts = array(
	'newest' => __( 'Newest first', 'twentytwentyfive-child' ),
	'oldest' => __( 'Oldest first', 'twentytwentyfive-child' ),
	'title'  => __( 'A–Z', 'twentytwentyfive-child' ),
);
?>

<div class="cc-wrap cc-grid cc-page">
	<div class="cc-index" id="site-index">
		<?php cc_index_nav(); ?>
	</div>

	<main id="main" class="cc-main">
		<div class="cc-archive-head cc-field">
			<h1><?php post_type_archive_title(); ?></h1>
			<p>
				<?php
				if ( $cc_years && min( $cc_years ) === max( $cc_years ) ) {
					printf(
						/* translators: 1: number of titles, 2: year */
						esc_html( _n( '%1$s title, %2$s', '%1$s titles, %2$s', $cc_count, 'twentytwentyfive-child' ) ),
						esc_html( number_format_i18n( $cc_count ) ),
						esc_html( (string) min( $cc_years ) )
					);
				} elseif ( $cc_years ) {
					printf(
						/* translators: 1: number of titles, 2: first year, 3: latest year */
						esc_html( _n( '%1$s title, %2$s–%3$s', '%1$s titles, %2$s–%3$s', $cc_count, 'twentytwentyfive-child' ) ),
						esc_html( number_format_i18n( $cc_count ) ),
						esc_html( (string) min( $cc_years ) ),
						esc_html( (string) max( $cc_years ) )
					);
				} else {
					printf(
						/* translators: %s: number of titles */
						esc_html( _n( '%s title', '%s titles', $cc_count, 'twentytwentyfive-child' ) ),
						esc_html( number_format_i18n( $cc_count ) )
					);
				}
				?>
			</p>
		</div>

		<nav class="cc-sort" aria-label="<?php esc_attr_e( 'Sort books', 'twentytwentyfive-child' ); ?>">
			<p>
				<?php
				$cc_links = array();
				foreach ( $cc_sorts as $cc_key => $cc_label ) {
					$cc_url     = 'newest' === $cc_key ? $cc_base : add_query_arg( 'order', $cc_key, $cc_base );
					$cc_links[] = sprintf(
						'<a href="%1$s"%2$s>%3$s</a>',
						esc_url( $cc_url ),
						$cc_key === $cc_order ? ' aria-current="true"' : '',
						esc_html( $cc_label )
					);
				}
				echo implode( ' · ', $cc_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
				?>
			</p>
		</nav>

		<?php if ( have_posts() ) : ?>
			<ol class="cc-book-list">
				<?php
				while ( have_posts() ) :
					the_post();
					$cc_m = cc_book_meta( get_the_ID() );
					?>
					<li class="cc-book-row">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="cc-book-row__cover" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'medium', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
							</a>
						<?php else : ?>
							<span class="cc-book-row__cover cc-book-row__cover--empty" aria-hidden="true"></span>
						<?php endif; ?>
						<div<?php echo $cc_m['language'] ? ' lang="' . esc_attr( $cc_m['language'] ) . '"' : ''; ?>>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<?php if ( $cc_m['subtitle'] ) : ?>
								<p class="cc-subtitle"><?php echo esc_html( $cc_m['subtitle'] ); ?></p>
							<?php endif; ?>
						</div>
						<span class="cc-book-row__meta"><?php echo esc_html( $cc_m['publisher'] ); ?></span>
						<span class="cc-book-row__meta"><?php echo esc_html( $cc_m['year'] ); ?></span>
					</li>
				<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<p><?php esc_html_e( 'No books yet.', 'twentytwentyfive-child' ); ?></p>
		<?php endif; ?>
	</main>
</div>

<?php
get_footer();
