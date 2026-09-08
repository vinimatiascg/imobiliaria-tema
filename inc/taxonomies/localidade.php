<?php
/**
 * Register Taxonomy: Localidade
 */

function imob_register_tax_localidade() {
	$labels = array(
		'name'                       => _x( 'Localidades', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Localidade', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Localidades', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todas as Localidades', 'imobiliaria-tema' ),
		'parent_item'                => __( 'Localidade Pai (Estado/Cidade)', 'imobiliaria-tema' ),
		'parent_item_colon'          => __( 'Localidade Pai:', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Nova Localidade', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Nova Localidade', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Localidade', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Localidade', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Localidade', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe as localidades com vírgulas', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover localidades', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolha entre as mais usadas', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Localidades Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Localidades', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Não encontrada', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhuma localidade', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de localidades', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de localidades', 'imobiliaria-tema' ),
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
		'rewrite'                    => array( 'slug' => 'localidade', 'hierarchical' => true ),
	);
	register_taxonomy( 'localidade', array( 'imovel', 'empreendimento' ), $args );
}
add_action( 'init', 'imob_register_tax_localidade', 0 );
