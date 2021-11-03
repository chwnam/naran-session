<?php
/**
 * NSESS: session functions
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'nsess_init' ) ) {
	/**
	 * Initialize naran session.
	 *
	 * Do not call this method after headers are sent. It's useless.
	 */
	function nsess_init() {
		nsess()->session->initialize();
	}
}


if ( ! function_exists( 'nsess_get' ) ) {
	/**
	 * Get session value by key.
	 *
	 * Initialization is automatic, but note that initialization must be done before headers are sent.
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	function nsess_get( string $key, $default = null ) {
		return nsess()->session->get( $key, $default );
	}
}


if ( ! function_exists( 'nsess_set' ) ) {
	/**
	 * Set session value by key.
	 *
	 * Like nsess_get, initialization is automatic.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	function nsess_set( string $key, $value ) {
		nsess()->session->set( $key, $value );
	}
}


if ( ! function_exists( 'nsess_remove' ) ) {
	/**
	 * Remove session value by key.
	 *
	 * @param string $key
	 */
	function nsess_remove( string $key ) {
		nsess()->session->set( $key, null );
	}
}


if ( ! function_exists( 'nsess_reset' ) ) {
	/**
	 * Reset session.
	 */
	function nsess_reset() {
		nsess()->session->reset();
	}
}


if ( ! function_exists( 'nsess_default_constants' ) ) {
	function nsess_default_constants() {
		if ( ! defined( 'NSESS_COOKIE_NAME' ) ) {
			define( 'NSESS_COOKIE_NAME', 'nsess' );
		}

		if ( ! defined( 'NSESS_TIMEOUT' ) ) {
			define( 'NSESS_TIMEOUT', DAY_IN_SECONDS );
		}

		if ( ! defined( 'NSESS_COOKIEPATH' ) ) {
			define( 'NSESS_COOKIEPATH', '' );
		}

		if ( ! defined( 'NSESS_COOKIE_DOMAIN' ) ) {
			define( 'NSESS_COOKIE_DOMAIN', '' );
		}

		if ( ! defined( 'NSESS_SECURE' ) ) {
			define( 'NSESS_SECURE', '' );
		}

		if ( ! defined( 'NSESS_HTTP_ONLY' ) ) {
			define( 'NSESS_HTTP_ONLY', true );
		}
	}
}


if ( ! function_exists( 'nsess_cookie_name' ) ) {
	function nsess_cookie_name(): string {
		return NSESS_COOKIE_NAME ?: 'nsess';
	}
}


if ( ! function_exists( 'nsess_timeout' ) ) {
	function nsess_timeout(): int {
		$timeout = intval( NSESS_TIMEOUT );

		return $timeout > 0 ? $timeout : DAY_IN_SECONDS;
	}
}


if ( ! function_exists( 'nsess_cookie_path' ) ) {
	function nsess_cookie_path(): string {
		return NSESS_COOKIEPATH ?: COOKIEPATH;
	}
}

if ( ! function_exists( 'nsess_cookie_domain' ) ) {
	function nsess_cookie_domain(): string {
		return NSESS_COOKIE_DOMAIN ?: COOKIE_DOMAIN;
	}
}


if ( ! function_exists( 'nsess_secure_cookie' ) ) {
	function nsess_secure_cookie(): bool {
		if ( '' === NSESS_SECURE ) {
			return is_ssl();
		} else {
			return filter_var( NSESS_SECURE, FILTER_VALIDATE_BOOLEAN );
		}
	}
}


if ( ! function_exists( 'nsess_http_only_cookie' ) ) {
	function nsess_http_only_cookie(): bool {
		return filter_var( NSESS_HTTP_ONLY, FILTER_VALIDATE_BOOLEAN );
	}
}
