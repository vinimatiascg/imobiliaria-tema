<?php
/**
 * Marca d'água das imagens de imóveis e empreendimentos.
 *
 * A marca é aplicada uma única vez por imagem e o anexo recebe o meta
 * `_imob_watermarked` = 1, para que o otimizador nunca aplique de novo
 * (marca dupla). O processamento completo (JPG + 1280px + miniatura) fica em
 * inc/core/image-optimizer.php.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data a partir da qual o filtro antigo de upload aplicava a marca d'água
 * (v1.1.0). Usada só para reconhecer imagens antigas que já têm a marca mas
 * ainda não têm o meta `_imob_watermarked`.
 */
if ( ! defined( 'IMOB_WATERMARK_LEGACY_SINCE' ) ) {
	define( 'IMOB_WATERMARK_LEGACY_SINCE', '2026-10-08 00:00:00' );
}

/**
 * Caminho da imagem da marca d'água configurada, ou string vazia.
 *
 * @return string
 */
function imob_get_watermark_path() {
	$watermark_id = (int) get_option( 'imob_watermark_image' );
	if ( ! $watermark_id ) {
		return '';
	}
	$path = get_attached_file( $watermark_id );
	return ( $path && file_exists( $path ) ) ? $path : '';
}

/**
 * Diz se o anexo já está com a marca d'água.
 *
 * Imagens antigas (sem o meta) são reconhecidas por dois sinais deixados pela
 * versão anterior do tema:
 * - o otimizador antigo gerava o tamanho imob_large com sufixo "-1280x.ext";
 * - o filtro antigo de upload marcava JPG/PNG enviados direto para um imóvel
 *   ou empreendimento a partir de IMOB_WATERMARK_LEGACY_SINCE.
 *
 * @param int $attachment_id ID do anexo.
 * @return bool
 */
function imob_attachment_is_watermarked( $attachment_id ) {
	$flag = get_post_meta( $attachment_id, '_imob_watermarked', true );
	if ( '1' === (string) $flag ) {
		return true;
	}
	if ( '0' === (string) $flag ) {
		return false;
	}

	$legacy = false;

	$meta = wp_get_attachment_metadata( $attachment_id );
	if ( ! empty( $meta['sizes']['imob_large']['file'] ) && preg_match( '/-1280x\.(jpe?g|png)$/i', $meta['sizes']['imob_large']['file'] ) ) {
		$legacy = true;
	}

	if ( ! $legacy ) {
		$att = get_post( $attachment_id );
		if ( $att && in_array( $att->post_mime_type, array( 'image/jpeg', 'image/png' ), true ) && $att->post_parent && $att->post_date >= IMOB_WATERMARK_LEGACY_SINCE ) {
			$parent_type = get_post_type( $att->post_parent );
			$legacy      = in_array( $parent_type, array( 'imovel', 'empreendimento' ), true );
		}
	}

	/**
	 * Permite ajustar a detecção de marca d'água em imagens antigas.
	 *
	 * @param bool $legacy        Se a imagem já deve ser considerada marcada.
	 * @param int  $attachment_id ID do anexo.
	 */
	return (bool) apply_filters( 'imob_attachment_legacy_watermarked', $legacy, $attachment_id );
}

/**
 * Aplica a marca d'água configurada em um arquivo JPG/PNG/WebP do disco.
 *
 * Deve receber a imagem já reduzida (até 1280px): abrir a foto original de
 * celular em tamanho cheio no GD estourava a memória e a marca era pulada.
 *
 * @param string $file_path Caminho absoluto da imagem.
 * @return bool True se a marca foi aplicada.
 */
