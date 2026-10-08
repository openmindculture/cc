<?php

if ( is_admin() ) {

	if ( ! defined( 'custom_CUSTOM_BLOCK_TYPES' ) ) {
		$custom_custom_block_types = array(
			'custom-card',
		);
		define( 'custom_CUSTOM_BLOCK_TYPES', $custom_custom_block_types );
	}

	add_action( 'after_setup_theme', function () {
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style.css' );
		// no need to add custom block styles here, as they will be loaded by block editor JS
		// workaround to ensure custom block styles are applied correctly
		foreach ( custom_CUSTOM_BLOCK_TYPES as $block_type ) {
			add_editor_style( 'blocks/' . $block_type . '/' . $block_type . '.css' );
		}
	} );

	add_action( 'admin_enqueue_scripts', function () {
		foreach ( custom_CUSTOM_BLOCK_TYPES as $block_type ) {
			wp_enqueue_script_module(
				'custom_admin_script_register_block_' . $block_type,
				get_stylesheet_directory_uri() . '/blocks/' . $block_type . '/' . $block_type . '.js',
				array( 'wp-blocks' ),
				wp_get_theme()->get( 'Version' )
			);
		}
	} );

	function custom_allowed_block_types( $allowed_block_types ) {
		// Get all registered blocks if $allowed_block_types is not already set.
		// https://developer.wordpress.org/news/2024/01/29/how-to-disable-specific-blocks-in-wordpress/
		if ( ! is_array( $allowed_block_types ) || empty( $allowed_block_types ) ) {
			$registered_blocks   = WP_Block_Type_Registry::get_instance()->get_all_registered();
			$allowed_block_types = array_keys( $registered_blocks );
		}
		/* https://wordpress.org/documentation/article/blocks-list/ */
		$theme_allowed_block_types = array();
		$disallowed_block_types    = array(
			/* text */
			'core/classic',
			'core/code',
			/* 'core/details', generic read-more accordion */
			'core/freeform',
			'core/preformatted',
			'core/quote',
			'core/verse',
			/* media */
			'core/audio',
			'core/cover',
			'core/gallery',
			'core/media-text',
			/* design */
			'core/more',
			'core/nextpage',
			'core/page-break',
			'core/separator',
			'group/group-grid',
			/* widgets */
			'core/archives',
			'core/calendar',
			'core/categories',
			'core/latest-comments',
			/* 'core/latest-posts', */
			'core/page-list',
			'core/rss',
			'core/search',
			'core/search',
			'core/tag-cloud',
			/* theme */
			/* 'core/site-logo',
			'core/site-title',
			'core/site-tagline', */
			'core/loginout',
			/* 'core/query', */
			/* 'core/query-loop', */
			'post-terms/category',
			'post-terms/post_tag',
			'core/navigation',
			'core/post-template',
			/* 'core/post-title',
			'core/post-excerpt',
			'core/post-featured-image',
			'core/post-content',
			'core/post-author',  */
			'core/post-author-biography',
			/* 'core/post-author-name',
			'core/post-date',
			'core/post-modified-date', */
			'core/post-categories',
			'core/post-tags',
			'core/pagination',
			'core/posts-list',
			'core/avatar',
			'core/read-more',
			'core/comments',
			'core/post-comments-form',
			'core/term-description',
			'core/template-part',
			'query-title/archive-title',
			/* embed */
			'core/embed',
			'core/twitter-embed',
			'core/wordpress-tv-embed',
			'core/bluesky-embed',
			'core/facebook-embed'
		);
		foreach ( $allowed_block_types as $block_type ) {
			if ( ! in_array( $block_type, $disallowed_block_types ) ) {
				$theme_allowed_block_types[] = $block_type;
			}
		}
		foreach ( custom_CUSTOM_BLOCK_TYPES as $block_type ) {
			$theme_allowed_block_types[] = $block_type;
		}

		return $theme_allowed_block_types;
	}

	add_filter( 'allowed_block_types_all', 'custom_allowed_block_types', 10, 1 );
	// TODO unregisterBlockVariation for those core blocks that are variations if necessary

	/** allow SVG upload to media library */
	/* + define('ALLOW_UNFILTERED_UPLOADS', true); needs to be in project wp-config */
	function cc_mime_types( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';

		return $mimes;
	}

	add_filter( 'upload_mimes', 'cc_mime_types' );
}
