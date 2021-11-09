<?php
/**
 * NSESS: Register interface
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( 'NSESS_Register' ) ) {
	interface NSESS_Register {
		public function get_items(): Generator;

		public function register();
	}
}
