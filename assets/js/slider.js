/**
 * Slider de largura total com transição em fade.
 * Autoplay (pausa ao passar o mouse, com foco ou com a aba oculta), setas, indicadores,
 * teclado (setas esquerda/direita) e deslizar no celular.
 */
( function () {
	'use strict';

	function init( root ) {
		if ( root.dataset.hmSliderReady ) {
			return;
		}
		root.dataset.hmSliderReady = '1';

		var slides = root.querySelectorAll( '.hm-slide' );
		var dots = root.querySelectorAll( '[data-hm-slider-dot]' );
		var total = slides.length;
		if ( total < 2 ) {
			return;
		}

		var current = 0;
		var timer = null;
		var autoplay = root.getAttribute( 'data-autoplay' ) === '1' && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		var interval = parseInt( root.getAttribute( 'data-interval' ), 10 ) || 6000;

		function go( index ) {
			index = ( index + total ) % total;
			if ( index === current ) {
				return;
			}
			slides[ current ].classList.remove( 'is-active' );
			slides[ current ].setAttribute( 'aria-hidden', 'true' );
			slides[ index ].classList.add( 'is-active' );
			slides[ index ].removeAttribute( 'aria-hidden' );

			// Carrega a imagem do próximo slide antes de precisar dela.
			var nextImg = slides[ ( index + 1 ) % total ].querySelector( 'img[loading="lazy"]' );
			if ( nextImg ) {
				nextImg.loading = 'eager';
			}

			for ( var i = 0; i < dots.length; i++ ) {
				dots[ i ].classList.toggle( 'is-active', i === index );
				if ( i === index ) {
					dots[ i ].setAttribute( 'aria-current', 'true' );
				} else {
					dots[ i ].removeAttribute( 'aria-current' );
				}
			}
			current = index;
		}

		function stop() {
			if ( timer ) {
				clearInterval( timer );
				timer = null;
			}
		}
		function start() {
			stop();
			if ( autoplay ) {
				timer = setInterval( function () {
					go( current + 1 );
				}, interval );
			}
		}

		var prev = root.querySelector( '[data-hm-slider-prev]' );
		var next = root.querySelector( '[data-hm-slider-next]' );
		if ( prev ) {
			prev.addEventListener( 'click', function () { go( current - 1 ); start(); } );
		}
		if ( next ) {
			next.addEventListener( 'click', function () { go( current + 1 ); start(); } );
		}
		for ( var d = 0; d < dots.length; d++ ) {
			dots[ d ].addEventListener( 'click', function () {
				go( parseInt( this.getAttribute( 'data-hm-slider-dot' ), 10 ) );
				start();
			} );
		}

		root.addEventListener( 'mouseenter', stop );
		root.addEventListener( 'mouseleave', start );
		root.addEventListener( 'focusin', stop );
		root.addEventListener( 'focusout', start );
		root.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowLeft' ) { go( current - 1 ); }
			if ( e.key === 'ArrowRight' ) { go( current + 1 ); }
		} );

		var startX = null;
		root.addEventListener( 'touchstart', function ( e ) {
			startX = e.touches[ 0 ].clientX;
			stop();
		}, { passive: true } );
		root.addEventListener( 'touchend', function ( e ) {
			if ( startX === null ) {
				return;
			}
			var dx = e.changedTouches[ 0 ].clientX - startX;
			if ( Math.abs( dx ) > 40 ) {
				go( dx < 0 ? current + 1 : current - 1 );
			}
			startX = null;
			start();
		}, { passive: true } );

		document.addEventListener( 'visibilitychange', function () {
			if ( document.hidden ) { stop(); } else { start(); }
		} );

		start();
	}

	function initAll() {
		document.querySelectorAll( '[data-hm-slider]' ).forEach( init );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAll );
	} else {
		initAll();
	}

	// Editor do Elementor: inicializa ao (re)renderizar o widget.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/imob_sec_slider.default', function ( $scope ) {
				var el = $scope[ 0 ] ? $scope[ 0 ].querySelector( '[data-hm-slider]' ) : null;
				if ( el ) {
					init( el );
				}
			} );
		}
	} );
}() );
