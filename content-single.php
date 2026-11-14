<?php
/**
 * Template used to display post content on single pages.
 *
 * @package restroom
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	do_action( 'restroom_single_post_top' );

	/**
	 * Functions hooked into restroom_single_post add_action
	 *
	 * @hooked restroom_post_header          - 10
	 * @hooked restroom_post_content         - 30
	 */
	do_action( 'restroom_single_post' );

	/**
	 * Functions hooked in to restroom_single_post_bottom action
	 *
	 * @hooked restroom_post_nav         - 10
	 * @hooked restroom_display_comments - 20
	 */
	do_action( 'restroom_single_post_bottom' );
	?>

</article><!-- #post-## -->
