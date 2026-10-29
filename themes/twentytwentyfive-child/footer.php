<?php
/**
 * Site footer (hybrid theme). DESIGN.md §5 "Footer".
 *
 * @package twentytwentyfive-child
 */
?>
<footer class="cc-footer">
	<div class="cc-wrap cc-footer__inner">
		<p>
			<?php
			printf(
				/* translators: 1: year, 2: site name */
				esc_html__( '© %1$s %2$s · artwork © the artists', 'twentytwentyfive-child' ),
				esc_html( wp_date( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</p>
		<?php cc_footer_nav(); ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
