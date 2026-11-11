<?php
/**
 * Conteúdo inicial do tema: 5 notícias do mercado imobiliário e a landing page da Black Friday.
 *
 * Roda uma única vez, quando um administrador acessa o painel. Cada item só é criado
 * se ainda não existir um conteúdo com o mesmo slug, então nada é duplicado ou sobrescrito.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IMOB_SEED_CONTENT_VERSION', '2026-11-bf' );

/**
 * Notícias a criar.
 *
 * @return array
 */
function imob_seed_posts_data() {
	return array(
		array(
			'slug'    => 'copom-mantem-selic-13-75-financiamento-imobiliario',
			'title'   => 'Copom mantém Selic em 13,75% e juros do financiamento imobiliário seguem altos',
			'excerpt' => 'O Banco Central interrompeu a sequência de cortes e manteve a Selic em 13,75% ao ano. Veja o que muda para quem vai financiar um imóvel e como reduzir o impacto dos juros.',
			'file'    => '01-selic-copom.html',
			'image'   => 'selic-13-75-copom-novembro-2026.jpg',
			'alt'     => 'Selic em 13,75% ao ano: Copom mantém juros e financiamento imobiliário segue caro',
			'tags'    => array( 'Selic', 'Copom', 'Juros', 'Financiamento imobiliário' ),
		),
		array(
			'slug'    => 'minha-casa-minha-vida-2026-novas-faixas-renda',
			'title'   => 'Minha Casa Minha Vida 2026: novas faixas de renda e imóveis de até R$ 600 mil',
			'excerpt' => 'Com as novas regras em vigor, o Minha Casa Minha Vida atende famílias com renda de até R$ 13 mil e financia imóveis de até R$ 600 mil. Confira faixas, subsídios, juros e tetos.',
			'file'    => '02-minha-casa-minha-vida.html',
			'image'   => 'minha-casa-minha-vida-2026-novas-faixas.jpg',
			'alt'     => 'Minha Casa Minha Vida 2026: renda de até R$ 13 mil na Faixa 4 e imóveis de até R$ 600 mil',
			'tags'    => array( 'Minha Casa Minha Vida', 'FGTS', 'Subsídio', 'Financiamento imobiliário' ),
		),
		array(
			'slug'    => 'credito-imobiliario-sbpe-setembro-2026-abecip',
			'title'   => 'Crédito imobiliário com poupança soma R$ 13,6 bilhões em setembro, aponta Abecip',
			'excerpt' => 'Os financiamentos pelo SBPE chegaram a R$ 13,62 bilhões em setembro e quase 548 mil imóveis em 12 meses. Entenda o cenário do crédito e o alerta da captação da poupança.',
			'file'    => '03-credito-sbpe.html',
			'image'   => 'credito-imobiliario-sbpe-setembro-2026.jpg',
			'alt'     => 'Crédito imobiliário pelo SBPE soma R$ 13,6 bilhões em setembro de 2026',
			'tags'    => array( 'Abecip', 'SBPE', 'Poupança', 'Crédito imobiliário' ),
		),
		array(
			'slug'    => 'fipezap-outubro-2026-preco-imoveis-sobe',
			'title'   => 'Preço dos imóveis sobe 0,48% em outubro e acumula alta acima da inflação, diz FipeZAP',
			'excerpt' => 'O Índice FipeZAP subiu 0,48% em outubro e acumula 4,54% em 2026, acima da inflação. Nordeste aparece entre as maiores altas. Veja os números e o que eles indicam para Campina Grande.',
			'file'    => '04-fipezap-outubro.html',
			'image'   => 'fipezap-outubro-2026-preco-imoveis.jpg',
			'alt'     => 'Índice FipeZAP: preço dos imóveis sobe 0,48% em outubro de 2026',
			'tags'    => array( 'FipeZAP', 'Preço dos imóveis', 'Valorização', 'Nordeste' ),
		),
		array(
			'slug'    => 'reforma-tributaria-imoveis-ibs-cbs-o-que-muda',
			'title'   => 'Reforma tributária: o que muda na compra, venda e aluguel de imóveis com o IBS e a CBS',
			'excerpt' => 'A reforma tributária já está em transição. Veja o cronograma até 2033, os redutores para imóveis, quando a pessoa física paga e os tributos que continuam iguais, como ITBI e IR.',
			'file'    => '05-reforma-tributaria.html',
			'image'   => 'reforma-tributaria-imoveis-ibs-cbs.jpg',
			'alt'     => 'Reforma tributária e imóveis: IBS e CBS na compra, venda e locação',
			'tags'    => array( 'Reforma tributária', 'IBS', 'CBS', 'ITBI', 'Impostos' ),
		),
	);
}

/**
 * Importa uma imagem do tema para a biblioteca de mídia.
 *
 * @param string $file    Nome do arquivo em assets/images/posts/.
 * @param string $alt     Texto alternativo.
 * @param int    $post_id Post pai.
 * @return int ID do anexo ou 0.
 */
