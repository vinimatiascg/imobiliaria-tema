<?php
/**
 * SEO, Local Geo Tags, Social Discovery (Open Graph / Twitter Cards),
 * Google Discover & Google News Meta Optimization for Imobiliária Tema.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Garante diretivas ideais para Google Discover e Google News no meta robots
 *    (max-image-preview:large, max-snippet:-1, max-video-preview:-1).
 */
function imob_optimize_robots_for_discover( array $robots ) {
	if ( is_404() || is_search() ) {
		return $robots;
	}

	$robots['index']             = true;
	$robots['follow']            = true;
	$robots['max-image-preview'] = 'large';
	$robots['max-snippet']       = '-1';
	$robots['max-video-preview'] = '-1';

	return $robots;
}
add_filter( 'wp_robots', 'imob_optimize_robots_for_discover', 20 );

/**
 * 2. Sanitiza URLs legadas (http://localhost/vinicius) em qualquer metadado ou tag do Yoast SEO.
 */
function imob_sanitize_seo_url( $url ) {
	if ( empty( $url ) || ! is_string( $url ) ) {
		return $url;
	}
	if ( strpos( $url, 'localhost/vinicius' ) !== false ) {
		$home = untrailingslashit( home_url() );
		$url  = str_replace(
			array( 'http://localhost/vinicius', 'https://localhost/vinicius' ),
			$home,
			$url
		);
	}
	return $url;
}
add_filter( 'wpseo_opengraph_image', 'imob_sanitize_seo_url', 20 );
add_filter( 'wpseo_twitter_image', 'imob_sanitize_seo_url', 20 );
add_filter( 'wpseo_canonical', 'imob_sanitize_seo_url', 20 );
add_filter( 'wpseo_opengraph_url', 'imob_sanitize_seo_url', 20 );

/**
 * Filtra os presenters do Yoast SEO para corrigir qualquer URL legada em og:image ou schema do Yoast.
 */
function imob_filter_wpseo_head_output( $output ) {
	return imob_sanitize_seo_url( $output );
}
add_filter( 'wpseo_frontend_presentation', function( $presentation ) {
	return $presentation;
}, 20 );

/**
 * Inicia buffer leve no wp_head para garantir que nenhum plugin imprima http://localhost/vinicius nas meta tags OpenGraph.
 */
function imob_seo_head_buffer_start() {
	ob_start( 'imob_sanitize_seo_url' );
}
function imob_seo_head_buffer_end() {
	if ( ob_get_level() > 0 ) {
		ob_end_flush();
	}
}
add_action( 'wp_head', 'imob_seo_head_buffer_start', 0 );
add_action( 'wp_head', 'imob_seo_head_buffer_end', 999 );

/**
 * 3. Constrói o contexto completo de SEO, GEO, Social Discovery e Google News para a página atual.
 *
 * @return array
 */
