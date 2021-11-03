<?php
/**
 * NSESS: Cron register
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Cron' ) ) {
	class NSESS_Register_Cron implements NSESS_Register {
		use NSESS_Hook_Impl;

		public function __construct() {
			register_activation_hook( nsess()->get_main_file(), [ $this, 'register' ] );
			register_deactivation_hook( nsess()->get_main_file(), [ $this, 'unregister' ] );
		}

		public function register() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Cron ) {
					$item->register();
				}
			}
		}

		public function unregister() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Cron ) {
					$item->unregister();
				}
			}
		}

		public function get_items(): Generator {
			yield null;
		}
	}
}
