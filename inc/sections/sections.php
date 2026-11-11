<?php
/**
 * Seções do layout Figma (home, Sobre, Avaliação de imóveis).
 *
 * Cada seção tem um esquema de campos. O mesmo esquema gera:
 * - os widgets do Elementor (categoria "Imobiliária - Seções"), com os textos do Figma como padrão;
 * - os argumentos dos templates em template-parts/sections/*.php.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Esquema das seções: título, ícone do Elementor e campos.
 *
 * Tipos de campo: text, textarea, html, image, url, select, switcher, number, repeater.
 *
 * @return array
 */
function imob_sections_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$btn_fields = function ( $b1 = 'Ver imóveis', $b2 = 'Entre em contato' ) {
		return array(
			'btn1_text' => array( 'text', 'Botão 1: texto', $b1 ),
			'btn1_url'  => array( 'url', 'Botão 1: link (vazio = página de imóveis)', '' ),
			'btn2_text' => array( 'text', 'Botão 2: texto', $b2 ),
			'btn2_url'  => array( 'url', 'Botão 2: link (vazio = WhatsApp)', '' ),
		);
	};

	$steps = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$steps[] = array(
			'label' => imob_home_mod( "step_{$i}_label" ),
			'title' => imob_home_mod( "step_{$i}_title" ),
			'text'  => imob_home_mod( "step_{$i}_text" ),
			'image' => (int) get_theme_mod( "imob_home_step_{$i}_image", 0 ),
		);
	}
	$numbers = array();
	for ( $i = 1; $i <= 6; $i++ ) {
		$numbers[] = array(
			'icon'  => imob_home_mod( "number_{$i}_icon" ),
			'value' => imob_home_mod( "number_{$i}_value" ),
			'label' => imob_home_mod( "number_{$i}_label" ),
		);
	}

	// Padrões do layout "Oferta" do topo: dados da campanha da Black Friday.
	$bf = function_exists( 'imob_bf_hero_args' ) ? imob_bf_hero_args() : array_fill_keys( array( 'tag', 'title_big', 'title_sub', 'text', 'btn1_text', 'btn2_text', 'card_label', 'card_prefix', 'card_value', 'card_text' ), '' );

	$schema = array(
		'hero'         => array(
			'title'  => 'Topo (hero)',
			'icon'   => 'eicon-banner',
			'fields' => array_merge(
				array(
					'eyebrow'     => array( 'text', 'Chamada superior', imob_home_mod( 'hero_eyebrow' ) ),
					'title'       => array( 'textarea', 'Título (H1)', imob_home_mod( 'hero_title' ) ),
					'text'        => array( 'textarea', 'Texto', imob_home_mod( 'hero_text' ) ),
					'image'       => array( 'image', 'Imagem', (int) imob_home_mod( 'hero_image' ) ),
					'image_style' => array(
						'select',
						'Estilo da imagem',
						'angled',
						array(
							'angled'   => 'Foto inclinada (ocupa a lateral)',
							'portrait' => 'Retrato recortado (PNG sem fundo)',
						),
					),
					'bg_image'    => array( 'image', 'Imagem de fundo (estilo retrato)', 0 ),
				),
				$btn_fields(),
				array(
					'show_partners'  => array( 'switcher', 'Mostrar construtoras parceiras', 'yes' ),
					'partners_title' => array( 'text', 'Texto das construtoras', imob_home_mod( 'construtoras_title' ) ),
				),
				array(
					'layout'          => array(
						'select',
						'Layout do topo',
						'auto',
						array(
							'auto'    => 'Automático (Oferta na página inicial, Clássico nas demais)',
							'offer'   => 'Oferta (igual à landing da Black Friday)',
							'classic' => 'Clássico (texto + imagem)',
						),
					),
					'offer_tag'       => array( 'text', 'Oferta: etiqueta', $bf['tag'] ),
					'offer_title_big' => array( 'text', 'Oferta: título grande (H1)', $bf['title_big'] ),
					'offer_title_sub' => array( 'text', 'Oferta: subtítulo', $bf['title_sub'] ),
					'offer_text'      => array( 'textarea', 'Oferta: texto (aceita <strong>)', $bf['text'] ),
					'offer_btn1_text' => array( 'text', 'Oferta: botão 1 texto', $bf['btn1_text'] ),
					'offer_btn1_url'  => array( 'url', 'Oferta: botão 1 link (vazio = WhatsApp da campanha)', '' ),
					'offer_btn2_text' => array( 'text', 'Oferta: botão 2 texto', $bf['btn2_text'] ),
					'offer_btn2_url'  => array( 'url', 'Oferta: botão 2 link (vazio = página da Black Friday)', '' ),
					'offer_period'    => array( 'switcher', 'Oferta: mostrar período da campanha', 'yes' ),
					'card_label'      => array( 'text', 'Quadro: etiqueta', $bf['card_label'] ),
					'card_prefix'     => array( 'text', 'Quadro: texto acima do valor', $bf['card_prefix'] ),
					'card_value'      => array( 'text', 'Quadro: valor em destaque', $bf['card_value'] ),
					'card_text'       => array( 'text', 'Quadro: texto abaixo do valor', $bf['card_text'] ),
					'card_countdown'  => array( 'switcher', 'Quadro: contagem regressiva (datas em Personalizar > Black Friday)', 'yes' ),
				)
			),
		),
		'linkbio'      => array(
			'title'  => 'Links (estilo Linktree)',
			'icon'   => 'eicon-link',
			'fields' => array(
				'avatar'         => array( 'image', 'Foto ou logo (vazio = logo do site)', 0 ),
				'name'           => array( 'text', 'Nome', 'Vinicius Matias' ),
				'bio'            => array( 'textarea', 'Descrição', 'Corretor de imóveis em Campina Grande - PB | CRECI-PB 9972-F. Encontre o seu imóvel com quem entende do assunto.' ),
				'show_social'    => array( 'switcher', 'Mostrar ícones das redes sociais', 'yes' ),
				'wa_text'        => array( 'text', 'Botão principal (WhatsApp): texto', 'Falar com o corretor no WhatsApp' ),
				'wa_message'     => array( 'textarea', 'Mensagem do WhatsApp', 'Olá! Vim pelo Instagram e quero ajuda para encontrar um imóvel.' ),
				'bf_show'        => array( 'switcher', 'Mostrar destaque da Black Friday (some sozinho quando a campanha acaba)', 'yes' ),
				'bf_text'        => array( 'text', 'Black Friday: texto', 'Black Friday: R$ 1.000 de desconto extra' ),
				'imoveis_count'  => array( 'number', 'Quantidade de últimos imóveis', 4 ),
				'imoveis_title'  => array( 'text', 'Título dos imóveis', 'Últimos imóveis cadastrados' ),
				'imoveis_tipo'   => array( 'text', 'Tipo de imóvel (slug; vazio = todos)', '' ),
				'all_text'       => array( 'text', 'Botão "ver todos": texto', 'Ver todos os imóveis' ),
				'posts_count'    => array( 'number', 'Quantidade de últimos posts', 3 ),
				'posts_title'    => array( 'text', 'Título dos posts', 'Últimas do blog' ),
				'links'          => array(
					'repeater',
					'Links extras',
					array(
						array(
							'text' => 'Imóveis Minha Casa Minha Vida',
							'url'  => '/imoveis-minha-casa-minha-vida-em-campina-grande/',
							'icon' => 'family_home',
						),
						array(
							'text' => 'Quanto vale o seu imóvel? Peça uma avaliação',
							'url'  => '/avaliacao-de-imoveis/',
							'icon' => 'request_quote',
						),
					),
					array(
						'text' => array( 'text', 'Texto', '' ),
						'url'  => array( 'url', 'Link ({imoveis}, {whatsapp} ou URL)', '' ),
						'icon' => array( 'text', 'Ícone (Material Symbols, ex.: home, sell, chat)', 'link' ),
					),
				),
				'utm'            => array( 'switcher', 'Marcar links para o Google Analytics (utm_source=instagram)', 'yes' ),
				'footer_text'    => array( 'text', 'Rodapé', 'IS22 Imóveis · Campina Grande - PB' ),
			),
		),
		'slider'       => array(
			'title'  => 'Slider (largura total)',
			'icon'   => 'eicon-slides',
			'fields' => array(
				'slides'      => array(
					'repeater',
					'Slides',
					array(
						array(
							'image'     => 0,
							'eyebrow'   => 'O endereço dos seus planos.',
							'title'     => 'Encontre o imóvel ideal em Campina Grande',
							'text'      => 'Apartamentos, casas, terrenos e lançamentos com as melhores condições.',
							'btn1_text' => 'Ver imóveis',
							'btn1_url'  => '{imoveis}',
							'btn2_text' => 'Fale conosco',
							'btn2_url'  => '{whatsapp}',
							'align'     => 'left',
						),
						array(
							'image'     => 0,
							'eyebrow'   => 'Minha Casa Minha Vida',
							'title'     => 'Seu imóvel com subsídio e juros reduzidos',
							'text'      => 'Simule o financiamento e descubra a sua faixa do programa.',
							'btn1_text' => 'Simular agora',
							'btn1_url'  => '{whatsapp}',
							'btn2_text' => '',
							'btn2_url'  => '',
							'align'     => 'center',
						),
					),
					array(
						'image'     => array( 'image', 'Imagem de fundo', 0 ),
						'eyebrow'   => array( 'text', 'Chamada superior', '' ),
						'title'     => array( 'text', 'Título', '' ),
						'text'      => array( 'textarea', 'Texto', '' ),
						'btn1_text' => array( 'text', 'Botão 1: texto', '' ),
						'btn1_url'  => array( 'url', 'Botão 1: link ({imoveis}, {whatsapp} ou URL)', '' ),
						'btn2_text' => array( 'text', 'Botão 2: texto', '' ),
						'btn2_url'  => array( 'url', 'Botão 2: link ({imoveis}, {whatsapp} ou URL)', '' ),
						'link_url'  => array( 'url', 'Link do slide inteiro (opcional)', '' ),
						'align'     => array(
							'select',
							'Alinhamento do texto',
							'left',
							array(
								'left'   => 'Esquerda',
								'center' => 'Centro',
								'right'  => 'Direita',
							),
						),
					),
				),
				'height'      => array(
					'select',
					'Altura',
					'md',
					array(
						'sm'   => 'Baixa (420px)',
						'md'   => 'Média (560px)',
						'lg'   => 'Alta (720px)',
						'full' => 'Tela inteira',
					),
				),
				'overlay'     => array(
					'select',
					'Escurecer a imagem',
					'50',
					array(
						'0'  => 'Sem escurecer',
						'30' => 'Leve (30%)',
						'50' => 'Médio (50%)',
						'70' => 'Forte (70%)',
					),
				),
				'autoplay'    => array( 'switcher', 'Passar automaticamente', 'yes' ),
				'interval'    => array( 'number', 'Tempo de cada slide (segundos)', 6 ),
				'arrows'      => array( 'switcher', 'Mostrar setas', 'yes' ),
				'dots'        => array( 'switcher', 'Mostrar indicadores', 'yes' ),
				'full_bleed'  => array( 'switcher', 'Forçar 100% da largura da tela (mesmo dentro de container)', 'yes' ),
				'first_is_h1' => array( 'switcher', 'Título do 1º slide como H1 (use se for o topo da página)', '' ),
			),
		),
		'process'      => array(
			'title'  => 'Etapas do processo',
			'icon'   => 'eicon-number-field',
			'fields' => array(
				'title'   => array( 'text', 'Título', imob_home_mod( 'process_title' ) ),
				'text'    => array( 'textarea', 'Texto', imob_home_mod( 'process_text' ) ),
				'columns' => array(
					'select',
					'Etapas por linha',
					'5',
					array(
						'3' => '3 por linha',
						'4' => '4 por linha',
						'5' => '5 por linha',
					),
				),
				'steps'   => array(
					'repeater',
					'Etapas',
					$steps,
					array(
						'label' => array( 'text', 'Etiqueta', '' ),
						'title' => array( 'text', 'Título', '' ),
						'text'  => array( 'textarea', 'Texto', '' ),
						'image' => array( 'image', 'Imagem', 0 ),
					),
				),
			),
		),
		'properties'   => array(
			'title'  => 'Grade de imóveis',
			'icon'   => 'eicon-posts-grid',
			'fields' => array(
				'title'      => array( 'text', 'Título', imob_home_mod( 'latest_title' ) ),
				'text'       => array( 'html', 'Texto (use {whatsapp} como link do WhatsApp)', imob_home_mod( 'latest_text' ) ),
				'tipo'       => array( 'text', 'Tipo de imóvel (slug; vazio = todos; vários separados por vírgula)', '' ),
				'count'      => array( 'number', 'Quantidade total de imóveis', 24 ),
				'per_page'   => array( 'number', 'Imóveis por página', 6 ),
				'style'      => array(
					'select',
					'Fundo',
					'split',
					array(
						'light' => 'Claro',
						'dark'  => 'Azul-marinho',
						'split' => 'Claro em cima, azul embaixo',
					),
				),
				'all_text'   => array( 'text', 'Texto do botão "ver todos"', 'Ver todos' ),
				'all_url'    => array( 'url', 'Link do botão (vazio = página de imóveis)', '' ),
				'hide_empty' => array( 'switcher', 'Esconder a seção quando não houver imóveis', 'yes' ),
			),
		),
		'numbers'      => array(
			'title'  => 'Números da empresa',
			'icon'   => 'eicon-counter',
			'fields' => array(
				'title'       => array( 'text', 'Título', imob_home_mod( 'numbers_title' ) ),
				'text'        => array( 'textarea', 'Texto', imob_home_mod( 'numbers_text' ) ),
				'items'       => array(
					'repeater',
					'Números',
					$numbers,
					array(
						'icon'  => array( 'text', 'Ícone (Google Material Symbols, ex.: home)', 'home' ),
						'value' => array( 'text', 'Número', '' ),
						'label' => array( 'text', 'Descrição', '' ),
					),
				),
				'divider_top' => array( 'switcher', 'Linha dourada no topo', 'yes' ),
				'tight_top'   => array( 'switcher', 'Encostar na seção anterior (fundo azul contínuo)', 'yes' ),
			),
		),
		'lead'         => array(
			'title'  => 'Formulário de interesse',
			'icon'   => 'eicon-form-horizontal',
			'fields' => array_merge(
				array(
					'eyebrow' => array( 'text', 'Chamada superior', imob_home_mod( 'form_eyebrow' ) ),
					'title'   => array( 'text', 'Título', imob_home_mod( 'form_title' ) ),
					'text'    => array( 'textarea', 'Texto', imob_home_mod( 'form_text' ) ),
				),
				$btn_fields(),
				array(
					'submit_text' => array( 'text', 'Texto do botão de envio', 'Enviar mensagem' ),
				)
			),
		),
		'blog'         => array(
			'title'  => 'Últimas do blog',
			'icon'   => 'eicon-post-list',
			'fields' => array(
				'title' => array( 'text', 'Título', imob_home_mod( 'blog_title' ) ),
				'text'  => array( 'textarea', 'Texto', imob_home_mod( 'blog_text' ) ),
				'count' => array( 'number', 'Quantidade de posts', 4 ),
			),
		),
		'testimonials' => array(
			'title'  => 'Depoimentos',
			'icon'   => 'eicon-testimonial',
			'fields' => array(
				'title'     => array( 'text', 'Título', imob_home_mod( 'testimonials_title' ) ),
				'text'      => array( 'textarea', 'Texto', imob_home_mod( 'testimonials_text' ) ),
				'count'     => array( 'number', 'Quantidade de depoimentos', 12 ),
				'show_whatsapp' => array( 'switcher', 'Exibir botão do WhatsApp', 'yes' ),
				'btn1_text' => array( 'text', 'Botão: texto', 'Realize o seu sonho' ),
				'btn1_url'  => array( 'url', 'Botão: link (vazio = WhatsApp)', '' ),
			),
		),
		'split'        => array(
			'title'  => 'Apresentação (título + texto)',
			'icon'   => 'eicon-column',
			'fields' => array_merge(
				array(
					'eyebrow'      => array( 'text', 'Chamada superior', 'Conheça sua imobiliária' ),
					'title'        => array( 'textarea', 'Título', 'A IS22 imóveis' ),
					'subtitle'     => array( 'textarea', 'Subtítulo', 'Mais do que encontrar imóveis, abrimos caminhos para novas possibilidades.' ),
					'content'      => array( 'html', 'Texto do quadro', '<p>A IS22 Imóveis nasceu com o propósito de oferecer uma experiência mais próxima, transparente e eficiente na compra e venda de imóveis em Campina Grande e região.</p>' ),
					'reverse'      => array( 'switcher', 'Inverter (quadro à esquerda, título à direita)', '' ),
					'show_buttons' => array( 'switcher', 'Mostrar botões', 'yes' ),
				),
				$btn_fields(),
				array(
					'quote'       => array( 'text', 'Citação abaixo (opcional)', '' ),
					'quote_ref'   => array( 'text', 'Referência da citação', '' ),
					'divider'     => array( 'switcher', 'Linha dourada no fim', '' ),
					'title_large' => array( 'switcher', 'Título grande', 'yes' ),
				)
			),
		),
		'split_image'  => array(
			'title'  => 'Apresentação com foto (texto + imagem)',
			'icon'   => 'eicon-image-box',
			'fields' => array_merge(
				array(
					'eyebrow'     => array( 'text', 'Chamada superior', 'Seu imóvel, meu compromisso' ),
					'title'       => array( 'textarea', 'Título', 'Quem está por trás da IS22' ),
					'subtitle'    => array( 'textarea', 'Subtítulo', 'Experiência, estratégia e atendimento para ajudar você a encontrar o imóvel certo.' ),
					'content'     => array( 'html', 'Texto', '<p><strong>Sou Vinicius Matias, corretor e avaliador de imóveis, CRECI-PB 9972-F e CNAI 35503.</strong></p><p>Minha trajetória no mercado imobiliário é construída a partir da união entre conhecimento do mercado, experiência com marketing digital e um olhar atento às novas formas de apresentar e conectar pessoas aos imóveis.</p><p>Essa experiência com internet e comunicação faz parte da forma como trabalho: busco apresentar cada imóvel de maneira estratégica, com informações claras, anúncios atrativos e ferramentas que facilitem a jornada de compra e venda.</p>' ),
					'image'       => array( 'image', 'Foto (PNG com fundo transparente)', 0 ),
					'image_alt'   => array( 'text', 'Texto alternativo da foto', 'Vinicius Matias, corretor de imóveis em Campina Grande' ),
					'image_side'  => array(
						'select',
						'Posição da foto',
						'right',
						array(
							'right' => 'Foto à direita',
							'left'  => 'Foto à esquerda',
						),
					),
					'backdrop'    => array(
						'select',
						'Fundo atrás da foto',
						'arch',
						array(
							'arch'   => 'Arco azul com detalhe dourado',
							'circle' => 'Círculo dourado suave',
							'none'   => 'Sem fundo',
						),
					),
					'show_buttons' => array( 'switcher', 'Mostrar botões', 'yes' ),
				),
				$btn_fields(),
				array(
					'title_large' => array( 'switcher', 'Título grande', 'yes' ),
					'divider'     => array( 'switcher', 'Linha dourada no fim', '' ),
				)
			),
		),
		'pillars'      => array(
			'title'  => 'Blocos coloridos (missão, visão, valores)',
			'icon'   => 'eicon-gallery-justified',
			'fields' => array(
				'title' => array( 'text', 'Título da seção (opcional)', '' ),
				'text'  => array( 'textarea', 'Texto da seção (opcional)', '' ),
				'items' => array(
					'repeater',
					'Blocos',
					array(
						array( 'icon' => 'target', 'title' => 'Missão', 'content' => '<p>Texto da missão.</p>', 'width' => 'half', 'tone' => 'mid' ),
						array( 'icon' => 'visibility', 'title' => 'Visão', 'content' => '<p>Texto da visão.</p>', 'width' => 'half', 'tone' => 'deep' ),
						array( 'icon' => 'diamond', 'title' => 'Valores', 'content' => '<ul><li>Valor 1</li></ul>', 'width' => 'full', 'tone' => 'steel' ),
					),
					array(
						'icon'    => array( 'text', 'Ícone (Google Material Symbols)', 'star' ),
						'title'   => array( 'text', 'Título', '' ),
						'content' => array( 'html', 'Texto', '' ),
						'width'   => array(
							'select',
							'Largura',
							'half',
							array(
								'third' => '1/3',
								'half'  => '1/2',
								'full'  => 'Linha inteira',
							),
						),
						'tone'    => array(
							'select',
							'Cor',
							'mid',
							array(
								'mid'   => 'Azul médio',
								'deep'  => 'Azul-marinho',
								'steel' => 'Azul acinzentado',
							),
						),
					),
				),
			),
		),
		'checklist'    => array(
			'title'  => 'Texto + lista com ícones',
			'icon'   => 'eicon-bullet-list',
			'fields' => array(
				'title'   => array( 'text', 'Título', 'Trajetória e formação' ),
				'content' => array( 'html', 'Texto', '<p>Texto de apresentação.</p>' ),
				'icon'    => array( 'text', 'Ícone da lista (Material Symbols)', 'check' ),
				'items'   => array(
					'repeater',
					'Itens',
					array( array( 'text' => 'Item da lista' ) ),
					array(
						'text' => array( 'textarea', 'Texto', '' ),
					),
				),
			),
		),
		'features'     => array(
			'title'  => 'Diferenciais (grade com ícones)',
			'icon'   => 'eicon-icon-box',
			'fields' => array(
				'eyebrow' => array( 'text', 'Chamada superior', '' ),
				'title'   => array( 'text', 'Título', 'Nossos diferenciais' ),
				'text'    => array( 'textarea', 'Texto', '' ),
				'style'   => array(
					'select',
					'Fundo',
					'light',
					array(
						'light' => 'Claro',
						'dark'  => 'Azul-marinho',
					),
				),
				'items'   => array(
					'repeater',
					'Itens',
					array( array( 'icon' => 'verified', 'title' => 'Diferencial', 'text' => 'Descrição do diferencial.' ) ),
					array(
						'icon'  => array( 'text', 'Ícone (Google Material Symbols)', 'verified' ),
						'title' => array( 'text', 'Título', '' ),
						'text'  => array( 'textarea', 'Texto', '' ),
					),
				),
			),
		),
		'divider'      => array(
			'title'  => 'Linha dourada',
			'icon'   => 'eicon-divider',
			'fields' => array(
				'on_dark' => array( 'switcher', 'Sobre fundo azul-marinho', '' ),
			),
		),
		'cta'          => array(
			'title'  => 'Chamada para contato',
			'icon'   => 'eicon-call-to-action',
			'fields' => array_merge(
				array(
					'eyebrow' => array( 'text', 'Chamada superior', '' ),
					'title'   => array( 'text', 'Título', 'Entre em contato e saiba mais' ),
					'content' => array( 'html', 'Texto', '' ),
				),
				$btn_fields( 'Falar no WhatsApp', '' )
			),
		),
	);

	return $schema;
}

