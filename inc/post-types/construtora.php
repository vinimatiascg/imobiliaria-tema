<?php
/**
 * Register Custom Post Type: Construtora
 */

function imob_register_cpt_construtora() {
	$labels = array(
		'name'                  => _x( 'Construtoras', 'Post Type General Name', 'imobiliaria-tema' ),
		'singular_name'         => _x( 'Construtora', 'Post Type Singular Name', 'imobiliaria-tema' ),
		'menu_name'             => __( 'Construtoras', 'imobiliaria-tema' ),
		'name_admin_bar'        => __( 'Construtora', 'imobiliaria-tema' ),
		'archives'              => __( 'Arquivos de Construtoras', 'imobiliaria-tema' ),
		'all_items'             => __( 'Todas as Construtoras', 'imobiliaria-tema' ),
		'add_new_item'          => __( 'Adicionar Nova Construtora', 'imobiliaria-tema' ),
		'add_new'               => __( 'Adicionar Nova', 'imobiliaria-tema' ),
		'new_item'              => __( 'Nova Construtora', 'imobiliaria-tema' ),
		'edit_item'             => __( 'Editar Construtora', 'imobiliaria-tema' ),
		'update_item'           => __( 'Atualizar Construtora', 'imobiliaria-tema' ),
		'view_item'             => __( 'Ver Construtora', 'imobiliaria-tema' ),
		'search_items'          => __( 'Buscar Construtora', 'imobiliaria-tema' ),
	);
	$args = array(
		'label'                 => __( 'Construtora', 'imobiliaria-tema' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => 'edit.php?post_type=imovel', // Nested under Imóveis
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
		'rewrite'               => array( 'slug' => 'construtoras' ),
	);
	register_post_type( 'construtora', $args );
}
add_action( 'init', 'imob_register_cpt_construtora', 0 );
