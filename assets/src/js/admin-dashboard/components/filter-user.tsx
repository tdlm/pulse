/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import React from 'react';
import Select from 'react-select';
import { UserOption } from '../types';

type FilterUserProps = {
	userId: number | null;
	setUserId: ( userId: number | null ) => void;
	setPaged: ( paged: number ) => void;
	users: UserOption[];
};

/**
 * Filter user.
 *
 * @param root0
 * @param root0.userId    The user ID.
 * @param root0.setUserId The function to set the user ID.
 * @param root0.setPaged  The function to set the page.
 * @param root0.users     The users.
 * @return The filter user.
 */
export default function FilterUser( {
	userId,
	setUserId,
	setPaged,
	users,
}: FilterUserProps ) {
	return (
		<Select
			className="user-filter"
			defaultValue={ users?.find(
				( option ) => Number( option.id ) === Number( userId )
			) }
			formatOptionLabel={ ( option: UserOption ) => (
				<div className="user-option">
					<img src={ option.gravatar_url } alt="" />
					<span>{ option.name }</span>
				</div>
			) }
			isClearable
			isSearchable={ false }
			onChange={ ( option ) => {
				setUserId( null === option ? null : Number( option?.id ) );
				setPaged( 1 );
			} }
			options={ users }
			placeholder="All users"
		/>
	);
}
