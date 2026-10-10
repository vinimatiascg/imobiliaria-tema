<?php
/**
 * Register Custom Post Type: Empreendimento
 */

function imob_register_cpt_empreendimento() {
	$labels = array(
		'name'                  => _x( 'Empreendimentos', 'Post Type General Name', 'imobiliaria-tema' ),
		'singular_name'         => _x( 'Empreendimento', 'Post Type Singular Name', 'imobiliaria-tema' ),
		'menu_name'             => __( 'Empreendimentos', 'imobiliaria-tema' ),
		'name_admin_bar'        => __( 'Empreendimento', 'imobiliaria-tema' ),
		'archives'              => __( 'Arquivos de Empreendimentos', 'imobiliaria-tema' ),
		'all_items'             => __( 'Todos os Empreendimentos', 'imobiliaria-tema' ),
		'add_new_item'          => __( 'Adicionar Novo Empreendimento', 'imobiliaria-tema' ),
		'add_new'               => __( 'Adicionar Novo', 'imobiliaria-tema' ),
		'new_item'              => __( 'Novo Empreendimento', 'imobiliaria-tema' ),
		'edit_item'             => __( 'Editar Empreendimento', 'imobiliaria-tema' ),
		'update_item'           => __( 'Atualizar Empreendimento', 'imobiliaria-tema' ),
		'view_item'             => __( 'Ver Empreendimento', 'imobiliaria-tema' ),
		'search_items'          => __( 'Buscar Empreendimento', 'imobiliaria-tema' ),
	);
	$args = array(
		'label'                 => __( 'Empreendimento', 'imobiliaria-tema' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
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
		'rewrite'               => array( 'slug' => 'empreendimentos' ),
		'taxonomies'            => array( 'caracteristica', 'localidade', 'estagio_obra', 'tipo_imovel' ),
	);
	register_post_type( 'empreendimento', $args );
}
add_action( 'init', 'imob_register_cpt_empreendimento', 0 );
