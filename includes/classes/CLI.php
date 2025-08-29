<?php
/**
 * Pulse CLI commands.
 *
 * @package WP_Pulse
 * @subpackage CLI
 * @since 1.0.0
 * @version 1.0.0
 */

namespace WP_Pulse;

/**
 * Commands for Pulse.
 */
class CLI extends \WP_CLI_Command {
	/**
	 * Query the database.
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : The format to use for the output. Valid formats are: table, json, csv, yaml, count.
	 *
	 * [--limit=<limit>]
	 * : The number of records to return.
	 *
	 * [--offset=<offset>]
	 * : The number of records to skip.
	 *
	 * [--orderby=<orderby>]
	 * : The field to order the records by.
	 *
	 * [--order=<order>]
	 * : The order to sort the records in.
	 *
	 * ## EXAMPLES
	 *
	 *     wp pulse query
	 *     wp pulse query --format=json
	 *
	 * @param array $args         The arguments.
	 * @param array $assoc_args   The associative arguments.
	 */
	public function query( $args, $assoc_args ) {
		$args = wp_parse_args(
			$assoc_args,
			[
				'format'  => 'table',
				'limit'   => 20,
				'offset'  => 0,
				'orderby' => 'created_at_gmt',
				'order'   => 'DESC',
			]
		);

		if ( false === in_array( strtolower( $args['orderby'] ), [ 'id', 'user_id', 'action', 'created_at_gmt' ], true ) ) {
			\WP_CLI::error( 'Invalid orderby. Valid orderbys are: id, user_id, action, created_at_gmt.' );
		}

		if ( false === in_array( strtolower( $args['order'] ), [ 'asc', 'desc' ], true ) ) {
			\WP_CLI::error( 'Invalid order. Valid orders are: ASC, DESC.' );
		}

		if ( false === in_array(
			$args['format'],
			[ 'table', 'json', 'csv', 'yaml', 'count' ],
			true
		) ) {
			\WP_CLI::error( 'Invalid format. Valid formats are: table, json, csv, yaml, count.' );
		}

		$records = Database::get_records(
			[
				'limit'   => $args['limit'],
				'offset'  => $args['offset'],
				'orderby' => $args['orderby'],
				'order'   => $args['order'],
			]
		);

		\WP_CLI\Utils\format_items( $args['format'], $records, [ 'id', 'user_id', 'action', 'created_at_gmt' ] );
	}
}
