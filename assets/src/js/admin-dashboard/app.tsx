/// <reference path="../admin-dashboard/globals.d.ts" />
/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable camelcase */

import { useDebounce } from '@uidotdev/usehooks';
import { __ } from '@wordpress/i18n';
import clsx from 'clsx';
import { useQueryState } from 'nuqs';
import React, { useEffect, useState } from 'react';

// Types.
import { Filters } from './types';

// Hooks.
import useFetchRecords from '../lib/useFetchRecords';

// Components.
import ColumnRow from './components/column-row';
import DataRow from './components/data-row';
import ExportModal from './components/export-modal';
import FilterDate from './components/filter-date';
import FilterUser from './components/filter-user';
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

	const [ date_range, setDateRange ] = useQueryState( 'date_range', {
		defaultValue: '',
	} );

	const [ ip, setIp ] = useQueryState( 'ip', {
		defaultValue: '',
	} );

	const [ order_by, setOrderBy ] = useQueryState( 'orderby', {
		defaultValue: 'created_at_gmt',
	} );

	const [ order, setOrder ] = useQueryState( 'order', {
		defaultValue: 'desc',
	} );

	const [ paged, setPaged ] = useQueryState( 'paged', {
		defaultValue: 1,
		parse: ( value ) => Number( value ),
	} );

	const [ pulse, setPulse ] = useQueryState( 'pulse', {
		defaultValue: '',
	} );

	const [ user_id, setUserId ] = useQueryState< number | null >( 'user_id', {
		defaultValue: null,
		parse: ( value ) => Number( value ),
	} );

	const filters = {
		action,
		context,
		created_at,
		date_range,
		ip,
		order_by,
		order,
		pulse,
		user_id,
	} as Filters;

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
		date_range,
		ip,
		!! window.PulseAdminDashboard.settings.live_updates_enabled,
		offset,
		order_by,
		order,
		pulse,
		user_id
	);

	useEffect( () => {
		setHasFilters(
			Boolean(
				search ||
					action ||
					context ||
					created_at ||
					date_range ||
					ip ||
					order !== 'desc' ||
					order_by !== 'created_at_gmt' ||
					pulse ||
					user_id !== null
			)
		);
	}, [
		action,
		context,
		created_at,
		date_range,
		ip,
		order_by,
		order,
		pulse,
		search,
		user_id,
	] );

	return (
		<form
			method="get"
			action={ window.PulseAdminDashboard.settings.admin_url }
		>
			<input type="hidden" name="page" value="wp-pulse" />
			<p className="search-box">
				<label
					className="screen-reader-text"
					htmlFor="record-search-input"
				>
					{ __( 'Search Records:', 'pulse' ) }
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
					value={ __( 'Search Records', 'pulse' ) }
				/>
			</p>
			<div className="tablenav top">
				<div className="alignleft actions">
					<div className="pulse-filters">
						<FilterDate
							dateRange={ date_range }
							setDateRange={ setDateRange }
							setPaged={ setPaged }
						/>
						<FilterUser
							userId={ user_id }
							setUserId={ setUserId }
							setPaged={ setPaged }
							users={ data?.users }
						/>
						{ hasFilters && (
							<a
								href={
									window.PulseAdminDashboard.settings
										.dashboard_base_url
								}
								id="record-query-reset"
							>
								<span className="dashicons dashicons-dismiss"></span>
								<span className="record-query-reset-text">
									{ __( 'Reset filters', 'pulse' ) }
								</span>
							</a>
						) }
					</div>
				</div>
				<div
					className={ clsx(
						'tablenav-pages',
						getTableNavPagesClass(
							Number( data?.count ),
							Number( data?.pages )
						)
					) }
				>
					<span className="displaying-num">
						{ data?.count } { __( 'items', 'pulse' ) }
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
					<ColumnRow
						orderBy={ order_by }
						order={ order }
						setOrderBy={ setOrderBy }
						setOrder={ setOrder }
					/>
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
									<p>
										{ __(
											'No pulse records were found.',
											'pulse'
										) }
									</p>
								</td>
							</tr>
						) }
				</tbody>
				<tfoot>
					<ColumnRow
						orderBy={ order_by }
						order={ order }
						setOrderBy={ setOrderBy }
						setOrder={ setOrder }
					/>
				</tfoot>
			</table>
			<div className="tablenav bottom">
				<div className="alignleft actions pulse-export-actions">
					<ExportModal
						exportType="csv"
						filters={ filters }
						label={ __( 'Export CSV', 'pulse' ) }
					/>
					<ExportModal
						exportType="json"
						filters={ filters }
						label={ __( 'Export JSON', 'pulse' ) }
					/>
				</div>

				<div
					className={ clsx(
						'tablenav-pages',
						getTableNavPagesClass(
							Number( data?.count ),
							Number( data?.pages )
						)
					) }
				>
					<span className="displaying-num">
						{ data?.count } { __( 'items', 'pulse' ) }
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
