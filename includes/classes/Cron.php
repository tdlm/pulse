<?php
/**
 * Pulse cron functionality.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Cron class.
 */
class Cron extends Singleton {

	/**
	 * Set up the schedules.
	 *
	 * @return void
	 *
	 * @action wp_loaded
	 */
	public function set_up_schedules() {
		if ( false === wp_next_scheduled( 'wp_pulse_purge_records' ) ) {
			wp_schedule_event( time(), 'daily', 'wp_pulse_purge_records' );
		}
	}

	/**
	 * Purge records.
	 *
	 * @return void
	 *
	 * @action wp_pulse_purge_records
	 */
	public function purge_records() {
		$keep_forever = 1 === Admin::get_setting( 'general', 'keep_forever', 0 );
		$keep_days    = Admin::get_setting( 'general', 'keep_days', 30 );

		// If keep forever is enabled, return.
		if ( true === $keep_forever ) {
			return;
		}

		// If keep days is less than 1, return.
		if ( 1 > $keep_days ) {
			return;
		}

		Database::delete_records_before_days_ago( $keep_days );
	}
}
