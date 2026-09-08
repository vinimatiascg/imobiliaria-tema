<?php
/**
 * Elementor Widget: Busca Avançada
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_imovel_search extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_imovel_search';
	}

	public function get_title() {
		return __( 'Busca de Imóveis', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Opções de Busca', 'imobiliaria-tema' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_tipo',
			[
				'label' => __( 'Mostrar Filtro de Tipo', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_localidade',
			[
				'label' => __( 'Mostrar Filtro de Localidade', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="imob-advanced-search-wrapper">
			<form role="search" method="get" class="imob-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="hidden" name="post_type" value="imovel" />

				<div class="imob-search-tabs">
					<label class="imob-search-tab">
						<input type="radio" name="finalidade" value="aluguel" <?php checked( isset($_GET['finalidade']) && $_GET['finalidade'] == 'aluguel' ); ?>>
						<span>Aluguel</span>
					</label>
					<label class="imob-search-tab">
						<input type="radio" name="finalidade" value="venda" <?php checked( !isset($_GET['finalidade']) || $_GET['finalidade'] == 'venda' ); ?>>
						<span>Venda</span>
					</label>
				</div>

				<div class="imob-search-fields">
					<div class="imob-search-field imob-search-keyword">
						<span class="material-symbols-outlined search-icon">search</span>
						<input type="text" name="s" placeholder="<?php _e( 'O que você procura?', 'imobiliaria-tema' ); ?>" value="<?php echo get_search_query(); ?>" />
					</div>

					<div class="imob-search-field">
						<?php
						wp_dropdown_categories( array(
							'show_option_all' => __( 'Cidade', 'imobiliaria-tema' ),
							'taxonomy'        => 'localidade',
							'name'            => 'cidade',
							'orderby'         => 'name',
							'value_field'     => 'slug',
							'selected'        => isset( $_GET['cidade'] ) ? $_GET['cidade'] : '',
							'hierarchical'    => true,
							'class'           => 'imob-select',
						) );
						?>
					</div>

					<div class="imob-search-field">
						<?php
						wp_dropdown_categories( array(
							'show_option_all' => __( 'Bairro', 'imobiliaria-tema' ),
							'taxonomy'        => 'localidade',
							'name'            => 'bairro',
							'orderby'         => 'name',
							'value_field'     => 'slug',
							'selected'        => isset( $_GET['bairro'] ) ? $_GET['bairro'] : '',
							'hierarchical'    => true,
							'class'           => 'imob-select',
						) );
						?>
					</div>

					<?php if ( 'yes' === $settings['show_tipo'] ) : ?>
					<div class="imob-search-field">
						<?php
						wp_dropdown_categories( array(
							'show_option_all' => __( 'Modalidade', 'imobiliaria-tema' ),
							'taxonomy'        => 'tipo_imovel',
							'name'            => 'tipo',
							'orderby'         => 'name',
							'value_field'     => 'slug',
							'selected'        => isset( $_GET['tipo'] ) ? $_GET['tipo'] : '',
							'hierarchical'    => true,
							'class'           => 'imob-select',
						) );
						?>
					</div>
					<?php endif; ?>

					<div class="imob-search-submit">
						<button type="submit" class="imob-btn-primary" style="width:100%; border:none; border-radius: 20px; padding: 15px 30px; cursor: pointer;"><?php _e( 'Mostrar resultados', 'imobiliaria-tema' ); ?></button>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}
