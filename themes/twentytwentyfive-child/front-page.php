<?php
/**
 * Front page. DESIGN.md §5, board "v2 Home".
 *
 * Editors control: the front page's featured image (printed into the field),
 * its content (intro), the "Page design" panel (field, quotation).
 *
 * @package twentytwentyfive-child
 */

get_header();

$cc_front   = 'page' === get_option( 'show_on_front' ) ? (int) get_option( 'page_on_front' ) : 0;
$cc_quote   = $cc_front ? (string) get_post_meta( $cc_front, 'cc_quote', true ) : '';
$cc_source  = $cc_front ? (string) get_post_meta( $cc_front, 'cc_quote_source', true ) : '';
$cc_qlang   = $cc_front ? (string) get_post_meta( $cc_front, 'cc_quote_lang', true ) : '';
$cc_thumb   = $cc_front ? (int) get_post_thumbnail_id( $cc_front ) : 0;
$cc_caption = $cc_thumb ? wp_get_attachment_caption( $cc_thumb ) : '';
?>

<div class="cc-wrap cc-grid cc-page">
	<div class="cc-index" id="site-index">
		<p class="cc-affiliation"><?php echo nl2br( esc_html( get_bloginfo( 'description' ) ) ); ?></p>
		<?php cc_index_nav(); ?>
	</div>

	<main id="main" class="cc-main">
		<h1 class="screen-reader-text"><?php echo esc_html( get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' ) ); ?></h1>

		<?php if ( $cc_thumb ) : ?>
			<figure class="cc-field cc-printed cc-hero-image" style="margin:0">
				<?php echo wp_get_attachment_image( $cc_thumb, 'large', false, array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
			</figure>
			<?php if ( $cc_caption ) : ?>
				<details class="cc-what">
					<summary><?php esc_html_e( 'What is this?', 'twentytwentyfive-child' ); ?></summary>
					<p><?php echo esc_html( $cc_caption ); ?></p>
				</details>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( $cc_front ) : ?>
			<div class="cc-intro cc-content">
				<?php
				$cc_post = get_post( $cc_front );
				// Render the front page's blocks without disturbing the main query.
				echo apply_filters( 'the_content', $cc_post->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		<?php endif; ?>
	</main>
</div>

<?php if ( $cc_quote ) : ?>
	<section class="cc-quote-band cc-field" aria-label="<?php esc_attr_e( 'Quotation', 'twentytwentyfive-child' ); ?>">
		<div class="cc-wrap cc-quote-band__inner">
			<blockquote<?php echo $cc_qlang ? ' lang="' . esc_attr( $cc_qlang ) . '"' : ''; ?>>
				<p><?php echo esc_html( $cc_quote ); ?></p>
			</blockquote>
			<?php if ( $cc_source ) : ?>
				<p class="cc-quote-band__source">— <?php echo esc_html( $cc_source ); ?></p>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>

<?php
$cc_recent = new WP_Query(
	array(
		'post_type'           => array( 'book', 'publication', 'post' ),
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( $cc_recent->have_posts() ) :
	?>
	<section class="cc-wrap cc-grid cc-section" aria-labelledby="cc-recent-title">
		<h2 id="cc-recent-title" class="cc-label cc-section__title"><?php esc_html_e( 'Recently', 'twentytwentyfive-child' ); ?></h2>
		<ol class="cc-rows">
			<?php
			while ( $cc_recent->have_posts() ) :
				$cc_recent->the_post();
				cc_item_row( get_the_ID() );
			endwhile;
			wp_reset_postdata();
			?>
		</ol>
	</section>
<?php endif; ?>

<?php
get_footer();
