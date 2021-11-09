<?php
/**
 * NSESS: Custom post type register base
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Base_Post_Type' ) ) {
	abstract class NSESS_Register_Base_Post_Type implements NSESS_Register {
		use NSESS_Hook_Impl;

		public function __construct() {
			$this->add_filter( 'init', 'register' );
		}

		/**
		 * @callback
		 * @actin       init
		 */
		public function register() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Post_Type ) {
					$item->register();
				}
			}
		}
	}
}
