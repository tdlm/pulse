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
 * @param isLive          Is record fetching live?
 * @param offset          The offset.
 * @param order_by        The order by.
 * @param order           The order.
 * @param pulse           The pulse.
 * @param userId          The user ID.
 * @return The fetch records.
 */
export default function useFetchRecords(
	debouncedSearch: string,
	action: string,
	context: string,
	created_at: string,
	date_range: string,
	ip: string,
	isLive: boolean,
	offset: number,
	order_by: string,
	order: string,
	pulse: string,
	userId: number
) {
	return useQuery< Records >( {
		initialData: {
			count: window.PulseAdminDashboard.count,
			items: window.PulseAdminDashboard.items,
			limit: window.PulseAdminDashboard.limit,
			offset: window.PulseAdminDashboard.offset,
			pages: window.PulseAdminDashboard.pages,
			users: window.PulseAdminDashboard.users,
		},
		refetchInterval: 10 * 1000, // 10 seconds.
		enabled: isLive,
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
			order_by,
			order,
			pulse,
			userId,
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
				order_by,
				order,
				pulse,
				user_id: userId,
				signal,
			} ),
	} );
}
