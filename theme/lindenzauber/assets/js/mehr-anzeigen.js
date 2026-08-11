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
 * Das Auf- und Zuklappen läuft als Übergang von einer gemessenen Höhe zur
 * anderen. Danach wird die Höhe wieder freigegeben, damit ein späterer
 * Umbruch – etwa beim Schmalerziehen des Fensters – nichts abschneidet.
 *
 * Ohne JavaScript passiert nichts davon – dann steht schlicht der
 * vollständige Text da. Es geht also nie Inhalt verloren.
 */
( function () {
	'use strict';

	// Sperre gegen einen zweiten Durchlauf. Wird diese Datei versehentlich
	// zweimal eingebunden, würde der zweite Lauf über bereits umgebaute
	// Porträts stolpern. Einmal ist genug.
	if ( window.lzMehrLaeuft ) {
		return;
	}

	window.lzMehrLaeuft = true;

	var TEXTE = window.lzMehrTexte || { mehr: 'Mehr anzeigen', weniger: 'Weniger anzeigen' };
	var GRENZE = 19 * 16; // ab dieser Höhe in Pixeln wird eingeklappt
	var HOEHE = 17 * 16;  // sichtbare Höhe im eingeklappten Zustand
	var zaehler = 0;

	var ruhig = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

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
	 * Klappt sanft auf oder zu.
	 *
	 * @param {HTMLElement} huelle  Der Rahmen mit dem Zustand.
	 * @param {HTMLElement} inhalt  Der Textteil, dessen Höhe sich ändert.
	 * @param {boolean}     oeffnen true = aufklappen.
	 */
	function umschalten( huelle, inhalt, oeffnen ) {
		var von = inhalt.getBoundingClientRect().height;

		// Für die Messung kurz freigeben, dann sofort zurücksetzen.
		// Gemessen wird die tatsächliche Höhe im Layout – scrollHeight liegt
		// hier daneben, weil es Ränder der letzten Zeile mitzählt, und die
		// Bewegung würde am Ende sichtbar zurückspringen.
		huelle.classList.remove( 'is-fertig' );
		inhalt.style.height = 'auto';
		var nach = oeffnen ? inhalt.getBoundingClientRect().height : HOEHE;
		inhalt.style.height = von + 'px';

		huelle.setAttribute( 'data-lz-mehr', oeffnen ? 'auf' : 'zu' );

		if ( ruhig ) {
			inhalt.style.height = oeffnen ? 'auto' : HOEHE + 'px';

			if ( oeffnen ) {
				huelle.classList.add( 'is-fertig' );
			}

			return;
		}

		// Ein Bildaufbau abwarten, sonst springt der Browser direkt zum Ziel.
		requestAnimationFrame( function () {
			requestAnimationFrame( function () {
				inhalt.style.height = nach + 'px';
			} );
		} );
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
			huelle.classList.add( 'is-fertig' );
			huelle.setAttribute( 'data-lz-mehr', 'auf' );
			inhalt.style.height = 'auto';

			return;
		}

		huelle.setAttribute( 'data-lz-mehr', 'zu' );
		inhalt.style.height = HOEHE + 'px';

		var schalter = document.createElement( 'button' );
		schalter.type = 'button';
		schalter.className = 'lz-mehr__schalter';
		schalter.textContent = TEXTE.mehr;
		schalter.setAttribute( 'aria-expanded', 'false' );
		schalter.setAttribute( 'aria-controls', inhalt.id );

		schalter.addEventListener( 'click', function () {
			var offen = 'auf' === huelle.getAttribute( 'data-lz-mehr' );

			umschalten( huelle, inhalt, ! offen );
			schalter.setAttribute( 'aria-expanded', offen ? 'false' : 'true' );
			schalter.textContent = offen ? TEXTE.mehr : TEXTE.weniger;

			if ( offen ) {
				// Beim Zuklappen den Anfang des Porträts wieder ins Bild holen.
				var oben = huelle.getBoundingClientRect().top;

				if ( oben < 0 ) {
					huelle.scrollIntoView( { block: 'start', behavior: ruhig ? 'auto' : 'smooth' } );
				}
			}
		} );

		// Nach dem Aufklappen die Höhe freigeben, damit späteres Umbrechen
		// den Text nicht abschneidet.
		inhalt.addEventListener( 'transitionend', function ( ereignis ) {
			if ( 'height' !== ereignis.propertyName ) {
				return;
			}

			if ( 'auf' === huelle.getAttribute( 'data-lz-mehr' ) ) {
				huelle.classList.add( 'is-fertig' );
				inhalt.style.height = 'auto';
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
