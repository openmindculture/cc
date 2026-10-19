<?php
/**
 * @package cc
 * @author openmindculture
 * @version 1.1.0
 *
 * @wordpress-plugin
 * Version: 1.1.0
 * Tested up to: 7.1
 * Plugin Name: CC Register Custom Post Types
 * Text Domain: cc-register-custom-post-types
 * Author: openmindculture
 * Author URI: https://wordpress.org/support/users/openmindculture/
 * Plugin URI: https://github.com/openmindculture/cc
 * Description: Plugin to register custom post types
 */

add_action('init',
    function (): void {
        if (post_type_exists('book')) {
            return;
        }
        register_post_type('book',
            array(
                'labels' => array(
                    'name' => 'Books',
                    'singular_name' => 'Book',
                    'add_new_item' => 'Add new book',
                    'edit_item' => 'Edit book',
                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon' => 'dashicons-book',
                'supports' => array(
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                    'custom-fields',
                ),
                'rewrite' => array(
                    'slug' => 'books'
                ),
                'show_in_rest' => true,
            )
        );
        register_post_meta('book', 'year', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'integer',
            'description' => __('Year', 'twentytwentyfive-child'),
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ));
        register_post_meta('film', 'isbn', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'description' => __('ISBN', 'twentytwentyfive-child'),
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ));
    }
);


