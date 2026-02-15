<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package restroom
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'restroom_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action( 'restroom_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner" style="<?php restroom_header_styles(); ?>">

		<?php
		/**
		 * Functions hooked into restroom_header action
		 *
		 * @hooked restroom_header_container                 - 0
		 * @hooked restroom_skip_links                       - 5
		 * @hooked restroom_social_icons                     - 10
		 * @hooked restroom_site_branding                    - 20
		 * @hooked restroom_secondary_navigation             - 30
		 * @hooked restroom_product_search                   - 40
		 * @hooked restroom_header_container_close           - 41
		 * @hooked restroom_primary_navigation_wrapper       - 42
		 * @hooked restroom_primary_navigation               - 50
		 * @hooked restroom_header_cart                      - 60
		 * @hooked restroom_primary_navigation_wrapper_close - 68
		 */
		do_action( 'restroom_header' );
		?>

	</header><!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to restroom_before_content
	 *
	 * @hooked restroom_header_widget_region - 10
	 * @hooked poocommerce_breadcrumb - 10
	 */
	do_action( 'restroom_before_content' );
	?>

	<div id="content" class="site-content" tabindex="-1">
		<div class="col-full">

		<?php
		do_action( 'restroom_content_top' );