/**
 * Valores padrão de uma seção, no formato dos templates.
 *
 * @param string $name Seção.
 * @return array
 */
function imob_section_defaults( $name ) {
	$schema = imob_sections_schema();
	$args   = array();
	foreach ( $schema[ $name ]['fields'] as $key => $field ) {
		$args[ $key ] = imob_section_normalize( $field[0], $field[2], $field );
	}
	return $args;
}

/**
 * Converte um valor (padrão do esquema ou do Elementor) para o formato do template.
 *
 * @param string $type  Tipo do campo.
 * @param mixed  $value Valor.
 * @param array  $field Definição do campo.
 * @return mixed
 */
function imob_section_normalize( $type, $value, $field = array() ) {
	switch ( $type ) {
		case 'image':
			if ( is_array( $value ) ) {
				return array(
					'id'  => isset( $value['id'] ) ? (int) $value['id'] : 0,
					'url' => isset( $value['url'] ) ? (string) $value['url'] : '',
				);
			}
			return array(
				'id'  => (int) $value,
				'url' => '',
			);
		case 'url':
			return is_array( $value ) ? (string) ( $value['url'] ?? '' ) : (string) $value;
		case 'switcher':
			return ( 'yes' === $value || true === $value );
		case 'number':
			return (int) $value;
		case 'repeater':
			$rows = array();
			foreach ( (array) $value as $row ) {
				$item = array();
				foreach ( $field[3] as $sub_key => $sub ) {
					$item[ $sub_key ] = imob_section_normalize( $sub[0], $row[ $sub_key ] ?? $sub[2], $sub );
				}
				$rows[] = $item;
			}
			return $rows;
		default:
			return is_scalar( $value ) ? (string) $value : '';
	}
}

