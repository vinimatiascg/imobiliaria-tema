<?php
/**
 * Elementor Widget: Imóvel Grid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_imovel_grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_imovel_grid';
	}

	public function get_title() {
		return __( 'Grid de Imóveis', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Conteúdo', 'imobiliaria-tema' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Quantidade de Imóveis', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
			]
		);

		$this->add_control(
			'destaque_only',
			[
				'label' => __( 'Apenas Destaques', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Sim', 'imobiliaria-tema' ),
				'label_off' => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		// Get all term names for tipo_imovel
		$tipos = get_terms([ 'taxonomy' => 'tipo_imovel', 'hide_empty' => false ]);
		$tipo_options = [ '' => __( 'Todos', 'imobiliaria-tema' ) ];
		if ( ! is_wp_error( $tipos ) ) {
			foreach ( $tipos as $term ) {
				$tipo_options[ $term->slug ] = $term->name;
			}
		}

		$this->add_control(
			'filter_tipo',
			[
				'label' => __( 'Filtrar por Tipo', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $tipo_options,
				'default' => '',
			]
		);

		// Get all term names for localidade
		$localidades = get_terms([ 'taxonomy' => 'localidade', 'hide_empty' => false ]);
		$localidade_options = [ '' => __( 'Todas', 'imobiliaria-tema' ) ];
		if ( ! is_wp_error( $localidades ) ) {
			foreach ( $localidades as $term ) {
				$localidade_options[ $term->slug ] = $term->name;
			}
		}

		$this->add_control(
			'filter_localidade',
			[
				'label' => __( 'Filtrar por Localidade', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $localidade_options,
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$args = array(
			'post_type' => 'imovel',
			'posts_per_page' => $settings['posts_per_page'],
		);

		if ( 'yes' === $settings['destaque_only'] ) {
			$args['meta_query'] = array(
				array(
					'key' => '_imob_destaque',
					'value' => '1',
					'compare' => '='
				)
			);
		}

		$tax_query = array();
		if ( ! empty( $settings['filter_tipo'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'tipo_imovel',
				'field'    => 'slug',
				'terms'    => $settings['filter_tipo'],
			);
		}
		if ( ! empty( $settings['filter_localidade'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'localidade',
				'field'    => 'slug',
				'terms'    => $settings['filter_localidade'],
			);
		}
		
		if ( ! empty( $tax_query ) ) {
			$tax_query['relation'] = 'AND';
			$args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			echo '<div class="imob-elementor-imovel-grid">';
			while ( $query->have_posts() ) {
				$query->the_post();
				?>
				<?php
				$preco = get_post_meta( get_the_ID(), '_imob_preco_venda', true );
				$price_html = imob_get_formatted_price( get_the_ID(), false );
				$quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
				$banheiros = get_post_meta( get_the_ID(), '_imob_banheiros', true );
				$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
				$empreendimento = get_post_meta( get_the_ID(), '_imob_empreendimento', true ); // or similar field for residential name

				$tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel', array( 'fields' => 'names' ) );
				$status_list = wp_get_post_terms( get_the_ID(), 'status_imovel', array( 'fields' => 'names' ) );
				$localidades = wp_get_post_terms( get_the_ID(), 'localidade', array( 'fields' => 'names' ) );

				$tipo = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : 'IMÓVEL';
				$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : 'VENDA';
				$bairro = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : 'Bairro não informado';
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card' ); ?>>
					<div class="imob-card-thumb">
						<div class="imob-card-badges">
							<span class="badge-tipo"><?php echo esc_html( imob_strtoupper( $tipo ) ); ?></span>
							<span class="badge-finalidade"><?php echo esc_html( imob_strtoupper( $finalidade ) ); ?></span>
							<?php 
							$badge_emp_html = imob_render_empreendimento_badge( get_the_ID() );
							if ( ! empty( $badge_emp_html ) ) {
								echo $badge_emp_html;
							}
							?>
						</div>
						<?php if ( $price_html ) : ?>
							<div class="imob-card-price">
								<?php echo $price_html; ?>
							</div>
						<?php endif; ?>
						<a href="<?php the_permalink(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'medium_large' );
							} else {
								echo '<div class="imob-card-placeholder"></div>';
							}
							?>
						</a>
					</div>
					<div class="imob-card-content">
						<div class="imob-card-info-top">
							<span class="info-bairro"><span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; color: var(--accent-color);">location_on</span> Bairro: <?php echo esc_html( $bairro ); ?></span>
						</div>
						<?php the_title( '<h3 class="imob-card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
						
						<?php if ( $empreendimento ) : ?>
							<p class="imob-card-residencial">Residencial: <strong><?php echo esc_html( $empreendimento ); ?></strong></p>
						<?php else : ?>
							<p class="imob-card-residencial"><strong>Inspire Itararé</strong></p>
						<?php endif; ?>

						<div class="imob-card-features">
							<?php if ( $quartos ) : ?>
								<span title="Quartos"><span class="material-symbols-outlined">bed</span> <?php echo esc_html( $quartos ); ?></span>
							<?php endif; ?>
							<?php if ( $banheiros ) : ?>
								<span title="Banheiros"><span class="material-symbols-outlined">shower</span> <?php echo esc_html( $banheiros ); ?></span>
							<?php endif; ?>
							<?php if ( $area ) : ?>
								<span title="Área Privativa"><span class="material-symbols-outlined">crop</span> <?php echo esc_html( $area ); ?>m²</span>
							<?php endif; ?>
						</div>
						
						<div class="imob-card-footer">
							<span class="imob-card-date">Data do anúncio: <?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
						</div>
					</div>
				</article>
				<?php
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>' . __( 'Nenhum imóvel encontrado.', 'imobiliaria-tema' ) . '</p>';
		}
	}
}
