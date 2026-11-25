<?php
/**
 * Imagens de imóveis: conversão para JPG, tamanhos oficiais, marca d'água,
 * limpeza de imagens órfãs e fila de processamento em segundo plano.
 *
 * Regras das imagens de imóveis e empreendimentos:
 * - o arquivo principal vira JPG com no máximo 1280px no maior lado;
 * - só existe mais uma resolução: a miniatura imob_thumb (400x300, cortada);
 * - o original enviado e todas as outras resoluções são apagados do disco;
 * - a marca d'água é aplicada uma única vez (meta _imob_watermarked).
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'IMOB_IMG_MAX' ) ) {
	define( 'IMOB_IMG_MAX', 1280 );
}
if ( ! defined( 'IMOB_IMG_THUMB_W' ) ) {
	define( 'IMOB_IMG_THUMB_W', 400 );
}
if ( ! defined( 'IMOB_IMG_THUMB_H' ) ) {
	define( 'IMOB_IMG_THUMB_H', 300 );
}
if ( ! defined( 'IMOB_IMG_JPEG_QUALITY' ) ) {
	define( 'IMOB_IMG_JPEG_QUALITY', 85 );
}
if ( ! defined( 'IMOB_IMG_VERSION' ) ) {
	define( 'IMOB_IMG_VERSION', '2' );
}

/* -------------------------------------------------------------------------
 * 1. TAMANHOS DE IMAGEM
 * ---------------------------------------------------------------------- */

function imob_setup_custom_image_sizes() {
	// 1280px para maiores detalhes (proporcional, sem corte).
	add_image_size( 'imob_large', IMOB_IMG_MAX, IMOB_IMG_MAX, false );
	// Miniatura 400x300 (com corte) para listagens.
	add_image_size( 'imob_thumb', IMOB_IMG_THUMB_W, IMOB_IMG_THUMB_H, true );
}
add_action( 'after_setup_theme', 'imob_setup_custom_image_sizes' );

/**
 * Gera no upload apenas imob_large e imob_thumb. Nas imagens de imóveis o
 * arquivo principal já chega com 1280px, então só a miniatura é criada.
 */
function imob_limit_intermediate_image_sizes( $sizes, $image_meta ) {
	return array(
		'imob_large' => array(
			'width'  => IMOB_IMG_MAX,
			'height' => IMOB_IMG_MAX,
			'crop'   => false,
		),
		'imob_thumb' => array(
			'width'  => IMOB_IMG_THUMB_W,
			'height' => IMOB_IMG_THUMB_H,
			'crop'   => true,
		),
	);
}
add_filter( 'intermediate_image_sizes_advanced', 'imob_limit_intermediate_image_sizes', 99, 2 );

// Sem a cópia "-scaled" do WordPress.
add_filter( 'big_image_size_threshold', '__return_false' );

/* -------------------------------------------------------------------------
 * 2. CONVERSÃO E PROCESSAMENTO DE UM ANEXO
 * ---------------------------------------------------------------------- */

/**
 * Formatos que podem ser convertidos para JPG.
 *
 * @return string[]
 */
function imob_img_convertible_mimes() {
	return array( 'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/heic', 'image/heif', 'image/bmp' );
}

/**
 * Gera o arquivo principal em JPG com no máximo 1280px a partir de $path.
 * Não apaga o arquivo de origem.
 *
 * @param string $path Caminho absoluto da imagem de origem.
 * @return array|WP_Error { file, width, height }
 */
function imob_img_make_main_jpg( $path ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_raise_memory_limit( 'image' );

	$mime = wp_get_image_mime( $path );
	if ( ! $mime || ! in_array( $mime, imob_img_convertible_mimes(), true ) ) {
		return new WP_Error( 'imob_img_mime', 'Formato de imagem não suportado (' . ( $mime ? $mime : 'desconhecido' ) . ').' );
	}

	$editor = wp_get_image_editor( $path );
	if ( is_wp_error( $editor ) ) {
		return $editor;
	}

	// Fotos de celular: gira de acordo com o EXIF antes de descartar os metadados.
	if ( method_exists( $editor, 'maybe_exif_rotate' ) ) {
		$editor->maybe_exif_rotate();
	}

	$size = $editor->get_size();
	if ( $size['width'] > IMOB_IMG_MAX || $size['height'] > IMOB_IMG_MAX ) {
		$resized = $editor->resize( IMOB_IMG_MAX, IMOB_IMG_MAX, false );
		if ( is_wp_error( $resized ) ) {
			return $resized;
		}
	}

	$dir  = dirname( $path );
	$stem = pathinfo( $path, PATHINFO_FILENAME );

	if ( 'image/jpeg' === $mime && preg_match( '/\.jpe?g$/i', $path ) ) {
		$target = $path;
	} else {
		$target = trailingslashit( $dir ) . wp_unique_filename( $dir, $stem . '.jpg' );
	}

	$editor->set_quality( IMOB_IMG_JPEG_QUALITY );

	if ( 'image/jpeg' === $mime ) {
		$saved = $editor->save( $target, 'image/jpeg' );
	} else {
		// PNG/WebP/GIF podem ter transparência: achata sobre fundo branco
		// (salvar direto em JPG deixaria o fundo preto).
		$saved = imob_img_save_flattened_jpg( $editor, $dir, $stem, $target );
	}

	if ( is_wp_error( $saved ) ) {
		return $saved;
	}

	return array(
		'file'   => $target,
		'width'  => (int) $saved['width'],
		'height' => (int) $saved['height'],
	);
}

/**
 * Salva o conteúdo do editor como JPG sobre fundo branco.
 *
 * @param WP_Image_Editor $editor Editor com a imagem já reduzida.
 * @param string          $dir    Pasta de destino.
 * @param string          $stem   Nome base.
 * @param string          $target Caminho final .jpg.
 * @return array|WP_Error
 */
