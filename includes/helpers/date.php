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

	$time_zone = wp_timezone();
	$now       = new \DateTimeImmutable( 'now', $time_zone );

	switch ( $date_range ) {
		default:
		case 'today':
			$matched_range_value = [
				'start' => $now->modify( 'today' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'today' )->format( 'Y-m-d' ),
			];
			break;
		case 'yesterday':
			$matched_range_value = [
				'start' => $now->modify( 'yesterday' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'yesterday' )->format( 'Y-m-d' ),
			];
			break;
		case 'last_7_days':
			$matched_range_value = [
				'start' => $now->modify( '-7 days' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'today' )->format( 'Y-m-d' ),
			];
			break;
		case 'last_30_days':
			$matched_range_value = [
				'start' => $now->modify( '-30 days' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'today' )->format( 'Y-m-d' ),
			];
			break;
		case 'this_month':
			$matched_range_value = [
				'start' => $now->modify( 'first day of this month' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'last day of this month' )->format( 'Y-m-d' ),
			];
			break;
		case 'last_month':
			$matched_range_value = [
				'start' => $now->modify( 'first day of previous month' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'last day of previous month' )->format( 'Y-m-d' ),
			];
			break;
		case 'last_6_months':
			$matched_range_value = [
				'start' => $now->modify( '-6 months' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'today' )->format( 'Y-m-d' ),
			];
			break;
		case 'last_12_months':
			$matched_range_value = [
				'start' => $now->modify( '-12 months' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'today' )->format( 'Y-m-d' ),
			];
			break;
		case 'this_year':
			$matched_range_value = [
				'start' => $now->modify( 'first day of january this year' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'last day of december this year' )->format( 'Y-m-d' ),
			];
			break;
		case 'last_year':
			$matched_range_value = [
				'start' => $now->modify( 'first day of january last year' )->format( 'Y-m-d' ),
				'end'   => $now->modify( 'last day of december last year' )->format( 'Y-m-d' ),
			];
			break;
	}

	return $matched_range_value[ $range_value ];
}
