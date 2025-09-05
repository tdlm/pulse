/* eslint-disable import/no-extraneous-dependencies */
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import clsx from 'clsx';
import React from 'react';

type ColumnRowProps = {
	order: string;
	orderBy: string;
	setOrder: ( order: string ) => void;
	setOrderBy: ( order_by: string ) => void;
};

/**
 * Header row for the admin dashboard.
 *
 * @param root0
 * @param root0.order
 * @param root0.orderBy
 * @param root0.setOrder
 * @param root0.setOrderBy
 * @return The header row.
 */
export default function ColumnRow( {
	order,
	orderBy,
	setOrder,
	setOrderBy,
}: ColumnRowProps ) {
	return (
		<tr>
			<th
				scope="col"
				id="date"
				className={ clsx(
					'manage-column column-date sorted',
					order === 'asc' ? 'asc' : 'desc'
				) }
				aria-sort={ order === 'asc' ? 'ascending' : 'descending' }
			>
				<a
					href={ addQueryArgs( window.PulseAdminDashboard.settings.dashboard_base_url, { orderby: orderBy, order: order } ) }
					onClick={ ( e ) => {
						e.preventDefault();
						setOrderBy( orderBy );
						setOrder( order === 'asc' ? 'desc' : 'asc' );
					} }
				>
					<span>{ __( 'Date', 'pulse' ) }</span>
					<span className="sorting-indicators">
						<span
							className="sorting-indicator asc"
							aria-hidden="true"
						></span>
						<span
							className="sorting-indicator desc"
							aria-hidden="true"
						></span>
					</span>
				</a>
			</th>
			<th
				scope="col"
				id="summary"
				className="manage-column column-summary"
			>
				{ __( 'Summary', 'pulse' ) }
			</th>
			<th
				scope="col"
				id="user_id"
				className="manage-column column-user_id"
			>
				{ __( 'User', 'pulse' ) }
			</th>
			<th
				scope="col"
				id="context"
				className="manage-column column-context"
			>
				{ __( 'Context', 'pulse' ) }
			</th>
			<th scope="col" id="action" className="manage-column column-action">
				{ __( 'Action', 'pulse' ) }
			</th>
		</tr>
	);
}
