<?php
/**
 * Pulse core functionality and actions.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Core class.
 */
class Core extends Singleton {

	/**
	 * The option key for the database version.
	 *
	 * @var string
	 */
	public static $option_key_db_version = 'wp_pulse_version_db';

	/**
	 * Activate the plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		Install::migrate();
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// Silence is golden.
	}

	/**
	 * Bootstrap the plugin.
	 *
	 * @return void
	 */
	public static function init() {
		Pulses::load();
	}

	/**
	 * Get the plugin's database version.
	 *
	 * @return string
	 */
	public static function get_db_version() {
		return get_option( self::$option_key_db_version, '' );
	}

	/**
	 * Automatically register JavaScript and CSS assets.
	 *
	 * @action init
	 *
	 * @return void
	 */
	public static function auto_register_assets() {
		$asset_root = PULSE_PLUGIN_DIR . 'assets/build/';
		$asset_uri  = PULSE_PLUGIN_URL . 'assets/build/';

		$asset_files = glob( $asset_root . '*.asset.php' );

		// Enqueue runtime.js, if it exists.
		if ( true === is_readable( $asset_root . 'runtime.js' ) ) {
			Helpers\Media\enqueue_script(
				'pulse/runtime',
				[],
				$asset_uri . 'runtime.js',
				[],
				filemtime( $asset_root . 'runtime.js' )
			);
		}

		foreach ( $asset_files as $asset_file ) {
			$asset_script = require $asset_file;

			$asset_filename = basename( $asset_file );

			$asset_slug_parts = explode( '.asset.php', $asset_filename );
			$asset_slug       = array_shift( $asset_slug_parts );

			$asset_handle = sprintf( 'pulse/%s', $asset_slug );

			$stylesheet_path = $asset_root . $asset_slug . '.css';
			$stylesheet_uri  = $asset_uri . $asset_slug . '.css';

			$javascript_path = $asset_root . $asset_slug . '.js';
			$javascript_uri  = $asset_uri . $asset_slug . '.js';

			if ( true === is_readable( $stylesheet_path ) ) {
				// Filter dependencies to only include registered styles.
				$style_dependencies = array_filter(
					$asset_script['dependencies'],
					function ( $dep ) {
						return wp_style_is( $dep, 'registered' );
					}
				);

				wp_register_style(
					$asset_handle,
					$stylesheet_uri,
					$style_dependencies,
					$asset_script['version']
				);
			}

			if ( true === is_readable( $javascript_path ) ) {
				// Filter dependencies to only include registered scripts.
				$script_dependencies_before = $asset_script['dependencies'];
				$script_dependencies_after  = array_filter(
					$asset_script['dependencies'],
					function ( $dep ) {
						return wp_script_is( $dep, 'registered' );
					}
				);

				wp_register_script(
					$asset_handle,
					$javascript_uri,
					$asset_script['dependencies'],
					$asset_script['version'],
					[
						'in_footer' => false,
					]
				);
			}
		}
	}
}
