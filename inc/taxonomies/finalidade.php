<?php
/**
 * Register Taxonomy: Finalidade
 */

function imob_register_tax_finalidade() {
	$labels = array(
		'name'                       => _x( 'Finalidades', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Finalidade', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Finalidades', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todas as Finalidades', 'imobiliaria-tema' ),
		'parent_item'                => __( 'Finalidade Pai', 'imobiliaria-tema' ),
		'parent_item_colon'          => __( 'Finalidade Pai:', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Nova Finalidade', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Nova Finalidade', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Finalidade', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Finalidade', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Finalidade', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe as finalidades com vírgulas', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover finalidades', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolha entre as mais usadas', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Finalidades Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Finalidades', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Não encontrada', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhuma finalidade', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de finalidades', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de finalidades', 'imobiliaria-tema' ),
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
		'rewrite'                    => array( 'slug' => 'finalidade' ),
	);
	register_taxonomy( 'finalidade', array( 'imovel' ), $args );
}
add_action( 'init', 'imob_register_tax_finalidade', 0 );

/**
 * Add default terms on init if they don't exist
 */
function imob_insert_default_finalidades() {
	$terms = array( 'Venda', 'Aluguel', 'Temporada' );
	foreach ( $terms as $term ) {
		if ( ! term_exists( $term, 'finalidade' ) ) {
			wp_insert_term( $term, 'finalidade' );
		}
	}
}
add_action( 'init', 'imob_insert_default_finalidades', 10 );
