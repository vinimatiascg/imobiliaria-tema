<?php
/**
 * Meta Boxes for Proprietario
 */

function imob_add_proprietario_meta_boxes() {
	add_meta_box(
		'imob_proprietario_details',
		__( 'Detalhes do Proprietário', 'imobiliaria-tema' ),
		'imob_proprietario_meta_box_callback',
		'proprietario',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'imob_add_proprietario_meta_boxes' );

function imob_proprietario_meta_box_callback( $post ) {
	wp_nonce_field( 'imob_save_proprietario_meta', 'imob_proprietario_meta_nonce' );

	$telefone = get_post_meta( $post->ID, '_imob_proprietario_telefone', true );
	$whatsapp = get_post_meta( $post->ID, '_imob_proprietario_whatsapp', true );
	?>
	<style>
		.imob-meta-row { margin-bottom: 15px; display: flex; align-items: center; }
		.imob-meta-row label { width: 150px; display: inline-block; font-weight: bold; }
		.imob-meta-row input[type="text"] { width: 300px; }
	</style>
	
	<div class="imob-meta-row">
		<label for="imob_proprietario_telefone"><?php _e( 'Telefone:', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_proprietario_telefone" name="imob_proprietario_telefone" class="imob-phone-mask" value="<?php echo esc_attr( $telefone ); ?>" placeholder="(00) 00000-0000">
	</div>

	<div class="imob-meta-row">
		<label for="imob_proprietario_whatsapp"><?php _e( 'WhatsApp:', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_proprietario_whatsapp" name="imob_proprietario_whatsapp" class="imob-phone-mask" value="<?php echo esc_attr( $whatsapp ); ?>" placeholder="(00) 00000-0000">
	</div>

	<script>
	jQuery(document).ready(function($){
		// Simple vanilla JS mask for phone (00) 00000-0000
		$('.imob-phone-mask').on('input', function(e) {
			var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
			e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
		});
	});
	</script>
	<?php
}

function imob_save_proprietario_meta( $post_id ) {
	if ( ! isset( $_POST['imob_proprietario_meta_nonce'] ) || ! wp_verify_nonce( $_POST['imob_proprietario_meta_nonce'], 'imob_save_proprietario_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'imob_proprietario_telefone',
		'imob_proprietario_whatsapp',
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'save_post_proprietario', 'imob_save_proprietario_meta' );
