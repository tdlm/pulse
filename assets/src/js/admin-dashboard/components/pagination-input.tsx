/* eslint-disable import/no-extraneous-dependencies */
import React from 'react';

type PaginationInputProps = {
	paged: number;
	setPage: ( page: number ) => void;
};

/**
 * Pagination input.
 *
 * @param root0
 * @param root0.paged   The current page.
 * @param root0.setPage The function to set the page.
 * @return The pagination input.
 */
export default function PaginationInput( {
	paged,
	setPage,
}: PaginationInputProps ) {
	return (
		<input
			aria-describedby="table-paging"
			className="current-page"
			value={ paged }
			name="paged"
			size={ 1 }
			type="text"
			onChange={ ( e ) => setPage( Number( e.target.value ) ) }
		/>
	);
}
