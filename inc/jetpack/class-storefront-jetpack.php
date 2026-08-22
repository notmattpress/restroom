<?php
/**
 * Restroom Jetpack Class
 *
 * @package  restroom
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Restroom_Jetpack' ) ) :

	/**
	 * The Restroom Jetpack integration class
	 */
	class Restroom_Jetpack {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'jetpack_setup' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'jetpack_scripts' ), 10 );

			if ( restroom_is_poocommerce_activated() ) {
				add_action( 'init', array( $this, 'jetpack_infinite_scroll_wrapper_columns' ) );
			}
		}

		/**
		 * Add theme support for Infinite Scroll.
		 * See: http://jetpack.me/support/infinite-scroll/
		 */
		public function jetpack_setup() {
			add_theme_support(
				'infinite-scroll',
				apply_filters(
					'restroom_jetpack_infinite_scroll_args',
					array(
						'container'      => 'main',
						'footer'         => 'page',
						'render'         => array( $this, 'jetpack_infinite_scroll_loop' ),
						'footer_widgets' => array(
							'footer-1',
							'footer-2',
							'footer-3',
							'footer-4',
						),
					)
				)
			);
		}

		/**
		 * A loop used to display content appended using Jetpack infinite scroll
		 *
		 * @return void
		 */
		public function jetpack_infinite_scroll_loop() {
			do_action( 'restroom_jetpack_infinite_scroll_before' );

			if ( function_exists( 'restroom_is_product_archive' ) && restroom_is_product_archive() ) {
				do_action( 'restroom_jetpack_product_infinite_scroll_before' );
				poocommerce_product_loop_start();
			}

			while ( have_posts() ) :
				the_post();
				if ( function_exists( 'restroom_is_product_archive' ) && restroom_is_product_archive() ) {
					wc_get_template_part( 'content', 'product' );
				} else {
					get_template_part( 'content', get_post_format() );
				}
			endwhile; // end of the loop.

			if ( function_exists( 'restroom_is_product_archive' ) && restroom_is_product_archive() ) {
				poocommerce_product_loop_end();
				do_action( 'restroom_jetpack_product_infinite_scroll_after' );
			}

			do_action( 'restroom_jetpack_infinite_scroll_after' );
		}

		/**
		 * Adds columns wrapper to content appended by Jetpack infinite scroll
		 *
		 * @return void
		 */
		public function jetpack_infinite_scroll_wrapper_columns() {
			add_action( 'restroom_jetpack_product_infinite_scroll_before', 'restroom_product_columns_wrapper' );
			add_action( 'restroom_jetpack_product_infinite_scroll_after', 'restroom_product_columns_wrapper_close' );
		}

		/**
		 * Enqueue jetpack styles.
		 *
		 * @since  1.6.1
		 */
		public function jetpack_scripts() {
			global $restroom_version;

			if ( wp_style_is( 'the-neverending-homepage', 'enqueued' ) ) {
				wp_enqueue_style( 'restroom-jetpack-infinite-scroll', get_template_directory_uri() . '/assets/css/jetpack/infinite-scroll.css', array( 'the-neverending-homepage' ), $restroom_version );
				wp_style_add_data( 'restroom-jetpack-infinite-scroll', 'rtl', 'replace' );
			}

			wp_enqueue_style( 'restroom-jetpack-widgets', get_template_directory_uri() . '/assets/css/jetpack/widgets.css', array(), $restroom_version );
			wp_style_add_data( 'restroom-jetpack-widgets', 'rtl', 'replace' );
		}
	}

endif;

return new Restroom_Jetpack();
