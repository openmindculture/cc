<?php
/**
 * Single book. DESIGN.md §5 "Book metadata", "Disclosures", board "v2 Book".
 *
 * Editors write "About the book" in the content and add Contents / Reviews
 * as Details blocks; the citation is generated from the book fields.
 *
 * @package twentytwentyfive-child
 */

get_header();

while ( have_posts() ) :
	the_post();
	$cc_id   = get_the_ID();
	$cc_m    = cc_book_meta( $cc_id );
	$cc_lang = $cc_m['language'];
	$cc_dl   = array_filter(
		array(
			__( 'Publisher', 'twentytwentyfive-child' ) => $cc_m['publisher'],
			__( 'Year', 'twentytwentyfive-child' )      => $cc_m['year'],
			__( 'Series', 'twentytwentyfive-child' )    => trim( $cc_m['series'] . ( $cc_m['series_volume'] ? ', vol. ' . $cc_m['series_volume'] : '' ) ),
			__( 'Pages', 'twentytwentyfive-child' )     => $cc_m['pages'],
		)
	);
	?>

<div class="cc-wrap cc-grid cc-page">
	<div class="cc-index" id="site-index">
		<?php cc_index_nav(); ?>
		<p class="cc-back"><a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>">← <?php esc_html_e( 'All books', 'twentytwentyfive-child' ); ?></a></p>
	</div>

	<main id="main" class="cc-main">
		<article <?php post_class(); ?>>
			<div class="cc-book-hero cc-field">
				<figure>
					<?php
					if ( has_post_thumbnail() ) {
						$cc_alt = trim( (string) get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) );
						the_post_thumbnail(
							'large',
							array(
								'class'   => 'cc-book-hero__cover',
								'alt'     => $cc_alt ? $cc_alt : sprintf( /* translators: %s: book title */ __( 'Cover of %s', 'twentytwentyfive-child' ), get_the_title() ),
								'loading' => 'eager',
							)
						);
					} else {
						echo '<span class="cc-book-hero__cover cc-book-hero__cover--empty" aria-hidden="true"></span>';
					}
					?>
				</figure>
				<div<?php echo $cc_lang ? ' lang="' . esc_attr( $cc_lang ) . '"' : ''; ?>>
					<p class="cc-label" lang="<?php echo esc_attr( get_bloginfo( 'language' ) ); ?>">
						<?php
						echo esc_html( __( 'Book', 'twentytwentyfive-child' ) . ( $cc_lang ? ' · ' . cc_language_name( $cc_lang ) : '' ) );
						?>
					</p>
					<h1><?php the_title(); ?></h1>
					<?php if ( $cc_m['subtitle'] ) : ?>
						<p class="cc-subtitle"><?php echo esc_html( $cc_m['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $cc_m['cover_credit'] ) : ?>
				<details class="cc-what">
					<summary><?php esc_html_e( 'About the cover', 'twentytwentyfive-child' ); ?></summary>
					<p><?php echo esc_html( $cc_m['cover_credit'] ); ?></p>
				</details>
			<?php endif; ?>

			<div class="cc-book-body">
				<dl class="cc-meta">
					<?php foreach ( $cc_dl as $cc_label => $cc_value ) : ?>
						<dt><?php echo esc_html( $cc_label ); ?></dt>
						<dd><?php echo esc_html( $cc_value ); ?></dd>
					<?php endforeach; ?>
					<?php if ( $cc_m['isbn'] || $cc_m['isbn_ebook'] ) : ?>
						<dt><?php esc_html_e( 'ISBN', 'twentytwentyfive-child' ); ?></dt>
						<dd>
							<?php if ( $cc_m['isbn'] ) : ?>
								<?php echo esc_html( $cc_m['isbn'] ); ?> <span class="cc-quiet">(<?php esc_html_e( 'print', 'twentytwentyfive-child' ); ?>)</span><br>
							<?php endif; ?>
							<?php if ( $cc_m['isbn_ebook'] ) : ?>
								<?php echo esc_html( $cc_m['isbn_ebook'] ); ?> <span class="cc-quiet">(<?php esc_html_e( 'eBook', 'twentytwentyfive-child' ); ?>)</span>
							<?php endif; ?>
						</dd>
					<?php endif; ?>
					<?php if ( $cc_m['doi'] ) : ?>
						<dt><?php esc_html_e( 'DOI', 'twentytwentyfive-child' ); ?></dt>
						<dd><a href="<?php echo esc_url( 'https://doi.org/' . $cc_m['doi'] ); ?>"><?php echo esc_html( $cc_m['doi'] ); ?></a></dd>
					<?php endif; ?>
					<?php if ( $cc_m['publisher_url'] ) : ?>
						<dd style="margin-top:1.25rem">
							<a class="cc-button" href="<?php echo esc_url( $cc_m['publisher_url'] ); ?>" rel="noopener"><?php esc_html_e( 'View at publisher', 'twentytwentyfive-child' ); ?>&nbsp;↗</a>
						</dd>
					<?php endif; ?>
				</dl>

				<div class="cc-book-text">
					<h2 class="cc-label"><?php esc_html_e( 'About the book', 'twentytwentyfive-child' ); ?></h2>
					<div class="cc-content">
						<?php the_content(); ?>
					</div>

					<div class="cc-disclosures">
						<details class="cc-disclosure cc-cite">
							<summary><?php esc_html_e( 'Cite', 'twentytwentyfive-child' ); ?></summary>
							<p><?php echo cc_book_citation_chicago( $cc_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function. ?></p>
							<pre lang="en"><?php echo esc_html( cc_book_bibtex( $cc_id ) ); ?></pre>
						</details>
					</div>
				</div>
			</div>
		</article>
	</main>
</div>

	<?php
endwhile;

get_footer();
