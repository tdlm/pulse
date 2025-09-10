/* eslint-disable import/no-extraneous-dependencies */
import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';

import '../scss/admin-pulse-detail.scss';

const app = document.getElementById( 'pulse-detail-container' );

function PulseDetailApp() {
	const [ record ] = useState( window.PulseAdminPulseDetail?.record ?? {} );

	if ( Object.keys( record ).length === 0 ) {
		return null;
	}

	return (
		<>
			<table className="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">Pulse ID</label>
						</th>
						<td>{ record.id }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">Action</label>
						</th>
						<td>{ record.action }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">Context</label>
						</th>
						<td>{ record.context }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">Description</label>
						</th>
						<td>{ record.description }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">User</label>
						</th>
						<td>{ record.user_id }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">IP</label>
						</th>
						<td>{ record.ip }</td>
					</tr>
				</tbody>
			</table>

			{ Object.keys( record.meta ?? {} ).length > 0 && (
				<>
					<h2>Meta</h2>
					<table className="form-table">
						<tbody>
							{ Object.keys( record.meta ?? {} ).map( ( key ) => (
								<tr key={ key }>
									<th scope="row">
										<label htmlFor="pulse-id">
											{ key }
										</label>
									</th>
									<td>{ record?.meta?.[ key ] }</td>
								</tr>
							) ) }
						</tbody>
					</table>
				</>
			) }
		</>
	);
}

if ( app ) {
	const root = createRoot( app );
	root.render( <PulseDetailApp /> );
}
