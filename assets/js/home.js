/**
 * Página inicial: paginação das grades de imóveis e setas dos carrosséis.
 */
( function () {
	'use strict';

	function init( root ) {
	root = root || document;

	// Paginação no navegador: mostra N cards por página.
	root.querySelectorAll( '[data-hm-paginate]:not([data-hm-ready])' ).forEach( function ( grid ) {
		grid.setAttribute( 'data-hm-ready', '' );
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
	root.querySelectorAll( '[data-hm-scroll]:not([data-hm-ready])' ).forEach( function ( btn ) {
		btn.setAttribute( 'data-hm-ready', '' );
		btn.addEventListener( 'click', function () {
			var track = document.getElementById( btn.getAttribute( 'data-hm-scroll' ) );
			if ( ! track ) {
				return;
			}
			var dir = parseInt( btn.getAttribute( 'data-dir' ), 10 ) || 1;
			track.scrollBy( { left: dir * track.clientWidth * 0.8, behavior: 'smooth' } );
		} );
	} );
	}

	init( document );

	// Editor do Elementor: reaplica quando um widget é redesenhado.
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function ( $scope ) {
					init( $scope[ 0 ] );
				} );
			}
		} );
	}
} )();
