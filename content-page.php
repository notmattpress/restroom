<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package restroom
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked in to restroom_page add_action
	 *
	 * @hooked restroom_page_header          - 10
	 * @hooked restroom_page_content         - 20
	 */
	do_action( 'restroom_page' );
	?>
</article><!-- #post-## -->
