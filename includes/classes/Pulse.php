<?php
/**
 * Pulse.
 *
 * @package WP_Pulse
 * @subpackage Pulse
 * @since 1.0.0
 */
namespace WP_Pulse;

class Pulse
{
    public $actions = [];

    private $is_registered = false;

    public function __construct()
    {
        // Silence is golden.
    }

    public function register() {
        if ( true === $this->is_registered ) {
            return;
        }

        foreach ( $this->actions as $action ) {
            add_action( $action, [ $this, 'callback' ], 10, 99 );
        }

        $this->is_registered = true;        
    }

    public function unregister() {
        if ( false === $this->is_registered ) {
            return;
        }

        foreach ( $this->actions as $action ) {
            remove_action( $action, [ $this, 'callback' ], 10, 99 );
        }

        $this->is_registered = false;
    }

    public function callback() {
        $action = current_filter();
        $callback = [ $this, 'callback_' . preg_replace( '/[^a-z0-9_]/', '_', $action ) ];

        if ( true === is_callable( $callback ) ) {
            return call_user_func_array( $callback, func_get_args() );
        }

        return false;
    }
}