function imob_img_save_flattened_jpg( $editor, $dir, $stem, $target ) {
	if ( function_exists( 'imagecreatetruecolor' ) ) {
		$tmp       = trailingslashit( $dir ) . wp_unique_filename( $dir, $stem . '-imobtmp.png' );
		$saved_tmp = $editor->save( $tmp, 'image/png' );

		if ( ! is_wp_error( $saved_tmp ) ) {
			$src = imob_gd_load( $saved_tmp['path'] );
			if ( $src ) {
				$w      = imagesx( $src );
				$h      = imagesy( $src );
				$canvas = imagecreatetruecolor( $w, $h );
				imagefill( $canvas, 0, 0, imagecolorallocate( $canvas, 255, 255, 255 ) );
				imagealphablending( $canvas, true );
				imagecopy( $canvas, $src, 0, 0, 0, 0, $w, $h );
				$ok = imagejpeg( $canvas, $target, IMOB_IMG_JPEG_QUALITY );
				imagedestroy( $canvas );
				imagedestroy( $src );
				wp_delete_file( $saved_tmp['path'] );

				if ( $ok ) {
					return array(
						'path'   => $target,
						'width'  => $w,
						'height' => $h,
					);
				}
			} else {
				wp_delete_file( $saved_tmp['path'] );
			}
		}
	}

	return $editor->save( $target, 'image/jpeg' );
}

/**
 * O que falta fazer em um anexo para seguir as regras das imagens de imóveis.
 *
 * @param int $attachment_id ID do anexo.
 * @return array { convert: bool, watermark: bool, cleanup: bool, any: bool }
 */
function imob_img_needs_processing( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	$none          = array(
		'convert'   => false,
		'watermark' => false,
		'cleanup'   => false,
		'any'       => false,
	);

	if ( ! $attachment_id || in_array( $attachment_id, imob_img_protected_ids(), true ) ) {
		return $none;
	}

	$mime = get_post_mime_type( $attachment_id );
	if ( ! $mime || ! in_array( $mime, imob_img_convertible_mimes(), true ) ) {
		return $none;
	}

	$file = get_attached_file( $attachment_id );
	$meta = wp_get_attachment_metadata( $attachment_id );
	$meta = is_array( $meta ) ? $meta : array();

	$w = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
	$h = isset( $meta['height'] ) ? (int) $meta['height'] : 0;
	if ( ( ! $w || ! $h ) && $file && file_exists( $file ) ) {
		$dims = wp_getimagesize( $file );
		$w    = $dims ? (int) $dims[0] : 0;
		$h    = $dims ? (int) $dims[1] : 0;
	}

	$convert = ( 'image/jpeg' !== $mime )
		|| ( $file && ! preg_match( '/\.jpe?g$/i', $file ) )
		|| $w > IMOB_IMG_MAX
		|| $h > IMOB_IMG_MAX
		|| ! empty( $meta['original_image'] );

	$extra_sizes = ! empty( $meta['sizes'] ) && array_diff( array_keys( (array) $meta['sizes'] ), array( 'imob_thumb' ) );

	$watermark = imob_get_watermark_path() && ! imob_attachment_is_watermarked( $attachment_id );

	$result = array(
		'convert'   => $convert,
		'watermark' => $watermark,
		'cleanup'   => (bool) $extra_sizes,
	);
	$result['any'] = $result['convert'] || $result['watermark'] || $result['cleanup'];

	return $result;
}

/**
 * Anexos que nunca devem ser convertidos nem receber marca d'água
 * (a própria marca d'água, logo e ícone do site).
 *
 * @return int[]
 */
function imob_img_protected_ids() {
	$ids = array(
		(int) get_option( 'imob_watermark_image' ),
		(int) get_option( 'site_logo' ),
		(int) get_theme_mod( 'custom_logo' ),
		(int) get_option( 'site_icon' ),
	);
	return array_values( array_filter( $ids ) );
}

/**
 * Otimiza um anexo de imóvel:
 * - converte para JPG com no máximo 1280px (o original é apagado);
 * - aplica a marca d'água, se ainda não tiver;
 * - recria só a miniatura 400x300 e apaga todas as outras resoluções.
 *
 * @param int $attachment_id ID do anexo.
 * @return array Resultado do processamento.
 */
