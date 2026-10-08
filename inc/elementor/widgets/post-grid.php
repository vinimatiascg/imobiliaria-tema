<?php
/**
 * Elementor Widget: Post Grid
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/blog-grid.php';

class Imob_Elementor_Widget_post_grid extends Imob_Elementor_Widget_blog_grid {

	public function get_name() {
		return 'imob_post_grid';
	}

	public function get_title() {
		return __( 'Grid de Posts', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	public function get_keywords() {
		return [ 'posts', 'grid', 'blog', 'noticias', 'artigos', 'imoveis', 'publicacoes' ];
	}
}
