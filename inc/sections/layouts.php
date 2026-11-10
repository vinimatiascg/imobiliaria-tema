<?php
/**
 * Conteúdo inicial das páginas montadas com as seções (home, Sobre, Avaliação de imóveis).
 *
 * Usado pela home de fallback (front-page.php) e pelo comando
 * `wp imob montar-pagina <ID> <layout>`, que grava o layout no Elementor.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Layouts disponíveis: lista de [seção, argumentos].
 *
 * @return array
 */
function imob_sections_layouts() {
	$launch_text = imob_home_mod( 'launch_text' ) . ' <a href="{whatsapp}" target="_blank" rel="noopener noreferrer">Fale conosco</a> e descubra as opções disponíveis e as melhores condições para o seu perfil.';

	return array(
		'home'      => array(
			array( 'hero', array() ),
			array( 'process', array() ),
			array( 'divider', array() ),
			array( 'properties', array( 'style' => 'split' ) ),
			array( 'numbers', array() ),
			array(
				'properties',
				array(
					'title' => imob_home_mod( 'launch_title' ),
					'text'  => $launch_text,
					'tipo'  => imob_home_mod( 'launch_tipo' ),
					'style' => 'dark',
				),
			),
			array( 'lead', array() ),
			array(
				'properties',
				array(
					'title' => imob_home_mod( 'condo_title' ),
					'text'  => imob_home_mod( 'condo_text' ),
					'tipo'  => imob_home_mod( 'condo_tipo' ),
					'style' => 'light',
				),
			),
			array( 'divider', array() ),
			array( 'blog', array() ),
			array( 'divider', array() ),
			array( 'testimonials', array() ),
		),

		'sobre'     => array(
			array(
				'hero',
				array(
					'eyebrow'       => 'O endereço dos seus planos.',
					'title'         => 'Seu próximo imóvel começa com uma boa escolha.',
					'text'          => 'A IS22 Imóveis nasceu para oferecer uma experiência mais próxima, transparente e eficiente na compra e venda de imóveis em Campina Grande e região.',
					'image'         => 0,
					'image_style'   => 'portrait',
					'show_partners' => false,
				),
			),
			array(
				'split',
				array(
					'eyebrow'   => 'Conheça sua imobiliária',
					'title'     => 'A IS22 imóveis',
					'subtitle'  => 'Mais do que encontrar imóveis, abrimos caminhos para novas possibilidades.',
					'content'   => '<p>A IS22 Imóveis nasceu com o propósito de oferecer uma experiência mais próxima, transparente e eficiente na compra e venda de imóveis em Campina Grande e região.</p><p>Somos parceiros das principais construtoras da cidade e também trabalhamos em conjunto com corretores e imobiliárias, ampliando as opções e oportunidades disponíveis para nossos clientes.</p><p>Nosso objetivo é tornar a busca pelo imóvel mais simples, segura e personalizada — desde o primeiro contato até a conclusão da negociação.</p>',
					'quote'     => 'Deus abrirá portas que ninguém poderá fechar.',
					'quote_ref' => 'IS 22:22',
					'divider'   => true,
				),
			),
			array(
				'split',
				array(
					'eyebrow'  => 'Seu imóvel, meu compromisso',
					'title'    => 'Quem está por trás da IS22',
					'subtitle' => 'Experiência, estratégia e atendimento para ajudar você a encontrar o imóvel certo.',
					'content'  => '<p><strong>Sou Vinicius Matias, corretor e avaliador de imóveis, CRECI-PB 9972-F e CNAI 35503.</strong></p><p>Minha trajetória no mercado imobiliário é construída a partir da união entre conhecimento do mercado, experiência com marketing digital e um olhar atento às novas formas de apresentar e conectar pessoas aos imóveis.</p><p>Essa experiência com internet e comunicação faz parte da forma como trabalho: busco apresentar cada imóvel de maneira estratégica, com informações claras, anúncios atrativos e ferramentas que facilitem a jornada de compra e venda.</p>',
					'reverse'  => true,
				),
			),
			array(
				'pillars',
				array(
					'items' => array(
						array(
							'icon'    => 'track_changes',
							'title'   => 'Missão',
							'content' => '<p>Oferecer uma experiência imobiliária mais simples, transparente e eficiente, unindo atendimento de excelência, tecnologia e proximidade para atender nossos clientes nos canais digitais e presenciais.</p>',
							'width'   => 'half',
							'tone'    => 'mid',
						),
						array(
							'icon'    => 'visibility',
							'title'   => 'Visão',
							'content' => '<p>Ser referência no mercado imobiliário de Campina Grande e região, reconhecida pela qualidade do atendimento, inovação e capacidade de conectar pessoas às melhores oportunidades imobiliárias.</p>',
							'width'   => 'half',
							'tone'    => 'deep',
						),
						array(
							'icon'    => 'diamond',
							'title'   => 'Valores',
							'content' => '<ul><li>Atendimento: respeito, cordialidade e atenção a cada cliente.</li><li>Transparência: clareza e ética em todas as etapas.</li><li>Qualidade: anúncios e serviços feitos com excelência.</li><li>Ética profissional: respeito às normas e ao código do CRECI-PB.</li><li>Inovação: tecnologia e aprendizado constante para melhorar a experiência imobiliária.</li></ul>',
							'width'   => 'full',
							'tone'    => 'steel',
						),
					),
				),
			),
			array(
				'checklist',
				array(
					'title'   => 'Trajetória e formação',
					'content' => '<p class="hm-lead-line">Uma trajetória construída entre <strong>tecnologia</strong>, <strong>inovação e mercado imobiliário</strong>.</p><p>A experiência em tecnologia e desenvolvimento web trouxe uma visão estratégica para a atuação no mercado imobiliário, permitindo unir conhecimento técnico, comunicação digital e atendimento para oferecer uma experiência mais completa aos clientes.</p>',
					'icon'    => 'check',
					'items'   => array(
						array( 'text' => 'Licenciado em Computação pela UEPB;' ),
						array( 'text' => 'Especialista em Desenvolvimento Web pela UNOPAR;' ),
						array( 'text' => 'Mestre em Engenharia Ambiental, com foco em Modelagem Computacional, pela UEPB;' ),
						array( 'text' => 'Servidor público concursado da UEPB desde 2008;' ),
						array( 'text' => 'Experiência profissional na área de Tecnologia da Informação, com passagens por empresas como Unimed-CG, S.Toledo e Rede, entre outras;' ),
						array( 'text' => 'Formação em Técnico em Transações Imobiliárias (TTI) pela K&R Soluções.' ),
					),
				),
			),
		),

		'avaliacao' => array(
			array(
				'hero',
				array(
					'eyebrow'       => 'Laudos técnicos de avaliação',
					'title'         => 'Saiba quanto vale o seu imóvel.',
					'text'          => 'Laudos de avaliação mercadológica judiciais e extrajudiciais, feitos por corretor avaliador credenciado e seguindo a ABNT NBR 14.653 e a Resolução COFECI nº 1.066/2007.',
					'image'         => 0,
					'image_style'   => 'angled',
					'btn1_text'     => 'Solicitar orçamento',
					'btn1_url'      => '{whatsapp}',
					'btn2_text'     => 'Ver imóveis',
					'btn2_url'      => '{imoveis}',
					'show_partners' => false,
				),
			),
			array(
				'split',
				array(
					'eyebrow'   => 'Avaliação de imóveis',
					'title'     => 'O valor real, com técnica e transparência',
					'subtitle'  => 'Para vender, comprar, financiar, fazer partilha ou resolver uma questão judicial com segurança.',
					'content'   => '<p>Oferecemos um trabalho diferenciado, com as técnicas de avaliação mais recentes, para chegar ao real valor de mercado do seu imóvel.</p><p>Você também conta com <strong>suporte técnico gratuito</strong>: tiramos todas as dúvidas sobre o laudo para que você saiba exatamente o que está contratando.</p><p>Nosso compromisso é atender por completo às necessidades de cada cliente, do orçamento à entrega do laudo.</p>',
					'btn1_text' => 'Solicitar orçamento',
					'btn1_url'  => '{whatsapp}',
					'btn2_text' => '',
					'divider'   => true,
				),
			),
			array(
				'pillars',
				array(
					'items' => array(
						array(
							'icon'    => 'home_work',
							'title'   => 'Tipos de imóveis',
							'content' => '<ul><li>Residenciais</li><li>Comerciais</li><li>Empresariais</li><li>Rurais</li></ul>',
							'width'   => 'third',
							'tone'    => 'mid',
						),
						array(
							'icon'    => 'query_stats',
							'title'   => 'Métodos de avaliação',
							'content' => '<ul><li>Comparativo direto de dados de mercado</li><li>Evolutivo</li><li>Involutivo</li><li>Capitalização da renda</li></ul>',
							'width'   => 'third',
							'tone'    => 'deep',
						),
						array(
							'icon'    => 'gavel',
							'title'   => 'Outros serviços',
							'content' => '<ul><li>Assistência técnica em processos judiciais</li><li>Avaliação patrimonial de bens imóveis</li><li>Elaboração de plano diretor para cidades</li></ul>',
							'width'   => 'third',
							'tone'    => 'steel',
						),
					),
				),
			),
			array(
				'features',
				array(
					'eyebrow' => 'Por que nos escolher',
					'title'   => 'Vantagens para clientes e parceiros',
					'text'    => 'Um processo claro e organizado, do orçamento à apresentação do laudo.',
					'items'   => array(
						array( 'icon' => 'event_available', 'title' => 'Cronograma de conclusão', 'text' => 'Junto com o orçamento você recebe o cronograma de entrega do laudo, para planejar cada etapa.' ),
						array( 'icon' => 'public', 'title' => 'Atendimento em todo o país', 'text' => 'Fazemos avaliações de imóveis em qualquer região do Brasil.' ),
						array( 'icon' => 'handshake', 'title' => 'Parcerias lucrativas', 'text' => 'Planos exclusivos para bancos e escritórios de advocacia que precisam de mais de uma avaliação.' ),
						array( 'icon' => 'co_present', 'title' => 'Apresentação do laudo', 'text' => 'Se precisar, apresentamos o laudo final para você e sua equipe, presencialmente ou por videoconferência.' ),
						array( 'icon' => 'verified', 'title' => 'Normas da ABNT', 'text' => 'Todos os laudos seguem as normas mais recentes da ABNT para textos técnicos e científicos.' ),
						array( 'icon' => 'support_agent', 'title' => 'Suporte', 'text' => 'Atendimento por WhatsApp, e-mail e telefone em horário comercial.' ),
					),
				),
			),
			array(
				'cta',
				array(
					'eyebrow'   => 'Fale com a gente',
					'title'     => 'Entre em contato e saiba mais',
					'content'   => '<p>Quer saber mais sobre o nosso trabalho? Agende uma visita ou uma videochamada pelo Zoom, Skype ou Google Meet.</p><p><strong>E-mail:</strong> contato@viniciusmatias.com.br<br><strong>Telefone/WhatsApp:</strong> (83) 99926-7089</p>',
					'btn1_text' => 'Solicitar orçamento',
					'btn1_url'  => '{whatsapp}',
					'btn2_text' => 'Instagram',
					'btn2_url'  => 'https://www.instagram.com/viniciusmatiascorretor/',
				),
			),
		),
	);
}

