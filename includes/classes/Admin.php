<?php
/**
 * Where all the admin stuff happens.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Admin class.
 */
class Admin extends Singleton {

	/**
	 * Add the menu page.
	 *
	 * @action admin_menu
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
		Helpers\Media\enqueue_script( 'pulse/admin' );
		Helpers\Media\enqueue_style( 'pulse/admin' );

		View::include_template( 'admin/dashboard' );
	}
}
