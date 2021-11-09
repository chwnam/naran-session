<?php
/**
 * NSESS: Callback exception
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Callback_Exception' ) ) {
	class NSESS_Callback_Exception extends Exception{
	}
}
