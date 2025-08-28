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
			'orderby' => 'created_at',
			'order'   => 'DESC',
		];

		$args = wp_parse_args( $args, $defaults );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results(
			$wpdb->prepare(
				/* translators: %d: limit, %d: offset */
				"SELECT * FROM {$table_name} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$args['limit'],
				$args['offset']
			)
		);
	}
}
