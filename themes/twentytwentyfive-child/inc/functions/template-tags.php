<?php
/**
 * Template helpers for the hybrid PHP templates (DESIGN.md §5, §7).
 *
 * @package twentytwentyfive-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Language code => English name, for labels like "Book · Spanish".
 */
function cc_language_name( string $code ): string {
	$names = array(
		'es' => __( 'Spanish', 'twentytwentyfive-child' ),
		'en' => __( 'English', 'twentytwentyfive-child' ),
		'fr' => __( 'French', 'twentytwentyfive-child' ),
		'pt' => __( 'Portuguese', 'twentytwentyfive-child' ),
		'it' => __( 'Italian', 'twentytwentyfive-child' ),
		'de' => __( 'German', 'twentytwentyfive-child' ),
	);
	return $names[ strtolower( $code ) ] ?? strtoupper( $code );
}

/**
 * The search form used in the header.
 */
function cc_search_form(): void {
	?>
	<form role="search" method="get" class="cc-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="cc-search-input" class="screen-reader-text"><?php esc_html_e( 'Search the site', 'twentytwentyfive-child' ); ?></label>
		<input type="search" id="cc-search-input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search', 'twentytwentyfive-child' ); ?>">
		<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'twentytwentyfive-child' ); ?>">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
		</button>
	</form>
	<?php
}

/**
 * The vertical index navigation. Uses the "primary" menu when assigned,
 * otherwise a fallback built from the content model and pages.
 */
function cc_index_nav(): void {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location'       => 'primary',
				'container'            => 'nav',
				'container_class'      => 'cc-index-nav',
				'container_aria_label' => __( 'Main', 'twentytwentyfive-child' ),
				'menu_class'           => 'cc-index-nav__list',
				'depth'                => 2,
				'fallback_cb'          => false,
			)
		);
		return;
	}

	$items = array();

	if ( post_type_exists( 'book' ) ) {
		$items[] = array(
			'label'   => __( 'Books', 'twentytwentyfive-child' ),
			'url'     => get_post_type_archive_link( 'book' ),
			'current' => is_post_type_archive( 'book' ) || is_singular( 'book' ),
		);
	}

	if ( taxonomy_exists( 'publication_type' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'publication_type',
				'hide_empty' => false,
				'orderby'    => 'term_order',
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$items[] = array(
					'label'   => $term->name,
					'url'     => get_term_link( $term ),
					'current' => is_tax( 'publication_type', $term->term_id ) || ( is_singular( 'publication' ) && has_term( $term->term_id, 'publication_type' ) ),
				);
			}
		}
	}

	$front_id = (int) get_option( 'page_on_front' );
	foreach ( get_pages( array( 'sort_column' => 'menu_order,post_title', 'parent' => 0 ) ) as $page ) {
		if ( $page->ID === $front_id ) {
			continue;
		}
		$items[] = array(
			'label'   => get_the_title( $page ),
			'url'     => get_permalink( $page ),
			'current' => is_page( $page->ID ),
		);
	}
	?>
	<nav class="cc-index-nav" aria-label="<?php esc_attr_e( 'Main', 'twentytwentyfive-child' ); ?>">
		<ul>
			<?php foreach ( $items as $item ) : ?>
				<li><a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Footer links: the "footer" menu if assigned, else the privacy policy page.
 */
function cc_footer_nav(): void {
	if ( has_nav_menu( 'footer' ) ) {
		wp_nav_menu(
			array(
				'theme_location'       => 'footer',
				'container'            => 'nav',
				'container_aria_label' => __( 'Legal', 'twentytwentyfive-child' ),
				'depth'                => 1,
				'fallback_cb'          => false,
			)
		);
		return;
	}
	$privacy = get_privacy_policy_url();
	if ( $privacy ) {
		printf(
			'<nav aria-label="%1$s"><ul><li><a href="%2$s">%3$s</a></li></ul></nav>',
			esc_attr__( 'Legal', 'twentytwentyfive-child' ),
			esc_url( $privacy ),
			esc_html__( 'Privacy policy', 'twentytwentyfive-child' )
		);
	}
}

/**
 * Book meta as an associative array (empty strings for unknown values).
 *
 * @return array<string,string>
 */
function cc_book_meta( int $post_id ): array {
	$keys = array( 'subtitle', 'year', 'publisher', 'series', 'series_volume', 'language', 'pages', 'isbn', 'isbn_ebook', 'doi', 'publisher_url', 'cover_credit' );
	$meta = array();
	foreach ( $keys as $key ) {
		$value        = get_post_meta( $post_id, $key, true );
		$meta[ $key ] = ( '' === $value || null === $value || 0 === $value || '0' === $value ) ? '' : (string) $value;
	}
	return $meta;
}

/**
 * Display year for any list item: the "year" meta if set, else the post year.
 */
function cc_item_year( int $post_id ): string {
	$year = get_post_meta( $post_id, 'year', true );
	if ( $year ) {
		return (string) $year;
	}
	// Books and publications: an unknown year stays empty, never the post date.
	return in_array( get_post_type( $post_id ), array( 'book', 'publication' ), true ) ? '' : get_the_date( 'Y', $post_id );
}

/**
 * Type label for the mixed "Recently" list and search results.
 */
function cc_item_type( int $post_id ): string {
	switch ( get_post_type( $post_id ) ) {
		case 'book':
			return __( 'Book', 'twentytwentyfive-child' );
		case 'publication':
			$terms = get_the_terms( $post_id, 'publication_type' );
			return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : __( 'Publication', 'twentytwentyfive-child' );
		case 'page':
			return __( 'Page', 'twentytwentyfive-child' );
		default:
			$cats = array_filter(
				get_the_category( $post_id ),
				function ( $cat ) {
					return (int) $cat->term_id !== (int) get_option( 'default_category' );
				}
			);
			return $cats ? reset( $cats )->name : __( 'Note', 'twentytwentyfive-child' );
	}
}

