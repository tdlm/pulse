/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable import/no-unresolved */

import React from 'react';
import Select from 'react-select';
import { Option } from '../types';

const options: Option[] = [
	{ value: 'today', label: 'Today' },
	{ value: 'yesterday', label: 'Yesterday' },
	{ value: 'last_7_days', label: 'Last 7 Days' },
	{ value: 'last_30_days', label: 'Last 30 Days' },
	{ value: 'this_month', label: 'This Month' },
	{ value: 'last_month', label: 'Last Month' },
	{ value: 'last_6_months', label: 'Last 6 Months' },
	{ value: 'last_12_months', label: 'Last 12 Months' },
	{ value: 'this_year', label: 'This Year' },
	{ value: 'last_year', label: 'Last Year' },
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
			placeholder="All dates"
		/>
	);
}
