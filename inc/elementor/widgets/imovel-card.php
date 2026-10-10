<?php
/**
 * Elementor Widget: Card Individual de Imóvel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_imovel_card extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_imovel_card';
	}

	public function get_title() {
		return __( 'Card de Imóvel (Unitário)', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-single-post';
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

		// Get latest 50 properties to populate select
		$imoveis = get_posts([
			'post_type' => 'imovel',
			'posts_per_page' => 50,
			'post_status' => 'publish'
		]);
		$options = [ '' => __( 'Selecione um imóvel', 'imobiliaria-tema' ) ];
		foreach ( $imoveis as $imovel ) {
			$options[ $imovel->ID ] = $imovel->post_title;
		}

		$this->add_control(
			'imovel_id',
			[
				'label' => __( 'Selecionar Imóvel', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => '',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		
		if ( empty( $settings['imovel_id'] ) ) {
			echo '<p>' . __( 'Selecione um imóvel no painel.', 'imobiliaria-tema' ) . '</p>';
			return;
		}

		$post_id = $settings['imovel_id'];
		$post = get_post( $post_id );
		
		if ( ! $post || $post->post_type !== 'imovel' ) {
			echo '<p>' . __( 'Imóvel não encontrado.', 'imobiliaria-tema' ) . '</p>';
			return;
		}

		// Setup post data so we can use template tags
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$preco = get_post_meta( get_the_ID(), '_imob_preco_venda', true );
		$price_html = imob_get_formatted_price( get_the_ID(), false );
		$quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
		$banheiros = get_post_meta( get_the_ID(), '_imob_banheiros', true );
		$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
		$empreendimento = get_post_meta( get_the_ID(), '_imob_empreendimento', true );

		$tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel', array( 'fields' => 'names' ) );
		$status_list = wp_get_post_terms( get_the_ID(), 'status_imovel', array( 'fields' => 'names' ) );
		$localidades = wp_get_post_terms( get_the_ID(), 'localidade', array( 'fields' => 'names' ) );

		$tipo = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : 'IMÓVEL';
		$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : 'VENDA';
		$bairro = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : 'Bairro não informado';
		?>
		<div class="imob-elementor-imovel-card-widget">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card' ); ?> style="max-width: 400px; margin: 0 auto;">
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
		</div>
		<?php
		wp_reset_postdata();
	}
}
