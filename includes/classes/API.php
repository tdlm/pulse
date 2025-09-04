<?php
/**
 * REST API for the WP-Pulse plugin.
 *
 * @package WP_Pulse
 * @subpackage Classes\API
 * @since 1.0.0
 */

namespace WP_Pulse;

/**
 * API class.
 *
 * @api-namespace wp-pulse
 * @api-version   1
 */
class API extends Singleton {

	use Traits\API;

	/**
	 * Initialize the API.
	 *
	 * @return void
	 */
	public static function init() {
		self::set_api_routes(
			[
				'records' => [
					[
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => [ self::class, 'get_records' ],
						'permission_callback' => '__return_true',
						'args'                => [],
					],
				],
				'database/reset' => [
					[
						'methods'             => \WP_REST_Server::CREATABLE,
						'callback'            => [ self::class, 'reset_database' ],
						'permission_callback' => [ self::class, 'reset_database_permission_callback' ],
					],
				],
			],
		);
	}

	/**
	 * Get records.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response
	 */
	public static function get_records( \WP_REST_Request $request ) {
		$args = $request->get_params();

		$args = wp_parse_args(
			$args,
			[
				'action'     => '',
				'context'    => '',
				'created_at' => '',
				'date_range' => '',
				'ip'         => '',
				'limit'      => 20,
				'offset'     => 0,
				'orderby'    => 'created_at_gmt',
				'order'      => 'DESC',
				'pulse'      => '',
				'search'     => '',
				'user_id'    => '',
			]
		);

		$records = Database::get_records(
			[
				'action'     => $args['action'],
				'context'    => $args['context'],
				'created_at' => $args['created_at'],
				'date_range' => $args['date_range'],
				'ip'         => $args['ip'],
				'limit'      => $args['limit'],
				'offset'     => $args['offset'],
				'orderby'    => $args['orderby'],
				'order'      => $args['order'],
				'pulse'      => $args['pulse'],
				'search'     => $args['search'],
				'user_id'    => $args['user_id'],
			]
		);

		return new \WP_REST_Response(
			[
				'count'  => $records['count'],
				'items'  => $records['items'],
				'limit'  => $args['limit'],
				'offset' => $args['offset'],
				'pages'  => ceil( $records['count'] / $args['limit'] ),
				'users'  => $records['users'],
			],
			200,
			[
				'X-WP-Total'      => $records['count'],
				'X-WP-TotalPages' => ceil( $records['count'] / $args['limit'] ),
			]
		);
	}

	/**
	 * Reset the Pulse database.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response
	 */
	public static function reset_database( \WP_REST_Request $request ) {
		Database::reset();

		return new \WP_REST_Response(
			[
				'message' => 'Pulse database reset.',
			]
		);
	}

	/**
	 * Reset the Pulse database permission callback.
	 *
	 * @return bool
	 */
	public static function reset_database_permission_callback() {
		return current_user_can( 'manage_options' );
	}
}
