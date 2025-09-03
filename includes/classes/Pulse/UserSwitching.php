<?php
/**
 * User Switching pulse.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;
use WP_Pulse\Log;

/**
 * User Switching pulse.
 */
class UserSwitching extends Pulse {

	/**
	 * The pulse slug.
	 *
	 * @var string
	 */
	protected $pulse_slug = 'user_switching';

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [
		'switch_back_user',
		'switch_off_user',
		'switch_to_user',
	];

	/**
	 * Get labels.
	 *
	 * @return array The labels.
	 */
	public static function get_labels() {
		return [
			'session'          => __( 'Session', 'pulse' ),
			'switch_back_user' => __( 'Switched Back', 'pulse' ),
			'switch_off_user'  => __( 'Switched Off', 'pulse' ),
			'switch_to_user'   => __( 'Switched To', 'pulse' ),
			'user_switching'   => __( 'User Switching', 'pulse' ),
		];
	}

	/**
	 * Callback for switch_back_user.
	 *
	 * @param int $user_id The ID of the user who switched back.
	 * @param int $old_user_id The ID of the user who was switched from.
	 * @return void
	 */
	public function callback_switch_back_user( $user_id, $old_user_id ) {
		$current_user  = get_userdata( $user_id );
		$previous_user = get_userdata( $old_user_id );

		$user_details = [
			'from_id'           => $previous_user->ID,
			'from_login'        => $previous_user->user_login,
			'from_email'        => $previous_user->user_email,
			'from_display_name' => $previous_user->display_name,
			'from_roles'        => implode( ', ', $previous_user->roles ),
			'to_id'             => $current_user->ID,
			'to_login'          => $current_user->user_login,
			'to_email'          => $current_user->user_email,
			'to_display_name'   => $current_user->display_name,
			'to_roles'          => implode( ', ', $current_user->roles ),
		];

		Log::log(
			'switch_back_user',
			sprintf(
				/* translators: %1$s: Current user display name. %2$s: Previous user display name. */
				__( 'Switched back to %1$s from %2$s.', 'pulse' ),
				$current_user->display_name,
				$previous_user->display_name
			),
			$this->pulse_slug,
			'session',
			$previous_user->ID,
			$previous_user->ID,
			$user_details,
		);
	}

	/**
	 * Callback for switch_off_user.
	 *
	 * @param int $old_user_id The ID of the user who was switched off.
	 * @return void
	 */
	public function callback_switch_off_user( $old_user_id ) {
		$user = get_userdata( $old_user_id );

		$user_details = [
			'user_id'           => $user->ID,
			'user_login'        => $user->user_login,
			'user_email'        => $user->user_email,
			'user_display_name' => $user->display_name,
			'user_roles'        => implode( ', ', $user->roles ),
		];

		Log::log(
			'switch_off_user',
			sprintf(
				/* translators: %1$s: User display name. */
				__( 'Switched off %1$s.', 'pulse' ),
				$user->display_name,
			),
			$this->pulse_slug,
			'session',
			$user->ID,
			$user->ID,
			$user_details,
		);
	}

	/**
	 * Callback for switch_to_user.
	 *
	 * @param int $user_id The ID of the user who was switched to.
	 * @param int $old_user_id The ID of the user who was switched from.
	 * @return void
	 */
	public function callback_switch_to_user( $user_id, $old_user_id ) {
		$current_user  = get_userdata( $user_id );
		$previous_user = get_userdata( $old_user_id );

		$user_details = [
			'from_id'           => $previous_user->ID,
			'from_login'        => $previous_user->user_login,
			'from_email'        => $previous_user->user_email,
			'from_display_name' => $previous_user->display_name,
			'from_roles'        => implode( ', ', $previous_user->roles ),
			'to_id'             => $current_user->ID,
			'to_login'          => $current_user->user_login,
			'to_email'          => $current_user->user_email,
			'to_display_name'   => $current_user->display_name,
			'to_roles'          => implode( ', ', $current_user->roles ),
		];

		Log::log(
			'switch_to_user',
			sprintf(
				/* translators: %1$s: Current user display name. %2$s: Previous user display name. */
				__( 'Switched to %1$s from %2$s.', 'pulse' ),
				$current_user->display_name,
				$previous_user->display_name
			),
			$this->pulse_slug,
			'session',
			$previous_user->ID,
			$previous_user->ID,
			$user_details,
		);
	}
}
