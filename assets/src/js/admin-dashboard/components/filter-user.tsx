/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import React from 'react';
import Select from 'react-select';
import { UserOption } from '../types';

const userOptions: UserOption[] = [
	{
		value: '1',
		label: 'Scott Weaver',
		image: 'https://www.gravatar.com/avatar/1?d=mm',
	},
	{
		value: '2',
		label: 'Scott Adrian',
		image: 'https://www.gravatar.com/avatar/2?d=mm',
	},
];

type FilterUserProps = {
	userId: number | null;
	setUserId: ( userId: number | null ) => void;
	setPaged: ( paged: number ) => void;
};

/**
 * Filter user.
 *
 * @param root0
 * @param root0.userId    The user ID.
 * @param root0.setUserId The function to set the user ID.
 * @param root0.setPaged  The function to set the page.
 * @return The filter user.
 */
export default function FilterUser( {
	userId,
	setUserId,
	setPaged,
}: FilterUserProps ) {
	return (
		<Select
			defaultValue={ userOptions.find(
				( option ) => option.value === userId?.toString()
			) }
			formatOptionLabel={ ( option: UserOption ) => (
				<div className="user-option">
					<img src={ option.image } alt="" />
					<span>{ option.label }</span>
				</div>
			) }
			isClearable
			isSearchable={ false }
			onChange={ ( option ) => {
				setUserId( null === option ? null : Number( option?.value ) );
				setPaged( 1 );
			} }
			options={ userOptions }
			placeholder="All users"
		/>
	);
}
