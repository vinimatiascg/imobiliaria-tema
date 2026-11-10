<?php
/**
 * Opções editáveis da Página Inicial (Personalizar > Página Inicial).
 *
 * Todos os textos da home vêm daqui, com os textos do layout do Figma como padrão.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valores padrão das opções da home.
 *
 * @return array
 */
function imob_home_defaults() {
	return array(
		// Hero.
		'hero_eyebrow'        => 'O endereço dos seus planos.',
		'hero_title'          => 'O seu imóvel está aqui!',
		'hero_text'           => 'Encontre os melhores imóveis de Campina Grande e região para morar ou investir. Apartamentos, casas, terrenos, imóveis comerciais e muito mais, em opções para diferentes perfis e necessidades.',
		'hero_image'          => 0,
		'construtoras_title'  => 'Trabalhamos com as principais construtoras do mercado:',

		// Processo.
		'process_title'       => 'Cuidamos de todo o processo para você',
		'process_text'        => 'Acompanhamos você em todas as etapas da compra do imóvel, desde a simulação e escolha da melhor opção até a vistoria e o recebimento das chaves.',
		'step_1_label'        => 'Simulação',
		'step_1_title'        => 'Aprovação do financiamento',
		'step_1_text'         => 'Simule as opções de financiamento e descubra as condições que melhor se encaixam no seu perfil.',
		'step_2_label'        => 'Avaliação',
		'step_2_title'        => 'Análise do seu perfil',
		'step_2_text'         => 'Avaliamos sua capacidade financeira, entrada e condições de financiamento para encontrar a melhor opção para você.',
		'step_3_label'        => 'Análise de crédito',
		'step_3_title'        => 'Aprovação do financiamento',
		'step_3_text'         => 'O banco analisa sua renda, histórico de crédito e documentação para verificar a aprovação do financiamento.',
		'step_4_label'        => 'Escolha do imóvel',
		'step_4_title'        => 'Encontre o imóvel ideal',
		'step_4_text'         => 'Visite os imóveis que mais combinam com seu perfil e escolha aquele que atende às suas necessidades.',
		'step_5_label'        => 'Assinatura',
		'step_5_title'        => 'Hora de oficializar a compra',
		'step_5_text'         => 'Com tudo aprovado, acompanhamos a assinatura do contrato e a formalização da compra do seu imóvel.',

		// Últimos imóveis.
		'latest_title'        => 'Últimos imóveis cadastrados',
		'latest_text'         => 'Confira os imóveis mais recentes disponíveis em Campina Grande e região. Atualizamos constantemente nossas opções para você encontrar novas oportunidades para morar ou investir.',

		// Números.
		'numbers_title'       => 'Números da nossa empresa',
		'numbers_text'        => 'Nossa experiência e resultados refletem o compromisso com nossos clientes. Conheça alguns dos nossos números e entenda por que somos referência em imóveis em Campina Grande e região.',
		'number_1_icon'       => 'home',
		'number_1_value'      => '100+',
		'number_1_label'      => 'Imóveis vendidos',
		'number_2_icon'       => 'calendar_month',
		'number_2_value'      => '3 meses',
		'number_2_label'      => 'Tempo médio de vendas',
		'number_3_icon'       => 'upload',
		'number_3_value'      => '200+',
		'number_3_label'      => 'Imóveis publicados',
		'number_4_icon'       => 'person',
		'number_4_value'      => '3000+',
		'number_4_label'      => 'Seguidores no Instagram e muitos sonhos à frente',
		'number_5_icon'       => 'shopping_basket',
		'number_5_value'      => '5',
		'number_5_label'      => 'Cestas básicas doadas em cada venda',
		'number_6_icon'       => 'calendar_month',
		'number_6_value'      => '7 anos',
		'number_6_label'      => 'no mercado de Campina e região',

		// Lançamentos.
		'launch_title'        => 'Imóveis com condições especiais de lançamento',
		'launch_text'         => 'Encontre oportunidades de imóveis na planta com condições especiais de lançamento.',
		'launch_tipo'         => 'lancamento',

		// Formulário.
		'form_eyebrow'        => 'Conte-nos o que você procura.',
		'form_title'          => 'Procurando por um imóvel?',
		'form_text'           => 'Preencha o formulário e nossa equipe entrará em contato para apresentar imóveis que combinam com o seu perfil e seus objetivos.',

		// Casas em condomínio.
		'condo_title'         => 'Casas em condomínio',
		'condo_text'          => 'Encontre casas em condomínios fechados nos principais empreendimentos de Campina Grande. Confira opções de diferentes padrões, localizações e condições para encontrar o imóvel ideal para você.',
		'condo_tipo'          => 'casa-em-condominio',

		// Blog.
		'blog_title'          => 'Últimas do nosso blog',
		'blog_text'           => 'Acompanhe as novidades do mercado imobiliário, dicas para comprar ou investir em imóveis e informações que ajudam você a tomar as melhores decisões.',

		// Depoimentos.
		'testimonials_title'  => 'A confiança de quem já escolheu a gente',
		'testimonials_text'   => 'Mais do que um imóvel, uma experiência segura, tranquila e especial.',
	);
}

