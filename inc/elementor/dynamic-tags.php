<?php
/**
 * Elementor Dynamic Tags for Imobiliária Tema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Imob_Elementor_Dynamic_Tags {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_tags' ] );
	}

	public function register_tags( $dynamic_tags ) {
		$dynamic_tags->register( new Imob_Tag_Preco_Venda() );
		$dynamic_tags->register( new Imob_Tag_Area_Privativa() );
		$dynamic_tags->register( new Imob_Tag_Quartos() );
	}
}

class Imob_Tag_Preco_Venda extends \Elementor\Core\DynamicTags\Tag {
	public function get_name() { return 'imob-preco-venda'; }
	public function get_title() { return __( 'Imóvel: Preço de Venda', 'imobiliaria-tema' ); }
	public function get_group() { return 'post'; }
	public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }
	protected function render() {
		$preco = get_post_meta( get_the_ID(), '_imob_preco_venda', true );
		if ( $preco ) {
			echo 'R$ ' . number_format( (float) $preco, 2, ',', '.' );
		}
	}
}

class Imob_Tag_Area_Privativa extends \Elementor\Core\DynamicTags\Tag {
	public function get_name() { return 'imob-area-privativa'; }
	public function get_title() { return __( 'Imóvel: Área Privativa', 'imobiliaria-tema' ); }
	public function get_group() { return 'post'; }
	public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }
	protected function render() {
		$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
		if ( $area ) {
			echo $area . ' m²';
		}
	}
}

class Imob_Tag_Quartos extends \Elementor\Core\DynamicTags\Tag {
	public function get_name() { return 'imob-quartos'; }
	public function get_title() { return __( 'Imóvel: Quartos', 'imobiliaria-tema' ); }
	public function get_group() { return 'post'; }
	public function get_categories() { return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ]; }
	protected function render() {
		echo get_post_meta( get_the_ID(), '_imob_quartos', true );
	}
}

add_action( 'init', function() {
	if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
		Imob_Elementor_Dynamic_Tags::instance();
	}
} );
