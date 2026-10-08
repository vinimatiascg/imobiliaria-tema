<?php
/**
 * User Profile Custom Contact Fields
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom contact fields to user profile
 */
function imob_user_profile_contact_fields( $user ) {
	$whatsapp  = get_user_meta( $user->ID, 'imob_whatsapp', true ) ?: get_user_meta( $user->ID, 'whatsapp', true );
	$telefone  = get_user_meta( $user->ID, 'imob_phone', true ) ?: get_user_meta( $user->ID, 'phone', true ) ?: get_user_meta( $user->ID, 'telefone', true );
	$creci     = get_user_meta( $user->ID, 'imob_creci', true ) ?: get_user_meta( $user->ID, 'creci', true );
	$cnai      = get_user_meta( $user->ID, 'imob_cnai', true ) ?: get_user_meta( $user->ID, 'cnai', true );
	$instagram = get_user_meta( $user->ID, 'imob_instagram', true ) ?: get_user_meta( $user->ID, 'instagram', true );
	$cargo     = get_user_meta( $user->ID, 'imob_cargo', true ) ?: get_user_meta( $user->ID, 'cargo', true );
	$foto_id   = get_user_meta( $user->ID, 'imob_user_foto', true ) ?: get_user_meta( $user->ID, 'foto', true );
	$foto_url  = $foto_id ? wp_get_attachment_image_url( $foto_id, 'thumbnail' ) : '';
	?>
	<h2><?php _e( 'Dados do Corretor / Contato Imobiliário', 'imobiliaria-tema' ); ?></h2>
	<table class="form-table">
		<tr>
			<th><label for="imob_cargo"><?php _e( 'Cargo / Função', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<input type="text" name="imob_cargo" id="imob_cargo" value="<?php echo esc_attr( $cargo ); ?>" class="regular-text" placeholder="Ex: Corretor e avaliador de imóveis" />
			</td>
		</tr>
		<tr>
			<th><label for="imob_creci"><?php _e( 'CRECI', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<input type="text" name="imob_creci" id="imob_creci" value="<?php echo esc_attr( $creci ); ?>" class="regular-text" placeholder="Ex: 12345-F" />
			</td>
		</tr>
		<tr>
			<th><label for="imob_cnai"><?php _e( 'CNAI', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<input type="text" name="imob_cnai" id="imob_cnai" value="<?php echo esc_attr( $cnai ); ?>" class="regular-text" placeholder="Ex: 6789" />
			</td>
		</tr>
		<tr>
			<th><label for="imob_whatsapp"><?php _e( 'WhatsApp', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<input type="text" name="imob_whatsapp" id="imob_whatsapp" value="<?php echo esc_attr( $whatsapp ); ?>" class="regular-text" placeholder="Ex: (83) 99999-9999" />
				<p class="description"><?php _e( 'Número de WhatsApp usado nos botões de contato dos imóveis.', 'imobiliaria-tema' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="imob_phone"><?php _e( 'Telefone Fixo / Outro', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<input type="text" name="imob_phone" id="imob_phone" value="<?php echo esc_attr( $telefone ); ?>" class="regular-text" placeholder="Ex: (83) 3333-3333" />
			</td>
		</tr>
		<tr>
			<th><label for="imob_instagram"><?php _e( 'Instagram', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<input type="text" name="imob_instagram" id="imob_instagram" value="<?php echo esc_attr( $instagram ); ?>" class="regular-text" placeholder="Ex: @corretor" />
			</td>
		</tr>
		<tr>
			<th><label><?php _e( 'Foto do Perfil / Corretor', 'imobiliaria-tema' ); ?></label></th>
			<td>
				<div id="imob-user-foto-preview" style="margin-bottom: 10px;">
					<?php if ( $foto_url ) : ?>
						<img src="<?php echo esc_url( $foto_url ); ?>" style="max-width: 100px; border-radius: 8px; display: block;" />
					<?php endif; ?>
				</div>
				<input type="hidden" name="imob_user_foto" id="imob_user_foto" value="<?php echo esc_attr( $foto_id ); ?>" />
				<button type="button" class="button" id="imob_user_foto_btn"><?php _e( 'Selecionar Imagem', 'imobiliaria-tema' ); ?></button>
				<button type="button" class="button" id="imob_user_foto_remove" style="<?php echo $foto_url ? '' : 'display:none;'; ?> color: red;"><?php _e( 'Remover Imagem', 'imobiliaria-tema' ); ?></button>
			</td>
		</tr>
	</table>
	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#imob_user_foto_btn').on('click', function(e) {
			e.preventDefault();
			if ( frame ) { frame.open(); return; }
			frame = wp.media({ title: 'Selecione a foto do corretor', button: { text: 'Usar foto' }, multiple: false });
			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#imob_user_foto').val(attachment.id);
				$('#imob-user-foto-preview').html('<img src="'+attachment.url+'" style="max-width:100px;border-radius:8px;display:block;">');
				$('#imob_user_foto_remove').show();
			});
			frame.open();
		});
		$('#imob_user_foto_remove').on('click', function(e) {
			e.preventDefault();
			$('#imob_user_foto').val('');
			$('#imob-user-foto-preview').html('');
			$(this).hide();
		});
	});
	</script>
	<?php
}
add_action( 'show_user_profile', 'imob_user_profile_contact_fields' );
add_action( 'edit_user_profile', 'imob_user_profile_contact_fields' );

/**
 * Save user profile custom contact fields
 */
function imob_save_user_profile_contact_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$fields = array(
		'imob_cargo'     => 'sanitize_text_field',
		'imob_creci'     => 'sanitize_text_field',
		'imob_cnai'      => 'sanitize_text_field',
		'imob_whatsapp'  => 'sanitize_text_field',
		'imob_phone'     => 'sanitize_text_field',
		'imob_instagram' => 'sanitize_text_field',
		'imob_user_foto' => 'intval',
	);

	foreach ( $fields as $field => $sanitizer ) {
		if ( isset( $_POST[ $field ] ) ) {
			$val = call_user_func( $sanitizer, $_POST[ $field ] );
			update_user_meta( $user_id, $field, $val );
			// Also sync common fallback aliases
			if ( $field === 'imob_whatsapp' ) {
				update_user_meta( $user_id, 'whatsapp', $val );
			} elseif ( $field === 'imob_phone' ) {
				update_user_meta( $user_id, 'phone', $val );
			} elseif ( $field === 'imob_creci' ) {
				update_user_meta( $user_id, 'creci', $val );
			} elseif ( $field === 'imob_cnai' ) {
				update_user_meta( $user_id, 'cnai', $val );
			} elseif ( $field === 'imob_instagram' ) {
				update_user_meta( $user_id, 'instagram', $val );
			} elseif ( $field === 'imob_cargo' ) {
				update_user_meta( $user_id, 'cargo', $val );
			}
		}
	}
}
add_action( 'personal_options_update', 'imob_save_user_profile_contact_fields' );
add_action( 'edit_user_profile_update', 'imob_save_user_profile_contact_fields' );

