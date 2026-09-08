<?php
/**
 * Enqueue scripts and styles.
 *
 * @package ImobiliariaTema
 */

function imob_theme_scripts() {
	// Google Fonts: Inter
	wp_enqueue_style( 'imob-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );

	// Material Symbols Outlined
	wp_enqueue_style( 'imob-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200', array(), null );

	// Enqueue main stylesheet.
	wp_enqueue_style( 'imob-theme-style', get_stylesheet_uri(), array(), IMOB_THEME_VERSION );

	// GLightbox (for gallery slide show)
	wp_enqueue_style( 'glightbox-css', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', array(), null );
	wp_enqueue_script( 'glightbox-js', 'https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js', array(), null, true );

	// Elementor frontend compatibility if needed.
	// Google Maps API
	$gmaps_key = get_option( 'imob_gmaps_key' );
	if ( $gmaps_key && is_singular( 'imovel' ) ) {
		wp_enqueue_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $gmaps_key ), array(), null, true );
	}
}
add_action( 'wp_enqueue_scripts', 'imob_theme_scripts' );

/**
 * Enqueue admin scripts and styles.
 */
function imob_theme_admin_scripts( $hook ) {
	// Need media uploader for taxonomies and meta boxes
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'imob_theme_admin_scripts' );
