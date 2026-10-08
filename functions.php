<?php
/**
 * Tema Imobiliário Customizado - functions.php
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'IMOB_THEME_VERSION', '1.1.5' );
define( 'IMOB_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'IMOB_THEME_URL', trailingslashit( get_template_directory_uri() ) );

/**
 * Autoload function for classes (if needed later) or require standard files.
 */
$imob_includes = [
	'inc/core/setup.php',
	'inc/core/enqueue.php',
	'inc/core/search-logic.php',
	'inc/core/theme-options.php',
	'inc/core/user-profile.php',
	'inc/core/seo.php',
	'inc/core/watermark.php',
	'inc/core/image-optimizer.php',
	'inc/post-types/imovel.php',
	'inc/post-types/construtora.php',
	'inc/post-types/proprietario.php',
	'inc/post-types/empreendimento.php',
	'inc/taxonomies/tipo_imovel.php',
	'inc/taxonomies/localidade.php',
	'inc/taxonomies/caracteristica.php',
	// 'inc/taxonomies/finalidade.php', // Removido conforme solicitação
	'inc/taxonomies/status_imovel.php',
	'inc/taxonomies/corretor.php',
	'inc/meta-boxes/imovel-meta.php',
	'inc/meta-boxes/empreendimento-meta.php',
	'inc/meta-boxes/construtora-meta.php',
	'inc/meta-boxes/proprietario-meta.php',
	'inc/elementor/widgets-registration.php',
	'inc/elementor/dynamic-tags.php',
	'inc/seo/schema.php',
	'inc/api/xml-feed.php',
	'inc/importers/realhomes-importer.php',
];

foreach ( $imob_includes as $file ) {
	$filepath = IMOB_THEME_DIR . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}

/**
 * UTF-8 Multibyte uppercase helper for badges and labels
 */
function imob_strtoupper( $string ) {
	if ( empty( $string ) ) {
		return '';
	}
	if ( function_exists( 'mb_strtoupper' ) ) {
		return mb_strtoupper( (string) $string, 'UTF-8' );
	}
	return strtoupper( (string) $string );
}

/**
 * Get formatted price with before/after texts and previous price
 */
function imob_get_formatted_price( $post_id, $is_single = false ) {
	$preco_venda = get_post_meta( $post_id, '_imob_preco_venda', true );
	$preco_antes = get_post_meta( $post_id, '_imob_preco_antes_texto', true );
	$preco_depois = get_post_meta( $post_id, '_imob_preco_depois_texto', true );
	$preco_anterior = get_post_meta( $post_id, '_imob_preco_anterior', true );

	if ( ! $preco_venda ) {
		return '';
	}

	$html = '';
	
	if ( $is_single ) {
		$html .= '<div class="imob-single-price-wrap" style="background: #0E1A2B; color: #fff; padding: 10px 25px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">';
		if ( $preco_anterior ) {
			$html .= '<div class="imob-preco-anterior" style="color: rgba(255,255,255,0.7); text-decoration: line-through; font-size: 0.9rem; margin-bottom: 5px;">De: R$ ' . number_format( (float) $preco_anterior, 2, ',', '.' ) . '</div>';
			$html .= '<div class="imob-preco-atual" style="display: flex; align-items: baseline; gap: 8px;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes" style="font-size: 1rem; font-weight: 600;">' . esc_html( $preco_antes ) . ' </span>';
			$html .= '<span class="imob-txt-por" style="font-size: 1rem;">Por: </span>';
			$html .= '<span class="imob-txt-valor" style="font-size: 1.5rem; font-weight: 800; color: #fff;">R$ ' . number_format( (float) $preco_venda, 2, ',', '.' ) . '</span>';
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 1rem;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		} else {
			$html .= '<div class="imob-preco-atual" style="display: flex; align-items: baseline; gap: 8px;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes" style="font-size: 1rem; font-weight: 600;">' . esc_html( $preco_antes ) . ' </span>';
			$html .= '<span class="imob-txt-valor" style="font-size: 1.5rem; font-weight: 800; color: #fff;">R$ ' . number_format( (float) $preco_venda, 2, ',', '.' ) . '</span>';
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 1rem;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		}
		$html .= '</div>';
	} else {
		// Card format
		$html .= '<div class="imob-card-price-badge" style="display: inline-block; line-height: 1.2;">';
		if ( $preco_anterior ) {
			$html .= '<div class="imob-preco-anterior" style="font-size: 0.75rem; text-decoration: line-through; opacity: 0.7;">De: R$ ' . number_format( (float) $preco_anterior, 2, ',', '.' ) . '</div>';
			$html .= '<div class="imob-preco-atual" style="font-size: 1rem; font-weight: 700;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes">' . esc_html( $preco_antes ) . ' </span>';
			$html .= 'Por: R$ ' . number_format( (float) $preco_venda, 2, ',', '.' );
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 0.8rem; font-weight: normal;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		} else {
			$html .= '<div class="imob-preco-atual" style="font-size: 1rem; font-weight: 700;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes" style="font-size: 0.8rem; font-weight: normal;">' . esc_html( $preco_antes ) . ' </span>';
			$html .= 'R$ ' . number_format( (float) $preco_venda, 2, ',', '.' );
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 0.8rem; font-weight: normal;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		}
		$html .= '</div>';
	}

	return $html;
}
