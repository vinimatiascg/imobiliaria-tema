<?php
/**
 * Elementor Integration: Register Widgets and Categories
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Imob_Elementor_Widgets_Manager {
	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}

	public function add_category( $elements_manager ) {
		$elements_manager->add_category(
			'imobiliaria-tema',
			[
				'title' => __( 'Imobiliária Tema Imóveis', 'imobiliaria-tema' ),
				'icon'  => 'fa fa-home',
			]
		);
		$elements_manager->add_category(
			'imobiliaria-secoes',
			[
				'title' => __( 'Imobiliária - Seções do site', 'imobiliaria-tema' ),
				'icon'  => 'fa fa-th-large',
			]
		);
	}

	public function register_widgets( $widgets_manager ) {
		$widgets = [
			'imovel-grid',
			'imovel-search',
			'imovel-card',
			'imovel-map',
			'depoimentos',
			'construtoras',
			'numeros',
			'blog-grid',
			'post-grid',
			'faq-accordion',
		];

		foreach ( $widgets as $widget ) {
			require_once IMOB_THEME_DIR . 'inc/elementor/widgets/' . $widget . '.php';
			$class_name = 'Imob_Elementor_Widget_' . str_replace( '-', '_', $widget );
			if ( class_exists( $class_name ) ) {
				$widgets_manager->register( new $class_name() );
			}
		}

		// Seções do layout Figma (home, Sobre, Avaliação...).
		require_once IMOB_THEME_DIR . 'inc/elementor/widgets/sections.php';
		foreach ( array( 'Hero', 'Process', 'Properties', 'Numbers', 'Lead', 'Blog', 'Testimonials', 'Split', 'Pillars', 'Checklist', 'Features', 'Divider', 'Cta' ) as $section ) {
			$class_name = 'Imob_Section_Widget_' . $section;
			$widgets_manager->register( new $class_name() );
		}
	}
}
add_action( 'init', function() {
	if ( did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) ) {
		Imob_Elementor_Widgets_Manager::instance();
	}
} );
