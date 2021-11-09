<?php
/**
 * NSESS: Activation register base
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Base_Activation' ) ) {
	abstract class NSESS_Register_Base_Activation implements NSESS_Register {
		public function __construct() {
			register_activation_hook( nsess()->get_main_file(), [ $this, 'register' ] );
		}

		/**
		 * Method name can mislead, but it does activation callback jobs.
		 */
		public function register() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Activation ) {
					$item->register();
				}
			}
		}
	}
}
