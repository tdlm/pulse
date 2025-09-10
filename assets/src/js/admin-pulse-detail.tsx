/* eslint-disable import/no-extraneous-dependencies */
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import './admin-dashboard/globals.d.ts';

import '../scss/admin-pulse-detail.scss';

const app = document.getElementById( 'pulse-detail-container' );

function PulseDetailApp() {
	const [ record ] = useState( window.PulseAdminPulseDetail?.record ?? {} );
	const [ meta ] = useState( window.PulseAdminPulseDetail?.meta ?? {} );

	if ( Object.keys( record ).length === 0 ) {
		return null;
	}

	return (
		<>
			<table className="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">
								{ __( 'Pulse ID', 'pulse' ) }
							</label>
						</th>
						<td>{ record.id }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">
								{ __( 'Action', 'pulse' ) }
							</label>
						</th>
						<td>{ record.action }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">
								{ __( 'Context', 'pulse' ) }
							</label>
						</th>
						<td>{ record.context }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">
								{ __( 'Description', 'pulse' ) }
							</label>
						</th>
						<td>{ record.description }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">
								{ __( 'User', 'pulse' ) }
							</label>
						</th>
						<td>{ record.user_id }</td>
					</tr>
					<tr>
						<th scope="row">
							<label htmlFor="pulse-id">
								{ __( 'IP', 'pulse' ) }
							</label>
						</th>
						<td>{ record.ip }</td>
					</tr>
				</tbody>
			</table>

			{ Object.keys( meta ?? {} ).length > 0 && (
				<>
					<h2>{ __( 'Meta', 'pulse' ) }</h2>
					<table className="form-table">
						<tbody>
							{ Object.keys( meta ?? {} ).map( ( key ) => (
								<tr key={ key }>
									<th scope="row">
										<label htmlFor="pulse-id">
											{ key }
										</label>
									</th>
									<td>{ meta?.[ key ] }</td>
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
