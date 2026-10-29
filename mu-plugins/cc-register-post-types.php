<?php
/**
 * @package cc
 * @author openmindculture
 * @version 1.2.0
 *
 * @wordpress-plugin
 * Version: 1.2.0
 * Tested up to: 7.1
 * Plugin Name: CC Register Custom Post Types
 * Text Domain: cc-register-custom-post-types
 * Author: openmindculture
 * Author URI: https://wordpress.org/support/users/openmindculture/
 * Plugin URI: https://github.com/openmindculture/cc
 * Description: Registers the content model (books, publications, publication types and their meta). See DESIGN.md §7.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a set of post meta fields for one post type.
 *
 * @param string $post_type Post type slug.
 * @param array  $fields    meta_key => [ 'type' => 'string'|'integer', 'label' => string, 'sanitize' => callable ].
 */
function cc_register_meta_fields( string $post_type, array $fields ): void {
	foreach ( $fields as $key => $field ) {
		$type = $field['type'] ?? 'string';
		register_post_meta(
			$post_type,
			$key,
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => $type,
				'description'       => $field['label'],
				'sanitize_callback' => $field['sanitize'] ?? ( 'integer' === $type ? 'absint' : 'sanitize_text_field' ),
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}

add_action(
	'init',
	function (): void {

		/* ---------- Books ---------- */

		if ( ! post_type_exists( 'book' ) ) {
			register_post_type(
				'book',
				array(
					'labels'        => array(
						'name'          => 'Books',
						'singular_name' => 'Book',
						'add_new_item'  => 'Add new book',
						'edit_item'     => 'Edit book',
					),
					'public'        => true,
					'has_archive'   => true,
					'menu_icon'     => 'dashicons-book',
					'menu_position' => 20,
					'supports'      => array(
						'title',
						'editor',     // About the book, Contents, Reviews (Details blocks).
						'thumbnail',  // Cover.
						'excerpt',    // Short abstract for lists and meta description.
						'custom-fields',
					),
					'rewrite'       => array(
						'slug' => 'books',
					),
					'show_in_rest'  => true,
				)
			);
		}

		cc_register_meta_fields(
			'book',
			array(
				'subtitle'      => array( 'label' => 'Subtitle' ),
				'year'          => array( 'label' => 'Year', 'type' => 'integer' ),
				'publisher'     => array( 'label' => 'Publisher' ),
				'series'        => array( 'label' => 'Series' ),
				'series_volume' => array( 'label' => 'Series volume' ),
				'language'      => array( 'label' => 'Language (BCP 47 code, e.g. es, en, fr)' ),
				'pages'         => array( 'label' => 'Number of pages', 'type' => 'integer' ),
				'isbn'          => array( 'label' => 'ISBN (print)' ),
				'isbn_ebook'    => array( 'label' => 'ISBN (eBook)' ),
				'doi'           => array( 'label' => 'DOI (without https://doi.org/)' ),
				'publisher_url' => array( 'label' => 'Publisher page URL', 'sanitize' => 'esc_url_raw' ),
				'cover_credit'  => array( 'label' => 'Cover artwork credit: title, artist, year, medium' ),
			)
		);

		/* ---------- Publications (articles, chapters, edited volumes, reviews) ---------- */

		if ( ! post_type_exists( 'publication' ) ) {
			register_post_type(
				'publication',
				array(
					'labels'        => array(
						'name'          => 'Publications',
						'singular_name' => 'Publication',
						'add_new_item'  => 'Add new publication',
						'edit_item'     => 'Edit publication',
					),
					'public'        => true,
					'has_archive'   => true,
					'menu_icon'     => 'dashicons-media-text',
					'menu_position' => 21,
					'supports'      => array(
						'title',
						'editor',
						'excerpt',
						'custom-fields',
					),
					'taxonomies'    => array( 'publication_type' ),
					'rewrite'       => array(
						'slug' => 'publications',
					),
					'show_in_rest'  => true,
				)
			);
		}

		if ( ! taxonomy_exists( 'publication_type' ) ) {
			register_taxonomy(
				'publication_type',
				array( 'publication' ),
				array(
					'labels'            => array(
						'name'          => 'Publication types',
						'singular_name' => 'Publication type',
					),
					'public'            => true,
					'hierarchical'      => true, // Checkbox UI; terms: Articles, Chapters, Edited volumes, Reviews.
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'rewrite'           => array(
						'slug' => 'publications/type',
					),
				)
			);
		}

		cc_register_meta_fields(
			'publication',
			array(
				'year'            => array( 'label' => 'Year', 'type' => 'integer' ),
				'container_title' => array( 'label' => 'Journal or book title' ),
				'editors'         => array( 'label' => 'Editors of the containing book (chapters)' ),
				'volume'          => array( 'label' => 'Volume' ),
				'issue'           => array( 'label' => 'Issue / number' ),
				'pages_range'     => array( 'label' => 'Page range, e.g. 12–34' ),
				'publisher'       => array( 'label' => 'Publisher (chapters, edited volumes)' ),
				'place'           => array( 'label' => 'Place of publication' ),
				'language'        => array( 'label' => 'Language (BCP 47 code, e.g. es, en, fr)' ),
				'doi'             => array( 'label' => 'DOI (without https://doi.org/)' ),
				'url'             => array( 'label' => 'URL (journal page or PDF)', 'sanitize' => 'esc_url_raw' ),
			)
		);
	}
);
