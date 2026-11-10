<?php
/**
 * Formulário "Procurando por um imóvel?" da home.
 *
 * Envia por e-mail para o contato configurado em Opções do Tema
 * (ou para o e-mail do administrador) e guarda cada lead como post privado.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function imob_register_lead_cpt() {
	register_post_type(
		'imob_lead',
		array(
			'labels'       => array(
				'name'          => __( 'Leads da home', 'imobiliaria-tema' ),
				'singular_name' => __( 'Lead', 'imobiliaria-tema' ),
				'menu_name'     => __( 'Leads', 'imobiliaria-tema' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'menu_icon'    => 'dashicons-email-alt',
			'supports'     => array( 'title', 'editor' ),
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'imob_register_lead_cpt' );

function imob_handle_lead_form() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = remove_query_arg( 'imob_lead', $redirect );

	if ( ! isset( $_POST['imob_lead_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['imob_lead_nonce'] ), 'imob_lead_form' ) ) {
		wp_safe_redirect( add_query_arg( 'imob_lead', 'erro', $redirect ) . '#imob-lead' );
		exit;
	}

	// Honeypot anti-spam: campo invisível que humanos não preenchem.
	if ( ! empty( $_POST['imob_site'] ) ) {
		wp_safe_redirect( add_query_arg( 'imob_lead', 'ok', $redirect ) . '#imob-lead' );
		exit;
	}

	$fields = array(
		'nome'       => sanitize_text_field( wp_unslash( $_POST['nome'] ?? '' ) ),
		'email'      => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
		'whatsapp'   => sanitize_text_field( wp_unslash( $_POST['whatsapp'] ?? '' ) ),
		'estado'     => sanitize_text_field( wp_unslash( $_POST['estado'] ?? '' ) ),
		'tipo'       => sanitize_text_field( wp_unslash( $_POST['tipo'] ?? '' ) ),
		'localidade' => sanitize_text_field( wp_unslash( $_POST['localidade'] ?? '' ) ),
		'mensagem'   => sanitize_textarea_field( wp_unslash( $_POST['mensagem'] ?? '' ) ),
	);

	if ( ! $fields['nome'] || ! is_email( $fields['email'] ) || ! $fields['whatsapp'] ) {
		wp_safe_redirect( add_query_arg( 'imob_lead', 'invalido', $redirect ) . '#imob-lead' );
		exit;
	}

	// Origem do lead (ex.: landing page da Black Friday).
	$origem = sanitize_key( wp_unslash( $_POST['origem'] ?? '' ) );
	$origens = array(
		'black-friday' => 'Landing page Black Friday',
	);
	$fields['origem'] = isset( $origens[ $origem ] ) ? $origens[ $origem ] : 'Página inicial';

	$labels = array(
		'origem'     => 'Origem',
		'nome'       => 'Nome',
		'email'      => 'E-mail',
		'whatsapp'   => 'Celular/WhatsApp',
		'estado'     => 'Estado do imóvel',
		'tipo'       => 'Tipo de imóvel',
		'localidade' => 'Localização',
		'mensagem'   => 'Mensagem',
	);
	$body = '';
	foreach ( $labels as $key => $label ) {
		$body .= $label . ': ' . ( '' !== $fields[ $key ] ? $fields[ $key ] : '-' ) . "\n";
	}

	wp_insert_post(
		array(
			'post_type'    => 'imob_lead',
			'post_status'  => 'private',
			'post_title'   => ( 'black-friday' === $origem ? '[Black Friday] ' : '' ) . $fields['nome'] . ' (' . $fields['email'] . ')',
			'post_content' => $body,
		)
	);

	$to = get_option( 'imob_contact_email' ) ? get_option( 'imob_contact_email' ) : get_option( 'admin_email' );
	wp_mail(
		$to,
		sprintf( '[%s] %s: %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), 'black-friday' === $origem ? 'Novo lead da Black Friday' : 'Novo interesse em imóvel', $fields['nome'] ),
		$body,
		array( 'Reply-To: ' . $fields['nome'] . ' <' . $fields['email'] . '>' )
	);

	wp_safe_redirect( add_query_arg( 'imob_lead', 'ok', $redirect ) . '#imob-lead' );
	exit;
}
add_action( 'admin_post_nopriv_imob_lead_form', 'imob_handle_lead_form' );
add_action( 'admin_post_imob_lead_form', 'imob_handle_lead_form' );
