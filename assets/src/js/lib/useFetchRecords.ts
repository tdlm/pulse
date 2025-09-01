/// <reference path="../admin-dashboard/types.d.ts" />
/* eslint-disable camelcase */
import { useQuery } from '@tanstack/react-query';
import fetchRecords from './fetchRecords';
import { Records } from '../admin-dashboard/types';

/**
 * Use fetch records.
 *
 * @param debouncedSearch The debounced search.
 * @param action          The action.
 * @param context         The context.
 * @param created_at      The created at.
 * @param date_range      The date range.
 * @param ip              The IP.
 * @param offset          The offset.
 * @param pulse           The pulse.
 * @param user_id         The user ID.
 * @return The fetch records.
 */
export default function useFetchRecords(
	debouncedSearch: string,
	action: string,
	context: string,
	created_at: string,
	date_range: string,
	ip: string,
	offset: number,
	pulse: string,
	user_id: number
) {
	return useQuery< Records >( {
		initialData: {
			...window.PulseAdminDashboard,
		},
		refetchInterval: 10 * 1000, // 10 seconds.
		queryKey: [
			'records',
			action,
			context,
			created_at,
			date_range,
			debouncedSearch,
			ip,
			window.PulseAdminDashboard.limit,
			offset,
			pulse,
			user_id,
		],
		queryFn: ( { signal } ) =>
			fetchRecords( {
				action,
				context,
				created_at,
				date_range,
				search: debouncedSearch,
				ip,
				limit: window.PulseAdminDashboard.limit,
				offset,
				pulse,
				user_id,
				signal,
			} ),
	} );
}
