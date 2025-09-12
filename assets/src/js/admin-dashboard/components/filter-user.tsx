/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import { __ } from '@wordpress/i18n';
import React from 'react';
import AsyncSelect from 'react-select/async';
import { UserOption } from '../types';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

type FilterUserProps = {
	userId: number | null;
	setUserId: ( userId: number | null ) => void;
	setPaged: ( paged: number ) => void;
	users: UserOption[];
};

/**
 * Load options.
 *
 * @param inputValue The input value.
 * @return The options.
 */
const loadOptions = async ( inputValue: string ) => {
	if ( '' === inputValue ) {
		return [];
	}

	if ( 3 > inputValue.length ) {
		return [];
	}

	const response = await apiFetch( {
		path: addQueryArgs( '/wp/v2/users', {
			search: inputValue,
		} ),
		method: 'GET',
		parse: false,
	} );
	const data = await response.json();
	return data.map( ( user: UserOption ) => ( {
		avatar_urls: user.avatar_urls,
		id: user.id,
		name: user.name,
		value: user.id,
	} ) );
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
		<AsyncSelect
			cacheOptions
			className="user-filter"
			classNames={ {
				input: () => 'user-filter-input',
			} }
			defaultOptions={ users }
			defaultValue={ users?.find(
				( option ) => Number( option.id ) === Number( userId )
			) }
			loadOptions={ loadOptions }
			formatOptionLabel={ ( option: UserOption ) => (
				<div className="user-option">
					<img src={ option.avatar_urls[ 96 ] } alt="" />
					<span>{ option.name }</span>
				</div>
			) }
			isClearable
			isSearchable={ true }
			onChange={ ( option ) => {
				setUserId( null === option ? null : Number( option?.id ) );
				setPaged( 1 );
			} }
			placeholder={ __( 'All users', 'pulse' ) }
		/>
	);
}
