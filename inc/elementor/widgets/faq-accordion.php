<?php
/**
 * Elementor Widget: FAQ Accordion
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_faq_accordion extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_faq_accordion';
	}

	public function get_title() {
		return __( 'FAQ - Perguntas Frequentes', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	public function get_keywords() {
		return [ 'faq', 'perguntas', 'respostas', 'accordion', 'sanfona', 'ajuda', 'duvidas' ];
	}

	protected function register_controls() {

		// -------------------------------------------------------------
		// TAB CONTEÚDO: CABEÇALHO
		// -------------------------------------------------------------
		$this->start_controls_section(
			'section_header',
			[
				'label' => __( 'Cabeçalho da Seção', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_header',
			[
				'label'        => __( 'Exibir Cabeçalho?', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'section_badge',
			[
				'label'       => __( 'Badge / Tag Superior', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Tire suas dúvidas', 'imobiliaria-tema' ),
				'condition'   => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_control(
			'section_title',
			[
				'label'       => __( 'Título Principal', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Perguntas Frequentes', 'imobiliaria-tema' ),
				'label_block' => true,
				'condition'   => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_control(
			'section_subtitle',
			[
				'label'       => __( 'Subtítulo / Descrição', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => __( 'Confira abaixo as respostas para as principais dúvidas sobre financiamento, compra, venda e locação de imóveis.', 'imobiliaria-tema' ),
				'condition'   => [ 'show_header' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB CONTEÚDO: PERGUNTAS E RESPOSTAS (REPEATER COM WYSIWYG)
		// -------------------------------------------------------------
		$this->start_controls_section(
			'section_faq_items',
			[
				'label' => __( 'Perguntas e Respostas', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'question',
			[
				'label'       => __( 'Pergunta', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => __( 'Como funciona o financiamento de um imóvel?', 'imobiliaria-tema' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		// Campo WYSIWYG completo com negrito, itálico, links e formatações
		$repeater->add_control(
			'answer',
			[
				'label'       => __( 'Resposta (com editor de texto e links)', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::WYSIWYG,
				'default'     => __( 'O financiamento pode cobrir até <strong>80% do valor total do imóvel</strong> através dos principais bancos. Você precisará comprovar renda e apresentar documentação pessoal. <a href="#">Fale com nossos corretores</a> para simular o seu crédito.', 'imobiliaria-tema' ),
				'show_label'  => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'faq_items',
			[
				'label'       => __( 'Lista de Perguntas', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ question }}}',
				'default'     => [
					[
						'question' => __( 'Como funciona o financiamento de um imóvel?', 'imobiliaria-tema' ),
						'answer'   => __( 'O financiamento bancário pode cobrir até <strong>80% do valor do imóvel</strong>. O comprador apresenta a documentação necessária, passa por análise de crédito e escolhe o melhor plano de amortização. <a href="#">Converse com um de nossos corretores</a> para fazer uma simulação personalizada.', 'imobiliaria-tema' ),
					],
					[
						'question' => __( 'Posso usar meu saldo do FGTS na compra?', 'imobiliaria-tema' ),
						'answer'   => __( '<strong>Sim!</strong> O saldo da sua conta vinculada ao FGTS pode ser utilizado como entrada, amortização ou liquidação do saldo devedor do financiamento, desde que o imóvel e o comprador atendam aos critérios do Sistema Financeiro de Habitação (SFH).', 'imobiliaria-tema' ),
					],
					[
						'question' => __( 'Quais são as taxas e custos extras na aquisição?', 'imobiliaria-tema' ),
						'answer'   => __( 'Além do valor do imóvel, o comprador deve se atentar aos seguintes custos: <strong>ITBI (Imposto de Transmissão de Bens Imóveis)</strong>, custos de escritura ou contrato de financiamento com força de escritura, e <strong>registro do imóvel no cartório de Registro de Imóveis</strong>.', 'imobiliaria-tema' ),
					],
					[
						'question' => __( 'Como agendar uma visita presencial para conhecer o imóvel?', 'imobiliaria-tema' ),
						'answer'   => __( 'Você pode agendar sua visita facilmente clicando no <strong>botão de WhatsApp</strong> no canto inferior da página ou preenchendo o formulário de contato do anúncio. Nossa equipe retornará rapidamente para confirmar o horário mais conveniente.', 'imobiliaria-tema' ),
					],
				],
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB CONTEÚDO: COMPORTAMENTO DO ACCORDION
		// -------------------------------------------------------------
		$this->start_controls_section(
			'section_behavior',
			[
				'label' => __( 'Configurações do Accordion', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'first_open',
			[
				'label'        => __( 'Primeiro item aberto por padrão?', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'close_others',
			[
				'label'        => __( 'Fechar outros ao abrir um? (Modo Sanfona)', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'icon_style',
			[
				'label'   => __( 'Tipo de Ícone', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'chevron' => __( 'Seta / Chevron', 'imobiliaria-tema' ),
					'plus'    => __( 'Mais / Menos (+ / -)', 'imobiliaria-tema' ),
				],
				'default' => 'chevron',
			]
		);

		$this->add_control(
			'enable_faq_schema',
			[
				'label'        => __( 'Adicionar Schema FAQPage para SEO?', 'imobiliaria-tema' ),
				'description'  => __( 'Gera microdados estruturados para que as perguntas possam aparecer nos resultados de busca do Google.', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB ESTILO: CABEÇALHO
		// -------------------------------------------------------------
		$this->start_controls_section(
			'style_header',
			[
				'label'     => __( 'Cabeçalho da Seção', 'imobiliaria-tema' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_header' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'header_align',
			[
				'label'     => __( 'Alinhamento', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => __( 'Esquerda', 'imobiliaria-tema' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => __( 'Centro', 'imobiliaria-tema' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => __( 'Direita', 'imobiliaria-tema' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-header' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => __( 'Cor da Tag/Badge', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#B2915A',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-badge' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Cor do Título', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0E1A2B',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .imob-faq-title',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => __( 'Cor do Subtítulo', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#666666',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_margin_bottom',
			[
				'label'      => __( 'Espaçamento Abaixo do Cabeçalho', 'imobiliaria-tema' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 80 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 35 ],
				'selectors'  => [
					'{{WRAPPER}} .imob-faq-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB ESTILO: ITENS DO ACCORDION
		// -------------------------------------------------------------
		$this->start_controls_section(
			'style_items',
			[
				'label' => __( 'Caixa dos Itens', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_spacing',
			[
				'label'      => __( 'Espaçamento entre os Itens', 'imobiliaria-tema' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 40 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 14 ],
				'selectors'  => [
					'{{WRAPPER}} .imob-faq-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_bg_color',
			[
				'label'     => __( 'Cor de Fundo do Item (Fechado)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_bg_active_color',
			[
				'label'     => __( 'Cor de Fundo do Item (Aberto)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-item.is-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .imob-faq-item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => __( 'Arredondamento das Bordas', 'imobiliaria-tema' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'      => 8,
					'right'    => 8,
					'bottom'   => 8,
					'left'     => 8,
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .imob-faq-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .imob-faq-item',
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB ESTILO: PERGUNTA (CABEÇALHO DO ITEM)
		// -------------------------------------------------------------
		$this->start_controls_section(
			'style_question',
			[
				'label' => __( 'Pergunta (Botão de Abertura)', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'question_padding',
			[
				'label'      => __( 'Padding da Pergunta', 'imobiliaria-tema' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => 18,
					'right'    => 20,
					'bottom'   => 18,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .imob-faq-question' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'question_color',
			[
				'label'     => __( 'Cor do Texto (Normal)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0E1A2B',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-question-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'question_active_color',
			[
				'label'     => __( 'Cor do Texto (Aberto/Ativo)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2F80ED',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-item.is-active .imob-faq-question-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'question_typography',
				'selector' => '{{WRAPPER}} .imob-faq-question-text',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Cor do Ícone (Normal)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#999999',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-icon svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __( 'Cor do Ícone (Aberto)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2F80ED',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-item.is-active .imob-faq-icon svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB ESTILO: RESPOSTA (CONTEÚDO WYSIWYG)
		// -------------------------------------------------------------
		$this->start_controls_section(
			'style_answer',
			[
				'label' => __( 'Resposta (Conteúdo com Formatação e Links)', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'answer_padding',
			[
				'label'      => __( 'Padding da Resposta', 'imobiliaria-tema' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => 0,
					'right'    => 20,
					'bottom'   => 20,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .imob-faq-answer-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'answer_color',
			[
				'label'     => __( 'Cor do Texto', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#555555',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-answer-inner' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'answer_link_color',
			[
				'label'     => __( 'Cor dos Links', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2F80ED',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-answer-inner a' => 'color: {{VALUE}}; text-decoration: underline;',
				],
			]
		);

		$this->add_control(
			'answer_link_hover_color',
			[
				'label'     => __( 'Cor dos Links (Hover)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1b68cc',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-answer-inner a:hover' => 'color: {{VALUE}}; text-decoration: none;',
				],
			]
		);

		$this->add_control(
			'answer_strong_color',
			[
				'label'     => __( 'Cor do Texto em Negrito (<strong>)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0E1A2B',
				'selectors' => [
					'{{WRAPPER}} .imob-faq-answer-inner strong, {{WRAPPER}} .imob-faq-answer-inner b' => 'color: {{VALUE}}; font-weight: 700;',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'answer_typography',
				'selector' => '{{WRAPPER}} .imob-faq-answer-inner',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['faq_items'] ) ) {
			return;
		}

		$close_others = ( 'yes' === $settings['close_others'] ) ? 'true' : 'false';
		$first_open   = ( 'yes' === $settings['first_open'] );
		$icon_type    = $settings['icon_style'];
		$widget_id    = $this->get_id();

		$schema_data = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => [],
		];
		?>
		<div class="imob-faq-wrapper" id="imob-faq-<?php echo esc_attr( $widget_id ); ?>" data-close-others="<?php echo esc_attr( $close_others ); ?>">
			
			<?php if ( 'yes' === $settings['show_header'] ) : ?>
				<div class="imob-faq-header" style="margin-bottom: 30px;">
					<?php if ( ! empty( $settings['section_badge'] ) ) : ?>
						<span class="imob-faq-badge" style="display: inline-block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #B2915A; border: 1px solid #B2915A; padding: 4px 12px; border-radius: 20px; margin-bottom: 12px;">
							<?php echo esc_html( $settings['section_badge'] ); ?>
						</span>
					<?php endif; ?>

					<?php if ( ! empty( $settings['section_title'] ) ) : ?>
						<h2 class="imob-faq-title" style="margin: 0 0 10px; font-size: 2rem; font-weight: 800; color: #0E1A2B;">
							<?php echo esc_html( $settings['section_title'] ); ?>
						</h2>
					<?php endif; ?>

					<?php if ( ! empty( $settings['section_subtitle'] ) ) : ?>
						<p class="imob-faq-subtitle" style="margin: 0; font-size: 1rem; color: #666; max-width: 650px; margin-left: auto; margin-right: auto; line-height: 1.6;">
							<?php echo esc_html( $settings['section_subtitle'] ); ?>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="imob-faq-accordion-container" role="region" aria-label="<?php echo esc_attr( $settings['section_title'] ?: 'Perguntas Frequentes' ); ?>">
				<?php
				foreach ( $settings['faq_items'] as $index => $item ) :
					$is_active = ( 0 === $index && $first_open );
					$item_id   = 'imob-faq-item-' . $widget_id . '-' . $index;
					$ans_id    = 'imob-faq-ans-' . $widget_id . '-' . $index;

					// Coletar microdados Schema se ativado
					if ( 'yes' === $settings['enable_faq_schema'] && ! empty( $item['question'] ) && ! empty( $item['answer'] ) ) {
						$schema_data['mainEntity'][] = [
							'@type'          => 'Question',
							'name'           => wp_strip_all_tags( $item['question'] ),
							'acceptedAnswer' => [
								'@type' => 'Answer',
								'text'  => wp_kses_post( $item['answer'] ),
							],
						];
					}
					?>
					<div class="imob-faq-item <?php echo $is_active ? 'is-active' : ''; ?>" 
					     id="<?php echo esc_attr( $item_id ); ?>" 
					     style="border: 1px solid #E5E7EB; border-radius: 8px; margin-bottom: 14px; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.02); overflow: hidden; transition: all 0.25s ease;">
						
						<button type="button" 
						        class="imob-faq-question" 
						        aria-expanded="<?php echo $is_active ? 'true' : 'false'; ?>" 
						        aria-controls="<?php echo esc_attr( $ans_id ); ?>" 
						        style="width: 100%; text-align: left; background: none; border: none; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-family: inherit;">
							
							<span class="imob-faq-question-text" style="font-size: 1.05rem; font-weight: 700; color: <?php echo $is_active ? '#2F80ED' : '#0E1A2B'; ?>; line-height: 1.4; padding-right: 15px; transition: color 0.2s ease;">
								<?php echo esc_html( $item['question'] ); ?>
							</span>

							<span class="imob-faq-icon" style="flex-shrink: 0; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #F4F6F8; transition: transform 0.3s ease, background 0.2s ease;">
								<?php if ( 'plus' === $icon_type ) : ?>
									<svg class="icon-plus" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="<?php echo $is_active ? 'display:none;' : ''; ?>">
										<line x1="12" y1="5" x2="12" y2="19"></line>
										<line x1="5" y1="12" x2="19" y2="12"></line>
									</svg>
									<svg class="icon-minus" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="<?php echo $is_active ? '' : 'display:none;'; ?>">
										<line x1="5" y1="12" x2="19" y2="12"></line>
									</svg>
								<?php else : ?>
									<svg class="icon-chevron" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transform: <?php echo $is_active ? 'rotate(180deg)' : 'rotate(0deg)'; ?>; transition: transform 0.3s ease;">
										<polyline points="6 9 12 15 18 9"></polyline>
									</svg>
								<?php endif; ?>
							</span>
						</button>

						<div class="imob-faq-answer" 
						     id="<?php echo esc_attr( $ans_id ); ?>" 
						     role="region" 
						     style="<?php echo $is_active ? 'display: block;' : 'display: none;'; ?>">
							<div class="imob-faq-answer-inner" style="padding: 0 20px 20px 20px; color: #555555; font-size: 0.98rem; line-height: 1.7;">
								<?php echo wp_kses_post( $item['answer'] ); ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<?php if ( 'yes' === $settings['enable_faq_schema'] && ! empty( $schema_data['mainEntity'] ) ) : ?>
			<script type="application/ld+json">
				<?php echo wp_json_encode( $schema_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>
			</script>
		<?php endif; ?>

		<script>
		(function($){
			function initFaqAccordion($scope) {
				var $wrapper = $scope.find('.imob-faq-wrapper');
				if ( ! $wrapper.length ) return;

				var closeOthers = $wrapper.data('close-others') === true || $wrapper.data('close-others') === 'true';

				$wrapper.off('click', '.imob-faq-question').on('click', '.imob-faq-question', function(e){
					e.preventDefault();
					var $btn = $(this);
					var $item = $btn.closest('.imob-faq-item');
					var $answer = $item.find('.imob-faq-answer');
					var isCurrentlyActive = $item.hasClass('is-active');

					if ( closeOthers ) {
						$wrapper.find('.imob-faq-item').not($item).removeClass('is-active').each(function(){
							var $otherItem = $(this);
							$otherItem.find('.imob-faq-question').attr('aria-expanded', 'false');
							$otherItem.find('.imob-faq-answer').slideUp(200);
							$otherItem.find('.icon-chevron').css('transform', 'rotate(0deg)');
							$otherItem.find('.icon-plus').show();
							$otherItem.find('.icon-minus').hide();
						});
					}

					if ( isCurrentlyActive ) {
						$item.removeClass('is-active');
						$btn.attr('aria-expanded', 'false');
						$answer.slideUp(200);
						$item.find('.icon-chevron').css('transform', 'rotate(0deg)');
						$item.find('.icon-plus').show();
						$item.find('.icon-minus').hide();
					} else {
						$item.addClass('is-active');
						$btn.attr('aria-expanded', 'true');
						$answer.slideDown(250);
						$item.find('.icon-chevron').css('transform', 'rotate(180deg)');
						$item.find('.icon-plus').hide();
						$item.find('.icon-minus').show();
					}
				});
			}

			// Inicializar no carregamento do DOM e no preview do Elementor
			$(document).ready(function(){
				initFaqAccordion($(document));
			});

			$(window).on('elementor/frontend/init', function () {
				elementorFrontend.hooks.addAction('frontend/element_ready/imob_faq_accordion.default', function($scope){
					initFaqAccordion($scope);
				});
			});
		})(jQuery);
		</script>
		<?php
	}
}