/**
 * Layout padrão da home (fallback do front-page.php).
 *
 * @return array
 */
function imob_home_default_layout() {
	$layouts = imob_sections_layouts();
	return $layouts['home'];
}

/**
 * Converte um layout em dados do Elementor (um contêiner de largura total com os widgets).
 *
 * @param array $layout Lista de [seção, argumentos].
 * @return array
 */
function imob_sections_layout_to_elementor( $layout ) {
	$schema = imob_sections_schema();
	$id     = function () {
		return substr( md5( wp_generate_uuid4() ), 0, 7 );
	};
	$to_setting = function ( $type, $value, $field ) use ( &$to_setting, $id ) {
		switch ( $type ) {
			case 'image':
				$img_id = is_array( $value ) ? (int) ( $value['id'] ?? 0 ) : (int) $value;
				return array(
					'id'  => $img_id,
					'url' => $img_id ? (string) wp_get_attachment_image_url( $img_id, 'full' ) : '',
				);
			case 'url':
				return array(
					'url'         => (string) $value,
					'is_external' => '',
					'nofollow'    => '',
				);
			case 'switcher':
				return ( true === $value || 'yes' === $value ) ? 'yes' : '';
			case 'repeater':
				$rows = array();
				foreach ( (array) $value as $row ) {
					$item = array( '_id' => $id() );
					foreach ( $field[3] as $sub_key => $sub ) {
						$item[ $sub_key ] = $to_setting( $sub[0], $row[ $sub_key ] ?? $sub[2], $sub );
					}
					$rows[] = $item;
				}
				return $rows;
			default:
				return $value;
		}
	};

	$widgets = array();
	foreach ( $layout as $entry ) {
		list( $name, $args ) = $entry;
		$settings = array();
		foreach ( $schema[ $name ]['fields'] as $key => $field ) {
			$value            = array_key_exists( $key, $args ) ? $args[ $key ] : $field[2];
			$settings[ $key ] = $to_setting( $field[0], $value, $field );
		}
		$widgets[] = array(
			'id'         => $id(),
			'elType'     => 'widget',
			'widgetType' => 'imob_sec_' . $name,
			'settings'   => $settings,
			'elements'   => array(),
		);
	}

	return array(
		array(
			'id'       => $id(),
			'elType'   => 'container',
			'isInner'  => false,
			'settings' => array(
				'content_width'  => 'full',
				'flex_direction' => 'column',
				'flex_gap'       => array(
					'column'   => '0',
					'row'      => '0',
					'isLinked' => true,
					'unit'     => 'px',
					'size'     => 0,
				),
				'padding'        => array(
					'unit'     => 'px',
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'isLinked' => true,
				),
			),
			'elements' => $widgets,
		),
	);
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Grava um layout de seções numa página, no Elementor.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : ID da página.
	 *
	 * <layout>
	 * : home, sobre ou avaliacao.
	 *
	 * ## EXAMPLES
	 *
	 *     wp imob montar-pagina 17736 sobre
	 */
	WP_CLI::add_command(
		'imob montar-pagina',
		function ( $cli_args ) {
			list( $post_id, $layout_name ) = $cli_args;
			$post_id = (int) $post_id;
			$layouts = imob_sections_layouts();
			if ( ! get_post( $post_id ) || ! isset( $layouts[ $layout_name ] ) ) {
				WP_CLI::error( 'Página ou layout inválido. Layouts: ' . implode( ', ', array_keys( $layouts ) ) );
			}

			// Guarda o conteúdo anterior do Elementor (uma vez) para poder voltar.
			$old = get_post_meta( $post_id, '_elementor_data', true );
			if ( $old && ! metadata_exists( 'post', $post_id, '_imob_elementor_data_antes_secoes' ) ) {
				update_post_meta( $post_id, '_imob_elementor_data_antes_secoes', wp_slash( $old ) );
			}

			$data = imob_sections_layout_to_elementor( $layouts[ $layout_name ] );
			update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
			update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
			update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
			update_post_meta( $post_id, '_wp_page_template', 'elementor_header_footer' );
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
			}
			delete_post_meta( $post_id, '_elementor_css' );
			delete_post_meta( $post_id, '_elementor_element_cache' );
			delete_post_meta( $post_id, '_elementor_page_assets' );
			if ( class_exists( '\Elementor\Plugin' ) ) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			}
			clean_post_cache( $post_id );
			WP_CLI::success( sprintf( 'Página %d montada com o layout "%s" (%d seções).', $post_id, $layout_name, count( $layouts[ $layout_name ] ) ) );
		}
	);
}
