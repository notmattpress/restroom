<?php
/**
 * Template used to display post content.
 *
 * @package restroom
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	/**
	 * Functions hooked in to restroom_loop_post action.
	 *
	 * @hooked restroom_post_header          - 10
	 * @hooked restroom_post_content         - 30
	 * @hooked restroom_post_taxonomy        - 40
	 */
	do_action( 'restroom_loop_post' );
	?>

</article><!-- #post-## -->
