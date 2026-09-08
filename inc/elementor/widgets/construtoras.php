<?php
/**
 * Elementor Widget: Construtoras
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_construtoras extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_construtoras';
	}

	public function get_title() {
		return __( 'Construtoras', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-images-carousel';
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
			'title',
			[
				'label' => __( 'Título', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Trabalhamos com as principais construtoras do mercado:', 'imobiliaria-tema' ),
			]
		);

		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'logo',
			[
				'label' => __( 'Logo da Construtora', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);

		$this->add_control(
			'logos',
			[
				'label' => __( 'Construtoras', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="imob-construtoras" style="display: flex; align-items: center; justify-content: space-between; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); gap: 40px;">
			<div style="flex: 0 0 300px;">
				<h3 style="margin: 0; font-size: 1.2rem; font-weight: 600; line-height: 1.4; color: var(--primary-color);"><?php echo esc_html( $settings['title'] ); ?></h3>
			</div>
			<div style="flex: 1; display: flex; gap: 20px; overflow-x: auto; padding-bottom: 10px; align-items: center;">
				<?php if ( ! empty( $settings['logos'] ) ) : ?>
					<?php foreach ( $settings['logos'] as $item ) : ?>
						<?php if ( ! empty( $item['logo']['url'] ) ) : ?>
							<img src="<?php echo esc_url( $item['logo']['url'] ); ?>" alt="Construtora" style="height: 60px; object-fit: contain; filter: grayscale(100%); opacity: 0.6; transition: all 0.3s;" onmouseover="this.style.filter='none'; this.style.opacity='1'" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.6'">
						<?php else: ?>
							<div style="width: 120px; height: 120px; background: #f0f0f0; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 8px;">Logo</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<!-- Placeholders for design mockup -->
					<div style="width: 120px; height: 120px; background: #f9f9f9; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 8px;"></div>
					<div style="width: 120px; height: 120px; background: #f9f9f9; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 8px;"></div>
					<div style="width: 120px; height: 120px; background: #f9f9f9; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 8px;"></div>
					<div style="width: 120px; height: 120px; background: #f9f9f9; border: 1px dashed #ddd; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 8px;"></div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
