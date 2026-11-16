<?php
/**
 * Enqueue scripts and styles.
 *
 * @package ImobiliariaTema
 */

function imob_theme_scripts() {
	// Google Fonts: Inter
	wp_enqueue_style( 'imob-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400&display=swap', array(), null );

	// Material Symbols Outlined: só os eixos usados no tema (opsz 24, peso 400,
	// FILL 0/1, GRAD 0). A fonte variável completa tem vários MB e pesava no celular.
	wp_enqueue_style( 'imob-material-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0..1,0&display=block', array(), null );

	// Enqueue main stylesheet.
	wp_enqueue_style( 'imob-theme-style', get_stylesheet_uri(), array(), (string) filemtime( get_stylesheet_directory() . '/style.css' ) );

	// Cabeçalho (menu com submenu e versão móvel).
	wp_enqueue_style( 'imob-header', get_template_directory_uri() . '/assets/css/header.css', array( 'imob-theme-style' ), (string) filemtime( get_template_directory() . '/assets/css/header.css' ) );
	wp_enqueue_script( 'imob-header', get_template_directory_uri() . '/assets/js/header.js', array(), (string) filemtime( get_template_directory() . '/assets/js/header.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );

	// GLightbox: só nas páginas com galeria, com defer (não ocupa o carregamento).
	if ( is_singular( array( 'imovel', 'empreendimento' ) ) ) {
		wp_enqueue_style( 'glightbox-css', 'https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css', array(), null );
		wp_enqueue_script( 'glightbox-js', 'https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js', array(), null, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}

	// Google Maps sob demanda (a API só baixa quando o mapa se aproxima da tela).
	imob_register_maps_lazy();
	if ( is_singular( array( 'imovel', 'empreendimento' ) ) ) {
		wp_enqueue_script( 'imob-maps-lazy' );
	}
}
add_action( 'wp_enqueue_scripts', 'imob_theme_scripts' );

/**
 * Registra o carregador do Google Maps sob demanda (usado também pelo widget de mapa).
 */
function imob_register_maps_lazy() {
	if ( wp_script_is( 'imob-maps-lazy', 'registered' ) ) {
		return;
	}
	$file = get_template_directory() . '/assets/js/maps-lazy.js';
	wp_register_script( 'imob-maps-lazy', get_template_directory_uri() . '/assets/js/maps-lazy.js', array(), (string) filemtime( $file ), true );
	wp_add_inline_script( 'imob-maps-lazy', 'window.imobMapsCfg = ' . wp_json_encode( array( 'key' => (string) get_option( 'imob_gmaps_key' ) ) ) . ';', 'before' );
}

/**
 * Enqueue admin scripts and styles.
 */
function imob_theme_admin_scripts( $hook ) {
	// Need media uploader for taxonomies and meta boxes
	wp_enqueue_media();

	global $post_type;
	$gmaps_key = get_option( 'imob_gmaps_key' );
	if ( $gmaps_key && ( $post_type === 'imovel' || ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'imovel' ) ) ) {
		wp_enqueue_script( 'google-maps-admin', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $gmaps_key ) . '&libraries=places', array(), null, true );
	}
}
add_action( 'admin_enqueue_scripts', 'imob_theme_admin_scripts' );
