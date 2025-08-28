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
		$hook = add_menu_page(
			'Pulse',
			'Pulse',
			'manage_options',
			'wp-pulse',
			[ self::class, 'render_page' ],
			'dashicons-visibility',
			2.75
		);

		add_action( "load-$hook", [ self::class, 'add_screen_options' ] );
	}

	/**
	 * Add screen options (per-page setting).
	 *
	 * @return void
	 */
	public static function add_screen_options() {
		add_screen_option(
			'per_page',
			[
				'label'   => __( 'Pulse records per page', 'wp-pulse' ),
				'default' => 20,
				'option'  => 'pulse_per_page',
			]
		);
	}

	/**
	 * Hook for saving the per-page option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @return mixed
	 *
	 * @filter set-screen-option
	 */
	public static function set_screen_option( $status, $option, $value ) {
		if ( 'pulse_per_page' === $option ) {
			return (int) $value;
		}

		return $status;
	}

	/**
	 * Save the screen options.
	 *
	 * @action init
	 *
	 * @return void
	 */
	public function save_screen_options() {
		$enable_live_update_nonce = filter_input( INPUT_POST, 'pulse_enable_live_update_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$enable_live_update_user  = filter_input( INPUT_POST, 'pulse_enable_live_update_user', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$enable_live_update       = filter_input( INPUT_POST, 'pulse_enable_live_update', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$wp_screen_options        = filter_input( INPUT_POST, 'wp_screen_options', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		if ( false === empty( $enable_live_update_nonce ) && false === empty( $wp_screen_options ) ) {
			// TODO: Bring back nonce check.
			update_user_option(
				$enable_live_update_user,
				'pulse_live_update',
				'on' === $enable_live_update ? 'on' : 'off',
				true
			);
		}
	}

	/**
	 * Modify the screen controls.
	 *
	 * @param mixed $status Status.
	 * @param mixed $args   Args.
	 *
	 * @filter screen_settings
	 *
	 * @return string
	 */
	public function modify_screen_controls( $status, $args ) {
		unset( $status, $args );

		$user_id   = get_current_user_id();
		$heartbeat = 120;
		$option    = get_user_option( 'pulse_live_update', $user_id );
		$nonce     = wp_create_nonce( 'pulse_enable_live_update_nonce' );

		return View::render_template(
			'admin/dashboard-screen-controls',
			compact(
				'nonce',
				'user_id',
				'heartbeat',
				'option',
			)
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public static function render_page() {
		$user_id  = get_current_user_id();
		$per_page = get_user_option( 'pulse_per_page', $user_id );

		if ( false === is_numeric( $per_page ) ) {
			$per_page = 20;
		}

		$records = DB::get_records(
			[
				'limit' => $per_page,
			]
		);

		Helpers\Media\enqueue_script(
			'pulse/admin-dashboard',
			[
				[
					'object_name' => 'PulseAdminDashboard',
					'value'       => [
						'records' => $records,
					],
				],
			]
		);
		Helpers\Media\enqueue_style( 'pulse/admin-dashboard' );

		View::include_template( 'admin/dashboard' );
	}
}
