<?php
/**
 * Log class.
 * 
 * @package WP_Pulse
 */

namespace WP_Pulse;

class Log
{
    /**
     * Log an action.
     * 
     * @param mixed $action
     * @param mixed $description
     * @param mixed $user_id
     */
    public static function log($action, $description, $context, $user_id = null)
    {
        global $wpdb;

        if (true === is_null($user_id)) {
            $user_id = get_current_user_id();
        }

        $pulse = [
            'action' => $action,
            'description' => $description,
            'context' => $context,
            'user_id' => $user_id,
            'ip' => filter_var(filter_input(INPUT_SERVER, 'REMOTE_ADDR'), FILTER_VALIDATE_IP),
            'created_at' => current_time('mysql', true),
        ];

        $pulse_id = $wpdb->insert($wpdb->prefix . 'pulse', $pulse);

        if (true === is_numeric($pulse_id)) {
            return $pulse_id;
        }

        return false;
    }
}