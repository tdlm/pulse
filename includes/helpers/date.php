<?php
/**
 * Date functionality helpers.
 *
 * @package WP_Pulse
 * @subpackage Helpers\Date
 * @since 1.0.0
 */

namespace WP_Pulse\Helpers\Date;

/**
 * Get the date range value.
 *
 * @param string $date_range The date range.
 * @param string $range_value The range value.
 * @return string|\WP_Error The date range value.
 */
function get_date_range_value( $date_range = 'today', $range_value = 'start' ) {
	$matched_range_value = '';

	if ( false === in_array( $range_value, [ 'start', 'end' ], true ) ) {
		return new \WP_Error( 'invalid_range_value', 'Invalid range value.' );
	}

	switch ( $date_range ) {
		default:
		case 'today':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( 'today' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'yesterday':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( 'yesterday' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'yesterday' ) ),
			];
			break;
		case 'last_7_days':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( '-7 days' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'last_30_days':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( '-30 days' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'this_month':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( 'this month' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'last_month':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( 'last month' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'last_6_months':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( '-6 months' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'last_12_months':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( '-12 months' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'today' ) ),
			];
			break;
		case 'this_year':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( 'first day of january this year' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'last day of december this year' ) ),
			];
			break;
		case 'last_year':
			$matched_range_value = [
				'start' => wp_date( 'Y-m-d', strtotime( 'first day of january last year' ) ),
				'end'   => wp_date( 'Y-m-d', strtotime( 'last day of december last year' ) ),
			];
			break;
	}

	return $matched_range_value[ $range_value ];
}
