/// <reference path="../admin-dashboard/types.d.ts" />
/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable camelcase */

import { useDebounce } from '@uidotdev/usehooks';
import clsx from 'clsx';
import { useQueryState } from 'nuqs';
import React, { useEffect, useState } from 'react'; // eslint-disable-line import/no-extraneous-dependencies
import useFetchRecords from '../lib/useFetchRecords';
import ColumnRow from './components/column-row';
import DataRow from './components/data-row';
import Pagination from './components/pagination';

/**
 * Get the table nav pages class.
 *
 * @param totalItems The items.
 * @param totalPages The total pages.
 * @return The table nav pages class.
 */
const getTableNavPagesClass = ( totalItems: number, totalPages: number ) => {
	if ( totalItems < 1 ) {
		return 'no-pages';
	} else if ( 1 === totalPages ) {
		return 'one-page';
	}
	return '';
};

/**
 * Admin dashboard app.
 *
 * @return The admin dashboard app.
 */
export default function AdminDashboardApp() {
	const [ hasFilters, setHasFilters ] = useState( false );
	const [ search, setSearch ] = useQueryState( 'search', {
		defaultValue: '',
	} );

	const [ action, setAction ] = useQueryState( 'action', {
		defaultValue: '',
	} );

	const [ context, setContext ] = useQueryState( 'context', {
		defaultValue: '',
	} );

	const [ created_at, setCreatedAt ] = useQueryState( 'created_at', {
		defaultValue: '',
	} );

	const [ ip, setIp ] = useQueryState( 'ip', {
		defaultValue: '',
	} );

	const [ paged, setPaged ] = useQueryState( 'paged', {
		defaultValue: 1,
		parse: ( value ) => Number( value ),
	} );

	const [ pulse, setPulse ] = useQueryState( 'pulse', {
		defaultValue: '',
	} );

	const [ user_id, setUserId ] = useQueryState( 'user_id', {
		defaultValue: null,
		parse: ( value ) => Number( value ),
	} );

	const debouncedSearch = useDebounce( search, 350 );

	const offset =
		Number( paged ) < 2
			? 0
			: ( Number( paged ) - 1 ) * window.PulseAdminDashboard.limit;

	const { data, isLoading, isError } = useFetchRecords(
		debouncedSearch,
		action,
		context,
		created_at,
		ip,
		offset,
		pulse,
		user_id
	);

	useEffect( () => {
		setHasFilters(
			Boolean(
				search ||
					action ||
					created_at ||
					context ||
					ip ||
					pulse ||
					user_id ||
					created_at
			)
		);
	}, [ search, action, context, created_at, ip, pulse, user_id ] );

	return (
		<form method="get" action="http://localhost:8888/wp-admin/admin.php">
			<input type="hidden" name="page" value="wp-pulse" />
			<p className="search-box">
				<label
					className="screen-reader-text"
					htmlFor="record-search-input"
				>
					Search Records:
				</label>
				<input
					type="search"
					id="record-search-input"
					name="search"
					defaultValue={ search }
				/>
				<input
					type="submit"
					name=""
					id="search-submit"
					className="button"
					value="Search Records"
				/>
			</p>
			<div className="tablenav top">
				<div className="alignleft actions">
					{ hasFilters && (
						<a
							href="http://localhost:8888/wp-admin/admin.php?page=wp-pulse"
							id="record-query-reset"
						>
							<span className="dashicons dashicons-dismiss"></span>
							<span className="record-query-reset-text">
								Reset filters
							</span>
						</a>
					) }
				</div>
				<div
					className={ clsx(
						'tablenav-pages',
						getTableNavPagesClass( data?.count, data?.pages )
					) }
				>
					<span className="displaying-num">
						{ data?.count } items
					</span>
					<Pagination
						paged={ Number( paged ) }
						setPage={ setPaged }
						totalPages={ data?.pages }
					/>
				</div>
			</div>
			<table className="pulse-table wp-list-table widefat fixed striped">
				<thead>
					<ColumnRow />
				</thead>
				<tbody className="the-list">
					{ Array.isArray( data?.items ) &&
						data.items.length > 0 &&
						data.items.map( ( record ) => (
							<DataRow
								key={ record.id }
								record={ record }
								setAction={ setAction }
								setContext={ setContext }
								setCreatedAt={ setCreatedAt }
								setIp={ setIp }
								setPaged={ setPaged }
								setPulse={ setPulse }
								setUserId={ setUserId }
							/>
						) ) }
					{ Array.isArray( data?.items ) &&
						data.items.length === 0 && (
							<tr className="no-items">
								<td className="colspanchange" colSpan={ 6 }>
									<p>No pulse records were found.</p>
								</td>
							</tr>
						) }
				</tbody>
				<tfoot>
					<ColumnRow />
				</tfoot>
			</table>
			<div className="tablenav bottom">
				<div className="alignleft actions recordactions">Actions</div>
				<div className="alignleft actions"></div>

				<div
					className={ clsx(
						'tablenav-pages',
						getTableNavPagesClass( data?.count, data?.pages )
					) }
				>
					<span className="displaying-num">
						{ data?.count } items
					</span>
					<Pagination
						paged={ Number( paged ) }
						setPage={ setPaged }
						totalPages={ data?.pages }
					/>
				</div>
			</div>
		</form>
	);
}
