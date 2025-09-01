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
	date_range: string;
	setDateRange: ( date_range: string ) => void;
	setPaged: ( paged: number ) => void;
};

/**
 * Filter date.
 *
 * @param date_range.date_range
 * @param date_range              The date range.
 * @param setDateRange            The function to set the date range.
 * @param setPaged                The function to set the page.
 * @param date_range.setDateRange
 * @param date_range.setPaged
 * @return The filter date.
 */
export default function FilterDate( {
	date_range,
	setDateRange,
	setPaged,
}: FilterDateProps ) {
	return (
		<Select
			defaultValue={ options.find(
				( option ) => option.value === date_range
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
