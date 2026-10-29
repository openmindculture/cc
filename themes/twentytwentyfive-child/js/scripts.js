/**
 * Front-end script, loaded deferred. The design itself needs no JavaScript;
 * this only powers the opt-in "Read more" Details block style.
 */
document.addEventListener( 'DOMContentLoaded', function () {
	const closeButtons = document.querySelectorAll( 'details.is-style-read-more .wp-block-details-close' );
	for ( const closeButton of closeButtons ) {
		closeButton.addEventListener( 'click', () => {
			const details = closeButton.closest( 'details' );
			details.removeAttribute( 'open' );
			details.querySelector( 'summary' )?.focus();
		} );
	}
} );
