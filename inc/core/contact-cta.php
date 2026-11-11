<?php
/**
 * Chamada para contato (CTA) usada no blog, nas listagens de imóveis e na página do imóvel,
 * e a descrição da página do blog.
 *
 * Textos em Aparência > Personalizar > Chamada para contato (CTA) e > Blog.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Textos padrão.
 *
 * @return array
 */
function imob_contact_cta_defaults() {
	return array(
		'eyebrow'   => 'Fale com um corretor',
		'title'     => 'Quer comprar o seu imóvel em Campina Grande com condições especiais?',
		'text'      => 'Entre em contato conosco: tiramos suas dúvidas sobre financiamento, Minha Casa Minha Vida e uso do FGTS, e apresentamos os imóveis que cabem no seu bolso.',
		'btn1_text' => 'Falar no WhatsApp',
		'btn2_text' => 'Ver imóveis',
	);
}

/**
 * Descrição padrão da página do blog.
 *
 * @return string
 */
function imob_blog_description_default() {
	return 'Acompanhe as principais tendências, análises do mercado imobiliário e dicas exclusivas para morar bem e investir com segurança.';
}

/**
 * Descrição da página do blog (Personalizar > Blog).
 *
 * @return string
 */
function imob_blog_description() {
	$value = get_theme_mod( 'imob_blog_description', imob_blog_description_default() );
	return '' === trim( (string) $value ) ? imob_blog_description_default() : $value;
}

/**
 * Lê um texto do CTA.
 *
 * @param string $key Chave.
 * @return string
 */
function imob_contact_cta_mod( $key ) {
	$d     = imob_contact_cta_defaults();
	$value = get_theme_mod( 'imob_contact_cta_' . $key, $d[ $key ] ?? '' );
	return ( '' === $value || null === $value ) ? ( $d[ $key ] ?? '' ) : $value;
}

/**
 * Exibe o CTA.
 *
 * @param array $overrides Ex.: array( 'btn1_url' => 'https://wa.me/...' ) na página do imóvel.
 */
function imob_contact_cta( $overrides = array() ) {
	if ( ! function_exists( 'imob_render_section' ) || ! get_theme_mod( 'imob_contact_cta_enabled', true ) ) {
		return;
	}
	$text = imob_contact_cta_mod( 'text' );
	$args = array_merge(
		array(
			'eyebrow'   => imob_contact_cta_mod( 'eyebrow' ),
			'title'     => imob_contact_cta_mod( 'title' ),
			'content'   => $text ? '<p>' . esc_html( $text ) . '</p>' : '',
			'btn1_text' => imob_contact_cta_mod( 'btn1_text' ),
			'btn1_url'  => '{whatsapp}',
			'btn2_text' => imob_contact_cta_mod( 'btn2_text' ),
			'btn2_url'  => '{imoveis}',
		),
		$overrides
	);
	echo '<div class="imob-contact-cta">';
	imob_render_section( 'cta', $args );
	echo '</div>';
}

/**
 * Carrega o CSS das seções no <head> das páginas que exibem o CTA (evita o CTA aparecer sem estilo).
 */
function imob_contact_cta_assets() {
	$is_listing = is_post_type_archive( array( 'imovel', 'empreendimento' ) )
		|| is_tax( array( 'localidade', 'tipo_imovel', 'estagio_obra', 'corretor', 'status_imovel', 'caracteristica' ) )
		|| is_singular( array( 'imovel' ) )
		|| is_page_template( array( 'template-imoveis.php', 'template-blog.php' ) )
		|| is_page();
	if ( $is_listing && function_exists( 'imob_sections_enqueue' ) ) {
		imob_sections_enqueue();
	}
}
add_action( 'wp_enqueue_scripts', 'imob_contact_cta_assets', 21 );

/**
 * Personalizar: CTA e Blog.
 *
 * @param WP_Customize_Manager $wp_customize Gerenciador do Customizer.
 */
function imob_contact_cta_customize( $wp_customize ) {
	$wp_customize->add_section(
		'imob_contact_cta',
		array(
			'title'       => __( 'Chamada para contato (CTA)', 'imobiliaria-tema' ),
			'description' => __( 'Quadro azul exibido no fim da página do blog, das listagens de imóveis e da página de cada imóvel. Na página do imóvel, o botão do WhatsApp já envia a mensagem com o nome e a referência do imóvel.', 'imobiliaria-tema' ),
			'priority'    => 121,
		)
	);
	$wp_customize->add_setting(
		'imob_contact_cta_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
	$wp_customize->add_control(
		'imob_contact_cta_enabled',
		array(
			'label'   => __( 'Exibir a chamada para contato', 'imobiliaria-tema' ),
			'section' => 'imob_contact_cta',
			'type'    => 'checkbox',
		)
	);
	$fields = array(
		'eyebrow'   => array( __( 'Chamada superior', 'imobiliaria-tema' ), 'text' ),
		'title'     => array( __( 'Título', 'imobiliaria-tema' ), 'text' ),
		'text'      => array( __( 'Texto', 'imobiliaria-tema' ), 'textarea' ),
		'btn1_text' => array( __( 'Botão 1 (WhatsApp): texto', 'imobiliaria-tema' ), 'text' ),
		'btn2_text' => array( __( 'Botão 2 (ver imóveis): texto (vazio = sem botão)', 'imobiliaria-tema' ), 'text' ),
	);
	$d = imob_contact_cta_defaults();
	foreach ( $fields as $key => $cfg ) {
		$wp_customize->add_setting(
			'imob_contact_cta_' . $key,
			array(
				'default'           => $d[ $key ],
				'sanitize_callback' => 'textarea' === $cfg[1] ? 'sanitize_textarea_field' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'imob_contact_cta_' . $key,
			array(
				'label'   => $cfg[0],
				'section' => 'imob_contact_cta',
				'type'    => $cfg[1],
			)
		);
	}

	$wp_customize->add_section(
		'imob_blog',
		array(
			'title'    => __( 'Blog', 'imobiliaria-tema' ),
			'priority' => 122,
		)
	);
	$wp_customize->add_setting(
		'imob_blog_description',
		array(
			'default'           => imob_blog_description_default(),
			'sanitize_callback' => 'sanitize_textarea_field',
		)
	);
	$wp_customize->add_control(
		'imob_blog_description',
		array(
			'label'       => __( 'Descrição da página do blog', 'imobiliaria-tema' ),
			'description' => __( 'Texto abaixo do título "Nosso Blog". Também é usado como descrição da página para o Google quando o SEO não tiver outra.', 'imobiliaria-tema' ),
			'section'     => 'imob_blog',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'imob_contact_cta_customize' );
