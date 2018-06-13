<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GACWP_Frontend_Setup' ) ) {

	final class GACWP_Frontend_Setup {

		private $gacwp;

		public function __construct() {
			$this->gacwp = GACWP();

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
			if ( GACWP_Tools::check_roles( $this->gacwp->config->options['access_front'] ) && $this->gacwp->config->options['frontend_item_reports'] ) {

				wp_enqueue_style( 'gacwp-nprogress', GACWP_URL . 'common/nprogress/nprogress.css', null, GACWP_CURRENT_VERSION );

				wp_enqueue_style( 'gacwp-frontend-item-reports', GACWP_URL . 'front/css/item-reports.css', null, GACWP_CURRENT_VERSION );

				$country_codes = GACWP_Tools::get_countrycodes();
				if ( $this->gacwp->config->options['ga_target_geomap'] && isset( $country_codes[$this->gacwp->config->options['ga_target_geomap']] ) ) {
					$region = $this->gacwp->config->options['ga_target_geomap'];
				} else {
					$region = false;
				}

				wp_enqueue_style( "wp-jquery-ui-dialog" );

				wp_register_script( 'googlecharts', 'https://www.gstatic.com/charts/loader.js', array(), null );

				wp_enqueue_script( 'gacwp-nprogress', GACWP_URL . 'common/nprogress/nprogress.js', array( 'jquery' ), GACWP_CURRENT_VERSION );

				wp_enqueue_script( 'gacwp-frontend-item-reports', GACWP_URL . 'common/js/reports5.js', array( 'gacwp-nprogress', 'googlecharts', 'jquery', 'jquery-ui-dialog' ), GACWP_CURRENT_VERSION, true );

				/* @formatter:off */
				wp_localize_script( 'gacwp-frontend-item-reports', 'gacwpItemData', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'gacwp_frontend_item_reports' ),
					'dateList' => array(
						'today' => __( "Today", 'google-analytics-connector-wp' ),
						'yesterday' => __( "Yesterday", 'google-analytics-connector-wp' ),
						'7daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-connector-wp' ), 7 ),
						'14daysAgo' => sprintf( __( "Last %d Days", 'google-analytics-connector-wp' ), 14 ),
						'30daysAgo' =>  sprintf( __( "Last %d Days", 'google-analytics-connector-wp' ), 30 ),
						'90daysAgo' =>  sprintf( __( "Last %d Days", 'google-analytics-connector-wp' ), 90 ),
						'365daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 1, 'google-analytics-connector-wp' ), __('One', 'google-analytics-connector-wp') ),
						'1095daysAgo' =>  sprintf( _n( "%s Year", "%s Years", 3, 'google-analytics-connector-wp' ), __('Three', 'google-analytics-connector-wp') ),
					),
					'reportList' => array(
						'uniquePageviews' => __( "Unique Views", 'google-analytics-connector-wp' ),
						'users' => __( "Users", 'google-analytics-connector-wp' ),
						'organicSearches' => __( "Organic", 'google-analytics-connector-wp' ),
						'pageviews' => __( "Page Views", 'google-analytics-connector-wp' ),
						'visitBounceRate' => __( "Bounce Rate", 'google-analytics-connector-wp' ),
						'locations' => __( "Location", 'google-analytics-connector-wp' ),
						'referrers' => __( "Referrers", 'google-analytics-connector-wp' ),
						'searches' => __( "Searches", 'google-analytics-connector-wp' ),
						'trafficdetails' => __( "Traffic", 'google-analytics-connector-wp' ),
						'technologydetails' => __( "Technology", 'google-analytics-connector-wp' ),
					),
					'i18n' => array(
							__( "A JavaScript Error is blocking plugin resources!", 'google-analytics-connector-wp' ), //0
							__( "Traffic Mediums", 'google-analytics-connector-wp' ),
							__( "Visitor Type", 'google-analytics-connector-wp' ),
							__( "Search Engines", 'google-analytics-connector-wp' ),
							__( "Social Networks", 'google-analytics-connector-wp' ),
							__( "Unique Views", 'google-analytics-connector-wp' ),
							__( "Users", 'google-analytics-connector-wp' ),
							__( "Page Views", 'google-analytics-connector-wp' ),
							__( "Bounce Rate", 'google-analytics-connector-wp' ),
							__( "Organic Search", 'google-analytics-connector-wp' ),
							__( "Pages/Session", 'google-analytics-connector-wp' ),
							__( "Invalid response", 'google-analytics-connector-wp' ),
							__( "No Data", 'google-analytics-connector-wp' ),
							__( "This report is unavailable", 'google-analytics-connector-wp' ),
							__( "report generated by", 'google-analytics-connector-wp' ), //14
							__( "This plugin needs an authorization:", 'google-analytics-connector-wp' ) . ' <strong>' . __( "authorize the plugin", 'google-analytics-connector-wp' ) . '</strong>!',
							__( "Browser", 'google-analytics-connector-wp' ), //16
							__( "Operating System", 'google-analytics-connector-wp' ),
							__( "Screen Resolution", 'google-analytics-connector-wp' ),
							__( "Mobile Brand", 'google-analytics-connector-wp' ),
							__( "Future Use", 'google-analytics-connector-wp' ),
							__( "Future Use", 'google-analytics-connector-wp' ),
							__( "Future Use", 'google-analytics-connector-wp' ),
							__( "Future Use", 'google-analytics-connector-wp' ),
							__( "Future Use", 'google-analytics-connector-wp' ),
							__( "Future Use", 'google-analytics-connector-wp' ), //25
							__( "Time on Page", 'google-analytics-connector-wp' ),
							__( "Page Load Time", 'google-analytics-connector-wp' ),
							__( "Exit Rate", 'google-analytics-connector-wp' ),
							__( "Precision: ", 'google-analytics-connector-wp' ), //29
					),
					'colorVariations' => GACWP_Tools::variations( $this->gacwp->config->options['theme_color'] ),
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