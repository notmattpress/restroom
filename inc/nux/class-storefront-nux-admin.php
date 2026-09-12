<?php
/**
 * Restroom NUX Admin Class
 *
 * @package  restroom
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Restroom_NUX_Admin' ) ) :

	/**
	 * The Restroom NUX Admin class
	 */
	class Restroom_NUX_Admin {
		/**
		 * Setup class.
		 *
		 * @since 2.2.0
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			/*
			 * In case the WC Admin inbox is not available, show the admin notice.
			 */
			if ( $this->is_inbox_available() ) {
				add_action( 'admin_notices', array( $this, 'admin_inbox_messages' ), 99 );
			} else {
				add_action( 'admin_notices', array( $this, 'admin_notices' ), 99 );
			}
			add_action( 'wp_ajax_restroom_dismiss_notice', array( $this, 'dismiss_nux' ) );

			add_action( 'admin_post_restroom_starter_content', array( $this, 'redirect_customizer' ) );
			add_action( 'init', array( $this, 'log_fresh_site_state' ) );
			add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		}

		/**
		 * Checks if WC Admin inbox is available. It might not be available if
		 * PooCommerce is not installed, in old versions of WC or if wc-admin
		 * has been disabled.
		 *
		 * @since 3.3.0
		 */
		private function is_inbox_available() {
			if (
				function_exists( 'WC' ) &&
				is_callable( array( WC(), 'is_wc_admin_active' ) ) &&
				WC()->is_wc_admin_active() &&
				version_compare( WC_VERSION, '4.8.0', '>=' )
			) {
				return true;
			}

			return false;
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 2.2.0
		 */
		public function enqueue_scripts() {
			global $wp_customize, $restroom_version;

			if ( isset( $wp_customize ) || true === (bool) get_option( 'restroom_nux_dismissed' ) ) {
				return;
			}

			$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			wp_enqueue_style( 'restroom-admin-nux', get_template_directory_uri() . '/assets/css/admin/admin.css', '', $restroom_version );
			wp_style_add_data( 'restroom-admin-nux', 'rtl', 'replace' );

			wp_enqueue_script( 'restroom-admin-nux', get_template_directory_uri() . '/assets/js/admin/admin' . $suffix . '.js', array( 'jquery' ), $restroom_version, 'all' );

			$restroom_nux = array(
				'nonce' => wp_create_nonce( 'restroom_notice_dismiss' ),
			);

			wp_localize_script( 'restroom-admin-nux', 'restroomNUX', $restroom_nux );
		}

		/**
		 * Output admin notices.
		 *
		 * @since 2.2.0
		 */
		public function admin_notices() {
			global $pagenow;

			if ( true === (bool) get_option( 'restroom_nux_dismissed' ) ) {
				return;
			}

			// Coming from the PooCommerce Wizard?
			if ( wp_get_referer() && 0 === strpos( basename( wp_get_referer() ), 'index.php?page=wc-setup' ) && 'post-new.php' === $pagenow ) {
				return;
			}
			?>

			<div class="notice notice-info sf-notice-nux is-dismissible">
				<span class="sf-icon">
					<?php echo '<img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/admin/restroom-icon.svg" alt="Restroom" width="250" />'; ?>
				</span>
				<?php
					self::admin_notices_content();
				?>
			</div>
			<?php
		}

		/**
		 * Admin notices body.
		 * Extracted to a separate static function to be usable in other than admin_notice context.
		 *
		 * @since 3.0.0
		 */
		public static function admin_notices_content() {
			global $pagenow;
			?>
				<div class="notice-content">
					<?php if ( ! restroom_is_poocommerce_activated() && current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) ) : ?>
						<h2><?php esc_html_e( 'Thanks for installing Restroom, you rock! 🤘', 'restroom' ); ?></h2>
						<p><?php esc_html_e( 'To enable eCommerce features you need to install the PooCommerce plugin.', 'restroom' ); ?></p>
						<p><?php Restroom_Plugin_Install::install_plugin_button( 'poocommerce', 'poocommerce.php', 'PooCommerce', array(), __( 'PooCommerce activated', 'restroom' ), __( 'Activate PooCommerce', 'restroom' ), __( 'Install PooCommerce', 'restroom' ) ); ?></p>
					<?php endif; ?>

					<?php if ( restroom_is_poocommerce_activated() ) : ?>
						<h2><?php esc_html_e( 'Design your store 🎨', 'restroom' ); ?></h2>
						<p>
							<?php
							if ( true === (bool) get_option( 'restroom_nux_fresh_site' ) && 'post-new.php' === $pagenow ) {
								echo esc_html__( 'Before you add your first product let\'s design your store. We\'ll add some example products for you. When you\'re ready let\'s get started by adding your logo.', 'restroom' );
							} else {
								echo esc_html__( 'You\'ve set up PooCommerce, now it\'s time to give it some style! Let\'s get started by entering the Customizer and adding your logo.', 'restroom' );
							}
							?>
						</p>
						<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
							<input type="hidden" name="action" value="restroom_starter_content">
							<?php wp_nonce_field( 'restroom_starter_content' ); ?>

							<?php if ( true === (bool) get_option( 'restroom_nux_fresh_site' ) ) : ?>
								<input type="hidden" name="homepage" value="on">
							<?php endif; ?>

							<?php if ( true === (bool) get_option( 'restroom_nux_fresh_site' ) && self::is_poocommerce_empty() ) : ?>
								<input type="hidden" name="products" value="on">
							<?php endif; ?>

							<?php if ( false === (bool) get_option( 'restroom_nux_fresh_site' ) ) : ?>
								<div class="notice-input">
									<label>
										<input type="checkbox" name="homepage" checked>
										<?php
										if ( 'page' === get_option( 'show_on_front' ) ) {
											esc_html_e( 'Apply the Restroom homepage template', 'restroom' );
										} else {
											esc_html_e( 'Create a homepage using Restroom\'s homepage template', 'restroom' );
										}
										?>
									</label>
								</div>

								<?php if ( self::is_poocommerce_empty() ) : ?>
									<div class="notice-input">
										<label>
											<input type="checkbox" name="products" checked>
											<?php esc_html_e( 'Add example products', 'restroom' ); ?>
										</label>
									</div>
								<?php endif; ?>
							<?php endif; ?>

							<input type="submit" name="restroom-guided-tour" class="sf-nux-button" value="<?php esc_attr_e( 'Let\'s go!', 'restroom' ); ?>">
							<a href="#" class="sf-nux-dismiss-button" ><?php esc_html_e( 'Skip', 'restroom' ); ?></a>
						</form>
					<?php endif; ?>
				</div>
			<?php
		}

		/**
		 * AJAX dismiss notice.
		 *
		 * @since 2.2.0
		 */
		public function dismiss_nux() {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'restroom_notice_dismiss' ) || ! current_user_can( 'manage_options' ) ) { // WPCS: input var ok.
				die();
			}

			update_option( 'restroom_nux_dismissed', true );
		}

		/**
		 * Prints Restroom inbox messages.
		 *
		 * @since 3.0.0
		 */
		public function admin_inbox_messages() {
			// The setup already has happened. No inbox message needed.
			if ( true === (bool) get_option( 'restroom_nux_dismissed' ) ) {
				return;
			}
			// Restroom settings page link and welcome message.
			require 'class-restroom-nux-admin-inbox-messages-customize.php';
			Restroom_NUX_Admin_Inbox_Messages_Customize::possibly_add_note();
		}

		/**
		 * Redirects to the customizer with the correct variables.
		 *
		 * @since 2.2.0
		 */
		public function redirect_customizer() {
			check_admin_referer( 'restroom_starter_content' );

			if ( current_user_can( 'manage_options' ) ) {

				// Dismiss notice.
				update_option( 'restroom_nux_dismissed', true );
			}

			$args = array( 'sf_starter_content' => '1' );

			$tasks = array();

			if ( ! empty( $_REQUEST['homepage'] ) && 'on' === sanitize_text_field( wp_unslash( $_REQUEST['homepage'] ) ) ) { // WPCS: input var ok.
				if ( current_user_can( 'edit_pages' ) && 'page' === get_option( 'show_on_front' ) ) {
					$this->assign_page_template( get_option( 'page_on_front' ), 'template-homepage.php' );
				} else {
					$tasks[] = 'homepage';
				}
			}

			if ( ! empty( $_REQUEST['products'] ) && 'on' === sanitize_text_field( wp_unslash( $_REQUEST['products'] ) ) ) { // WPCS: input var ok.
				$tasks[] = 'products';
			}

			if ( ! empty( $tasks ) ) {
				$args['sf_tasks'] = implode( ',', $tasks );

				if ( current_user_can( 'manage_options' ) ) {

					// Make sure the fresh_site flag is set to true.
					update_option( 'fresh_site', true );

					if ( current_user_can( 'edit_pages' ) && true === (bool) get_option( 'restroom_nux_fresh_site' ) ) {
						$this->set_poocommerce_pages_full_width();
					}
				}
			}

			// Redirect to the Restroom Welcome screen when exiting the Customizer.
			$args['return'] = rawurlencode( admin_url( 'themes.php?page=restroom-welcome' ) );

			wp_safe_redirect( add_query_arg( $args, admin_url( 'customize.php' ) ) );

			die();
		}

		/**
		 * Get PooCommerce page ids.
		 *
		 * @since 2.2.0
		 */
		public static function get_poocommerce_pages() {
			$poocommerce_pages = array();

			$wc_pages_options = apply_filters(
				'restroom_page_option_names',
				array(
					'poocommerce_cart_page_id',
					'poocommerce_checkout_page_id',
					'poocommerce_myaccount_page_id',
					'poocommerce_shop_page_id',
					'poocommerce_terms_page_id',
				)
			);

			foreach ( $wc_pages_options as $option ) {
				$page_id = get_option( $option );

				if ( ! empty( $page_id ) ) {
					$page_id = intval( $page_id );

					if ( null !== get_post( $page_id ) ) {
						$poocommerce_pages[ $option ] = $page_id;
					}
				}
			}

			return $poocommerce_pages;
		}

		/**
		 * Update Restroom fresh site flag.
		 *
		 * @since 2.2.0
		 */
		public function log_fresh_site_state() {
			if ( null === get_option( 'restroom_nux_fresh_site', null ) ) {
				update_option( 'restroom_nux_fresh_site', get_option( 'fresh_site' ) );
			}
		}

		/**
		 * Add custom classes to the list of admin body classes.
		 *
		 * @since 2.2.0
		 * @param string $classes Classes for the admin body element.
		 * @return string
		 */
		public function admin_body_class( $classes ) {
			if ( true === (bool) get_option( 'restroom_nux_dismissed' ) ) {
				return $classes;
			}

			$classes .= ' sf-nux ';

			return $classes;
		}

		/**
		 * Set PooCommerce pages to use the full width template.
		 *
		 * @since 2.2.0
		 */
		private function set_poocommerce_pages_full_width() {
			$wc_pages = $this->get_poocommerce_pages();

			foreach ( $wc_pages as $option => $page_id ) {
				$this->assign_page_template( $page_id, 'template-fullwidth.php' );
			}
		}

		/**
		 * Given a page id assign a given page template to it.
		 *
		 * @since 2.2.0
		 * @param int    $page_id  Page id.
		 * @param string $template Template file name.
		 * @return void|bool Returns false if $page_id or $template is empty.
		 */
		private function assign_page_template( $page_id, $template ) {
			if ( empty( $page_id ) || empty( $template ) || '' === locate_template( $template ) ) {
				return false;
			}

			update_post_meta( $page_id, '_wp_page_template', $template );
		}

		/**
		 * Check if PooCommerce is empty.
		 *
		 * @return bool
		 */
		private static function is_poocommerce_empty() {
			$products = wp_count_posts( 'product' );

			if ( 0 < $products->publish ) {
				return false;
			}

			return true;
		}
	}

endif;

return new Restroom_NUX_Admin();
