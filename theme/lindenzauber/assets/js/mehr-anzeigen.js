/**
 * Lindenzauber – lange Porträttexte einklappen.
 *
 * Zwei Aufgaben, damit im Editor nichts eingestellt werden muss:
 *
 * 1. Bei den Porträts der Erzählenden wechselt das Bild automatisch die
 *    Seite: erstes links, zweites rechts, drittes links …
 * 2. Ist ein Porträttext länger als etwa acht Zeilen, wird er weich
 *    ausgeblendet und bekommt einen Schalter "Mehr anzeigen".
 *
 * Ohne JavaScript passiert nichts davon – dann steht schlicht der
 * vollständige Text da. Es geht also nie Inhalt verloren.
 */
( function () {
	'use strict';

	var TEXTE = window.lzMehrTexte || { mehr: 'Mehr anzeigen', weniger: 'Weniger anzeigen' };
	var GRENZE = 19 * 16; // ab dieser Höhe in Pixeln wird eingeklappt
	var HOEHE = 17 * 16;  // sichtbare Höhe im eingeklappten Zustand
	var zaehler = 0;

	function istUeberschrift( element ) {
		return /^H[1-6]$/.test( element.tagName );
	}

	/**
	 * Findet die Spalte mit dem Text (die, in der eine Überschrift steht).
	 */
	function textspalte( portraet ) {
		var spalten = portraet.querySelectorAll( ':scope > .wp-block-column' );
		var i;

		for ( i = 0; i < spalten.length; i++ ) {
			if ( spalten[ i ].querySelector( 'h1, h2, h3, h4, h5, h6' ) ) {
				return spalten[ i ];
			}
		}

		return null;
	}

	/**
	 * Klappt den Text unterhalb von Name und Rolle ein.
	 */
	function einklappen( spalte ) {
		var kinder = Array.prototype.slice.call( spalte.children );
		var start = 0;
		var i;

		// Name und Rolle bleiben immer sichtbar.
		for ( i = 0; i < kinder.length; i++ ) {
			if ( istUeberschrift( kinder[ i ] ) || kinder[ i ].classList.contains( 'is-style-lz-rolle' ) ) {
				start = i + 1;
			}
		}

		var rest = kinder.slice( start );

		if ( rest.length < 2 ) {
			return;
		}

		var huelle = document.createElement( 'div' );
		huelle.className = 'lz-mehr';

		var inhalt = document.createElement( 'div' );
		inhalt.className = 'lz-mehr__inhalt';
		zaehler += 1;
		inhalt.id = 'lz-mehr-' + zaehler;

		spalte.insertBefore( huelle, rest[ 0 ] );
		huelle.appendChild( inhalt );
		rest.forEach( function ( element ) {
			inhalt.appendChild( element );
		} );

		// Erst jetzt messen – vorher steht der Text noch woanders.
		if ( inhalt.scrollHeight <= GRENZE ) {
			return;
		}

		huelle.style.setProperty( '--lz-mehr-hoehe', HOEHE + 'px' );
		huelle.setAttribute( 'data-lz-mehr', 'zu' );

		var schalter = document.createElement( 'button' );
		schalter.type = 'button';
		schalter.className = 'lz-mehr__schalter';
		schalter.textContent = TEXTE.mehr;
		schalter.setAttribute( 'aria-expanded', 'false' );
		schalter.setAttribute( 'aria-controls', inhalt.id );

		schalter.addEventListener( 'click', function () {
			var offen = 'auf' === huelle.getAttribute( 'data-lz-mehr' );

			huelle.setAttribute( 'data-lz-mehr', offen ? 'zu' : 'auf' );
			schalter.setAttribute( 'aria-expanded', offen ? 'false' : 'true' );
			schalter.textContent = offen ? TEXTE.mehr : TEXTE.weniger;

			if ( offen ) {
				// Beim Zuklappen wieder an den Anfang des Porträts springen.
				var oben = huelle.getBoundingClientRect().top;

				if ( oben < 0 ) {
					huelle.scrollIntoView( { block: 'start', behavior: 'smooth' } );
				}
			}
		} );

		huelle.appendChild( schalter );
	}

	function start() {
		var portraets = document.querySelectorAll( '.wp-block-columns.is-style-lz-portraet' );

		portraets.forEach( function ( portraet, index ) {
			// Bild abwechselnd links und rechts.
			portraet.classList.add( index % 2 === 0 ? 'lz-bild-links' : 'lz-bild-rechts' );

			var spalte = textspalte( portraet );

			if ( spalte ) {
				einklappen( spalte );
			}
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
}() );
