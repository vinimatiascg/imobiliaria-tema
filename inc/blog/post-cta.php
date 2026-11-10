<?php
/**
 * CTA de WhatsApp no final de todos os posts do blog.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL do WhatsApp para o CTA dos posts, com mensagem citando o artigo lido.
 *
 * @param int $post_id ID do post.
 * @return string
 */
function imob_post_cta_whatsapp_url( $post_id = 0 ) {
	$num = get_option( 'imob_contact_whatsapp' ) ? get_option( 'imob_contact_whatsapp' ) : get_option( 'imob_contact_phone' );
	$num = preg_replace( '/\D/', '', (string) $num );
	if ( strlen( $num ) === 10 || strlen( $num ) === 11 ) {
		$num = '55' . $num;
	}

	$msg = __( 'Olá! Quero comprar o meu imóvel em Campina Grande com condições especiais.', 'imobiliaria-tema' );
	if ( $post_id ) {
		/* translators: %s: título do artigo. */
		$msg .= ' ' . sprintf( __( 'Li o artigo "%s" no blog.', 'imobiliaria-tema' ), wp_strip_all_tags( get_the_title( $post_id ) ) );
	}

	$url = 'https://api.whatsapp.com/send?';
	if ( $num ) {
		$url .= 'phone=' . $num . '&';
	}
	return $url . 'text=' . rawurlencode( $msg );
}

/**
 * HTML do CTA.
 *
 * @param int $post_id ID do post.
 * @return string
 */
function imob_post_cta_html( $post_id = 0 ) {
	$text = __( 'Quer comprar o seu imóvel em Campina Grande com condições especiais, entre em contato conosco.', 'imobiliaria-tema' );
	$icon = function_exists( 'imob_home_whatsapp_icon' ) ? imob_home_whatsapp_icon() : '';

	return sprintf(
		'<aside class="imob-post-cta" aria-label="%1$s"><p class="imob-post-cta-text">%2$s</p><a class="imob-post-cta-btn" href="%3$s" target="_blank" rel="noopener noreferrer">%4$s<span>%5$s</span></a></aside>',
		esc_attr__( 'Fale com a imobiliária', 'imobiliaria-tema' ),
		esc_html( $text ),
		esc_url( imob_post_cta_whatsapp_url( $post_id ) ),
		$icon,
		esc_html__( 'Falar no WhatsApp', 'imobiliaria-tema' )
	);
}

/**
 * Adiciona o CTA ao final do conteúdo de todos os posts (inclusive os feitos no Elementor).
 *
 * @param string $content Conteúdo do post.
 * @return string
 */
function imob_append_post_cta( $content ) {
	if ( is_feed() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( get_queried_object_id() !== get_the_ID() || false !== strpos( $content, 'imob-post-cta' ) ) {
		return $content;
	}
	return $content . imob_post_cta_html( get_the_ID() );
}
add_filter( 'the_content', 'imob_append_post_cta', 50 );
