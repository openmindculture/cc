<?php
/**
 * Idempotent book import from seed/books.csv.
 *
 * Usage (path as seen inside the WP-CLI container):
 *   wp eval-file seed/seed.php [books.csv] [covers-dir]
 *
 * - Updates an existing book by slug (or by ISBN), never duplicates.
 * - Empty CSV cells leave existing values untouched (unknown facts are never overwritten with blanks).
 * - cover_file is looked up in covers-dir (default: data/covers next to the repo root) and imported once.
 *
 * @package cc
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit;
}

$cc_dir    = __DIR__;
$cc_csv    = $args[0] ?? $cc_dir . '/books.csv';
$cc_covers = $args[1] ?? dirname( $cc_dir ) . '/data/covers';

if ( ! post_type_exists( 'book' ) ) {
	WP_CLI::error( 'Post type "book" is not registered. Is mu-plugins/cc-register-post-types.php mounted?' );
}
if ( ! is_readable( $cc_csv ) ) {
	WP_CLI::error( "Cannot read $cc_csv" );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$cc_handle = fopen( $cc_csv, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
$cc_header = fgetcsv( $cc_handle, 0, ',', '"', '\\' );
$cc_meta   = array( 'subtitle', 'year', 'publisher', 'series', 'series_volume', 'language', 'pages', 'isbn', 'isbn_ebook', 'doi', 'publisher_url', 'cover_credit' );
$cc_stats  = array( 'created' => 0, 'updated' => 0 );

while ( ( $cc_cells = fgetcsv( $cc_handle, 0, ',', '"', '\\' ) ) !== false ) {
	if ( count( $cc_cells ) !== count( $cc_header ) ) {
		WP_CLI::warning( 'Skipping malformed row: ' . implode( ',', $cc_cells ) );
		continue;
	}
	$cc_row = array_map( 'trim', array_combine( $cc_header, $cc_cells ) );
	if ( '' === $cc_row['title'] ) {
		continue;
	}
	$cc_slug = $cc_row['slug'] ? sanitize_title( $cc_row['slug'] ) : sanitize_title( $cc_row['title'] );

	// Find existing: by slug, then by ISBN.
	$cc_existing = get_page_by_path( $cc_slug, OBJECT, 'book' );
	if ( ! $cc_existing && $cc_row['isbn'] ) {
		$cc_found    = get_posts( array( 'post_type' => 'book', 'post_status' => 'any', 'meta_key' => 'isbn', 'meta_value' => $cc_row['isbn'], 'numberposts' => 1 ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		$cc_existing = $cc_found ? $cc_found[0] : null;
	}

	$cc_post = array(
		'post_type'   => 'book',
		'post_title'  => $cc_row['title'],
		'post_name'   => $cc_slug,
		'post_status' => 'publish',
	);
	if ( $cc_existing ) {
		$cc_post['ID'] = $cc_existing->ID;
		$cc_id         = wp_update_post( $cc_post, true );
		++$cc_stats['updated'];
	} else {
		$cc_id = wp_insert_post( $cc_post, true );
		++$cc_stats['created'];
	}
	if ( is_wp_error( $cc_id ) ) {
		WP_CLI::warning( $cc_row['title'] . ': ' . $cc_id->get_error_message() );
		continue;
	}

	foreach ( $cc_meta as $cc_key ) {
		if ( isset( $cc_row[ $cc_key ] ) && '' !== $cc_row[ $cc_key ] ) {
			update_post_meta( $cc_id, $cc_key, $cc_row[ $cc_key ] );
		}
	}

	// Cover: import once, remember the source file name.
	$cc_file = $cc_row['cover_file'] ?? '';
	if ( $cc_file && get_post_meta( $cc_id, '_cc_cover_source', true ) !== $cc_file ) {
		$cc_path = trailingslashit( $cc_covers ) . basename( $cc_file );
		if ( is_readable( $cc_path ) ) {
			$cc_tmp = wp_tempnam( $cc_file );
			copy( $cc_path, $cc_tmp );
			$cc_att = media_handle_sideload( array( 'name' => basename( $cc_file ), 'tmp_name' => $cc_tmp ), $cc_id, $cc_row['title'] );
			if ( is_wp_error( $cc_att ) ) {
				WP_CLI::warning( "Cover for {$cc_row['title']}: " . $cc_att->get_error_message() );
			} else {
				set_post_thumbnail( $cc_id, $cc_att );
				update_post_meta( $cc_id, '_cc_cover_source', $cc_file );
			}
		} else {
			WP_CLI::warning( "Cover file not found: $cc_path" );
		}
	}
	WP_CLI::log( ( $cc_existing ? 'Updated ' : 'Created ' ) . $cc_row['title'] . " (#$cc_id)" );
}
fclose( $cc_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions

WP_CLI::success( sprintf( '%d created, %d updated.', $cc_stats['created'], $cc_stats['updated'] ) );
