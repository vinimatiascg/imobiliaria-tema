<?php
/**
 * Meta Boxes for Imóveis
 */

function imob_add_imovel_meta_boxes() {
	add_meta_box(
		'imob_imovel_details',
		__( 'Detalhes do Imóvel', 'imobiliaria-tema' ),
		'imob_imovel_meta_box_callback',
		'imovel',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'imob_add_imovel_meta_boxes' );

function imob_imovel_meta_box_callback( $post ) {
	wp_nonce_field( 'imob_save_imovel_meta', 'imob_imovel_meta_nonce' );

	$ref = get_post_meta( $post->ID, '_imob_ref', true );
	$preco_venda = get_post_meta( $post->ID, '_imob_preco_venda', true );
	$preco_antes_texto = get_post_meta( $post->ID, '_imob_preco_antes_texto', true );
	$preco_depois_texto = get_post_meta( $post->ID, '_imob_preco_depois_texto', true );
	$preco_anterior = get_post_meta( $post->ID, '_imob_preco_anterior', true );
	$preco_aluguel = get_post_meta( $post->ID, '_imob_preco_aluguel', true );
	$condominio = get_post_meta( $post->ID, '_imob_condominio', true );
	$iptu = get_post_meta( $post->ID, '_imob_iptu', true );
	
	$area_privativa = get_post_meta( $post->ID, '_imob_area_privativa', true );
	$area_total = get_post_meta( $post->ID, '_imob_area_total', true );
	
	$quartos = get_post_meta( $post->ID, '_imob_quartos', true );
	$suites = get_post_meta( $post->ID, '_imob_suites', true );
	$banheiros = get_post_meta( $post->ID, '_imob_banheiros', true );
	$vagas = get_post_meta( $post->ID, '_imob_vagas', true );
	
	$destaque = get_post_meta( $post->ID, '_imob_destaque', true );
	$video_url = get_post_meta( $post->ID, '_imob_video_url', true );
	
	$lat = get_post_meta( $post->ID, '_imob_lat', true );
	$lng = get_post_meta( $post->ID, '_imob_lng', true );

	$construtora_id = get_post_meta( $post->ID, '_imob_construtora_id', true );
	$proprietario_id = get_post_meta( $post->ID, '_imob_proprietario_id', true );
	$empreendimento_id = get_post_meta( $post->ID, '_imob_empreendimento_id', true );
	$endereco = get_post_meta( $post->ID, '_imob_endereco', true );
	
	$galeria = get_post_meta( $post->ID, '_imob_galeria', true );
	if ( !is_array($galeria) && !empty($galeria) ) {
		$galeria = explode(',', $galeria);
	}
	$galeria = is_array($galeria) ? $galeria : [];

	wp_enqueue_media();
	?>
	<style>
		.imob-meta-row { margin-bottom: 15px; display: flex; align-items: center; }
		.imob-meta-row label { width: 150px; display: inline-block; font-weight: bold; }
		.imob-meta-row input[type="text"], .imob-meta-row input[type="number"], .imob-meta-row select { width: 300px; }
		.imob-meta-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #ccc; }
	</style>
	
	<div class="imob-meta-row">
		<label for="imob_ref"><?php _e( 'Referência (Cód):', 'imobiliaria-tema' ); ?></label>
		<input type="text" id="imob_ref" name="imob_ref" value="<?php echo esc_attr( $ref ); ?>">
	</div>

	<div class="imob-meta-section">
		<h4><?php _e( 'Valores', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-meta-row">
			<label for="imob_preco_venda"><?php _e( 'Preço de Venda (R$):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_preco_venda" name="imob_preco_venda" value="<?php echo esc_attr( $preco_venda ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_preco_antes_texto"><?php _e( 'Texto antes do Preço:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_preco_antes_texto" name="imob_preco_antes_texto" value="<?php echo esc_attr( $preco_antes_texto ); ?>" placeholder="Ex: A partir de:">
		</div>
		<div class="imob-meta-row">
			<label for="imob_preco_depois_texto"><?php _e( 'Texto depois do Preço:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_preco_depois_texto" name="imob_preco_depois_texto" value="<?php echo esc_attr( $preco_depois_texto ); ?>" placeholder="Ex: /mês">
		</div>
		<div class="imob-meta-row">
			<label for="imob_preco_anterior"><?php _e( 'Preço Anterior (R$):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_preco_anterior" name="imob_preco_anterior" value="<?php echo esc_attr( $preco_anterior ); ?>" placeholder="Valor antigo para gerar o De: Por:">
		</div>
		<div class="imob-meta-row">
			<label for="imob_preco_aluguel"><?php _e( 'Valor Aluguel (R$):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_preco_aluguel" name="imob_preco_aluguel" value="<?php echo esc_attr( $preco_aluguel ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_condominio"><?php _e( 'Condomínio (R$):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_condominio" name="imob_condominio" value="<?php echo esc_attr( $condominio ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_iptu"><?php _e( 'IPTU (R$):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_iptu" name="imob_iptu" value="<?php echo esc_attr( $iptu ); ?>">
		</div>
	</div>

	<div class="imob-meta-section">
		<h4><?php _e( 'Estrutura e Áreas', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-meta-row">
			<label for="imob_area_privativa"><?php _e( 'Área Privativa (m²):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_area_privativa" name="imob_area_privativa" value="<?php echo esc_attr( $area_privativa ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_area_total"><?php _e( 'Área Total (m²):', 'imobiliaria-tema' ); ?></label>
			<input type="number" step="0.01" id="imob_area_total" name="imob_area_total" value="<?php echo esc_attr( $area_total ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_quartos"><?php _e( 'Quartos:', 'imobiliaria-tema' ); ?></label>
			<input type="number" id="imob_quartos" name="imob_quartos" value="<?php echo esc_attr( $quartos ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_suites"><?php _e( 'Suítes:', 'imobiliaria-tema' ); ?></label>
			<input type="number" id="imob_suites" name="imob_suites" value="<?php echo esc_attr( $suites ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_banheiros"><?php _e( 'Banheiros:', 'imobiliaria-tema' ); ?></label>
			<input type="number" id="imob_banheiros" name="imob_banheiros" value="<?php echo esc_attr( $banheiros ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_vagas"><?php _e( 'Vagas:', 'imobiliaria-tema' ); ?></label>
			<input type="number" id="imob_vagas" name="imob_vagas" value="<?php echo esc_attr( $vagas ); ?>">
		</div>
	</div>

	<div class="imob-meta-section">
		<h4><?php _e( 'Relacionamentos', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-meta-row">
			<label for="imob_construtora_id"><?php _e( 'Construtora:', 'imobiliaria-tema' ); ?></label>
			<select name="imob_construtora_id" id="imob_construtora_id">
				<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
				<?php
				$construtoras = get_posts( array( 'post_type' => 'construtora', 'numberposts' => -1 ) );
				foreach ( $construtoras as $const ) {
					echo '<option value="' . $const->ID . '" ' . selected( $construtora_id, $const->ID, false ) . '>' . $const->post_title . '</option>';
				}
				?>
			</select>
		</div>
		<div class="imob-meta-row">
			<label for="imob_proprietario_id"><?php _e( 'Proprietário (Interno):', 'imobiliaria-tema' ); ?></label>
			<select name="imob_proprietario_id" id="imob_proprietario_id">
				<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
				<?php
				$proprietarios = get_posts( array( 'post_type' => 'proprietario', 'numberposts' => -1 ) );
				foreach ( $proprietarios as $prop ) {
					echo '<option value="' . $prop->ID . '" ' . selected( $proprietario_id, $prop->ID, false ) . '>' . $prop->post_title . '</option>';
				}
				?>
			</select>
		</div>
		<div class="imob-meta-row">
			<label for="imob_empreendimento_id"><?php _e( 'Empreendimento:', 'imobiliaria-tema' ); ?></label>
			<select name="imob_empreendimento_id" id="imob_empreendimento_id">
				<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
				<?php
				$empreendimentos = get_posts( array( 'post_type' => 'empreendimento', 'numberposts' => -1 ) );
				foreach ( $empreendimentos as $emp ) {
					echo '<option value="' . $emp->ID . '" ' . selected( $empreendimento_id, $emp->ID, false ) . '>' . $emp->post_title . '</option>';
				}
				?>
			</select>
		</div>
	</div>

	<div class="imob-meta-section">
		<h4><?php _e( 'Mídia e Localização', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-meta-row">
			<label for="imob_video_url"><?php _e( 'Vídeo URL:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_video_url" name="imob_video_url" value="<?php echo esc_attr( $video_url ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_lat"><?php _e( 'Latitude:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_lat" name="imob_lat" value="<?php echo esc_attr( $lat ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_lng"><?php _e( 'Longitude:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_lng" name="imob_lng" value="<?php echo esc_attr( $lng ); ?>">
		</div>
		<div class="imob-meta-row">
			<label for="imob_endereco"><?php _e( 'Endereço Completo:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_endereco" name="imob_endereco" value="<?php echo esc_attr( $endereco ); ?>" placeholder="Ex: Rua Teste, 123 - Centro">
		</div>
	</div>
	
	<div class="imob-meta-section">
		<h4><?php _e( 'Galeria de Imagens', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-gallery-container" style="margin-bottom: 15px;">
			<ul id="imob-gallery-list" style="display: flex; flex-wrap: wrap; gap: 10px; margin: 0; padding: 0; list-style: none;">
				<?php foreach ( $galeria as $img_id ) : ?>
					<?php if ( $img_id ) : ?>
						<li data-id="<?php echo esc_attr($img_id); ?>" style="position:relative; width: 100px; height: 100px; border: 1px solid #ccc;">
							<?php echo wp_get_attachment_image( $img_id, 'thumbnail', false, ['style' => 'width:100%;height:100%;object-fit:cover;'] ); ?>
							<a href="#" class="imob-remove-img" style="position:absolute; top:-5px; right:-5px; background:red; color:#fff; border-radius:50%; width:20px; height:20px; text-align:center; line-height:20px; text-decoration:none; font-weight:bold;">&times;</a>
							<input type="hidden" name="imob_galeria[]" value="<?php echo esc_attr($img_id); ?>">
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<button type="button" class="button" id="imob_add_gallery_images"><?php _e( 'Adicionar Imagens', 'imobiliaria-tema' ); ?></button>
	</div>
	
	<div class="imob-meta-section">
		<h4><?php _e( 'Outros', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-meta-row">
			<label for="imob_destaque"><?php _e( 'Destaque:', 'imobiliaria-tema' ); ?></label>
			<input type="checkbox" id="imob_destaque" name="imob_destaque" value="1" <?php checked( $destaque, 1 ); ?>>
			<span><?php _e( 'Exibir na home como destaque.', 'imobiliaria-tema' ); ?></span>
		</div>
	</div>

	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#imob_add_gallery_images').on('click', function(e) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Selecione imagens para a galeria',
				button: { text: 'Adicionar à galeria' },
				multiple: true
			});
			frame.on('select', function() {
				var selection = frame.state().get('selection');
				selection.map(function(attachment) {
					attachment = attachment.toJSON();
					var li = '<li data-id="'+attachment.id+'" style="position:relative; width: 100px; height: 100px; border: 1px solid #ccc;">' +
						'<img src="'+attachment.sizes.thumbnail.url+'" style="width:100%;height:100%;object-fit:cover;">' +
						'<a href="#" class="imob-remove-img" style="position:absolute; top:-5px; right:-5px; background:red; color:#fff; border-radius:50%; width:20px; height:20px; text-align:center; line-height:20px; text-decoration:none; font-weight:bold;">&times;</a>' +
						'<input type="hidden" name="imob_galeria[]" value="'+attachment.id+'">' +
						'</li>';
					$('#imob-gallery-list').append(li);
				});
			});
			frame.open();
		});

		$('#imob-gallery-list').on('click', '.imob-remove-img', function(e){
			e.preventDefault();
			$(this).parent('li').remove();
		});
	});
	</script>
	<?php
}

function imob_save_imovel_meta( $post_id ) {
	if ( ! isset( $_POST['imob_imovel_meta_nonce'] ) || ! wp_verify_nonce( $_POST['imob_imovel_meta_nonce'], 'imob_save_imovel_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'imob_ref',
		'imob_preco_venda',
		'imob_preco_antes_texto',
		'imob_preco_depois_texto',
		'imob_preco_anterior',
		'imob_preco_aluguel',
		'imob_condominio',
		'imob_iptu',
		'imob_area_privativa',
		'imob_area_total',
		'imob_quartos',
		'imob_suites',
		'imob_banheiros',
		'imob_vagas',
		'imob_video_url',
		'imob_lat',
		'imob_lng',
		'imob_endereco',
		'imob_construtora_id',
		'imob_proprietario_id',
		'imob_empreendimento_id',
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	$destaque = isset( $_POST['imob_destaque'] ) ? 1 : 0;
	update_post_meta( $post_id, '_imob_destaque', $destaque );

	if ( isset( $_POST['imob_galeria'] ) ) {
		$galeria = array_map( 'intval', $_POST['imob_galeria'] );
		update_post_meta( $post_id, '_imob_galeria', $galeria );
	} else {
		delete_post_meta( $post_id, '_imob_galeria' );
	}
}
add_action( 'save_post_imovel', 'imob_save_imovel_meta' );
