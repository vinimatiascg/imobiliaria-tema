<?php
/**
 * Elementor Widget: Carrossel de Construtoras
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_construtoras extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_construtoras';
	}

	public function get_title() {
		return __( 'Carrossel de Construtoras', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-carousel';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	protected function register_controls() {
		// SEÇÃO DE CONTEÚDO
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Conteúdo do Carrossel', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => __( 'Título', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Principais Construtoras Parceiras', 'imobiliaria-tema' ),
			]
		);

		$this->add_control(
			'subtitle',
			[
				'label'   => __( 'Subtítulo', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Trabalhamos com as marcas mais sólidas e reconhecidas do mercado imobiliário', 'imobiliaria-tema' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'   => __( 'Origem das Construtoras', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto'   => __( 'Automático (Carregar todas cadastradas)', 'imobiliaria-tema' ),
					'manual' => __( 'Manual (Adicionar Logos & Links)', 'imobiliaria-tema' ),
				],
			]
		);

		// Repeater para modo Manual
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'name',
			[
				'label'   => __( 'Nome da Construtora', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Construtora', 'imobiliaria-tema' ),
			]
		);
		$repeater->add_control(
			'logo',
			[
				'label'   => __( 'Logo da Construtora', 'imobiliaria-tema' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'link',
			[
				'label'         => __( 'Link para a Página', 'imobiliaria-tema' ),
				'type'          => \Elementor\Controls_Manager::URL,
				'placeholder'   => __( 'https://seusite.com/construtora/nome', 'imobiliaria-tema' ),
				'show_external' => true,
				'default'       => [ 'url' => '' ],
			]
		);

		$this->add_control(
			'manual_items',
			[
				'label'       => __( 'Lista de Construtoras', 'imobiliaria-tema' ),
				'type'        => \Elementor\Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'condition'   => [ 'source' => 'manual' ],
				'title_field' => '{{{ name }}}',
			]
		);

		$this->end_controls_section();

		// SEÇÃO DE ESTILOS & COMPORTAMENTO
		$this->start_controls_section(
			'settings_section',
			[
				'label' => __( 'Configurações do Carrossel', 'imobiliaria-tema' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay Automático', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'grayscale',
			[
				'label'        => __( 'Logos em Preto e Branco (Colorido no Hover)', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$carousel_id = 'imob-carousel-' . $this->get_id();

		// Coletar itens (Automático ou Manual)
		$items = [];

		if ( 'auto' === $settings['source'] ) {
			$construtoras = get_posts( [
				'post_type'      => 'construtora',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'post_status'    => 'publish',
			] );

			foreach ( $construtoras as $c ) {
				$logo_url = has_post_thumbnail( $c->ID ) ? get_the_post_thumbnail_url( $c->ID, 'medium' ) : '';
				$items[]  = [
					'name' => $c->post_title,
					'logo' => $logo_url,
					'url'  => get_permalink( $c->ID ),
				];
			}
		} else {
			if ( ! empty( $settings['manual_items'] ) ) {
				foreach ( $settings['manual_items'] as $item ) {
					$items[] = [
						'name' => ! empty( $item['name'] ) ? $item['name'] : '',
						'logo' => ! empty( $item['logo']['url'] ) ? $item['logo']['url'] : '',
						'url'  => ! empty( $item['link']['url'] ) ? $item['link']['url'] : '',
					];
				}
			}
		}

		if ( empty( $items ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div style="padding: 30px; text-align: center; background: #fff; border: 1px dashed #ccc; border-radius: 8px;">';
				echo '<strong>Carrossel de Construtoras:</strong> Nenhuma construtora encontrada. Cadastre construtoras no menu Construtoras ou use a opção Manual.';
				echo '</div>';
			}
			return;
		}

		$is_grayscale = 'yes' === $settings['grayscale'];
		$is_autoplay  = 'yes' === $settings['autoplay'];
		?>
		<div class="imob-construtoras-carousel-widget" id="<?php echo esc_attr( $carousel_id ); ?>" style="background: #ffffff; padding: 40px 30px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 20px rgba(0,0,0,0.04); position: relative;">
			
			<?php if ( ! empty( $settings['title'] ) || ! empty( $settings['subtitle'] ) ) : ?>
				<div style="text-align: center; margin-bottom: 30px;">
					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h3 style="font-size: 1.6rem; color: var(--primary-color); font-weight: 800; margin: 0 0 8px;"><?php echo esc_html( $settings['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
						<p style="color: var(--text-light); font-size: 0.95rem; margin: 0; max-width: 650px; margin-inline: auto;"><?php echo esc_html( $settings['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<!-- CONTAINER DO CARROSSEL COM CONTROLES -->
			<div style="position: relative; display: flex; align-items: center;">
				<!-- BOTÃO PREV -->
				<button type="button" class="imob-carousel-nav btn-prev" aria-label="Anterior" style="position: absolute; left: -15px; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: #ffffff; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--primary-color); transition: all 0.2s ease;">
					<span class="material-symbols-outlined" style="font-size: 22px;">chevron_left</span>
				</button>

				<!-- TRILHO DO CARROSSEL -->
				<div class="imob-carousel-track" style="display: flex; gap: 24px; overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory; padding: 15px 5px; width: 100%; -ms-overflow-style: none; scrollbar-width: none;">
					<?php foreach ( $items as $const ) : ?>
						<div class="imob-carousel-item" style="flex: 0 0 calc(20% - 20px); min-width: 170px; scroll-snap-align: start; display: flex; align-items: center; justify-content: center;">
							<?php if ( ! empty( $const['url'] ) ) : ?>
								<a href="<?php echo esc_url( $const['url'] ); ?>" title="<?php echo esc_attr( $const['name'] ); ?>" class="imob-const-card-link" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 110px; padding: 15px; border-radius: 10px; background: #fafafa; border: 1px solid #eef2f6; text-decoration: none; transition: all 0.3s ease;">
							<?php else : ?>
								<div class="imob-const-card-link" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 110px; padding: 15px; border-radius: 10px; background: #fafafa; border: 1px solid #eef2f6; transition: all 0.3s ease;">
							<?php endif; ?>

								<?php if ( ! empty( $const['logo'] ) ) : ?>
									<img src="<?php echo esc_url( $const['logo'] ); ?>" alt="<?php echo esc_attr( $const['name'] ); ?>" class="imob-const-logo-img" style="max-height: 60px; max-width: 130px; object-fit: contain; <?php echo $is_grayscale ? 'filter: grayscale(100%); opacity: 0.6;' : ''; ?> transition: all 0.3s ease;">
								<?php else : ?>
									<span class="material-symbols-outlined" style="font-size: 32px; color: var(--accent-color); margin-bottom: 4px;">apartment</span>
									<span style="font-size: 0.85rem; font-weight: 700; color: var(--primary-color); text-align: center; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html( $const['name'] ); ?></span>
								<?php endif; ?>

							<?php if ( ! empty( $const['url'] ) ) : ?>
								</a>
							<?php else : ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- BOTÃO NEXT -->
				<button type="button" class="imob-carousel-nav btn-next" aria-label="Próximo" style="position: absolute; right: -15px; z-index: 10; width: 40px; height: 40px; border-radius: 50%; background: #ffffff; border: 1px solid var(--border-color); box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--primary-color); transition: all 0.2s ease;">
					<span class="material-symbols-outlined" style="font-size: 22px;">chevron_right</span>
				</button>
			</div>

		</div>

		<style>
			#<?php echo esc_attr( $carousel_id ); ?> .imob-carousel-track::-webkit-scrollbar { display: none; }
			#<?php echo esc_attr( $carousel_id ); ?> .imob-const-card-link:hover {
				background: #ffffff !important;
				border-color: var(--accent-color) !important;
				box-shadow: 0 8px 20px rgba(178, 145, 90, 0.15) !important;
				transform: translateY(-4px);
			}
			#<?php echo esc_attr( $carousel_id ); ?> .imob-const-card-link:hover .imob-const-logo-img {
				filter: none !important;
				opacity: 1 !important;
				transform: scale(1.05);
			}
			#<?php echo esc_attr( $carousel_id ); ?> .imob-carousel-nav:hover {
				background: var(--accent-color) !important;
				color: #ffffff !important;
				border-color: var(--accent-color) !important;
			}
			@media (max-width: 992px) {
				#<?php echo esc_attr( $carousel_id ); ?> .imob-carousel-item { flex: 0 0 calc(33.333% - 16px) !important; }
			}
			@media (max-width: 600px) {
				#<?php echo esc_attr( $carousel_id ); ?> .imob-carousel-item { flex: 0 0 calc(50% - 12px) !important; }
				#<?php echo esc_attr( $carousel_id ); ?> { padding: 25px 15px !important; }
			}
		</style>

		<script>
		(function(){
			var wrapper = document.getElementById('<?php echo esc_js( $carousel_id ); ?>');
			if (!wrapper) return;
			var track = wrapper.querySelector('.imob-carousel-track');
			var btnPrev = wrapper.querySelector('.btn-prev');
			var btnNext = wrapper.querySelector('.btn-next');
			if (!track) return;

			var scrollAmount = 240;

			if (btnPrev) {
				btnPrev.addEventListener('click', function(){
					track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
				});
			}
			if (btnNext) {
				btnNext.addEventListener('click', function(){
					track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
				});
			}

			<?php if ( $is_autoplay ) : ?>
			var autoInterval;
			function startAutoScroll() {
				autoInterval = setInterval(function(){
					if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 10) {
						track.scrollTo({ left: 0, behavior: 'smooth' });
					} else {
						track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
					}
				}, 3500);
			}
			function stopAutoScroll() {
				clearInterval(autoInterval);
			}
			wrapper.addEventListener('mouseenter', stopAutoScroll);
			wrapper.addEventListener('mouseleave', startAutoScroll);
			startAutoScroll();
			<?php endif; ?>
		})();
		</script>
		<?php
	}
}
