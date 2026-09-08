<?php
/**
 * Theme Options Page
 */

function imob_theme_options_page() {
	add_menu_page(
		'Configurações da Imobiliária',
		'Opções do Tema',
		'manage_options',
		'imobiliaria-options',
		'imob_theme_options_html',
		'dashicons-admin-generic',
		99
	);
}
add_action( 'admin_menu', 'imob_theme_options_page' );

function imob_register_theme_options() {
	register_setting( 'imob_theme_options_group', 'imob_gmaps_key' );
	register_setting( 'imob_theme_options_group', 'imob_recaptcha_key' );
	register_setting( 'imob_theme_options_group', 'imob_social_instagram' );
	register_setting( 'imob_theme_options_group', 'imob_social_facebook' );
	register_setting( 'imob_theme_options_group', 'imob_contact_phone' );
	register_setting( 'imob_theme_options_group', 'imob_contact_whatsapp' );
	register_setting( 'imob_theme_options_group', 'imob_contact_email' );
	
	// Watermark settings
	register_setting( 'imob_theme_options_group', 'imob_watermark_image' );
	register_setting( 'imob_theme_options_group', 'imob_watermark_opacity' );
	register_setting( 'imob_theme_options_group', 'imob_watermark_position' );
}
add_action( 'admin_init', 'imob_register_theme_options' );

function imob_theme_options_enqueue( $hook ) {
	if ( $hook !== 'toplevel_page_imobiliaria-options' ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'imob_theme_options_enqueue' );

function imob_theme_options_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Configurações da Imobiliária</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'imob_theme_options_group' ); ?>
			<?php do_settings_sections( 'imob_theme_options_group' ); ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Google Maps API Key</th>
					<td><input type="text" name="imob_gmaps_key" value="<?php echo esc_attr( get_option('imob_gmaps_key') ); ?>" class="regular-text" />
					<p class="description">Chave para habilitar o carregamento dos mapas via satélite.</p></td>
				</tr>
				
				<tr valign="top">
					<th scope="row">reCAPTCHA v3 Key</th>
					<td><input type="text" name="imob_recaptcha_key" value="<?php echo esc_attr( get_option('imob_recaptcha_key') ); ?>" class="regular-text" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Instagram URL</th>
					<td><input type="url" name="imob_social_instagram" value="<?php echo esc_attr( get_option('imob_social_instagram') ); ?>" class="regular-text" placeholder="https://instagram.com/..." /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Facebook URL</th>
					<td><input type="url" name="imob_social_facebook" value="<?php echo esc_attr( get_option('imob_social_facebook') ); ?>" class="regular-text" placeholder="https://facebook.com/..." /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Telefone Principal</th>
					<td><input type="text" name="imob_contact_phone" value="<?php echo esc_attr( get_option('imob_contact_phone') ); ?>" class="regular-text" placeholder="(00) 00000-0000" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">WhatsApp Principal</th>
					<td><input type="text" name="imob_contact_whatsapp" value="<?php echo esc_attr( get_option('imob_contact_whatsapp') ); ?>" class="regular-text" placeholder="(00) 00000-0000" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">E-mail Principal</th>
					<td><input type="email" name="imob_contact_email" value="<?php echo esc_attr( get_option('imob_contact_email') ); ?>" class="regular-text" /></td>
				</tr>

				<tr><td colspan="2"><hr><h2>Marca D'água (Imóveis)</h2></td></tr>

				<tr valign="top">
					<th scope="row">Imagem da Marca D'água</th>
					<td>
						<input type="hidden" name="imob_watermark_image" id="imob_watermark_image" value="<?php echo esc_attr( get_option('imob_watermark_image') ); ?>" />
						<div id="imob_watermark_preview" style="margin-bottom: 10px;">
							<?php if ( get_option('imob_watermark_image') ) : ?>
								<img src="<?php echo esc_url( wp_get_attachment_url( get_option('imob_watermark_image') ) ); ?>" style="max-width: 200px; max-height: 200px;" />
							<?php endif; ?>
						</div>
						<button type="button" class="button" id="imob_watermark_upload_btn">Selecionar Imagem</button>
						<button type="button" class="button" id="imob_watermark_remove_btn" style="<?php echo get_option('imob_watermark_image') ? '' : 'display:none;'; ?>">Remover Imagem</button>
						<p class="description">Selecione uma imagem PNG com fundo transparente.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Transparência (%)</th>
					<td>
						<input type="number" name="imob_watermark_opacity" value="<?php echo esc_attr( get_option('imob_watermark_opacity', 100) ); ?>" min="0" max="100" />
						<p class="description">Ex: 50 para metade transparente, 100 para sem transparência.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Posição</th>
					<td>
						<?php $pos = get_option('imob_watermark_position', 'center'); ?>
						<select name="imob_watermark_position">
							<option value="center" <?php selected($pos, 'center'); ?>>Centro</option>
							<option value="bottom_right" <?php selected($pos, 'bottom_right'); ?>>Canto Inferior Direito</option>
							<option value="bottom_left" <?php selected($pos, 'bottom_left'); ?>>Canto Inferior Esquerdo</option>
							<option value="top_right" <?php selected($pos, 'top_right'); ?>>Canto Superior Direito</option>
							<option value="top_left" <?php selected($pos, 'top_left'); ?>>Canto Superior Esquerdo</option>
						</select>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>
	</div>

	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#imob_watermark_upload_btn').on('click', function(e) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Selecione a Marca D\'água',
				button: { text: 'Usar esta imagem' },
				multiple: false
			});
			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#imob_watermark_image').val(attachment.id);
				$('#imob_watermark_preview').html('<img src="'+attachment.url+'" style="max-width: 200px; max-height: 200px;" />');
				$('#imob_watermark_remove_btn').show();
			});
			frame.open();
		});

		$('#imob_watermark_remove_btn').on('click', function(e) {
			e.preventDefault();
			$('#imob_watermark_image').val('');
			$('#imob_watermark_preview').html('');
			$(this).hide();
		});
	});
	</script>
	<?php
}
