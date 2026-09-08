<?php
/**
 * Register Taxonomy: Corretor
 */

function imob_register_tax_corretor() {
	$labels = array(
		'name'                       => _x( 'Corretores', 'Taxonomy General Name', 'imobiliaria-tema' ),
		'singular_name'              => _x( 'Corretor', 'Taxonomy Singular Name', 'imobiliaria-tema' ),
		'menu_name'                  => __( 'Corretores', 'imobiliaria-tema' ),
		'all_items'                  => __( 'Todos os Corretores', 'imobiliaria-tema' ),
		'new_item_name'              => __( 'Novo Nome de Corretor', 'imobiliaria-tema' ),
		'add_new_item'               => __( 'Adicionar Novo Corretor', 'imobiliaria-tema' ),
		'edit_item'                  => __( 'Editar Corretor', 'imobiliaria-tema' ),
		'update_item'                => __( 'Atualizar Corretor', 'imobiliaria-tema' ),
		'view_item'                  => __( 'Ver Corretor', 'imobiliaria-tema' ),
		'separate_items_with_commas' => __( 'Separe corretores por vírgula', 'imobiliaria-tema' ),
		'add_or_remove_items'        => __( 'Adicionar ou remover corretores', 'imobiliaria-tema' ),
		'choose_from_most_used'      => __( 'Escolher entre os mais usados', 'imobiliaria-tema' ),
		'popular_items'              => __( 'Corretores Populares', 'imobiliaria-tema' ),
		'search_items'               => __( 'Buscar Corretores', 'imobiliaria-tema' ),
		'not_found'                  => __( 'Não encontrado', 'imobiliaria-tema' ),
		'no_terms'                   => __( 'Nenhum corretor', 'imobiliaria-tema' ),
		'items_list'                 => __( 'Lista de corretores', 'imobiliaria-tema' ),
		'items_list_navigation'      => __( 'Navegação da lista de corretores', 'imobiliaria-tema' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false, // Like tags
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'               => true,
		'rewrite'                    => array( 'slug' => 'corretor' ),
	);
	register_taxonomy( 'corretor', array( 'imovel' ), $args );
}
add_action( 'init', 'imob_register_tax_corretor', 0 );

/**
 * Add custom fields to Corretor taxonomy
 */
function imob_corretor_add_form_fields( $taxonomy ) {
	?>
	<div class="form-field term-group">
		<label for="imob_corretor_creci"><?php _e( 'CRECI', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_corretor_creci" name="imob_corretor_creci" value="">
	</div>
	<div class="form-field term-group">
		<label for="imob_corretor_cnai"><?php _e( 'CNAI', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_corretor_cnai" name="imob_corretor_cnai" value="">
	</div>
	<div class="form-field term-group">
		<label for="imob_corretor_resumo"><?php _e( 'Resumo (Bio)', 'imobiliaria-tema' ); ?></label>
		<textarea id="imob_corretor_resumo" name="imob_corretor_resumo" rows="3"></textarea>
	</div>
	<div class="form-field term-group">
		<label for="imob_corretor_instagram"><?php _e( 'Instagram', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_corretor_instagram" name="imob_corretor_instagram" value="" placeholder="@corretor">
	</div>
	<div class="form-field term-group">
		<label for="imob_corretor_whatsapp"><?php _e( 'WhatsApp', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_corretor_whatsapp" name="imob_corretor_whatsapp" value="" placeholder="(00) 00000-0000">
	</div>
	<div class="form-field term-group">
		<label for="imob_corretor_email"><?php _e( 'E-mail', 'imobiliaria-tema' ); ?></label>
		<input type="email" id="imob_corretor_email" name="imob_corretor_email" value="">
	</div>
	<div class="form-field term-group">
		<label><?php _e( 'Foto do Corretor', 'imobiliaria-tema' ); ?></label>
		<div id="imob-corretor-foto-wrapper"></div>
		<input type="hidden" id="imob_corretor_foto" name="imob_corretor_foto" value="">
		<p><button type="button" class="button" id="imob_corretor_foto_btn"><?php _e( 'Selecionar Imagem', 'imobiliaria-tema' ); ?></button>
		<button type="button" class="button" id="imob_corretor_foto_remove" style="display:none; color:red;"><?php _e( 'Remover Imagem', 'imobiliaria-tema' ); ?></button></p>
	</div>
	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#imob_corretor_foto_btn').on('click', function(e) {
			e.preventDefault();
			if ( frame ) { frame.open(); return; }
			frame = wp.media({ title: 'Selecione a foto', button: { text: 'Usar imagem' }, multiple: false });
			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#imob_corretor_foto').val(attachment.id);
				$('#imob-corretor-foto-wrapper').html('<img src="'+attachment.url+'" style="max-width:150px;display:block;margin-bottom:10px;">');
				$('#imob_corretor_foto_remove').show();
			});
			frame.open();
		});
		$('#imob_corretor_foto_remove').on('click', function(e) {
			e.preventDefault();
			$('#imob_corretor_foto').val('');
			$('#imob-corretor-foto-wrapper').html('');
			$(this).hide();
		});
	});
	</script>
	<?php
}
add_action( 'corretor_add_form_fields', 'imob_corretor_add_form_fields', 10, 2 );

