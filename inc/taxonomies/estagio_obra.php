<?php
/**
 * Register Taxonomy: Estágio da Obra
 *
 * @package ImobiliariaTema
 */

function imob_register_tax_estagio_obra() {
	$labels = array(
		'name'                       => _x( 'Estágios da Obra', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Estágio da Obra', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Estágio da Obra', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todos os Estágios', 'imobiliaria-tema' ),
		'parent_item'                => __( 'Estágio Pai', 'imobiliaria-tema' ),
		'parent_item_colon'          => __( 'Estágio Pai:', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Novo Estágio', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Novo Estágio', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Estágio', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Estágio', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Estágio', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe os estágios com vírgulas', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover estágios', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolha entre os mais usados', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Estágios Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Estágios', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Nenhum estágio encontrado', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhum estágio', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de estágios', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de estágios', 'imobiliaria-tema' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
		'show_in_rest'      => true,
		'rewrite'           => array( 'slug' => 'estagio-obra' ),
	);

	register_taxonomy( 'estagio_obra', array( 'imovel', 'empreendimento' ), $args );
}
add_action( 'init', 'imob_register_tax_estagio_obra', 0 );

/**
 * Insere os termos padrões caso não existam
 */
function imob_insert_default_estagios_obra() {
	$terms = array( 'Lançamento', 'Em Construção', 'Pronto para Morar', 'Na Planta' );
	foreach ( $terms as $term ) {
		if ( ! term_exists( $term, 'estagio_obra' ) ) {
			wp_insert_term( $term, 'estagio_obra' );
		}
	}
}
add_action( 'init', 'imob_insert_default_estagios_obra', 10 );
