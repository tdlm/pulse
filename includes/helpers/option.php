<?php
/**
 * Option functionality helpers.
 *
 * @package WP_Pulse
 * @subpackage Helpers\Option
 * @since 1.0.0
 */

namespace WP_Pulse\Helpers\Option;

/**
 * Get changed keys.
 *
 * @param mixed $old_value Old value.
 * @param mixed $new_value New value.
 * @param bool  $deep Deep.
 *
 * @return array
 */
function get_changed_keys( $old_value, $new_value, $deep = false ) {
	if ( false === is_array( $old_value ) && false === is_array( $new_value ) ) {
		return [];
	}

	if ( false === is_array( $old_value ) ) {
		return array_keys( $new_value );
	}

	if ( false === is_array( $new_value ) ) {
		return array_keys( $old_value );
	}

	$diff = array_udiff_assoc(
		$old_value,
		$new_value,
		function ( $value1, $value2 ) {
			return wp_json_encode( $value1 ) !== wp_json_encode( $value2 );
		}
	);

	$result = array_keys( $diff );

	$common_keys     = array_keys( array_intersect_key( $old_value, $new_value ) );
	$unique_keys_old = array_values( array_diff( array_keys( $old_value ), $common_keys ) );
	$unique_keys_new = array_values( array_diff( array_keys( $new_value ), $common_keys ) );

	$result = array_merge( $result, $unique_keys_old, $unique_keys_new );

	$result = array_filter(
		$result,
		function ( $value ) {
			return (string) (int) $value !== (string) $value;
		}
	);

	$result = array_values( array_unique( $result ) );

	if ( false === $deep ) {
		return $result;
	}

	$result = array_fill_keys( $result, null );

	foreach ( $result as $key => $val ) {
		if ( true === in_array( $key, $unique_keys_old, true ) ) {
			$result[ $key ] = false;
		} elseif ( true === in_array( $key, $unique_keys_new, true ) ) {
			$result[ $key ] = true;
		} elseif ( $deep ) {
			if ( true === is_array( $old_value[ $key ] ) && true === is_array( $new_value[ $key ] ) ) {
				$inner  = [];
				$parent = $key;
				--$deep;
				$changed = get_changed_keys( $old_value[ $key ], $new_value[ $key ], $deep );
				foreach ( $changed as $child => $change ) {
					$inner[ $parent . '::' . $child ] = $change;
				}
				$result[ $key ] = 0;
				$result         = array_merge( $result, $inner );
			}
		}
	}

	return $result;
}

/**
 * Should ignore option.
 *
 * @param string $option_name Option name.
 *
 * @return bool
 */
function should_ignore_option( string $option_name ) {
	if ( 0 === stripos( $option_name, '_transient_' ) ) {
		return true;
	}

	if ( 0 === stripos( $option_name, '_site_transient_' ) ) {
		return true;
	}

	/**
	 * Default ignored options.
	 *
	 * @param array $default_ignored_options Default ignored options.
	 * @param string $option_name Option name.
	 * @return array
	 */
	$default_ignored_options = apply_filters(
		'pulse_should_ignore_option',
		[
			'image_default_link_type',
			'medium_large_size_h',
			'medium_large_size_w',
		],
		$option_name,
	);

	return true === in_array( $option_name, $default_ignored_options, true );
}

/**
 * Is option group.
 *
 * @param mixed $value Value.
 *
 * @return bool
 */
function is_option_group( $value ) {
	if ( false === is_array( $value ) ) {
		return false;
	}

	if ( 0 === count( array_filter( array_keys( $value ), 'is_string' ) ) ) {
		return false;
	}

	return true;
}
