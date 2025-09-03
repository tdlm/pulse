<?php
/**
 * String functionality helpers.
 *
 * @package WP_Pulse
 * @subpackage Helpers\Strings
 * @since 1.0.0
 */

namespace WP_Pulse\Helpers\Strings;

/**
 * Convert a string to snake case.
 *
 * @since 1.0.0
 *
 * @param string $str The string to convert.
 *
 * @return string The snake case string.
 */
function to_snake_case( string $str ): string {
	return strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', $str ) );
}

/**
 * Convert a string to camel case.
 *
 * @since 1.0.0
 *
 * @param string $str The string to convert.
 *
 * @return string The camel case string.
 */
function to_camel_case( string $str ): string {
	return lcfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $str ) ) ) );
}

/**
 * Convert a string to pascal case.
 *
 * @since 1.0.0
 *
 * @param string $str The string to convert.
 *
 * @return string The pascal case string.
 */
function to_pascal_case( string $str ): string {
	return ucfirst( str_replace( ' ', '', ucwords( str_replace( '_', ' ', $str ) ) ) );
}
