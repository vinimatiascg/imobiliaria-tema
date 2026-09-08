<?php
/**
 * Elementor Widget: Números da Empresa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_numeros extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_numeros';
	}

	public function get_title() {
		return __( 'Números da Empresa', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-counter';
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

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'icon',
			[
				'label' => __( 'Ícone (Material Symbols)', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => 'home',
			]
		);
		$repeater->add_control(
			'numero',
			[
				'label' => __( 'Número', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '100+',
			]
		);
		$repeater->add_control(
			'texto',
			[
				'label' => __( 'Texto Secundário', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'default' => 'Imóveis vendidos',
			]
		);

		$this->add_control(
			'items',
			[
				'label' => __( 'Itens', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[ 'icon' => 'home', 'numero' => '100+', 'texto' => 'Imóveis vendidos' ],
					[ 'icon' => 'calendar_month', 'numero' => '3 meses', 'texto' => 'Tempo médio de vendas' ],
					[ 'icon' => 'publish', 'numero' => '200+', 'texto' => 'Imóveis publicados' ],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( empty( $settings['items'] ) ) return;
		?>
		<div class="imob-numeros-wrapper" style="background: #fff; padding: 40px; border-radius: 10px; display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
			<?php foreach ( $settings['items'] as $index => $item ) : ?>
				<div class="imob-numero-item" style="display: flex; align-items: center; gap: 20px; min-width: 200px; <?php echo ($index > 0) ? 'border-left: 1px solid var(--border-color); padding-left: 40px;' : ''; ?>">
					<div class="imob-numero-icon" style="width: 60px; height: 60px; background: var(--accent-color); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
						<span class="material-symbols-outlined" style="font-size: 30px;"><?php echo esc_html( $item['icon'] ); ?></span>
					</div>
					<div>
						<div style="font-size: 1.8rem; font-weight: 700; color: var(--primary-color); line-height: 1; margin-bottom: 5px;"><?php echo esc_html( $item['numero'] ); ?></div>
						<div style="font-size: 0.9rem; color: var(--text-light); line-height: 1.2;"><?php echo esc_html( $item['texto'] ); ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
