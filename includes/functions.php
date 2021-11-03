<?php
/**
 * NSESS: functions.php
 */

/* ABSPATH check skipped because of phpunit */

if ( ! function_exists( 'nsess' ) ) {
	/**
	 * NSESS_Main alias.
	 *
	 * @return NSESS_Main
	 */
	function nsess(): NSESS_Main {
		return NSESS_Main::get_instance();
	}
}


if ( ! function_exists( 'nsess_parse_module' ) ) {
	/**
	 * Retrieve submodule by given string notaion.
	 *
	 * @param string $module_notation
	 *
	 * @return object|false;
	 */
	function nsess_parse_module( string $module_notation ) {
		return nsess()->get_module_by_notation( $module_notation );
	}
}


if ( ! function_exists( 'nsess_parse_callback' ) ) {
	/**
	 * Return submodule's callback method by given string notation.
	 *
	 * @param Closure|array|string $maybe_callback
	 *
	 * @return callable|array|string
	 * @throws NSESS_Callback_Exception
	 * @example foo.bar@baz ---> array( nsess()->foo->bar, 'baz )
	 */
	function nsess_parse_callback( $maybe_callback ) {
		return nsess()->parse_callback( $maybe_callback );
	}
}


if ( ! function_exists( 'nsess_option' ) ) {
	/**
	 * Alias function for option.
	 *
	 * @return NSESS_Register_Option|null
	 */
	function nsess_option(): ?NSESS_Register_Option {
		return nsess()->registers->option;
	}
}


if ( ! function_exists( 'nsess_comment_meta' ) ) {
	/**
	 * Alias function for comment meta.
	 *
	 * @return NSESS_Register_Comment_Meta|null
	 */
	function nsess_comment_meta(): ?NSESS_Register_Comment_Meta {
		return nsess()->registers->comment_meta;
	}
}


if ( ! function_exists( 'nsess_post_meta' ) ) {
	/**
	 * Alias function for post meta.
	 *
	 * @return NSESS_Register_Post_Meta|null
	 */
	function nsess_post_meta(): ?NSESS_Register_Post_Meta {
		return nsess()->registers->post_meta;
	}
}


if ( ! function_exists( 'nsess_term_meta' ) ) {
	/**
	 * Alias function for term meta.
	 *
	 * @return NSESS_Register_Term_Meta|null
	 */
	function nsess_term_meta(): ?NSESS_Register_Term_Meta {
		return nsess()->registers->term_meta;
	}
}


if ( ! function_exists( 'nsess_user_meta' ) ) {
	/**
	 * Alias function for user meta.
	 *
	 * @return NSESS_Register_User_Meta|null
	 */
	function nsess_user_meta(): ?NSESS_Register_User_Meta {
		return nsess()->registers->user_meta;
	}
}


if ( ! function_exists( 'nsess_script_debug' ) ) {
	/**
	 * Return SCRIPT_DEBUG.
	 *
	 * @return bool
	 */
	function nsess_script_debug(): bool {
		return apply_filters( 'nsess_script_debug', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
	}
}


if ( ! function_exists( 'nsess_format_callback' ) ) {
	/**
	 * Format callback method or function.
	 *
	 * This method does not care about $callable is actually callable.
	 *
	 * @param Closure|array|string $callback
	 *
	 * @return string
	 */
	function nsess_format_callback( $callback ): string {
		if ( is_string( $callback ) ) {
			return $callback;
		} elseif (
			( is_array( $callback ) && 2 === count( $callback ) ) &&
			( is_object( $callback[0] ) || is_string( $callback[0] ) ) &&
			is_string( $callback[1] )
		) {
			if ( method_exists( $callback[0], $callback[1] ) ) {
				try {
					$ref = new ReflectionClass( $callback[0] );
					if ( $ref->isAnonymous() ) {
						return "{AnonymousClass}::{$callback[1]}";
					}
				} catch ( ReflectionException $e ) {
				}
			}

			if ( is_string( $callback[0] ) ) {
				return "{$callback[0]}::{$callback[1]}";
			} elseif ( is_object( $callback[0] ) ) {
				return get_class( $callback[0] ) . '::' . $callback[1];
			}
		} elseif ( $callback instanceof Closure ) {
			return '{Closure}';
		}

		return '{Unknown}';
	}
}
