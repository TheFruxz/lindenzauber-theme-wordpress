/**
 * Lindenzauber – Kopfbereich und Menü.
 *
 * Drei Aufgaben:
 *
 * 1. Die tatsächliche Höhe des Kopfbereichs steht als CSS-Wert --lz-kopf
 *    bereit. Daraus rechnet das Stylesheet den Abstand für Sprungmarken –
 *    ein fester Wert würde daneben liegen, sobald jemand ein größeres Logo
 *    einsetzt.
 * 2. Sobald man den Seitenanfang verlässt, wird der Kopfbereich flacher.
 * 3. Auf kleinen Bildschirmen öffnet der Knopf das Menü als Vollbild.
 *
 * Ohne JavaScript bleibt der Kopfbereich in voller Höhe oben stehen – das
 * steht im Stylesheet und braucht hier nichts. Nur das Menü auf kleinen
 * Bildschirmen lässt sich dann nicht öffnen; dafür gibt es keine Lösung
 * ohne Skript, die nicht an anderer Stelle mehr kaputt macht.
 */
( function () {
	'use strict';

	// Sperre gegen einen zweiten Durchlauf. Wird diese Datei versehentlich
	// zweimal eingebunden, hingen zwei Klick-Behandler am Menüknopf: das
	// Menü öffnete und schloss sich im selben Klick und wirkte tot.
	if ( window.lzNavLaeuft ) {
		return;
	}

	window.lzNavLaeuft = true;

	var wurzel = document.documentElement;
	var kopf = document.querySelector( '.site-header' );

	if ( ! kopf ) {
		return;
	}

	/* ------------------------------------------- 1. Höhe bereitstellen */

	var zuletzt = -1;
	var zuletztVoll = -1;

	function kopfhoehe() {
		var hoch = Math.round( kopf.getBoundingClientRect().height );

		if ( hoch < 1 ) {
			return;
		}

		// Nur schreiben, wenn sich wirklich etwas geändert hat – während
		// des Übergangs meldet der Beobachter sonst jeden Zwischenschritt.
		if ( hoch !== zuletzt ) {
			zuletzt = hoch;
			wurzel.style.setProperty( '--lz-kopf', hoch + 'px' );
		}

		// Die Höhe im ausgefahrenen Zustand wird getrennt gemerkt. Daraus
		// rechnet der Kopfbereich der Startseite seine Höhe, damit die
		// Häuserreihe genau am Bildschirmrand steht. Nähme er --lz-kopf,
		// würde die Seite beim ersten Scrollen einen Satz machen, sobald
		// der Kopfbereich flacher wird.
		if ( ! kopf.classList.contains( 'is-kompakt' ) && hoch !== zuletztVoll ) {
			zuletztVoll = hoch;
			wurzel.style.setProperty( '--lz-kopf-voll', hoch + 'px' );
		}
	}

	kopfhoehe();

	if ( window.ResizeObserver ) {
		new ResizeObserver( kopfhoehe ).observe( kopf );
	} else {
		window.addEventListener( 'resize', kopfhoehe );
	}

	/* ------------------------------------------ 2. Flacher beim Scrollen */

	if ( window.IntersectionObserver && kopf.parentNode ) {
		var merker = document.createElement( 'div' );
		merker.className = 'lz-kopf-merker';
		merker.setAttribute( 'aria-hidden', 'true' );
		kopf.parentNode.insertBefore( merker, kopf );

		new IntersectionObserver( function ( eintraege ) {
			kopf.classList.toggle( 'is-kompakt', ! eintraege[ 0 ].isIntersecting );
		}, { threshold: 0 } ).observe( merker );
	}

	/* --------------------------------------------------- 3. Das Menü */

	var schalter = document.querySelector( '.nav-toggle' );
	var menue = document.getElementById( 'hauptmenue' );

	if ( ! schalter || ! menue ) {
		return;
	}

	// Die Vorgaben werden ergänzt, nicht ersetzt. Bringt die Seite nur einen
	// Teil der Texte mit, fehlten sonst genau die übrigen – und der Knopf
	// stünde ohne Aufschrift da.
	var TEXTE = {
		oeffnen: 'Menü öffnen',
		schliessen: 'Menü schließen',
		knopfAuf: 'Menü',
		knopfZu: 'Schließen',
	};

	if ( window.lzNavTexte ) {
		Object.keys( TEXTE ).forEach( function ( name ) {
			if ( window.lzNavTexte[ name ] ) {
				TEXTE[ name ] = window.lzNavTexte[ name ];
			}
		} );
	}
	var aufschrift = schalter.querySelector( '.nav-toggle__label' );
	var offen = false;
	var scrollstand = 0;

	/**
	 * Alles, was bei offenem Menü angesprungen werden kann: die Marke, der
	 * Schalter und die Einträge. Was keine Fläche hat, ist nicht sichtbar
	 * und zählt nicht mit.
	 */
	function anspringbar() {
		return Array.prototype.filter.call(
			kopf.querySelectorAll( 'a[href], button' ),
			function ( element ) {
				return element.getClientRects().length > 0;
			}
		);
	}

	/**
	 * Setzt die Seite fest, ohne dass sie nach oben springt.
	 */
	function festsetzen() {
		scrollstand = window.pageYOffset || wurzel.scrollTop || 0;
		document.body.style.top = -scrollstand + 'px';
		document.body.classList.add( 'lz-fest' );
	}

	function freigeben() {
		document.body.classList.remove( 'lz-fest' );
		document.body.style.top = '';

		// Zurück an die alte Stelle – und zwar sofort. Die Seite scrollt sonst
		// sanft, und die Rückkehr wäre eine sichtbare Fahrt über die halbe
		// Seite. Der Umweg über die Stilangabe allein reicht nicht: sie wirkt
		// erst nach der nächsten Stilrechnung, und scrollTo läuft davor.
		// Deshalb wird die Rechnung erzwungen und die Art des Scrollens
		// zusätzlich direkt mitgegeben.
		var vorher = wurzel.style.scrollBehavior;
		wurzel.style.scrollBehavior = 'auto';
		void wurzel.offsetHeight;

		try {
			window.scrollTo( { top: scrollstand, left: 0, behavior: 'instant' } );
		} catch ( fehler ) {
			// Ältere Browser kennen "instant" nicht.
			window.scrollTo( 0, scrollstand );
		}

		wurzel.style.scrollBehavior = vorher;
	}

	/**
	 * Der Rest der Seite wird stillgelegt, solange das Menü darüber liegt –
	 * sonst wandern Tastatur und Vorlesehilfe hinter das Menü.
	 */
	function stillegen( an ) {
		[ '.site-main', '.site-footer' ].forEach( function ( auswahl ) {
			var teil = document.querySelector( auswahl );

			if ( ! teil ) {
				return;
			}

			if ( an ) {
				teil.setAttribute( 'inert', '' );
			} else {
				teil.removeAttribute( 'inert' );
			}
		} );
	}

	function oeffnen() {
		if ( offen ) {
			return;
		}

		offen = true;
		festsetzen();
		menue.classList.add( 'is-open' );
		kopf.classList.add( 'hat-menue' );
		schalter.setAttribute( 'aria-expanded', 'true' );
		schalter.setAttribute( 'aria-label', TEXTE.schliessen );

		if ( aufschrift ) {
			aufschrift.textContent = TEXTE.knopfZu;
		}

		stillegen( true );

		var erster = menue.querySelector( 'a[href]' );

		if ( erster ) {
			erster.focus( { preventScroll: true } );
		}
	}

	/**
	 * @param {boolean} fokusZurueck false, wenn gerade ein Link angeklickt
	 *                               wurde – dann gehört der Fokus dorthin.
	 */
	function schliessen( fokusZurueck ) {
		if ( ! offen ) {
			return;
		}

		offen = false;
		menue.classList.remove( 'is-open' );
		kopf.classList.remove( 'hat-menue' );
		schalter.setAttribute( 'aria-expanded', 'false' );
		schalter.setAttribute( 'aria-label', TEXTE.oeffnen );

		if ( aufschrift ) {
			aufschrift.textContent = TEXTE.knopfAuf;
		}

		stillegen( false );
		freigeben();

		if ( false !== fokusZurueck ) {
			schalter.focus( { preventScroll: true } );
		}
	}

	schalter.setAttribute( 'aria-label', TEXTE.oeffnen );

	schalter.addEventListener( 'click', function () {
		if ( offen ) {
			schliessen();
		} else {
			oeffnen();
		}
	} );

	menue.addEventListener( 'click', function ( ereignis ) {
		// Ein Eintrag: schließen und den Sprung machen lassen.
		if ( ereignis.target.closest( 'a[href]' ) ) {
			schliessen( false );

			return;
		}

		// Daneben getippt: das Menü ist überall sonst eine Schließfläche.
		if ( ! ereignis.target.closest( 'li' ) ) {
			schliessen();
		}
	} );

	document.addEventListener( 'keydown', function ( ereignis ) {
		if ( ! offen ) {
			return;
		}

		if ( 'Escape' === ereignis.key ) {
			schliessen();

			return;
		}

		if ( 'Tab' !== ereignis.key ) {
			return;
		}

		// Der Fokus läuft im Menü im Kreis, statt dahinter zu verschwinden.
		var liste = anspringbar();

		if ( liste.length < 2 ) {
			return;
		}

		var erster = liste[ 0 ];
		var letzter = liste[ liste.length - 1 ];

		if ( ereignis.shiftKey && document.activeElement === erster ) {
			ereignis.preventDefault();
			letzter.focus();
		} else if ( ! ereignis.shiftKey && document.activeElement === letzter ) {
			ereignis.preventDefault();
			erster.focus();
		}
	} );

	// Wird das Fenster breit, gibt es kein Vollbildmenü mehr.
	window.addEventListener( 'resize', function () {
		if ( offen && window.innerWidth > 940 ) {
			schliessen( false );
		}
	} );
}() );
