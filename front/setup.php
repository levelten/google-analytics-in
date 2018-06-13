<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GAINWP_Frontend_Setup' ) ) {

	final class GAINWP_Frontend_Setup {

		private $gacwp;

		public function __construct() {
			$this->gacwp = GAINWP();

			// Styles & Scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
		}

		/**
		 * Styles & Scripts conditional loading
		 *
		 * @param
		 *            $hook
		 */
		public function load_styles_scripts() {
			$lang = get_bloginfo( 'language' );
			$lang = explode( '-', $lang );
			$lang = $lang[0];

			/*
			 * Item reports Styles & Scripts
			 */
			if ( GAINWP_Tools::check_roles( $this->gacwp->config->options['access_front'] ) && $this->gacwp->config->options['frontend_item_reports'] ) {

				wp_enqueue_style( 'gacwp-nprogress', GAINWP_URL . 'common/nprogress/nprogress.css', null, GAINWP_CURRENT_VERSION );

				wp_enqueue_style( 'gacwp-frontend-item-reports', GAINWP_URL . 'front/css/item-reports.css', null, GAINWP_CURRENT_VERSION );

				$country_codes = GAINWP_Tools::get_countrycodes();
				if ( $this->gacwp->config->options['ga_target_geomap'] && isset( $country_codes[$this->gacwp->config->options['ga_target_geomap']] ) ) {
					$region = $this->gacwp->config->options['ga_target_geomap'];
				} else {
					$region = false;
				}

				wp_enqueue_style( "wp-jquery-ui-dialog" );

				wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

				wp_enqueue_script( 'gacwp-nprogress', GAINWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GAINWP_CURRENT_VERSION );

				wp_enqueue_script( 'gacwp-frontend-item-reports', GAINWP_URL . 'common/js/reports5.js', array( 'gacwp-nprogress', 'googlecharts', 'jquery', 'jquery-ui-dialog' ), GAINWP_CURRENT_VERSION, true );

				/* @formatter:off */
				wp_localize_script( 'gacwp-frontend-item-reports', 'gacwpItemData', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'gacwp_frontend_item_reports' ),
					'dateList' => array(
						'today' => __( "Today", 'google-analytics-in-wp' ),
						'yesterday' => __( "Yesterday", 'google-analytics-in-wp' ),
						'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 7 ),
						'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 14 ),
						'30daysAgo' =>  sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 30 ),
						'90daysAgo' =>  sprintf( __( "Last %d Days", 'google-analytics-in-wp' ), 90 ),
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
							__( "Search Engines", 'google-analytics-in-wp' ),
							__( "Social Networks", 'google-analytics-in-wp' ),
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
							__( "This plugin needs an authorization:", 'google-analytics-in-wp' ) . ' <strong>' . __( "authorize the plugin", 'google-analytics-in-wp' ) . '</strong>!',
							__( "Browser", 'google-analytics-in-wp' ), //16
							__( "Operating System", 'google-analytics-in-wp' ),
							__( "Screen Resolution", 'google-analytics-in-wp' ),
							__( "Mobile Brand", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ),
							__( "Future Use", 'google-analytics-in-wp' ), //25
							__( "Time on Page", 'google-analytics-in-wp' ),
							__( "Page Load Time", 'google-analytics-in-wp' ),
							__( "Exit Rate", 'google-analytics-in-wp' ),
							__( "Precision: ", 'google-analytics-in-wp' ), //29
					),
					'colorVariations' => GAINWP_Tools::variations( $this->gacwp->config->options['theme_color'] ),
					'region' => $region,
					'mapsApiKey' => apply_filters( 'gacwp_maps_api_key', $this->gacwp->config->options['maps_api_key'] ),
					'language' => get_bloginfo( 'language' ),
					'filter' => $_SERVER["REQUEST_URI"],
					'viewList' => false,
					'scope' => 'front-item',
				 )
				);
				/* @formatter:on */
			}
		}
	}
}
