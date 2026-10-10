<?php
/**
 * Register Taxonomy: Status do Imóvel
 */

function imob_register_tax_status_imovel() {
	$labels = array(
		'name'                       => _x( 'Status', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Status', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Status', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todos os Status', 'imobiliaria-tema' ),
		'parent_item'                => __( 'Status Pai', 'imobiliaria-tema' ),
		'parent_item_colon'          => __( 'Status Pai:', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Novo Status', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Novo Status', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Status', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Status', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Status', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe os status com vírgulas', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover status', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolha entre os mais usados', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Status Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Status', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Não encontrado', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhum status', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de status', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de status', 'imobiliaria-tema' ),
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
		'rewrite'                    => array( 'slug' => 'status' ),
	);
	register_taxonomy( 'status_imovel', array( 'imovel' ), $args );
}
add_action( 'init', 'imob_register_tax_status_imovel', 0 );

/**
 * Add default terms on init if they don't exist
 */
function imob_insert_default_status() {
	$terms = array( 'Disponível', 'Reservado', 'Vendido', 'Alugado' );
	foreach ( $terms as $term ) {
		if ( ! term_exists( $term, 'status_imovel' ) ) {
			wp_insert_term( $term, 'status_imovel' );
		}
	}
}
add_action( 'init', 'imob_insert_default_status', 10 );
