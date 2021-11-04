<?php
/**
 * NSESS: session functions
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'nsess_start' ) ) {
	/**
	 * Initialize naran session.
	 *
	 * Do not call this method after headers are sent. It's useless.
	 */
	function nsess_start() {
		nsess()->session->initialize();
	}
}


if ( ! function_exists( 'nsess_get' ) ) {
	/**
	 * Get session value by key.
	 *
	 * Initialization is automatic, but note that initialization must be done before headers are sent.
	 *
	 * @param string $key     Identifier key.
	 * @param mixed  $default Alternative value if key not found.
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
	 * @param string   $key
	 * @param mixed    $value
	 * @param int|null $timeout
	 */
	function nsess_set( string $key, $value, ?int $timeout = null ) {
		nsess()->session->set( $key, $value );
	}
}


if ( ! function_exists( 'nsess_has' ) ) {
	/**
	 * Check if session has key
	 */
	function nsess_has( string $key ): bool {
		return nsess()->session->has( $key );
	}
}


if ( ! function_exists( 'nsess_get_expiration' ) ) {
	/**
	 * Get key's expiration timeout.
	 *
	 * @param string $key
	 *
	 * @return int
	 */
	function nsess_get_expiration( string $key ): int {
		return nsess()->session->get_expiration( $key );
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


if ( ! function_exists( 'nsess_destroy' ) ) {
	/**
	 * Destory session.
	 */
	function nsess_destroy() {
		nsess()->session->destroy();
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
		return apply_filters( 'nsess_cookie_name', NSESS_COOKIE_NAME ?: 'nsess' );
	}
}


if ( ! function_exists( 'nsess_timeout' ) ) {
	function nsess_timeout(): int {
		$timeout = intval( NSESS_TIMEOUT );
		$timeout = $timeout > 0 ? $timeout : DAY_IN_SECONDS;

		return apply_filters( 'nsess_timeout', $timeout );
	}
}


if ( ! function_exists( 'nsess_cookie_path' ) ) {
	function nsess_cookie_path(): string {
		return apply_filters( 'nsess_cookie_path', NSESS_COOKIEPATH ?: COOKIEPATH );
	}
}

if ( ! function_exists( 'nsess_cookie_domain' ) ) {
	function nsess_cookie_domain(): string {
		return apply_filters( 'nsess_cookie_domain', NSESS_COOKIE_DOMAIN ?: COOKIE_DOMAIN );
	}
}


if ( ! function_exists( 'nsess_secure_cookie' ) ) {
	function nsess_secure_cookie(): bool {
		if ( '' === NSESS_SECURE ) {
			$secure_cookie = is_ssl();
		} else {
			$secure_cookie = filter_var( NSESS_SECURE, FILTER_VALIDATE_BOOLEAN );
		}

		return apply_filters( 'nsess_secure_cookie', $secure_cookie );
	}
}


if ( ! function_exists( 'nsess_http_only_cookie' ) ) {
	function nsess_http_only_cookie(): bool {
		$http_only = filter_var( NSESS_HTTP_ONLY, FILTER_VALIDATE_BOOLEAN );

		return apply_filters( 'nsess_http_only_cookie', $http_only );
	}
}
