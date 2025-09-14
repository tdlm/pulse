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
			'footer-menu'    => __( 'Footer Menu', 'pulse' ),
			'menu-created'   => __( 'Created', 'pulse' ),
			'menu-deleted'   => __( 'Deleted', 'pulse' ),
			'menu-updated'   => __( 'Updated', 'pulse' ),
			'menu'           => __( 'Menu', 'pulse' ),
			'menus'          => __( 'Menus', 'pulse' ),
			'primary-menu'   => __( 'Primary Menu', 'pulse' ),
			'secondary-menu' => __( 'Secondary Menu', 'pulse' ),
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

		$menu = get_term( $menu_id, 'nav_menu' );

		Log::log(
			'menu-created',
			sprintf(
				/* translators: %s: Menu ID. */
				__( 'Menu "%s" created.', 'pulse' ),
				$menu->name
			),
			$this->pulse_slug,
			$menu->name,
			$current_user->ID,
			$menu_id,
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

		$menu = get_term( $menu_id, 'nav_menu' );

		Log::log(
			'menu-deleted',
			sprintf(
				/* translators: %s: Menu ID. */
				__( 'Menu "%s" deleted.', 'pulse' ),
				$menu->name
			),
			$this->pulse_slug,
			$menu->name,
			$current_user->ID,
			$menu_id,
		);
	}

	/**
	 * Callback for wp_update_nav_menu.
	 *
	 * @param array ...$args The arguments.
	 * @return void
	 */
	public function callback_wp_update_nav_menu( ...$args ) {
		$current_user = wp_get_current_user();

		// Extract args this way since we sporadically get the menu data.
		$menu_id   = $args[0] ?? null;
		$menu_data = $args[1] ?? [];

		// If we don't have a menu ID, we don't care.
		if ( true === empty( $menu_id ) ) {
			return;
		}

		$menu = get_term( $menu_id, 'nav_menu' );

		Log::log(
			'menu-updated',
			sprintf(
				/* translators: %s: Menu ID. */
				__( 'Menu "%s" updated.', 'pulse' ),
				$menu->name
			),
			$this->pulse_slug,
			$menu->slug,
			$current_user->ID,
			$menu_id,
			$menu_data
		);
	}
}
