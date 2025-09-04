/* eslint-disable import/no-extraneous-dependencies */
import { __ } from '@wordpress/i18n';
import React from 'react';
import PaginationButton from './pagination-button';
import PaginationInput from './pagination-input';

type PaginationProps = {
	paged: number;
	setPage: ( page: number ) => void;
	totalPages: number;
};

/**
 * Pagination.
 *
 * @param root0
 * @param root0.paged      The current page.
 * @param root0.setPage    The function to set the page.
 * @param root0.totalPages The total number of pages.
 * @return The pagination.
 */
export default function Pagination( {
	paged,
	setPage,
	totalPages,
}: PaginationProps ) {
	return (
		<span className="pagination-links">
			<PaginationButton
				setPage={ setPage }
				type="first"
				page={ 1 }
				currentPage={ Number( paged ) }
				totalPages={ totalPages }
			/>
			<PaginationButton
				setPage={ setPage }
				type="previous"
				page={ Number( paged ) - 1 }
				currentPage={ Number( paged ) }
				totalPages={ totalPages }
			/>
			<span className="paging-input">
				<label
					htmlFor="current-page-selector"
					className="screen-reader-text"
				>
					{ __( 'Current Page', 'pulse' ) }
				</label>
				<PaginationInput
					paged={ Number( paged ) }
					setPage={ setPage }
				/>
				<span className="tablenav-paging-text">
					{ ' ' }
					{ __( 'of', 'pulse' ) }{ ' ' }
					<span className="total-pages">{ totalPages }</span>
				</span>
			</span>
			<PaginationButton
				setPage={ setPage }
				type="next"
				page={ Number( paged ) + 1 }
				currentPage={ Number( paged ) }
				totalPages={ totalPages }
			/>
			<PaginationButton
				setPage={ setPage }
				type="last"
				page={ totalPages }
				currentPage={ Number( paged ) }
				totalPages={ totalPages }
			/>
		</span>
	);
}
