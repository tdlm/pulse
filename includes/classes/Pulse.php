<?php
/**
 * The base class for all pulses.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * Pulse class.
 */
class Pulse {

	/**
	 * The actions to register.
	 *
	 * @var array
	 */
	public $actions = [];

	/**
	 * Whether the Pulse class has been registered.
	 *
	 * @var bool
	 */
	private $is_registered = false;

	/**
	 * Register the actions.
	 *
	 * @return void
	 */
	public function register() {
		if ( true === $this->is_registered ) {
			return;
		}

		foreach ( $this->actions as $action ) {
			add_action( $action, [ $this, 'callback' ], 10, 99 );
		}

		$this->is_registered = true;
	}

	/**
	 * Unregister the actions.
	 *
	 * @return void
	 */
	public function unregister() {
		if ( false === $this->is_registered ) {
			return;
		}

		foreach ( $this->actions as $action ) {
			remove_action( $action, [ $this, 'callback' ], 10, 99 );
		}

		$this->is_registered = false;
	}

	/**
	 * The callback for the actions.
	 *
	 * @return mixed
	 */
	public function callback() {
		$action   = current_filter();
		$callback = [ $this, 'callback_' . preg_replace( '/[^a-z0-9_]/', '_', $action ) ];

		if ( true === is_callable( $callback ) ) {
			return call_user_func_array( $callback, func_get_args() );
		}

		return false;
	}
}
