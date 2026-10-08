<?php
/**
 * Otimizador de Imagens, Limpeza de Imagens Órfãs e Restrição de Tamanhos de Imagem
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. REGISTRAR APENAS OS TAMANHOS DESEJADOS E LIMITAR GERAÇÃO DE IMAGENS
 * Define apenas imob_large (1280px) e imob_thumb (400x300 miniatura)
 */
function imob_setup_custom_image_sizes() {
	// 1280px para maiores detalhes (proporcional, sem corte)
	add_image_size( 'imob_large', 1280, 1280, false );

	// Miniatura 400x300 (com corte)
	add_image_size( 'imob_thumb', 400, 300, true );
}
add_action( 'after_setup_theme', 'imob_setup_custom_image_sizes' );

/**
 * Filtra os tamanhos intermediários gerados no upload para manter apenas os dois tamanhos requeridos:
 * 1280px para maiores detalhes e miniatura para listagens.
 */
function imob_limit_intermediate_image_sizes( $sizes, $image_meta ) {
	// Mantém apenas imob_large e imob_thumb
	$allowed = [
		'imob_large' => [
			'width'  => 1280,
			'height' => 1280,
			'crop'   => false,
		],
		'imob_thumb' => [
			'width'  => 400,
			'height' => 300,
			'crop'   => true,
		],
	];

	return $allowed;
}
add_filter( 'intermediate_image_sizes_advanced', 'imob_limit_intermediate_image_sizes', 99, 2 );

/**
 * Desabilita geração de imagens redundantes -scaled de alta resolução nativas do WP
 */
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * 2. APLICAÇÃO DE MARCA D'ÁGUA EM ARQUIVOS FÍSICOS
 *
 * @param string $file_path Caminho absoluto da imagem no disco.
 * @return bool Sucesso ou falso.
 */
function imob_apply_watermark_to_file( $file_path ) {
	if ( ! file_exists( $file_path ) ) {
		return false;
	}

	$watermark_id = get_option( 'imob_watermark_image' );
	if ( ! $watermark_id ) {
		return false; // Sem marca d'água configurada
	}

	$watermark_path = get_attached_file( $watermark_id );
	if ( ! $watermark_path || ! file_exists( $watermark_path ) ) {
		return false;
	}

	$img_info = @getimagesize( $file_path );
	if ( ! $img_info ) {
		return false;
	}

	$mime = $img_info['mime'];
	if ( $mime === 'image/jpeg' ) {
		$main_img = @imagecreatefromjpeg( $file_path );
	} elseif ( $mime === 'image/png' ) {
		$main_img = @imagecreatefrompng( $file_path );
	} else {
		return false;
	}

	if ( ! $main_img ) {
		return false;
	}

	$wtm_info = @getimagesize( $watermark_path );
	if ( ! $wtm_info ) {
		imagedestroy( $main_img );
		return false;
	}

	$wtm_mime = $wtm_info['mime'];
	if ( $wtm_mime === 'image/png' ) {
		$watermark = @imagecreatefrompng( $watermark_path );
	} elseif ( $wtm_mime === 'image/jpeg' ) {
		$watermark = @imagecreatefromjpeg( $watermark_path );
	} else {
		imagedestroy( $main_img );
		return false;
	}

	if ( ! $watermark ) {
		imagedestroy( $main_img );
		return false;
	}

	$main_w  = imagesx( $main_img );
	$main_h  = imagesy( $main_img );
	$wtm_w   = imagesx( $watermark );
	$wtm_h   = imagesy( $watermark );
	$opacity = intval( get_option( 'imob_watermark_opacity', 100 ) );
	$pos     = get_option( 'imob_watermark_position', 'center' );

	// Redimensiona marca d'água se for maior que a imagem
	if ( $wtm_w > $main_w || $wtm_h > $main_h ) {
		$scale     = min( max( 0.1, ($main_w - 40) / $wtm_w ), max( 0.1, ($main_h - 40) / $wtm_h ) );
		$new_wtm_w = max( 1, intval( $wtm_w * $scale ) );
		$new_wtm_h = max( 1, intval( $wtm_h * $scale ) );

		$new_watermark = imagecreatetruecolor( $new_wtm_w, $new_wtm_h );
		imagealphablending( $new_watermark, false );
		imagesavealpha( $new_watermark, true );
		$transparent = imagecolorallocatealpha( $new_watermark, 255, 255, 255, 127 );
		imagefilledrectangle( $new_watermark, 0, 0, $new_wtm_w, $new_wtm_h, $transparent );

		imagecopyresampled( $new_watermark, $watermark, 0, 0, 0, 0, $new_wtm_w, $new_wtm_h, $wtm_w, $wtm_h );
		imagedestroy( $watermark );

		$watermark = $new_watermark;
		$wtm_w     = $new_wtm_w;
		$wtm_h     = $new_wtm_h;
	}

	$padding = 20;
	$dest_x  = 0;
	$dest_y  = 0;

	switch ( $pos ) {
		case 'bottom_right':
			$dest_x = $main_w - $wtm_w - $padding;
			$dest_y = $main_h - $wtm_h - $padding;
			break;
		case 'bottom_left':
			$dest_x = $padding;
			$dest_y = $main_h - $wtm_h - $padding;
			break;
		case 'top_right':
			$dest_x = $main_w - $wtm_w - $padding;
			$dest_y = $padding;
			break;
		case 'top_left':
			$dest_x = $padding;
			$dest_y = $padding;
			break;
		case 'center':
		default:
			$dest_x = ($main_w / 2) - ($wtm_w / 2);
			$dest_y = ($main_h / 2) - ($wtm_h / 2);
			break;
	}

	$dest_x = max( 0, (int) $dest_x );
	$dest_y = max( 0, (int) $dest_y );

	imagealphablending( $main_img, true );
	imagesavealpha( $main_img, true );

	if ( $opacity >= 100 ) {
		imagecopy( $main_img, $watermark, $dest_x, $dest_y, 0, 0, $wtm_w, $wtm_h );
	} else {
		$cut = imagecreatetruecolor( $wtm_w, $wtm_h );
		imagecopy( $cut, $main_img, 0, 0, $dest_x, $dest_y, $wtm_w, $wtm_h );
		imagecopy( $cut, $watermark, 0, 0, 0, 0, $wtm_w, $wtm_h );
		imagecopymerge( $main_img, $cut, $dest_x, $dest_y, 0, 0, $wtm_w, $wtm_h, $opacity );
		imagedestroy( $cut );
	}

	if ( $mime === 'image/jpeg' ) {
		imagejpeg( $main_img, $file_path, 90 );
	} else {
		imagepng( $main_img, $file_path );
	}

	imagedestroy( $main_img );
	imagedestroy( $watermark );

	return true;
}

