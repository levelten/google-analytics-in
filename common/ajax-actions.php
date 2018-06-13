<?php
/**
 * Copyright 2013 Alin Marcu
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

if ( ! class_exists( 'GACWP_Common_Ajax' ) ) {

	final class GACWP_Common_Ajax {

		private $gacwp;

		public function __construct() {
			$this->gacwp = GACWP();

			if ( GACWP_Tools::check_roles( $this->gacwp->config->options['access_back'] ) || GACWP_Tools::check_roles( $this->gacwp->config->options['access_front'] ) ) {
				add_action( 'wp_ajax_gacwp_set_error', array( $this, 'ajax_set_error' ) );
			}
		}

		/**
		 * Ajax handler for storing JavaScript Errors
		 *
		 * @return json|int
		 */
		public function ajax_set_error() {
			if ( ! isset( $_POST['gacwp_security_set_error'] ) || ! ( wp_verify_nonce( $_POST['gacwp_security_set_error'], 'gacwp_backend_item_reports' ) || wp_verify_nonce( $_POST['gacwp_security_set_error'], 'gacwp_frontend_item_reports' ) ) ) {
				wp_die( - 40 );
			}
			$timeout = 24 * 60 * 60;
			GACWP_Tools::set_error( $_POST['response'], $timeout );
			wp_die();
		}
	}
}