/**
 * Converte as configurações de um widget do Elementor em argumentos do template.
 *
 * @param string $name     Seção.
 * @param array  $settings Configurações do widget.
 * @return array
 */
function imob_section_args_from_settings( $name, $settings ) {
	$schema = imob_sections_schema();
	$args   = array();
	foreach ( $schema[ $name ]['fields'] as $key => $field ) {
		$value        = array_key_exists( $key, $settings ) ? $settings[ $key ] : $field[2];
		$args[ $key ] = imob_section_normalize( $field[0], $value, $field );
	}
	return $args;
}

/**
 * Exibe uma seção.
 *
 * @param string $name Seção.
 * @param array  $args Argumentos (o que faltar vem do padrão).
 */
function imob_render_section( $name, $args = array() ) {
	$args = wp_parse_args( $args, imob_section_defaults( $name ) );
	imob_sections_enqueue();
	echo '<div class="hm hm-block hm-block--' . esc_attr( $name ) . '">';
	get_template_part( 'template-parts/sections/' . $name, null, $args );
	echo '</div>';
}

/* -------------------------------------------------------------------------
 * Ajudantes dos templates
 * ---------------------------------------------------------------------- */

/**
 * URL da listagem de imóveis.
 *
 * @return string
 */
function imob_sections_archive_url() {
	$url = get_post_type_archive_link( 'imovel' );
	return $url ? $url : home_url( '/imoveis/' );
}