function imob_process_optimize_attachment( $attachment_id ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$attachment_id = (int) $attachment_id;
	$file_path     = get_attached_file( $attachment_id );
	$title         = get_the_title( $attachment_id );
	$title         = $title ? $title : ( $file_path ? wp_basename( $file_path ) : '#' . $attachment_id );

	if ( ! $file_path || ! file_exists( $file_path ) ) {
		return array(
			'success' => false,
			'id'      => $attachment_id,
			'title'   => $title,
			'message' => "Arquivo do anexo #{$attachment_id} não encontrado no disco.",
		);
	}

	$need = imob_img_needs_processing( $attachment_id );
	if ( ! $need['any'] ) {
		if ( '' === (string) get_post_meta( $attachment_id, '_imob_img_processed', true ) && in_array( get_post_mime_type( $attachment_id ), imob_img_convertible_mimes(), true ) ) {
			update_post_meta( $attachment_id, '_imob_img_processed', IMOB_IMG_VERSION );
		}
		return array(
			'success' => true,
			'skipped' => true,
			'id'      => $attachment_id,
			'title'   => $title,
			'message' => 'já estava otimizada',
		);
	}

	$meta     = wp_get_attachment_metadata( $attachment_id );
	$meta     = is_array( $meta ) ? $meta : array();
	$dir      = trailingslashit( dirname( $file_path ) );
	$old_mime = get_post_mime_type( $attachment_id );

	// Arquivos antigos que vão sair do disco.
	$old_files = array();
	if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
		foreach ( $meta['sizes'] as $size_data ) {
			if ( ! empty( $size_data['file'] ) ) {
				$old_files[] = $dir . $size_data['file'];
			}
		}
	}
	if ( ! empty( $meta['original_image'] ) ) {
		$old_files[] = $dir . $meta['original_image'];
	}

	// 1. JPG com no máximo 1280px.
	$new_file = $file_path;
	if ( $need['convert'] ) {
		$main = imob_img_make_main_jpg( $file_path );
		if ( is_wp_error( $main ) ) {
			return array(
				'success' => false,
				'id'      => $attachment_id,
				'title'   => $title,
				'message' => 'Falha ao converter: ' . $main->get_error_message(),
			);
		}
		$new_file = $main['file'];
	}

	// 2. Marca d'água (uma única vez).
	$watermark_applied = false;
	if ( $need['watermark'] ) {
		$watermark_applied = imob_apply_watermark_to_file( $new_file );
	}

	// 3. Apaga o original e todas as resoluções antigas.
	$deleted = 0;
	foreach ( array_unique( $old_files ) as $old ) {
		if ( $old !== $new_file && file_exists( $old ) ) {
			wp_delete_file( $old );
			$deleted++;
		}
	}
	if ( $file_path !== $new_file && file_exists( $file_path ) ) {
		wp_delete_file( $file_path );
		$deleted++;
	}

	// 4. Miniatura 400x300 (gerada depois da marca d'água).
	$sizes  = array();
	$editor = wp_get_image_editor( $new_file );
	if ( ! is_wp_error( $editor ) ) {
		$editor->set_quality( IMOB_IMG_JPEG_QUALITY );
		$resized = $editor->resize( IMOB_IMG_THUMB_W, IMOB_IMG_THUMB_H, true );
		if ( ! is_wp_error( $resized ) ) {
			$saved = $editor->save( $editor->generate_filename(), 'image/jpeg' );
			if ( ! is_wp_error( $saved ) ) {
				$sizes['imob_thumb'] = array(
					'file'      => wp_basename( $saved['path'] ),
					'width'     => (int) $saved['width'],
					'height'    => (int) $saved['height'],
					'mime-type' => 'image/jpeg',
					'filesize'  => (int) @filesize( $saved['path'] ),
				);
			}
		}
	}

	// 5. Metadados.
	$dims = wp_getimagesize( $new_file );
	clearstatcache( true, $new_file );

	if ( $new_file !== $file_path ) {
		update_attached_file( $attachment_id, $new_file );
	}

	$meta['file']     = _wp_relative_upload_path( $new_file );
	$meta['width']    = $dims ? (int) $dims[0] : ( isset( $meta['width'] ) ? (int) $meta['width'] : 0 );
	$meta['height']   = $dims ? (int) $dims[1] : ( isset( $meta['height'] ) ? (int) $meta['height'] : 0 );
	$meta['filesize'] = (int) @filesize( $new_file );
	$meta['sizes']    = $sizes;
	unset( $meta['original_image'] );
	wp_update_attachment_metadata( $attachment_id, $meta );

	if ( 'image/jpeg' !== $old_mime ) {
		global $wpdb;
		$wpdb->update( $wpdb->posts, array( 'post_mime_type' => 'image/jpeg' ), array( 'ID' => $attachment_id ) );
		clean_post_cache( $attachment_id );
	}

	update_post_meta( $attachment_id, '_imob_img_processed', IMOB_IMG_VERSION );

	if ( $watermark_applied || ( ! $need['watermark'] && imob_attachment_is_watermarked( $attachment_id ) ) ) {
		update_post_meta( $attachment_id, '_imob_watermarked', '1' );
	}

	$message = array();
	if ( $need['convert'] ) {
		$message[] = 'convertida para JPG ' . $meta['width'] . 'x' . $meta['height'];
	}
	if ( $watermark_applied ) {
		$message[] = "com marca d'água";
	} elseif ( $need['watermark'] ) {
		$message[] = "ATENÇÃO: não foi possível aplicar a marca d'água";
	}
	$message[] = $deleted . ' arquivo(s) antigo(s) removido(s)';

	return array(
		'success'           => true,
		'id'                => $attachment_id,
		'title'             => $title,
		'watermark'         => $watermark_applied,
		'watermark_failed'  => $need['watermark'] && ! $watermark_applied,
		'deleted_old_sizes' => $deleted,
		'new_sizes'         => array_keys( $sizes ),
		'message'           => implode( ', ', $message ),
	);
}

/* -------------------------------------------------------------------------
 * 3. UPLOAD: CONVERTE, REDUZ E MARCA ANTES DE GERAR A MINIATURA
 * ---------------------------------------------------------------------- */

/**
 * Upload feito de dentro de um imóvel ou empreendimento?
 *
 * @return bool
 */
function imob_img_upload_is_for_imovel() {
	// Modal de mídia clássico envia post_id; o editor de blocos (REST /wp/v2/media) envia post.
	$post_id = 0;
	foreach ( array( 'post_id', 'post' ) as $key ) {
		if ( ! $post_id && isset( $_REQUEST[ $key ] ) && is_numeric( $_REQUEST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$post_id = (int) $_REQUEST[ $key ]; // phpcs:ignore WordPress.Security.NonceVerification
		}
	}
	if ( ! $post_id ) {
		return false;
	}
	return in_array( get_post_type( $post_id ), array( 'imovel', 'empreendimento' ), true );
}

/**
 * Arquivos processados neste request (caminho => marca aplicada).
 *
 * @var array
 */
$GLOBALS['imob_img_uploaded'] = array();

function imob_img_handle_upload( $upload, $context = 'upload' ) {
	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) || ! imob_img_upload_is_for_imovel() ) {
		return $upload;
	}
	if ( empty( $upload['type'] ) || ! in_array( $upload['type'], imob_img_convertible_mimes(), true ) ) {
		return $upload;
	}

	$source = $upload['file'];
	$main   = imob_img_make_main_jpg( $source );
	if ( is_wp_error( $main ) ) {
		// Fica como veio; a fila de segundo plano tenta de novo ao salvar o imóvel.
		$GLOBALS['imob_img_uploaded'][ wp_normalize_path( $source ) ] = null;
		return $upload;
	}

	if ( $main['file'] !== $source && file_exists( $source ) ) {
		wp_delete_file( $source );
	}

	$watermark_applied = imob_apply_watermark_to_file( $main['file'] );

	$upload['file'] = $main['file'];
	$upload['url']  = trailingslashit( dirname( $upload['url'] ) ) . wp_basename( $main['file'] );
	$upload['type'] = 'image/jpeg';

	$GLOBALS['imob_img_uploaded'][ wp_normalize_path( $main['file'] ) ] = $watermark_applied;

	return $upload;
}
add_filter( 'wp_handle_upload', 'imob_img_handle_upload', 10, 2 );

