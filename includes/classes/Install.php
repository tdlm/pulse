<?php
/**
 * Install class.
 * 
 * @package WP_Pulse
 */

namespace WP_Pulse;

class Install {

    protected static $migrations = [
        '1.0.0' => 'v1_0_0_create_base_tables',
    ];

    public static function migrate() {
        $current_db_version = Core::get_db_version();

        if (version_compare($current_db_version, PULSE_VERSION, '>=')) {
            error_log('Pulse: Database is up to date.');
            return;
        }

        foreach(self::$migrations as $version => $migration) {
            if (version_compare($current_db_version, $version, '<')) {
                error_log('Pulse: Migrating to version ' . $version);
                self::$migration();
                error_log('Pulse: Migrating to version ' . $version . ' complete.');
            }
        }
    }

    public static function v1_0_0_create_base_tables() {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}pulse (
          id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          action varchar(255) NOT NULL,
          description longtext NOT NULL,
          context longtext NOT NULL,
          user_id bigint(20) NOT NULL DEFAULT 0,
          ip VARCHAR(255) NOT NULL,
          created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
        ) {$charset_collate};";

        dbDelta($sql);

        // Create the pulse meta table.
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}pulse_meta (
          id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          pulse_id bigint(20) NOT NULL,
          meta_key varchar(255) NOT NULL,
          meta_value longtext NOT NULL,
          created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
        ) {$charset_collate};";

        dbDelta($sql);

        update_option(Core::$option_key_db_version, '1.0.0');
    }
}