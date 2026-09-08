<?php
/**
 * Register Custom Post Type: Proprietário
 */

function imob_register_cpt_proprietario() {
	$labels = array(
		'name'                  => _x( 'Proprietários', 'Post Type General Name', 'imobiliaria-tema' ),
		'singular_name'         => _x( 'Proprietário', 'Post Type Singular Name', 'imobiliaria-tema' ),
		'menu_name'             => __( 'Proprietários', 'imobiliaria-tema' ),
		'name_admin_bar'        => __( 'Proprietário', 'imobiliaria-tema' ),
		'archives'              => __( 'Arquivos de Proprietários', 'imobiliaria-tema' ),
		'all_items'             => __( 'Todos os Proprietários', 'imobiliaria-tema' ),
		'add_new_item'          => __( 'Adicionar Novo Proprietário', 'imobiliaria-tema' ),
		'add_new'               => __( 'Adicionar Novo', 'imobiliaria-tema' ),
		'new_item'              => __( 'Novo Proprietário', 'imobiliaria-tema' ),
		'edit_item'             => __( 'Editar Proprietário', 'imobiliaria-tema' ),
		'update_item'           => __( 'Atualizar Proprietário', 'imobiliaria-tema' ),
		'view_item'             => __( 'Ver Proprietário', 'imobiliaria-tema' ),
		'search_items'          => __( 'Buscar Proprietário', 'imobiliaria-tema' ),
	);
	$args = array(
		'label'                 => __( 'Proprietário', 'imobiliaria-tema' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'custom-fields' ),
		'public'                => false, // Internal only
		'show_ui'               => true,
		'show_in_menu'          => 'edit.php?post_type=imovel', // Nested under Imóveis
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
		'show_in_rest'          => false, // No REST API access
	);
	register_post_type( 'proprietario', $args );
}
add_action( 'init', 'imob_register_cpt_proprietario', 0 );
