<?php
/**
 * NSESS: Session module
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Session' ) ) {
	class NSESS_Session implements NSESS_Module {
		use NSESS_Hook_Impl;

		private ?array $session_storage;

		private ?array $expiry_storage;

		private bool $dirty;

		private string $cookie_name;

		private string $session_id;

		public function __construct() {
			$this->session_storage = null;
			$this->expiry_storage  = null;
			$this->dirty           = false;
			$this->cookie_name     = nsess_cookie_name();
		}

		/**
		 * Get a value by key.
		 *
		 * @param string $key
		 * @param mixed  $default Alternative value if key not found.
		 *
		 * @return mixed
		 */
		public function get( string $key, $default = null ) {
			if ( ! $this->is_initialized() ) {
				$this->initialize();
			}

			if (
				isset( $this->session_storage[ $key ] ) &&
				isset( $this->expiry_storage[ $key ] ) &&
				$this->expiry_storage[ $key ] > time()
			) {
				return $this->session_storage[ $key ];
			} else {
				return $default;
			}
		}

		/**
		 * Store a value by key.
		 *
		 * @param string $key
		 * @param mixed  $value
		 */
		public function set( string $key, $value ) {
			if ( ! $this->is_initialized() ) {
				$this->initialize();
			}

			if ( is_null( $value ) ) {
				unset( $this->session_storage[ $key ], $this->expiry_storage[ $key ] );
			} else {
				$this->session_storage[ $key ] = $value;
				$this->expiry_storage[ $key ]  = self::generate_expiration();
			}

			$this->dirty = true;
		}

		public function reset() {
			if ( ! $this->is_initialized() ) {
				$this->initialize();
			}

			$this->session_storage = [];
			$this->expiry_storage  = [];
			$this->dirty           = true;
		}

		public function is_initialized(): bool {
			return ! ( is_null( $this->session_storage ) || is_null( $this->expiry_storage ) );
		}

		public function initialize() {
			if ( $this->is_initialized() ) {
				return;
			}

			if ( $this->verify_cookie_value() ) {
				// Client have a valid session. Load session.
				$this->session_id = $this->get_session_id();

				// Load session value. This code must be placed after `$this->session_id = $this->get_session_id()`.
				$transient = get_transient( $this->get_transient_name() );

				$this->session_storage = $transient['session'] ?? [];
				$this->expiry_storage  = $transient['expiry'] ?? [];

				// Shake off storage and get maximum timeout value.
				$max_timeout = $this->shake_storage();

				if ( ! headers_sent() && $max_timeout < 600 ) {
					setcookie(
						$this->cookie_name,
						self::generate_cookie_value( $this->session_id ),
						self::generate_expiration(),
						nsess_cookie_path(),
						nsess_cookie_domain(),
						nsess_secure_cookie(),
						nsess_http_only_cookie()
					);
				}
				// Save session whenever it is initialized because cookie is already present.
				$this->add_action( 'shutdown', 'save_session' );
			} else {
				// Create a new session, as session is invalid, expired, or not created.
				$this->session_id      = self::generate_session_id();
				$this->session_storage = [];
				$this->expiry_storage  = [];
				$this->dirty           = true;

				if ( ! headers_sent() ) {
					setcookie(
						$this->cookie_name,
						self::generate_cookie_value( $this->session_id ),
						self::generate_expiration(),
						nsess_cookie_path(),
						nsess_cookie_domain(),
						nsess_secure_cookie(),
						nsess_http_only_cookie()
					);
					// Save session only when cookie is successfully sent.
					$this->add_action( 'shutdown', 'save_session' );
				}
			}
		}

		/**
		 * Store session data to the database.
		 */
		public function save_session() {
			if ( $this->is_initialized() && $this->dirty ) {
				$timeout = $this->shake_storage();

				$merged = [
					'session' => $this->session_storage,
					'expiry'  => $this->expiry_storage,
				];

				// NOTE: $timeout is not a timestamp!
				set_transient( $this->get_transient_name(), $merged, $timeout );
			}
		}

		/**
		 * Remove expired values from storge.
		 *
		 * Return timeout value for transient.
		 *
		 * @return int
		 */
		private function shake_storage(): int {
			$keys = array_keys( $this->session_storage );
			$now  = time();
			$max  = 0;

			foreach ( $keys as $key ) {
				if ( $this->expiry_storage[ $key ] <= $now ) {
					unset( $this->session_storage[ $key ], $this->expiry_storage[ $key ] );
					$this->dirty = true;
				} elseif ( $max < $this->expiry_storage[ $key ] ) {
					$max = $this->expiry_storage[ $key ];
				}
			}

			return $max > $now ? $max - $now : nsess_timeout();
		}

		private function get_transient_name(): string {
			return "nsess_$this->session_id";
		}

		/**
		 * Get session id from cookie.
		 *
		 * @return string
		 */
		private function get_session_id(): string {
			if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {
				$tokens = explode( '|', urldecode( $_COOKIE[ $this->cookie_name ] ) );
				return 2 === count( $tokens ) ? $tokens[0] : '';
			} else {
				return '';
			}
		}

		private function verify_cookie_value(): bool {
			if ( ! isset( $_COOKIE[ $this->cookie_name ] ) ) {
				return false;
			}

			$t = explode( '|', urldecode( $_COOKIE[ $this->cookie_name ] ) );
			if ( 2 !== count( $t ) ) {
				return false;
			}

			return hash_equals( $t[1], hash_hmac( self::algo(), $t[0], AUTH_KEY ) );
		}

		private static function generate_session_id(): string {
			return time() . ':' . wp_generate_password( 12, false );
		}

		private static function generate_expiration(): int {
			return time() + nsess_timeout();
		}

		private static function generate_cookie_value( string $session_id ): string {
			$hash = hash_hmac( self::algo(), $session_id, AUTH_KEY );

			return "$session_id|$hash";
		}

		private static function algo(): string {
			return function_exists( 'hash' ) ? 'sha256' : 'sha1';
		}
	}
}
