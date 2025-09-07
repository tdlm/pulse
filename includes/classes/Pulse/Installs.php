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
use WP_Pulse\Registry;

/**
 * Installs class.
 */
class Installs extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'installs';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'_core_updated_successfully',
		'activate_plugin',
		'deactivate_plugin',
		'delete_plugin',
		'deleted_plugin',
		'delete_theme',
		'deleted_theme',
		'switch_theme',
		'upgrader_process_complete',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'activated'          => __( 'Activated', 'pulse' ),
			'core'               => __( 'Core', 'pulse' ),
			'deactivated'        => __( 'Deactivated', 'pulse' ),
			'installs'           => __( 'Installs', 'pulse' ),
			'plugin-deleted'     => __( 'Deleted', 'pulse' ),
			'plugin-installed'   => __( 'Installed', 'pulse' ),
			'plugin-not-deleted' => __( 'Not Deleted', 'pulse' ),
			'plugin-updated'     => __( 'Updated', 'pulse' ),
			'plugin'             => __( 'Plugin', 'pulse' ),
			'theme-deleted'      => __( 'Deleted', 'pulse' ),
			'theme-installed'    => __( 'Installed', 'pulse' ),
			'theme-updated'      => __( 'Updated', 'pulse' ),
			'theme'              => __( 'Theme', 'pulse' ),
			'updated'            => __( 'Updated', 'pulse' ),
		];
	}

	/**
	 * Activate plugin callback.
	 *
	 * @param string $slug Plugin slug.
	 * @return void
	 */
	public function callback_activate_plugin( $slug ) {
		$plugin_details = $this->get_plugin_details( $slug );

		Log::log(
			'activated',
			sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin %1$s version %2$s activated.', 'pulse' ),
				$plugin_details['Name'],
				$plugin_details['Version']
			),
			$this->pulse_slug,
			'plugin',
			null,
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
			'deactivated',
			sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin %1$s version %2$s deactivated.', 'pulse' ),
				$plugin_details['Name'],
				$plugin_details['Version']
			),
			$this->pulse_slug,
			'plugin',
			null,
			null,
			$this->get_plugin_details( $slug )
		);
	}

	/**
	 * Delete plugin callback.
	 *
	 * @param string $slug Plugin slug.
	 * @return void
	 */
	public function callback_delete_plugin( $slug ) {
		$plugin_details = $this->get_plugin_details( $slug );

		if ( true === empty( $plugin_details ) ) {
			return;
		}

		Registry::set( 'deleted_plugin_details', $plugin_details );
	}

	/**
	 * Deleted plugin callback.
	 *
	 * @param string $plugin_file Plugin file.
	 * @param bool   $deleted Whether the plugin was deleted.
	 * @return void
	 */
	public function callback_deleted_plugin( $plugin_file, $deleted ) {
		$deleted_plugin_details = Registry::get( 'deleted_plugin_details', [] );

		if ( true === empty( $deleted_plugin_details ) ) {
			return;
		}

		if ( true === $deleted ) {
			$action  = 'plugin-deleted';
			$message = sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin %1$s version %2$s deleted.', 'pulse' ),
				$deleted_plugin_details['Name'],
				$deleted_plugin_details['Version']
			);
		} else {
			$action  = 'plugin-not-deleted';
			$message = sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin %1$s version %2$s failed to delete.', 'pulse' ),
				$deleted_plugin_details['Name'],
				$deleted_plugin_details['Version']
			);
		}

		Log::log(
			$action,
			$message,
			$this->pulse_slug,
			'plugin',
			null,
			null,
			$deleted_plugin_details
		);
	}

	/**
	 * Delete theme callback.
	 *
	 * @param string $slug Theme slug.
	 * @return void
	 */
	public function callback_delete_theme( $slug ) {
		$theme_details = $this->get_theme_details( $slug );

		if ( true === empty( $theme_details ) ) {
			return;
		}

		Registry::set( 'deleted_theme_details', $theme_details );
	}

	/**
	 * Deleted theme callback.
	 *
	 * @param string $slug Theme slug.
	 * @return void
	 */
	public function callback_deleted_theme( $slug ) {
		$deleted_theme_details = Registry::get( 'deleted_theme_details', [] );

		if ( true === empty( $deleted_theme_details ) ) {
			return;
		}

		Log::log(
			'theme-deleted',
			sprintf(
				/* translators: %1$s: Theme name. %2$s: Theme version. */
				__( 'Theme "%1$s" version %2$s deleted.', 'pulse' ),
				$deleted_theme_details['Name'],
				$deleted_theme_details['Version']
			),
			$this->pulse_slug,
			'theme',
			null,
			null,
			$deleted_theme_details
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
			'activated',
			sprintf(
				/* translators: %1$s: Theme name. %2$s: Theme version. */
				__( 'Theme "%1$s" version %2$s activated.', 'pulse' ),
				$theme->get( 'Name' ),
				$theme->get( 'Version' )
			),
			$this->pulse_slug,
			'theme',
			null,
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
			'updated',
			$description,
			$this->pulse_slug,
			'core',
			null,
			null,
			[
				'old_version'  => $wp_version,
				'new_version'  => $new_version,
				'auto_updated' => true === $auto_updated ? 'yes' : 'no',
			]
		);
	}

	/**
	 * Plugin installed successfully callback.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 * @param array        $extra The extra data.
	 * @return void
	 */
	public function callback_upgrader_process_complete( $upgrader, $extra ) {
		// If the type or action is not set, we don't care.
		if ( false === isset( $extra['type'] ) || false === isset( $extra['action'] ) ) {
			return;
		}

		// We only care about plugins and themes.
		if ( false === in_array( $extra['type'], [ 'plugin', 'theme' ], true ) ) {
			return;
		}

		// We only care about install and update actions.
		if ( false === in_array( $extra['action'], [ 'install', 'update' ], true ) ) {
			return;
		}

		if ( 'install' === $extra['action'] && 'plugin' === $extra['type'] ) {
			$this->handle_plugin_install( $upgrader );
		} elseif ( 'update' === $extra['action'] && 'plugin' === $extra['type'] ) {
			$this->handle_plugin_update( $upgrader );
		} elseif ( 'install' === $extra['action'] && 'theme' === $extra['type'] ) {
			$this->handle_theme_install( $upgrader );
		} elseif ( 'update' === $extra['action'] && 'theme' === $extra['type'] ) {
			$this->handle_theme_update( $upgrader );
		}
	}

	/**
	 * Handle plugin install.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 * @return void
	 */
	private function handle_plugin_install( $upgrader ) {
		$plugin_path = $upgrader->plugin_info();

		if ( true === empty( $plugin_path ) ) {
			[];
		}

		// Clear the plugins cache so we can get that fresh data.
		wp_clean_plugins_cache();

		$plugin_details = $this->get_plugin_details( $plugin_path );

		Log::log(
			'plugin-installed',
			sprintf(
				/* translators: %1$s: Plugin name. %2$s: Plugin version. */
				__( 'Plugin "%1$s" version %2$s installed.', 'pulse' ),
				$plugin_details['Name'],
				$plugin_details['Version']
			),
			$this->pulse_slug,
			'plugin',
			null,
			null,
			$plugin_details
		);
	}

	/**
	 * Handle plugin install bulk.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 *
	 * @return void
	 */
	private function handle_plugin_update( $upgrader ) {
		$plugin_slug = $upgrader->plugin_info();

		if ( true === empty( $plugin_slug ) ) {
			return;
		}

		$old_plugin_data = $upgrader->skin->plugin_info;
		$new_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_slug );

		Log::log(
			'plugin-updated',
			sprintf(
				/* translators: %1$s: Plugin name. %2$s: Old plugin version. %3$s: New plugin version. */
				__( 'Plugin "%1$s" updated from version %2$s to %3$s.', 'pulse' ),
				$new_plugin_data['Name'],
				$old_plugin_data['Version'],
				$new_plugin_data['Version']
			),
			$this->pulse_slug,
			'plugin',
			null,
			null,
			array_merge(
				array_filter( $new_plugin_data ),
				[
					'old_version' => $old_plugin_data['Version'],
				]
			)
		);
	}

	/**
	 * Handle theme install.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 * @return void
	 */
	private function handle_theme_install( $upgrader ) {
		$theme_slug = $upgrader->theme_info();

		if ( true === empty( $theme_slug ) ) {
			return;
		}

		// Clear the themes cache so we can get that fresh data.
		wp_clean_themes_cache();

		$theme_details = $this->get_theme_details( $theme_slug );

		Log::log(
			'theme-installed',
			sprintf(
				/* translators: %1$s: Theme name. %2$s: Theme version. */
				__( 'Theme "%1$s" version %2$s installed.', 'pulse' ),
				$theme_details['Name'],
				$theme_details['Version']
			),
			$this->pulse_slug,
			'theme',
			null,
			null,
			$theme_details
		);
	}

	/**
	 * Handle theme update.
	 *
	 * @param \WP_Upgrader $upgrader The upgrader object.
	 * @return void
	 */
	private function handle_theme_update( $upgrader ) {
		$theme_path = $upgrader->theme_info();

		if ( true === empty( $theme_path ) ) {
			[];
		}

		$new_theme_data = $this->get_theme_details( $theme_path );
		$old_theme_data = $upgrader->skin->theme_info;

		Log::log(
			'theme-updated',
			sprintf(
				/* translators: %1$s: Theme name. %2$s: Theme version. */
				__( 'Theme "%1$s" updated from version %2$s to %3$s.', 'pulse' ),
				$new_theme_data['Name'],
				$old_theme_data['Version'],
				$new_theme_data['Version']
			),
			$this->pulse_slug,
			'theme',
			null,
			null,
			array_merge(
				$new_theme_data,
				[
					'old_version' => $old_theme_data['Version'],
				]
			)
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
	 * Get theme details.
	 *
	 * @param string $slug Theme slug.
	 * @return array Theme details.
	 */
	public function get_theme_details( $slug ) {
		$theme = wp_get_theme( $slug );

		if ( false === $theme->exists() ) {
			return [];
		}

		return [
			'Name'    => $theme->get( 'Name' ),
			'Status'  => $theme->get( 'Status' ),
			'Version' => $theme->get( 'Version' ),
		];
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
