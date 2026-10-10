<?php
/**
 * Meta Boxes for Imóveis
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna o prefixo do código de referência baseado no tipo de imóvel
 */
function imob_get_tipo_prefix( $tipo_name ) {
	$map = array(
		'apartamento'  => 'Apt',
		'apto'         => 'Apt',
		'casa'         => 'Cas',
		'terreno'      => 'Ter',
		'lote'         => 'Lot',
		'loteamento'   => 'Lot',
		'galpao'       => 'Gal',
		'galpão'       => 'Gal',
		'sala'         => 'Sal',
		'comercial'    => 'Com',
		'cobertura'    => 'Cob',
		'flat'         => 'Fla',
		'studio'       => 'Stu',
		'estudio'      => 'Stu',
		'estúdio'      => 'Stu',
		'sitio'        => 'Sit',
		'sítio'        => 'Sit',
		'chacara'      => 'Cha',
		'chácara'      => 'Cha',
		'fazenda'      => 'Faz',
		'duplex'       => 'Dup',
		'triplex'      => 'Tri',
		'penthouse'    => 'Pen',
	);

	$normalized = mb_strtolower( trim( (string) $tipo_name ), 'UTF-8' );
	foreach ( $map as $key => $prefix ) {
		if ( mb_strpos( $normalized, $key ) !== false ) {
			return $prefix;
		}
	}

	// Fallback com as 3 primeiras letras
	$clean = preg_replace( '/[^A-Za-z]/', '', $normalized );
	if ( strlen( $clean ) >= 3 ) {
		return ucfirst( substr( $clean, 0, 3 ) );
	}

	return 'Imob';
}

/**
 * Gera um código de referência único para o imóvel no formato Prefixo-999999
 */
