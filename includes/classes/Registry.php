<?php
/**
 * Registry.
 *
 * @package WP_Pulse
 * @subpackage Registry
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Class Registry
 */
class Registry {

	/**
	 * The stored registries.
	 *
	 * @var array
	 */
	private static array $registries = [];

	/**
	 * Get all registries.
	 *
	 * @return array The registries.
	 */
	public static function get_all() {
		return self::$registries;
	}

	/**
	 * Get registry value for key.
	 *
	 * @param string       $key Key string.
	 * @param mixed|string $fallback Fallback value if key is not set.
	 *
	 * @return mixed|string The value, if any.
	 */
	public static function get( string $key, $fallback = '' ) {
		if ( true === isset( self::$registries[ $key ] ) ) {
			return self::$registries[ $key ];
		}

		return $fallback;
	}

	/**
	 * Get registry value for key if it exists, or create a new registry for the key if it doesn't exist.
	 *
	 * @param string $key         Key string.
	 * @param mixed  $start_value A value to start the reigstry. Default is an empty array.
	 *
	 * @return mixed|string Value, if any.
	 */
	public static function get_or_create( string $key, $start_value = [] ) {
		// If key exists on registries.
		if ( true === isset( self::$registries[ $key ] ) ) {
			// Return it.
			return self::$registries[ $key ];
		}

		// Otherwise set it first.
		self::set( $key, $start_value );

		// Then return it.
		return self::get( $key );
	}

	/**
	 * Pop the element off the end of registry array (if it is an array).
	 *
	 * @param string $key Key string.
	 *
	 * @return mixed|void|null
	 */
	public static function pop( string $key ) {
		if ( true === is_array( self::$registries[ $key ] ) ) {
			return array_pop( self::$registries[ $key ] );
		}
	}

	/**
	 * Set registry value for key.
	 *
	 * @param string $key Key string.
	 * @param mixed  $value Any value.
	 */
	public static function set( string $key, $value ) {
		self::$registries[ $key ] = $value;
	}

	/**
	 * Shift an element off the beginning of registry array (if it is an array).
	 *
	 * @param string $key Key string.
	 *
	 * @return mixed|void|null
	 */
	public static function shift( string $key ) {
		if ( false === empty( self::$registries[ $key ] ) && true === is_array( self::$registries[ $key ] ) ) {
			return array_shift( self::$registries[ $key ] );
		}
	}

	/**
	 * Push a new element onto the end of registry array (if it is an array).
	 *
	 * @param string $key Key string.
	 * @param mixed  $value The value to push.
	 * @return mixed|void|null
	 */
	public static function push( string $key, $value ) {
		if ( true === is_array( self::$registries[ $key ] ) ) {
			return array_push( self::$registries[ $key ], $value );
		}
	}
}
