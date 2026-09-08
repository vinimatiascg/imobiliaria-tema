<?php
/**
 * Meta Boxes for Construtora
 */

function imob_add_construtora_meta_boxes() {
	add_meta_box(
		'imob_construtora_details',
		__( 'Detalhes da Construtora', 'imobiliaria-tema' ),
		'imob_construtora_meta_box_callback',
		'construtora',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'imob_add_construtora_meta_boxes' );

function imob_construtora_meta_box_callback( $post ) {
	wp_nonce_field( 'imob_save_construtora_meta', 'imob_construtora_meta_nonce' );

	$telefone = get_post_meta( $post->ID, '_imob_construtora_telefone', true );
	$whatsapp = get_post_meta( $post->ID, '_imob_construtora_whatsapp', true );
	$site = get_post_meta( $post->ID, '_imob_construtora_site', true );
	$instagram = get_post_meta( $post->ID, '_imob_construtora_instagram', true );
	?>
	<style>
		.imob-meta-row { margin-bottom: 15px; display: flex; align-items: center; }
		.imob-meta-row label { width: 150px; display: inline-block; font-weight: bold; }
		.imob-meta-row input[type="text"], .imob-meta-row input[type="url"] { width: 300px; }
	</style>
	
	<div class="imob-meta-row">
		<label for="imob_construtora_telefone"><?php _e( 'Telefone:', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_construtora_telefone" name="imob_construtora_telefone" class="imob-phone-mask" value="<?php echo esc_attr( $telefone ); ?>" placeholder="(00) 00000-0000">
	</div>

	<div class="imob-meta-row">
		<label for="imob_construtora_whatsapp"><?php _e( 'WhatsApp:', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_construtora_whatsapp" name="imob_construtora_whatsapp" class="imob-phone-mask" value="<?php echo esc_attr( $whatsapp ); ?>" placeholder="(00) 00000-0000">
	</div>

	<div class="imob-meta-row">
		<label for="imob_construtora_site"><?php _e( 'Site:', 'imobiliaria-tema' ); ?></label>
		<input type="url" id="imob_construtora_site" name="imob_construtora_site" value="<?php echo esc_attr( $site ); ?>" placeholder="https://...">
	</div>

	<div class="imob-meta-row">
		<label for="imob_construtora_instagram"><?php _e( 'Instagram (@):', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_construtora_instagram" name="imob_construtora_instagram" value="<?php echo esc_attr( $instagram ); ?>" placeholder="@construtora">
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

function imob_save_construtora_meta( $post_id ) {
	if ( ! isset( $_POST['imob_construtora_meta_nonce'] ) || ! wp_verify_nonce( $_POST['imob_construtora_meta_nonce'], 'imob_save_construtora_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'imob_construtora_telefone',
		'imob_construtora_whatsapp',
		'imob_construtora_site',
		'imob_construtora_instagram',
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'save_post_construtora', 'imob_save_construtora_meta' );
