<?php
/**
 * @package twentytwentyfive-child
 * @author openmindculture
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( get_stylesheet_directory() . '/inc/functions/customize-frontend.php' );

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'twentytwentyfive-child-style', get_stylesheet_uri() );
	wp_dequeue_style('twentytwentyfive-style');
	wp_dequeue_style('twentytwentyfive-block-style');
} );

// disable "choose pattern" popup when adding a page in wp-admin
add_action( 'init', function () {
	if (! class_exists('WP_Block_Patterns_Registry')) {
		return;
	}

	$registry = WP_Block_Patterns_Registry::get_instance();
	$patterns = $registry->get_all_registered();

	foreach ($patterns as $pattern) {
		if (empty($pattern['blockTypes']) || ! in_array('core/post-content', $pattern['blockTypes'], true)) {
			continue;
		}
		unregister_block_pattern( $pattern['name']);

		$pattern['blockTypes'] = array_values(
			array_diff( $pattern['blockTypes'], array( 'core/post-content'))
		);

		register_block_pattern( $pattern['name'], $pattern);
	}
}, 20);

if (is_admin()) {
	require_once( get_stylesheet_directory() . '/inc/functions/customize-backend.php' );
}