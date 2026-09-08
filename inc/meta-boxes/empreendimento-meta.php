<?php
/**
 * Meta Boxes for Empreendimento
 */

function imob_add_empreendimento_meta_boxes() {
	add_meta_box(
		'imob_empreendimento_details',
		__( 'Detalhes do Empreendimento', 'imobiliaria-tema' ),
		'imob_empreendimento_meta_box_callback',
		'empreendimento',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'imob_add_empreendimento_meta_boxes' );

function imob_empreendimento_meta_box_callback( $post ) {
	wp_nonce_field( 'imob_save_empreendimento_meta', 'imob_empreendimento_meta_nonce' );

	$estagio = get_post_meta( $post->ID, '_imob_emp_estagio', true );
	$previsao = get_post_meta( $post->ID, '_imob_emp_previsao', true );
	$construtora_id = get_post_meta( $post->ID, '_imob_emp_construtora_id', true );
	
	?>
	<style>
		.imob-meta-row { margin-bottom: 15px; display: flex; align-items: center; }
		.imob-meta-row label { width: 150px; display: inline-block; font-weight: bold; }
		.imob-meta-row input[type="text"], .imob-meta-row select { width: 300px; }
	</style>
	
	<div class="imob-meta-row">
		<label for="imob_emp_estagio"><?php _e( 'Estágio da Obra:', 'imobiliaria-tema' ); ?></label>
		<select name="imob_emp_estagio" id="imob_emp_estagio">
			<option value="Lancamento" <?php selected($estagio, 'Lancamento'); ?>><?php _e( 'Lançamento', 'imobiliaria-tema' ); ?></option>
			<option value="Em Construcao" <?php selected($estagio, 'Em Construcao'); ?>><?php _e( 'Em Construção', 'imobiliaria-tema' ); ?></option>
			<option value="Pronto" <?php selected($estagio, 'Pronto'); ?>><?php _e( 'Pronto para Morar', 'imobiliaria-tema' ); ?></option>
		</select>
	</div>

	<div class="imob-meta-row">
		<label for="imob_emp_previsao"><?php _e( 'Previsão de Entrega:', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_emp_previsao" name="imob_emp_previsao" value="<?php echo esc_attr( $previsao ); ?>" placeholder="Ex: Dez/2026">
	</div>

	<div class="imob-meta-row">
		<label for="imob_emp_construtora_id"><?php _e( 'Construtora:', 'imobiliaria-tema' ); ?></label>
		<select name="imob_emp_construtora_id" id="imob_emp_construtora_id">
			<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
			<?php
			$construtoras = get_posts( array( 'post_type' => 'construtora', 'numberposts' => -1 ) );
			foreach ( $construtoras as $const ) {
				echo '<option value="' . $const->ID . '" ' . selected( $construtora_id, $const->ID, false ) . '>' . $const->post_title . '</option>';
			}
			?>
		</select>
	</div>
	<?php
}

function imob_save_empreendimento_meta( $post_id ) {
	if ( ! isset( $_POST['imob_empreendimento_meta_nonce'] ) || ! wp_verify_nonce( $_POST['imob_empreendimento_meta_nonce'], 'imob_save_empreendimento_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'imob_emp_estagio',
		'imob_emp_previsao',
		'imob_emp_construtora_id'
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'save_post_empreendimento', 'imob_save_empreendimento_meta' );
