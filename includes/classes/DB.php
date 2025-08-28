<?php

namespace WP_Pulse;

/**
 * Database class.
 */
class DB {

    public static function get_records( $args = [] ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'pulse';

        $defaults = [
            'limit' => 10,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        ];

        $args = wp_parse_args( $args, $defaults );

        $sql = $wpdb->prepare(
            "SELECT * FROM {$table_name} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d",
            $args['limit'],
            $args['offset']
        );

        return $wpdb->get_results( $sql );
    }
}
