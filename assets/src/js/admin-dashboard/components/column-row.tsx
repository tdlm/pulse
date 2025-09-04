/* eslint-disable import/no-extraneous-dependencies */

import { __ } from '@wordpress/i18n';
import React from 'react';

/**
 * Header row for the admin dashboard.
 *
 * @return The header row.
 */
export default function ColumnRow() {
	return (
		<tr>
			<th
				scope="col"
				id="expand-toggle"
				className="manage-column column-expand-toggle"
			></th>
			<th
				scope="col"
				id="date"
				className="manage-column column-date sorted asc"
				aria-sort="ascending"
			>
				<a href="http://localhost:8888/wp-admin/admin.php?page=wp-pulse&orderby=date&order=desc">
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
