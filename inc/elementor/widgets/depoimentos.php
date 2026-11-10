<?php
/**
 * Elementor Widget: Depoimentos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_depoimentos extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_depoimentos';
	}

	public function get_title() {
		return __( 'Depoimentos', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-testimonial';
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
			'titulo',
			[
				'label' => __( 'Título', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Sem burocracia', 'imobiliaria-tema' ),
			]
		);

		$repeater->add_control(
			'texto',
			[
				'label' => __( 'Texto do Depoimento', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
			]
		);

		$repeater->add_control(
			'nome',
			[
				'label' => __( 'Nome', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$repeater->add_control(
			'cidade',
			[
				'label' => __( 'Cidade', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'items',
			[
				'label' => __( 'Depoimentos', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[ 'titulo' => 'Sem burocracia', 'nome' => 'Nick Costa' ],
				],
			]
		);

		$this->add_control(
			'show_whatsapp',
			[
				'label'        => __( 'Exibir botão do WhatsApp', 'imobiliaria-tema' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'imobiliaria-tema' ),
				'label_off'    => __( 'Não', 'imobiliaria-tema' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'whatsapp_label',
			[
				'label'     => __( 'Texto do botão', 'imobiliaria-tema' ),
				'type'      => \Elementor\Controls_Manager::TEXT,
				'default'   => __( 'Realize o seu sonho', 'imobiliaria-tema' ),
				'condition' => [ 'show_whatsapp' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( empty( $settings['items'] ) ) return;
		?>
		<div class="imob-depoimentos-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
			<?php foreach ( $settings['items'] as $item ) : ?>
				<div class="imob-depoimento-card" style="background: var(--accent-color); color: #fff; padding: 30px; border-radius: 10px; position: relative;">
					<span class="material-symbols-outlined" style="font-size: 40px; opacity: 0.3; margin-bottom: 10px; display: block;">format_quote</span>
					<h4 style="margin: 0 0 15px; font-size: 1.2rem;"><?php echo esc_html( $item['titulo'] ); ?></h4>
					<p style="font-size: 0.9rem; margin-bottom: 20px;"><?php echo esc_html( $item['texto'] ); ?></p>
					<div style="display: flex; align-items: center; gap: 10px;">
						<div style="width: 40px; height: 40px; background: #fff; border-radius: 50%; color: var(--accent-color); display: flex; align-items: center; justify-content: center; font-weight: bold;">
							<?php echo esc_html( substr( $item['nome'], 0, 1 ) ); ?>
						</div>
						<div>
							<div style="font-weight: 700;"><?php echo esc_html( $item['nome'] ); ?></div>
							<div style="font-size: 0.8rem;"><?php echo esc_html( $item['cidade'] ); ?></div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		if ( 'yes' === ( $settings['show_whatsapp'] ?? '' ) && function_exists( 'imob_home_whatsapp_url' ) ) :
			$label = ! empty( $settings['whatsapp_label'] ) ? $settings['whatsapp_label'] : __( 'Realize o seu sonho', 'imobiliaria-tema' );
			?>
			<div class="imob-depoimentos-cta" style="display: flex; justify-content: center; margin-top: 36px;">
				<a href="<?php echo esc_url( imob_home_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer" class="imob-btn-whatsapp" style="display: inline-flex; align-items: center; gap: 8px; background: var(--secondary-color, #2F80ED); color: #fff; padding: 14px 28px; border-radius: 8px; font-weight: 700; text-decoration: none;">
					<?php echo imob_home_whatsapp_icon(); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG fixo. ?>
					<?php echo esc_html( $label ); ?>
				</a>
			</div>
			<?php
		endif;
	}
}