/**
 * Marca o anexo recém-criado como processado (e com marca d'água).
 */
function imob_img_flag_uploaded_attachment( $metadata, $attachment_id ) {
	$file = get_attached_file( $attachment_id );
	$file = $file ? wp_normalize_path( $file ) : '';
	if ( $file && array_key_exists( $file, $GLOBALS['imob_img_uploaded'] ) ) {
		$state = $GLOBALS['imob_img_uploaded'][ $file ];
		if ( null !== $state ) {
			update_post_meta( $attachment_id, '_imob_img_processed', IMOB_IMG_VERSION );
		}
		// Sempre grava o meta: sem ele a detecção de imagens antigas poderia
		// achar que a foto já tem marca e nunca tentar de novo.
		update_post_meta( $attachment_id, '_imob_watermarked', $state ? '1' : '0' );
		unset( $GLOBALS['imob_img_uploaded'][ $file ] );
	}
	return $metadata;
}
add_filter( 'wp_generate_attachment_metadata', 'imob_img_flag_uploaded_attachment', 20, 2 );

/* -------------------------------------------------------------------------
 * 4. AO SALVAR UM IMÓVEL: FOTOS AINDA NÃO PROCESSADAS VÃO PARA A FILA
 *    (fotos escolhidas da biblioteca, importadas do RealHomes etc.)
 * ---------------------------------------------------------------------- */

function imob_img_queue_post_images( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( in_array( $post->post_status, array( 'auto-draft', 'trash' ), true ) ) {
		return;
	}

	$pending = array();
	foreach ( imob_get_post_image_ids( $post_id ) as $att_id ) {
		$need = imob_img_needs_processing( $att_id );
		if ( $need['any'] ) {
			$pending[] = $att_id;
		}
	}

	if ( $pending ) {
		imob_bg_enqueue( 'optimize', $pending );
	}
}
add_action( 'save_post_imovel', 'imob_img_queue_post_images', 99, 2 );
add_action( 'save_post_empreendimento', 'imob_img_queue_post_images', 99, 2 );

/* -------------------------------------------------------------------------
 * 5. COLETA DE IMAGENS DE IMÓVEIS
 * ---------------------------------------------------------------------- */

/**
 * Converte o valor de uma galeria (array, serializado ou "1,2,3") em IDs.
 *
 * @param mixed $raw Valor do meta.
 * @return int[]
 */
function imob_parse_gallery_ids( $raw ) {
	$value = maybe_unserialize( $raw );
	if ( is_string( $value ) ) {
		$value = preg_split( '/[\s,]+/', $value );
	}
	if ( ! is_array( $value ) ) {
		$value = array( $value );
	}

	$ids = array();
	array_walk_recursive(
		$value,
		function ( $item ) use ( &$ids ) {
			if ( is_numeric( $item ) && (int) $item > 0 ) {
				$ids[] = (int) $item;
			}
		}
	);
	return $ids;
}

/**
 * Imagens de um imóvel/empreendimento (destacada + galeria).
 *
 * @param int $post_id ID do post.
 * @return int[]
 */
function imob_get_post_image_ids( $post_id ) {
	$ids   = array();
	$thumb = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}
	foreach ( array( '_imob_galeria', '_imob_emp_galeria' ) as $key ) {
		$raw = get_post_meta( $post_id, $key, true );
		if ( $raw ) {
			$ids = array_merge( $ids, imob_parse_gallery_ids( $raw ) );
		}
	}
	return array_values( array_diff( array_unique( $ids ), imob_img_protected_ids() ) );
}

/**
 * Todas as imagens de imóveis e empreendimentos.
 *
 * @return int[]
 */