function imob_get_seo_context() {
	static $cached_context = null;
	if ( null !== $cached_context ) {
		return $cached_context;
	}

	$site_name   = get_bloginfo( 'name' ) ?: 'IS22 Imóveis | Vinicius Matias';
	$default_lat = '-7.2247';
	$default_lng = '-35.8816';

	$ctx = array(
		'title'          => wp_get_document_title(),
		'description'    => get_bloginfo( 'description' ) ?: 'Imóveis à venda, lançamentos, casas em condomínio, apartamentos e avaliação de imóveis em Campina Grande - PB e região.',
		'canonical'      => home_url( add_query_arg( array(), $GLOBALS['wp']->request ? '/' . trailingslashit( $GLOBALS['wp']->request ) : '/' ) ),
		'og_type'        => 'website',
		'image_url'      => '',
		'image_width'    => 1200,
		'image_height'   => 630,
		'image_alt'      => $site_name,
		'image_type'     => 'image/jpeg',
		'lat'            => $default_lat,
		'lng'            => $default_lng,
		'placename'      => 'Campina Grande, Paraíba, Brasil',
		'region'         => 'BR-PB',
		'price'          => '',
		'currency'       => 'BRL',
		'availability'   => 'in stock',
		'published_time' => '',
		'modified_time'  => '',
		'author_name'    => 'Vinicius Matias - Corretor e Avaliador de Imóveis (CRECI-PB 9972-F)',
		'section'        => 'Mercado Imobiliário',
		'keywords'       => array( 'imóveis em Campina Grande', 'apartamentos à venda Campina Grande', 'casas em condomínio Campina Grande', 'IS22 Imóveis', 'Vinicius Matias' ),
		'label1'         => '',
		'data1'          => '',
		'label2'         => '',
		'data2'          => '',
	);

	// Fallback de imagem global (Logo customizada ou ícone do site)
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_data = wp_get_attachment_image_src( $custom_logo_id, 'full' );
		if ( $logo_data ) {
			$ctx['image_url']    = imob_sanitize_seo_url( $logo_data[0] );
			$ctx['image_width']  = (int) $logo_data[1];
			$ctx['image_height'] = (int) $logo_data[2];
		}
	}
	if ( empty( $ctx['image_url'] ) && has_site_icon() ) {
		$ctx['image_url'] = imob_sanitize_seo_url( get_site_icon_url( 512 ) );
	}

	if ( is_singular( 'imovel' ) ) {
		$post_id               = get_queried_object_id();
		$title                 = get_the_title( $post_id );
		$ref                   = get_post_meta( $post_id, '_imob_ref', true );
		$preco_venda           = get_post_meta( $post_id, '_imob_preco_venda', true );
		$preco_aluguel         = get_post_meta( $post_id, '_imob_preco_aluguel', true );
		$quartos               = get_post_meta( $post_id, '_imob_quartos', true );
		$suites                = get_post_meta( $post_id, '_imob_suites', true );
		$vagas                 = get_post_meta( $post_id, '_imob_vagas', true );
		$area                  = get_post_meta( $post_id, '_imob_area_privativa', true ) ?: get_post_meta( $post_id, '_imob_area_total', true );
		$endereco              = get_post_meta( $post_id, '_imob_endereco', true );
		$registro_incorporacao = trim( (string) get_post_meta( $post_id, '_imob_registro_incorporacao', true ) );
		$emp_data              = function_exists( 'imob_get_imovel_empreendimento' ) ? imob_get_imovel_empreendimento( $post_id ) : null;
		if ( '' === $registro_incorporacao && $emp_data && ! empty( $emp_data['id'] ) ) {
			$registro_incorporacao = trim( (string) get_post_meta( $emp_data['id'], '_imob_emp_registro_incorporacao', true ) );
		}

		$tipos       = wp_get_post_terms( $post_id, 'tipo_imovel', array( 'fields' => 'names' ) );
		$localidades = wp_get_post_terms( $post_id, 'localidade', array( 'fields' => 'names' ) );
		$status_list = wp_get_post_terms( $post_id, 'status_imovel', array( 'fields' => 'slugs' ) );

		$tipo_nome   = ( ! empty( $tipos ) && ! is_wp_error( $tipos ) ) ? $tipos[0] : 'Imóvel';
		$bairro_nome = ( ! empty( $localidades ) && ! is_wp_error( $localidades ) ) ? $localidades[0] : 'Campina Grande';

		// Construção de resumo rico para SEO, GEO e compartilhamento social
		$specs = array();
		if ( $quartos ) {
			$specs[] = $quartos . ( (int) $quartos > 1 ? ' quartos' : ' quarto' ) . ( $suites ? ' (' . $suites . ' suíte' . ( (int) $suites > 1 ? 's' : '' ) . ')' : '' );
		}
		if ( $area ) {
			$specs[] = $area . 'm²';
		}
		if ( $vagas ) {
			$specs[] = $vagas . ( (int) $vagas > 1 ? ' vagas' : ' vaga' );
		}

		$price_num       = $preco_venda ?: $preco_aluguel;
		$price_formatted = $price_num ? 'R$ ' . number_format( (float) $price_num, 2, ',', '.' ) : '';

		$summary_parts   = array();
		$summary_parts[] = sprintf( '%s em %s, Campina Grande - PB', $tipo_nome, $bairro_nome );
		if ( ! empty( $specs ) ) {
			$summary_parts[] = implode( ', ', $specs );
		}
		if ( $emp_data && ! empty( $emp_data['nome'] ) ) {
			$summary_parts[] = 'Empreendimento ' . $emp_data['nome'];
		}
		if ( '' !== $registro_incorporacao ) {
			$summary_parts[] = 'RI: ' . $registro_incorporacao;
		}
		if ( $price_formatted ) {
			$summary_parts[] = 'Valor: ' . $price_formatted;
		}
		if ( $ref ) {
			$summary_parts[] = 'Ref: ' . $ref;
		}

		$raw_excerpt = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );
		$desc        = implode( ' | ', $summary_parts ) . '. ' . wp_trim_words( $raw_excerpt, 24 );

		$ctx['title']          = $title . ' - ' . $bairro_nome . ' | ' . $site_name;
		$ctx['description']    = trim( $desc );
		$ctx['canonical']      = get_permalink( $post_id );
		$ctx['og_type']        = 'article';
		$ctx['published_time'] = get_the_date( 'c', $post_id );
		$ctx['modified_time']  = get_the_modified_date( 'c', $post_id );
		$ctx['placename']      = $bairro_nome . ', Campina Grande, Paraíba, Brasil';
		$ctx['section']        = $tipo_nome;
		$ctx['keywords']       = array_filter( array(
			$tipo_nome . ' em ' . $bairro_nome,
			$tipo_nome . ' em Campina Grande',
			$emp_data ? $emp_data['nome'] : '',
			$bairro_nome,
			'Campina Grande PB',
			'IS22 Imóveis',
		) );

		if ( $price_num ) {
			$ctx['price']  = (string) $price_num;
			$ctx['label1'] = 'Valor';
			$ctx['data1']  = $price_formatted;
		}
		$ctx['label2'] = 'Localização';
		$ctx['data2']  = $bairro_nome . ' - Campina Grande/PB' . ( ! empty( $specs ) ? ' (' . implode( ', ', $specs ) . ')' : '' );

		if ( ! empty( $status_list ) && ! is_wp_error( $status_list ) ) {
			if ( in_array( $status_list[0], array( 'vendido', 'alugado' ), true ) ) {
				$ctx['availability'] = 'out of stock';
			}
		}

		// Imagem principal (Thumbnail -> Galeria do Imóvel -> Empreendimento)
		$img_id = get_post_thumbnail_id( $post_id );
		if ( ! $img_id ) {
			$galeria = get_post_meta( $post_id, '_imob_galeria', true );
			if ( ! empty( $galeria ) ) {
				$galeria_arr = is_array( $galeria ) ? $galeria : explode( ',', (string) $galeria );
				if ( ! empty( $galeria_arr[0] ) ) {
					$img_id = (int) $galeria_arr[0];
				}
			}
		}
		if ( ! $img_id && $emp_data && ! empty( $emp_data['id'] ) ) {
			$img_id = get_post_thumbnail_id( $emp_data['id'] );
		}
		if ( $img_id ) {
			$img_src = wp_get_attachment_image_src( $img_id, 'large' ) ?: wp_get_attachment_image_src( $img_id, 'full' );
			if ( $img_src ) {
				$ctx['image_url']    = imob_sanitize_seo_url( $img_src[0] );
				$ctx['image_width']  = (int) $img_src[1];
				$ctx['image_height'] = (int) $img_src[2];
				$ctx['image_alt']    = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $title . ' - ' . $bairro_nome;
				$mime                = get_post_mime_type( $img_id );
				if ( $mime ) {
					$ctx['image_type'] = $mime;
				}
			}
		}

		// Coordenadas Geográficas
		$lat = get_post_meta( $post_id, '_imob_lat', true );
		$lng = get_post_meta( $post_id, '_imob_lng', true );
		if ( ( empty( $lat ) || empty( $lng ) ) ) {
			$mapa = get_post_meta( $post_id, '_imob_mapa', true );
			if ( $mapa && strpos( $mapa, ',' ) !== false ) {
				$parts = explode( ',', $mapa );
				$lat   = trim( $parts[0] );
				$lng   = trim( $parts[1] );
			}
		}
		if ( ! empty( $lat ) && ! empty( $lng ) && is_numeric( $lat ) && is_numeric( $lng ) ) {
			$ctx['lat'] = $lat;
			$ctx['lng'] = $lng;
		}
	} elseif ( is_singular( 'empreendimento' ) ) {
		$emp_id        = get_queried_object_id();
		$title         = get_the_title( $emp_id );
		$previsao      = get_post_meta( $emp_id, '_imob_emp_previsao', true );
		$registro_inc  = trim( (string) get_post_meta( $emp_id, '_imob_emp_registro_incorporacao', true ) );
		$endereco      = get_post_meta( $emp_id, '_imob_emp_endereco', true );
		$lat           = get_post_meta( $emp_id, '_imob_emp_lat', true );
		$lng           = get_post_meta( $emp_id, '_imob_emp_lng', true );
		$estagio_data  = function_exists( 'imob_get_empreendimento_estagio_data' ) ? imob_get_empreendimento_estagio_data( $emp_id ) : null;
		$const_id      = get_post_meta( $emp_id, '_imob_emp_construtora_id', true );
		$const_title   = $const_id ? get_the_title( (int) $const_id ) : '';

		$summary_parts   = array( 'Empreendimento ' . $title . ' em Campina Grande - PB' );
		if ( $estagio_data && ! empty( $estagio_data['name'] ) ) {
			$summary_parts[] = 'Estágio: ' . $estagio_data['name'];
		}
		if ( $previsao ) {
			$summary_parts[] = 'Entrega: ' . $previsao;
		}
		if ( $const_title ) {
			$summary_parts[] = 'Construtora: ' . $const_title;
		}
		if ( '' !== $registro_inc ) {
			$summary_parts[] = 'Registro de Incorporação: ' . $registro_inc;
		}

		$raw_excerpt = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $emp_id ) ) );
		$ctx['title']          = $title . ' - Empreendimento em Campina Grande | ' . $site_name;
		$ctx['description']    = trim( implode( ' | ', $summary_parts ) . '. ' . wp_trim_words( $raw_excerpt, 24 ) );
		$ctx['canonical']      = get_permalink( $emp_id );
		$ctx['og_type']        = 'article';
		$ctx['published_time'] = get_the_date( 'c', $emp_id );
		$ctx['modified_time']  = get_the_modified_date( 'c', $emp_id );
		$ctx['section']        = 'Empreendimentos Imobiliários';
		$ctx['keywords']       = array_filter( array( $title, $const_title, 'Empreendimento em Campina Grande', 'Lançamento Imobiliário Campina Grande' ) );

		if ( $estagio_data && ! empty( $estagio_data['name'] ) ) {
			$ctx['label1'] = 'Estágio da Obra';
			$ctx['data1']  = $estagio_data['name'] . ( $previsao ? ' (Entrega: ' . $previsao . ')' : '' );
		}
		if ( $endereco ) {
			$ctx['label2']    = 'Localização';
			$ctx['data2']     = $endereco;
			$ctx['placename'] = $endereco;
		}

		$img_id = get_post_thumbnail_id( $emp_id );
		if ( ! $img_id ) {
			$galeria = get_post_meta( $emp_id, '_imob_emp_galeria', true );
			$galeria = is_array( $galeria ) ? $galeria : ( ! empty( $galeria ) ? explode( ',', (string) $galeria ) : array() );
			if ( ! empty( $galeria[0] ) ) {
				$img_id = (int) $galeria[0];
			}
		}
		if ( $img_id ) {
			$img_src = wp_get_attachment_image_src( $img_id, 'large' ) ?: wp_get_attachment_image_src( $img_id, 'full' );
			if ( $img_src ) {
				$ctx['image_url']    = imob_sanitize_seo_url( $img_src[0] );
				$ctx['image_width']  = (int) $img_src[1];
				$ctx['image_height'] = (int) $img_src[2];
				$ctx['image_alt']    = $title . ' - Empreendimento em Campina Grande';
			}
		}

		if ( ! empty( $lat ) && ! empty( $lng ) && is_numeric( $lat ) && is_numeric( $lng ) ) {
			$ctx['lat'] = $lat;
			$ctx['lng'] = $lng;
		}
	} elseif ( is_singular( 'construtora' ) ) {
		$const_id           = get_queried_object_id();
		$title              = get_the_title( $const_id );
		$raw_excerpt        = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $const_id ) ) );
		$ctx['title']       = $title . ' - Construtora em Campina Grande | ' . $site_name;
		$ctx['description'] = $raw_excerpt ? wp_trim_words( $raw_excerpt, 28 ) : sprintf( 'Conheça os empreendimentos, lançamentos e imóveis da construtora %s em Campina Grande - PB.', $title );
		$ctx['canonical']   = get_permalink( $const_id );
		$ctx['og_type']     = 'profile';
		$img_id             = get_post_thumbnail_id( $const_id );
		if ( $img_id ) {
			$img_src = wp_get_attachment_image_src( $img_id, 'large' );
			if ( $img_src ) {
				$ctx['image_url']    = imob_sanitize_seo_url( $img_src[0] );
				$ctx['image_width']  = (int) $img_src[1];
				$ctx['image_height'] = (int) $img_src[2];
				$ctx['image_alt']    = $title;
			}
		}
	} elseif ( is_singular( 'post' ) ) {
		$post_id               = get_queried_object_id();
		$title                 = get_the_title( $post_id );
		$raw_excerpt           = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : get_post_field( 'post_content', $post_id );
		$clean_excerpt         = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $raw_excerpt ) ), 28 );
		$cats                  = get_the_category( $post_id );
		$tags                  = get_the_tags( $post_id );
		$author_id             = get_post_field( 'post_author', $post_id );
		$author_name           = $author_id ? get_the_author_meta( 'display_name', $author_id ) : $ctx['author_name'];

		$ctx['title']          = $title . ' | ' . $site_name;
		$ctx['description']    = $clean_excerpt ?: $title;
		$ctx['canonical']      = get_permalink( $post_id );
		$ctx['og_type']        = 'article';
		$ctx['published_time'] = get_the_date( 'c', $post_id );
		$ctx['modified_time']  = get_the_modified_date( 'c', $post_id );
		$ctx['author_name']    = $author_name;
		if ( ! empty( $cats ) ) {
			$ctx['section'] = $cats[0]->name;
		}

		$kw = array();
		if ( ! empty( $cats ) ) {
			foreach ( $cats as $c ) {
				$kw[] = $c->name;
			}
		}
		if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
			foreach ( $tags as $t ) {
				$kw[] = $t->name;
			}
		}
		$kw[]            = 'Campina Grande';
		$kw[]            = 'Mercado Imobiliário';
		$ctx['keywords'] = array_unique( array_filter( $kw ) );

		$img_id = get_post_thumbnail_id( $post_id );
		if ( $img_id ) {
			$img_src = wp_get_attachment_image_src( $img_id, 'full' ) ?: wp_get_attachment_image_src( $img_id, 'large' );
			if ( $img_src ) {
				$ctx['image_url']    = imob_sanitize_seo_url( $img_src[0] );
				$ctx['image_width']  = (int) $img_src[1];
				$ctx['image_height'] = (int) $img_src[2];
				$ctx['image_alt']    = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $title;
			}
		}
	} elseif ( is_post_type_archive( 'imovel' ) ) {
		$ctx['title']       = 'Imóveis à Venda e Aluguel em Campina Grande - PB | ' . $site_name;
		$ctx['description'] = 'Confira casas, apartamentos, coberturas, terrenos, lançamentos e imóveis em condomínio fechado em Campina Grande - PB com condições especiais.';
		$ctx['canonical']   = get_post_type_archive_link( 'imovel' );
	} elseif ( is_post_type_archive( 'empreendimento' ) ) {
		$ctx['title']       = 'Empreendimentos e Lançamentos em Campina Grande - PB | ' . $site_name;
		$ctx['description'] = 'Explore os melhores empreendimentos residenciais, condomínios fechados e lançamentos na planta em Campina Grande - PB.';
		$ctx['canonical']   = get_post_type_archive_link( 'empreendimento' );
	} elseif ( is_post_type_archive( 'construtora' ) ) {
		$ctx['title']       = 'Construtoras Parceiras em Campina Grande - PB | ' . $site_name;
		$ctx['description'] = 'Conheça as principais construtoras de Campina Grande - PB e veja seus imóveis e empreendimentos disponíveis.';
		$ctx['canonical']   = get_post_type_archive_link( 'construtora' );
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$term_link          = get_term_link( $term );
			$ctx['title']       = $term->name . ' em Campina Grande - PB | ' . $site_name;
			$ctx['description'] = ! empty( $term->description )
				? wp_trim_words( wp_strip_all_tags( $term->description ), 28 )
				: sprintf( 'Confira os melhores imóveis e conteúdos de %s em Campina Grande - PB selecionados pela %s.', $term->name, $site_name );
			if ( ! is_wp_error( $term_link ) ) {
				$ctx['canonical'] = $term_link;
			}
		}
	} elseif ( is_singular( 'page' ) ) {
		$page_id            = get_queried_object_id();
		$ctx['canonical']   = get_permalink( $page_id );
		$raw_excerpt        = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $page_id ) ) );
		if ( ! empty( $raw_excerpt ) ) {
			$ctx['description'] = wp_trim_words( $raw_excerpt, 28 );
		}
		$img_id = get_post_thumbnail_id( $page_id );
		if ( $img_id ) {
			$img_src = wp_get_attachment_image_src( $img_id, 'large' );
			if ( $img_src ) {
				$ctx['image_url']    = imob_sanitize_seo_url( $img_src[0] );
				$ctx['image_width']  = (int) $img_src[1];
				$ctx['image_height'] = (int) $img_src[2];
			}
		}
	}

	$cached_context = $ctx;
	return $ctx;
}

