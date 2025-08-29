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
				'limit'   => 20,
				'offset'  => 0,
				'orderby' => 'created_at_gmt',
				'order'   => 'DESC',
			]
		);

		$records = Database::get_records(
			[
				'limit'   => $args['limit'],
				'offset'  => $args['offset'],
				'orderby' => $args['orderby'],
				'order'   => $args['order'],
			]
		);

		return new \WP_REST_Response( $records );
	}
}
