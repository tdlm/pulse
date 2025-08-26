<?php
/**
 * Pulse for WordPress.
 *
 * @package Pulse
 * @subpackage Pulses
 * @since 1.0.0
 */
namespace WP_Pulse;

class Pulses
{
    /**
     * Load pulses.
     * @return void
     */
    public static function load()
    {
        $pulses = apply_filters('wp_pulse_pulses', [
            'installs'
        ]);

        foreach ($pulses as $pulse) {
            $pulse_class = 'WP_Pulse\\Pulse\\' . $pulse;

            if ( true === class_exists( $pulse_class ) ) {
                $pulse = new $pulse_class();
                $pulse->register();
            } else {
                error_log( sprintf( 'Pulse class %s does not exist.', $pulse_class ) );
            }
        }
    }
}