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
    /**
     * Activate the plugin.
     * @return void
     */
    public static function activate()
    {
        // Silence is golden.
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
}