/**
 * 4. Integração com filtros do Yoast SEO (quando ativo) para enriquecer descrições e imagens de imóveis/empreendimentos.
 */
function imob_filter_yoast_metadesc( $desc ) {
	if ( ! empty( $desc ) ) {
		return $desc;
	}
	$ctx = imob_get_seo_context();
	return ! empty( $ctx['description'] ) ? $ctx['description'] : $desc;
}
add_filter( 'wpseo_metadesc', 'imob_filter_yoast_metadesc', 15 );
add_filter( 'wpseo_opengraph_desc', 'imob_filter_yoast_metadesc', 15 );
add_filter( 'wpseo_twitter_description', 'imob_filter_yoast_metadesc', 15 );

function imob_filter_yoast_og_image( $image ) {
	$image = imob_sanitize_seo_url( $image );
	if ( ( is_singular( 'imovel' ) || is_singular( 'empreendimento' ) ) && ( empty( $image ) || strpos( $image, 'LOGO-VP' ) !== false ) ) {
		$ctx = imob_get_seo_context();
		if ( ! empty( $ctx['image_url'] ) ) {
			return $ctx['image_url'];
		}
	}
	return $image;
}
add_filter( 'wpseo_opengraph_image', 'imob_filter_yoast_og_image', 25 );
add_filter( 'wpseo_twitter_image', 'imob_filter_yoast_og_image', 25 );