function imob_get_all_imovel_image_ids() {
	global $wpdb;

	$ids = array_map(
		'intval',
		$wpdb->get_col(
			"SELECT DISTINCT pm.meta_value
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE p.post_type IN ('imovel', 'empreendimento')
			  AND pm.meta_key = '_thumbnail_id'
			  AND pm.meta_value > 0"
		)
	);

	$galerias = $wpdb->get_col(
		"SELECT pm.meta_value
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type IN ('imovel', 'empreendimento')
		  AND pm.meta_key IN ('_imob_galeria', '_imob_emp_galeria')"
	);
	foreach ( $galerias as $raw ) {
		$ids = array_merge( $ids, imob_parse_gallery_ids( $raw ) );
	}

	$children = $wpdb->get_col(
		"SELECT p_att.ID
		FROM {$wpdb->posts} p_att
		INNER JOIN {$wpdb->posts} p_parent ON p_parent.ID = p_att.post_parent
		WHERE p_parent.post_type IN ('imovel', 'empreendimento')
		  AND p_att.post_type = 'attachment'
		  AND p_att.post_mime_type LIKE 'image/%'"
	);
	$ids = array_merge( $ids, array_map( 'intval', $children ) );

	// Só anexos de imagem que existem.
	$ids = array_values( array_diff( array_unique( array_filter( $ids ) ), imob_img_protected_ids() ) );
	if ( ! $ids ) {
		return array();
	}

	$existing = array();
	foreach ( array_chunk( $ids, 1000 ) as $chunk ) {
		$in       = implode( ',', array_map( 'intval', $chunk ) );
		$existing = array_merge(
			$existing,
			array_map( 'intval', $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE ID IN ($in) AND post_type = 'attachment' AND post_mime_type LIKE 'image/%'" ) ) // phpcs:ignore WordPress.DB.PreparedSQL
		);
	}
	sort( $existing );
	return $existing;
}

/* -------------------------------------------------------------------------
 * 6. IMAGENS ÓRFÃS
 * ---------------------------------------------------------------------- */

/**
 * IDs de imagens referenciadas por ID em algum lugar do site.
 * Na dúvida a imagem é considerada em uso (nunca apagar por engano).
 *
 * @return array<int,bool>
 */
function imob_get_used_image_id_set() {
	global $wpdb;

	$used = array();
	$add  = function ( $values ) use ( &$used ) {
		foreach ( (array) $values as $v ) {
			$v = (int) $v;
			if ( $v > 0 ) {
				$used[ $v ] = true;
			}
		}
	};

	// Qualquer meta numérico de posts que não são anexos (_thumbnail_id,
	// foto de proprietário, IDs de imagem salvos por plugins...).
	$add(
		$wpdb->get_col(
			"SELECT DISTINCT pm.meta_value
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE p.post_type <> 'attachment'
			  AND pm.meta_value REGEXP '^[0-9]+$'"
		)
	);

	// Galerias (array, serializado ou lista separada por vírgula).
	$galerias = $wpdb->get_col(
		"SELECT pm.meta_value
		FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE p.post_type <> 'attachment'
		  AND ( pm.meta_key LIKE '%galeria%' OR pm.meta_key LIKE '%gallery%' OR pm.meta_key LIKE '%planta%' OR pm.meta_key LIKE '%images%' )"
	);
	foreach ( $galerias as $raw ) {
		$add( imob_parse_gallery_ids( $raw ) );
	}

	// Usuários e termos (foto de corretor, avatar etc.).
	$add( $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$wpdb->usermeta} WHERE meta_value REGEXP '^[0-9]+$'" ) );
	$add( $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$wpdb->termmeta} WHERE meta_value REGEXP '^[0-9]+$'" ) );

	// Opções com ID numérico (logo, ícone, marca d'água...).
	$add( imob_img_protected_ids() );
	$add( $wpdb->get_col( "SELECT DISTINCT option_value FROM {$wpdb->options} WHERE option_value REGEXP '^[0-9]+$' AND option_name NOT LIKE '\\_transient%' AND option_name NOT LIKE '\\_site\\_transient%'" ) );

	// Anexos com post pai existente e fora da lixeira.
	$add(
		$wpdb->get_col(
			"SELECT DISTINCT p_att.ID
			FROM {$wpdb->posts} p_att
			INNER JOIN {$wpdb->posts} p_parent ON p_parent.ID = p_att.post_parent
			WHERE p_att.post_type = 'attachment'
			  AND p_parent.post_status <> 'trash'"
		)
	);

	return $used;
}

/**
 * Imagens da biblioteca sem vínculo por ID com nada do site.
 *
 * @return int[]
 */
function imob_get_orphan_image_ids() {
	global $wpdb;

	$all = array_map( 'intval', $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%'" ) );
	if ( ! $all ) {
		return array();
	}

	$used = imob_get_used_image_id_set();
	return array_values(
		array_filter(
			$all,
			function ( $id ) use ( $used ) {
				return ! isset( $used[ $id ] );
			}
		)
	);
}

/**
 * Verificação extra antes de apagar: o arquivo aparece pelo nome ou pelo ID
 * no conteúdo dos posts, no Elementor, em metas ou em opções?
 *
 * @param int $attachment_id ID do anexo.
 * @return bool
 */
function imob_attachment_is_referenced( $attachment_id ) {
	global $wpdb;

	$attachment_id = (int) $attachment_id;
	$rel           = (string) get_post_meta( $attachment_id, '_wp_attached_file', true );
	$stem          = $rel ? pathinfo( $rel, PATHINFO_FILENAME ) : '';

	$like = array();
	if ( strlen( $stem ) >= 3 ) {
		$like[] = '%' . $wpdb->esc_like( $stem . '.' ) . '%';
		$like[] = '%' . $wpdb->esc_like( $stem . '-' ) . '%';
	}

	// Conteúdo dos posts (blocos, galerias, Elementor salvo no conteúdo).
	$content_like = $like;
	$content_like[] = '%' . $wpdb->esc_like( 'wp-image-' . $attachment_id ) . '%';
	$content_like[] = '%' . $wpdb->esc_like( '"id":' . $attachment_id . ',' ) . '%';
	$content_like[] = '%' . $wpdb->esc_like( '"id":' . $attachment_id . '}' ) . '%';
	$where          = implode( ' OR ', array_fill( 0, count( $content_like ), 'post_content LIKE %s' ) );
	// phpcs:ignore WordPress.DB.PreparedSQL
	$found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type NOT IN ('attachment','revision') AND post_status <> 'trash' AND ( $where ) LIMIT 1", $content_like ) );
	if ( $found ) {
		return true;
	}

	// Metas de posts (Elementor _elementor_data, construtores, plugins).
	$meta_like   = $like;
	$meta_like[] = '%' . $wpdb->esc_like( '"id":' . $attachment_id . ',' ) . '%';
	$meta_like[] = '%' . $wpdb->esc_like( '"id":' . $attachment_id . '}' ) . '%';
	$meta_like[] = '%' . $wpdb->esc_like( '"id":"' . $attachment_id . '"' ) . '%';
	$where       = implode( ' OR ', array_fill( 0, count( $meta_like ), 'pm.meta_value LIKE %s' ) );
	// phpcs:ignore WordPress.DB.PreparedSQL
	$found = $wpdb->get_var( $wpdb->prepare( "SELECT pm.meta_id FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE p.post_type NOT IN ('attachment','revision') AND ( $where ) LIMIT 1", $meta_like ) );
	if ( $found ) {
		return true;
	}

	if ( $like ) {
		$where = implode( ' OR ', array_fill( 0, count( $like ), 'option_value LIKE %s' ) );
		// phpcs:ignore WordPress.DB.PreparedSQL
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT option_id FROM {$wpdb->options} WHERE option_name NOT LIKE '\\_transient%%' AND option_name NOT LIKE '\\_site\\_transient%%' AND ( $where ) LIMIT 1", $like ) ) ) {
			return true;
		}
		$where = implode( ' OR ', array_fill( 0, count( $like ), 'meta_value LIKE %s' ) );
		// phpcs:ignore WordPress.DB.PreparedSQL
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT umeta_id FROM {$wpdb->usermeta} WHERE $where LIMIT 1", $like ) ) ) {
			return true;
		}
		// phpcs:ignore WordPress.DB.PreparedSQL
		if ( $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM {$wpdb->termmeta} WHERE $where LIMIT 1", $like ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Apaga uma imagem órfã depois de conferir de novo que ela não está em uso.
 *
 * @param int $attachment_id ID do anexo.
 * @return array
 */
function imob_process_orphan_attachment( $attachment_id ) {
	static $used = null;

	$attachment_id = (int) $attachment_id;
	$post          = get_post( $attachment_id );
	if ( ! $post || 'attachment' !== $post->post_type ) {
		return array(
			'success' => true,
			'skipped' => true,
			'id'      => $attachment_id,
			'title'   => '#' . $attachment_id,
			'message' => 'não existe mais',
		);
	}

	$title = $post->post_title ? $post->post_title : wp_basename( (string) get_attached_file( $attachment_id ) );

	if ( null === $used ) {
		$used = imob_get_used_image_id_set();
	}
	if ( isset( $used[ $attachment_id ] ) ) {
		return array(
			'success' => true,
			'skipped' => true,
			'id'      => $attachment_id,
			'title'   => $title,
			'message' => 'mantida: passou a ser usada no site',
		);
	}
	if ( imob_attachment_is_referenced( $attachment_id ) ) {
		return array(
			'success' => true,
			'skipped' => true,
			'id'      => $attachment_id,
			'title'   => $title,
			'message' => 'mantida: aparece no conteúdo de alguma página ou configuração',
		);
	}

	if ( wp_delete_attachment( $attachment_id, true ) ) {
		return array(
			'success' => true,
			'id'      => $attachment_id,
			'title'   => $title,
			'message' => 'excluída permanentemente',
		);
	}

	return array(
		'success' => false,
		'id'      => $attachment_id,
		'title'   => $title,
		'message' => 'falha ao excluir',
	);
}

/* -------------------------------------------------------------------------
 * 7. FILA EM SEGUNDO PLANO
 *
 * O trabalho fica salvo em uma opção e é processado em lotes de ~20s por um
 * request de loopback (admin-ajax), com o WP-Cron como reserva. A página de
 * opções só acompanha o progresso: pode ser fechada que o trabalho continua.
 * ---------------------------------------------------------------------- */

if ( ! defined( 'IMOB_BG_TIME_BUDGET' ) ) {
	define( 'IMOB_BG_TIME_BUDGET', 20 );
}

/**
 * Tipos de tarefa e a função que processa cada item.
 *
 * @return array<string,array>
 */
function imob_bg_types() {
	return array(
		'optimize' => array(
			'label'    => 'Otimização das imagens dos imóveis',
			'callback' => 'imob_process_optimize_attachment',
		),
		'orphans'  => array(
			'label'    => 'Limpeza de imagens órfãs',
			'callback' => 'imob_process_orphan_attachment',
		),
	);
}

/**
 * Lê uma opção direto do banco (sem cache), para ver mudanças feitas por
 * outros requests enquanto o lote roda.
 */
function imob_bg_raw_option( $name ) {
	global $wpdb;
	$value = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s LIMIT 1", $name ) );
	return null === $value ? null : maybe_unserialize( $value );
}

function imob_bg_delete_raw_option( $name ) {
	global $wpdb;
	$wpdb->delete( $wpdb->options, array( 'option_name' => $name ) );
	wp_cache_delete( $name, 'options' );
	wp_cache_delete( 'notoptions', 'options' );
}

function imob_bg_get_job( $type ) {
	$job = imob_bg_raw_option( 'imob_bg_job_' . $type );
	return is_array( $job ) ? $job : null;
}

function imob_bg_save_job( $type, $job ) {
	global $wpdb;
	$name  = 'imob_bg_job_' . $type;
	$value = maybe_serialize( $job );
	$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)", $name, $value ) );
	wp_cache_delete( $name, 'options' );
}

function imob_bg_log( &$job, $message, $is_error = false ) {
	$job['log'][] = array(
		't' => time(),
		'm' => $message,
		'e' => (bool) $is_error,
	);
	if ( count( $job['log'] ) > 300 ) {
		$job['log'] = array_slice( $job['log'], -300 );
	}
}

function imob_bg_is_running( $job ) {
	return $job && 'running' === $job['status'];
}

/**
 * Inicia uma tarefa nova.
 *
 * @param string $type   Tipo (optimize|orphans).
 * @param int[]  $ids    IDs de anexos.
 * @param string $source manual|auto.
 * @return array Tarefa criada.
 */
function imob_bg_start( $type, $ids, $source = 'manual' ) {
	$types = imob_bg_types();
	$ids   = array_values( array_unique( array_map( 'intval', $ids ) ) );

	imob_bg_delete_raw_option( 'imob_bg_cancel_' . $type );
	imob_bg_delete_raw_option( 'imob_bg_pending_' . $type );

	$job = array(
		'type'     => $type,
		'source'   => $source,
		'status'   => $ids ? 'running' : 'done',
		'queue'    => $ids,
		'total'    => count( $ids ),
		'done'     => 0,
		'ok'       => 0,
		'skipped'  => 0,
		'errors'   => 0,
		'log'      => array(),
		'started'  => time(),
		'updated'  => time(),
		'finished' => $ids ? 0 : time(),
	);
	imob_bg_log( $job, $types[ $type ]['label'] . ' iniciada em segundo plano: ' . count( $ids ) . ' imagem(ns) na fila.' );
	imob_bg_save_job( $type, $job );

	if ( $ids ) {
		imob_bg_dispatch( $type );
	}
	return $job;
}

/**
 * Coloca IDs na fila. Se já houver tarefa rodando, eles entram nela.
 *
 * @param string $type Tipo.
 * @param int[]  $ids  IDs.
 */
function imob_bg_enqueue( $type, $ids ) {
	$ids = array_values( array_unique( array_map( 'intval', $ids ) ) );
	if ( ! $ids ) {
		return;
	}

	$job = imob_bg_get_job( $type );
	if ( ! imob_bg_is_running( $job ) ) {
		imob_bg_start( $type, $ids, 'auto' );
		return;
	}

	$pending = imob_bg_raw_option( 'imob_bg_pending_' . $type );
	$pending = is_array( $pending ) ? $pending : array();
	$pending = array_values( array_unique( array_merge( $pending, $ids ) ) );

	global $wpdb;
	$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no') ON DUPLICATE KEY UPDATE option_value = VALUES(option_value)", 'imob_bg_pending_' . $type, maybe_serialize( $pending ) ) );
	wp_cache_delete( 'imob_bg_pending_' . $type, 'options' );

	imob_bg_dispatch( $type );
}

function imob_bg_token() {
	$token = get_option( 'imob_bg_token' );
	if ( ! $token ) {
		$token = wp_generate_password( 32, false );
		update_option( 'imob_bg_token', $token, false );
	}
	return $token;
}

/**
 * Dispara o processamento: loopback imediato + WP-Cron de reserva.
 */
function imob_bg_dispatch( $type ) {
	if ( ! wp_next_scheduled( 'imob_bg_cron', array( $type ) ) ) {
		wp_schedule_single_event( time() + 60, 'imob_bg_cron', array( $type ) );
	}

	wp_remote_post(
		admin_url( 'admin-ajax.php' ),
		array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'body'      => array(
				'action' => 'imob_bg_worker',
				'type'   => $type,
				'token'  => imob_bg_token(),
			),
		)
	);
}

/**
 * Trava para só um lote por tipo rodar de cada vez.
 */
function imob_bg_lock( $type ) {
	global $wpdb;
	$name = 'imob_bg_lock_' . $type;
	$now  = time();

	$inserted = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, %s, 'no')", $name, $now ) );
	if ( $inserted ) {
		return true;
	}

	$current = (int) $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) );
	if ( $current && ( $now - $current ) < ( IMOB_BG_TIME_BUDGET * 4 ) ) {
		return false;
	}

	// Trava abandonada (request morto): assume.
	return (bool) $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET option_value = %s WHERE option_name = %s AND option_value = %s", $now, $name, $current ) );
}

