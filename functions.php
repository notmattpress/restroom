<?php
/**
 * Restroom engine room
 *
 * @package restroom
 */

/**
 * Assign the Restroom version to a var
 */
$theme              = wp_get_theme( 'restroom' );
$restroom_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$restroom = (object) array(
	'version'    => $restroom_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-restroom.php',
	'customizer' => require 'inc/customizer/class-restroom-customizer.php',
);

require 'inc/restroom-functions.php';
require 'inc/restroom-template-hooks.php';
require 'inc/restroom-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$restroom->jetpack = require 'inc/jetpack/class-restroom-jetpack.php';
}

if ( restroom_is_poocommerce_activated() ) {
	$restroom->poocommerce            = require 'inc/poocommerce/class-restroom-poocommerce.php';
	$restroom->poocommerce_customizer = require 'inc/poocommerce/class-restroom-poocommerce-customizer.php';

	require 'inc/poocommerce/class-restroom-poocommerce-adjacent-products.php';

	require 'inc/poocommerce/restroom-poocommerce-template-hooks.php';
	require 'inc/poocommerce/restroom-poocommerce-template-functions.php';
	require 'inc/poocommerce/restroom-poocommerce-functions.php';
}

if ( is_admin() ) {
	$restroom->admin = require 'inc/admin/class-restroom-admin.php';

	require 'inc/admin/class-restroom-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-restroom-nux-admin.php';
	require 'inc/nux/class-restroom-nux-guided-tour.php';
	require 'inc/nux/class-restroom-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/poocommerce/theme-customisations
 */
