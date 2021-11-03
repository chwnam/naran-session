<?php
/**
 * NSESS: Script register
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Register_Script' ) ) {
	class NSESS_Register_Script implements NSESS_Register {
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
				if ( $item instanceof NSESS_Reg_Script ) {
					$item->register();
				}
			}
		}

		public function get_items(): Generator {
			yield null;
		}

		/**
		 * @param string $rel_path
		 * @param bool   $replace_min
		 *
		 * @return string
		 */
		protected function src_helper( string $rel_path, bool $replace_min = true ): string {
			$rel_path = trim( $rel_path, '\\/' );

			if ( nsess_script_debug() && $replace_min && substr( $rel_path, - 7 ) === '.min.js' ) {
				$rel_path = substr( $rel_path, 0, strlen( $rel_path ) - 7 ) . '.js';
			}

			return plugin_dir_url( nsess()->get_main_file() ) . "assets/js/{$rel_path}";
		}
	}
}
