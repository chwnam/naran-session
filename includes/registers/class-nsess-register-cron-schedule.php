<?php
/**
 * NSESS: Cron schedule register
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Cron_Schedule' ) ) {
	class NSESS_Register_Cron_Schedule implements NSESS_Register {
		use NSESS_Hook_Impl;

		public function __construct() {
			$this->add_filter( 'cron_schedules', 'register' );
		}

		/**
		 * @callback
		 * @filter   cron_schedules
		 *
		 * @return  array
		 * @see      wp_get_schedules()
		 */
		public function register(): array {
			$schedules = func_get_arg( 0 );

			foreach ( $this->get_items() as $item ) {
				if (
					$item instanceof NSESS_Reg_Cron_Schedule &&
					$item->interval > 0 &&
					! isset( $schedules[ $item->name ] )
				) {
					$schedules[ $item->name ] = [
						'interval' => $item->interval,
						'display'  => $item->display,
					];
				}
			}

			return $schedules;
		}

		public function get_items(): Generator {
			yield null;
		}
	}
}
