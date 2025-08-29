<?php
/**
 * Pulse CLI commands for the database.
 *
 * @package WP_Pulse
 * @subpackage CLI
 * @since 1.0.0
 * @version 1.0.0
 */

namespace WP_Pulse\CLI;

/**
 * Database commands for Pulse.
 */
class DB extends \WP_CLI_Command {
	/**
	 * Migrate the database.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp pulse db migrate
	 *
	 * @param array $args         The arguments.
	 * @param array $assoc_args   The associative arguments.
	 */
	public function migrate( $args, $assoc_args ) {
		\WP_Pulse\Install::migrate();

		\WP_CLI::success( 'Migration complete.' );
	}

	/**
	 * Destroy the database.
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp pulse db reset
	 *
	 * @param array $args         The arguments.
	 * @param array $assoc_args   The associative arguments.
	 */
	public function reset( $args, $assoc_args ) {
		\WP_Pulse\Database::destroy_tables();
		\WP_Pulse\Database::remove_pulse_version_db();

		\WP_CLI::success( 'Database reset.' );
	}
}
