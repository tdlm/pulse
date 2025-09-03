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
		'password_reset',
		'profile_update',
		'retrieve_password',
		'set_logged_in_cookie',
		'set_user_role',
		'user_register',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'user-created'                => __( 'Created', 'pulse' ),
			'user-deleted'                => __( 'Deleted', 'pulse' ),
			'user-log-in'                 => __( 'Log in', 'pulse' ),
			'user-log-out'                => __( 'Log out', 'pulse' ),
			'user-password-request-reset' => __( 'Reset', 'pulse' ),
			'user-profile-updated'        => __( 'Updated', 'pulse' ),
			'users'                       => __( 'Users', 'pulse' ),
			'user'                        => __( 'User', 'pulse' ),
			'session'                     => __( 'Session', 'pulse' ),
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
	 * Callback for password_reset.
	 *
	 * @param \WP_User $user The user object.
	 * @return void
	 */
	public function callback_password_reset( $user ) {
		$current_user = get_current_user();

		Log::log(
			'password-reset',
			sprintf(
				/* translators: %s: User display name. */
				__( 'User %s\'s password reset.', 'pulse' ),
				$user->display_name
			),
			$this->pulse_slug,
			'user',
			$current_user->ID,
			$user->ID,
			[]
		);
	}

	/**
	 * Callback for profile_update.
	 *
	 * @param int      $user_id The user ID.
	 * @param \WP_User $old_user_data The old user data.
	 * @param array    $user_data The new user data.
	 *
	 * @return void
	 */
	public function callback_profile_update( $user_id, $old_user_data, $user_data ) {
		$current_user = wp_get_current_user();

		Log::log(
			'user-profile-updated',
			sprintf(
				/* translators: %s: User display name. */
				__( 'User %s\'s profile updated.', 'pulse' ),
				$old_user_data->display_name
			),
			$this->pulse_slug,
			'user',
			$current_user->ID,
			$user_id,
		);
	}


	/**
	 * Callback for retrieve_password.
	 *
	 * @param string $user_login The user login.
	 * @return void
	 */
	public function callback_retrieve_password( $user_login ) {
		$current_user = get_current_user();

		$email = filter_var( $user_login, FILTER_VALIDATE_EMAIL );

		if ( false === empty( $email ) ) {
			$user = get_user_by( 'email', $email );
		} else {
			$user = get_user_by( 'login', $user_login );
		}

		Log::log(
			'user-password-request-reset',
			sprintf(
				/* translators: %s: User display name. */
				__( 'Password requested to be reset for user %s.', 'pulse' ),
				$user->display_name
			),
			$this->pulse_slug,
			'user',
			$current_user->ID,
			$user->ID,
			[]
		);
	}

	/**
	 * Summary of callback_set_user_role
	 *
	 * @param int      $user_id The user ID.
	 * @param string   $new_role The new role.
	 * @param string[] $old_roles The old roles.
	 * @return void
	 */
	public function callback_set_user_role( $user_id, $new_role, $old_roles ) {
		$current_user = wp_get_current_user();

		$user = get_user_by( 'ID', $user_id );

		if ( false === $user instanceof \WP_User ) {
			$display_name = $user_id;
		} else {
			$display_name = $user->display_name;
		}

		$old_role       = current( $old_roles );
		$old_role_label = \WP_Pulse\Helpers\Users\get_user_role_label( $old_role );
		$new_role_label = \WP_Pulse\Helpers\Users\get_user_role_label( $new_role );

		Log::log(
			'user-role-changed',
			sprintf(
				/* translators: %s: User display name. */
				__( 'User %1$s\'s role changed from %2$s to %3$s.', 'pulse' ),
				$display_name,
				$old_role_label,
				$new_role_label
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
