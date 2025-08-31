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
	 * @throws \Exception If a pulse class is missing a required method.
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

				// Make sure get_labels() is defined on the class.
				if ( false === method_exists( $pulse_class, 'get_labels' ) ) {
					Admin::notice( sprintf( 'Class %s must define a get_labels() method', $pulse_class ), 'error' );
				}

				// Make sure the register() method is defined on the class.
				if ( false === method_exists( $pulse_class, 'register' ) ) {
					Admin::notice( sprintf( 'Class %s must define a register() method', $pulse_class ), 'error' );
				}

				$pulse->register();
			}
		}
	}
}
