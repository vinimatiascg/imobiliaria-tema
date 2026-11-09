<?php
/**
 * CPT Depoimento: avaliações de clientes exibidas na home.
 *
 * Título = chamada (ex.: "Sem burocracia"), conteúdo = texto do depoimento.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function imob_register_depoimento_cpt() {
	register_post_type(
		'depoimento',
		array(
			'labels'              => array(
				'name'          => __( 'Depoimentos', 'imobiliaria-tema' ),
				'singular_name' => __( 'Depoimento', 'imobiliaria-tema' ),
				'add_new_item'  => __( 'Adicionar depoimento', 'imobiliaria-tema' ),
				'edit_item'     => __( 'Editar depoimento', 'imobiliaria-tema' ),
				'menu_name'     => __( 'Depoimentos', 'imobiliaria-tema' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 26,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'exclude_from_search' => true,
		)
	);
}
add_action( 'init', 'imob_register_depoimento_cpt' );

function imob_depoimento_meta_box() {
	add_meta_box( 'imob_depoimento_dados', __( 'Dados do cliente', 'imobiliaria-tema' ), 'imob_depoimento_meta_box_html', 'depoimento', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'imob_depoimento_meta_box' );

function imob_depoimento_meta_box_html( $post ) {
	wp_nonce_field( 'imob_depoimento_save', 'imob_depoimento_nonce' );
	$nome   = get_post_meta( $post->ID, '_imob_dep_nome', true );
	$cidade = get_post_meta( $post->ID, '_imob_dep_cidade', true );
	$nota   = get_post_meta( $post->ID, '_imob_dep_nota', true );
	$nota   = $nota ? (int) $nota : 5;
	?>
	<p>
		<label for="imob_dep_nome"><strong><?php esc_html_e( 'Nome do cliente', 'imobiliaria-tema' ); ?></strong></label><br>
		<input type="text" id="imob_dep_nome" name="imob_dep_nome" value="<?php echo esc_attr( $nome ); ?>" class="widefat">
	</p>
	<p>
		<label for="imob_dep_cidade"><strong><?php esc_html_e( 'Cidade / UF', 'imobiliaria-tema' ); ?></strong></label><br>
		<input type="text" id="imob_dep_cidade" name="imob_dep_cidade" value="<?php echo esc_attr( $cidade ); ?>" class="widefat" placeholder="Campina Grande - PB">
	</p>
	<p>
		<label for="imob_dep_nota"><strong><?php esc_html_e( 'Nota (1 a 5)', 'imobiliaria-tema' ); ?></strong></label><br>
		<input type="number" id="imob_dep_nota" name="imob_dep_nota" value="<?php echo esc_attr( $nota ); ?>" min="1" max="5" class="small-text">
	</p>
	<p class="description"><?php esc_html_e( 'A imagem destacada é usada como foto do cliente. Sem foto, aparece a inicial do nome.', 'imobiliaria-tema' ); ?></p>
	<?php
}

function imob_depoimento_save( $post_id ) {
	if ( ! isset( $_POST['imob_depoimento_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['imob_depoimento_nonce'] ), 'imob_depoimento_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_imob_dep_nome', sanitize_text_field( wp_unslash( $_POST['imob_dep_nome'] ?? '' ) ) );
	update_post_meta( $post_id, '_imob_dep_cidade', sanitize_text_field( wp_unslash( $_POST['imob_dep_cidade'] ?? '' ) ) );
	update_post_meta( $post_id, '_imob_dep_nota', max( 1, min( 5, absint( $_POST['imob_dep_nota'] ?? 5 ) ) ) );
}
add_action( 'save_post_depoimento', 'imob_depoimento_save' );
