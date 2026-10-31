<?php
/**
 * Design v5: block style and front-end script.
 * Load from functions.php: require_once get_stylesheet_directory() . '/inc/v5.php';
 *
 * @package twentytwentyfive-child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Same CSS in the Site Editor canvas, so header and window look as on the front end.
add_action(
    'after_setup_theme',
    function () {
        add_editor_style( 'style.css' );
    }
);

// "Window" block style for Cover and Image: picture drifts inside its frame (style.css §3).
add_action(
    'init',
    function () {
        foreach ( array( 'core/cover', 'core/image' ) as $cc_block ) {
            register_block_style(
                $cc_block,
                array(
                    'name'  => 'window',
                    'label' => __( 'Window (scroll drift)', 'twentytwentyfive-child' ),
                )
            );
        }
    }
);

add_action(
    'wp_enqueue_scripts',
    function () {
        $cc_version = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'twentytwentyfive-child-style', get_stylesheet_uri(), array(), $cc_version );
        wp_enqueue_script(
            'cc-v5',
            get_stylesheet_directory_uri() . '/js/script.js',
            array(),
            $cc_version,
            array(
                'strategy'  => 'defer',
                'in_footer' => true,
            )
        );
    }
);
