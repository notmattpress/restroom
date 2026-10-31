<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package restroom
 */

?>

		</div><!-- .col-full -->
	</div><!-- #content -->

	<?php do_action( 'restroom_before_footer' ); ?>

	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="col-full">

			<?php
			/**
			 * Functions hooked in to restroom_footer action
			 *
			 * @hooked restroom_footer_widgets - 10
			 * @hooked restroom_credit         - 20
			 */
			do_action( 'restroom_footer' );
			?>

		</div><!-- .col-full -->
	</footer><!-- #colophon -->

	<?php do_action( 'restroom_after_footer' ); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
