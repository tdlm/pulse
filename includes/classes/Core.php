<?php
/**
 * Pulse for WordPress.
 *
 * @package WP_Pulse
 * @subpackage Core
 * @since 1.0.0
 */
namespace WP_Pulse;

class Core
{
    public static $option_key_db_version = 'wp_pulse_version_db';

    /**
     * Activate the plugin.
     * @return void
     */
    public static function activate()
    {
        // Silence is golden.
        Install::migrate();
    }

    /**
     * Deactivate the plugin.
     * @return void
     */
    public static function deactivate()
    {
        // Silence is golden.
    }

    /**
     * Bootstrap the plugin.
     * @return void
     */
    public static function bootstrap()
    {
        Pulses::load();
    }

    public static function get_db_version() {
        return get_option(self::$option_key_db_version, '');
    }
}
