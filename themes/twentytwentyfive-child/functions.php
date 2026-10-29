<?php
/**
 * @package twentytwentyfive-child
 * @author openmindculture
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( get_stylesheet_directory() . '/inc/functions/customize-frontend.php' );
require_once( get_stylesheet_directory() . '/inc/functions/field.php' );
require_once( get_stylesheet_directory() . '/inc/functions/template-tags.php' );

add_action( 'wp_enqueue_scripts', function() {
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'twentytwentyfive-child-style', get_stylesheet_uri(), array(), $version );
	wp_dequeue_style('twentytwentyfive-style');
	wp_dequeue_style('twentytwentyfive-block-style');
	// Only needed for the opt-in "Read more" Details style.
	wp_enqueue_script( 'twentytwentyfive-child-scripts', get_stylesheet_directory_uri() . '/js/scripts.js', array(), $version, array( 'strategy' => 'defer', 'in_footer' => true ) );
} );

// Block styles editors can pick (DESIGN.md §5, §7).
add_action( 'init', function () {
	register_block_style( 'core/group', array( 'name' => 'field', 'label' => __( 'Field', 'twentytwentyfive-child' ) ) );
	register_block_style( 'core/image', array( 'name' => 'printed', 'label' => __( 'Printed into field', 'twentytwentyfive-child' ) ) );
	register_block_style( 'core/details', array( 'name' => 'read-more', 'label' => __( 'Read more', 'twentytwentyfive-child' ) ) );
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