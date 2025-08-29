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
 *
 * @package Pulse
 */

// Prevent direct access.
if ( false === defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PULSE_VERSION', '1.0.0' );
define( 'PULSE_PLUGIN_FILE', __FILE__ );
define( 'PULSE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PULSE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PULSE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Load helpers.
require_once __DIR__ . '/includes/helpers/media.php';
require_once __DIR__ . '/includes/helpers/strings.php';
require_once __DIR__ . '/includes/helpers/users.php';

// Register autoloader.
spl_autoload_register(
	function ( $class_name ) {
		$prefix   = 'WP_Pulse\\';
		$base_dir = __DIR__ . '/includes/classes/';
		$len      = strlen( $prefix );

		if ( 0 !== strncmp( $prefix, $class_name, $len ) ) {
			return;
		}

		$relative_class = substr( $class_name, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( true === file_exists( $file ) ) {
			require $file;
		}
	}
);

// Activation/Deactivation.
register_activation_hook( __FILE__, [ '\WP_Pulse\Core', 'activate' ] );
register_deactivation_hook( __FILE__, [ '\WP_Pulse\Core', 'deactivate' ] );

// Bootstrap the plugin.
\WP_Pulse\Core::instance();
\WP_Pulse\API::instance();

if ( true === is_admin() ) {
	\WP_Pulse\Admin::instance();
}

// Load WP-CLI command.
if ( true === defined( 'WP_CLI' ) && true === WP_CLI ) {
	\WP_CLI::add_command( 'pulse', 'WP_Pulse\CLI' );
	\WP_CLI::add_command( 'pulse db', 'WP_Pulse\CLI\DB' );
}
