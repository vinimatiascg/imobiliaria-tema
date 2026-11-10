/**
 * Contagem regressiva do hero de oferta (landing Black Friday e home).
 */
( function () {
	'use strict';

	var box = document.querySelector( '[data-bf-countdown]' );
	if ( ! box ) {
		return;
	}

	var end = parseInt( box.getAttribute( 'data-bf-countdown' ), 10 ) * 1000;
	var units = {
		d: box.querySelector( '[data-bf-unit="d"]' ),
		h: box.querySelector( '[data-bf-unit="h"]' ),
		m: box.querySelector( '[data-bf-unit="m"]' ),
		s: box.querySelector( '[data-bf-unit="s"]' )
	};

	function pad( n ) {
		return n < 10 ? '0' + n : String( n );
	}

	function tick() {
		var diff = Math.max( 0, end - Date.now() );
		var sec = Math.floor( diff / 1000 );
		units.d.textContent = pad( Math.floor( sec / 86400 ) );
		units.h.textContent = pad( Math.floor( ( sec % 86400 ) / 3600 ) );
		units.m.textContent = pad( Math.floor( ( sec % 3600 ) / 60 ) );
		units.s.textContent = pad( sec % 60 );
		if ( diff <= 0 ) {
			clearInterval( timer );
		}
	}

	var timer = setInterval( tick, 1000 );
	tick();
}() );