/**
 * Lê uma opção da home com fallback para o padrão.
 *
 * @param string $key Chave da opção.
 * @return mixed
 */
function imob_home_mod( $key ) {
	$defaults = imob_home_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	$value    = get_theme_mod( 'imob_home_' . $key, $default );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/**
 * Registra o painel "Página Inicial" no Personalizar.
 *
 * @param WP_Customize_Manager $wp_customize Gerenciador do Customizer.
 */
function imob_home_customize_register( $wp_customize ) {
	$defaults = imob_home_defaults();

	$wp_customize->add_panel(
		'imob_home_panel',
		array(
			'title'       => __( 'Página Inicial', 'imobiliaria-tema' ),
			'description' => __( 'Textos, imagens e números das seções da home.', 'imobiliaria-tema' ),
			'priority'    => 30,
		)
	);

	$sections = array(
		'hero'         => array( __( 'Topo (Hero)', 'imobiliaria-tema' ), array( 'hero_eyebrow', 'hero_title', 'hero_text', 'hero_image', 'construtoras_title' ) ),
		'process'      => array( __( 'Processo de compra', 'imobiliaria-tema' ), array( 'process_title', 'process_text' ) ),
		'latest'       => array( __( 'Últimos imóveis', 'imobiliaria-tema' ), array( 'latest_title', 'latest_text' ) ),
		'numbers'      => array( __( 'Números da empresa', 'imobiliaria-tema' ), array( 'numbers_title', 'numbers_text' ) ),
		'launch'       => array( __( 'Lançamentos', 'imobiliaria-tema' ), array( 'launch_title', 'launch_text', 'launch_tipo' ) ),
		'form'         => array( __( 'Formulário de interesse', 'imobiliaria-tema' ), array( 'form_eyebrow', 'form_title', 'form_text' ) ),
		'condo'        => array( __( 'Casas em condomínio', 'imobiliaria-tema' ), array( 'condo_title', 'condo_text', 'condo_tipo' ) ),
		'blog'         => array( __( 'Blog', 'imobiliaria-tema' ), array( 'blog_title', 'blog_text' ) ),
		'testimonials' => array( __( 'Depoimentos', 'imobiliaria-tema' ), array( 'testimonials_title', 'testimonials_text' ) ),
	);

	// Etapas do processo e números entram nas respectivas seções.
	for ( $i = 1; $i <= 5; $i++ ) {
		array_push( $sections['process'][1], "step_{$i}_label", "step_{$i}_title", "step_{$i}_text", "step_{$i}_image" );
	}
	for ( $i = 1; $i <= 6; $i++ ) {
		array_push( $sections['numbers'][1], "number_{$i}_icon", "number_{$i}_value", "number_{$i}_label" );
	}

	$labels = array(
		'launch_tipo' => __( 'Slug do tipo de imóvel exibido (ex.: lancamento)', 'imobiliaria-tema' ),
		'condo_tipo'  => __( 'Slug do tipo de imóvel exibido (ex.: casa-em-condominio)', 'imobiliaria-tema' ),
	);

	foreach ( $sections as $section_id => $section ) {
		$wp_customize->add_section(
			'imob_home_' . $section_id,
			array(
				'title' => $section[0],
				'panel' => 'imob_home_panel',
			)
		);

		foreach ( $section[1] as $key ) {
			$is_image = ( 'hero_image' === $key || preg_match( '/^step_\d_image$/', $key ) );
			$is_long  = ( preg_match( '/_text$/', $key ) );

			$wp_customize->add_setting(
				'imob_home_' . $key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $is_image ? 'absint' : ( $is_long ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
				)
			);

			if ( $is_image ) {
				$wp_customize->add_control(
					new WP_Customize_Media_Control(
						$wp_customize,
						'imob_home_' . $key,
						array(
							'label'     => imob_home_label( $key ),
							'section'   => 'imob_home_' . $section_id,
							'mime_type' => 'image',
						)
					)
				);
			} else {
				$wp_customize->add_control(
					'imob_home_' . $key,
					array(
						'label'       => isset( $labels[ $key ] ) ? $labels[ $key ] : imob_home_label( $key ),
						'description' => preg_match( '/^number_\d_icon$/', $key ) ? __( 'Nome do ícone do Google Material Symbols (ex.: home, person, calendar_month).', 'imobiliaria-tema' ) : '',
						'section'     => 'imob_home_' . $section_id,
						'type'        => $is_long ? 'textarea' : 'text',
					)
				);
			}
		}
	}
}
add_action( 'customize_register', 'imob_home_customize_register' );

/**
 * Rótulo legível para uma chave de opção.
 *
 * @param string $key Chave.
 * @return string
 */
function imob_home_label( $key ) {
	if ( preg_match( '/^step_(\d)_(label|title|text|image)$/', $key, $m ) ) {
		$parts = array(
			'label' => 'Etiqueta',
			'title' => 'Título',
			'text'  => 'Texto',
			'image' => 'Imagem',
		);
		return sprintf( 'Etapa %d: %s', $m[1], $parts[ $m[2] ] );
	}
	if ( preg_match( '/^number_(\d)_(icon|value|label)$/', $key, $m ) ) {
		$parts = array(
			'icon'  => 'Ícone',
			'value' => 'Número',
			'label' => 'Descrição',
		);
		return sprintf( 'Número %d: %s', $m[1], $parts[ $m[2] ] );
	}
	$map = array(
		'eyebrow' => 'Chamada superior',
		'title'   => 'Título',
		'text'    => 'Texto',
		'image'   => 'Imagem',
	);
	$suffix = substr( $key, strrpos( $key, '_' ) + 1 );
	if ( 'construtoras_title' === $key ) {
		return 'Texto das construtoras';
	}
	return isset( $map[ $suffix ] ) ? $map[ $suffix ] : $key;
}

/**
 * URL de WhatsApp geral do site (Opções do Tema).
 *
 * @return string
 */
function imob_home_whatsapp_url() {
	$num = get_option( 'imob_contact_whatsapp' ) ? get_option( 'imob_contact_whatsapp' ) : get_option( 'imob_contact_phone' );
	$msg = get_option( 'imob_whatsapp_default_message', 'Olá! Gostaria de mais informações sobre os imóveis.' );
	$num = preg_replace( '/\D/', '', (string) $num );
	if ( strlen( $num ) === 10 || strlen( $num ) === 11 ) {
		$num = '55' . $num;
	}
	$url = 'https://api.whatsapp.com/send?';
	if ( $num ) {
		$url .= 'phone=' . $num . '&';
	}
	return $url . 'text=' . rawurlencode( $msg );
}

/**
 * Ícone SVG do WhatsApp para botões.
 *
 * @return string
 */
function imob_home_whatsapp_icon() {
	return '<svg class="hm-wa-icon" viewBox="0 0 32 32" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M16 0C7.16 0 0 7.16 0 16c0 2.83.74 5.58 2.14 8L0 32l8.22-2.1A15.9 15.9 0 0 0 16 32c8.84 0 16-7.16 16-16S24.84 0 16 0zm0 29.33c-2.46 0-4.86-.66-6.95-1.9l-.5-.3-5.17 1.32 1.38-4.95-.33-.52A13.3 13.3 0 0 1 2.45 16C2.45 8.53 8.53 2.45 16 2.45S29.55 8.53 29.55 16 23.47 29.33 16 29.33zm7.43-10.15c-.41-.2-2.41-1.19-2.79-1.33-.37-.13-.64-.2-.91.21-.27.4-1.05 1.32-1.29 1.6-.24.27-.48.3-.88.1-.41-.2-1.72-.64-3.28-2.02-1.21-1.08-2.03-2.42-2.27-2.82-.23-.41-.02-.63.18-.83.18-.18.41-.48.61-.72.2-.23.27-.4.41-.67.13-.28.07-.51-.04-.72-.1-.2-.91-2.2-1.25-3.02-.33-.8-.67-.69-.92-.7h-.78c-.27 0-.71.1-1.09.51-.37.41-1.42 1.4-1.42 3.4s1.46 3.93 1.66 4.2c2.04 2.72 4.4 4.24 6.84 5.29 1.46.63 2.6.7 3.57.56 1.09-.16 2.41-.98 2.75-1.93.34-.95.34-1.77.24-1.94-.1-.17-.38-.27-.78-.47z"/></svg>';
}

/**
 * Carrega CSS e JS da home somente na página inicial.
 */
function imob_home_assets() {
	if ( ! is_front_page() ) {
		return;
	}
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'imob-home', $uri . '/assets/css/home.css', array( 'imob-theme-style' ), (string) filemtime( $dir . '/assets/css/home.css' ) );
	wp_enqueue_script( 'imob-home', $uri . '/assets/js/home.js', array(), (string) filemtime( $dir . '/assets/js/home.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'imob_home_assets', 20 );

/**
 * Pré-carrega a imagem do hero (LCP).
 */
function imob_home_preload_hero() {
	if ( ! is_front_page() ) {
		return;
	}
	$id = (int) imob_home_mod( 'hero_image' );
	if ( $id ) {
		$src = wp_get_attachment_image_url( $id, 'full' );
		if ( $src ) {
			echo '<link rel="preload" as="image" href="' . esc_url( $src ) . '" fetchpriority="high">' . "\n";
		}
	}
}
add_action( 'wp_head', 'imob_home_preload_hero', 2 );

/**
 * Faz a home do tema (front-page.php) prevalecer sobre o modelo de página do Elementor
 * definido na página inicial. Pode ser desligado em Personalizar > Página Inicial > Topo.
 *
 * @param string $template Template escolhido.
 * @return string
 */
function imob_home_force_template( $template ) {
	if ( is_front_page() && ! is_home() && get_theme_mod( 'imob_home_use_theme', true ) && ! isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$front = locate_template( 'front-page.php' );
		if ( $front ) {
			return $front;
		}
	}
	return $template;
}
add_filter( 'template_include', 'imob_home_force_template', 100 );

/**
 * Chave para voltar à home antiga do Elementor.
 *
 * @param WP_Customize_Manager $wp_customize Gerenciador do Customizer.
 */
function imob_home_customize_toggle( $wp_customize ) {
	$wp_customize->add_setting(
		'imob_home_use_theme',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'imob_home_use_theme',
		array(
			'label'       => __( 'Usar a home do tema (layout novo)', 'imobiliaria-tema' ),
			'description' => __( 'Desmarque para voltar a exibir a página inicial montada no Elementor.', 'imobiliaria-tema' ),
			'section'     => 'imob_home_hero',
			'type'        => 'checkbox',
			'priority'    => 1,
		)
	);
}
add_action( 'customize_register', 'imob_home_customize_toggle', 20 );

/**
 * Imagens padrão (ilustrações do tema) das etapas do processo.
 *
 * @param int $i Número da etapa (1 a 5).
 * @return string URL da ilustração.
 */
function imob_home_step_default_image( $i ) {
	$files = array(
		1 => 'etapa-1-simulacao.svg',
		2 => 'etapa-2-avaliacao.svg',
		3 => 'etapa-3-analise-credito.svg',
		4 => 'etapa-4-escolha-imovel.svg',
		5 => 'etapa-5-assinatura.svg',
	);
	return isset( $files[ $i ] ) ? get_template_directory_uri() . '/assets/images/etapas/' . $files[ $i ] : '';
}

/**
 * Quantidade de etapas por linha (1 a 5).
 *
 * @return int
 */
function imob_home_process_columns() {
	$cols = (int) get_theme_mod( 'imob_home_process_columns', 5 );
	return ( $cols >= 1 && $cols <= 5 ) ? $cols : 5;
}

/**
 * Controles extras: colunas das etapas e botão de WhatsApp dos depoimentos.
 *
 * @param WP_Customize_Manager $wp_customize Gerenciador do Customizer.
 */
function imob_home_customize_extra( $wp_customize ) {
	$wp_customize->add_setting(
		'imob_home_process_columns',
		array(
			'default'           => 5,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'imob_home_process_columns',
		array(
			'label'       => __( 'Etapas por linha', 'imobiliaria-tema' ),
			'description' => __( 'Quantidade de etapas exibidas lado a lado em telas grandes. Em tablets e celulares o layout se ajusta automaticamente.', 'imobiliaria-tema' ),
			'section'     => 'imob_home_process',
			'type'        => 'select',
			'priority'    => 5,
			'choices'     => array(
				1 => __( '1 por linha', 'imobiliaria-tema' ),
				2 => __( '2 por linha', 'imobiliaria-tema' ),
				3 => __( '3 por linha', 'imobiliaria-tema' ),
				4 => __( '4 por linha', 'imobiliaria-tema' ),
				5 => __( '5 por linha', 'imobiliaria-tema' ),
			),
		)
	);

	$wp_customize->add_setting(
		'imob_home_testimonials_whatsapp',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'imob_home_testimonials_whatsapp',
		array(
			'label'       => __( 'Exibir botão do WhatsApp', 'imobiliaria-tema' ),
			'description' => __( 'Mostra o botão "Realize o seu sonho" abaixo dos depoimentos.', 'imobiliaria-tema' ),
			'section'     => 'imob_home_testimonials',
			'type'        => 'checkbox',
		)
	);
	$wp_customize->add_setting(
		'imob_home_testimonials_whatsapp_label',
		array(
			'default'           => __( 'Realize o seu sonho', 'imobiliaria-tema' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'imob_home_testimonials_whatsapp_label',
		array(
			'label'   => __( 'Texto do botão do WhatsApp', 'imobiliaria-tema' ),
			'section' => 'imob_home_testimonials',
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'imob_home_customize_extra', 25 );
