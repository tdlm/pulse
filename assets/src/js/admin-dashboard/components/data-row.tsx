/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import { addQueryArgs } from '@wordpress/url';
import moment from 'moment';
import React from 'react';
import TimeAgo from 'react-timeago';
import { makeIntlFormatter } from 'react-timeago/defaultFormatter';
import { Record } from '../types';
import CardUser from './card-user';

const intlFormatter = makeIntlFormatter( {
	locale: undefined, // string
	localeMatcher: 'best fit', // 'lookup' | 'best fit',
	numberingSystem: 'latn', // Intl$NumberingSystem such as 'arab', 'deva', 'hebr' etc.
	style: 'long', // 'long' | 'short' | 'narrow',
	numeric: 'auto', //  'always' | 'auto', Using 'auto` will convert "1 day ago" to "yesterday" etc.
} );

type DataRowProps = {
	key: string | number;
	record: Record;
	setAction: ( action: string ) => void;
	setContext: ( context: string ) => void;
	setCreatedAt: ( createdAt: string ) => void;
	setIp: ( ip: string ) => void;
	setPaged: ( paged: number ) => void;
	setPulse: ( pulse: string ) => void;
	setUserId: ( userId: number ) => void;
};

/**
 * Data row for the admin dashboard.
 *
 * @param root0
 * @param root0.record
 * @param root0.setAction
 * @param root0.setContext
 * @param root0.setCreatedAt
 * @param root0.setIp
 * @param root0.setPaged
 * @param root0.setPulse
 * @param root0.setUserId
 * @return The data row.
 */
export default function DataRow( {
	record,
	setAction,
	setContext,
	setCreatedAt,
	setIp,
	setPaged,
	setPulse,
	setUserId,
}: DataRowProps ) {
	return (
		<tr>
			<td data-colname="Date">
				<strong>
					<TimeAgo
						date={ record.created_at }
						formatter={ intlFormatter }
					/>
				</strong>
				<br />
				<a
					title=""
					href={ addQueryArgs(
						window.PulseAdminDashboard.settings.dashboard_base_url,
						{
							created_at: moment( record.created_at ).format(
								'YYYY-MM-DD'
							),
						}
					) }
					onClick={ ( e ) => {
						e.preventDefault();
						setCreatedAt(
							moment( record.created_at ).format( 'YYYY-MM-DD' )
						);
						setPaged( 1 );
					} }
				>
					<time dateTime={ record.created_at }>
						{ moment( record.created_at ).format( 'YYYY/MM/DD' ) }
					</time>
				</a>
				<br />
				<span>
					{ moment( record.created_at ).format( 'hh:mm:ssA' ) }
				</span>
			</td>
			<td data-colname="Description">
				<div className="pulse-row-description">
					{ record.description }
					{ ( Object.keys( record.pulse_links ).length > 0 && (
						<div className="row-actions">
							{ Object.keys( record.pulse_links ).map(
								( key, _ ) => (
									<React.Fragment key={ key }>
										<span>
											<a
												href={ decodeURIComponent(
													decodeURIComponent(
														record.pulse_links[
															key
														]
													)
												) }
											>
												{ key }
											</a>
										</span>
										{ _ <
											Object.keys( record.pulse_links )
												.length -
												1 && <span> | </span> }
									</React.Fragment>
								)
							) }
						</div>
					) ) || <div className="row-actions">&nbsp;</div> }
				</div>
			</td>
			<td data-colname="User">
				<a
					href={ addQueryArgs(
						window.PulseAdminDashboard.settings.dashboard_base_url,
						{ user_id: record.user_id }
					) }
					onClick={ ( e ) => {
						e.preventDefault();
						setUserId( record.user_id );
						setPaged( 1 );
					} }
				>
					<img
						src={ record.gravatar_url }
						srcSet={ `${ record.gravatar_url_2x } 2x` }
						alt=""
						width="80"
						height="80"
					/>
				</a>
				<div>
					<CardUser
						record={ record }
						setUserId={ setUserId }
						setPaged={ setPaged }
						setIp={ setIp }
					/>
				</div>
			</td>
			<td data-colname="Context">
				<a
					title=""
					href={ addQueryArgs(
						window.PulseAdminDashboard.settings.dashboard_base_url,
						{ pulse: record.pulse }
					) }
					onClick={ ( e ) => {
						e.preventDefault();
						setPulse( record.pulse );
						setPaged( 1 );
					} }
				>
					{ record.pulse_label }
				</a>
				<br />
				↳&nbsp;
				<a
					title=""
					href={ addQueryArgs(
						window.PulseAdminDashboard.settings.dashboard_base_url,
						{ pulse: record.pulse, context: record.context }
					) }
					onClick={ ( e ) => {
						e.preventDefault();
						setPulse( record.pulse );
						setContext( record.context );
						setPaged( 1 );
					} }
				>
					{ record.context_label }
				</a>
			</td>
			<td data-colname="Action">
				<a
					title=""
					href={ addQueryArgs(
						window.PulseAdminDashboard.settings.dashboard_base_url,
						{ action: record.action }
					) }
					onClick={ ( e ) => {
						e.preventDefault();
						setAction( record.action );
						setPaged( 1 );
					} }
				>
					{ record.action_label }
				</a>
			</td>
		</tr>
	);
}
