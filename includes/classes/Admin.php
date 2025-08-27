<?php
/**
 * Admin class.
 *
 * @package WP_Pulse
 */

namespace WP_Pulse;

/**
 * Admin class.
 */
class Admin {

	/**
	 * Add the menu page.
	 *
	 * @return void
	 */
	public static function add_menu_page() {
		add_menu_page(
			'Pulse',
			'Pulse',
			'manage_options',
			'wp-pulse',
			[ self::class, 'render_page' ],
			'dashicons-visibility',
			2.75
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public static function render_page() {
		echo '<div class="wrap"><h1>Pulse</h1></div>';
	}

	/**
	 * Bootstrap the admin.
	 *
	 * @return void
	 */
	public static function bootstrap() {
		add_action( 'admin_menu', [ self::class, 'add_menu_page' ] );
	}
}