function imob_generate_unique_imovel_ref( $post_id ) {
	$current_ref = get_post_meta( $post_id, '_imob_ref', true );
	if ( ! empty( $current_ref ) ) {
		return $current_ref;
	}

	$tipos = wp_get_post_terms( $post_id, 'tipo_imovel' );
	$prefix = 'Imob';
	if ( ! empty( $tipos ) && ! is_wp_error( $tipos ) ) {
		$prefix = imob_get_tipo_prefix( $tipos[0]->name );
	}

	global $wpdb;
	$ref = '';
	$attempts = 0;
	do {
		$random_num = mt_rand( 100000, 999999 );
		$ref = $prefix . '-' . $random_num;
		$exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_imob_ref' AND meta_value = %s AND post_id != %d LIMIT 1",
			$ref,
			$post_id
		) );
		$attempts++;
	} while ( $exists && $attempts < 30 );

	update_post_meta( $post_id, '_imob_ref', $ref );
	return $ref;
}

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
	if ( ! is_array( $galeria ) && ! empty( $galeria ) ) {
		$galeria = explode( ',', $galeria );
	}
	$galeria = is_array( $galeria ) ? $galeria : [];

	$gmaps_key = get_option( 'imob_gmaps_key' );

	wp_enqueue_media();
	?>
	<style>
		.imob-meta-row { margin-bottom: 15px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
		.imob-meta-row label { width: 170px; display: inline-block; font-weight: bold; }
		.imob-meta-row input[type="text"], .imob-meta-row input[type="number"], .imob-meta-row select { width: 300px; }
		.imob-meta-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #ccd0d4; }
		.imob-ref-badge { display: inline-flex; align-items: center; background: #0E1A2B; color: #fff; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 0.95rem; }
		.imob-ref-pending { background: #f0f4f8; color: #555; border: 1px dashed #999; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; }
		.imob-quick-btn { background: #f6f7f7 !important; border: 1px solid #2271b1 !important; color: #2271b1 !important; border-radius: 4px; padding: 4px 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: 0.85rem; font-weight: 500; height: 32px; line-height: 1; }
		.imob-quick-btn:hover { background: #2271b1 !important; color: #fff !important; }
		
		/* Modais */
		.imob-modal-backdrop { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.65); z-index: 100050; align-items: center; justify-content: center; }
		.imob-modal-box { background: #fff; border-radius: 8px; width: 500px; max-width: 90%; box-shadow: 0 10px 30px rgba(0,0,0,0.3); overflow: hidden; animation: imobFadeIn 0.2s ease-out; }
		@keyframes imobFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
		.imob-modal-header { background: #0E1A2B; color: #fff; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
		.imob-modal-header h3 { margin: 0; color: #fff; font-size: 1.1rem; }
		.imob-modal-close { background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; line-height: 1; padding: 0; }
		.imob-modal-body { padding: 20px; max-height: 75vh; overflow-y: auto; }
		.imob-modal-field { margin-bottom: 12px; }
		.imob-modal-field label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 0.85rem; }
		.imob-modal-field input, .imob-modal-field select, .imob-modal-field textarea { width: 100%; box-sizing: border-box; }
		.imob-modal-footer { padding: 12px 20px; background: #f6f7f7; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px; }
	</style>
	
	<!-- Código de Referência (Automático e baseado no Tipo) -->
	<div class="imob-meta-row" style="background: #fafafa; padding: 12px; border-radius: 6px; border: 1px solid #e5e5e5;">
		<label style="width: 170px;"><?php _e( 'Código do Imóvel:', 'imobiliaria-tema' ); ?></label>
		<div>
			<?php if ( ! empty( $ref ) ) : ?>
				<span class="imob-ref-badge">
					<span class="dashicons dashicons-tag" style="margin-right: 4px; font-size: 17px; line-height: 1.2;"></span>
					<?php echo esc_html( $ref ); ?>
				</span>
				<p class="description" style="margin-top: 5px;"><?php _e( 'Código gerado automaticamente com base no tipo do imóvel.', 'imobiliaria-tema' ); ?></p>
			<?php else : ?>
				<span class="imob-ref-pending">
					<span class="dashicons dashicons-update" style="margin-right: 4px; font-size: 16px; line-height: 1.3;"></span>
					<?php _e( 'Será gerado automaticamente ao salvar (Ex: Apt-123456)', 'imobiliaria-tema' ); ?>
				</span>
				<p class="description" style="margin-top: 5px;"><?php _e( 'O código é gerado automaticamente combinando o prefixo do tipo selecionado e 6 dígitos aleatórios.', 'imobiliaria-tema' ); ?></p>
			<?php endif; ?>
			<input type="hidden" id="imob_ref" name="imob_ref" value="<?php echo esc_attr( $ref ); ?>">
		</div>
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

	<!-- Relacionamentos com Botões de Cadastro Rápido -->
	<div class="imob-meta-section">
		<h4><?php _e( 'Relacionamentos', 'imobiliaria-tema' ); ?></h4>
		<div class="imob-meta-row">
			<label for="imob_construtora_id"><?php _e( 'Construtora:', 'imobiliaria-tema' ); ?></label>
			<select name="imob_construtora_id" id="imob_construtora_id">
				<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
				<?php
				$construtoras = get_posts( array( 'post_type' => 'construtora', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
				foreach ( $construtoras as $const ) {
					echo '<option value="' . $const->ID . '" ' . selected( $construtora_id, $const->ID, false ) . '>' . esc_html( $const->post_title ) . '</option>';
				}
				?>
			</select>
			<button type="button" class="imob-quick-btn" data-target="modal-quick-construtora">
				<span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Nova Construtora', 'imobiliaria-tema' ); ?>
			</button>
		</div>

		<div class="imob-meta-row">
			<label for="imob_proprietario_id"><?php _e( 'Proprietário (Interno):', 'imobiliaria-tema' ); ?></label>
			<select name="imob_proprietario_id" id="imob_proprietario_id">
				<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
				<?php
				$proprietarios = get_posts( array( 'post_type' => 'proprietario', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
				foreach ( $proprietarios as $prop ) {
					echo '<option value="' . $prop->ID . '" ' . selected( $proprietario_id, $prop->ID, false ) . '>' . esc_html( $prop->post_title ) . '</option>';
				}
				?>
			</select>
			<button type="button" class="imob-quick-btn" data-target="modal-quick-proprietario">
				<span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Novo Proprietário', 'imobiliaria-tema' ); ?>
			</button>
		</div>

		<div class="imob-meta-row">
			<label for="imob_empreendimento_id"><?php _e( 'Empreendimento:', 'imobiliaria-tema' ); ?></label>
			<select name="imob_empreendimento_id" id="imob_empreendimento_id">
				<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
				<?php
				$empreendimentos = get_posts( array( 'post_type' => 'empreendimento', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
				foreach ( $empreendimentos as $emp ) {
					echo '<option value="' . $emp->ID . '" ' . selected( $empreendimento_id, $emp->ID, false ) . '>' . esc_html( $emp->post_title ) . '</option>';
				}
				?>
			</select>
			<button type="button" class="imob-quick-btn" data-target="modal-quick-empreendimento">
				<span class="dashicons dashicons-plus-alt2"></span> <?php _e( 'Novo Empreendimento', 'imobiliaria-tema' ); ?>
			</button>
		</div>
	</div>

	<!-- Mídia e Localização com Google Maps Interativo -->
	<div class="imob-meta-section">
		<h4><?php _e( 'Mídia e Localização no Mapa', 'imobiliaria-tema' ); ?></h4>
		
		<div class="imob-meta-row">
			<label for="imob_video_url"><?php _e( 'Vídeo URL:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_video_url" name="imob_video_url" value="<?php echo esc_attr( $video_url ); ?>">
		</div>

		<div style="background: #f8fafc; border: 1px solid #dce4ec; border-radius: 8px; padding: 15px; margin: 15px 0;">
			<h4 style="margin: 0 0 10px; color: #0E1A2B;">
				<span class="dashicons dashicons-location" style="vertical-align: middle;"></span>
				<?php _e( 'Seleção Direta no Google Maps (Campina Grande - PB)', 'imobiliaria-tema' ); ?>
			</h4>
			<p class="description" style="margin-bottom: 12px;">
				<?php _e( 'Clique em qualquer lugar no mapa ou arraste o marcador para definir as coordenadas e buscar o endereço automaticamente.', 'imobiliaria-tema' ); ?>
			</p>

			<div style="display: flex; gap: 8px; margin-bottom: 12px;">
				<input type="text" id="imob_map_search" placeholder="<?php _e( 'Digite rua, bairro ou ponto de referência para localizar...', 'imobiliaria-tema' ); ?>" style="flex: 1; height: 36px;">
				<button type="button" class="button button-primary" id="imob_btn_search_map" style="height: 36px; display: inline-flex; align-items: center; gap: 5px;">
					<span class="dashicons dashicons-search"></span> <?php _e( 'Buscar no Mapa', 'imobiliaria-tema' ); ?>
				</button>
			</div>

			<?php if ( $gmaps_key ) : ?>
				<div id="imob_admin_map" style="width: 100%; height: 380px; border-radius: 6px; border: 1px solid #ccc; background: #e9ecef;"></div>
			<?php else : ?>
				<div style="padding: 20px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 6px; color: #856404;">
					<strong><?php _e( 'Google Maps API Key não configurada:', 'imobiliaria-tema' ); ?></strong>
					<?php _e( 'Para habilitar o mapa interativo e a busca automática de endereços, insira sua chave em ', 'imobiliaria-tema' ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=imobiliaria-options' ) ); ?>" target="_blank" style="font-weight: 600; text-decoration: underline;"><?php _e( 'Opções do Tema', 'imobiliaria-tema' ); ?></a>.
				</div>
			<?php endif; ?>

			<div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
				<div style="flex: 1; min-width: 200px;">
					<label for="imob_lat" style="font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;"><?php _e( 'Latitude:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="imob_lat" name="imob_lat" value="<?php echo esc_attr( $lat ); ?>" style="width: 100%;">
				</div>
				<div style="flex: 1; min-width: 200px;">
					<label for="imob_lng" style="font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;"><?php _e( 'Longitude:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="imob_lng" name="imob_lng" value="<?php echo esc_attr( $lng ); ?>" style="width: 100%;">
				</div>
			</div>

			<div style="margin-top: 12px;">
				<label for="imob_endereco" style="font-weight: bold; font-size: 0.85rem; display: block; margin-bottom: 4px;"><?php _e( 'Endereço Completo:', 'imobiliaria-tema' ); ?></label>
				<input type="text" id="imob_endereco" name="imob_endereco" value="<?php echo esc_attr( $endereco ); ?>" placeholder="Ex: Rua João Pessoa, 123 - Centro, Campina Grande - PB" style="width: 100%;">
			</div>
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

	<!-- Modais de Cadastro Rápido -->
	<!-- Modal Construtora -->
	<div class="imob-modal-backdrop" id="modal-quick-construtora">
		<div class="imob-modal-box">
			<div class="imob-modal-header">
				<h3><?php _e( 'Cadastrar Construtora', 'imobiliaria-tema' ); ?></h3>
				<button type="button" class="imob-modal-close">&times;</button>
			</div>
			<div class="imob-modal-body">
				<div class="imob-modal-field">
					<label><?php _e( 'Nome da Construtora *', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_const_nome" placeholder="Ex: Rocha Construtora">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Telefone:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_const_telefone" placeholder="(83) 3333-0000">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'WhatsApp:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_const_whatsapp" placeholder="(83) 99999-0000">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Site:', 'imobiliaria-tema' ); ?></label>
					<input type="url" id="quick_const_site" placeholder="https://construtora.com.br">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Instagram:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_const_instagram" placeholder="@construtora">
				</div>
			</div>
			<div class="imob-modal-footer">
				<button type="button" class="button imob-modal-cancel"><?php _e( 'Cancelar', 'imobiliaria-tema' ); ?></button>
				<button type="button" class="button button-primary imob-btn-save-quick" data-type="construtora"><?php _e( 'Salvar Construtora', 'imobiliaria-tema' ); ?></button>
			</div>
		</div>
	</div>

	<!-- Modal Proprietário -->
	<div class="imob-modal-backdrop" id="modal-quick-proprietario">
		<div class="imob-modal-box">
			<div class="imob-modal-header">
				<h3><?php _e( 'Cadastrar Proprietário', 'imobiliaria-tema' ); ?></h3>
				<button type="button" class="imob-modal-close">&times;</button>
			</div>
			<div class="imob-modal-body">
				<div class="imob-modal-field">
					<label><?php _e( 'Nome do Proprietário *', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_prop_nome" placeholder="Ex: João da Silva">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Telefone:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_prop_telefone" placeholder="(83) 3333-0000">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'WhatsApp:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_prop_whatsapp" placeholder="(83) 99999-0000">
				</div>
			</div>
			<div class="imob-modal-footer">
				<button type="button" class="button imob-modal-cancel"><?php _e( 'Cancelar', 'imobiliaria-tema' ); ?></button>
				<button type="button" class="button button-primary imob-btn-save-quick" data-type="proprietario"><?php _e( 'Salvar Proprietário', 'imobiliaria-tema' ); ?></button>
			</div>
		</div>
	</div>

	<!-- Modal Empreendimento -->
	<div class="imob-modal-backdrop" id="modal-quick-empreendimento">
		<div class="imob-modal-box">
			<div class="imob-modal-header">
				<h3><?php _e( 'Cadastrar Empreendimento', 'imobiliaria-tema' ); ?></h3>
				<button type="button" class="imob-modal-close">&times;</button>
			</div>
			<div class="imob-modal-body">
				<div class="imob-modal-field">
					<label><?php _e( 'Nome do Empreendimento *', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_emp_nome" placeholder="Ex: Residencial Mirante da Serra">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Estágio da Obra:', 'imobiliaria-tema' ); ?></label>
					<select id="quick_emp_estagio">
						<?php
						$estagio_terms = get_terms( array( 'taxonomy' => 'estagio_obra', 'hide_empty' => false ) );
						if ( ! empty( $estagio_terms ) && ! is_wp_error( $estagio_terms ) ) {
							foreach ( $estagio_terms as $sterm ) {
								echo '<option value="' . esc_attr( $sterm->slug ) . '">' . esc_html( $sterm->name ) . '</option>';
							}
						} else {
							echo '<option value="lancamento">' . __( 'Lançamento', 'imobiliaria-tema' ) . '</option>';
							echo '<option value="em-construcao">' . __( 'Em Construção', 'imobiliaria-tema' ) . '</option>';
							echo '<option value="pronto">' . __( 'Pronto para Morar', 'imobiliaria-tema' ) . '</option>';
						}
						?>
					</select>
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Previsão de Entrega:', 'imobiliaria-tema' ); ?></label>
					<input type="text" id="quick_emp_previsao" placeholder="Ex: Dez/2026">
				</div>
				<div class="imob-modal-field">
					<label><?php _e( 'Construtora:', 'imobiliaria-tema' ); ?></label>
					<select id="quick_emp_construtora_id">
						<option value=""><?php _e( 'Selecione', 'imobiliaria-tema' ); ?></option>
						<?php
						foreach ( $construtoras as $const ) {
							echo '<option value="' . $const->ID . '">' . esc_html( $const->post_title ) . '</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="imob-modal-footer">
				<button type="button" class="button imob-modal-cancel"><?php _e( 'Cancelar', 'imobiliaria-tema' ); ?></button>
				<button type="button" class="button button-primary imob-btn-save-quick" data-type="empreendimento"><?php _e( 'Salvar Empreendimento', 'imobiliaria-tema' ); ?></button>
			</div>
		</div>
	</div>

	<!-- Scripts para Galeria, Modais e Google Maps -->
	<script>
	jQuery(document).ready(function($){
		// Isola os modais de cadastro rápido fora do form principal de publicação
		$('body').append($('#modal-quick-construtora, #modal-quick-proprietario, #modal-quick-empreendimento'));

		// --- 1. Galeria de Imagens ---
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
					var thumbUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
					var li = '<li data-id="'+attachment.id+'" style="position:relative; width: 100px; height: 100px; border: 1px solid #ccc;">' +
						'<img src="'+thumbUrl+'" style="width:100%;height:100%;object-fit:cover;">' +
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

		// --- 2. Modais de Cadastro Rápido ---
		$('.imob-quick-btn').on('click', function(e){
			e.preventDefault();
			var target = $(this).data('target');
			$('#' + target).css('display', 'flex');
		});

		$('.imob-modal-close, .imob-modal-cancel').on('click', function(e){
			e.preventDefault();
			$(this).closest('.imob-modal-backdrop').hide();
		});

		$('.imob-modal-backdrop').on('click', function(e){
			if ( $(e.target).hasClass('imob-modal-backdrop') ) {
				$(this).hide();
			}
		});

		$('.imob-btn-save-quick').on('click', function(e){
			e.preventDefault();
			var btn = $(this);
			var entityType = btn.data('type');
			var data = {
				action: 'imob_quick_create_relation',
				entity_type: entityType,
				nonce: '<?php echo wp_create_nonce("imob_quick_add_nonce"); ?>'
			};

			if ( entityType === 'construtora' ) {
				data.nome = $('#quick_const_nome').val().trim();
				data.telefone = $('#quick_const_telefone').val().trim();
				data.whatsapp = $('#quick_const_whatsapp').val().trim();
				data.site = $('#quick_const_site').val().trim();
				data.instagram = $('#quick_const_instagram').val().trim();
				if ( ! data.nome ) { alert('Por favor, informe o nome da construtora.'); return; }
			} else if ( entityType === 'proprietario' ) {
				data.nome = $('#quick_prop_nome').val().trim();
				data.telefone = $('#quick_prop_telefone').val().trim();
				data.whatsapp = $('#quick_prop_whatsapp').val().trim();
				if ( ! data.nome ) { alert('Por favor, informe o nome do proprietário.'); return; }
			} else if ( entityType === 'empreendimento' ) {
				data.nome = $('#quick_emp_nome').val().trim();
				data.estagio = $('#quick_emp_estagio').val();
				data.previsao = $('#quick_emp_previsao').val().trim();
				data.construtora_id = $('#quick_emp_construtora_id').val();
				if ( ! data.nome ) { alert('Por favor, informe o nome do empreendimento.'); return; }
			}

			btn.prop('disabled', true).text('Salvando...');

			$.post(ajaxurl, data, function(res){
				btn.prop('disabled', false).text('Salvar');
				if ( res && res.success ) {
					var newId = res.data.id;
					var newTitle = res.data.title;
					var selectTarget = '';

					if ( entityType === 'construtora' ) {
						selectTarget = '#imob_construtora_id';
						// Atualiza também o select de construtora dentro do modal de empreendimento
						$('#quick_emp_construtora_id').append('<option value="'+newId+'">'+newTitle+'</option>');
						$('#quick_const_nome, #quick_const_telefone, #quick_const_whatsapp, #quick_const_site, #quick_const_instagram').val('');
					} else if ( entityType === 'proprietario' ) {
						selectTarget = '#imob_proprietario_id';
						$('#quick_prop_nome, #quick_prop_telefone, #quick_prop_whatsapp').val('');
					} else if ( entityType === 'empreendimento' ) {
						selectTarget = '#imob_empreendimento_id';
						$('#quick_emp_nome, #quick_emp_previsao').val('');
					}

					$(selectTarget).append('<option value="'+newId+'">'+newTitle+'</option>');
					$(selectTarget).val(newId);
					$('#modal-quick-' + entityType).hide();
					alert(newTitle + ' cadastrado e selecionado com sucesso!');
				} else {
					alert(res && res.data ? res.data : 'Erro ao cadastrar.');
				}
			}).fail(function(){
				btn.prop('disabled', false).text('Salvar');
				alert('Erro na requisição.');
			});
		});

		// --- 3. Google Maps Interativo (Campina Grande - PB) ---
		var mapElement = document.getElementById('imob_admin_map');
		if ( mapElement && typeof google !== 'undefined' && google.maps ) {
			var campinaGrande = { lat: -7.2247, lng: -35.8816 };
			var currentLat = $('#imob_lat').val() ? parseFloat($('#imob_lat').val()) : null;
			var currentLng = $('#imob_lng').val() ? parseFloat($('#imob_lng').val()) : null;
			var initialCenter = (currentLat && currentLng) ? { lat: currentLat, lng: currentLng } : campinaGrande;
			var initialZoom = (currentLat && currentLng) ? 17 : 13;

			var map = new google.maps.Map(mapElement, {
				center: initialCenter,
				zoom: initialZoom,
				mapTypeId: 'roadmap',
				streetViewControl: false,
				mapTypeControl: true
			});

			var geocoder = new google.maps.Geocoder();
			var marker = new google.maps.Marker({
				position: initialCenter,
				map: map,
				draggable: true,
				title: 'Localização do Imóvel'
			});

			function updatePositionAndAddress(latLng, shouldGeocode) {
				var lat = latLng.lat().toFixed(6);
				var lng = latLng.lng().toFixed(6);
				$('#imob_lat').val(lat);
				$('#imob_lng').val(lng);

				if ( shouldGeocode !== false ) {
					geocoder.geocode({ location: latLng }, function(results, status) {
						if ( status === 'OK' && results[0] ) {
							$('#imob_endereco').val(results[0].formatted_address);
							if ( ! $('#imob_map_search').val() ) {
								$('#imob_map_search').val(results[0].formatted_address);
							}
						}
					});
				}
			}

			// Se já tinha coordenadas salvas, não sobrescreve endereço salvo
			if ( currentLat && currentLng ) {
				marker.setPosition(initialCenter);
			}

			// Clique no mapa
			google.maps.event.addListener(map, 'click', function(e) {
				marker.setPosition(e.latLng);
				updatePositionAndAddress(e.latLng, true);
			});

			// Arrastar marcador
			google.maps.event.addListener(marker, 'dragend', function(e) {
				updatePositionAndAddress(e.latLng, true);
			});

			// Busca de endereço no mapa
			function searchAddressOnMap() {
				var query = $('#imob_map_search').val().trim();
				if ( ! query ) {
					query = $('#imob_endereco').val().trim();
				}
				if ( ! query ) {
					alert('Digite um endereço para buscar no mapa.');
					return;
				}

				// Adiciona contexto de Campina Grande se não informado estado/cidade
				var searchAddress = query;
				if ( searchAddress.toLowerCase().indexOf('campina grande') === -1 ) {
					searchAddress += ', Campina Grande - PB';
				}

				geocoder.geocode({ address: searchAddress }, function(results, status) {
					if ( status === 'OK' && results[0] ) {
						var location = results[0].geometry.location;
						map.setCenter(location);
						map.setZoom(17);
						marker.setPosition(location);
						updatePositionAndAddress(location, false);
						if ( ! $('#imob_endereco').val() ) {
							$('#imob_endereco').val(results[0].formatted_address);
						}
					} else {
						alert('Endereço não localizado no Google Maps. Tente clicar diretamente no mapa.');
					}
				});
			}

			$('#imob_btn_search_map').on('click', function(e){
				e.preventDefault();
				searchAddressOnMap();
			});

			$('#imob_map_search').on('keypress', function(e){
				if ( e.which === 13 ) {
					e.preventDefault();
					searchAddressOnMap();
				}
			});
		}
	});
	</script>
	<?php
}

/**
 * Salva metadados do imóvel
 */
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

	// Atualiza coordenadas no formato lat,lng para _imob_mapa
	$lat = isset( $_POST['imob_lat'] ) ? sanitize_text_field( wp_unslash( $_POST['imob_lat'] ) ) : '';
	$lng = isset( $_POST['imob_lng'] ) ? sanitize_text_field( wp_unslash( $_POST['imob_lng'] ) ) : '';
	if ( ! empty( $lat ) && ! empty( $lng ) ) {
		update_post_meta( $post_id, '_imob_mapa', $lat . ',' . $lng );
	}

	// Geração automática do código de referência baseado no tipo de imóvel
	$ref = get_post_meta( $post_id, '_imob_ref', true );
	if ( empty( $ref ) ) {
		imob_generate_unique_imovel_ref( $post_id );
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
add_action( 'save_post_imovel', 'imob_save_imovel_meta', 10 );

/**
 * Garante que o código do imóvel seja gerado após os termos serem vinculados no salvamento
 */
function imob_ensure_imovel_ref_on_update( $post_id, $post ) {
	if ( $post->post_type !== 'imovel' || wp_is_post_revision( $post_id ) ) {
		return;
	}
	$ref = get_post_meta( $post_id, '_imob_ref', true );
	if ( empty( $ref ) ) {
		imob_generate_unique_imovel_ref( $post_id );
	}
}
add_action( 'wp_after_insert_post', 'imob_ensure_imovel_ref_on_update', 20, 2 );

/**
 * AJAX para cadastro rápido de Construtora, Proprietário ou Empreendimento
 */
function imob_ajax_quick_create_relation() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( __( 'Permissão negada.', 'imobiliaria-tema' ) );
	}

	check_ajax_referer( 'imob_quick_add_nonce', 'nonce' );

	$entity_type = isset( $_POST['entity_type'] ) ? sanitize_key( $_POST['entity_type'] ) : '';
	$nome = isset( $_POST['nome'] ) ? sanitize_text_field( wp_unslash( $_POST['nome'] ) ) : '';

	if ( empty( $nome ) ) {
		wp_send_json_error( __( 'O nome é obrigatório.', 'imobiliaria-tema' ) );
	}

	if ( ! in_array( $entity_type, array( 'construtora', 'proprietario', 'empreendimento' ), true ) ) {
		wp_send_json_error( __( 'Tipo inválido.', 'imobiliaria-tema' ) );
	}

	$post_id = wp_insert_post( array(
		'post_title'  => $nome,
		'post_type'   => $entity_type,
		'post_status' => 'publish',
	) );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_send_json_error( __( 'Erro ao criar registro.', 'imobiliaria-tema' ) );
	}

	if ( $entity_type === 'construtora' ) {
		if ( isset( $_POST['telefone'] ) ) update_post_meta( $post_id, '_imob_construtora_telefone', sanitize_text_field( wp_unslash( $_POST['telefone'] ) ) );
		if ( isset( $_POST['whatsapp'] ) ) update_post_meta( $post_id, '_imob_construtora_whatsapp', sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) );
		if ( isset( $_POST['site'] ) ) update_post_meta( $post_id, '_imob_construtora_site', esc_url_raw( wp_unslash( $_POST['site'] ) ) );
		if ( isset( $_POST['instagram'] ) ) update_post_meta( $post_id, '_imob_construtora_instagram', sanitize_text_field( wp_unslash( $_POST['instagram'] ) ) );
	} elseif ( $entity_type === 'proprietario' ) {
		if ( isset( $_POST['telefone'] ) ) update_post_meta( $post_id, '_imob_proprietario_telefone', sanitize_text_field( wp_unslash( $_POST['telefone'] ) ) );
		if ( isset( $_POST['whatsapp'] ) ) update_post_meta( $post_id, '_imob_proprietario_whatsapp', sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) );
	} elseif ( $entity_type === 'empreendimento' ) {
		if ( isset( $_POST['estagio'] ) && ! empty( $_POST['estagio'] ) ) {
			$estagio_val = sanitize_text_field( wp_unslash( $_POST['estagio'] ) );
			update_post_meta( $post_id, '_imob_emp_estagio', $estagio_val );
			wp_set_object_terms( $post_id, $estagio_val, 'estagio_obra' );
		}
		if ( isset( $_POST['previsao'] ) ) update_post_meta( $post_id, '_imob_emp_previsao', sanitize_text_field( wp_unslash( $_POST['previsao'] ) ) );
		if ( isset( $_POST['construtora_id'] ) ) update_post_meta( $post_id, '_imob_emp_construtora_id', intval( $_POST['construtora_id'] ) );
	}

	wp_send_json_success( array(
		'id'    => $post_id,
		'title' => $nome,
	) );
}
add_action( 'wp_ajax_imob_quick_create_relation', 'imob_ajax_quick_create_relation' );