/**
 * Secondary text after a title in rows: publisher for books, journal for publications.
 */
function cc_item_source( int $post_id ): string {
	$type = get_post_type( $post_id );
	if ( 'book' === $type ) {
		return (string) get_post_meta( $post_id, 'publisher', true );
	}
	if ( 'publication' === $type ) {
		return (string) get_post_meta( $post_id, 'container_title', true );
	}
	return '';
}

/**
 * One row: year | type | title · source.
 */
function cc_item_row( int $post_id ): void {
	$source = cc_item_source( $post_id );
	?>
	<li class="cc-row">
		<span class="cc-label"><?php echo esc_html( cc_item_year( $post_id ) ); ?></span>
		<span class="cc-label cc-row__type"><?php echo esc_html( cc_item_type( $post_id ) ); ?></span>
		<span class="cc-row__title">
			<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
			<?php if ( $source ) : ?>
				<span class="cc-quiet">· <?php echo esc_html( $source ); ?></span>
			<?php endif; ?>
		</span>
	</li>
	<?php
}

/**
 * Chicago-style citation (HTML) for a book. Only known parts are included.
 */
function cc_book_citation_chicago( int $post_id ): string {
	$m     = cc_book_meta( $post_id );
	$title = get_the_title( $post_id ) . ( $m['subtitle'] ? ': ' . $m['subtitle'] : '' );
	$imprint = trim( implode( ', ', array_filter( array( $m['publisher'], $m['year'] ) ) ) );

	$out = 'Canaparo, Claudio. <em>' . esc_html( $title ) . '</em>.';
	if ( $imprint ) {
		$out .= ' ' . esc_html( $imprint ) . '.';
	}
	if ( $m['doi'] ) {
		$out .= ' https://doi.org/' . esc_html( $m['doi'] ) . '.';
	}
	return $out;
}

/**
 * BibTeX entry (plain text) for a book.
 */
function cc_book_bibtex( int $post_id ): string {
	$m      = cc_book_meta( $post_id );
	$key    = 'canaparo' . ( $m['year'] ? $m['year'] : '' ) . preg_replace( '/[^a-z]/', '', strtolower( remove_accents( strtok( get_the_title( $post_id ), ' ' ) ) ) );
	$fields = array(
		'author'    => 'Canaparo, Claudio',
		'title'     => get_the_title( $post_id ),
		'subtitle'  => $m['subtitle'],
		'publisher' => $m['publisher'],
		'year'      => $m['year'],
		'series'    => $m['series'],
		'volume'    => $m['series_volume'],
		'isbn'      => $m['isbn'],
		'doi'       => $m['doi'],
		'language'  => $m['language'],
	);
	$lines = array();
	foreach ( array_filter( $fields ) as $name => $value ) {
		$lines[] = sprintf( '  %-9s = {%s}', $name, $value );
	}
	return '@book{' . $key . ",\n" . implode( ",\n", $lines ) . "\n}";
}

/**
 * schema.org Book JSON-LD on single book pages.
 */
add_action(
	'wp_head',
	function (): void {
		if ( ! is_singular( 'book' ) ) {
			return;
		}
		$id   = get_queried_object_id();
		$m    = cc_book_meta( $id );
		$data = array_filter(
			array(
				'@context'      => 'https://schema.org',
				'@type'         => 'Book',
				'name'          => get_the_title( $id ),
				'alternativeHeadline' => $m['subtitle'],
				'author'        => array( '@type' => 'Person', 'name' => 'Claudio Canaparo' ),
				'publisher'     => $m['publisher'] ? array( '@type' => 'Organization', 'name' => $m['publisher'] ) : '',
				'datePublished' => $m['year'],
				'inLanguage'    => $m['language'],
				'numberOfPages' => $m['pages'] ? (int) $m['pages'] : '',
				'isbn'          => $m['isbn'],
				'url'           => get_permalink( $id ),
				'image'         => get_the_post_thumbnail_url( $id, 'large' ),
				'sameAs'        => $m['doi'] ? 'https://doi.org/' . $m['doi'] : '',
				'description'   => wp_strip_all_tags( get_the_excerpt( $id ) ),
			)
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
	}
);

/**
 * Books archive: all books on one page, sortable by ?order=newest|oldest|title.
 * Books without a year are kept (LEFT JOIN via the NOT EXISTS clause) and sort last.
 */
add_action(
	'pre_get_posts',
	function ( WP_Query $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'book' ) ) {
			return;
		}
		$query->set( 'posts_per_page', -1 );
		$order = cc_book_order();
		if ( 'title' === $order ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
			return;
		}
		$query->set(
			'meta_query',
			array(
				'relation'  => 'OR',
				'cc_year'   => array(
					'key'     => 'year',
					'compare' => 'EXISTS',
					'type'    => 'NUMERIC',
				),
				'cc_noyear' => array(
					'key'     => 'year',
					'compare' => 'NOT EXISTS',
				),
			)
		);
		$query->set(
			'orderby',
			array(
				'cc_year' => 'oldest' === $order ? 'ASC' : 'DESC',
				'title'   => 'ASC',
			)
		);
	}
);

/**
 * Current books sort order from the query string.
 */
function cc_book_order(): string {
	$order = isset( $_GET['order'] ) ? sanitize_key( wp_unslash( $_GET['order'] ) ) : 'newest'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return in_array( $order, array( 'newest', 'oldest', 'title' ), true ) ? $order : 'newest';
}
