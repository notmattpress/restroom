<?php
/**
 * Restroom PooCommerce Class
 *
 * @package  restroom
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Restroom_PooCommerce' ) ) :

	/**
	 * The Restroom PooCommerce Integration class
	 */
	class Restroom_PooCommerce {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_filter( 'body_class', array( $this, 'poocommerce_body_class' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'poocommerce_scripts' ), 20 );
			add_filter( 'poocommerce_output_related_products_args', array( $this, 'related_products_args' ) );
			add_filter( 'poocommerce_product_thumbnails_columns', array( $this, 'thumbnail_columns' ) );
			add_filter( 'poocommerce_breadcrumb_defaults', array( $this, 'change_breadcrumb_delimiter' ) );

			// Integrations.
			add_action( 'restroom_poocommerce_setup', array( $this, 'setup_integrations' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'poocommerce_integrations_scripts' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_customizer_css' ), 140 );

			// Instead of loading Core CSS files, we only register the font families.
			add_filter( 'poocommerce_enqueue_styles', '__return_empty_array' );
			add_filter( 'wp_enqueue_scripts', array( $this, 'add_core_fonts' ), 130 );
		}

		/**
		 * Sets up theme defaults and registers support for various PooCommerce features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * @since 2.4.0
		 * @return void
		 */
		public function setup() {
			add_theme_support(
				'poocommerce',
				apply_filters(
					'restroom_poocommerce_args',
					array(
						'single_image_width'    => 416,
						'thumbnail_image_width' => 324,
						'product_grid'          => array(
							'default_columns' => 3,
							'default_rows'    => 4,
							'min_columns'     => 1,
							'max_columns'     => 6,
							'min_rows'        => 1,
						),
					)
				)
			);

			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );

			/**
			 * Add 'restroom_poocommerce_setup' action.
			 *
			 * @since  2.4.0
			 */
			do_action( 'restroom_poocommerce_setup' );
		}

		/**
		 * Add CSS in <head> for styles handled by the theme customizer
		 * If the Customizer is active pull in the raw css. Otherwise pull in the prepared theme_mods if they exist.
		 *
		 * @since 2.1.0
		 * @return void
		 */
		public function add_customizer_css() {
			wp_add_inline_style( 'restroom-poocommerce-style', $this->get_poocommerce_extension_css() );
		}

		/**
		 * Add CSS in <head> to register PooCommerce Core fonts.
		 *
		 * @since 3.4.0
		 * @return void
		 */
		public function add_core_fonts() {
			$fonts_url = plugins_url( '/poocommerce/assets/fonts/' );
			wp_add_inline_style(
				'restroom-poocommerce-style',
				'@font-face {
				font-family: star;
				src: url(' . $fonts_url . 'star.eot);
				src:
					url(' . $fonts_url . 'star.eot?#iefix) format("embedded-opentype"),
					url(' . $fonts_url . 'star.woff) format("woff"),
					url(' . $fonts_url . 'star.ttf) format("truetype"),
					url(' . $fonts_url . 'star.svg#star) format("svg");
				font-weight: 400;
				font-style: normal;
			}
			@font-face {
				font-family: PooCommerce;
				src: url(' . $fonts_url . 'PooCommerce.eot);
				src:
					url(' . $fonts_url . 'PooCommerce.eot?#iefix) format("embedded-opentype"),
					url(' . $fonts_url . 'PooCommerce.woff) format("woff"),
					url(' . $fonts_url . 'PooCommerce.ttf) format("truetype"),
					url(' . $fonts_url . 'PooCommerce.svg#PooCommerce) format("svg");
				font-weight: 400;
				font-style: normal;
			}'
			);
		}

		/**
		 * Add PooCommerce specific classes to the body tag
		 *
		 * @param  array $classes css classes applied to the body tag.
		 * @return array $classes modified to include 'poocommerce-active' class
		 */
		public function poocommerce_body_class( $classes ) {
			$classes[] = 'poocommerce-active';

			// Remove `no-wc-breadcrumb` body class.
			$key = array_search( 'no-wc-breadcrumb', $classes, true );

			if ( false !== $key ) {
				unset( $classes[ $key ] );
			}

			return $classes;
		}

		/**
		 * PooCommerce specific scripts & stylesheets
		 *
		 * @since 1.0.0
		 */
		public function poocommerce_scripts() {
			global $restroom_version;

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'restroom-poocommerce-style', get_template_directory_uri() . '/assets/css/poocommerce/poocommerce.css', array( 'restroom-style', 'restroom-icons' ), $restroom_version );
			wp_style_add_data( 'restroom-poocommerce-style', 'rtl', 'replace' );

			wp_register_script( 'restroom-header-cart', get_template_directory_uri() . '/assets/js/poocommerce/header-cart' . $suffix . '.js', array(), $restroom_version, true );
			wp_enqueue_script( 'restroom-header-cart' );

			wp_enqueue_script( 'restroom-handheld-footer-bar', get_template_directory_uri() . '/assets/js/footer' . $suffix . '.js', array(), $restroom_version, true );

			if ( ! class_exists( 'Restroom_Sticky_Add_to_Cart' ) && is_product() ) {
				wp_register_script( 'restroom-sticky-add-to-cart', get_template_directory_uri() . '/assets/js/sticky-add-to-cart' . $suffix . '.js', array(), $restroom_version, true );
			}
		}

		/**
		 * Related Products Args
		 *
		 * @param  array $args related products args.
		 * @since 1.0.0
		 * @return  array $args related products args
		 */
		public function related_products_args( $args ) {
			$args = apply_filters(
				'restroom_related_products_args',
				array(
					'posts_per_page' => 3,
					'columns'        => 3,
				)
			);

			return $args;
		}

		/**
		 * Product gallery thumbnail columns
		 *
		 * @return integer number of columns
		 * @since  1.0.0
		 */
		public function thumbnail_columns() {
			$columns = 4;

			if ( ! is_active_sidebar( 'sidebar-1' ) ) {
				$columns = 5;
			}

			return intval( apply_filters( 'restroom_product_thumbnail_columns', $columns ) );
		}

		/**
		 * Query PooCommerce Extension Activation.
		 *
		 * @param string $extension Extension class name.
		 * @return boolean
		 */
		public function is_poocommerce_extension_activated( $extension = 'WC_Bookings' ) {
			return class_exists( $extension ) ? true : false;
		}

		/**
		 * Remove the breadcrumb delimiter
		 *
		 * @param  array $defaults The breadcrumb defaults.
		 * @return array           The breadcrumb defaults.
		 * @since 2.2.0
		 */
		public function change_breadcrumb_delimiter( $defaults ) {
			$defaults['delimiter']   = '<span class="breadcrumb-separator"> / </span>';
			$defaults['wrap_before'] = '<div class="restroom-breadcrumb"><div class="col-full"><nav class="poocommerce-breadcrumb" aria-label="' . esc_attr__( 'breadcrumbs', 'restroom' ) . '">';
			$defaults['wrap_after']  = '</nav></div></div>';
			return $defaults;
		}

		/**
		 * Integration Styles & Scripts
		 *
		 * @return void
		 */
		public function poocommerce_integrations_scripts() {
			global $restroom_version;

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			/**
			 * Bookings
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Bookings' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-bookings-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/bookings.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-bookings-style', 'rtl', 'replace' );
			}

			/**
			 * Brands
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Brands' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-brands-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/brands.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-brands-style', 'rtl', 'replace' );

				wp_enqueue_script( 'restroom-poocommerce-brands', get_template_directory_uri() . '/assets/js/poocommerce/extensions/brands' . $suffix . '.js', array(), $restroom_version, true );
			}

			/**
			 * Wishlists
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Wishlists_Wishlist' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-wishlists-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/wishlists.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-wishlists-style', 'rtl', 'replace' );
			}

			/**
			 * AJAX Layered Nav
			 */
			if ( $this->is_poocommerce_extension_activated( 'SOD_Widget_Ajax_Layered_Nav' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-ajax-layered-nav-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/ajax-layered-nav.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-ajax-layered-nav-style', 'rtl', 'replace' );
			}

			/**
			 * Variation Swatches
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_SwatchesPlugin' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-variation-swatches-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/variation-swatches.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-variation-swatches-style', 'rtl', 'replace' );
			}

			/**
			 * Composite Products
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Composite_Products' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-composite-products-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/composite-products.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-composite-products-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Photography
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Photography' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-photography-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/photography.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-photography-style', 'rtl', 'replace' );
			}

			/**
			 * Product Reviews Pro
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Product_Reviews_Pro' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-product-reviews-pro-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/product-reviews-pro.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-product-reviews-pro-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Smart Coupons
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Smart_Coupons' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-smart-coupons-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/smart-coupons.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-smart-coupons-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Deposits
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Deposits' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-deposits-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/deposits.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-deposits-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Product Bundles
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Bundles' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-bundles-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/bundles.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-bundles-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Multiple Shipping Addresses
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Ship_Multiple' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-sma-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/ship-multiple-addresses.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-sma-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Advanced Product Labels
			 */
			if ( $this->is_poocommerce_extension_activated( 'Woocommerce_Advanced_Product_Labels' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-apl-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/advanced-product-labels.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-apl-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Mix and Match
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Mix_and_Match' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-mix-and-match-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/mix-and-match.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-mix-and-match-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Memberships
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Memberships' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-memberships-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/memberships.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-memberships-style', 'rtl', 'replace' );
			}

			/**
			 * PooCommerce Quick View
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Quick_View' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-quick-view-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/quick-view.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-quick-view-style', 'rtl', 'replace' );
			}

			/**
			 * Checkout Add Ons
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Checkout_Add_Ons' ) ) {
				add_filter( 'restroom_sticky_order_review', '__return_false' );
			}

			/**
			 * PooCommerce Product Recommendations
			 */
			if ( $this->is_poocommerce_extension_activated( 'WC_Product_Recommendations' ) ) {
				wp_enqueue_style( 'restroom-poocommerce-product-recommendations-style', get_template_directory_uri() . '/assets/css/poocommerce/extensions/product-recommendations.css', 'restroom-poocommerce-style', $restroom_version );
				wp_style_add_data( 'restroom-poocommerce-product-recommendations-style', 'rtl', 'replace' );
			}
		}

		/**
		 * Get extension css.
		 *
		 * @see get_restroom_theme_mods()
		 * @return array $styles the css
		 */
		public function get_poocommerce_extension_css() {
			global $restroom;

			if ( ! is_object( $restroom ) ||
				! property_exists( $restroom, 'customizer' ) ||
				! is_a( $restroom->customizer, 'Restroom_Customizer' ) ||
				! method_exists( $restroom->customizer, 'get_restroom_theme_mods' ) ) {
				return apply_filters( 'restroom_customizer_poocommerce_extension_css', '' );
			}

			$restroom_theme_mods = $restroom->customizer->get_restroom_theme_mods();

			$poocommerce_extension_style = '';

			if ( $this->is_poocommerce_extension_activated( 'WC_Bookings' ) ) {
				$poocommerce_extension_style .= '
				.wc-bookings-date-picker .ui-datepicker td.bookable a {
					background-color: ' . $restroom_theme_mods['accent_color'] . ' !important;
				}

				.wc-bookings-date-picker .ui-datepicker td.bookable a.ui-state-default {
					background-color: ' . restroom_adjust_color_brightness( $restroom_theme_mods['accent_color'], -10 ) . ' !important;
				}

				.wc-bookings-date-picker .ui-datepicker td.bookable a.ui-state-active {
					background-color: ' . restroom_adjust_color_brightness( $restroom_theme_mods['accent_color'], -50 ) . ' !important;
				}
				';
			}

			if ( $this->is_poocommerce_extension_activated( 'WC_Product_Reviews_Pro' ) ) {
				$poocommerce_extension_style .= '
				.poocommerce #reviews .product-rating .product-rating-details table td.rating-graph .bar,
				.poocommerce-page #reviews .product-rating .product-rating-details table td.rating-graph .bar {
					background-color: ' . $restroom_theme_mods['text_color'] . ' !important;
				}

				.poocommerce #reviews .contribution-actions .feedback,
				.poocommerce-page #reviews .contribution-actions .feedback,
				.star-rating-selector:not(:checked) label.checkbox {
					color: ' . $restroom_theme_mods['text_color'] . ';
				}

				.poocommerce #reviews #comments ol.commentlist li .contribution-actions a,
				.poocommerce-page #reviews #comments ol.commentlist li .contribution-actions a,
				.star-rating-selector:not(:checked) input:checked ~ label.checkbox,
				.star-rating-selector:not(:checked) label.checkbox:hover ~ label.checkbox,
				.star-rating-selector:not(:checked) label.checkbox:hover,
				.poocommerce #reviews #comments ol.commentlist li .contribution-actions a,
				.poocommerce-page #reviews #comments ol.commentlist li .contribution-actions a,
				.poocommerce #reviews .form-contribution .attachment-type:not(:checked) label.checkbox:before,
				.poocommerce-page #reviews .form-contribution .attachment-type:not(:checked) label.checkbox:before {
					color: ' . $restroom_theme_mods['accent_color'] . ' !important;
				}';
			}

			if ( $this->is_poocommerce_extension_activated( 'WC_Smart_Coupons' ) ) {
				$poocommerce_extension_style .= '
				.coupon-container {
					background-color: ' . $restroom_theme_mods['button_background_color'] . ' !important;
				}

				.coupon-content {
					border-color: ' . $restroom_theme_mods['button_text_color'] . ' !important;
					color: ' . $restroom_theme_mods['button_text_color'] . ';
				}

				.sd-buttons-transparent.poocommerce .coupon-content,
				.sd-buttons-transparent.poocommerce-page .coupon-content {
					border-color: ' . $restroom_theme_mods['button_background_color'] . ' !important;
				}';
			}

			return apply_filters( 'restroom_customizer_poocommerce_extension_css', $poocommerce_extension_style );
		}

		/*
		|--------------------------------------------------------------------------
		| Integrations.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Sets up integrations.
		 *
		 * @since  2.3.4
		 *
		 * @return void
		 */
		public function setup_integrations() {

			if ( $this->is_poocommerce_extension_activated( 'WC_Bundles' ) ) {
				add_filter( 'poocommerce_bundled_table_item_js_enqueued', '__return_true' );
				add_filter( 'poocommerce_bundles_group_mode_options_data', array( $this, 'bundles_group_mode_options_data' ) );
			}

			if ( $this->is_poocommerce_extension_activated( 'WC_Composite_Products' ) ) {
				add_filter( 'poocommerce_composited_table_item_js_enqueued', '__return_true' );
				add_filter( 'poocommerce_display_composite_container_cart_item_data', '__return_true' );
			}
		}

		/**
		 * Add "Includes" meta to parent cart items.
		 * Displayed only on handheld/mobile screens.
		 *
		 * @since  2.3.4
		 *
		 * @param  array $group_mode_data Group mode data.
		 * @return array
		 */
		public function bundles_group_mode_options_data( $group_mode_data ) {
			$group_mode_data['parent']['features'][] = 'parent_cart_item_meta';

			return $group_mode_data;
		}
	}

endif;

return new Restroom_PooCommerce();