/**
 * Imagem de um campo "image" normalizado.
 *
 * @param array  $img   ['id' => int, 'url' => string].
 * @param string $size  Tamanho.
 * @param array  $attrs Atributos.
 * @return string HTML.
 */
function imob_section_image( $img, $size = 'large', $attrs = array() ) {
	if ( ! empty( $img['id'] ) ) {
		$html = wp_get_attachment_image( (int) $img['id'], $size, false, $attrs );
		if ( $html ) {
			return $html;
		}
	}
	if ( ! empty( $img['url'] ) ) {
		$attr_html = '';
		foreach ( $attrs as $k => $v ) {
			$attr_html .= ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"';
		}
		return '<img src="' . esc_url( $img['url'] ) . '"' . ( isset( $attrs['alt'] ) ? '' : ' alt=""' ) . $attr_html . '>';
	}
	return '';
}

/**
 * Troca os atalhos {whatsapp} e {imoveis} pelos links do site.
 *
 * @param string $text Texto ou URL.
 * @return string
 */
function imob_section_url( $text ) {
	return str_replace(
		array( '{whatsapp}', '{imoveis}' ),
		array( imob_home_whatsapp_url(), imob_sections_archive_url() ),
		(string) $text
	);
}

/**
 * Texto com HTML permitido e o atalho {whatsapp} trocado pelo link do WhatsApp.
 *
 * @param string $html Texto.
 * @return string
 */
