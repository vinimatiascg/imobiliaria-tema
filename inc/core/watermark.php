<?php
/**
 * Adiciona marca d'água nas imagens de imóveis durante o upload.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function imob_apply_watermark_to_upload( $upload ) {
	// Only run if we are uploading to an imovel
	if ( empty( $_REQUEST['post_id'] ) ) {
		return $upload;
	}

	$post_id = intval( $_REQUEST['post_id'] );
	$post = get_post( $post_id );
	if ( ! $post || $post->post_type !== 'imovel' ) {
		return $upload;
	}

	// Check if watermark is configured
	$watermark_id = get_option( 'imob_watermark_image' );
	if ( ! $watermark_id ) {
		return $upload;
	}

	$watermark_path = get_attached_file( $watermark_id );
	if ( ! $watermark_path || ! file_exists( $watermark_path ) ) {
		return $upload;
	}

	$file_path = $upload['file'];
	$file_type = $upload['type'];

	// Only process JPG and PNG
	if ( strpos( $file_type, 'image/jpeg' ) === false && strpos( $file_type, 'image/png' ) === false ) {
		return $upload;
	}

	$opacity = intval( get_option( 'imob_watermark_opacity', 100 ) );
	$position = get_option( 'imob_watermark_position', 'center' );

	// Create GD image from uploaded file
	$img_info = getimagesize( $file_path );
	if ( ! $img_info ) return $upload;
	
	$mime = $img_info['mime'];
	if ( $mime == 'image/jpeg' ) {
		$main_img = imagecreatefromjpeg( $file_path );
	} elseif ( $mime == 'image/png' ) {
		$main_img = imagecreatefrompng( $file_path );
	} else {
		return $upload;
	}

	// Create GD image from watermark
	$wtm_info = getimagesize( $watermark_path );
	if ( ! $wtm_info ) return $upload;
	
	$wtm_mime = $wtm_info['mime'];
	if ( $wtm_mime == 'image/png' ) {
		$watermark = imagecreatefrompng( $watermark_path );
	} elseif ( $wtm_mime == 'image/jpeg' ) {
		$watermark = imagecreatefromjpeg( $watermark_path );
	} else {
		return $upload;
	}

	$main_w = imagesx( $main_img );
	$main_h = imagesy( $main_img );
	$wtm_w = imagesx( $watermark );
	$wtm_h = imagesy( $watermark );
	
	// Se a marca d'água for maior que a imagem, redimensionar
	if ( $wtm_w > $main_w || $wtm_h > $main_h ) {
		// Simples escala para caber com padding
		$scale = min( ($main_w - 40) / $wtm_w, ($main_h - 40) / $wtm_h );
		$new_wtm_w = $wtm_w * $scale;
		$new_wtm_h = $wtm_h * $scale;
		
		$new_watermark = imagecreatetruecolor( $new_wtm_w, $new_wtm_h );
		imagealphablending( $new_watermark, false );
		imagesavealpha( $new_watermark, true );
		$transparent = imagecolorallocatealpha( $new_watermark, 255, 255, 255, 127 );
		imagefilledrectangle( $new_watermark, 0, 0, $new_wtm_w, $new_wtm_h, $transparent );
		
		imagecopyresampled( $new_watermark, $watermark, 0, 0, 0, 0, $new_wtm_w, $new_wtm_h, $wtm_w, $wtm_h );
		imagedestroy( $watermark );
		
		$watermark = $new_watermark;
		$wtm_w = $new_wtm_w;
		$wtm_h = $new_wtm_h;
	}

	// Calculate position
	$dest_x = 0;
	$dest_y = 0;
	$padding = 20;

	switch ( $position ) {
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

	// Prevent negative coordinates if image is too small even with padding
	$dest_x = max(0, $dest_x);
	$dest_y = max(0, $dest_y);

	// Ensure alpha blending is on for main image
	imagealphablending( $main_img, true );
	imagesavealpha( $main_img, true );

	if ( $opacity >= 100 ) {
		// Just copy directly
		imagecopy( $main_img, $watermark, $dest_x, $dest_y, 0, 0, $wtm_w, $wtm_h );
	} else {
		// Opacity trick: 
		// 1. Create a cut of the background
		$cut = imagecreatetruecolor( $wtm_w, $wtm_h );
		imagecopy( $cut, $main_img, 0, 0, $dest_x, $dest_y, $wtm_w, $wtm_h );
		// 2. Place watermark on the cut (preserves alpha channel)
		imagecopy( $cut, $watermark, 0, 0, 0, 0, $wtm_w, $wtm_h );
		// 3. Merge cut back into original background with opacity
		imagecopymerge( $main_img, $cut, $dest_x, $dest_y, 0, 0, $wtm_w, $wtm_h, $opacity );
		imagedestroy( $cut );
	}

	// Save the image
	if ( $mime == 'image/jpeg' ) {
		imagejpeg( $main_img, $file_path, 90 );
	} else {
		imagepng( $main_img, $file_path );
	}

	// Free memory
	imagedestroy( $main_img );
	imagedestroy( $watermark );

	return $upload;
}
add_filter( 'wp_handle_upload', 'imob_apply_watermark_to_upload' );
