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
			$this->cookie_name     = nsess_cookie_name();
			$this->session_storage = null;
			$this->expiry_storage  = null;
			$this->dirty           = false;
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
			$this->initialize();

			if ( $this->has( $key ) ) {
				return $this->session_storage[ $key ];
			} else {
				return $default;
			}
		}

		/**
		 * Store a value by key.
		 *
		 * @param string   $key
		 * @param mixed    $value
		 * @param int|null $timeout
		 */
		public function set( string $key, $value, ?int $timeout = null ) {
			$this->initialize();

			if ( is_null( $value ) ) {
				unset( $this->session_storage[ $key ], $this->expiry_storage[ $key ] );
			} else {
				$this->session_storage[ $key ] = $value;
				$this->expiry_storage[ $key ]  = self::generate_expiration( $timeout );
			}

			$this->dirty = true;
		}

		/**
		 * Check if session has key.
		 *
		 * @param string $key
		 *
		 * @return bool
		 */
		public function has( string $key ): bool {
			$this->initialize();

			return isset( $this->session_storage[ $key ] ) &&
			       isset( $this->expiry_storage[ $key ] ) &&
			       $this->expiry_storage[ $key ] > time();
		}

		/**
		 * Get key's expiration timestamp.
		 *
		 * @param string $key
		 *
		 * @return int
		 */
		public function get_expiration( string $key ): int {
			$this->initialize();

			if ( $this->has( $key ) ) {
				return $this->expiry_storage[ $key ];
			} else {
				return 0;
			}
		}

		public function reset() {
			$this->initialize();

			$this->session_storage = [];
			$this->expiry_storage  = [];
			$this->dirty           = true;
		}

		public function destroy() {
			$this->initialize();

			$this
				->remove_filter( 'wp_headers', 'send_cookie' )
				->remove_action( 'shutdown', 'save_session' )
			;

			if ( ! headers_sent() ) {
				// Destory cookie.
				setcookie(
					$this->cookie_name,
					' ',
					time() - ( 10 * YEAR_IN_SECONDS ),
					nsess_cookie_path(),
					nsess_cookie_domain(),
					nsess_secure_cookie(),
					nsess_http_only_cookie()
				);
			}

			delete_transient( self::generate_transient_name( $this->session_id ) );
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

				$transient = get_transient( self::generate_transient_name( $this->session_id ) );

				$this->session_storage = $transient['session'] ?? [];
				$this->expiry_storage  = $transient['expiry'] ?? [];
				$this->dirty           = false;
			} else {
				// Create a new session, as session is invalid, expired, or not created.
				$this->session_id      = self::generate_session_id();
				$this->session_storage = [];
				$this->expiry_storage  = [];
				$this->dirty           = true;
			}

			$this
				->add_filter( 'wp_headers', 'send_cookie', null, 2 )
				->add_action( 'shutdown', 'save_session' )
			;
		}

		/**
		 * When session is changed, cookie expiration must be modified.
		 *
		 * @callback
		 * @filter    wp_headers
		 *
		 * @param array $headers Bypassed.
		 *
		 * @return array
		 */
		public function send_cookie( $headers ): array {
			if ( $this->is_initialized() && $this->dirty && ! headers_sent() ) {
				$expiry = $this->shake_storage();

				setcookie(
					$this->cookie_name,
					urlencode( $_COOKIE[ $this->cookie_name ] ?? self::generate_cookie_value( $this->session_id ) ),
					$expiry ?: self::generate_expiration(),
					nsess_cookie_path(),
					nsess_cookie_domain(),
					nsess_secure_cookie(),
					nsess_http_only_cookie()
				);
			}

			return $headers;
		}

		/**
		 * Store session data to the database.
		 */
		public function save_session() {
			if ( $this->is_initialized() && $this->dirty ) {
				$expiration = $this->shake_storage();

				set_transient(
					self::generate_transient_name( $this->session_id ),
					[
						'session' => $this->session_storage,
						'expiry'  => $this->expiry_storage,
					],
					max( 10, $expiration - time() )
				);
			}
		}

		/**
		 * Remove expired values from storge.
		 *
		 * Return maximum expiration timestamp.
		 *
		 * @return int
		 */
		private function shake_storage(): int {
			$keys = array_keys( $this->session_storage );
			$max  = 0;

			foreach ( $keys as $key ) {
				if ( $this->expiry_storage[ $key ] <= time() ) {
					unset( $this->session_storage[ $key ], $this->expiry_storage[ $key ] );
					$this->dirty = true;
				} elseif ( $max < $this->expiry_storage[ $key ] ) {
					$max = $this->expiry_storage[ $key ];
				}
			}

			return $max;
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

		private static function generate_transient_name( string $session_id ): string {
			return "nsess_$session_id";
		}

		private static function generate_session_id(): string {
			return time() . ':' . wp_generate_password( 12, false );
		}

		private static function generate_expiration( ?int $timeout = null ): int {
			if ( is_null( $timeout ) || $timeout < 1 ) {
				$timeout = nsess_timeout();
			} else {
				$timeout = min( $timeout, nsess_timeout() );
			}

			return time() + $timeout;
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