/**
 * 3. OTIMIZAR UM ANEXO DE IMAGEM:
 * - Aplica marca d'água no original
 * - Gera imob_large (1280px) e imob_thumb (400x300)
 * - Apaga do disco todas as outras resoluções antigas
 *
 * @param int $attachment_id ID do anexo.
 * @return array Resultado do processamento.
 */
function imob_process_optimize_attachment( $attachment_id ) {
	$attachment_id = intval( $attachment_id );
	$file_path     = get_attached_file( $attachment_id );

	if ( ! $file_path || ! file_exists( $file_path ) ) {
		return [
			'success' => false,
			'message' => "Arquivo original do anexo #{$attachment_id} não encontrado no disco.",
		];
	}

	$meta       = wp_get_attachment_metadata( $attachment_id );
	$upload_dir = dirname( $file_path );
	$deleted_files_count = 0;

	// Apagar resoluções antigas do disco
	if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $size_key => $size_data ) {
			if ( ! empty( $size_data['file'] ) ) {
				$old_sub_file = trailingslashit( $upload_dir ) . $size_data['file'];
				if ( file_exists( $old_sub_file ) && $old_sub_file !== $file_path ) {
					@unlink( $old_sub_file );
					$deleted_files_count++;
				}
			}
		}
	}

	// Aplicar marca d'água na imagem original (se configurada)
	$watermark_applied = imob_apply_watermark_to_file( $file_path );

	// Gerar as duas novas resoluções com WP Image Editor
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$new_sizes = [];

	// 1. imob_large (1280px max proporção)
	$editor_large = wp_get_image_editor( $file_path );
	if ( ! is_wp_error( $editor_large ) ) {
		$editor_large->resize( 1280, 1280, false );
		$filename_large = $editor_large->generate_filename( '1280x' );
		$saved_large    = $editor_large->save( $filename_large );
		if ( ! is_wp_error( $saved_large ) ) {
			$new_sizes['imob_large'] = [
				'file'      => wp_basename( $saved_large['path'] ),
				'width'     => $saved_large['width'],
				'height'    => $saved_large['height'],
				'mime-type' => $saved_large['mime-type'],
			];
		}
	}

	// 2. imob_thumb (400x300 miniatura cortada)
	$editor_thumb = wp_get_image_editor( $file_path );
	if ( ! is_wp_error( $editor_thumb ) ) {
		$editor_thumb->resize( 400, 300, true );
		$filename_thumb = $editor_thumb->generate_filename( '400x300' );
		$saved_thumb    = $editor_thumb->save( $filename_thumb );
		if ( ! is_wp_error( $saved_thumb ) ) {
			$new_sizes['imob_thumb'] = [
				'file'      => wp_basename( $saved_thumb['path'] ),
				'width'     => $saved_thumb['width'],
				'height'    => $saved_thumb['height'],
				'mime-type' => $saved_thumb['mime-type'],
			];
		}
	}

	// Atualizar metadados do anexo
	if ( ! is_array( $meta ) ) {
		$meta = wp_generate_attachment_metadata( $attachment_id, $file_path );
	}
	$meta['sizes'] = $new_sizes;
	wp_update_attachment_metadata( $attachment_id, $meta );

	return [
		'success'            => true,
		'id'                 => $attachment_id,
		'title'              => get_the_title( $attachment_id ) ?: wp_basename( $file_path ),
		'watermark'          => $watermark_applied,
		'deleted_old_sizes'  => $deleted_files_count,
		'new_sizes'          => array_keys( $new_sizes ),
	];
}

