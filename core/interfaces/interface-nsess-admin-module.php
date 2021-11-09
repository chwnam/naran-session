<?php
/**
 * NSESS: Admin module interface
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( 'NSESS_Admin_Module' ) ) {
	interface NSESS_Admin_Module extends NSESS_Module {
	}
}
