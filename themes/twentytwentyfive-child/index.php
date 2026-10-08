<?php // fallback code for classic/hybrid theme behavior; remove this file for full-site editing
get_header();
if (have_posts()):
	the_post();
	?>
    <main class="site-main wp-block-group" id="site-main">
        <div class="wp-block-group alignfull">
            <h1 class="site-main-title"><?php echo the_title() ?></h1>
			<?php /* echo get_the_post_thumbnail( get_the_ID(), 'full' ); */  ?>
            <div class="custom-content-wrapper alignfull is-layout-constrained">
				<?php the_content(); ?>
            </div>
        </div>
    </main>
<?php endif; /* have_posts */
get_footer();
?>