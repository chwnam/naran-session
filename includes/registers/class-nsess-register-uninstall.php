<?php
/**
 * NSESS: Uninstall register
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Uninstall' ) ) {
	class NSESS_Register_Uninstall implements NSESS_Register {
		public function __construct() {
			register_uninstall_hook( nsess()->get_main_file(), [ $this, 'register' ] );
		}

		/**
		 * Method name can mislead, but it does uninstall callback jobs.
		 */
		public function register() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Uninstall ) {
					$item->register();
				}
			}
		}

		public function get_items(): Generator {
			yield null;
		}
	}
}
