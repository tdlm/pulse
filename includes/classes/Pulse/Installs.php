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
		'_core_updated_successfully',
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
			sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin %1$s version %2$s activated.', 'pulse' ),
				$plugin_details['Name'],
				$plugin_details['Version']
			),
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
			sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin %1$s version %2$s deactivated.', 'pulse' ),
				$plugin_details['Name'],
				$plugin_details['Version']
			),
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
		$theme_details = [
			'Name'    => $theme->get( 'Name' ),
			'Version' => $theme->get( 'Version' ),
			'Status'  => $theme->get( 'Status' ),
		];

		Log::log(
			'switch_theme',
			sprintf(
				/* translators: %1$s: Theme name. %2$s: Theme version. */
				__( 'Theme %1$s version %2$s switched.', 'pulse' ),
				$theme->get( 'Name' ),
				$theme->get( 'Version' )
			),
			'theme',
			null,
			$theme_details
		);
	}

	/**
	 * Core updated successfully callback.
	 *
	 * @param string $new_version New WordPress version.
	 * @return void
	 */
	public function callback__core_updated_successfully( $new_version ) {
		global $pagenow;
		global $wp_version;

		$auto_updated = ( 'update-core.php' !== $pagenow );

		if ( true === $auto_updated ) {
			/* translators: %1$s: New WordPress version. */
			$description = sprintf( __( 'WordPress automatically updated to version %1$s.', 'pulse' ), $new_version );
		} else {
			/* translators: %1$s: New WordPress version. */
			$description = sprintf( __( 'WordPress updated to version %1$s.', 'pulse' ), $new_version );
		}

		Log::log(
			'core_updated_successfully',
			$description,
			'WordPress',
			null,
			[
				'old_version'  => $wp_version,
				'new_version'  => $new_version,
				'auto_updated' => true === $auto_updated ? 'yes' : 'no',
			]
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
