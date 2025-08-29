<?php
/**
 * String functionality helpers.
 *
 * @package WP_Pulse
 * @subpackage Helpers\Strings
 * @since 1.0.0
 */

namespace WP_Pulse\Helpers\Users;

/**
 * Get the label for a user role.
 *
 * @param string $role The role to get the label for.
 * @return string The label for the role.
 */
function get_user_role_label( string $role ): string {
	global $wp_roles;

	if ( true === isset( $wp_roles->role_names[ $role ] ) ) {
		return $wp_roles->role_names[ $role ];
	}

	return $role;
}
