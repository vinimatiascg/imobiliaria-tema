<?php
/**
 * Rodapé personalizável: áreas de widgets, menu do rodapé, widgets do tema
 * ("Marca do rodapé" e "Mais visitados") e contador de visitas.
 *
 * Aparência > Widgets: "Rodapé - Coluna 1/2/3". Coluna sem widgets mostra o conteúdo padrão.
 * Aparência > Menus: local "Menu do rodapé (Acesso rápido)".
 * Personalizar > Rodapé: textos e quantidade de "Mais visitados" do conteúdo padrão.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* -------------------------------------------------------------------------
 * Áreas de widgets e menu
 * ---------------------------------------------------------------------- */

function imob_footer_register_menu() {
	register_nav_menus(
		array(
			'footer' => __( 'Menu do rodapé (Acesso rápido)', 'imobiliaria-tema' ),
		)
	);
}
add_action( 'after_setup_theme', 'imob_footer_register_menu', 20 );

function imob_footer_register_sidebars() {
	$cols = array(
		1 => __( 'Marca, texto e botão "Fale conosco". Sem widgets, mostra o conteúdo padrão.', 'imobiliaria-tema' ),
		2 => __( 'Sem widgets, mostra o "Menu do rodapé (Acesso rápido)".', 'imobiliaria-tema' ),
		3 => __( 'Sem widgets, mostra os itens mais visitados do site.', 'imobiliaria-tema' ),
	);
	foreach ( $cols as $i => $desc ) {
		register_sidebar(
			array(
				/* translators: %d: número da coluna. */
				'name'          => sprintf( __( 'Rodapé - Coluna %d', 'imobiliaria-tema' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => $desc,
				'before_widget' => '<div id="%1$s" class="footer-widget-item %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}
	register_widget( 'Imob_Footer_Brand_Widget' );
	register_widget( 'Imob_Most_Visited_Widget' );
}
add_action( 'widgets_init', 'imob_footer_register_sidebars' );

/* -------------------------------------------------------------------------
 * Opções (Personalizar > Rodapé)
 * ---------------------------------------------------------------------- */

function imob_footer_defaults() {
	return array(
		'brand_text'     => 'Compre seu imóvel com quem é referência em qualidade de atendimento em Campina Grande e região. Os melhores imóveis estão aqui.',
		'button_text'    => 'Fale conosco',
		'menu_title'     => 'Acesso rápido',
		'visited_title'  => 'Mais visitados',
		'visited_count'  => 6,
		'visited_types'  => 'page,imovel,empreendimento',
		'copyright'      => 'Todos os Direitos Reservados',
	);
}

function imob_footer_mod( $key ) {
	$d     = imob_footer_defaults();
	$value = get_theme_mod( 'imob_footer_' . $key, $d[ $key ] ?? '' );
	return ( '' === $value || null === $value ) ? ( $d[ $key ] ?? '' ) : $value;
}

function imob_footer_customize( $wp_customize ) {
	$wp_customize->add_section(
		'imob_footer',
		array(
			'title'       => __( 'Rodapé', 'imobiliaria-tema' ),
			'description' => __( 'Conteúdo padrão das colunas do rodapé. Para trocar uma coluna inteira, adicione widgets em Aparência > Widgets > "Rodapé - Coluna 1/2/3". Os links de "Acesso rápido" vêm do menu do local "Menu do rodapé".', 'imobiliaria-tema' ),
			'priority'    => 120,
		)
	);
	$fields = array(
		'brand_text'    => array( __( 'Texto da coluna 1', 'imobiliaria-tema' ), 'textarea' ),
		'button_text'   => array( __( 'Texto do botão (WhatsApp)', 'imobiliaria-tema' ), 'text' ),
		'menu_title'    => array( __( 'Título da coluna 2 (menu)', 'imobiliaria-tema' ), 'text' ),
		'visited_title' => array( __( 'Título da coluna 3 (mais visitados)', 'imobiliaria-tema' ), 'text' ),
		'visited_count' => array( __( 'Quantidade de itens mais visitados', 'imobiliaria-tema' ), 'number' ),
		'visited_types' => array( __( 'Tipos de conteúdo nos mais visitados (separados por vírgula: page, imovel, empreendimento, post)', 'imobiliaria-tema' ), 'text' ),
		'copyright'     => array( __( 'Texto do copyright', 'imobiliaria-tema' ), 'text' ),
	);
	$defaults = imob_footer_defaults();
	foreach ( $fields as $key => $cfg ) {
		$wp_customize->add_setting(
			'imob_footer_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'number' === $cfg[1] ? 'absint' : ( 'textarea' === $cfg[1] ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
			)
		);
		$wp_customize->add_control(
			'imob_footer_' . $key,
			array(
				'label'       => $cfg[0],
				'section'     => 'imob_footer',
				'type'        => $cfg[1],
				'input_attrs' => 'number' === $cfg[1] ? array( 'min' => 1, 'max' => 20 ) : array(),
			)
		);
	}
}
add_action( 'customize_register', 'imob_footer_customize' );

/* -------------------------------------------------------------------------
 * Contador de visitas (para "Mais visitados")
 * ---------------------------------------------------------------------- */

/**
 * Tipos de conteúdo que podem ser contados.
 *
 * @return string[]
 */
function imob_views_allowed_types() {
	return array( 'page', 'post', 'imovel', 'empreendimento', 'construtora' );
}

function imob_views_register_route() {
	register_rest_route(
		'imob/v1',
		'/view',
		array(
			'methods'             => 'POST',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'required'          => true,
					'sanitize_callback' => 'absint',
				),
			),
			'callback'            => 'imob_views_handle',
		)
	);
}
add_action( 'rest_api_init', 'imob_views_register_route' );

/**
 * Registra uma visita (ignora robôs e conteúdos não públicos).
 *
 * @param WP_REST_Request $request Requisição.
 * @return WP_REST_Response
 */
function imob_views_handle( $request ) {
	$id   = (int) $request->get_param( 'id' );
	$post = $id ? get_post( $id ) : null;
	$ua   = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

	if ( ! $post || 'publish' !== $post->post_status || ! in_array( $post->post_type, imob_views_allowed_types(), true ) || '' === $ua || preg_match( '/bot|crawl|spider|slurp|preview|lighthouse|headless/i', $ua ) ) {
		return new WP_REST_Response( array( 'ok' => false ), 200 );
	}

	$views = (int) get_post_meta( $id, '_imob_views', true );
	update_post_meta( $id, '_imob_views', $views + 1 );

	return new WP_REST_Response( array( 'ok' => true ), 200 );
}

/**
 * Script leve que conta a visita (1 por conteúdo por dia em cada navegador).
 * Funciona mesmo com cache de página (Cloudflare), pois a contagem é feita no navegador.
 */
function imob_views_script() {
	if ( ! is_singular( imob_views_allowed_types() ) || is_user_logged_in() || is_preview() ) {
		return;
	}
	$id = get_queried_object_id();
	if ( ! $id || (int) get_option( 'page_on_front' ) === $id ) {
		return;
	}
	$url = esc_url_raw( rest_url( 'imob/v1/view' ) );
	?>
	<script>
	( function () {
		try {
			var id = <?php echo (int) $id; ?>, key = 'imob_v_' + id, today = new Date().toISOString().slice( 0, 10 );
			if ( localStorage.getItem( key ) === today ) { return; }
			localStorage.setItem( key, today );
			var data = new FormData(); data.append( 'id', id );
			if ( navigator.sendBeacon ) { navigator.sendBeacon( <?php echo wp_json_encode( $url ); ?>, data ); }
			else { fetch( <?php echo wp_json_encode( $url ); ?>, { method: 'POST', body: data, keepalive: true } ); }
		} catch ( e ) {}
	}() );
	</script>
	<?php
}
add_action( 'wp_footer', 'imob_views_script', 99 );

/**
 * Conteúdos mais visitados (com cache de 1 hora).
 *
 * @param int      $count Quantidade.
 * @param string[] $types Tipos de conteúdo.
 * @return WP_Post[]
 */
function imob_get_most_visited( $count = 6, $types = array() ) {
	$count = max( 1, min( 20, (int) $count ) );
	$types = array_values( array_intersect( array_map( 'trim', (array) $types ), imob_views_allowed_types() ) );
	if ( ! $types ) {
		$types = array( 'page', 'imovel', 'empreendimento' );
	}

	$cache_key = 'imob_most_visited_' . md5( $count . '|' . implode( ',', $types ) );
	$ids       = get_transient( $cache_key );
	if ( false === $ids ) {
		$exclude = array_filter( array( (int) get_option( 'page_on_front' ), (int) get_option( 'wp_page_for_privacy_policy' ) ) );
		$ids     = get_posts(
			array(
				'post_type'           => $types,
				'post_status'         => 'publish',
				'posts_per_page'      => $count,
				'fields'              => 'ids',
				'meta_key'            => '_imob_views', // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'             => 'meta_value_num',
				'order'               => 'DESC',
				'post__not_in'        => $exclude,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		set_transient( $cache_key, $ids, HOUR_IN_SECONDS );
	}
	return array_filter( array_map( 'get_post', (array) $ids ) );
}

/**
 * Lista HTML dos mais visitados. Enquanto não houver visitas registradas,
 * mostra os links padrão mais buscados.
 *
 * @param int      $count Quantidade.
 * @param string[] $types Tipos.
 * @return string
 */
function imob_most_visited_list( $count = 6, $types = array() ) {
	$posts = imob_get_most_visited( $count, $types );
	$html  = '<ul class="imob-most-visited">';
	if ( $posts ) {
		foreach ( $posts as $p ) {
			$html .= '<li><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a></li>';
		}
	} else {
		$fallback = array(
			'/imoveis-com-possibilidade-de-entrada-zero-em-campina-grande/' => 'Imóveis com entrada zero',
			'/imoveis-minha-casa-minha-vida-em-campina-grande/'             => 'Imóveis "Minha casa, minha vida"',
			'/imoveis/?localidade=campina-grande'                            => 'Imóveis em Campina Grande',
			'/apartamentos/'                                                 => 'Apartamentos à venda',
			'/imoveis/?tipo=casa&finalidade=venda'                           => 'Casas à venda',
			'/terrenos/'                                                     => 'Terrenos',
		);
		foreach ( array_slice( $fallback, 0, max( 1, (int) $count ), true ) as $path => $label ) {
			$html .= '<li><a href="' . esc_url( home_url( $path ) ) . '">' . esc_html( $label ) . '</a></li>';
		}
	}
	return $html . '</ul>';
}

/* -------------------------------------------------------------------------
 * Conteúdo padrão das colunas
 * ---------------------------------------------------------------------- */

/**
 * Coluna 1: logo, texto, Instagram e botão "Fale conosco".
 *
 * @param string $wa_url URL do WhatsApp.
 * @param array  $o      Textos.
 */
function imob_footer_brand_html( $wa_url, $o = array() ) {
	$text   = isset( $o['text'] ) ? $o['text'] : imob_footer_mod( 'brand_text' );
	$button = isset( $o['button'] ) ? $o['button'] : imob_footer_mod( 'button_text' );
	?>
	<div class="site-branding footer-branding">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-text" style="color: #fff; font-size: 1.5rem; font-weight: 700;"><?php bloginfo( 'name' ); ?></a>
		<?php endif; ?>
	</div>
	<?php if ( $text ) : ?>
		<p class="footer-brand-text"><?php echo nl2br( esc_html( $text ) ); ?></p>
	<?php endif; ?>
	<div class="footer-brand-actions">
		<?php if ( get_option( 'imob_social_instagram' ) ) : ?>
			<a class="footer-social" href="<?php echo esc_url( get_option( 'imob_social_instagram' ) ); ?>" target="_blank" rel="noopener noreferrer" title="Instagram" aria-label="Instagram"><span class="material-symbols-outlined" aria-hidden="true">photo_camera</span></a>
		<?php endif; ?>
		<?php if ( $button ) : ?>
			<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn-primary footer-contact-btn"><?php echo esc_html( $button ); ?></a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Coluna 2: menu do rodapé (com lista padrão enquanto o menu não for criado).
 */
function imob_footer_menu_html() {
	echo '<h3>' . esc_html( imob_footer_mod( 'menu_title' ) ) . '</h3>';
	if ( has_nav_menu( 'footer' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => false,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	$links = array(
		'/'                     => 'Página inicial',
		'/imoveis/'             => 'Todos os imóveis',
		'/avaliacao-de-imoveis/' => 'Avaliação de imóveis',
		'/faq/'                 => 'Perguntas frequentes',
		'/termos-e-condicoes/'  => 'Termos e condições',
		'/nosso-blog/'          => 'Nosso blog',
	);
	echo '<ul>';
	foreach ( $links as $path => $label ) {
		echo '<li><a href="' . esc_url( home_url( $path ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Coluna 3: mais visitados.
 */
function imob_footer_visited_html() {
	echo '<h3>' . esc_html( imob_footer_mod( 'visited_title' ) ) . '</h3>';
	echo imob_most_visited_list( (int) imob_footer_mod( 'visited_count' ), explode( ',', (string) imob_footer_mod( 'visited_types' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Assinatura do desenvolvedor (mesma tipografia do logo de vmatias.dev).
 */
function imob_footer_developer_html() {
	?>
	<div class="developer">
		<span class="developer-label"><?php esc_html_e( 'Desenvolvido por', 'imobiliaria-tema' ); ?></span>
		<a class="vm-who" href="https://vmatias.dev/" target="_blank" rel="noopener">Vinicius Matias<span>especialista em wordpress</span></a>
	</div>
	<?php
}

/* -------------------------------------------------------------------------
 * Widgets
 * ---------------------------------------------------------------------- */

/**
 * Widget: marca do rodapé (logo, texto e botão "Fale conosco").
 */
class Imob_Footer_Brand_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct( 'imob_footer_brand', __( 'Imobiliária - Marca do rodapé', 'imobiliaria-tema' ), array( 'description' => __( 'Logo, texto e botão "Fale conosco" (WhatsApp).', 'imobiliaria-tema' ) ) );
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
		imob_footer_brand_html(
			function_exists( 'imob_home_whatsapp_url' ) ? imob_home_whatsapp_url() : '#',
			array(
				'text'   => $instance['text'] ?? imob_footer_mod( 'brand_text' ),
				'button' => $instance['button'] ?? imob_footer_mod( 'button_text' ),
			)
		);
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public function form( $instance ) {
		$text   = $instance['text'] ?? imob_footer_mod( 'brand_text' );
		$button = $instance['button'] ?? imob_footer_mod( 'button_text' );
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Texto', 'imobiliaria-tema' ); ?></label>
		<textarea class="widefat" rows="4" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>"><?php echo esc_textarea( $text ); ?></textarea></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'button' ) ); ?>"><?php esc_html_e( 'Texto do botão (vazio = sem botão)', 'imobiliaria-tema' ); ?></label>
		<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button' ) ); ?>" value="<?php echo esc_attr( $button ); ?>"></p>
		<?php
	}

	public function update( $new, $old ) {
		return array(
			'text'   => sanitize_textarea_field( $new['text'] ?? '' ),
			'button' => sanitize_text_field( $new['button'] ?? '' ),
		);
	}
}

/**
 * Widget: conteúdos mais visitados.
 */
class Imob_Most_Visited_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct( 'imob_most_visited', __( 'Imobiliária - Mais visitados', 'imobiliaria-tema' ), array( 'description' => __( 'Lista as páginas e imóveis mais visitados do site.', 'imobiliaria-tema' ) ) );
	}

	public function widget( $args, $instance ) {
		$title = $instance['title'] ?? __( 'Mais visitados', 'imobiliaria-tema' );
		$count = isset( $instance['count'] ) ? (int) $instance['count'] : 6;
		$types = isset( $instance['types'] ) ? (array) $instance['types'] : array( 'page', 'imovel', 'empreendimento' );

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo imob_most_visited_list( $count, $types ); // phpcs:ignore WordPress.Security.EscapeOutput
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public function form( $instance ) {
		$title  = $instance['title'] ?? __( 'Mais visitados', 'imobiliaria-tema' );
		$count  = isset( $instance['count'] ) ? (int) $instance['count'] : 6;
		$types  = isset( $instance['types'] ) ? (array) $instance['types'] : array( 'page', 'imovel', 'empreendimento' );
		$labels = array(
			'page'           => __( 'Páginas', 'imobiliaria-tema' ),
			'imovel'         => __( 'Imóveis', 'imobiliaria-tema' ),
			'empreendimento' => __( 'Empreendimentos', 'imobiliaria-tema' ),
			'construtora'    => __( 'Construtoras', 'imobiliaria-tema' ),
			'post'           => __( 'Posts do blog', 'imobiliaria-tema' ),
		);
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Título', 'imobiliaria-tema' ); ?></label>
		<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>"></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Quantidade de itens', 'imobiliaria-tema' ); ?></label>
		<input class="tiny-text" type="number" min="1" max="20" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo esc_attr( $count ); ?>"></p>
		<p><?php esc_html_e( 'Incluir:', 'imobiliaria-tema' ); ?><br>
		<?php foreach ( $labels as $type => $label ) : ?>
			<label style="display:block"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'types' ) ); ?>[]" value="<?php echo esc_attr( $type ); ?>" <?php checked( in_array( $type, $types, true ) ); ?>> <?php echo esc_html( $label ); ?></label>
		<?php endforeach; ?>
		</p>
		<p class="description"><?php esc_html_e( 'As visitas são contadas automaticamente (robôs e usuários logados não contam). Enquanto não houver visitas registradas, aparecem os links mais buscados.', 'imobiliaria-tema' ); ?></p>
		<?php
	}

	public function update( $new, $old ) {
		$types = array_values( array_intersect( (array) ( $new['types'] ?? array() ), imob_views_allowed_types() ) );
		return array(
			'title' => sanitize_text_field( $new['title'] ?? '' ),
			'count' => max( 1, min( 20, absint( $new['count'] ?? 6 ) ) ),
			'types' => $types ? $types : array( 'page' ),
		);
	}
}
