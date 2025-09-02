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
		'user_register',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'user-created' => __( 'Created', 'pulse' ),
			'user-log-in'  => __( 'Log in', 'pulse' ),
			'user-log-out' => __( 'Log out', 'pulse' ),
			'users'        => __( 'Users', 'pulse' ),
			'user'         => __( 'User', 'pulse' ),
			'session'      => __( 'Session', 'pulse' ),
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
			'user-log-out',
			sprintf(
				/* translators: %s: User display name. */
				__( '%s logged out', 'pulse' ),
				$user->display_name
			),
			'users',
			'user',
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
			'user-log-in',
			sprintf(
				/* translators: %s: User display name. */
				__( 'User %s logged in.', 'pulse' ),
				$user->display_name
			),
			'users',
			'user',
			$user_id,
			$user_id,
			[]
		);
	}

	/**
	 * Callback for user_register.
	 *
	 * @param int $user_id The user ID.
	 * @return void
	 */
	public function callback_user_register( $user_id ) {
		$current_user  = wp_get_current_user();
		$register_user = get_user_by( 'ID', $user_id );

		if ( false === $current_user instanceof \WP_User ) {
			$message = sprintf(
				/* translators: %s: User display name. */
				__( 'New user registered: %s', 'pulse' ),
				$register_user->display_name
			);

			$user_object_id = $register_user->ID;
		} else {
			$message = sprintf(
				/* translators: %s: User display name. */
				__( 'New account registered: %s', 'pulse' ),
				$register_user->display_name
			);

			$user_object_id = $current_user->ID;
		}

		Log::log(
			'user-created',
			$message,
			'user',
			'session',
			$user_object_id,
			$register_user->ID,
			[]
		);
	}
}