function imob_section_html( $html ) {
	$html = str_replace(
		array( '{whatsapp}', '{imoveis}' ),
		array( esc_url( imob_home_whatsapp_url() ), esc_url( imob_sections_archive_url() ) ),
		(string) $html
	);
	return wp_kses_post( $html );
}

/**
 * Par de botões padrão (azul + contorno).
 *
 * @param array $args    Argumentos da seção (btn1_text, btn1_url, btn2_text, btn2_url).
 * @param bool  $on_dark Fundo escuro (botão 2 branco) ou claro (botão 2 azul-marinho).
 */
function imob_section_buttons( $args, $on_dark = true ) {
	$b1 = trim( (string) ( $args['btn1_text'] ?? '' ) );
	$b2 = trim( (string) ( $args['btn2_text'] ?? '' ) );
	if ( ! $b1 && ! $b2 ) {
		return;
	}
	$u1 = imob_section_url( ! empty( $args['btn1_url'] ) ? $args['btn1_url'] : '{imoveis}' );
	$u2 = imob_section_url( ! empty( $args['btn2_url'] ) ? $args['btn2_url'] : '{whatsapp}' );
	$ext = function ( $url ) {
		return ( 0 === strpos( $url, 'http' ) && false === strpos( $url, wp_parse_url( home_url(), PHP_URL_HOST ) ) ) ? ' target="_blank" rel="noopener noreferrer"' : '';
	};
	$is_wa = function ( $url ) {
		return ( false !== strpos( $url, 'whatsapp' ) || false !== strpos( $url, 'wa.me' ) );
	};
	echo '<div class="hm-actions">';
	if ( $b1 ) {
		$icon = $is_wa( $u1 ) ? imob_home_whatsapp_icon() : '<span class="material-symbols-outlined">home</span>';
		echo '<a class="hm-btn hm-btn--blue" href="' . esc_url( $u1 ) . '"' . $ext( $u1 ) . '>' . $icon . esc_html( $b1 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	if ( $b2 ) {
		echo '<a class="hm-btn ' . ( $on_dark ? 'hm-btn--ghost' : 'hm-btn--outline' ) . '" href="' . esc_url( $u2 ) . '"' . $ext( $u2 ) . '>' . ( $is_wa( $u2 ) ? imob_home_whatsapp_icon() : '' ) . esc_html( $b2 ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</div>';
}

/**
 * Cabeçalho padrão de seção (título + texto).
 *
 * @param string $title Título.
 * @param string $text  Texto (aceita HTML simples).
 */
function imob_section_head( $title, $text = '' ) {
	if ( ! $title && ! $text ) {
		return;
	}
	echo '<header class="hm-section-head">';
	if ( $title ) {
		echo '<h2 class="hm-section-title">' . esc_html( $title ) . '</h2>';
	}
	if ( $text ) {
		echo '<div class="hm-section-text">' . wpautop( imob_section_html( $text ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</header>';
}

/**
 * Consulta de imóveis disponíveis (sem vendidos/alugados), opcionalmente por tipo.
 *
 * @param string $slug  Slug(s) do tipo, separados por vírgula.
 * @param int    $count Quantidade.
 * @return WP_Query
 */
function imob_sections_property_query( $slug, $count = 24 ) {
	$args = array(
		'post_type'           => 'imovel',
		'posts_per_page'      => max( 1, (int) $count ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'status_imovel',
				'field'    => 'slug',
				'terms'    => array( 'vendido', 'alugado' ),
				'operator' => 'NOT IN',
			),
		),
	);
	if ( $slug ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'tipo_imovel',
			'field'    => 'slug',
			'terms'    => array_map( 'trim', explode( ',', $slug ) ),
		);
	}
	return new WP_Query( $args );
}

/**
 * Está no editor/pré-visualização do Elementor?
 *
 * @return bool
 */
function imob_sections_is_editor() {
	if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return true;
	}
	return class_exists( '\\Elementor\\Plugin' ) && \Elementor\Plugin::$instance->editor && \Elementor\Plugin::$instance->editor->is_edit_mode();
}

/* -------------------------------------------------------------------------
 * CSS e JS
 * ---------------------------------------------------------------------- */

/**
 * Registra os arquivos das seções.
 */
function imob_sections_register_assets() {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_register_style( 'imob-home', $uri . '/assets/css/home.css', array( 'imob-theme-style' ), (string) filemtime( $dir . '/assets/css/home.css' ) );
	wp_register_style( 'imob-hero-offer', $uri . '/assets/css/hero-offer.css', array( 'imob-home' ), (string) filemtime( $dir . '/assets/css/hero-offer.css' ) );
	wp_register_style( 'imob-sections', $uri . '/assets/css/sections.css', array( 'imob-home', 'imob-hero-offer' ), (string) filemtime( $dir . '/assets/css/sections.css' ) );
	wp_register_style( 'imob-linkbio', $uri . '/assets/css/linkbio.css', array( 'imob-sections' ), (string) filemtime( $dir . '/assets/css/linkbio.css' ) );
	wp_register_style( 'imob-slider', $uri . '/assets/css/slider.css', array( 'imob-sections' ), (string) filemtime( $dir . '/assets/css/slider.css' ) );
	wp_register_script( 'imob-home', $uri . '/assets/js/home.js', array(), (string) filemtime( $dir . '/assets/js/home.js' ), true );
	wp_register_script( 'imob-hero-offer', $uri . '/assets/js/hero-offer.js', array(), (string) filemtime( $dir . '/assets/js/hero-offer.js' ), true );
	wp_register_script( 'imob-slider', $uri . '/assets/js/slider.js', array(), (string) filemtime( $dir . '/assets/js/slider.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'imob_sections_register_assets', 5 );

/**
 * Enfileira os arquivos das seções.
 */
function imob_sections_enqueue() {
	if ( ! wp_style_is( 'imob-sections', 'registered' ) ) {
		imob_sections_register_assets();
	}
	wp_enqueue_style( 'imob-sections' );
	wp_enqueue_script( 'imob-home' );
}

/**
 * Carrega o CSS no <head> na home e nas páginas que usam as seções no Elementor
 * (evita o conteúdo aparecer sem estilo antes do CSS chegar).
 */
function imob_sections_maybe_enqueue() {
	if ( is_front_page() ) {
		imob_sections_enqueue();
		return;
	}
	if ( is_singular() ) {
		$data = get_post_meta( get_queried_object_id(), '_elementor_data', true );
		if ( is_string( $data ) && false !== strpos( $data, 'imob_sec_' ) ) {
			imob_sections_enqueue();
		}
		if ( is_string( $data ) && false !== strpos( $data, 'imob_sec_linkbio' ) ) {
			wp_enqueue_style( 'imob-linkbio' );
		}
		if ( is_string( $data ) && false !== strpos( $data, 'imob_sec_slider' ) ) {
			wp_enqueue_style( 'imob-slider' );
			wp_enqueue_script( 'imob-slider' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'imob_sections_maybe_enqueue', 20 );
