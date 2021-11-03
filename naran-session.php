<?php
/**
 * Plugin Name:       Naran Session
 * Plugin URI:        https://github.com/chwnam/naran-session
 * Description:       Simple cookie-based session plugin for WordPress.
 * Version:           0.0.0
 * Requires at least: 5.5.0
 * Requires PHP:      7.4
 * Author:            changwoo
 * Author URI:        https://blog.changwoo.pe.kr/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nsess
 * Domain Path:       /languages
 * CPBN version:      1.0.3
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

const NSESS_MAIN_FILE = __FILE__;
const NSESS_VERSION   = '0.0.0';
const NSESS_PRIORITY  = 770;

nsess_default_constants();

nsess();
