<?php
/**
 * Duplicar Imóvel: cria uma cópia em rascunho com todos os campos, taxonomias,
 * galeria e imagem destacada do original, para agilizar o cadastro de imóveis parecidos.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL segura (com nonce) para duplicar um imóvel.
 */
function imob_duplicate_imovel_url( $post_id ) {
	return wp_nonce_url(
		admin_url( 'admin-post.php?action=imob_duplicate_imovel&post=' . absint( $post_id ) ),
		'imob_duplicate_imovel_' . absint( $post_id )
	);
}

/**
 * Link "Duplicar" na lista de imóveis.
 */
function imob_duplicate_imovel_row_action( $actions, $post ) {
	if ( 'imovel' === $post->post_type && current_user_can( 'edit_posts' ) && current_user_can( 'edit_post', $post->ID ) ) {
		$actions['imob_duplicate'] = sprintf(
			'<a href="%s" aria-label="%s">%s</a>',
			esc_url( imob_duplicate_imovel_url( $post->ID ) ),
			esc_attr( sprintf( __( 'Duplicar “%s”', 'imobiliaria-tema' ), get_the_title( $post ) ) ),
			esc_html__( 'Duplicar', 'imobiliaria-tema' )
		);
	}
	return $actions;
}
add_filter( 'post_row_actions', 'imob_duplicate_imovel_row_action', 10, 2 );

/**
 * Caixa lateral na tela de edição com o botão "Duplicar este imóvel".
 */
function imob_duplicate_imovel_meta_box() {
	add_meta_box(
		'imob_duplicate_box',
		__( 'Duplicar Imóvel', 'imobiliaria-tema' ),
		'imob_duplicate_imovel_meta_box_callback',
		'imovel',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes_imovel', 'imob_duplicate_imovel_meta_box' );

function imob_duplicate_imovel_meta_box_callback( $post ) {
	if ( 'auto-draft' === $post->post_status ) {
		echo '<p class="description">' . esc_html__( 'Salve o imóvel para poder duplicá-lo.', 'imobiliaria-tema' ) . '</p>';
		return;
	}
	echo '<p class="description" style="margin-top:0;">' . esc_html__( 'Cria uma cópia em rascunho com todos os dados deste imóvel. Salve as alterações antes de duplicar.', 'imobiliaria-tema' ) . '</p>';
	printf(
		'<a href="%s" class="button button-secondary" style="width:100%%;text-align:center;"><span class="dashicons dashicons-admin-page" style="vertical-align:middle;margin-top:-2px;"></span> %s</a>',
		esc_url( imob_duplicate_imovel_url( $post->ID ) ),
		esc_html__( 'Duplicar este imóvel', 'imobiliaria-tema' )
	);
}

/**
 * Metadados que não devem ir para a cópia.
 */
function imob_duplicate_imovel_skip_meta() {
	return array(
		'_edit_lock',
		'_edit_last',
		'_wp_old_slug',
		'_wp_old_date',
		'_imob_ref',            // A cópia recebe um código de referência novo.
		'_elementor_css',       // Cache do Elementor, é regerado.
		'_elementor_page_assets',
		'_yoast_wpseo_canonical', // Evita canonical apontando para o original.
		'ekit_post_views_count',  // A cópia começa com as visualizações zeradas.
		'post_views_count',
	);
}

/**
 * Faz a duplicação e abre a cópia no editor.
 */
function imob_handle_duplicate_imovel() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

	check_admin_referer( 'imob_duplicate_imovel_' . $post_id );

	$original = $post_id ? get_post( $post_id ) : null;
	if ( ! $original || 'imovel' !== $original->post_type ) {
		wp_die( esc_html__( 'Imóvel não encontrado.', 'imobiliaria-tema' ) );
	}
	if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( esc_html__( 'Você não tem permissão para duplicar este imóvel.', 'imobiliaria-tema' ) );
	}

	$new_id = wp_insert_post(
		wp_slash(
			array(
				'post_type'      => 'imovel',
				'post_status'    => 'draft',
				'post_title'     => $original->post_title . ' ' . __( '(Cópia)', 'imobiliaria-tema' ),
				'post_content'   => $original->post_content,
				'post_excerpt'   => $original->post_excerpt,
				'post_author'    => get_current_user_id(),
				'post_parent'    => $original->post_parent,
				'menu_order'     => $original->menu_order,
				'comment_status' => $original->comment_status,
				'ping_status'    => $original->ping_status,
				'post_password'  => $original->post_password,
			)
		),
		true
	);

	if ( is_wp_error( $new_id ) ) {
		wp_die( esc_html( $new_id->get_error_message() ) );
	}

	// Taxonomias (tipo, localidade, características, status, corretor, etc.).
	foreach ( get_object_taxonomies( 'imovel' ) as $taxonomy ) {
		$terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			wp_set_object_terms( $new_id, array_map( 'intval', $terms ), $taxonomy );
		}
	}

	// Metadados (preços, áreas, cômodos, localização, galeria, imagem destacada, relações, Elementor, SEO...).
	$skip = imob_duplicate_imovel_skip_meta();
	foreach ( get_post_meta( $post_id ) as $key => $values ) {
		if ( in_array( $key, $skip, true ) ) {
			continue;
		}
		delete_post_meta( $new_id, $key ); // Remove o que hooks de inserção já tenham criado.
		foreach ( $values as $value ) {
			add_post_meta( $new_id, $key, wp_slash( maybe_unserialize( $value ) ) );
		}
	}

	// Novo código de referência, agora com o tipo de imóvel já vinculado.
	delete_post_meta( $new_id, '_imob_ref' );
	if ( function_exists( 'imob_generate_unique_imovel_ref' ) ) {
		imob_generate_unique_imovel_ref( $new_id );
	}

	wp_safe_redirect( add_query_arg( 'imob_duplicated', 1, get_edit_post_link( $new_id, 'raw' ) ) );
	exit;
}
add_action( 'admin_post_imob_duplicate_imovel', 'imob_handle_duplicate_imovel' );

/**
 * Aviso na cópia recém-criada.
 */
function imob_duplicate_imovel_notice() {
	if ( empty( $_GET['imob_duplicated'] ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'imovel' !== $screen->post_type ) {
		return;
	}
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Imóvel duplicado como rascunho. Ajuste o título e os dados que mudam e publique quando estiver pronto.', 'imobiliaria-tema' ) . '</p></div>';
}
add_action( 'admin_notices', 'imob_duplicate_imovel_notice' );
