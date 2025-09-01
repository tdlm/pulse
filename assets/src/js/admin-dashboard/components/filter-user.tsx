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
	user_id: number | null;
	setUserId: ( user_id: number | null ) => void;
	setPaged: ( paged: number ) => void;
};

/**
 * Filter user.
 *
 * @param user_id.user_id
 * @param user_id           The user ID.
 * @param setUserId         The function to set the user ID.
 * @param setPaged          The function to set the page.
 * @param user_id.setUserId
 * @param user_id.setPaged
 * @return The filter user.
 */
export default function FilterUser( {
	user_id,
	setUserId,
	setPaged,
}: FilterUserProps ) {
	return (
		<Select
            defaultValue={ userOptions.find(
                ( option ) => option.value === user_id?.toString()
            ) }
			formatOptionLabel={ ( option: UserOption ) => (
				<div className="user-option">
					<img src={ option.image } />
					<span>{ option.label }</span>
				</div>
			) }
			isClearable
            isSearchable={ false }
			onChange={ ( option ) => {
				null === option
					? setUserId( null )
					: setUserId( Number( option?.value ) );
				setPaged( 1 );
			} }
			options={ userOptions }
            placeholder="All users"
		/>
	);
}
