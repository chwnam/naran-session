<?php
/**
 * NSESS: Registerable interface
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( 'NSESS_Reg' ) ) {
	interface NSESS_Reg {
		public function register( $dispatch = null );
	}
}
