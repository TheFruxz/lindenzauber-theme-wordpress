<?php
/**
 * Bausteine für die Vorlagen: Nachthimmel, Ornament, Zeichen.
 *
 * @package Lindenzauber
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Der Nachthimmel mit Sternen, Laternenschein und Lindenbaum.
 *
 * Liegt komplett im Theme, damit im Editor nichts verrutschen kann.
 */
function lz_atmosphaere() {
	?>
	<div class="lz-atmosphere" aria-hidden="true">
		<div class="lz-atmosphere__sky"></div>
		<div class="lz-atmosphere__stars--far"></div>
		<div class="lz-atmosphere__stars"></div>
		<div class="lz-atmosphere__glow"></div>
		<div class="lz-atmosphere__vignette"></div>
	</div>
	<?php
}

/**
 * Der Lindenbaum mit der Laterne am linken Rand.
 *
 * Steht bewusst innerhalb von .site: nur dort wird er seitlich abgeschnitten,
 * sodass sich die Seite auf dem Handy nicht seitlich verschieben lässt.
 */
function lz_szene() {
	?>
	<div class="lz-scene" aria-hidden="true">
		<img class="lz-scene__tree" src="<?php echo esc_url( LZ_URI . '/assets/img/linde.svg' ); ?>" alt="" width="900" height="1180" loading="eager" fetchpriority="low">
	</div>
	<?php
}

/**
 * Das Ornament unter einer Überschrift, wie auf dem Plakat.
 *
 * @param string $klasse Zusätzliche CSS-Klasse.
 */
function lz_ornament( $klasse = 'entry-header__ornament' ) {
	printf(
		'<svg class="%s" viewBox="0 0 320 34" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
		. '<path d="M8 17h96M216 17h96" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>'
		. '<path d="M104 17c14 0 18-9 28-9s14 9 28 9" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>'
		. '<path d="M160 17c14 0 18 9 28 9s14-9 28-9" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/>'
		. '<circle cx="104" cy="17" r="1.8" fill="currentColor"/><circle cx="216" cy="17" r="1.8" fill="currentColor"/>'
		. '<path d="M160 1c1.9 9.7 4.4 12.2 14 16-9.6 1.9-12.1 4.4-14 16-1.9-9.7-4.4-12.2-14-16 9.6-1.9 12.1-4.4 14-16Z" fill="currentColor"/>'
		. '</svg>',
		esc_attr( $klasse )
	);
}

/**
 * Ein Lindenblatt als kleines Zeichen.
 *
 * @param string $klasse CSS-Klasse.
 * @return string
 */
function lz_blatt( $klasse = 'site-footer__leaf' ) {
	return sprintf(
		'<svg class="%s" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">'
		. '<path d="M24 45V21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>'
		. '<path d="M24 21S9 20 5 9c11-4 19 3 19 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>'
		. '<path d="M24 21c0-9 8-16 19-12-4 11-19 12-19 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>'
		. '<path d="M24 33c-4-1-7-4-8-8m8 8c4-1 7-4 8-8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>'
		. '</svg>',
		esc_attr( $klasse )
	);
}
