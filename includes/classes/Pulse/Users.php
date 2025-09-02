<?php
/**
 * Keeps a finger on the pulse of user-related activity.
 *
 * @package WP_Pulse
 * @subpackage Pulse\Users
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;

/**
 * Users class.
 */
class Users extends Pulse {

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'clear_auth_cookie',
		'set_logged_in_cookie',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'log-in'  => __( 'Log in', 'pulse' ),
			'log-out' => __( 'Log out', 'pulse' ),
			'users'   => __( 'Users', 'pulse' ),
			'session' => __( 'Session', 'pulse' ),
		];
	}

	/**
	 * Callback for clear_auth_cookie.
	 *
	 * @return void
	 */
	public function callback_clear_auth_cookie() {
		$user_id = get_current_user_id();
		$user    = get_user_by( 'ID', $user_id );

		if ( false === $user instanceof \WP_User ) {
			return;
		}

		if ( false === $user->exists() ) {
			return;
		}

		Log::log(
			'log-out',
			sprintf(
				/* translators: %s: User display name. */
				__( '%s logged out', 'pulse' ),
				$user->display_name
			),
			'users',
			'session',
			$user->ID,
			$user->ID,
			[]
		);
	}

	/**
	 * Set logged in cookie callback.
	 *
	 * @param string $logged_in_cookie The logged in cookie.
	 * @param int    $expire The expire time.
	 * @param int    $expiration The expiration time.
	 * @param int    $user_id The user ID.
	 *
	 * @return void
	 */
	public function callback_set_logged_in_cookie( $logged_in_cookie, $expire, $expiration, $user_id ) {
		// @var \WP_User $user The user object.
		$user = \get_user_by( 'ID', $user_id );

		if ( false === $user instanceof \WP_User ) {
			return;
		}

		if ( false === $user->exists() ) {
			return;
		}

		Log::log(
			'log-in',
			sprintf(
				/* translators: %s: User display name. */
				__( 'User %s logged in.', 'pulse' ),
				$user->display_name
			),
			'users',
			'session',
			$user_id,
			$user_id,
			[]
		);
	}
}
