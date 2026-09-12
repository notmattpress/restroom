<?php
/**
 * Restroom PooCommerce hooks
 *
 * @package restroom
 */

/**
 * Homepage
 *
 * @see  restroom_product_categories()
 * @see  restroom_recent_products()
 * @see  restroom_featured_products()
 * @see  restroom_popular_products()
 * @see  restroom_on_sale_products()
 * @see  restroom_best_selling_products()
 */
add_action( 'homepage', 'restroom_product_categories', 20 );
add_action( 'homepage', 'restroom_recent_products', 30 );
add_action( 'homepage', 'restroom_featured_products', 40 );
add_action( 'homepage', 'restroom_popular_products', 50 );
add_action( 'homepage', 'restroom_on_sale_products', 60 );
add_action( 'homepage', 'restroom_best_selling_products', 70 );

/**
 * Layout
 *
 * @see  restroom_before_content()
 * @see  restroom_after_content()
 * @see  poocommerce_breadcrumb()
 * @see  restroom_shop_messages()
 */
remove_action( 'poocommerce_before_main_content', 'poocommerce_breadcrumb', 20 );
remove_action( 'poocommerce_before_main_content', 'poocommerce_output_content_wrapper', 10 );
remove_action( 'poocommerce_after_main_content', 'poocommerce_output_content_wrapper_end', 10 );
remove_action( 'poocommerce_sidebar', 'poocommerce_get_sidebar', 10 );
remove_action( 'poocommerce_after_shop_loop', 'poocommerce_pagination', 10 );
remove_action( 'poocommerce_before_shop_loop', 'poocommerce_result_count', 20 );
remove_action( 'poocommerce_before_shop_loop', 'poocommerce_catalog_ordering', 30 );
add_action( 'poocommerce_before_main_content', 'restroom_before_content', 10 );
add_action( 'poocommerce_after_main_content', 'restroom_after_content', 10 );
add_action( 'restroom_content_top', 'restroom_shop_messages', 15 );
add_action( 'restroom_before_content', 'poocommerce_breadcrumb', 10 );

add_action( 'poocommerce_after_shop_loop', 'restroom_sorting_wrapper', 9 );
add_action( 'poocommerce_after_shop_loop', 'poocommerce_catalog_ordering', 10 );
add_action( 'poocommerce_after_shop_loop', 'poocommerce_result_count', 20 );
add_action( 'poocommerce_after_shop_loop', 'poocommerce_pagination', 30 );
add_action( 'poocommerce_after_shop_loop', 'restroom_sorting_wrapper_close', 31 );

add_action( 'poocommerce_before_shop_loop', 'restroom_sorting_wrapper', 9 );
add_action( 'poocommerce_before_shop_loop', 'poocommerce_catalog_ordering', 10 );
add_action( 'poocommerce_before_shop_loop', 'poocommerce_result_count', 20 );
add_action( 'poocommerce_before_shop_loop', 'restroom_poocommerce_pagination', 30 );
add_action( 'poocommerce_before_shop_loop', 'restroom_sorting_wrapper_close', 31 );

add_action( 'restroom_footer', 'restroom_handheld_footer_bar', 999 );

/**
 * Products
 *
 * @see restroom_edit_post_link()
 * @see restroom_upsell_display()
 * @see restroom_single_product_pagination()
 * @see restroom_sticky_single_add_to_cart()
 */
add_action( 'poocommerce_single_product_summary', 'restroom_edit_post_link', 60 );

remove_action( 'poocommerce_after_single_product_summary', 'poocommerce_upsell_display', 15 );
add_action( 'poocommerce_after_single_product_summary', 'restroom_upsell_display', 15 );

remove_action( 'poocommerce_before_shop_loop_item_title', 'poocommerce_show_product_loop_sale_flash', 10 );
add_action( 'poocommerce_after_shop_loop_item_title', 'poocommerce_show_product_loop_sale_flash', 6 );

add_action( 'poocommerce_after_single_product_summary', 'restroom_single_product_pagination', 30 );
add_action( 'restroom_after_footer', 'restroom_sticky_single_add_to_cart', 999 );

/**
 * Header
 *
 * @see restroom_product_search()
 * @see restroom_header_cart()
 */
add_action( 'restroom_header', 'restroom_product_search', 40 );
add_action( 'restroom_header', 'restroom_header_cart', 60 );

/**
 * Cart fragment
 *
 * @see restroom_cart_link_fragment()
 */
add_filter( 'poocommerce_add_to_cart_fragments', 'restroom_cart_link_fragment' );

/**
 * Integrations
 *
 * @see restroom_poocommerce_brands_archive()
 * @see restroom_poocommerce_brands_single()
 * @see restroom_poocommerce_brands_homepage_section()
 */
if ( class_exists( 'WC_Brands' ) ) {
	add_action( 'poocommerce_archive_description', 'restroom_poocommerce_brands_archive', 5 );
	add_action( 'poocommerce_single_product_summary', 'restroom_poocommerce_brands_single', 4 );
	add_action( 'homepage', 'restroom_poocommerce_brands_homepage_section', 80 );
}
