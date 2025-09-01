<?php
/**
 * Database.
 *
 * @package WP_Pulse
 */

namespace WP_Pulse;

/**
 * Database class.
 */
class Database {

	/**
	 * Get records.
	 *
	 * @param mixed $args Arguments.
	 *
	 * @return mixed
	 */
	public static function get_records( $args = [] ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'pulse';

		$defaults = [
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
		];

		$args = wp_parse_args( $args, $defaults );

		$query = "SELECT pulse.*, users.display_name, users.user_email FROM {$table_name} AS pulse LEFT JOIN {$wpdb->users} AS users ON pulse.user_id = users.ID";

		$where_clauses = [];

		if ( false === empty( $args['action'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.action = %s', $args['action'] );
		}

		if ( false === empty( $args['context'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.context = %s', $args['context'] );
		}

		if ( false === empty( $args['created_at'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'DATE(pulse.created_at) = %s', $args['created_at'] );
		}

		if ( false === empty( $args['date_range'] ) ) {
			$where_clauses[] = $wpdb->prepare(
				'DATE(pulse.created_at) BETWEEN %s AND %s',
				\WP_Pulse\Helpers\Date\get_date_range_value( $args['date_range'], 'start' ),
				\WP_Pulse\Helpers\Date\get_date_range_value( $args['date_range'], 'end' )
			);
		}

		if ( false === empty( $args['ip'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.ip = %s', $args['ip'] );
		}

		if ( false === empty( $args['pulse'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.pulse = %s', $args['pulse'] );
		}

		if ( false === empty( $args['search'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.description LIKE %s', '%' . $args['search'] . '%' );
		}

		if ( false === empty( $args['user_id'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.user_id = %s', $args['user_id'] );
		}

		if ( false === empty( $where_clauses ) ) {
			$query .= ' WHERE ' . implode( ' AND ', $where_clauses );
		}

		$query .= " ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d";

		error_log( $query );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $wpdb->prepare( $query, $args['limit'], $args['offset'] ) );

		// Enrich results with user data.
		foreach ( $results as $result ) {
			// Get user roles.
			$user_info          = get_userdata( $result->user_id );
			$result->user_roles = true === is_object( $user_info ) && true === property_exists( $user_info, 'roles' ) ? $user_info->roles : [];

			// Get label for each role.
			$result->user_roles = array_map(
				function ( $role ) {
					return \WP_Pulse\Helpers\Users\get_user_role_label( $role );
				},
				$result->user_roles
			);

			// Get gravatar URL.
			$result->gravatar_url    = get_avatar_url( $result->user_email, [ 'size' => 80 ] );
			$result->gravatar_url_2x = get_avatar_url( $result->user_email, [ 'size' => 160 ] );

			$pulse = Helpers\Strings\to_pascal_case( $result->pulse );

			if ( true === method_exists( 'WP_Pulse\\Pulse\\' . $pulse, 'get_labels' ) ) {
				$labels = call_user_func( [ 'WP_Pulse\\Pulse\\' . $pulse, 'get_labels' ] );
			} else {
				$labels = [];
			}

			$result->action_label  = true === isset( $labels[ $result->action ] ) ? $labels[ $result->action ] : $result->action;
			$result->context_label = true === isset( $labels[ $result->context ] ) ? $labels[ $result->context ] : $result->context;
			$result->pulse_label   = true === isset( $labels[ $result->pulse ] ) ? $labels[ $result->pulse ] : $result->pulse;
		}

		$count_query = "SELECT COUNT(*) FROM {$table_name} AS pulse";

		if ( false === empty( $where_clauses ) ) {
			$count_query .= ' WHERE ' . implode( ' AND ', $where_clauses );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$count = $wpdb->get_var( $count_query );

		return [
			'count' => $count,
			'items' => $results,
		];
	}

	/**
	 * Destroy the tables.
	 *
	 * @return void
	 */
	public static function destroy_tables() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}pulse" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}pulse_meta" );
	}

	/**
	 * Remove the pulse version database option.
	 *
	 * @return void
	 */
	public static function remove_pulse_version_db() {
		delete_option( 'wp_pulse_version_db' );
	}
}
