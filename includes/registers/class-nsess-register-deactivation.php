<?php
/**
 * NSESS: Deactivation register
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Deactivation' ) ) {
	class NSESS_Register_Deactivation implements NSESS_Register {
		public function __construct() {
			register_deactivation_hook( nsess()->get_main_file(), [ $this, 'register' ] );
		}

		/**
		 * Method name can mislead, but it does deactivation callback jobs.
		 */
		public function register() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Deactivation ) {
					$item->register();
				}
			}
		}

		public function get_items(): Generator {
			// Define your deactivation regs for callback.
			yield null;
		}
	}
}
