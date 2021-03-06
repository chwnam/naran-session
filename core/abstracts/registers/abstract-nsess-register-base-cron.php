<?php
/**
 * NSESS: Cron register base
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Base_Cron' ) ) {
	abstract class NSESS_Register_Base_Cron implements NSESS_Register {
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
	}
}
