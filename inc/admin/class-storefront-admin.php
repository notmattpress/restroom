<?php
/**
 * Restroom Admin Class
 *
 * @package  restroom
 * @since    2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Restroom_Admin' ) ) :
	/**
	 * The Restroom admin class
	 */
	class Restroom_Admin {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'welcome_register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'welcome_style' ) );
		}

		/**
		 * Load welcome screen css
		 *
		 * @param string $hook_suffix the current page hook suffix.
		 * @return void
		 * @since  1.4.4
		 */
		public function welcome_style( $hook_suffix ) {
			global $restroom_version;

			if ( 'appearance_page_restroom-welcome' === $hook_suffix ) {
				wp_enqueue_style( 'restroom-welcome-screen', get_template_directory_uri() . '/assets/css/admin/welcome-screen/welcome.css', array(), $restroom_version );
				wp_style_add_data( 'restroom-welcome-screen', 'rtl', 'replace' );
			}
		}

		/**
		 * Creates the dashboard page
		 *
		 * @see  add_theme_page()
		 * @since 1.0.0
		 */
		public function welcome_register_menu() {
			add_theme_page( 'Restroom', 'Restroom', 'activate_plugins', 'restroom-welcome', array( $this, 'restroom_welcome_screen' ) );
		}

		/**
		 * The welcome screen
		 *
		 * @since 1.0.0
		 */
		public function restroom_welcome_screen() {
			require_once ABSPATH . 'wp-load.php';
			require_once ABSPATH . 'wp-admin/admin.php';
			require_once ABSPATH . 'wp-admin/admin-header.php';

			global $restroom_version;

			$show_setup_screen = ( false === (bool) get_option( 'restroom_nux_dismissed' ) ) && ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '4.8.0', '>=' ) );
			?>

			<div class="restroom-wrap">
				<section class="restroom-welcome-nav">
					<span class="restroom-welcome-nav__version">Restroom <?php echo esc_attr( $restroom_version ); ?></span>
					<ul>
						<li><a href="https://wordpress.org/support/theme/restroom" target="_blank"><?php esc_html_e( 'Support', 'restroom' ); ?></a></li>
						<li><a href="https://poocommerce.com/documentation/themes/restroom/" target="_blank"><?php esc_html_e( 'Documentation', 'restroom' ); ?></a></li>
						<li><a href="https://developer.woo.com/category/release-post/restroom-theme-release-notes/" target="_blank"><?php esc_html_e( 'Development blog', 'restroom' ); ?></a></li>
					</ul>
				</section>

				<div class="restroom-logo">
					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/restroom-icon.svg" alt="Restroom" />
				</div>

				<div class="restroom-intro">
					<?php
					if ( $show_setup_screen ) {
						?>
						<div class="restroom-intro-setup">
							<?php
							Restroom_NUX_Admin::admin_notices_content();
							?>
						</div>
						<?php
						echo '<div class="restroom-intro-message" style="display:none">';
					}

					/**
					 * Display a different message when the user visits this page when returning from the guided tour
					 */
					$referrer = wp_get_referer();

					if ( strpos( $referrer, 'sf_starter_content' ) !== false ) {
						/* translators: 1: HTML, 2: HTML */
						echo '<h1>' . sprintf( esc_attr__( 'Setup complete %1$sYour Restroom adventure begins now 🚀%2$s ', 'restroom' ), '<span>', '</span>' ) . '</h1>';
						echo '<p>' . esc_attr__( 'One more thing... You might be interested in the following Restroom extensions and designs.', 'restroom' ) . '</p>';
					} else {
						echo '<p>' . esc_attr__( 'Hello! You might be interested in the following Restroom extensions and designs.', 'restroom' ) . '</p>';
					}

					if ( $show_setup_screen ) {
						echo '</div>';
					}
					?>
				</div>

				<div class="restroom-enhance">
					<div class="restroom-enhance__column restroom-bundle">
						<h3><?php esc_html_e( 'Restroom Extensions Bundle', 'restroom' ); ?></h3>
						<span class="bundle-image">
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/welcome-screen/restroom-bundle-hero.png" alt="Restroom Extensions Hero" />
						</span>

						<p>
							<?php esc_html_e( 'All the tools you\'ll need to define your style and customize Restroom.', 'restroom' ); ?>
						</p>

						<p>
							<?php esc_html_e( 'Make it yours without touching code with the Restroom Extensions bundle. Express yourself, optimize conversions, delight customers.', 'restroom' ); ?>
						</p>


						<p>
							<a href="https://poocommerce.com/products/restroom-extensions-bundle/?utm_source=restroom&utm_medium=product&utm_campaign=restroomaddons" class="restroom-button" target="_blank"><?php esc_html_e( 'Read more and purchase', 'restroom' ); ?></a>
						</p>
					</div>
					<div class="restroom-enhance__column restroom-child-themes">
						<h3><?php esc_html_e( 'Alternate designs', 'restroom' ); ?></h3>
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/admin/welcome-screen/child-themes.jpg" alt="Restroom Powerpack" />

						<p>
							<?php esc_html_e( 'Quickly and easily transform your shops appearance with Restroom child themes.', 'restroom' ); ?>
						</p>

						<p>
							<?php esc_html_e( 'Each has been designed to serve a different industry - from fashion to food.', 'restroom' ); ?>
						</p>

						<p>
							<?php esc_html_e( 'Of course they are all fully compatible with each Restroom extension.', 'restroom' ); ?>
						</p>

						<p>
							<a href="https://poocommerce.com/documentation/products/themes/restroom/child-themes/?utm_source=restroom&utm_medium=product&utm_campaign=restroomaddons" class="restroom-button" target="_blank"><?php esc_html_e( 'Check \'em out', 'restroom' ); ?></a>
						</p>
					</div>
				</div>

				<div class="automattic">
					<p>
					<?php
						/* translators: %s: Automattic branding */
						printf( esc_html__( 'An %s project', 'restroom' ), '<a href="https://automattic.com/"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/admin/welcome-screen/automattic.png" alt="Automattic" /></a>' );
					?>
					</p>
				</div>
			</div>
			<?php
		}

		/**
		 * Welcome screen intro
		 *
		 * @since 1.0.0
		 */
		public function welcome_intro() {
			require_once get_template_directory() . '/inc/admin/welcome-screen/component-intro.php';
		}

		/**
		 * Output a button that will install or activate a plugin if it doesn't exist, or display a disabled button if the
		 * plugin is already activated.
		 *
		 * @param string $plugin_slug The plugin slug.
		 * @param string $plugin_file The plugin file.
		 */
		public function install_plugin_button( $plugin_slug, $plugin_file ) {
			if ( current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) ) {
				if ( is_plugin_active( $plugin_slug . '/' . $plugin_file ) ) {
					// The plugin is already active.
					$button = array(
						'message' => esc_attr__( 'Activated', 'restroom' ),
						'url'     => '#',
						'classes' => 'disabled',
					);
				} elseif ( $this->is_plugin_installed( $plugin_slug ) ) {
					$url = $this->is_plugin_installed( $plugin_slug );

					// The plugin exists but isn't activated yet.
					$button = array(
						'message' => esc_attr__( 'Activate', 'restroom' ),
						'url'     => $url,
						'classes' => 'activate-now',
					);
				} else {
					// The plugin doesn't exist.
					$url    = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'install-plugin',
								'plugin' => $plugin_slug,
							),
							self_admin_url( 'update.php' )
						),
						'install-plugin_' . $plugin_slug
					);
					$button = array(
						'message' => esc_attr__( 'Install now', 'restroom' ),
						'url'     => $url,
						'classes' => ' install-now install-' . $plugin_slug,
					);
				}
				?>
				<a href="<?php echo esc_url( $button['url'] ); ?>" class="restroom-button <?php echo esc_attr( $button['classes'] ); ?>" data-originaltext="<?php echo esc_attr( $button['message'] ); ?>" data-slug="<?php echo esc_attr( $plugin_slug ); ?>" aria-label="<?php echo esc_attr( $button['message'] ); ?>"><?php echo esc_html( $button['message'] ); ?></a>
				<a href="https://wordpress.org/plugins/<?php echo esc_attr( $plugin_slug ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'restroom' ); ?></a>
				<?php
			}
		}

		/**
		 * Check if a plugin is installed and return the url to activate it if so.
		 *
		 * @param string $plugin_slug The plugin slug.
		 */
		private function is_plugin_installed( $plugin_slug ) {
			if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
				$plugins = get_plugins( '/' . $plugin_slug );
				if ( ! empty( $plugins ) ) {
					$keys        = array_keys( $plugins );
					$plugin_file = $plugin_slug . '/' . $keys[0];
					$url         = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'activate',
								'plugin' => $plugin_file,
							),
							admin_url( 'plugins.php' )
						),
						'activate-plugin_' . $plugin_file
					);
					return $url;
				}
			}
			return false;
		}
		/**
		 * Welcome screen enhance section
		 *
		 * @since 1.5.2
		 */
		public function welcome_enhance() {
			require_once get_template_directory() . '/inc/admin/welcome-screen/component-enhance.php';
		}

		/**
		 * Welcome screen contribute section
		 *
		 * @since 1.5.2
		 */
		public function welcome_contribute() {
			require_once get_template_directory() . '/inc/admin/welcome-screen/component-contribute.php';
		}

		/**
		 * Get product data from json
		 *
		 * @param  string $url       URL to the json file.
		 * @param  string $transient Name the transient.
		 * @return [type]            [description]
		 */
		public function get_restroom_product_data( $url, $transient ) {
			$raw_products = wp_safe_remote_get( $url );
			$products     = json_decode( wp_remote_retrieve_body( $raw_products ) );

			if ( ! empty( $products ) ) {
				set_transient( $transient, $products, DAY_IN_SECONDS );
			}

			return $products;
		}
	}

endif;

return new Restroom_Admin();