function imob_bg_unlock( $type ) {
	imob_bg_delete_raw_option( 'imob_bg_lock_' . $type );
}

function imob_bg_is_locked( $type ) {
	$lock = (int) imob_bg_raw_option( 'imob_bg_lock_' . $type );
	return $lock && ( time() - $lock ) < ( IMOB_BG_TIME_BUDGET * 4 );
}

/**
 * Processa um lote da fila.
 *
 * @param string $type   Tipo.
 * @param int    $budget Segundos disponíveis.
 */
function imob_bg_run( $type, $budget = IMOB_BG_TIME_BUDGET ) {
	$types = imob_bg_types();
	if ( ! isset( $types[ $type ] ) ) {
		return;
	}

	$job = imob_bg_get_job( $type );
	if ( ! imob_bg_is_running( $job ) || ! imob_bg_lock( $type ) ) {
		return;
	}

	ignore_user_abort( true );
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( max( 60, $budget * 3 ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	wp_raise_memory_limit( 'image' );

	$callback = $types[ $type ]['callback'];
	$start    = time();
	$job      = imob_bg_get_job( $type ); // Relê depois de travar.

	while ( imob_bg_is_running( $job ) ) {
		// IDs adicionados por outros requests (ex.: imóvel salvo agora).
		$pending = imob_bg_raw_option( 'imob_bg_pending_' . $type );
		if ( is_array( $pending ) && $pending ) {
			imob_bg_delete_raw_option( 'imob_bg_pending_' . $type );
			$new = array_values( array_diff( array_map( 'intval', $pending ), $job['queue'] ) );
			if ( $new ) {
				$job['queue'] = array_merge( $job['queue'], $new );
				$job['total'] += count( $new );
				imob_bg_log( $job, count( $new ) . ' nova(s) imagem(ns) entraram na fila.' );
			}
		}

		if ( imob_bg_raw_option( 'imob_bg_cancel_' . $type ) ) {
			imob_bg_delete_raw_option( 'imob_bg_cancel_' . $type );
			$job['status']   = 'cancelled';
			$job['finished'] = time();
			$job['queue']    = array();
			imob_bg_log( $job, 'Tarefa cancelada. ' . $job['done'] . ' de ' . $job['total'] . ' processadas.', true );
			break;
		}

		if ( ! $job['queue'] ) {
			$job['status']   = 'done';
			$job['finished'] = time();
			imob_bg_log( $job, '✔ Concluído: ' . $job['ok'] . ' processada(s), ' . $job['skipped'] . ' sem alteração, ' . $job['errors'] . ' com erro.' );
			break;
		}

		if ( ( time() - $start ) >= $budget ) {
			break;
		}

		$id = (int) array_shift( $job['queue'] );
		try {
			$result = call_user_func( $callback, $id );
		} catch ( Throwable $e ) {
			$result = array(
				'success' => false,
				'id'      => $id,
				'title'   => '#' . $id,
				'message' => $e->getMessage(),
			);
		}

		$job['done']++;
		$label = '#' . $id . ( ! empty( $result['title'] ) ? ' (' . $result['title'] . ')' : '' );
		$msg   = isset( $result['message'] ) ? $result['message'] : '';

		if ( empty( $result['success'] ) ) {
			$job['errors']++;
			imob_bg_log( $job, 'Erro na imagem ' . $label . ': ' . $msg, true );
		} elseif ( ! empty( $result['skipped'] ) ) {
			$job['skipped']++;
			imob_bg_log( $job, 'Imagem ' . $label . ': ' . $msg . '.' );
		} else {
			$job['ok']++;
			imob_bg_log( $job, 'Imagem ' . $label . ': ' . $msg . '.', ! empty( $result['watermark_failed'] ) );
		}

		$job['updated'] = time();
		imob_bg_save_job( $type, $job );
	}

	$job['updated'] = time();
	imob_bg_save_job( $type, $job );
	imob_bg_unlock( $type );

	if ( imob_bg_is_running( $job ) ) {
		imob_bg_dispatch( $type );
	}
}

function imob_bg_cron_handler( $type ) {
	imob_bg_run( $type );
}
add_action( 'imob_bg_cron', 'imob_bg_cron_handler' );

/**
 * Loopback que processa o lote (sem login; protegido por token).
 */
function imob_ajax_bg_worker() {
	$token = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$type  = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	if ( ! $token || ! hash_equals( imob_bg_token(), $token ) ) {
		wp_die( '', '', array( 'response' => 403 ) );
	}

	imob_bg_run( $type );
	wp_die();
}
add_action( 'wp_ajax_imob_bg_worker', 'imob_ajax_bg_worker' );
add_action( 'wp_ajax_nopriv_imob_bg_worker', 'imob_ajax_bg_worker' );

/* -------------------------------------------------------------------------
 * 8. AJAX DO PAINEL (Opções do Tema)
 * ---------------------------------------------------------------------- */

function imob_optimizer_check_request() {
	check_ajax_referer( 'imob_optimizer_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Sem permissão.' ) );
	}
	$type = isset( $_POST['type'] ) ? sanitize_key( wp_unslash( $_POST['type'] ) ) : '';
	if ( ! array_key_exists( $type, imob_bg_types() ) ) {
		wp_send_json_error( array( 'message' => 'Tarefa inválida.' ) );
	}
	return $type;
}

