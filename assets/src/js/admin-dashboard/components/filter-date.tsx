/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import { __ } from '@wordpress/i18n';
import React from 'react';
import Select from 'react-select';
import { Option } from '../types';

const options: Option[] = [
	{ value: 'today', label: __( 'Today', 'pulse' ) },
	{ value: 'yesterday', label: __( 'Yesterday', 'pulse' ) },
	{ value: 'last_7_days', label: __( 'Last 7 Days', 'pulse' ) },
	{ value: 'last_30_days', label: __( 'Last 30 Days', 'pulse' ) },
	{ value: 'this_month', label: __( 'This Month', 'pulse' ) },
	{ value: 'last_month', label: __( 'Last Month', 'pulse' ) },
	{ value: 'last_6_months', label: __( 'Last 6 Months', 'pulse' ) },
	{ value: 'last_12_months', label: __( 'Last 12 Months', 'pulse' ) },
	{ value: 'this_year', label: __( 'This Year', 'pulse' ) },
	{ value: 'last_year', label: __( 'Last Year', 'pulse' ) },
];

type FilterDateProps = {
	dateRange: string;
	setDateRange: ( date_range: string ) => void;
	setPaged: ( paged: number ) => void;
};

/**
 * Filter date.
 *
 * @param root0
 * @param root0.dateRange    The date range.
 * @param root0.setDateRange The function to set the date range.
 * @param root0.setPaged     The function to set the page.
 * @return The filter date.
 */
export default function FilterDate( {
	dateRange,
	setDateRange,
	setPaged,
}: FilterDateProps ) {
	return (
		<Select
			className="date-filter"
			defaultValue={ options.find(
				( option ) => option.value === dateRange
			) }
			isClearable
			isSearchable={ false }
			onChange={ ( option ) => {
				setDateRange( option?.value ?? '' );
				setPaged( 1 );
			} }
			options={ options }
			placeholder={ __( 'All dates', 'pulse' ) }
		/>
	);
}
