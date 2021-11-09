<?php
/**
 * NSESS: Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Main' ) ) {
	/**
	 * Class NSESS_Main
	 *
	 * @property-read NSESS_Session $session
	 */
	final class NSESS_Main extends NSESS_Main_Base {
		protected function get_modules(): array {
			return [
				'session' => function () { return new NSESS_Session(); },
			];
		}
	}
}
