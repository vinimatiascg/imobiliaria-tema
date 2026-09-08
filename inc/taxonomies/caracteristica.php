<?php
/**
 * Register Taxonomy: Característica
 */

function imob_register_tax_caracteristica() {
	$labels = array(
		'name'                       => _x( 'Características', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Característica', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Características', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todas as Características', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Nova Característica', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Nova Característica', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Característica', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Característica', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Característica', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe as características com vírgulas', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover características', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolha entre as mais usadas', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Características Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Características', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Não encontrada', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhuma característica', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de características', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de características', 'imobiliaria-tema' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false, // Tags style
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'show_in_rest'               => true,
		'rewrite'                    => array( 'slug' => 'caracteristica' ),
	);
	register_taxonomy( 'caracteristica', array( 'imovel', 'empreendimento' ), $args );
}
add_action( 'init', 'imob_register_tax_caracteristica', 0 );
