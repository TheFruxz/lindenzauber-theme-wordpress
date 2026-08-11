/**
 * Lindenzauber – Menü auf kleinen Bildschirmen.
 */
( function () {
	'use strict';

	var toggle = document.querySelector( '.nav-toggle' );
	var nav = document.getElementById( 'hauptmenue' );

	if ( ! toggle || ! nav ) {
		return;
	}

	function schliessen() {
		nav.classList.remove( 'is-open' );
		toggle.setAttribute( 'aria-expanded', 'false' );
	}

	toggle.addEventListener( 'click', function () {
		var offen = nav.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', offen ? 'true' : 'false' );
	} );

	nav.addEventListener( 'click', function ( ereignis ) {
		if ( ereignis.target.closest( 'a' ) ) {
			schliessen();
		}
	} );

	document.addEventListener( 'keydown', function ( ereignis ) {
		if ( 'Escape' === ereignis.key && nav.classList.contains( 'is-open' ) ) {
			schliessen();
			toggle.focus();
		}
	} );

	document.addEventListener( 'click', function ( ereignis ) {
		if ( ! nav.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( ! nav.contains( ereignis.target ) && ! toggle.contains( ereignis.target ) ) {
			schliessen();
		}
	} );

	window.addEventListener( 'resize', function () {
		if ( window.innerWidth > 940 ) {
			schliessen();
		}
	} );
}() );