function imob_seed_import_image( $file, $alt, $post_id ) {
	$path = IMOB_THEME_DIR . 'assets/images/posts/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$upload = wp_upload_bits( $file, null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $alt,
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file'],
		$post_id
	);
	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return 0;
	}

	wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $upload['file'] ) );
	update_post_meta( $attach_id, '_wp_attachment_image_alt', $alt );
	set_post_thumbnail( $post_id, $attach_id );

	return (int) $attach_id;
}

/**
 * Busca um conteúdo pelo slug (qualquer status, menos lixeira).
 *
 * @param string $slug      Slug.
 * @param string $post_type Tipo.
 * @return WP_Post|null
 */
function imob_seed_find( $slug, $post_type ) {
	$found = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => 1,
		)
	);
	return $found ? $found[0] : null;
}

/**
 * URLs usadas dentro dos textos.
 *
 * @param int $bf_page_id ID da landing.
 * @return array
 */
function imob_seed_link_map( $bf_page_id ) {
	$imoveis = get_post_type_archive_link( 'imovel' );
	$imoveis = $imoveis ? $imoveis : home_url( '/imoveis/' );

	$mcmv      = $imoveis;
	$mcmv_page = get_page_by_path( 'imoveis-minha-casa-minha-vida-em-campina-grande' );
	if ( $mcmv_page ) {
		$mcmv = get_permalink( $mcmv_page );
	} else {
		$term = get_term_by( 'slug', 'mcmv', 'tipo_imovel' );
		if ( $term && ! is_wp_error( $term ) ) {
			$link = get_term_link( $term );
			$mcmv = is_wp_error( $link ) ? $imoveis : $link;
		}
	}

	return array(
		'{{IMOVEIS_URL}}' => esc_url( $imoveis ),
		'{{BF_URL}}'      => esc_url( $bf_page_id ? get_permalink( $bf_page_id ) : home_url( '/black-friday/' ) ),
		'{{MCMV_URL}}'    => esc_url( $mcmv ),
	);
}

/**
 * Cria a landing page da Black Friday.
 *
 * @return int ID da página.
 */
function imob_seed_black_friday_page() {
	$existing = imob_seed_find( 'black-friday', 'page' );
	if ( $existing ) {
		return (int) $existing->ID;
	}

	$content = '<p>A Black Friday de imóveis em Campina Grande chegou: todos os imóveis entraram em oferta, com descontos e brindes conforme o imóvel e, no mínimo, R$ 1.000 de desconto extra em qualquer imóvel à venda. Alguns imóveis têm documentação grátis, outros têm descontos especiais, e as casas em condomínio vêm com 1 ano de condomínio grátis.</p>';

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => 'black-friday',
			'post_title'   => 'Black Friday de Imóveis em Campina Grande',
			'post_excerpt' => 'Todos os imóveis em oferta: no mínimo R$ 1.000 de desconto extra em qualquer imóvel à venda, documentação grátis, descontos e 1 ano de condomínio grátis nas casas em condomínio.',
			'post_content' => $content,
			'post_author'  => get_current_user_id(),
		),
		true
	);
	if ( is_wp_error( $page_id ) ) {
		return 0;
	}

	update_post_meta( $page_id, '_wp_page_template', 'template-black-friday.php' );
	imob_seed_import_image( 'black-friday-imoveis-campina-grande.jpg', 'Black Friday de imóveis em Campina Grande: R$ 1.000 de desconto extra mínimo', $page_id );

	return (int) $page_id;
}

/**
 * Executa o seed uma única vez.
 */
function imob_seed_content() {
	if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( 'imob_seed_content_version' ) === IMOB_SEED_CONTENT_VERSION ) {
		return;
	}
	// Marca antes de rodar para evitar execuções simultâneas.
	update_option( 'imob_seed_content_version', IMOB_SEED_CONTENT_VERSION, false );

	$report     = array();
	$bf_page_id = imob_seed_black_friday_page();
	if ( $bf_page_id ) {
		$report[] = $bf_page_id;
	}

	$links = imob_seed_link_map( $bf_page_id );

	$cat_ids = array();
	foreach ( array( 'Mercado Imobiliário', 'Notícias' ) as $cat_name ) {
		$term = term_exists( $cat_name, 'category' );
		if ( ! $term ) {
			$term = wp_insert_term( $cat_name, 'category' );
		}
		if ( ! is_wp_error( $term ) ) {
			$cat_ids[] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
		}
	}

	$posts = array_reverse( imob_seed_posts_data() ); // O primeiro da lista fica como o mais recente.
	$now   = current_time( 'timestamp' );
	foreach ( $posts as $index => $data ) {
		if ( imob_seed_find( $data['slug'], 'post' ) ) {
			continue;
		}
		$file = IMOB_THEME_DIR . 'inc/content/posts/' . $data['file'];
		if ( ! file_exists( $file ) ) {
			continue;
		}
		$content = strtr( file_get_contents( $file ), $links ); // phpcs:ignore WordPress.WP.AlternativeFunctions

		$date    = gmdate( 'Y-m-d H:i:s', $now - ( count( $posts ) - $index ) * 5 * MINUTE_IN_SECONDS );
		$post_id = wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_status'   => 'publish',
				'post_name'     => $data['slug'],
				'post_title'    => $data['title'],
				'post_excerpt'  => $data['excerpt'],
				'post_content'  => $content,
				'post_author'   => get_current_user_id(),
				'post_category' => $cat_ids,
				'tags_input'    => $data['tags'],
				'post_date'     => $date,
			),
			true
		);
		if ( is_wp_error( $post_id ) ) {
			continue;
		}
		imob_seed_import_image( $data['image'], $data['alt'], $post_id );
		$report[] = $post_id;
	}

	if ( $report ) {
		set_transient( 'imob_seed_content_report', $report, DAY_IN_SECONDS );
	}
}
add_action( 'admin_init', 'imob_seed_content' );

