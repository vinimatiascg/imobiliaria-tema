/**
 * Menu do cabeçalho: abre/fecha no celular e expande submenus.
 */
( function () {
	'use strict';

	var header = document.getElementById( 'masthead' );
	if ( ! header ) {
		return;
	}
	var toggle = header.querySelector( '.menu-toggle' );
	var mobile = window.matchMedia( '(max-width: 1100px)' );

	if ( toggle ) {
		toggle.addEventListener( 'click', function () {
			var open = header.classList.toggle( 'is-menu-open' );
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			var icon = toggle.querySelector( '.material-symbols-outlined' );
			if ( icon ) {
				icon.textContent = open ? 'close' : 'menu';
			}
		} );
	}

	// No celular, o primeiro toque em "Imóveis" abre o submenu em vez de navegar.
	header.querySelectorAll( '.menu-item-has-children > a' ).forEach( function ( link ) {
		link.setAttribute( 'aria-haspopup', 'true' );
		link.addEventListener( 'click', function ( e ) {
			if ( ! mobile.matches ) {
				return;
			}
			var li = link.parentElement;
			if ( ! li.classList.contains( 'is-open' ) ) {
				e.preventDefault();
				li.classList.add( 'is-open' );
			}
		} );
	} );
} )();