/**
 * Estado da tarefa para o painel (sem a fila completa).
 */
function imob_bg_public_job( $type ) {
	$job = imob_bg_get_job( $type );
	if ( ! $job ) {
		return array(
			'type'   => $type,
			'status' => 'idle',
		);
	}

	$out = $job;
	unset( $out['queue'] );
	$out['remaining'] = count( $job['queue'] );
	$out['locked']    = imob_bg_is_locked( $type );
	$out['log']       = array_map(
		function ( $line ) {
			return array(
				't' => wp_date( 'H:i:s', $line['t'] ),
				'm' => $line['m'],
				'e' => $line['e'],
			);
		},
		array_slice( $job['log'], -150 )
	);
	return $out;
}

function imob_ajax_bg_start() {
	$type = imob_optimizer_check_request();

	if ( imob_bg_is_running( imob_bg_get_job( $type ) ) ) {
		wp_send_json_success( imob_bg_public_job( $type ) );
	}

	$ids = 'optimize' === $type ? imob_get_all_imovel_image_ids() : imob_get_orphan_image_ids();
	imob_bg_start( $type, $ids, 'manual' );

	wp_send_json_success( imob_bg_public_job( $type ) );
}
add_action( 'wp_ajax_imob_bg_start', 'imob_ajax_bg_start' );

