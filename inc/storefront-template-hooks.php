<?php
/**
 * Restroom hooks
 *
 * @package restroom
 */

/**
 * General
 *
 * @see  restroom_header_widget_region()
 * @see  restroom_get_sidebar()
 */
add_action( 'restroom_before_content', 'restroom_header_widget_region', 10 );
add_action( 'restroom_sidebar', 'restroom_get_sidebar', 10 );

/**
 * Header
 *
 * @see  restroom_skip_links()
 * @see  restroom_secondary_navigation()
 * @see  restroom_site_branding()
 * @see  restroom_primary_navigation()
 */
add_action( 'restroom_header', 'restroom_header_container', 0 );
add_action( 'restroom_header', 'restroom_skip_links', 5 );
add_action( 'restroom_header', 'restroom_site_branding', 20 );
add_action( 'restroom_header', 'restroom_secondary_navigation', 30 );
add_action( 'restroom_header', 'restroom_header_container_close', 41 );
add_action( 'restroom_header', 'restroom_primary_navigation_wrapper', 42 );
add_action( 'restroom_header', 'restroom_primary_navigation', 50 );
add_action( 'restroom_header', 'restroom_primary_navigation_wrapper_close', 68 );

/**
 * Footer
 *
 * @see  restroom_footer_widgets()
 * @see  restroom_credit()
 */
add_action( 'restroom_footer', 'restroom_footer_widgets', 10 );
add_action( 'restroom_footer', 'restroom_credit', 20 );

/**
 * Homepage
 *
 * @see  restroom_homepage_content()
 */
add_action( 'homepage', 'restroom_homepage_content', 10 );

/**
 * Posts
 *
 * @see  restroom_post_header()
 * @see  restroom_post_meta()
 * @see  restroom_post_content()
 * @see  restroom_paging_nav()
 * @see  restroom_single_post_header()
 * @see  restroom_post_nav()
 * @see  restroom_display_comments()
 */
add_action( 'restroom_loop_post', 'restroom_post_header', 10 );
add_action( 'restroom_loop_post', 'restroom_post_content', 30 );
add_action( 'restroom_loop_post', 'restroom_post_taxonomy', 40 );
add_action( 'restroom_loop_after', 'restroom_paging_nav', 10 );
add_action( 'restroom_single_post', 'restroom_post_header', 10 );
add_action( 'restroom_single_post', 'restroom_post_content', 30 );
add_action( 'restroom_single_post_bottom', 'restroom_edit_post_link', 5 );
add_action( 'restroom_single_post_bottom', 'restroom_post_taxonomy', 5 );
add_action( 'restroom_single_post_bottom', 'restroom_post_nav', 10 );
add_action( 'restroom_single_post_bottom', 'restroom_display_comments', 20 );
add_action( 'restroom_post_header_before', 'restroom_post_meta', 10 );
add_action( 'restroom_post_content_before', 'restroom_post_thumbnail', 10 );

/**
 * Pages
 *
 * @see  restroom_page_header()
 * @see  restroom_page_content()
 * @see  restroom_display_comments()
 */
add_action( 'restroom_page', 'restroom_page_header', 10 );
add_action( 'restroom_page', 'restroom_page_content', 20 );
add_action( 'restroom_page', 'restroom_edit_post_link', 30 );
add_action( 'restroom_page_after', 'restroom_display_comments', 10 );

/**
 * Homepage Page Template
 *
 * @see  restroom_homepage_header()
 * @see  restroom_page_content()
 */
add_action( 'restroom_homepage', 'restroom_homepage_header', 10 );
add_action( 'restroom_homepage', 'restroom_page_content', 20 );
