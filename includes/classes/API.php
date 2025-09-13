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
				'export'          => [
					[
						'methods'             => \WP_REST_Server::CREATABLE,
						'callback'            => [ self::class, 'export' ],
						'permission_callback' => [ self::class, 'export_permission_callback' ],
					],
				],
				'export/download' => [
					[
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => [ self::class, 'export_download' ],
						'permission_callback' => '__return_true', // No need to check for permission.
					],
				],
				'records'         => [
					[
						'methods'             => \WP_REST_Server::READABLE,
						'callback'            => [ self::class, 'get_records' ],
						'permission_callback' => [ self::class, 'get_records_permission_callback' ],
						'args'                => [],
					],
				],
				'database/reset'  => [
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
	 * Export.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return \WP_REST_Response
	 */
	public static function export( \WP_REST_Request $request ) {
		$args = $request->get_params();

		$parsed_args = wp_parse_args(
			$args,
			[
				'filters'        => [],
				'selectedFields' => [],
				'exportType'     => 'csv',
			]
		);

		$filters         = $parsed_args['filters'] ?? [];
		$selected_fields = $parsed_args['selectedFields'] ?? [];
		$export_type     = $parsed_args['exportType'] ?? 'csv';

		$filters = array_merge( $filters, [ 'limit' => -1 ] );

		$records = Database::get_records( $filters );

		// Generate file content based on export type.
		$file_content   = '';
		$file_extension = '';
		$mime_type      = '';

		switch ( $export_type ) {
			case 'json':
				// Filter records to only include selected fields.
				$filtered_records = [];
				foreach ( $records['items'] as $record ) {
					$filtered_record = [];
					foreach ( $selected_fields as $field ) {
						$filtered_record[ $field ] = $record->$field ?? '';
					}
					$filtered_records[] = $filtered_record;
				}

				$file_content   = wp_json_encode( $filtered_records, JSON_PRETTY_PRINT );
				$file_extension = 'json';
				$mime_type      = 'application/json';
				break;

			case 'csv':
			default:
				// Generate CSV content using string building (WordPress-friendly).
				$csv_rows = [];

				// Add header row.
				$csv_rows[] = self::array_to_csv_row( $selected_fields );

				// Add data rows.
				foreach ( $records['items'] as $record ) {
					$row = [];
					foreach ( $selected_fields as $field ) {
						$row[] = $record->$field ?? '';
					}
					$csv_rows[] = self::array_to_csv_row( $row );
				}

				$file_content   = implode( "\n", $csv_rows );
				$file_extension = 'csv';
				$mime_type      = 'text/csv';
				break;
		}

		// Store export data temporarily and return download URL.
		$export_id   = wp_generate_uuid4();
		$export_data = [
			'filename' => 'pulse-export-' . gmdate( 'Y-m-d-H-i-s' ) . '.' . $file_extension,
			'content'  => $file_content,
			'mimeType' => $mime_type,
			'expires'  => time() + 300, // 5 minutes.
		];

		// Store in transient (or could use wp_options for persistence).
		set_transient( 'pulse_export_' . $export_id, $export_data, 300 );

		return new \WP_REST_Response(
			[
				'downloadUrl' => rest_url( 'wp-pulse/v1/export/download?id=' . $export_id ),
				'filename'    => $export_data['filename'],
				'size'        => strlen( $file_content ),
			],
			200
		);
	}

	/**
	 * Download export file directly.
	 *
	 * @param \WP_REST_Request $request The request object.
	 * @return void
	 */
	public static function export_download( \WP_REST_Request $request ) {
		$export_id = $request->get_param( 'id' );

		if ( true === empty( $export_id ) ) {
			status_header( 400 );
			die( 'Missing export ID' );
		}

		$export_data = get_transient( 'pulse_export_' . $export_id );

		if ( false === $export_data ) {
			status_header( 404 );
			die( 'Export not found or expired' );
		}

		// Delete the transient after use.
		delete_transient( 'pulse_export_' . $export_id );

		// Set headers for file download.
		header( 'Content-Type: ' . $export_data['mimeType'] );
		header( 'Content-Disposition: attachment; filename="' . $export_data['filename'] . '"' );
		header( 'Content-Length: ' . strlen( $export_data['content'] ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Output file content.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $export_data['content'];
		exit;
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
				'order'      => 'desc',
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
	 * Export permission callback.
	 *
	 * @return bool
	 */
	public static function export_permission_callback() {
		return true === current_user_can( 'manage_options' );
	}

	/**
	 * Get records permission callback.
	 *
	 * @return bool
	 */
	public static function get_records_permission_callback() {
		return true === current_user_can( 'manage_options' );
	}

	/**
	 * Convert array to CSV row string.
	 *
	 * @param array $data The data array.
	 * @return string The CSV row string.
	 */
	private static function array_to_csv_row( array $data ): string {
		$escaped_data = [];

		foreach ( $data as $field ) {
			$field = (string) $field;

			// Escape quotes by doubling them.
			$field = str_replace( '"', '""', $field );

			// Wrap in quotes if field contains comma, quote, or newline.
			if ( strpos( $field, ',' ) !== false || strpos( $field, '"' ) !== false || strpos( $field, "\n" ) !== false || strpos( $field, "\r" ) !== false ) {
				$field = '"' . $field . '"';
			}

			$escaped_data[] = $field;
		}

		return implode( ',', $escaped_data );
	}

	/**
	 * Generate CSV using WP_Filesystem (alternative approach).
	 *
	 * @param array $records The records to export.
	 * @param array $selected_fields The selected fields.
	 * @return string|false The CSV content or false on failure.
	 */
	private static function generate_csv_with_wp_filesystem( array $records, array $selected_fields ) {
		// Initialize WP_Filesystem.
		if ( false === function_exists( 'WP_Filesystem' ) ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$access_type = get_filesystem_method();
		if ( 'direct' !== $access_type ) {
			// Can't use WP_Filesystem without credentials for non-direct methods.
			return false;
		}

		WP_Filesystem();
		global $wp_filesystem;

		// Create temporary file.
		$temp_file = wp_tempnam( 'pulse_export_' );

		if ( true === empty( $temp_file ) ) {
			return false;
		}

		// Write CSV header.
		$csv_content = self::array_to_csv_row( $selected_fields ) . "\n";

		// Write CSV data.
		foreach ( $records as $record ) {
			$row = [];
			foreach ( $selected_fields as $field ) {
				$row[] = $record->$field ?? '';
			}
			$csv_content .= self::array_to_csv_row( $row ) . "\n";
		}

		// Write to temporary file.
		if ( false === $wp_filesystem->put_contents( $temp_file, $csv_content ) ) {
			wp_delete_file( $temp_file );
			return false;
		}

		// Read content back.
		$file_content = $wp_filesystem->get_contents( $temp_file );

		// Clean up temporary file.
		wp_delete_file( $temp_file );

		return $file_content;
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
