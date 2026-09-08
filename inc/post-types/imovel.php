<?php
/**
 * Register Custom Post Type: Imóvel
 */

function imob_register_cpt_imovel() {
	$labels = array(
		'name'                  => _x( 'Imóveis', 'Post Type General Name', 'imobiliaria-tema' ),
		'singular_name'         => _x( 'Imóvel', 'Post Type Singular Name', 'imobiliaria-tema' ),
		'menu_name'             => __( 'Imóveis', 'imobiliaria-tema' ),
		'name_admin_bar'        => __( 'Imóvel', 'imobiliaria-tema' ),
		'archives'              => __( 'Arquivos de Imóveis', 'imobiliaria-tema' ),
		'attributes'            => __( 'Atributos do Imóvel', 'imobiliaria-tema' ),
		'parent_item_colon'     => __( 'Imóvel Ascendente:', 'imobiliaria-tema' ),
		'all_items'             => __( 'Todos os Imóveis', 'imobiliaria-tema' ),
		'add_new_item'          => __( 'Adicionar Novo Imóvel', 'imobiliaria-tema' ),
		'add_new'               => __( 'Adicionar Novo', 'imobiliaria-tema' ),
		'new_item'              => __( 'Novo Imóvel', 'imobiliaria-tema' ),
		'edit_item'             => __( 'Editar Imóvel', 'imobiliaria-tema' ),
		'update_item'           => __( 'Atualizar Imóvel', 'imobiliaria-tema' ),
		'view_item'             => __( 'Ver Imóvel', 'imobiliaria-tema' ),
		'view_items'            => __( 'Ver Imóveis', 'imobiliaria-tema' ),
		'search_items'          => __( 'Buscar Imóvel', 'imobiliaria-tema' ),
		'not_found'             => __( 'Não encontrado', 'imobiliaria-tema' ),
		'not_found_in_trash'    => __( 'Não encontrado na lixeira', 'imobiliaria-tema' ),
		'featured_image'        => __( 'Imagem Destacada', 'imobiliaria-tema' ),
		'set_featured_image'    => __( 'Definir imagem destacada', 'imobiliaria-tema' ),
		'remove_featured_image' => __( 'Remover imagem destacada', 'imobiliaria-tema' ),
		'use_featured_image'    => __( 'Usar como imagem destacada', 'imobiliaria-tema' ),
		'insert_into_item'      => __( 'Inserir no imóvel', 'imobiliaria-tema' ),
		'uploaded_to_this_item' => __( 'Enviado para este imóvel', 'imobiliaria-tema' ),
		'items_list'            => __( 'Lista de imóveis', 'imobiliaria-tema' ),
		'items_list_navigation' => __( 'Navegação da lista de imóveis', 'imobiliaria-tema' ),
		'filter_items_list'     => __( 'Filtrar lista de imóveis', 'imobiliaria-tema' ),
	);
	$args = array(
		'label'                 => __( 'Imóvel', 'imobiliaria-tema' ),
		'description'           => __( 'Cadastro de Imóveis', 'imobiliaria-tema' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-admin-home',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true, // Essential for Block Editor and Elementor
		'rewrite'               => array( 'slug' => 'imoveis/%localidade%', 'with_front' => false ),
	);
	register_post_type( 'imovel', $args );
}
add_action( 'init', 'imob_register_cpt_imovel', 0 );

/**
 * Filtro para substituir %localidade% pelo slug do termo na URL
 */
function imob_imovel_post_type_link( $post_link, $post ) {
	if ( 'imovel' != $post->post_type ) {
		return $post_link;
	}
	
	if ( strpos( $post_link, '%localidade%' ) === false ) {
		return $post_link;
	}

	$terms = wp_get_object_terms( $post->ID, 'localidade' );
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) && is_object( $terms[0] ) ) {
		$taxonomy_slug = $terms[0]->slug;
	} else {
		$taxonomy_slug = 'geral'; // fallback
	}

	return str_replace( '%localidade%', $taxonomy_slug, $post_link );
}
add_filter( 'post_type_link', 'imob_imovel_post_type_link', 10, 2 );
