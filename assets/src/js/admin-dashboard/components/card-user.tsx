/* eslint-disable import/no-extraneous-dependencies */

import React from 'react';
import { Record } from '../types';

type CardUserProps = {
	record: Record;
	setUserId: ( userId: number ) => void;
	setPaged: ( paged: number ) => void;
	setIp: ( ip: string ) => void;
};

export default function CardUser( {
	record,
	setUserId,
	setPaged,
	setIp,
}: CardUserProps ) {
	return (
		<>
			<a
				href={ `/admin.php?page=wp-pulse&user_id=${ record.user_id }` }
				onClick={ ( e ) => {
					e.preventDefault();
					setUserId( record.user_id );
					setPaged( 1 );
				} }
			>
				{ record.display_name }
			</a>

			<br />
			<small>{ record.user_roles.join( ', ' ) }</small>
			<br />
			<a
				title=""
				href={ `/admin.php?page=wp-pulse&ip=${ record.ip }` }
				onClick={ ( e ) => {
					e.preventDefault();
					setIp( record.ip );
					setPaged( 1 );
				} }
			>
				{ record.ip }
			</a>
		</>
	);
}
