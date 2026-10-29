<?php
/**
 * Site header (hybrid theme). DESIGN.md §5 "Header".
 *
 * @package twentytwentyfive-child
 */

$cc_is_home = is_front_page();
$cc_name    = get_bloginfo( 'name' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="cc-skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'twentytwentyfive-child' ); ?></a>

<header class="cc-header cc-wrap<?php echo $cc_is_home ? ' cc-header--home' : ''; ?>">
	<div class="cc-header__bar">
		<a class="cc-index-link" href="#site-index"><?php esc_html_e( 'Index', 'twentytwentyfive-child' ); ?></a>
		<?php if ( ! $cc_is_home ) : ?>
			<a class="cc-name" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $cc_name ); ?></a>
		<?php endif; ?>
		<?php cc_search_form(); ?>
	</div>
	<?php if ( $cc_is_home ) : ?>
		<a class="cc-name" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-current="page"><?php echo esc_html( $cc_name ); ?></a>
		<p class="cc-affiliation cc-affiliation--header"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
	<?php endif; ?>
</header>
