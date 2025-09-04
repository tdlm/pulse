import apiFetch from '@wordpress/api-fetch';

import '../scss/admin-settings.scss';

document.addEventListener( 'DOMContentLoaded', () => {

    const checkboxKeepDays = document.querySelector( '.pulse-keep-days' );
    const checkboxKeepForever = document.querySelector( '.pulse-keep-forever' );

    if (checkboxKeepDays && checkboxKeepForever) {
        // On load, if the keep forever checkbox is checked, hide the keep days checkbox.
        if (checkboxKeepForever.checked) {
            checkboxKeepDays.closest('tr').classList.add( 'hidden' );
        }

        // On change, if the keep forever checkbox is checked, hide the keep days checkbox.
        // If the keep forever checkbox is unchecked, show the keep days checkbox.
        checkboxKeepForever.addEventListener( 'change', ( e ) => {
            e.preventDefault();

            if (e.target.checked) {
                checkboxKeepDays.closest('tr').classList.add( 'hidden' );
            } else {
                checkboxKeepDays.closest('tr').classList.remove( 'hidden' );
            }
        } );
    }

	const resetDatabaseButton = document.querySelector(
		'#reset_all_pulses'
	);

	if ( resetDatabaseButton ) {
		resetDatabaseButton.addEventListener( 'click', ( e ) => {
			e.preventDefault();

			if (
				// eslint-disable-next-line no-alert
				window.confirm(
					'Are you sure you want to reset the Pulse database?'
				)
			) {
				apiFetch( {
					path: '/wp-pulse/v1/database/reset',
					method: 'POST',
				} )
					.then( () => {
						// eslint-disable-next-line no-alert
						window.alert( 'Pulse database reset.' );
					} )
					.catch( ( error ) => {
						// eslint-disable-next-line no-console
						console.error( error );
					} );
			}
		} );
	}
} );
