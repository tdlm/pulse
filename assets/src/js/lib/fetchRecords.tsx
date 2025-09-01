/* eslint-disable camelcase */
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';
import { Records, Record } from '../admin-dashboard/types';

/**
 * Fetch records.
 *
 * @param params
 * @param params.action     The action.
 * @param params.context    The context.
 * @param params.created_at The created at.
 * @param params.date_range The date range.
 * @param params.search     The search.
 * @param params.ip         The IP.
 * @param params.limit      The limit.
 * @param params.offset     The offset.
 * @param params.pulse      The pulse.
 * @param params.user_id    The user ID.
 * @param params.signal     The signal.
 * @return The fetch records.
 */
export default async function fetchRecords( params: {
	action?: string;
	context?: string;
	created_at?: string;
	date_range?: string;
	search?: string;
	ip?: string;
	limit?: number;
	offset?: number;
	pulse?: string;
	user_id?: number;
	signal?: AbortSignal;
} ): Promise< Records > {
	const {
		action,
		context,
		created_at,
		date_range,
		search,
		ip,
		pulse,
		user_id,
		limit,
		offset,
		signal,
	} = params;
	const response = await apiFetch( {
		path: addQueryArgs( '/wp-pulse/v1/records', {
			action,
			context,
			created_at,
			date_range,
			ip,
			limit,
			offset,
			pulse,
			search,
			user_id,
		} ),
		method: 'GET',
		parse: false,
		signal,
	} );

	const data = await response.json();

	return {
		items: data.items as Record[],
		count: Number( response.headers.get( 'X-WP-Total' ) ),
		limit: Number( response.headers.get( 'X-WP-TotalPages' ) ),
		offset: Number( response.headers.get( 'X-WP-TotalPages' ) ),
		pages: Number( response.headers.get( 'X-WP-TotalPages' ) ),
	};
}
