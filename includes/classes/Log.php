<?php
/**
 * Handles logging activity to the database.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Log class.
 */
class Log {

	/**
	 * Log an action.
	 *
	 * @param string   $action The action that was performed.
	 * @param string   $description The description of the action.
	 * @param string   $context The context of the action.
	 * @param int|null $user_id The user ID of the user who performed the action.
	 * @param array    $meta The meta data to log.
	 * @return int|false
	 */
	public static function log( $action, $description, $context, $user_id = null, $meta = [] ) {
		// @var \wpdb $wpdb The WordPress database object.
		global $wpdb;

		if ( true === is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$pulse = [
			'action'         => wp_strip_all_tags( $action ),
			'description'    => wp_strip_all_tags( $description ),
			'context'        => wp_strip_all_tags( $context ),
			'user_id'        => $user_id,
			'ip'             => filter_var( filter_input( INPUT_SERVER, 'REMOTE_ADDR' ), FILTER_VALIDATE_IP ),
			'created_at'     => current_time( 'mysql' ), // Local time.
			'created_at_gmt' => current_time( 'mysql', true ),
		];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert( $wpdb->prefix . 'pulse', $pulse );

		$pulse_id = $wpdb->insert_id;

		if ( false === empty( $meta ) ) {
			foreach ( $meta as $key => $value ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->insert(
					$wpdb->prefix . 'pulse_meta',
					[
						'pulse_id'       => $pulse_id,
						// @phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_key'       => strtolower( $key ),
						// @phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						'meta_value'     => $value,
						'created_at'     => current_time( 'mysql' ), // Local time.
						'created_at_gmt' => current_time( 'mysql', true ),
					]
				);
			}
		}

		if ( true === is_numeric( $pulse_id ) ) {
			return $pulse_id;
		}

		return false;
	}
}
