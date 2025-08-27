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
 * @param string $str The string to convert.
 * @return string The snake case string.
 */
function to_snake_case( string $str ): string {
	return strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', $str ) );
}
