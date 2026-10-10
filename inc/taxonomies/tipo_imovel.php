<?php
/**
 * Register Taxonomy: Tipo de Imóvel
 */

function imob_register_tax_tipo_imovel() {
	$labels = array(
		'name'                       => _x( 'Tipos de Imóvel', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Tipo de Imóvel', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Tipos', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todos os Tipos', 'imobiliaria-tema' ),
		'parent_item'                => __( 'Tipo Pai', 'imobiliaria-tema' ),
		'parent_item_colon'          => __( 'Tipo Pai:', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Novo Tipo', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Novo Tipo', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Tipo', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Tipo', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Tipo', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe os tipos com vírgulas', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover tipos', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolha entre os mais usados', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Tipos Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Tipos', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Não encontrado', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhum tipo', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de tipos', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de tipos', 'imobiliaria-tema' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
		'rewrite'                    => array( 'slug' => 'tipo' ),
	);
	register_taxonomy( 'tipo_imovel', array( 'imovel', 'empreendimento' ), $args );
}
add_action( 'init', 'imob_register_tax_tipo_imovel', 0 );
