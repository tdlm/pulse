<?php
/**
 * Database.
 *
 * @package WP_Pulse
 */

namespace WP_Pulse;

/**
 * Database class.
 */
class DB {

	/**
	 * Get records.
	 *
	 * @param mixed $args Arguments.
	 *
	 * @return mixed
	 */
	public static function get_records( $args = [] ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'pulse';

		$defaults = [
			'limit'   => 20,
			'offset'  => 0,
			'orderby' => 'created_at_gmt',
			'order'   => 'DESC',
		];

		$args = wp_parse_args( $args, $defaults );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT pulse.*, users.display_name, users.user_email FROM {$table_name} AS pulse LEFT JOIN {$wpdb->users} AS users ON pulse.user_id = users.ID ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
				$args['limit'],
				$args['offset']
			)
		);

		// Enrich results with user data.
		foreach ( $results as $result ) {
			// Get user roles.
			$user_info          = get_userdata( $result->user_id );
			$result->user_roles = true === is_object( $user_info ) && true === property_exists( $user_info, 'roles' ) ? $user_info->roles : [];

			// Get gravatar URL.
			$result->gravatar_url    = get_avatar_url( $result->user_email, [ 'size' => 80 ] );
			$result->gravatar_url_2x = get_avatar_url( $result->user_email, [ 'size' => 160 ] );
		}

		return $results;
	}
}