/**
 * 5. Injeta Meta Tags de SEO, GEO (Localização + IA), Social Discovery, Google Discover e Google News no <head>.
 */
function imob_seo_head() {
	$ctx            = imob_get_seo_context();
	$has_seo_plugin = ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) );

	echo "\n<!-- Imobiliária Tema: SEO, GEO, Social Discovery & Google News -->\n";

	// Theme Color & Mobile Discovery
	echo '<meta name="theme-color" content="#0E1A2B" />' . "\n";

	// Geo Meta Tags (Local SEO em Campina Grande - PB)
	echo '<meta name="geo.region" content="' . esc_attr( $ctx['region'] ) . '" />' . "\n";
	echo '<meta name="geo.placename" content="' . esc_attr( $ctx['placename'] ) . '" />' . "\n";
	echo '<meta name="geo.position" content="' . esc_attr( $ctx['lat'] . ';' . $ctx['lng'] ) . '" />' . "\n";
	echo '<meta name="ICBM" content="' . esc_attr( $ctx['lat'] . ', ' . $ctx['lng'] ) . '" />' . "\n";

	// Autor, Publisher e News Keywords (Google News & AI Discovery)
	echo '<meta name="author" content="' . esc_attr( $ctx['author_name'] ) . '" />' . "\n";
	echo '<meta name="publisher" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
	if ( ! empty( $ctx['keywords'] ) ) {
		echo '<meta name="news_keywords" content="' . esc_attr( implode( ', ', array_slice( $ctx['keywords'], 0, 10 ) ) ) . '" />' . "\n";
	}

	// Links de Descoberta (Google News Sitemap & LLMs.txt para Motores Generativos / GEO)
	echo '<link rel="sitemap" type="application/xml" title="Google News Sitemap" href="' . esc_url( home_url( '/sitemap-news.xml' ) ) . '" />' . "\n";
	echo '<link rel="alternate" type="text/plain" title="AI & LLM Context (GEO)" href="' . esc_url( home_url( '/llms.txt' ) ) . '" />' . "\n";

	// Metadados Comerciais de Imóvel / Empreendimento (WhatsApp, Facebook Catalog, Pinterest, X/Twitter Labels)
	if ( ! empty( $ctx['price'] ) ) {
		echo '<meta property="product:price:amount" content="' . esc_attr( $ctx['price'] ) . '" />' . "\n";
		echo '<meta property="product:price:currency" content="' . esc_attr( $ctx['currency'] ) . '" />' . "\n";
		echo '<meta property="product:availability" content="' . esc_attr( $ctx['availability'] ) . '" />' . "\n";
	}
	if ( ! empty( $ctx['label1'] ) && ! empty( $ctx['data1'] ) ) {
		echo '<meta name="twitter:label1" content="' . esc_attr( $ctx['label1'] ) . '" />' . "\n";
		echo '<meta name="twitter:data1" content="' . esc_attr( $ctx['data1'] ) . '" />' . "\n";
	}
	if ( ! empty( $ctx['label2'] ) && ! empty( $ctx['data2'] ) ) {
		echo '<meta name="twitter:label2" content="' . esc_attr( $ctx['label2'] ) . '" />' . "\n";
		echo '<meta name="twitter:data2" content="' . esc_attr( $ctx['data2'] ) . '" />' . "\n";
	}

	// Se não houver plugin de SEO ativo, o tema emite o conjunto completo de Open Graph, Twitter Cards, Description e Canonical
	if ( ! $has_seo_plugin ) {
		echo '<meta name="description" content="' . esc_attr( $ctx['description'] ) . '" />' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $ctx['canonical'] ) . '" />' . "\n";
		echo '<meta property="og:locale" content="pt_BR" />' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $ctx['og_type'] ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $ctx['title'] ) . '" />' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $ctx['description'] ) . '" />' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $ctx['canonical'] ) . '" />' . "\n";

		if ( ! empty( $ctx['published_time'] ) ) {
			echo '<meta property="article:published_time" content="' . esc_attr( $ctx['published_time'] ) . '" />' . "\n";
		}
		if ( ! empty( $ctx['modified_time'] ) ) {
			echo '<meta property="article:modified_time" content="' . esc_attr( $ctx['modified_time'] ) . '" />' . "\n";
			echo '<meta property="og:updated_time" content="' . esc_attr( $ctx['modified_time'] ) . '" />' . "\n";
		}
		if ( ! empty( $ctx['section'] ) && 'article' === $ctx['og_type'] ) {
			echo '<meta property="article:section" content="' . esc_attr( $ctx['section'] ) . '" />' . "\n";
		}

		if ( ! empty( $ctx['image_url'] ) ) {
			echo '<meta property="og:image" content="' . esc_url( $ctx['image_url'] ) . '" />' . "\n";
			echo '<meta property="og:image:secure_url" content="' . esc_url( set_url_scheme( $ctx['image_url'], 'https' ) ) . '" />' . "\n";
			if ( ! empty( $ctx['image_width'] ) && ! empty( $ctx['image_height'] ) ) {
				echo '<meta property="og:image:width" content="' . intval( $ctx['image_width'] ) . '" />' . "\n";
				echo '<meta property="og:image:height" content="' . intval( $ctx['image_height'] ) . '" />' . "\n";
			}
			echo '<meta property="og:image:alt" content="' . esc_attr( $ctx['image_alt'] ) . '" />' . "\n";
			echo '<meta property="og:image:type" content="' . esc_attr( $ctx['image_type'] ) . '" />' . "\n";
			echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
			echo '<meta name="twitter:image" content="' . esc_url( $ctx['image_url'] ) . '" />' . "\n";
			echo '<meta name="twitter:image:alt" content="' . esc_attr( $ctx['image_alt'] ) . '" />' . "\n";
		} else {
			echo '<meta name="twitter:card" content="summary" />' . "\n";
		}

		echo '<meta name="twitter:title" content="' . esc_attr( $ctx['title'] ) . '" />' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $ctx['description'] ) . '" />' . "\n";
	}
	echo "<!-- /Imobiliária Tema SEO & GEO -->\n";
}
add_action( 'wp_head', 'imob_seo_head', 5 );