function imob_corretor_edit_form_fields( $term, $taxonomy ) {
	$creci = get_term_meta( $term->term_id, 'imob_corretor_creci', true );
	$cnai = get_term_meta( $term->term_id, 'imob_corretor_cnai', true );
	$resumo = get_term_meta( $term->term_id, 'imob_corretor_resumo', true );
	$instagram = get_term_meta( $term->term_id, 'imob_corretor_instagram', true );
	$whatsapp = get_term_meta( $term->term_id, 'imob_corretor_whatsapp', true );
	$email = get_term_meta( $term->term_id, 'imob_corretor_email', true );
	$foto = get_term_meta( $term->term_id, 'imob_corretor_foto', true );
	$foto_url = $foto ? wp_get_attachment_url( $foto ) : '';
	?>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="imob_corretor_creci"><?php _e( 'CRECI', 'imobiliaria-tema' ); ?></label></th>
		<td><input type="text" id="imob_corretor_creci" name="imob_corretor_creci" value="<?php echo esc_attr( $creci ); ?>"></td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="imob_corretor_cnai"><?php _e( 'CNAI', 'imobiliaria-tema' ); ?></label></th>
		<td><input type="text" id="imob_corretor_cnai" name="imob_corretor_cnai" value="<?php echo esc_attr( $cnai ); ?>"></td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="imob_corretor_resumo"><?php _e( 'Resumo (Bio)', 'imobiliaria-tema' ); ?></label></th>
		<td><textarea id="imob_corretor_resumo" name="imob_corretor_resumo" rows="3"><?php echo esc_textarea( $resumo ); ?></textarea></td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="imob_corretor_instagram"><?php _e( 'Instagram', 'imobiliaria-tema' ); ?></label></th>
		<td><input type="text" id="imob_corretor_instagram" name="imob_corretor_instagram" value="<?php echo esc_attr( $instagram ); ?>" placeholder="@corretor"></td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="imob_corretor_whatsapp"><?php _e( 'WhatsApp', 'imobiliaria-tema' ); ?></label></th>
		<td><input type="text" id="imob_corretor_whatsapp" name="imob_corretor_whatsapp" value="<?php echo esc_attr( $whatsapp ); ?>" placeholder="(00) 00000-0000"></td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label for="imob_corretor_email"><?php _e( 'E-mail', 'imobiliaria-tema' ); ?></label></th>
		<td><input type="email" id="imob_corretor_email" name="imob_corretor_email" value="<?php echo esc_attr( $email ); ?>"></td>
	</tr>
	<tr class="form-field term-group-wrap">
		<th scope="row"><label><?php _e( 'Foto do Corretor', 'imobiliaria-tema' ); ?></label></th>
		<td>
			<div id="imob-corretor-foto-wrapper">
				<?php if ( $foto_url ) echo '<img src="'.esc_url($foto_url).'" style="max-width:150px;display:block;margin-bottom:10px;">'; ?>
			</div>
			<input type="hidden" id="imob_corretor_foto" name="imob_corretor_foto" value="<?php echo esc_attr( $foto ); ?>">
			<p><button type="button" class="button" id="imob_corretor_foto_btn"><?php _e( 'Selecionar Imagem', 'imobiliaria-tema' ); ?></button>
			<button type="button" class="button" id="imob_corretor_foto_remove" style="<?php echo $foto ? '' : 'display:none;'; ?> color:red;"><?php _e( 'Remover Imagem', 'imobiliaria-tema' ); ?></button></p>
		</td>
	</tr>
	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#imob_corretor_foto_btn').on('click', function(e) {
			e.preventDefault();
			if ( frame ) { frame.open(); return; }
			frame = wp.media({ title: 'Selecione a foto', button: { text: 'Usar imagem' }, multiple: false });
			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#imob_corretor_foto').val(attachment.id);
				$('#imob-corretor-foto-wrapper').html('<img src="'+attachment.url+'" style="max-width:150px;display:block;margin-bottom:10px;">');
				$('#imob_corretor_foto_remove').show();
			});
			frame.open();
		});
		$('#imob_corretor_foto_remove').on('click', function(e) {
			e.preventDefault();
			$('#imob_corretor_foto').val('');
			$('#imob-corretor-foto-wrapper').html('');
			$(this).hide();
		});
	});
	</script>
	<?php
}
add_action( 'corretor_edit_form_fields', 'imob_corretor_edit_form_fields', 10, 2 );

function imob_save_corretor_meta( $term_id ) {
	$fields = [
		'imob_corretor_creci',
		'imob_corretor_cnai',
		'imob_corretor_resumo',
		'imob_corretor_instagram',
		'imob_corretor_whatsapp',
		'imob_corretor_email',
		'imob_corretor_foto'
	];

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_term_meta( $term_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}
}
add_action( 'created_corretor', 'imob_save_corretor_meta', 10, 2 );
add_action( 'edited_corretor', 'imob_save_corretor_meta', 10, 2 );
