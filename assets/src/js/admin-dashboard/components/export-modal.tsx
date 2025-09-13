/// <reference path="../../admin-dashboard/globals.d.ts" />
/* eslint-disable import/no-extraneous-dependencies */
/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable camelcase */

import apiFetch from '@wordpress/api-fetch';
import { Button, Modal } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { BsFiletypeCsv, BsFiletypeJson } from 'react-icons/bs';
import { Filters } from '../types';

// Styles.
import './export-modal.scss';

type ExportModalProps = {
	exportType: 'csv' | 'json';
	filters: Filters;
	label: string;
};

type FieldDefinition = {
	label: string;
	value: string;
};

// Define available fields outside component to prevent recreation on each render.
const AVAILABLE_FIELDS: FieldDefinition[] = [
	{ label: 'ID', value: 'id' },
	{ label: 'Created At', value: 'created_at' },
	{ label: 'Description', value: 'description' },
	{ label: 'Pulse', value: 'pulse' },
	{ label: 'Context', value: 'context' },
	{ label: 'Action', value: 'action' },
	{ label: 'User ID', value: 'user_id' },
	{ label: 'Object ID', value: 'object_id' },
	{ label: 'IP', value: 'ip' },
];

export default function ExportModal( {
	exportType,
	filters,
	label,
}: ExportModalProps ) {
	const [ isOpen, setOpen ] = useState( false );
	const openModal = () => setOpen( true );
	const closeModal = () => setOpen( false );

	// Use a Set for O(1) lookups and simpler state management.
	const [ selectedFields, setSelectedFields ] = useState< Set< string > >(
		new Set( AVAILABLE_FIELDS.map( ( field ) => field.value ) ) // All selected by default.
	);
	const [ isExporting, setIsExporting ] = useState( false );

	const handleFieldToggle = ( fieldValue: string ) => {
		setSelectedFields( ( prev ) => {
			const newSet = new Set( prev );
			if ( newSet.has( fieldValue ) ) {
				newSet.delete( fieldValue );
			} else {
				newSet.add( fieldValue );
			}
			return newSet;
		} );
	};

	const handleExport = async () => {
		setIsExporting( true );

		try {
			const response = ( await apiFetch( {
				path: '/wp-pulse/v1/export',
				method: 'POST',
				data: {
					filters,
					selectedFields: Array.from( selectedFields ),
					exportType,
				},
			} ) ) as {
				downloadUrl: string;
				filename: string;
				size: number;
			};

			// Simply redirect to the download URL - browser handles the rest.
			window.location.href = response.downloadUrl;

			// Close modal after successful export.
			closeModal();
		} catch ( error ) {
			// eslint-disable-next-line no-console
			console.error( 'Export failed:', error );
			// TODO: Show user-friendly error message.
		} finally {
			setIsExporting( false );
		}
	};

	const getIconByExportType = () => {
		switch ( exportType ) {
			case 'csv':
				return <BsFiletypeCsv size={ 16 } />;
			case 'json':
				return <BsFiletypeJson size={ 16 } />;
		}
	};

	return (
		<>
			<Button variant="secondary" onClick={ openModal }>
				{ getIconByExportType() } { label }
			</Button>
			{ isOpen && (
				<Modal
					className="pulse-export-modal"
					title={ label }
					onRequestClose={ closeModal }
				>
					<div className="pulse-export-modal-content">
						<h2>{ __( 'Fields to export', 'pulse' ) }</h2>

						<div className="pulse-export-modal-fields">
							{ AVAILABLE_FIELDS.map( ( field ) => (
								<div key={ field.value }>
									{ /* eslint-disable-next-line jsx-a11y/label-has-associated-control */ }
									<label>
										<input
											type="checkbox"
											name={ field.value }
											checked={ selectedFields.has(
												field.value
											) }
											onChange={ () =>
												handleFieldToggle( field.value )
											}
											value={ field.value }
										/>
										{ field.label }
									</label>
								</div>
							) ) }
						</div>

						<Button
							className="pulse-export-modal-button"
							variant="primary"
							disabled={
								selectedFields.size === 0 || isExporting
							}
							onClick={ handleExport }
							isBusy={ isExporting }
						>
							{ isExporting
								? __( 'Exporting…', 'pulse' )
								: `${ label } (${ selectedFields.size } ${
										selectedFields.size === 1
											? __( 'field', 'pulse' )
											: __( 'fields', 'pulse' )
								  })` }
						</Button>
					</div>
				</Modal>
			) }
		</>
	);
}
