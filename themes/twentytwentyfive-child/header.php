<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <title><?php the_title() ?> - <?php bloginfo( 'name' ); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
    <script src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/js/scripts.js"></script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header id="masthead" class="site-header">
    <div class="site-header-inner wp-block-group alignfull">
        <div class="site-header-row is-layout-flex wp-block-group">
            <div class="site-branding wp-block-group alignwide">
                <div class="wrap">
                    <a href="<?php
					echo esc_url( home_url( '/' ) );
					?>" rel="home" class="site-title-link">
                        <picture>
                            <img src="<?php echo get_stylesheet_directory_uri() ?>/img/logo.png" alt="home logo"
                                 class="site-logo" width="640" height="289">
                        </picture>
                    </a>
                </div>
            </div>
            <div class="site-navigation wp-block-group alignwide">

                <nav id="navigation-menu"
                     class="navigation-menu wp-block-navigation is-responsive items-justified-right is-content-justification-right wp-block-navigation-is-layout-flex">
                    <a id="site-navigation-menu-close" class="site-navigation-menu-close" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                             aria-hidden="true" focusable="false">
                            <title>Menü schließen</title>
                            <path d="m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z"></path>
                        </svg>
                    </a>
                    <ul class="wp-block-navigation__container is-responsive items-justified-right wp-block-navigation">
						<?php
						wp_list_pages( array(
							'title_li'    => '',
							'sort_column' => 'menu_order',
							'post_type'   => 'page',
							'post_status' => 'publish',
							'exclude'     => '', /* Comma-separated list of page IDs to exclude. */
							'depth'       => 0,  /* 0 (zero) means unlimited depth */
						) );
						?>
                    </ul>
                </nav>
                <a id="site-navigation-menu-link" class="site-navigation-menu-link" href="#navigation-menu">
                    <svg width="57" height="50" viewBox="0 0 57 50" xmlns="http://www.w3.org/2000/svg"><title>Menü
                            öffnen</title>
                        <g fill="currentColor">
                            <rect width="50" height="3" x="0" y="0"/>
                            <rect width="50" height="3" x="0" y="12"/>
                            <rect width="50" height="3" x="0" y="24"/>
                        </g>
                    </svg>
                </a>

            </div>
        </div>
    </div>
</header>