/**
 * 6. Otimização de Imagens para Core Web Vitals (LCP) e Google Discover:
 *    - Imagens principais (full/large) em páginas single recebem fetchpriority="high" e decoding="async".
 *    - Demais imagens recebem loading="lazy" e alt automático contextual se vazio.
 */
function imob_optimize_attachment_image_attributes( $attr, $attachment, $size ) {
	$attr['decoding'] = 'async';

	if ( empty( $attr['alt'] ) ) {
		$post_title = get_the_title();
		$attr['alt'] = $post_title ? $post_title . ' - Campina Grande PB' : get_bloginfo( 'name' );
	}

	$is_hero_size = in_array( $size, array( 'full', 'large' ), true );
	if ( is_singular() && $is_hero_size && ! isset( $GLOBALS['imob_lcp_image_assigned'] ) ) {
		$GLOBALS['imob_lcp_image_assigned'] = true;
		$attr['fetchpriority']              = 'high';
		unset( $attr['loading'] );
	} else {
		if ( ! isset( $attr['loading'] ) ) {
			$attr['loading'] = 'lazy';
		}
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'imob_optimize_attachment_image_attributes', 10, 3 );

/**
 * 7. Inclui Imóveis e Empreendimentos no Feed RSS principal e adiciona imagem destacada no RSS
 *    (Fundamental para Google News Publisher Center e agregadores de conteúdo).
 */
function imob_enhance_rss_feed_content( $content ) {
	if ( ! is_feed() ) {
		return $content;
	}
	$post_id = get_the_ID();
	if ( has_post_thumbnail( $post_id ) ) {
		$img_html = get_the_post_thumbnail( $post_id, 'large', array( 'style' => 'max-width:100%;height:auto;margin-bottom:15px;' ) );
		$content  = $img_html . $content;
	}
	return $content;
}
add_filter( 'the_excerpt_rss', 'imob_enhance_rss_feed_content' );
add_filter( 'the_content_feed', 'imob_enhance_rss_feed_content' );

/**
 * 8. Verificador de elegibilidade para Google Discover / Google News no editor de posts.
 *    Mostra um aviso na tela de edição quando o post não atende aos requisitos principais.
 *
 * @param int $post_id ID do post.
 * @return string[] Lista de problemas encontrados.
 */
function imob_discover_issues( $post_id ) {
	$issues = array();
	$title  = get_the_title( $post_id );

	$thumb_id = get_post_thumbnail_id( $post_id );
	if ( ! $thumb_id ) {
		$issues[] = __( 'Sem imagem destacada. O Google Discover exige uma imagem grande e de qualidade.', 'imobiliaria-tema' );
	} else {
		$meta = wp_get_attachment_metadata( $thumb_id );
		if ( ! empty( $meta['width'] ) && (int) $meta['width'] < 1200 ) {
			/* translators: %d: largura em pixels. */
			$issues[] = sprintf( __( 'A imagem destacada tem %dpx de largura. Use no mínimo 1200px (ideal 1600x900, proporção 16:9).', 'imobiliaria-tema' ), (int) $meta['width'] );
		}
		if ( ! get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ) {
			$issues[] = __( 'A imagem destacada está sem texto alternativo (alt).', 'imobiliaria-tema' );
		}
	}

	if ( function_exists( 'mb_strlen' ) ? mb_strlen( $title ) > 110 : strlen( $title ) > 110 ) {
		$issues[] = __( 'O título passa de 110 caracteres e pode ser cortado no Google News.', 'imobiliaria-tema' );
	}
	if ( ! has_excerpt( $post_id ) ) {
		$issues[] = __( 'Sem resumo (excerpt). Ele é usado como meta descrição e no card do Discover.', 'imobiliaria-tema' );
	}
	if ( ! get_the_category( $post_id ) ) {
		$issues[] = __( 'Sem categoria definida.', 'imobiliaria-tema' );
	}
	$words = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
	if ( $words < 300 ) {
		$issues[] = __( 'Texto com menos de 300 palavras. Conteúdos mais completos têm mais chance no Discover.', 'imobiliaria-tema' );
	}
	if ( 'publish' === get_post_status( $post_id ) && '0' === (string) get_option( 'blog_public' ) ) {
		$issues[] = __( 'O site está configurado para não ser indexado (Configurações > Leitura).', 'imobiliaria-tema' );
	}
	return $issues;
}

function imob_discover_editor_notice() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->base || 'post' !== $screen->post_type ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $post_id ) {
		return;
	}
	$issues = imob_discover_issues( $post_id );
	if ( empty( $issues ) ) {
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Google Discover / News: este post atende aos requisitos técnicos do tema.', 'imobiliaria-tema' ) . '</p></div>';
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Google Discover / News: ajuste os pontos abaixo.', 'imobiliaria-tema' ) . '</strong></p><ul style="list-style:disc;margin-left:20px">';
	foreach ( $issues as $issue ) {
		echo '<li>' . esc_html( $issue ) . '</li>';
	}
	echo '</ul></div>';
}
add_action( 'admin_notices', 'imob_discover_editor_notice' );
