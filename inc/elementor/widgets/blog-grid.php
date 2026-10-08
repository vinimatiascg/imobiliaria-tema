<?php
/**
 * Elementor Widget: Grid de Posts (Blog)
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_blog_grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_blog_grid';
	}

	public function get_title() {
		return __( 'Grid de Posts (Blog)', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	public function get_keywords() {
		return [ 'posts', 'grid', 'blog', 'noticias', 'artigos', 'imoveis' ];
	}

	protected function register_controls() {

		// -------------------------------------------------------------
		// TAB CONTEÚDO: CONSULTA E FILTROS (QUERY)
		// -------------------------------------------------------------
		$this->start_controls_section(
			'section_query',
			[
				'label' => __( 'Consulta de Posts', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'   => __( 'Quantidade de Posts', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 30,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => __( 'Colunas', 'imobiliaria-tema' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1 Coluna',
					'2' => '2 Colunas',
					'3' => '3 Colunas',
					'4' => '4 Colunas',
				],
				'selectors'      => [
					'{{WRAPPER}} .imob-elementor-post-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		// Coletar categorias de posts do WP
		$categories = get_categories( [ 'hide_empty' => false ] );
		$cat_options = [ '' => __( 'Todas as Categorias', 'imobiliaria-tema' ) ];
		if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
			foreach ( $categories as $cat ) {
				$cat_options[ $cat->slug ] = $cat->name;
			}
		}

		$this->add_control(
			'filter_category',
			[
				'label'   => __( 'Filtrar por Categoria', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $cat_options,
				'default' => '',
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Ordenar Por', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'date'          => __( 'Data de Publicação', 'imobiliaria-tema' ),
					'title'         => __( 'Título do Post', 'imobiliaria-tema' ),
					'rand'          => __( 'Aleatório', 'imobiliaria-tema' ),
					'comment_count' => __( 'Mais Comentados', 'imobiliaria-tema' ),
				],
				'default' => 'date',
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Ordem', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'DESC' => __( 'Decrescente (Z-A / Mais Recentes)', 'imobiliaria-tema' ),
					'ASC'  => __( 'Crescente (A-Z / Mais Antigos)', 'imobiliaria-tema' ),
				],
				'default' => 'DESC',
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB CONTEÚDO: ELEMENTOS VISUAIS DO CARD
		// -------------------------------------------------------------
		$this->start_controls_section(
			'section_elements',
			[
				'label' => __( 'Elementos do Card', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_category_badge',
			[
				'label'        => __( 'Exibir Badges de Categorias?', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'        => __( 'Exibir Resumo?', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'     => __( 'Tamanho do Resumo (Palavras)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'default'   => 16,
				'min'       => 5,
				'max'       => 50,
				'condition' => [ 'show_excerpt' => 'yes' ],
			]
		);

		$this->add_control(
			'show_read_more',
			[
				'label'        => __( 'Exibir Botão "Ler Artigo"?', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label'     => __( 'Texto do Botão', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Ler artigo', 'imobiliaria-tema' ),
				'condition' => [ 'show_read_more' => 'yes' ],
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label'        => __( 'Habilitar Paginação Numérica?', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB ESTILO: GRID & CARDS
		// -------------------------------------------------------------
		$this->start_controls_section(
			'style_grid',
			[
				'label' => __( 'Grid e Cards', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_gap',
			[
				'label'      => __( 'Espaçamento entre os Cards', 'imobiliaria-tema' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 10, 'max' => 60 ],
				],
				'default'    => [ 'unit' => 'px', 'size' => 30 ],
				'selectors'  => [
					'{{WRAPPER}} .imob-elementor-post-grid' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_bg_color',
			[
				'label'     => __( 'Cor de Fundo do Card', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .imob-post-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .imob-post-card',
			]
		);

		$this->add_responsive_control(
			'card_border_radius',
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
					'{{WRAPPER}} .imob-post-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_box_shadow',
				'selector' => '{{WRAPPER}} .imob-post-card',
			]
		);

		$this->end_controls_section();

		// -------------------------------------------------------------
		// TAB ESTILO: TIPOGRAFIA E CORES DO CONTEÚDO
		// -------------------------------------------------------------
		$this->start_controls_section(
			'style_content',
			[
				'label' => __( 'Tipografia e Conteúdo', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Cor do Título', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#0E1A2B',
				'selectors' => [
					'{{WRAPPER}} .imob-card-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => __( 'Cor do Título (Hover)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2F80ED',
				'selectors' => [
					'{{WRAPPER}} .imob-card-title a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .imob-card-title',
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => __( 'Cor do Resumo', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#666666',
				'selectors' => [
					'{{WRAPPER}} .imob-post-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .imob-post-excerpt',
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => __( 'Cor do Botão "Ler artigo"', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#B2915A',
				'selectors' => [
					'{{WRAPPER}} .imob-read-more-link' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => __( 'Cor do Botão "Ler artigo" (Hover)', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#9E7E47',
				'selectors' => [
					'{{WRAPPER}} .imob-read-more-link:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

		$args = [
			'post_type'           => 'post',
			'posts_per_page'      => $settings['posts_per_page'],
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'ignore_sticky_posts' => 1,
			'paged'               => $paged,
		];

		if ( ! empty( $settings['filter_category'] ) ) {
			$args['category_name'] = $settings['filter_category'];
		}

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			echo '<div class="imob-elementor-imovel-grid imob-elementor-post-grid" style="display: grid; gap: 30px;">';

			while ( $query->have_posts() ) {
				$query->the_post();
				$categories = get_the_category();
				$excerpt_len = ! empty( $settings['excerpt_length'] ) ? intval( $settings['excerpt_length'] ) : 16;
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card imob-post-card' ); ?>>
					
					<!-- THUMBNAIL -->
					<div class="imob-card-thumb">
						<a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'imob_thumb' );
							} else {
								echo '<div class="imob-card-placeholder" style="display:flex; align-items:center; justify-content:center; background:#f0f2f5;">';
								echo '<span class="material-symbols-outlined" style="font-size: 44px; color: #b0b7c3;">article</span>';
								echo '</div>';
							}
							?>
						</a>
					</div>

					<!-- CORPO DO CARD -->
					<div class="imob-card-content">
						<?php the_title( '<h3 class="imob-card-title" style="margin: 0 0 10px;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>

						<?php if ( 'yes' === $settings['show_excerpt'] ) : ?>
							<div class="imob-card-residencial imob-post-excerpt" style="line-height: 1.6; margin-bottom: 15px;">
								<?php echo wp_trim_words( get_the_excerpt(), $excerpt_len, '...' ); ?>
							</div>
						<?php endif; ?>

						<!-- BADGES DE TODAS AS CATEGORIAS (ABAIXO DO RESUMO E ACIMA DA DATA) -->
						<?php if ( 'yes' === $settings['show_category_badge'] && ! empty( $categories ) ) : ?>
							<div class="imob-post-categories-badges" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 15px;">
								<?php foreach ( $categories as $cat ) : ?>
									<span class="badge-categoria"><?php echo esc_html( imob_strtoupper( $cat->name ) ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="imob-card-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: auto;">
							<span class="imob-card-date" style="display: inline-flex; align-items: center; gap: 6px; color: var(--text-light); font-size: 0.85rem; font-weight: 500;">
								<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">calendar_today</span>
								<?php echo get_the_date( 'j \d\e F \d\e Y' ); ?>
							</span>
							
							<?php if ( 'yes' === $settings['show_read_more'] ) : ?>
								<a href="<?php the_permalink(); ?>" class="imob-read-more-link" style="color: var(--accent-color); font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
									<?php echo esc_html( $settings['read_more_text'] ); ?>
									<span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
								</a>
							<?php endif; ?>
						</div>
					</div>

				</article>
				<?php
			}

			echo '</div>';

			// Paginação Numérica se habilitada
			if ( 'yes' === $settings['show_pagination'] && $query->max_num_pages > 1 ) {
				echo '<div class="imob-pagination" style="margin-top: 40px; display: flex; justify-content: center; gap: 8px;">';
				echo paginate_links( [
					'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'format'    => '?paged=%#%',
					'current'   => max( 1, $paged ),
					'total'     => $query->max_num_pages,
					'prev_text' => '<span class="material-symbols-outlined">chevron_left</span>',
					'next_text' => '<span class="material-symbols-outlined">chevron_right</span>',
					'type'      => 'plain',
				] );
				echo '</div>';
			}

			wp_reset_postdata();
		} else {
			echo '<p style="text-align: center; color: #888; padding: 30px;">' . __( 'Nenhum post encontrado.', 'imobiliaria-tema' ) . '</p>';
		}
	}
}
