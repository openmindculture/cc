<?php
/**
 * The pastel field: one per page, chosen in the editor (DESIGN.md §2, §7).
 *
 * Presentation meta lives in the theme on purpose: if the theme changes,
 * losing the field choice is harmless. Content meta lives in the mu-plugin.
 *
 * @package twentytwentyfive-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field slugs => labels. Slugs match the theme.json palette (field-{slug}).
 *
 * @return array<string,string>
 */
function cc_fields(): array {
	return array(
		'sage'    => __( 'Sage', 'twentytwentyfive-child' ),
		'stone'   => __( 'Stone', 'twentytwentyfive-child' ),
		'lagoon'  => __( 'Lagoon', 'twentytwentyfive-child' ),
		'rose'    => __( 'Rose', 'twentytwentyfive-child' ),
		'apricot' => __( 'Apricot', 'twentytwentyfive-child' ),
	);
}

add_action(
	'init',
	function (): void {
		$auth = function () {
			return current_user_can( 'edit_posts' );
		};

		foreach ( array( 'page', 'post', 'book', 'publication' ) as $post_type ) {
			register_post_meta(
				$post_type,
				'cc_field',
				array(
					'single'            => true,
					'type'              => 'string',
					'default'           => '',
					'description'       => 'Background field colour for this page (empty = template default).',
					'sanitize_callback' => function ( $value ) {
						return array_key_exists( $value, cc_fields() ) ? $value : '';
					},
					'auth_callback'     => $auth,
					'show_in_rest'      => array(
						'schema' => array(
							'type' => 'string',
							'enum' => array_merge( array( '' ), array_keys( cc_fields() ) ),
						),
					),
				)
			);
		}

		// Front-page quotation band (DESIGN.md §5): the Quote block is not offered to editors.
		register_post_meta(
			'page',
			'cc_quote',
			array(
				'single'            => true,
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => $auth,
				'show_in_rest'      => true,
			)
		);
		register_post_meta(
			'page',
			'cc_quote_source',
			array(
				'single'            => true,
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth,
				'show_in_rest'      => true,
			)
		);
		register_post_meta(
			'page',
			'cc_quote_lang',
			array(
				'single'            => true,
				'type'              => 'string',
				'default'           => 'es',
				'sanitize_callback' => 'sanitize_key',
				'auth_callback'     => $auth,
				'show_in_rest'      => true,
			)
		);
	}
);

/**
 * The field for the current request: the editor's choice, else the template default.
 */
function cc_current_field(): string {
	if ( is_singular() ) {
		$chosen = get_post_meta( get_queried_object_id(), 'cc_field', true );
		if ( is_string( $chosen ) && array_key_exists( $chosen, cc_fields() ) ) {
			return $chosen;
		}
	}

	if ( is_front_page() ) {
		$default = 'sage';
	} elseif ( is_singular( 'book' ) ) {
		$default = 'rose';
	} elseif ( is_post_type_archive( 'book' ) ) {
		$default = 'stone';
	} elseif ( is_post_type_archive( 'publication' ) || is_tax( 'publication_type' ) || is_singular( 'publication' ) ) {
		$default = 'lagoon';
	} elseif ( is_search() || is_404() ) {
		$default = 'apricot';
	} else {
		$default = 'sage';
	}

	/** Filter the template default field slug. */
	return (string) apply_filters( 'cc_default_field', $default );
}

add_filter(
	'body_class',
	function ( array $classes ): array {
		$classes[] = 'has-field-' . sanitize_html_class( cc_current_field() );
		return $classes;
	}
);

/* Editor: a small "Page design" panel (no build step, WordPress globals only). */
add_action(
	'enqueue_block_editor_assets',
	function (): void {
		$file = get_stylesheet_directory() . '/js/editor-field-panel.js';
		wp_enqueue_script(
			'cc-editor-field-panel',
			get_stylesheet_directory_uri() . '/js/editor-field-panel.js',
			array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n' ),
			(string) filemtime( $file ),
			true
		);
		wp_localize_script(
			'cc-editor-field-panel',
			'ccFieldPanel',
			array(
				'fields'    => cc_fields(),
				'postTypes' => array( 'page', 'post', 'book', 'publication' ),
			)
		);
	}
);
