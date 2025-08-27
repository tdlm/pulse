<?php
/**
 * Log class.
 *
 * @package WP_Pulse
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
	 * @return int|false
	 */
	public static function log( $action, $description, $context, $user_id = null ) {
		global $wpdb;

		if ( true === is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$pulse = [
			'action'      => $action,
			'description' => $description,
			'context'     => $context,
			'user_id'     => $user_id,
			'ip'          => filter_var( filter_input( INPUT_SERVER, 'REMOTE_ADDR' ), FILTER_VALIDATE_IP ),
			'created_at'  => current_time( 'mysql', true ),
		];

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$pulse_id = $wpdb->insert( $wpdb->prefix . 'pulse', $pulse );

		if ( true === is_numeric( $pulse_id ) ) {
			return $pulse_id;
		}

		return false;
	}
}
