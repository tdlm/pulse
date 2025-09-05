/* eslint-disable import/no-extraneous-dependencies */

import { __ } from '@wordpress/i18n';
import React from 'react';
import { addQueryArgs } from '@wordpress/url';

type PaginationButtonProps = {
	currentPage: number;
	page: number;
	setPage: ( page: number ) => void;
	totalPages: number;
	type: 'first' | 'previous' | 'next' | 'last';
};

const typeMap = {
	first: '«',
	previous: '‹',
	next: '›',
	last: '»',
};

const screenReaderTextMap = {
	first: __( 'First page', 'pulse' ),
	previous: __( 'Previous page', 'pulse' ),
	next: __( 'Next page', 'pulse' ),
	last: __( 'Last page', 'pulse' ),
};

export default function PaginationButton( {
	currentPage = 1,
	page = 1,
	setPage,
	totalPages = 1,
	type,
}: PaginationButtonProps ) {
	if ( currentPage <= 1 && ( type === 'first' || type === 'previous' ) ) {
		return (
			<span
				className="tablenav-pages-navspan button disabled"
				aria-hidden="true"
			>
				{ typeMap[ type ] }
			</span>
		);
	}

	if ( currentPage >= totalPages && ( type === 'next' || type === 'last' ) ) {
		return (
			<span
				className="tablenav-pages-navspan button disabled"
				aria-hidden="true"
			>
				{ typeMap[ type ] }
			</span>
		);
	}

	return (
		<a
			className="last-page button"
			href={ addQueryArgs(
				window.PulseAdminDashboard.settings.dashboard_base_url,
				{ paged: page }
			) }
			onClick={ ( e ) => {
				e.preventDefault();
				setPage( page );
			} }
		>
			<span className="screen-reader-text">
				{ screenReaderTextMap[ type ] }
			</span>
			<span aria-hidden="true">{ typeMap[ type ] }</span>
		</a>
	);
}
