<?php
/**
 * Textos padrão da Página Inicial (layout do Figma) e ajudantes de WhatsApp.
 *
 * A home é editada no Elementor com os widgets "Imobiliária - Seções do site"
 * (inc/sections/sections.php); estes textos são o conteúdo inicial desses widgets.
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
