<?php
/**
 * Plugin Name: Pulse for WordPress
 * Plugin URI: https://github.com/tdlm/pulse
 * Description: Pulse tracks user activity for logged in users in stunning detail.
 * Version: 1.0.0
 * Author: Scott Weaver
 * Author URI: https://tdlm.github.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pulse
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 *
 * @package Pulse
 */

// Prevent direct access
if (false === defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PULSE_VERSION', '1.0.0');
define('PULSE_PLUGIN_FILE', __FILE__);
define('PULSE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PULSE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PULSE_PLUGIN_BASENAME', plugin_basename(__FILE__));

function pulse_init() {
    echo "<!-- Pulse for WordPress -->";
}

add_action('init', 'pulse_init');
