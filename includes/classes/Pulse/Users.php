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
use WP_Pulse\Registry;

/**
 * Users class.
 */
class Users extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'users';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'clear_auth_cookie',
		'delete_user',
		'deleted_user',
		'profile_update',
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
			'user-created'         => __( 'Created', 'pulse' ),
			'user-deleted'         => __( 'Deleted', 'pulse' ),
			'user-log-in'          => __( 'Log in', 'pulse' ),
			'user-log-out'         => __( 'Log out', 'pulse' ),
			'user-profile-updated' => __( 'Profile updated', 'pulse' ),
			'users'                => __( 'Users', 'pulse' ),
			'user'                 => __( 'User', 'pulse' ),
			'session'              => __( 'Session', 'pulse' ),
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
				__( 'User %s logged out', 'pulse' ),
				$user->display_name
			),
			$this->pulse_slug,
			'user',
			$user->ID,
			$user->ID,
			[]
		);
	}

	/**
	 * Callback for delete_user.
	 *
	 * @param int $user_id The user ID.
	 * @return void
	 */
	public function callback_delete_user( $user_id ) {
		$user_ids_before_delete = Registry::get( 'user_ids_before_delete', [] );

		if ( false === isset( $user_ids_before_delete[ $user_id ] ) ) {
			$user_ids_before_delete[ $user_id ] = get_user_by( 'id', $user_id );
			Registry::set( 'user_ids_before_delete', $user_ids_before_delete );
		}
	}

	/**
	 * Callback for deleted_user.
	 *
	 * @param int $user_id The user ID.
	 * @return void
	 */
	public function callback_deleted_user( $user_id ) {
		$user                   = wp_get_current_user();
		$user_ids_before_delete = Registry::get( 'user_ids_before_delete', [] );

		if ( true === isset( $user_ids_before_delete[ $user_id ] ) ) {
			$message = sprintf(
				/* translators: %s: User display name. */
				__( 'User %s deleted.', 'pulse' ),
				$user_ids_before_delete[ $user_id ]->display_name
			);
		} else {
			$message = sprintf(
				/* translators: %s: User display name. */
				__( 'User %s deleted.', 'pulse' ),
				$user_id
			);
		}

		Log::log(
			'user-deleted',
			$message,
			$this->pulse_slug,
			'user',
			$user->ID,
			$user_id,
			[]
		);
	}

	/**
	 * Callback for profile_update.
	 *
	 * @param int      $user_id The user ID.
	 * @param \WP_User $user The user object.
	 *
	 * @return void
	 */
	public function callback_profile_update( $user_id, $user ) {
		$current_user = wp_get_current_user();

		Log::log(
			'user-profile-updated',
			sprintf(
				/* translators: %s: User display name. */
				__( 'User %s\'s profile updated.', 'pulse' ),
				$user->display_name
			),
			$this->pulse_slug,
			'user',
			$current_user->ID,
			$user_id,
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
			$this->pulse_slug,
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
			$this->pulse_slug,
			'user',
			$user_object_id,
			$register_user->ID,
			[]
		);
	}
}
