<?php
/**
 * NSESS: Deactivation register base
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Base_Deactivation' ) ) {
	abstract class NSESS_Register_Base_Deactivation implements NSESS_Register {
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
	}
}
