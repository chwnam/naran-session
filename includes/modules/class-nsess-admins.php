<?php
/**
 * NSESS: Admin modules group
 *
 * Manage all admin modules
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Admins' ) ) {
	class NSESS_Admins implements NSESS_Module {
		use NSESS_Submodule_Impl;

		public function __construct() {
			$this->assign_modules(
				[
				]
			);
		}
	}
}
