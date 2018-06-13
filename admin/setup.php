<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAINWP_Backend_Setup' ) ) {

	final class GAINWP_Backend_Setup {

		private $gacwp;

		public function __construct() {
			$this->gacwp = GAINWP();

			// Styles & Scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
			// Site Menu
			add_action( 'admin_menu', array( $this, 'site_menu' ) );
			// Network Menu
			add_action( 'network_admin_menu', array( $this, 'network_menu' ) );
			// Settings link
			add_filter( "plugin_action_links_" . plugin_basename( GAINWP_DIR . 'gacwp.php' ), array( $this, 'settings_link' ) );
			// Updated admin notice
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		}

		/**
		 * Add Site Menu
		 */
		public function site_menu() {
			global $wp_version;
			if ( current_user_can( 'manage_options' ) ) {
				include ( GAINWP_DIR . 'admin/settings.php' );
				add_menu_page( __( "Google Analytics", 'google-analytics-in-wp' ), __( "Google Analytics", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_settings', array( 'GAINWP_Settings', 'general_settings' ), version_compare( $wp_version, '3.8.0', '>=' ) ? 'dashicons-chart-area' : GAINWP_URL . 'admin/images/gacwp-icon.png' );
				add_submenu_page( 'gacwp_settings', __( "General Settings", 'google-analytics-in-wp' ), __( "General Settings", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_settings', array( 'GAINWP_Settings', 'general_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Tracking Settings", 'google-analytics-in-wp' ), __( "Tracking Settings", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_tracking_settings', array( 'GAINWP_Settings', 'tracking_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Reporting Settings", 'google-analytics-in-wp' ), __( "Reporting Settings", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_report_settings', array( 'GAINWP_Settings', 'reporting_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Errors & Debug", 'google-analytics-in-wp' ), __( "Errors & Debug", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_errors_debugging', array( 'GAINWP_Settings', 'errors_debugging' ) );
				/*
				add_submenu_page( 'gacwp_settings', __( "General Settings", 'google-analytics-in-wp' ), __( "General Settings", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_settings', array( 'GAINWP_Settings', 'general_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Backend Settings", 'google-analytics-in-wp' ), __( "Backend Settings", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_backend_settings', array( 'GAINWP_Settings', 'backend_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Frontend Settings", 'google-analytics-in-wp' ), __( "Frontend Settings", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_frontend_settings', array( 'GAINWP_Settings', 'frontend_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Tracking Settings", 'google-analytics-in-wp' ), __( "Tracking Code", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_tracking_settings', array( 'GAINWP_Settings', 'tracking_settings' ) );
				add_submenu_page( 'gacwp_settings', __( "Errors & Debug", 'google-analytics-in-wp' ), __( "Errors & Debug", 'google-analytics-in-wp' ), 'manage_options', 'gacwp_errors_debugging', array( 'GAINWP_Settings', 'errors_debugging' ) );
				*/
			}
		}

		/**
		 * Add Network Menu
		 */
		public function network_menu() {
			global $wp_version;
			if ( current_user_can( 'manage_network' ) ) {
				include ( GAINWP_DIR . 'admin/settings.php' );
				add_menu_page( __( "Google Analytics", 'google-analytics-in-wp' ), "Google Analytics", 'manage_network', 'gacwp_settings', array( 'GAINWP_Settings', 'general_settings_network' ), version_compare( $wp_version, '3.8.0', '>=' ) ? 'dashicons-chart-area' : GAINWP_URL . 'admin/images/gacwp-icon.png' );
				add_submenu_page( 'gacwp_settings', __( "General Settings", 'google-analytics-in-wp' ), __( "General Settings", 'google-analytics-in-wp' ), 'manage_network', 'gacwp_settings', array( 'GAINWP_Settings', 'general_settings_network' ) );
				add_submenu_page( 'gacwp_settings', __( "Errors & Debug", 'google-analytics-in-wp' ), __( "Errors & Debug", 'google-analytics-in-wp' ), 'manage_network', 'gacwp_errors_debugging', array( 'GAINWP_Settings', 'errors_debugging' ) );
			}
		}

		/**
		 * Styles & Scripts conditional loading (based on current URI)
		 *
		 * @param
		 *            $hook
		 */
		public function load_styles_scripts( $hook ) {
			$new_hook = explode( '_page_', $hook );

			if ( isset( $new_hook[1] ) ) {
				$new_hook = '_page_' . $new_hook[1];
			} else {
				$new_hook = $hook;
			}

			/*
			 * GAINWP main stylesheet
			 */
			wp_enqueue_style( 'gacwp', GAINWP_URL . 'admin/css/gacwp.css', null, GAINWP_CURRENT_VERSION );

			/*
			 * GAINWP UI
			 */

			if ( GAINWP_Tools::get_cache( 'gapi_errors' ) ) {
				$ed_bubble = '!';
			} else {
				$ed_bubble = '';
			}

			wp_enqueue_script( 'gacwp-backend-ui', plugins_url( 'js/ui.js', __FILE__ ), array( 'jquery' ), GAINWP_CURRENT_VERSION, true );

			/* @formatter:off */
			wp_localize_script( 'gacwp-backend-ui', 'gacwp_ui_data', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'gacwp_dismiss_notices' ),
				'ed_bubble' => $ed_bubble,
			)
			);
			/* @formatter:on */

			if ( $this->gacwp->config->options['switch_profile'] && count( $this->gacwp->config->options['ga_profiles_list'] ) > 1 ) {
				$views = array();
				foreach ( $this->gacwp->config->options['ga_profiles_list'] as $items ) {
					if ( $items[3] ) {
						$views[$items[1]] = esc_js( GAINWP_Tools::strip_protocol( $items[3] ) ); // . ' &#8658; ' . $items[0] );
					}
				}
			} else {
				$views = false;
			}

			/*
			 * Main Dashboard Widgets Styles & Scripts
			 */
			$widgets_hooks = array( 'index.php' );

			if ( in_array( $new_hook, $widgets_hooks ) ) {
				if ( GAINWP_Tools::check_roles( $this->gacwp->config->options['access_back'] ) && $this->gacwp->config->options['dashboard_widget'] ) {

					if ( $this->gacwp->config->options['ga_target_geomap'] ) {
						$country_codes = GAINWP_Tools::get_countrycodes();
						if ( isset( $country_codes[$this->gacwp->config->options['ga_target_geomap']] ) ) {
							$region = $this->gacwp->config->options['ga_target_geomap'];
						} else {
							$region = false;
						}
					} else {
						$region = false;
					}

					wp_enqueue_style( 'gacwp-nprogress', GAINWP_URL . 'common/nprogress/nprogress.css', null, GAINWP_CURRENT_VERSION );

					wp_enqueue_style( 'gacwp-backend-item-reports', GAINWP_URL . 'admin/css/admin-widgets.css', null, GAINWP_CURRENT_VERSION );

					wp_register_style( 'jquery-ui-tooltip-html', GAINWP_URL . 'common/realtime/jquery.ui.tooltip.html.css' );

					wp_enqueue_style( 'jquery-ui-tooltip-html' );

					wp_register_script( 'jquery-ui-tooltip-html', GAINWP_URL . 'common/realtime/jquery.ui.tooltip.html.js' );

					wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

					wp_enqueue_script( 'gacwp-nprogress', GAINWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GAINWP_CURRENT_VERSION );

					wp_enqueue_script( 'gacwp-backend-dashboard-reports', GAINWP_URL . 'common/js/reports5.js', array( 'jquery', 'googlecharts', 'gacwp-nprogress', 'jquery-ui-tooltip', 'jquery-ui-core', 'jquery-ui-position', 'jquery-ui-tooltip-html' ), GAINWP_CURRENT_VERSION, true );

					/* @formatter:off */

					$datelist = array(
						'realtime' => __( "Real-Time", 'google-analytics-in-wp' ),
						'today' => __( "Today", 'google-analytics-in-wp' ),
						'yesterday' => __( "Yesterday", 'google-analytics-in-wp' ),
						'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 7 ),
						'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 14 ),
						'30daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 30 ),
						'90daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 90 ),
						'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-in-wp' ), __('One', 'google-analytics-in-wp') ),
						'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-in-wp' ), __('Three', 'google-analytics-in-wp') ),
					);


					if ( $this->gacwp->config->options['user_api'] && ! $this->gacwp->config->options['backend_realtime_report'] ) {
						array_shift( $datelist );
					}

					wp_localize_script( 'gacwp-backend-dashboard-reports', 'gacwpItemData', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( 'gacwp_backend_item_reports' ),
						'dateList' => $datelist,
						'reportList' => array(
							'sessions' => __( "Sessions", 'google-analytics-in-wp' ),
							'users' => __( "Users", 'google-analytics-in-wp' ),
							'organicSearches' => __( "Organic", 'google-analytics-in-wp' ),
							'pageviews' => __( "Page Views", 'google-analytics-in-wp' ),
							'visitBounceRate' => __( "Bounce Rate", 'google-analytics-in-wp' ),
							'locations' => __( "Location", 'google-analytics-in-wp' ),
							'contentpages' =>  __( "Pages", 'google-analytics-in-wp' ),
							'referrers' => __( "Referrers", 'google-analytics-in-wp' ),
							'searches' => __( "Searches", 'google-analytics-in-wp' ),
							'trafficdetails' => __( "Traffic", 'google-analytics-in-wp' ),
							'technologydetails' => __( "Technology", 'google-analytics-in-wp' ),
							'404errors' => __( "404 Errors", 'google-analytics-in-wp' ),
						),
						'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-in-wp' ), //0
							__( "Traffic Mediums", 'google-analytics-in-wp' ),
							__( "Visitor Type", 'google-analytics-in-wp' ),
							__( "Search Engines", 'google-analytics-in-wp' ),
							__( "Social Networks", 'google-analytics-in-wp' ),
							__( "Sessions", 'google-analytics-in-wp' ),
							__( "Users", 'google-analytics-in-wp' ),
							__( "Page Views", 'google-analytics-in-wp' ),
							__( "Bounce Rate", 'google-analytics-in-wp' ),
							__( "Organic Search", 'google-analytics-in-wp' ),
							__( "Pages/Session", 'google-analytics-in-wp' ),
							__( "Invalid response", 'google-analytics-in-wp' ),
							__( "No Data", 'google-analytics-in-wp' ),
							__( "This report is unavailable", 'google-analytics-in-wp' ),
							__( "report generated by", 'google-analytics-in-wp' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-in-wp' ) . ' <a href="' . menu_page_url( 'gacwp_settings', false ) . '">' . __( "authorize the plugin", 'google-analytics-in-wp' ) . '</a>.',
							__( "Browser", 'google-analytics-in-wp' ), //16
							__( "Operating System", 'google-analytics-in-wp' ),
							__( "Screen Resolution", 'google-analytics-in-wp' ),
							__( "Mobile Brand", 'google-analytics-in-wp' ),
							__( "REFERRALS", 'google-analytics-in-wp' ), //20
							__( "KEYWORDS", 'google-analytics-in-wp' ),
							__( "SOCIAL", 'google-analytics-in-wp' ),
							__( "CAMPAIGN", 'google-analytics-in-wp' ),
							__( "DIRECT", 'google-analytics-in-wp' ),
							__( "NEW", 'google-analytics-in-wp' ), //25
							__( "Time on Page", 'google-analytics-in-wp' ),
							__( "Page Load Time", 'google-analytics-in-wp' ),
							__( "Session Duration", 'google-analytics-in-wp' ),
						),
						'rtLimitPages' => $this->gacwp->config->options['ga_realtime_pages'],
						'colorVariations' => GAINWP_Tools::variations( $this->gacwp->config->options['theme_color'] ),
						'region' => $region,
						'mapsApiKey' => apply_filters( 'gacwp_maps_api_key', $this->gacwp->config->options['maps_api_key'] ),
						'language' => get_bloginfo( 'language' ),
						'viewList' => $views,
						'scope' => 'admin-widgets',
					)

					);
					/* @formatter:on */
				}
			}

			/*
			 * Posts/Pages List Styles & Scripts
			 */
			$contentstats_hooks = array( 'edit.php' );
			if ( in_array( $hook, $contentstats_hooks ) ) {
				if ( GAINWP_Tools::check_roles( $this->gacwp->config->options['access_back'] ) && $this->gacwp->config->options['backend_item_reports'] ) {

					if ( $this->gacwp->config->options['ga_target_geomap'] ) {
						$country_codes = GAINWP_Tools::get_countrycodes();
						if ( isset( $country_codes[$this->gacwp->config->options['ga_target_geomap']] ) ) {
							$region = $this->gacwp->config->options['ga_target_geomap'];
						} else {
							$region = false;
						}
					} else {
						$region = false;
					}

					wp_enqueue_style( 'gacwp-nprogress', GAINWP_URL . 'common/nprogress/nprogress.css', null, GAINWP_CURRENT_VERSION );

					wp_enqueue_style( 'gacwp-backend-item-reports', GAINWP_URL . 'admin/css/item-reports.css', null, GAINWP_CURRENT_VERSION );

					wp_enqueue_style( "wp-jquery-ui-dialog" );

					wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

					wp_enqueue_script( 'gacwp-nprogress', GAINWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GAINWP_CURRENT_VERSION );

					wp_enqueue_script( 'gacwp-backend-item-reports', GAINWP_URL . 'common/js/reports5.js', array( 'gacwp-nprogress', 'googlecharts', 'jquery', 'jquery-ui-dialog' ), GAINWP_CURRENT_VERSION, true );

					/* @formatter:off */
					wp_localize_script( 'gacwp-backend-item-reports', 'gacwpItemData', array(
						'ajaxurl' => admin_url( 'admin-ajax.php' ),
						'security' => wp_create_nonce( 'gacwp_backend_item_reports' ),
						'dateList' => array(
							'today' => __( "Today", 'google-analytics-in-wp' ),
							'yesterday' => __( "Yesterday", 'google-analytics-in-wp' ),
							'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 7 ),
							'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 14 ),
							'30daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 30 ),
							'90daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 90 ),
							'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-in-wp' ), __('One', 'google-analytics-in-wp') ),
							'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-in-wp' ), __('Three', 'google-analytics-in-wp') ),
						),
						'reportList' => array(
							'uniquePageviews' => __( "Unique Views", 'google-analytics-in-wp' ),
							'users' => __( "Users", 'google-analytics-in-wp' ),
							'organicSearches' => __( "Organic", 'google-analytics-in-wp' ),
							'pageviews' => __( "Page Views", 'google-analytics-in-wp' ),
							'visitBounceRate' => __( "Bounce Rate", 'google-analytics-in-wp' ),
							'locations' => __( "Location", 'google-analytics-in-wp' ),
							'referrers' => __( "Referrers", 'google-analytics-in-wp' ),
							'searches' => __( "Searches", 'google-analytics-in-wp' ),
							'trafficdetails' => __( "Traffic", 'google-analytics-in-wp' ),
							'technologydetails' => __( "Technology", 'google-analytics-in-wp' ),
						),
						'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-in-wp' ), //0
							__( "Traffic Mediums", 'google-analytics-in-wp' ),
							__( "Visitor Type", 'google-analytics-in-wp' ),
							__( "Social Networks", 'google-analytics-in-wp' ),
							__( "Search Engines", 'google-analytics-in-wp' ),
							__( "Unique Views", 'google-analytics-in-wp' ),
							__( "Users", 'google-analytics-in-wp' ),
							__( "Page Views", 'google-analytics-in-wp' ),
							__( "Bounce Rate", 'google-analytics-in-wp' ),
							__( "Organic Search", 'google-analytics-in-wp' ),
							__( "Pages/Session", 'google-analytics-in-wp' ),
							__( "Invalid response", 'google-analytics-in-wp' ),
							__( "No Data", 'google-analytics-in-wp' ),
							__( "This report is unavailable", 'google-analytics-in-wp' ),
							__( "report generated by", 'google-analytics-in-wp' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-in-wp' ) . ' <a href="' . menu_page_url( 'gacwp_settings', false ) . '">' . __( "authorize the plugin", 'google-analytics-in-wp' ) . '</a>.',
							__( "Browser", 'google-analytics-in-wp' ), //16
							__( "Operating System", 'google-analytics-in-wp' ),
							__( "Screen Resolution", 'google-analytics-in-wp' ),
							__( "Mobile Brand", 'google-analytics-in-wp' ), //19
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ), //25
							__( "Time on Page", 'google-analytics-in-wp' ),
							__( "Page Load Time", 'google-analytics-in-wp' ),
							__( "Exit Rate", 'google-analytics-in-wp' ),
						),
						'colorVariations' => GAINWP_Tools::variations( $this->gacwp->config->options['theme_color'] ),
						'region' => $region,
						'mapsApiKey' => apply_filters( 'gacwp_maps_api_key', $this->gacwp->config->options['maps_api_key'] ),
						'language' => get_bloginfo( 'language' ),
						'viewList' => false,
						'scope' => 'admin-item',
						)
					);
					/* @formatter:on */
				}
			}

			/*
			 * Settings Styles & Scripts
			 */
			$settings_hooks = array( '_page_gacwp_settings', '_page_gacwp_backend_settings', '_page_gacwp_frontend_settings', '_page_gacwp_tracking_settings', '_page_gacwp_errors_debugging' );

			if ( in_array( $new_hook, $settings_hooks ) ) {
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker-script-handle', plugins_url( 'js/wp-color-picker-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
				wp_enqueue_script( 'gacwp-settings', plugins_url( 'js/settings.js', __FILE__ ), array( 'jquery' ), GAINWP_CURRENT_VERSION, true );
			}
		}

		/**
		 * Add "Settings" link in Plugins List
		 *
		 * @param
		 *            $links
		 * @return array
		 */
		public function settings_link( $links ) {
			$settings_link = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=gacwp_settings' ) ) . '">' . __( "Settings", 'google-analytics-in-wp' ) . '</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 *  Add an admin notice after a manual or atuomatic update
		 */
		function admin_notice() {
			$currentScreen = get_current_screen();

			if ( ! current_user_can( 'manage_options' ) || strpos( $currentScreen->base, '_gacwp_' ) === false ) {
				return;
			}

			if ( get_option( 'gacwp_got_updated' ) ) :
				?>
<div id="gacwp-notice" class="notice is-dismissible">
	<p><?php echo sprintf( __('Google Analytics for WP has been updated to version %s.', 'google-analytics-in-wp' ), GAINWP_CURRENT_VERSION).' '.sprintf( __('For details, check out %1$s.', 'google-analytics-in-wp' ), sprintf(' <a href="https://intelligencewp.com/google-analytics-connector-wordpress/?utm_source=gacwp_notice&utm_medium=link&utm_content=release_notice&utm_campaign=gacwp">%s</a>', __('the plugin documentation', 'google-analytics-in-wp') ) ); ?></p>
</div>

			<?php
			endif;
		}
	}
}