/**
 * Consulta o progresso. Se o loopback e o cron estiverem parados, processa um
 * lote curto aqui mesmo para a fila não ficar travada.
 */
function imob_ajax_bg_status() {
	$type = imob_optimizer_check_request();
	$job  = imob_bg_get_job( $type );

	if ( imob_bg_is_running( $job ) && ! imob_bg_is_locked( $type ) && ( time() - (int) $job['updated'] ) > 30 ) {
		imob_bg_run( $type, 10 );
	}

	wp_send_json_success( imob_bg_public_job( $type ) );
}
add_action( 'wp_ajax_imob_bg_status', 'imob_ajax_bg_status' );

function imob_ajax_bg_cancel() {
	$type = imob_optimizer_check_request();
	$job  = imob_bg_get_job( $type );

	if ( imob_bg_is_running( $job ) ) {
		update_option( 'imob_bg_cancel_' . $type, 1, false );
		if ( ! imob_bg_is_locked( $type ) ) {
			imob_bg_run( $type, 1 ); // Aplica o cancelamento agora.
		}
	}
	wp_send_json_success( imob_bg_public_job( $type ) );
}
add_action( 'wp_ajax_imob_bg_cancel', 'imob_ajax_bg_cancel' );

/**
 * Contagem rápida de imagens órfãs (antes de iniciar a exclusão).
 */
function imob_ajax_get_orphan_image_ids() {
	check_ajax_referer( 'imob_optimizer_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'Sem permissão.' ) );
	}
	$ids = imob_get_orphan_image_ids();
	wp_send_json_success( array( 'total' => count( $ids ) ) );
}
add_action( 'wp_ajax_imob_get_orphan_image_ids', 'imob_ajax_get_orphan_image_ids' );
