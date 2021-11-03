<?php
/**
 * NSESS: Activation register
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Activation' ) ) {
	class NSESS_Register_Activation implements NSESS_Register {
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

		public function get_items(): Generator {
			// Define your activation regs for callback.
			yield null;
		}
	}
}