/**
 * 4. BUSCAR TODOS OS ATTACHMENT IDs PERTENCENTES A IMÓVEIS
 *
 * @return array Lista de IDs únicos de anexos dos imóveis.
 */
function imob_get_all_imovel_image_ids() {
	global $wpdb;

	$attachment_ids = [];

	// 1. Imagens destacadas de imóveis
	$thumb_ids = $wpdb->get_col( "
		SELECT DISTINCT pm.meta_value 
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type = 'imovel' 
		  AND pm.meta_key = '_thumbnail_id' 
		  AND pm.meta_value > 0
	" );

	if ( ! empty( $thumb_ids ) ) {
		foreach ( $thumb_ids as $tid ) {
			$attachment_ids[] = intval( $tid );
		}
	}

	// 2. Galerias de fotos (_imob_galeria)
	$galerias = $wpdb->get_col( "
		SELECT DISTINCT pm.meta_value 
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type = 'imovel' 
		  AND pm.meta_key = '_imob_galeria'
	" );

	if ( ! empty( $galerias ) ) {
		foreach ( $galerias as $raw_gal ) {
			$unserialized = maybe_unserialize( $raw_gal );
			if ( is_array( $unserialized ) ) {
				foreach ( $unserialized as $gid ) {
					if ( is_numeric( $gid ) && intval( $gid ) > 0 ) {
						$attachment_ids[] = intval( $gid );
					}
				}
			} elseif ( is_numeric( $raw_gal ) && intval( $raw_gal ) > 0 ) {
				$attachment_ids[] = intval( $raw_gal );
			}
		}
	}

	// 3. Imagens filhas de posts imovel (post_parent = imovel)
	$parent_attachments = $wpdb->get_col( "
		SELECT p_att.ID 
		FROM {$wpdb->posts} p_att
		INNER JOIN {$wpdb->posts} p_parent ON p_parent.ID = p_att.post_parent
		WHERE p_parent.post_type = 'imovel'
		  AND p_att.post_type = 'attachment'
		  AND p_att.post_mime_type LIKE 'image/%'
	" );

	if ( ! empty( $parent_attachments ) ) {
		foreach ( $parent_attachments as $paid ) {
			$attachment_ids[] = intval( $paid );
		}
	}

	$attachment_ids = array_unique( array_filter( $attachment_ids ) );
	return array_values( $attachment_ids );
}

/**
 * 5. BUSCAR TODAS AS IMAGENS ÓRFÃS DA BIBLIOTECA
 * Imagens que não pertencem a nenhum imóvel, nem construtora, empreendimento,
 * proprietário, usuário, logo, marca d'água ou conteúdo de post.
 *
 * @return array Lista de IDs de anexos órfãos.
 */
function imob_get_orphan_image_ids() {
	global $wpdb;

	// Todas as imagens cadastradas
	$all_image_ids = $wpdb->get_col( "
		SELECT ID 
		FROM {$wpdb->posts} 
		WHERE post_type = 'attachment' 
		  AND post_mime_type LIKE 'image/%'
	" );

	if ( empty( $all_image_ids ) ) {
		return [];
	}

	$all_image_ids = array_map( 'intval', $all_image_ids );
	$used_ids      = [];

	// 1. Thumbnails de qualquer post/página/cpt
	$thumb_ids = $wpdb->get_col( "
		SELECT DISTINCT meta_value 
		FROM {$wpdb->postmeta} 
		WHERE meta_key = '_thumbnail_id' 
		  AND meta_value > 0
	" );
	foreach ( $thumb_ids as $tid ) {
		$used_ids[ intval( $tid ) ] = true;
	}

	// 2. Metas com arrays de imagens (_imob_galeria, galeria_fotos, etc.)
	$meta_galerias = $wpdb->get_col( "
		SELECT DISTINCT meta_value 
		FROM {$wpdb->postmeta} 
		WHERE meta_key IN ('_imob_galeria', '_construtora_galeria', '_empreendimento_galeria', '_imob_planta')
	" );
	foreach ( $meta_galerias as $raw_meta ) {
		$unserialized = maybe_unserialize( $raw_meta );
		if ( is_array( $unserialized ) ) {
			foreach ( $unserialized as $mid ) {
				if ( is_numeric( $mid ) && intval( $mid ) > 0 ) {
					$used_ids[ intval( $mid ) ] = true;
				}
			}
		} elseif ( is_numeric( $raw_meta ) && intval( $raw_meta ) > 0 ) {
			$used_ids[ intval( $raw_meta ) ] = true;
		}
	}

	// 3. Imagens de perfil de usuários
	$user_fotos = $wpdb->get_col( "
		SELECT DISTINCT meta_value 
		FROM {$wpdb->usermeta} 
		WHERE meta_key IN ('imob_user_foto', 'foto') 
		  AND meta_value > 0
	" );
	foreach ( $user_fotos as $ufid ) {
		$used_ids[ intval( $ufid ) ] = true;
	}

	// 4. Marca d'água, logo do site e ícone do site
	$wm_id = intval( get_option( 'imob_watermark_image' ) );
	if ( $wm_id ) {
		$used_ids[ $wm_id ] = true;
	}
	$logo_id = intval( get_option( 'site_logo' ) ?: get_theme_mod( 'custom_logo' ) );
	if ( $logo_id ) {
		$used_ids[ $logo_id ] = true;
	}
	$icon_id = intval( get_option( 'site_icon' ) );
	if ( $icon_id ) {
		$used_ids[ $icon_id ] = true;
	}

	// 5. Filhos com post_parent existente e publicado
	$parented = $wpdb->get_col( "
		SELECT DISTINCT p_att.ID 
		FROM {$wpdb->posts} p_att
		INNER JOIN {$wpdb->posts} p_parent ON p_parent.ID = p_att.post_parent
		WHERE p_att.post_type = 'attachment' 
		  AND p_parent.post_status != 'trash'
	" );
	foreach ( $parented as $pid ) {
		$used_ids[ intval( $pid ) ] = true;
	}

	// Filtra anexos que não estão em uso
	$orphan_ids = [];
	foreach ( $all_image_ids as $aid ) {
		if ( ! isset( $used_ids[ $aid ] ) ) {
			$orphan_ids[] = $aid;
		}
	}

	return $orphan_ids;
}

/**
 * 6. ENDPOINTS AJAX PARA O PAINEL DE CONTROLE DO TEMA
 */

// Buscar lista de IDs de imagens dos imóveis
function imob_ajax_get_imovel_image_ids() {
	check_ajax_referer( 'imob_optimizer_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Sem permissão.' );
	}

	$ids = imob_get_all_imovel_image_ids();
	wp_send_json_success( [
		'total' => count( $ids ),
		'ids'   => $ids,
	] );
}
add_action( 'wp_ajax_imob_get_imovel_image_ids', 'imob_ajax_get_imovel_image_ids' );

// Processar otimização de uma única imagem de imóvel
function imob_ajax_optimize_single_image() {
	check_ajax_referer( 'imob_optimizer_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Sem permissão.' );
	}

	$attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
	if ( ! $attachment_id ) {
		wp_send_json_error( 'ID inválido.' );
	}

	$result = imob_process_optimize_attachment( $attachment_id );
	if ( $result['success'] ) {
		wp_send_json_success( $result );
	} else {
		wp_send_json_error( $result );
	}
}
add_action( 'wp_ajax_imob_optimize_single_image', 'imob_ajax_optimize_single_image' );

// Buscar lista de IDs de imagens órfãs
function imob_ajax_get_orphan_image_ids() {
	check_ajax_referer( 'imob_optimizer_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Sem permissão.' );
	}

	$ids = imob_get_orphan_image_ids();
	wp_send_json_success( [
		'total' => count( $ids ),
		'ids'   => $ids,
	] );
}
add_action( 'wp_ajax_imob_get_orphan_image_ids', 'imob_ajax_get_orphan_image_ids' );

// Excluir uma imagem órfã
function imob_ajax_delete_single_orphan() {
	check_ajax_referer( 'imob_optimizer_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Sem permissão.' );
	}

	$attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
	if ( ! $attachment_id ) {
		wp_send_json_error( 'ID inválido.' );
	}

	$file_path = get_attached_file( $attachment_id );
	$title     = get_the_title( $attachment_id ) ?: wp_basename( $file_path );

	// wp_delete_attachment com true apaga os arquivos físicos e registros do banco
	$deleted = wp_delete_attachment( $attachment_id, true );

	if ( $deleted ) {
		wp_send_json_success( [
			'id'    => $attachment_id,
			'title' => $title,
		] );
	} else {
		wp_send_json_error( [
			'message' => "Falha ao excluir o anexo #{$attachment_id}.",
		] );
	}
}
add_action( 'wp_ajax_imob_delete_single_orphan', 'imob_ajax_delete_single_orphan' );
