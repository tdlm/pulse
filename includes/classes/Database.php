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

		if ( true === isset( $args['user_id'] ) && false === is_null( $args['user_id'] ) && '' !== $args['user_id'] ) {
			$where_clauses[] = $wpdb->prepare( 'pulse.user_id = %s', $args['user_id'] );
		}

		if ( false === empty( $where_clauses ) ) {
			$query .= ' WHERE ' . implode( ' AND ', $where_clauses );
		}

		$query .= " ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d";

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

			// Get labels.
			if ( true === method_exists( 'WP_Pulse\\Pulse\\' . $pulse, 'get_labels' ) ) {
				$labels = call_user_func( [ 'WP_Pulse\\Pulse\\' . $pulse, 'get_labels' ] );
			} else {
				$labels = [];
			}

			$result->action_label  = true === isset( $labels[ $result->action ] ) ? $labels[ $result->action ] : $result->action;
			$result->context_label = true === isset( $labels[ $result->context ] ) ? $labels[ $result->context ] : $result->context;

			// Get links.
			if ( true === method_exists( 'WP_Pulse\\Pulse\\' . $pulse, 'get_links' ) ) {
				$links = call_user_func( [ 'WP_Pulse\\Pulse\\' . $pulse, 'get_links' ], $result );
			} else {
				$links = [];
			}

			$result->pulse_label = true === isset( $labels[ $result->pulse ] ) ? $labels[ $result->pulse ] : $result->pulse;
			$result->pulse_links = $links;
		}

		$count_query = "SELECT COUNT(*) FROM {$table_name} AS pulse";

		if ( false === empty( $where_clauses ) ) {
			$count_query .= ' WHERE ' . implode( ' AND ', $where_clauses );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$count = $wpdb->get_var( $count_query );

		$users_query = "SELECT DISTINCT user_id FROM {$table_name} AS pulse WHERE (user_id > 0)";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$user_results = $wpdb->get_col( $users_query );

		$users = [];

		foreach ( $user_results as $user_id ) {
			$user_info = get_userdata( $user_id );

			$user_object = [
				'id'           => null,
				'name'         => null,
				'email'        => null,
				'gravatar_url' => null,
			];

			if ( false !== $user_info && true === $user_info instanceof \WP_User ) {
				$user_object = [
					'id'           => $user_id,
					'name'         => $user_info->display_name,
					'email'        => $user_info->user_email,
					'gravatar_url' => true === isset( $user_info->user_email ) ? get_avatar_url( $user_info->user_email, [ 'size' => 80 ] ) : '',
				];
			}

			$users[] = $user_object;
		}

		return [
			'count' => $count,
			'items' => $results,
			'users' => $users,
		];
	}

	/**
	 * Destroy the tables.
	 *
	 * @return void
	 */
	public static function destroy() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}pulse" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}pulse_meta" );
	}

	/**
	 * Reset the database.
	 *
	 * @return void
	 */
	public static function reset() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DELETE FROM {$wpdb->prefix}pulse" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DELETE FROM {$wpdb->prefix}pulse_meta" );
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
