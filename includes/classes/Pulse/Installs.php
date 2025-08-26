<?php

namespace WP_Pulse\Pulse;

use WP_Pulse\Pulse;

class Installs extends Pulse
{
    public $actions = [
        'activate_plugin',
        'deactivate_plugin',
    ];

    public function callback_activate_plugin() {
        error_log( 'activate_plugin' );
    }

    public function callback_deactivate_plugin() {
        error_log( 'deactivate_plugin' );
    }
}
