/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import React, { useState } from 'react';
import moment from 'moment';
import TimeAgo from 'react-timeago';
import { makeIntlFormatter } from 'react-timeago/defaultFormatter';
import { Record } from '../types';

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
};

/**
 * Data row for the admin dashboard.
 *
 * @param root0
 * @param root0.record
 * @return The data row.
 */
export default function DataRow( { record }: DataRowProps ) {
	const [ isExpanded, setIsExpanded ] = useState( false );

	return (
		<tr>
			<td data-colname="Expand Toggle">
				<button
					aria-label="Expand row"
					className={ `pulse-expand-toggle dashicons ${
						isExpanded
							? 'dashicons-arrow-down'
							: 'dashicons-arrow-right'
					}` }
					type="button"
					onClick={ () => setIsExpanded( ! isExpanded ) }
				>
					<span className="screen-reader-text">
						Show more details
					</span>
				</button>
			</td>
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
					href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&created_at=${ moment( record.created_at ).format( 'YYYY-MM-DD' ) }` }
				>
					<time dateTime={ record.created_at }>
						{ moment( record.created_at ).format( 'YYYY/MM/DD' ) }
					</time>
				</a>
				<br />
				<span>{ moment( record.created_at ).format( 'hh:mm:ssA' ) }</span>
			</td>
			<td data-colname="Description">
				<p>{ record.description }</p>
				{ isExpanded && (
					<div className="pulse-row-details">
						<pre>{ JSON.stringify( record, null, 2 ) }</pre>
					</div>
				) }
			</td>
			<td data-colname="User">
				<a
					href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&user_id=${ record.user_id }` }
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
					<a
						href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&user_id=${ record.user_id }` }
					>
						{ record.display_name }
					</a>

					<br />
					<small>{ record.user_roles.join( ', ' ) }</small>
					<br />
					<a
						title=""
						href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&ip=${ record.ip }` }
					>
						{ record.ip }
					</a>
				</div>
			</td>
			<td data-colname="Context">
				<a
					title=""
					href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&pulse=${ record.pulse }` }
				>
					{ record.pulse_label }
				</a>
				<br />
				↳&nbsp;
				<a
					title=""
					href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&pulse=${ record.pulse }&context=${ record.context }` }
				>
					{ record.context_label }
				</a>
			</td>
			<td data-colname="Action">
				<a
					title=""
					href={ `http://localhost:8888/wp-admin/admin.php?page=wp-pulse&action=${ record.action }` }
				>
					{ record.action_label }
				</a>
			</td>
		</tr>
	);
}
