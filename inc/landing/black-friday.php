<?php
/**
 * Landing page da Black Friday (modelo "Landing Page - Black Friday").
 *
 * Opções em Aparência > Personalizar > Black Friday.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valores padrão da campanha.
 *
 * @return array
 */
function imob_bf_defaults() {
	return array(
		'start'          => '2026-11-16T00:00',
		'end'            => '2026-11-30T23:59',
		'extra_discount' => 'R$ 1.000',
		'wa_message'     => 'Olá! Vi a Black Friday de imóveis e quero aproveitar as ofertas. Pode me ajudar?',
	);
}

/**
 * Lê uma opção da campanha.
 *
 * @param string $key Chave.
 * @return string
 */
function imob_bf_mod( $key ) {
	$defaults = imob_bf_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( 'imob_bf_' . $key, $default );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Timestamp (UTC) de uma data da campanha no fuso do site.
 *
 * @param string $key start|end.
 * @return int
 */
function imob_bf_timestamp( $key ) {
	try {
		$date = new DateTimeImmutable( imob_bf_mod( $key ), wp_timezone() );
		return $date->getTimestamp();
	} catch ( Exception $e ) {
		return 0;
	}
}

/**
 * URL de WhatsApp com a mensagem da campanha.
 *
 * @param string $extra Texto extra (ex.: nome do imóvel).
 * @return string
 */
function imob_bf_whatsapp_url( $extra = '' ) {
	$num = get_option( 'imob_contact_whatsapp' ) ? get_option( 'imob_contact_whatsapp' ) : get_option( 'imob_contact_phone' );
	$num = preg_replace( '/\D/', '', (string) $num );
	if ( strlen( $num ) === 10 || strlen( $num ) === 11 ) {
		$num = '55' . $num;
	}
	$msg = trim( imob_bf_mod( 'wa_message' ) . ' ' . $extra );
	$url = 'https://api.whatsapp.com/send?';
	if ( $num ) {
		$url .= 'phone=' . $num . '&';
	}
	return $url . 'text=' . rawurlencode( $msg );
}

/**
 * A página atual usa o modelo da Black Friday?
 *
 * @return bool
 */
function imob_is_black_friday_page() {
	return is_page_template( 'template-black-friday.php' );
}

/**
 * Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Gerenciador do Customizer.
 */
function imob_bf_customize_register( $wp_customize ) {
	$defaults = imob_bf_defaults();

	$wp_customize->add_section(
		'imob_bf',
		array(
			'title'       => __( 'Black Friday', 'imobiliaria-tema' ),
			'description' => __( 'Configurações da landing page da Black Friday (páginas com o modelo "Landing Page - Black Friday").', 'imobiliaria-tema' ),
			'priority'    => 31,
		)
	);

	$fields = array(
		'start'          => array( __( 'Início da campanha (AAAA-MM-DDTHH:MM)', 'imobiliaria-tema' ), 'text' ),
		'end'            => array( __( 'Fim da campanha (AAAA-MM-DDTHH:MM)', 'imobiliaria-tema' ), 'text' ),
		'extra_discount' => array( __( 'Desconto extra mínimo (ex.: R$ 1.000)', 'imobiliaria-tema' ), 'text' ),
		'wa_message'     => array( __( 'Mensagem padrão do WhatsApp', 'imobiliaria-tema' ), 'textarea' ),
	);

	foreach ( $fields as $key => $cfg ) {
		$wp_customize->add_setting(
			'imob_bf_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'textarea' === $cfg[1] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'imob_bf_' . $key,
			array(
				'label'   => $cfg[0],
				'section' => 'imob_bf',
				'type'    => $cfg[1],
			)
		);
	}
}
add_action( 'customize_register', 'imob_bf_customize_register' );

/**
 * CSS/JS da landing (reaproveita os componentes da home).
 */
function imob_bf_assets() {
	if ( ! imob_is_black_friday_page() ) {
		return;
	}
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'imob-home', $uri . '/assets/css/home.css', array( 'imob-theme-style' ), (string) filemtime( $dir . '/assets/css/home.css' ) );
	wp_enqueue_style( 'imob-black-friday', $uri . '/assets/css/black-friday.css', array( 'imob-home' ), (string) filemtime( $dir . '/assets/css/black-friday.css' ) );
	wp_enqueue_script( 'imob-home', $uri . '/assets/js/home.js', array(), (string) filemtime( $dir . '/assets/js/home.js' ), true );
	wp_enqueue_script( 'imob-black-friday', $uri . '/assets/js/black-friday.js', array(), (string) filemtime( $dir . '/assets/js/black-friday.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'imob_bf_assets', 20 );

/**
 * Classe no body para estilos da campanha.
 *
 * @param array $classes Classes.
 * @return array
 */
function imob_bf_body_class( $classes ) {
	if ( imob_is_black_friday_page() ) {
		$classes[] = 'imob-black-friday-page';
	}
	return $classes;
}
add_filter( 'body_class', 'imob_bf_body_class' );

/**
 * Schema.org da oferta (Event de vendas) na landing.
 */
function imob_bf_schema() {
	if ( ! imob_is_black_friday_page() ) {
		return;
	}
	$start = imob_bf_timestamp( 'start' );
	$end   = imob_bf_timestamp( 'end' );
	if ( ! $start || ! $end ) {
		return;
	}
	$schema = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'SaleEvent',
		'name'                => get_the_title(),
		'description'         => sprintf( 'Black Friday de imóveis em Campina Grande - PB: todos os imóveis em oferta, com pelo menos %s de desconto extra em qualquer imóvel à venda, documentação grátis em imóveis selecionados e 1 ano de condomínio grátis nas casas em condomínio.', imob_bf_mod( 'extra_discount' ) ),
		'url'                 => get_permalink(),
		'startDate'           => wp_date( 'c', $start ),
		'endDate'             => wp_date( 'c', $end ),
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/MixedEventAttendanceMode',
		'location'            => array(
			'@type'   => 'Place',
			'name'    => get_bloginfo( 'name' ),
			'address' => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => 'Campina Grande',
				'addressRegion'   => 'PB',
				'addressCountry'  => 'BR',
			),
		),
		'organizer'           => array(
			'@type' => 'RealEstateAgent',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'imob_bf_schema', 12 );
