/* eslint-disable import/no-extraneous-dependencies */
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { addQueryArgs } from '@wordpress/url';
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
			<table className="form-table striped">
				<tbody>
					<tr>
						<th scope="row">
							<label>
								{ __( 'Pulse ID', 'pulse' ) }
							</label>
						</th>
						<td>{ record.id }</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								{ __( 'Date', 'pulse' ) }
							</label>
						</th>
						<td>{ record.created_at }</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								{ __( 'Pulse', 'pulse' ) }
							</label>
						</th>
						<td>
							<a
								href={ addQueryArgs(
									window.PulseAdminPulseDetail.settings
										.admin_url,
									{ page: 'wp-pulse', pulse: record.pulse }
								) }
							>
								{ record.pulse_label }
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								{ __( 'Action', 'pulse' ) }
							</label>
						</th>
						<td>
							<a
								href={ addQueryArgs(
									window.PulseAdminPulseDetail.settings
										.admin_url,
									{ page: 'wp-pulse', action: record.action }
								) }
							>
								{ record.action_label }
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								{ __( 'Context', 'pulse' ) }
							</label>
						</th>
						<td>
							<a
								href={ addQueryArgs(
									window.PulseAdminPulseDetail.settings
										.admin_url,
									{
										page: 'wp-pulse',
										pulse: record.pulse,
										context: record.context,
									}
								) }
							>
								{ record.context_label }
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								{ __( 'Description', 'pulse' ) }
							</label>
						</th>
						<td>{ record.description }</td>
					</tr>

					{ record.user_id && record.display_name && (
						<tr>
							<th scope="row">
								<label>
									{ __( 'Action done by', 'pulse' ) }
								</label>
							</th>
							<td>
								<a
									href={ addQueryArgs(
										window.PulseAdminPulseDetail.settings
											.admin_url,
										{
											page: 'wp-pulse',
											user_id: record.user_id,
										}
									) }
								>
									{ record.display_name }
								</a>
								{' '}(
								<a
									href={ addQueryArgs(
										window.PulseAdminPulseDetail.settings
											.admin_url + 'user-edit.php',
										{ user_id: record.user_id }
									) }
								>
									Edit User
								</a>
								)
							</td>
						</tr>
					) }
					<tr>
						<th scope="row">
							<label>
								{ __( 'IP', 'pulse' ) }
							</label>
						</th>
						<td>
							<a
								href={ addQueryArgs(
									window.PulseAdminPulseDetail.settings
										.admin_url,
									{ page: 'wp-pulse', ip: record.ip }
								) }
							>
								{ record.ip }
							</a>
						</td>
					</tr>
				</tbody>
			</table>

			{ Object.keys( meta ?? {} ).length > 0 && (
				<>
					<h2>{ __( 'Meta', 'pulse' ) }</h2>
					<table className="form-table striped">
						<tbody>
							{ Object.keys( meta ?? {} ).map( ( key ) => (
								<tr key={ key }>
									<th scope="row">
										<label>
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
