<?php
/**
 * Keeps a finger on the pulse of menu-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Menus
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Log;
use WP_Pulse\Pulse;

/**
 * Menus class.
 */
class Menus extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'menus';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'wp_create_nav_menu',
		'wp_delete_nav_menu',
		'wp_update_nav_menu',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'menu-created' => __( 'Created', 'pulse' ),
		];
	}

	/**
	 * Callback for wp_create_nav_menu.
	 *
	 * @param int $menu_id The menu ID.
	 * @return void
	 */
	public function callback_wp_create_nav_menu( $menu_id ) {
		$current_user = wp_get_current_user();

		Log::log(
			'menu-created',
			sprintf(
				/* translators: %s: Menu ID. */
				__( 'Menu "%s" created.', 'pulse' ),
				$menu_id
			),
			$this->pulse_slug,
			$menu_id,
			$current_user->ID,
		);
	}

	/**
	 * Callback for delete_nav_menu.
	 *
	 * @param int $menu_id The menu ID.
	 * @return void
	 */
	public function callback_delete_nav_menu( $menu_id ) {
		$current_user = wp_get_current_user();

		Log::log(
			'menu-deleted',
			sprintf(
				/* translators: %s: Menu ID. */
				__( 'Menu "%s" deleted.', 'pulse' ),
				$menu_id
			),
			$this->pulse_slug,
			$menu_id,
			$current_user->ID,
		);
	}

	/**
	 * Callback for wp_update_nav_menu.
	 *
	 * @param int $menu_id The menu ID.
	 * @return void
	 */
	public function callback_wp_update_nav_menu( $menu_id ) {
		$current_user = wp_get_current_user();

		Log::log(
			'menu-updated',
			sprintf(
				/* translators: %s: Menu ID. */
				__( 'Menu "%s" updated.', 'pulse' ),
				$menu_id
			),
			$this->pulse_slug,
			$menu_id,
			$current_user->ID,
		);
	}
}
