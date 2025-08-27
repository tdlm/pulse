<?php
/**
 * Installs pulse.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Installs
 * @since 1.0.0
 */
namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;

class Installs extends Pulse {

	public $actions = [
		'activate_plugin',
		'deactivate_plugin',
		'switch_theme',
	];

	/**
	 * Activate plugin callback.
	 *
	 * @param string $slug Plugin slug.
	 * @return void
	 */
	public function callback_activate_plugin( $slug ) {
		$name = $this->get_plugin_name( $slug );

		Log::log(
			'activate_plugin',
			sprintf( 'Plugin %s activated.', $name ),
			'plugin',
		);
	}

	/**
	 * Deactivate plugin callback.
	 *
	 * @param string $slug Plugin slug.
	 * @return void
	 */
	public function callback_deactivate_plugin( $slug ) {
		$name = $this->get_plugin_name( $slug );

		Log::log(
			'deactivate_plugin',
			sprintf( 'Plugin %s deactivated.', $name ),
			'plugin',
		);
	}

	/**
	 * Switch theme callback.
	 *
	 * @param string $slug Theme slug.
	 * @return void
	 */
	public function callback_switch_theme( $slug ) {
		Log::log(
			'switch_theme',
			sprintf( 'Theme %s switched.', $slug ),
			'theme',
		);
	}

	/**
	 * Get plugin name.
	 *
	 * @param string $slug Plugin slug.
	 * @return string Plugin name or empty string if not found.
	 */
	public function get_plugin_name( $slug ) {
		$plugins = $this->get_plugins();

		if ( false === isset( $plugins[ $slug ], $plugins[ $slug ]['Name'] ) ) {
			return '';
		}

		return $plugins[ $slug ]['Name'];
	}

	/**
	 * Get plugins array.
	 *
	 * @return array
	 */
	public function get_plugins() {
		if ( false === function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins();
	}
}
