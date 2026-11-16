/**
 * Google Maps sob demanda: a API só é baixada quando o mapa chega perto da tela.
 * Evita que o Maps ocupe o processador do celular enquanto a pessoa navega (INP).
 *
 * Uso: <div data-imob-map='{"lat":-7.2,"lng":-35.8,"zoom":16,"type":"roadmap","title":"..."}'></div>
 * Outros scripts podem chamar window.imobLoadGoogleMaps( callback ).
 */
( function () {
	'use strict';

	var cfg = window.imobMapsCfg || {};
	var queue = [];
	var state = 'idle'; // idle | loading | ready

	function flush() {
		state = 'ready';
		while ( queue.length ) {
			try {
				queue.shift()();
			} catch ( e ) {}
		}
	}

	window.imobLoadGoogleMaps = function ( cb ) {
		if ( window.google && window.google.maps && window.google.maps.Map ) {
			cb();
			return;
		}
		queue.push( cb );
		if ( 'idle' !== state || ! cfg.key ) {
			return;
		}
		state = 'loading';
		window.imobMapsReady = flush;
		var s = document.createElement( 'script' );
		s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent( cfg.key ) + '&loading=async&callback=imobMapsReady';
		s.async = true;
		document.head.appendChild( s );
	};

	function render( el ) {
		var opts;
		try {
			opts = JSON.parse( el.getAttribute( 'data-imob-map' ) );
		} catch ( e ) {
			return;
		}
		window.imobLoadGoogleMaps( function () {
			var loc = { lat: parseFloat( opts.lat ), lng: parseFloat( opts.lng ) };
			var map = new google.maps.Map( el, {
				zoom: opts.zoom || 16,
				center: loc,
				mapTypeId: opts.type || 'roadmap'
			} );
			new google.maps.Marker( { position: loc, map: map, title: opts.title || '' } );
		} );
	}

	// Mapas declarados por data-attribute.
	function watch( el, fn ) {
		if ( ! ( 'IntersectionObserver' in window ) ) {
			fn( el );
			return;
		}
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					io.unobserve( entry.target );
					fn( entry.target );
				}
			} );
		}, { rootMargin: '300px 0px' } );
		io.observe( el );
	}

	window.imobWatchMap = watch;

	document.querySelectorAll( '[data-imob-map]' ).forEach( function ( el ) {
		watch( el, render );
	} );
} )();