/**
 * Aviso no painel com o que foi criado.
 */
function imob_seed_content_notice() {
	$report = get_transient( 'imob_seed_content_report' );
	if ( ! $report || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	delete_transient( 'imob_seed_content_report' );
	echo '<div class="notice notice-success is-dismissible"><p><strong>' . esc_html__( 'Imobiliária Tema: conteúdos criados.', 'imobiliaria-tema' ) . '</strong></p><ul style="list-style:disc;margin-left:20px">';
	foreach ( $report as $id ) {
		printf(
			'<li><a href="%1$s">%2$s</a> (%3$s) &middot; <a href="%4$s" target="_blank">%5$s</a></li>',
			esc_url( get_edit_post_link( $id ) ),
			esc_html( get_the_title( $id ) ),
			esc_html( 'page' === get_post_type( $id ) ? __( 'página', 'imobiliaria-tema' ) : __( 'post', 'imobiliaria-tema' ) ),
			esc_url( get_permalink( $id ) ),
			esc_html__( 'ver', 'imobiliaria-tema' )
		);
	}
	echo '</ul></div>';
}
add_action( 'admin_notices', 'imob_seed_content_notice' );

/**
 * Cria a página de links da bio (/links/) montada no Elementor com o widget
 * "Links (estilo Linktree)", no modelo "Elementor Tela Cheia" (sem cabeçalho e rodapé).
 * Roda uma vez, quando um administrador acessa o painel com o Elementor ativo.
 */
function imob_seed_links_page() {
	if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) || get_option( 'imob_seed_links_page' ) ) {
		return;
	}
	if ( ! class_exists( '\\Elementor\\Plugin' ) || ! function_exists( 'imob_sections_layout_to_elementor' ) || ! function_exists( 'imob_sections_layouts' ) ) {
		return; // Tenta de novo quando o Elementor estiver ativo.
	}
	update_option( 'imob_seed_links_page', 1, false );

	$page = imob_seed_find( 'links', 'page' );
	if ( $page ) {
		$data = get_post_meta( $page->ID, '_elementor_data', true );
		if ( is_string( $data ) && false !== strpos( $data, 'imob_sec_linkbio' ) ) {
			return; // Já existe.
		}
		if ( $data ) {
			return; // Existe uma página /links/ com outro conteúdo: não mexe.
		}
		$page_id = $page->ID;
	} else {
		$page_id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => 'links',
				'post_title'   => 'Links',
				'post_content' => '',
				'post_author'  => get_current_user_id(),
			),
			true
		);
		if ( is_wp_error( $page_id ) ) {
			return;
		}
	}

	$layouts = imob_sections_layouts();
	$data    = imob_sections_layout_to_elementor( $layouts['links'] );
	update_post_meta( $page_id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $page_id, '_wp_page_template', 'elementor_canvas' );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $page_id, '_elementor_version', ELEMENTOR_VERSION );
	}
	\Elementor\Plugin::$instance->files_manager->clear_cache();

	$report   = (array) get_transient( 'imob_seed_content_report' );
	$report[] = (int) $page_id;
	set_transient( 'imob_seed_content_report', array_filter( $report ), DAY_IN_SECONDS );
}
add_action( 'admin_init', 'imob_seed_links_page', 20 );

/**
 * A página de links não precisa aparecer no Google (evita conteúdo duplicado).
 *
 * @param array $robots Diretivas.
 * @return array
 */
function imob_links_page_robots( $robots ) {
	if ( is_page() ) {
		$data = get_post_meta( get_queried_object_id(), '_elementor_data', true );
		if ( is_string( $data ) && false !== strpos( $data, 'imob_sec_linkbio' ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
			unset( $robots['index'] );
		}
	}
	return $robots;
}
add_filter( 'wp_robots', 'imob_links_page_robots', 30 );
