<?php
/**
 * Keeps a finger on the pulse of install-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Installs
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;

/**
 * Installs class.
 */
class Installs extends Pulse {

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
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
		$plugin_details = $this->get_plugin_details( $slug );

		Log::log(
			'activate_plugin',
			sprintf( 'Plugin %s version %s activated.', $plugin_details['Name'], $plugin_details['Version'] ),
			'plugin',
			null,
			$this->get_plugin_details( $slug )
		);
	}

	/**
	 * Deactivate plugin callback.
	 *
	 * @param string $slug Plugin slug.
	 * @return void
	 */
	public function callback_deactivate_plugin( $slug ) {
		$plugin_details = $this->get_plugin_details( $slug );

		Log::log(
			'deactivate_plugin',
			sprintf( 'Plugin %s version %s deactivated.', $plugin_details['Name'], $plugin_details['Version'] ),
			'plugin',
			null,
			$this->get_plugin_details( $slug )
		);
	}

	/**
	 * Switch theme callback.
	 *
	 * @param string    $slug Theme slug.
	 * @param \WP_Theme $theme Theme details.
	 * @return void
	 */
	public function callback_switch_theme( $slug, $theme ) {
		// error_log( print_r( compact('slug','theme'), true ) );
		error_log( print_r( $theme->get( 'Name' ), true ) );
		error_log( print_r( $theme->get( 'Version' ), true ) );

		$theme_details = [
			'Name'           => $theme->get( 'Name' ),
			'Version'        => $theme->get( 'Version' ),
			'Status'         => $theme->get( 'Status' ),
		];

		Log::log(
			'switch_theme',
			sprintf( 'Theme %s version %s switched.', $theme->get( 'Name' ), $theme->get( 'Version' ) ),
			'theme',
			null,
			$theme_details
		);
	}

	/**
	 * Get plugin detail.
	 *
	 * @param string $slug Plugin slug.
	 * @param string $key Plugin detail key.
	 * @return string Plugin detail value.
	 */
	public function get_plugin_detail( $slug, $key ) {
		$details = $this->get_plugin_details( $slug );

		if ( false === isset( $details[ $key ] ) ) {
			return '';
		}

		return $details[ $key ];
	}

	/**
	 * Get plugin details.
	 *
	 * @param string $slug Plugin slug.
	 * @return array Plugin details.
	 */
	public function get_plugin_details( $slug ) {
		$plugins = $this->get_plugins();

		if ( false === isset( $plugins[ $slug ] ) ) {
			return [];
		}

		return array_filter( $plugins[ $slug ] );
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
