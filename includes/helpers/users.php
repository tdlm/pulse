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
 * @since 1.0.0
 *
 * @param string $role The role to get the label for.
 *
 * @return string The label for the role.
 */
function get_user_role_label( string $role ): string {
	global $wp_roles;

	if ( true === isset( $wp_roles->role_names[ $role ] ) ) {
		return $wp_roles->role_names[ $role ];
	}

	return $role;
}

/**
 * Get the details for a user.
 *
 * @since 1.0.0
 *
 * @param int $user_id The user ID.
 *
 * @return array The user details.
 */
function get_user_details( $user_id ) {
	$user = get_user_by( 'ID', $user_id );

	if ( false === $user instanceof \WP_User ) {
		return [];
	}

	return [
		'user_id'           => $user->ID,
		'user_login'        => $user->user_login,
		'user_email'        => $user->user_email,
		'user_display_name' => $user->display_name,
		'user_roles'        => implode(
			', ',
			array_map(
				function ( $role ) {
					return get_user_role_label( $role );
				},
				$user->roles
			)
		),
		'user_gravatar_url' => get_avatar_url( $user->user_email, [ 'size' => 80 ] ),
	];
}
