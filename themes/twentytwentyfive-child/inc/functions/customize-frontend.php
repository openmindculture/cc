<?php
add_action( 'after_setup_theme', function () {
	remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	remove_action( 'in_admin_header', 'wp_global_styles_render_svg_filters' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_action('wp_head', 'rest_output_link_wp_head', 10);
	remove_action('template_redirect', 'rest_output_link_header', 11);

	// If this is a static website without blog posts, you can remove feeds links.
	// If comments are enabled, you can remove the "extra" feeds links.
	// Optionally remove rel=alternative RSS feed links and extras (category, tag, comment)
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);

	// remove API discovery links if not used for Zapier, Instagram, etc.
	remove_action('wp_head', 'rest_output_link_wp_head', 10);
	remove_action('template_redirect', 'rest_output_link_header', 11);

	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	add_theme_support( 'disable-custom-font-sizes' );
	add_theme_support( 'disable-custom-gradients' );

	// remove the lines below to enable full-site editing header/footer templates in the block editor
	remove_theme_support( 'block-templates' );
	remove_theme_support( 'block-template-parts' );
	remove_theme_support( 'custom-header' );

	register_nav_menus( array(
		'primary' => __( 'Primary Navigation Menu', 'twentytwentyfive-child' ),
	) );
}, 10, 0 );

// remove category slug from single post urls e.g. /category/blog/foo => /blog/foo
add_filter( 'get_the_archive_title_prefix', '__return_false' );

// use short_title in navigation if set
add_filter( 'the_title', function ( $title, $post_id ) {
	// TODO restrict further if necessary
	if ( ! in_the_loop() && ! is_admin() ) {
		$short_title = get_post_meta( $post_id, 'short_title', true );
		if ( ! empty( $short_title ) ) {
			return esc_html( $short_title );
		}
	}

	return $title;
}, 10, 2 );

// Add Short Title column to Pages list
add_filter( 'manage_pages_columns', function ( $columns ) {
	// Insert after the title column
	$new_columns = array();
	foreach ( $columns as $key => $value ) {
		$new_columns[ $key ] = $value;
		if ( $key == 'title' ) {
			$new_columns['short_title'] = 'Short Title';
		}
	}

	return $new_columns;
} );

// Populate the Short Title column
add_action( 'manage_pages_custom_column', function ( $column_name, $post_id ) {
	if ( $column_name == 'short_title' ) {
		$short_title = get_post_meta( $post_id, 'short_title', true );
		echo $short_title ? esc_html( $short_title ) : '—';
	}
}, 10, 2 );

add_action( 'wp_print_styles', function () {
	wp_styles()->add_data( 'akismet-widget-style', 'after', '' );
} );

/**
 * @param string[] $classes
 *
 * @return string[]
 */
function custom_body_class( $classes ) {
	$classes[] = 'theme-custom';

	// Add page-{slug} class for all pages/posts
	$post_name = get_post_field( 'post_name', get_post() );
	if ( $post_name ) {
		$classes[] = 'page-' . sanitize_html_class( $post_name );
	}

	// Add category classes for singular posts
	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();
		foreach ( $categories as $category ) {
			$classes[] = 'in-category-' . sanitize_html_class( $category->slug );
		}
	}

	return $classes;
}

add_filter( 'body_class', 'custom_body_class' );

function custom_get_option_or_default( $option_key, $default_value ) {
	$value = get_theme_mod( $option_key );
	if ( empty( $value ) ) {
		$value = get_option( $option_key );
	}
	if ( !empty( $value ) ) {
		return $value;
	}

	return $default_value;
}

/* more or less collapsible details block */
add_filter('render_block', 'add_close_button_to_details_block', 10, 2);

function add_close_button_to_details_block($block_content, $block) {
	if ($block['blockName'] !== 'core/details') {
		return $block_content;
	}
	$close_button = '<button class="wp-block-details-close">Weniger</button>';
	$block_content = str_replace('</details>', $close_button . '</details>', $block_content);
	return $block_content;
}