function imob_apply_watermark_to_file( $file_path ) {
	if ( ! $file_path || ! file_exists( $file_path ) || ! function_exists( 'imagecreatetruecolor' ) ) {
		return false;
	}

	$watermark_path = imob_get_watermark_path();
	if ( ! $watermark_path ) {
		return false;
	}

	wp_raise_memory_limit( 'image' );

	$main_img = imob_gd_load( $file_path );
	if ( ! $main_img ) {
		return false;
	}

	$watermark = imob_gd_load( $watermark_path );
	if ( ! $watermark ) {
		imagedestroy( $main_img );
		return false;
	}

	$main_w  = imagesx( $main_img );
	$main_h  = imagesy( $main_img );
	$wtm_w   = imagesx( $watermark );
	$wtm_h   = imagesy( $watermark );
	$opacity = max( 0, min( 100, (int) get_option( 'imob_watermark_opacity', 100 ) ) );
	$pos     = get_option( 'imob_watermark_position', 'center' );
	$padding = 20;

	// Redimensiona a marca d'água se for maior que a imagem.
	if ( $wtm_w > ( $main_w - 2 * $padding ) || $wtm_h > ( $main_h - 2 * $padding ) ) {
		$scale     = min( max( 0.05, ( $main_w - 2 * $padding ) / $wtm_w ), max( 0.05, ( $main_h - 2 * $padding ) / $wtm_h ) );
		$new_wtm_w = max( 1, (int) round( $wtm_w * $scale ) );
		$new_wtm_h = max( 1, (int) round( $wtm_h * $scale ) );

		$resized = imagecreatetruecolor( $new_wtm_w, $new_wtm_h );
		imagealphablending( $resized, false );
		imagesavealpha( $resized, true );
		imagefilledrectangle( $resized, 0, 0, $new_wtm_w, $new_wtm_h, imagecolorallocatealpha( $resized, 255, 255, 255, 127 ) );
		imagecopyresampled( $resized, $watermark, 0, 0, 0, 0, $new_wtm_w, $new_wtm_h, $wtm_w, $wtm_h );
		imagedestroy( $watermark );

		$watermark = $resized;
		$wtm_w     = $new_wtm_w;
		$wtm_h     = $new_wtm_h;
	}

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
			$dest_x = ( $main_w - $wtm_w ) / 2;
			$dest_y = ( $main_h - $wtm_h ) / 2;
			break;
	}

	$dest_x = max( 0, (int) $dest_x );
	$dest_y = max( 0, (int) $dest_y );

	imagealphablending( $main_img, true );

	if ( $opacity >= 100 ) {
		imagecopy( $main_img, $watermark, $dest_x, $dest_y, 0, 0, $wtm_w, $wtm_h );
	} else {
		// Recorte do fundo + marca (respeita o alfa do PNG) mesclado com a opacidade.
		$cut = imagecreatetruecolor( $wtm_w, $wtm_h );
		imagecopy( $cut, $main_img, 0, 0, $dest_x, $dest_y, $wtm_w, $wtm_h );
		imagecopy( $cut, $watermark, 0, 0, 0, 0, $wtm_w, $wtm_h );
		imagecopymerge( $main_img, $cut, $dest_x, $dest_y, 0, 0, $wtm_w, $wtm_h, $opacity );
		imagedestroy( $cut );
	}

	$info = @getimagesize( $file_path );
	$mime = $info ? $info['mime'] : 'image/jpeg';

	if ( 'image/png' === $mime ) {
		imagesavealpha( $main_img, true );
		$saved = imagepng( $main_img, $file_path );
	} elseif ( 'image/webp' === $mime && function_exists( 'imagewebp' ) ) {
		$saved = imagewebp( $main_img, $file_path, IMOB_IMG_JPEG_QUALITY );
	} else {
		$saved = imagejpeg( $main_img, $file_path, IMOB_IMG_JPEG_QUALITY );
	}

	imagedestroy( $main_img );
	imagedestroy( $watermark );

	return (bool) $saved;
}

/**
 * Abre uma imagem JPG/PNG/WebP/GIF no GD.
 *
 * @param string $path Caminho absoluto.
 * @return GdImage|resource|false
 */
function imob_gd_load( $path ) {
	$info = @getimagesize( $path );
	if ( ! $info ) {
		return false;
	}
	switch ( $info['mime'] ) {
		case 'image/jpeg':
			return @imagecreatefromjpeg( $path );
		case 'image/png':
			return @imagecreatefrompng( $path );
		case 'image/webp':
			return function_exists( 'imagecreatefromwebp' ) ? @imagecreatefromwebp( $path ) : false;
		case 'image/gif':
			return @imagecreatefromgif( $path );
	}
	return false;
}
