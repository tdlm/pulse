<?php
/**
 * Responsible for registering all the pulses.
 *
 * @package WP_Pulse
 * @subpackage Pulses
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Pulses class.
 */
class Pulses {

	/**
	 * Load pulses.
	 *
	 * @return void
	 */
	public static function load() {
		$pulses = apply_filters(
			'wp_pulse_pulses',
			[
				'Installs',
				'Posts',
				'UserSwitching',
			]
		);

		foreach ( $pulses as $pulse ) {
			$pulse_class = 'WP_Pulse\\Pulse\\' . $pulse;

			if ( true === class_exists( $pulse_class ) ) {
				$pulse = new $pulse_class();
				$pulse->register();
			}
		}
	}
}
