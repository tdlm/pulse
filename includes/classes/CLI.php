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
	 * [--action=<action>]
	 * : The action to filter the records by.
	 *
	 * [--created_at=<created_at>]
	 * : The created at to filter the records by.
	 *
	 * [--date_range=<date_range>]
	 * : The date range to filter the records by.
	 *
	 * [--format=<format>]
	 * : The format to use for the output. Valid formats are: table, json, csv, yaml, count.
	 *
	 * [--ip=<ip>]
	 * : The ip to filter the records by.
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
	 * [--pulse=<pulse>]
	 * : The pulse to filter the records by.
	 *
	 * [--pulse_context=<context>]
	 * : The context to filter the records by.
	 *
	 * [--search=<search>]
	 * : The search to filter the records by.
	 *
	 * [--user_id=<user_id>]
	 * : The user id to filter the records by.
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
				'action'        => '',
				'created_at'    => '',
				'date_range'    => '',
				'format'        => 'table',
				'id'            => '',
				'ip'            => '',
				'limit'         => 20,
				'offset'        => 0,
				'order'         => 'desc',
				'orderby'       => 'created_at_gmt',
				'pulse_context' => '',
				'pulse'         => '',
				'search'        => '',
				'user_id'       => '',
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
				'action'     => $args['action'],
				'context'    => $args['pulse_context'],
				'created_at' => $args['created_at'],
				'date_range' => $args['date_range'],
				'id'         => $args['id'],
				'ip'         => $args['ip'],
				'limit'      => $args['limit'],
				'offset'     => $args['offset'],
				'order'      => $args['order'],
				'orderby'    => $args['orderby'],
				'pulse'      => $args['pulse'],
				'search'     => $args['search'],
				'user_id'    => $args['user_id'],
			]
		);

		\WP_CLI\Utils\format_items(
			$args['format'],
			$records['items'],
			[
				'id',
				'user_id',
				'action',
				'description',
				'ip',
				'created_at',
				'created_at_gmt',
			]
		);
	}
}
