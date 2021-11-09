<?php
/**
 * NSESS: Cron schedule reg.
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Reg_Cron_Schedule' ) ) {
	class NSESS_Reg_Cron_Schedule implements NSESS_Reg {
		public string $name;

		public int $interval;

		public string $display;

		public function __construct(
			string $name,
			int $interval,
			string $display
		) {
			$this->name     = $name;
			$this->interval = $interval;
			$this->display  = $display;
		}

		public function register( $dispatch = null ) {
			// Do nothing.
		}
	}
}
