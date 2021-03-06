<?php
/**
 * NSESS: Style register base
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Base_Style' ) ) {
	abstract class NSESS_Register_Base_Style implements NSESS_Register {
		use NSESS_Hook_Impl;

		public function __construct() {
			$this->add_action( 'init', 'register' );
		}

		/**
		 * @callback
		 * @action       init
		 */
		public function register() {
			foreach ( $this->get_items() as $item ) {
				if ( $item instanceof NSESS_Reg_Style ) {
					$item->register();
				}
			}
		}

		/**
		 * 'src' location helper.
		 *
		 * @param string $rel_path
		 * @param bool   $replace_min
		 *
		 * @return string
		 */
		protected function src_helper( string $rel_path, bool $replace_min = true ): string {
			$rel_path = trim( $rel_path, '\\/' );

			if ( nsess_script_debug() && $replace_min && substr( $rel_path, - 8 ) === '.min.css' ) {
				$rel_path = substr( $rel_path, 0, strlen( $rel_path ) - 8 ) . '.css';
			}

			return plugin_dir_url( nsess()->get_main_file() ) . 'assets/css/' . $rel_path;
		}
	}
}
