/**
 * Página inicial: paginação das grades de imóveis e setas dos carrosséis.
 */
( function () {
	'use strict';

	// Paginação no navegador: mostra N cards por página.
	document.querySelectorAll( '[data-hm-paginate]' ).forEach( function ( grid ) {
		var perPage = parseInt( grid.getAttribute( 'data-hm-paginate' ), 10 ) || 6;
		var cards = Array.prototype.slice.call( grid.children );
		var pager = document.querySelector( '[data-hm-pager-for="' + grid.id + '"] .hm-pager-pages' );
		var pages = Math.ceil( cards.length / perPage );

		function show( page ) {
			cards.forEach( function ( card, i ) {
				card.hidden = Math.floor( i / perPage ) !== page;
			} );
			if ( pager ) {
				pager.querySelectorAll( 'button' ).forEach( function ( btn, i ) {
					if ( i === page ) {
						btn.setAttribute( 'aria-current', 'page' );
					} else {
						btn.removeAttribute( 'aria-current' );
					}
				} );
			}
		}

		if ( pager && pages > 1 ) {
			for ( var p = 0; p < pages; p++ ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'hm-pager-btn';
				btn.textContent = String( p + 1 );
				btn.setAttribute( 'aria-label', 'Página ' + ( p + 1 ) );
				btn.addEventListener( 'click', ( function ( page ) {
					return function () {
						show( page );
						var top = grid.getBoundingClientRect().top + window.scrollY - 120;
						if ( window.scrollY > top ) {
							window.scrollTo( { top: top, behavior: 'smooth' } );
						}
					};
				} )( p ) );
				pager.appendChild( btn );
			}
		}

		show( 0 );
	} );

	// Setas de rolagem dos carrosséis.
	document.querySelectorAll( '[data-hm-scroll]' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var track = document.getElementById( btn.getAttribute( 'data-hm-scroll' ) );
			if ( ! track ) {
				return;
			}
			var dir = parseInt( btn.getAttribute( 'data-dir' ), 10 ) || 1;
			track.scrollBy( { left: dir * track.clientWidth * 0.8, behavior: 'smooth' } );
		} );
	} );
} )();