/**
 * Enqueue media scripts on profile page
 */
function imob_user_profile_enqueue( $hook ) {
	if ( in_array( $hook, array( 'profile.php', 'user-edit.php' ), true ) ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'imob_user_profile_enqueue' );

/**
 * Get unified user contact data for an author or user
 */
function imob_get_user_contact_data( $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_the_author_meta( 'ID' );
	}
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return false;
	}

	$whatsapp = get_user_meta( $user_id, 'imob_whatsapp', true )
		?: get_user_meta( $user_id, 'whatsapp', true )
		?: get_user_meta( $user_id, 'telefone', true )
		?: get_user_meta( $user_id, 'celular', true )
		?: get_user_meta( $user_id, 'phone', true )
		?: get_option( 'imob_contact_whatsapp' );

	$phone = get_user_meta( $user_id, 'imob_phone', true )
		?: get_user_meta( $user_id, 'phone', true )
		?: get_user_meta( $user_id, 'telefone', true )
		?: get_option( 'imob_contact_phone' );

	$email = get_user_meta( $user_id, 'imob_email', true )
		?: $user->user_email
		?: get_option( 'imob_contact_email' );

	$instagram = get_user_meta( $user_id, 'imob_instagram', true )
		?: get_user_meta( $user_id, 'instagram', true )
		?: get_option( 'imob_social_instagram' );

	$creci = get_user_meta( $user_id, 'imob_creci', true )
		?: get_user_meta( $user_id, 'creci', true )
		?: get_user_meta( $user_id, 'imob_corretor_creci', true );

	$cnai = get_user_meta( $user_id, 'imob_cnai', true )
		?: get_user_meta( $user_id, 'cnai', true )
		?: get_user_meta( $user_id, 'imob_corretor_cnai', true );

	$cargo = get_user_meta( $user_id, 'imob_cargo', true )
		?: get_user_meta( $user_id, 'cargo', true )
		?: 'Corretor e avaliador de imóveis';

	$resumo = $user->description
		?: get_user_meta( $user_id, 'imob_resumo', true )
		?: '';

	$foto_id = get_user_meta( $user_id, 'imob_user_foto', true )
		?: get_user_meta( $user_id, 'foto', true );
	$foto_url = '';
	if ( $foto_id && is_numeric( $foto_id ) ) {
		$foto_url = wp_get_attachment_image_url( $foto_id, 'thumbnail' );
	}
	if ( empty( $foto_url ) ) {
		$foto_url = get_avatar_url( $user_id, array( 'size' => 140 ) );
	}

	return array(
		'id'        => $user_id,
		'name'      => $user->display_name,
		'role'      => $cargo,
		'creci'     => $creci,
		'cnai'      => $cnai,
		'resumo'    => $resumo,
		'whatsapp'  => $whatsapp,
		'phone'     => $phone,
		'email'     => $email,
		'instagram' => $instagram,
		'foto_url'  => $foto_url,
		'link'      => get_author_posts_url( $user_id ),
	);
}
