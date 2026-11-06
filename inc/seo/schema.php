<?php
/**
 * SEO, GEO (Generative Engine Optimization) & Google News:
 * - Schema.org JSON-LD (@graph completo: RealEstateAgent, WebSite, RealEstateListing,
 *   ApartmentComplex, HomeAndConstructionBusiness, NewsArticle, CollectionPage, BreadcrumbList)
 * - Google News XML Sitemap (/sitemap-news.xml)
 * - Endpoint /llms.txt para Motores de Busca Generativos (ChatGPT, Perplexity, Gemini, Claude)
 * - Otimização de robots.txt para Google News e AI Crawlers
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Registra rotas amigáveis para /sitemap-news.xml, /llms.txt e /llms-full.txt
 */
function imob_register_seo_geo_endpoints() {
	add_rewrite_rule( '^sitemap-news\.xml$', 'index.php?imob_sitemap=news', 'top' );
	add_rewrite_rule( '^llms\.txt$', 'index.php?imob_llms=1', 'top' );
	add_rewrite_rule( '^llms-full\.txt$', 'index.php?imob_llms=1', 'top' );
}
add_action( 'init', 'imob_register_seo_geo_endpoints', 5 );

function imob_register_seo_geo_query_vars( $vars ) {
	$vars[] = 'imob_sitemap';
	$vars[] = 'imob_llms';
	return $vars;
}
add_filter( 'query_vars', 'imob_register_seo_geo_query_vars' );

/**
 * Intercepta requisições para /sitemap-news.xml, /llms.txt e /llms-full.txt
 */
function imob_handle_seo_geo_virtual_files() {
	$req_uri = isset( $_SERVER['REQUEST_URI'] ) ? strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : '';
	$is_news = ( get_query_var( 'imob_sitemap' ) === 'news' ) || ( isset( $_GET['imob_sitemap'] ) && 'news' === $_GET['imob_sitemap'] ) || ( '/sitemap-news.xml' === $req_uri );
	$is_llms = ( get_query_var( 'imob_llms' ) === '1' ) || isset( $_GET['imob_llms'] ) || in_array( $req_uri, array( '/llms.txt', '/llms-full.txt' ), true );

	if ( $is_news ) {
		imob_render_google_news_sitemap();
		exit;
	}

	if ( $is_llms ) {
		imob_render_llms_txt();
		exit;
	}
}
add_action( 'template_redirect', 'imob_handle_seo_geo_virtual_files', 1 );

/**
 * 2. Gera o JSON-LD Schema.org (@graph) unificado para SEO, GEO e Google News / Discover
 */
function imob_generate_schema_json() {
	$site_url  = trailingslashit( home_url() );
	$site_name = get_bloginfo( 'name' ) ?: 'IS22 Imóveis | Vinicius Matias';
	$ctx       = function_exists( 'imob_get_seo_context' ) ? imob_get_seo_context() : array();

	$wa_phone  = get_option( 'imob_contact_whatsapp' ) ?: get_option( 'imob_contact_phone' ) ?: '+55-83-99926-7089';
	$instagram = get_option( 'imob_social_instagram' ) ?: 'https://www.instagram.com/viniciusmatiasimoveis/';
	$logo_url  = ! empty( $ctx['image_url'] ) ? $ctx['image_url'] : '';

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_src = wp_get_attachment_image_url( $custom_logo_id, 'full' );
		if ( $logo_src ) {
			$logo_url = function_exists( 'imob_sanitize_seo_url' ) ? imob_sanitize_seo_url( $logo_src ) : $logo_src;
		}
	}

	$graph = array();

	// Entidade 1: RealEstateAgent / Organization (Autoridade Local & GEO)
	$org_node = array(
		'@type'       => array( 'RealEstateAgent', 'Organization' ),
		'@id'         => $site_url . '#organization',
		'name'        => $site_name,
		'alternateName' => 'Vinicius Matias Corretor e Avaliador de Imóveis - CRECI-PB 9972-F',
		'url'         => $site_url,
		'telephone'   => $wa_phone,
		'email'       => 'contato@viniciusmatias.com.br',
		'priceRange'  => 'R$ 150.000 - R$ 5.000.000',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Rua Severino Galileu, 296 - Liberdade',
			'addressLocality' => 'Campina Grande',
			'addressRegion'   => 'PB',
			'postalCode'      => '58414-220',
			'addressCountry'  => 'BR',
		),
		'geo'         => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => -7.2430477,
			'longitude' => -35.8999473,
		),
		'areaServed'  => array(
			array(
				'@type' => 'City',
				'name'  => 'Campina Grande',
			),
			array(
				'@type' => 'State',
				'name'  => 'Paraíba',
			),
		),
		'knowsAbout'  => array(
			'Mercado Imobiliário em Campina Grande - PB',
			'Compra e Venda de Imóveis Residenciais e Comerciais',
			'Casas em Condomínio Fechado em Campina Grande',
			'Lançamentos Imobiliários e Imóveis na Planta',
			'Avaliação Técnica de Imóveis (NBR 14653 / CNAI)',
			'Financiamento Imobiliário Minha Casa Minha Vida e Subsídio CEHAP',
		),
		'sameAs'      => array_values( array_filter( array( $instagram ) ) ),
	);

	if ( $logo_url ) {
		$org_node['logo'] = array(
			'@type' => 'ImageObject',
			'@id'   => $site_url . '#logo',
			'url'   => $logo_url,
		);
		$org_node['image'] = $logo_url;
	}

	$graph[] = $org_node;

	// Entidade 2: WebSite com SearchAction para Imóveis
	$graph[] = array(
		'@type'           => 'WebSite',
		'@id'             => $site_url . '#website',
		'url'             => $site_url,
		'name'            => $site_name,
		'inLanguage'      => 'pt-BR',
		'publisher'       => array( '@id' => $site_url . '#organization' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $site_url . '?post_type=imovel&s={search_term_string}',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	// Entidade 3: Página específica
	if ( is_singular( 'imovel' ) ) {
		$post_id               = get_queried_object_id();
		$permalink             = get_permalink( $post_id );
		$ref                   = get_post_meta( $post_id, '_imob_ref', true );
		$preco_venda           = get_post_meta( $post_id, '_imob_preco_venda', true );
		$preco_aluguel         = get_post_meta( $post_id, '_imob_preco_aluguel', true );
		$condominio            = get_post_meta( $post_id, '_imob_condominio', true );
		$iptu                  = get_post_meta( $post_id, '_imob_iptu', true );
		$quartos               = get_post_meta( $post_id, '_imob_quartos', true );
		$suites                = get_post_meta( $post_id, '_imob_suites', true );
		$banheiros             = get_post_meta( $post_id, '_imob_banheiros', true );
		$vagas                 = get_post_meta( $post_id, '_imob_vagas', true );
		$area_privativa        = get_post_meta( $post_id, '_imob_area_privativa', true );
		$area_total            = get_post_meta( $post_id, '_imob_area_total', true );
		$endereco              = get_post_meta( $post_id, '_imob_endereco', true );
		$registro_incorporacao = trim( (string) get_post_meta( $post_id, '_imob_registro_incorporacao', true ) );

		$emp_data   = function_exists( 'imob_get_imovel_empreendimento' ) ? imob_get_imovel_empreendimento( $post_id ) : null;
		$const_data = function_exists( 'imob_get_imovel_construtora' ) ? imob_get_imovel_construtora( $post_id ) : null;

		if ( '' === $registro_incorporacao && $emp_data && ! empty( $emp_data['id'] ) ) {
			$registro_incorporacao = trim( (string) get_post_meta( $emp_data['id'], '_imob_emp_registro_incorporacao', true ) );
		}

		$status_terms = wp_get_post_terms( $post_id, 'status_imovel' );
		$availability = 'InStock';
		if ( ! empty( $status_terms ) && ! is_wp_error( $status_terms ) ) {
			if ( in_array( $status_terms[0]->slug, array( 'vendido', 'alugado' ), true ) ) {
				$availability = 'SoldOut';
			}
		}

		$tipos       = wp_get_post_terms( $post_id, 'tipo_imovel' );
		$localidades = wp_get_post_terms( $post_id, 'localidade' );
		$caracs      = wp_get_post_terms( $post_id, 'caracteristica', array( 'fields' => 'names' ) );

		$accommodation_type = 'Accommodation';
		if ( ! empty( $tipos ) && ! is_wp_error( $tipos ) ) {
			$tipo_slug = $tipos[0]->slug;
			if ( strpos( $tipo_slug, 'apartamento' ) !== false || strpos( $tipo_slug, 'cobertura' ) !== false || strpos( $tipo_slug, 'flat' ) !== false ) {
				$accommodation_type = 'Apartment';
			} elseif ( strpos( $tipo_slug, 'casa' ) !== false || strpos( $tipo_slug, 'duplex' ) !== false ) {
				$accommodation_type = 'SingleFamilyResidence';
			}
		}

		// Imagens da galeria em alta resolução (para Google Discover e Rich Results)
		$image_urls = array();
		if ( has_post_thumbnail( $post_id ) ) {
			$u = get_the_post_thumbnail_url( $post_id, 'full' );
			if ( $u ) {
				$image_urls[] = function_exists( 'imob_sanitize_seo_url' ) ? imob_sanitize_seo_url( $u ) : $u;
			}
		}
		$galeria = get_post_meta( $post_id, '_imob_galeria', true );
		if ( ! empty( $galeria ) ) {
			$gal_ids = is_array( $galeria ) ? $galeria : explode( ',', (string) $galeria );
			foreach ( array_slice( $gal_ids, 0, 10 ) as $gid ) {
				$gu = wp_get_attachment_image_url( (int) $gid, 'full' );
				if ( $gu ) {
					$image_urls[] = function_exists( 'imob_sanitize_seo_url' ) ? imob_sanitize_seo_url( $gu ) : $gu;
				}
			}
		}
		$image_urls = array_values( array_unique( $image_urls ) );

		$listing = array(
			'@type'            => array( 'RealEstateListing', $accommodation_type ),
			'@id'              => $permalink . '#imovel',
			'url'              => $permalink,
			'name'             => get_the_title( $post_id ),
			'description'      => ! empty( $ctx['description'] ) ? $ctx['description'] : wp_strip_all_tags( get_the_excerpt( $post_id ) ),
			'datePosted'       => get_the_date( 'c', $post_id ),
			'dateModified'     => get_the_modified_date( 'c', $post_id ),
			'inLanguage'       => 'pt-BR',
			'provider'         => array( '@id' => $site_url . '#organization' ),
			'mainEntityOfPage' => $permalink,
			'speakable'        => array(
				'@type'       => 'SpeakableSpecification',
				'cssSelector' => array( '.imob-single-title', '.imob-single-location', '.imob-section-content' ),
			),
		);

		if ( ! empty( $image_urls ) ) {
			$listing['image'] = $image_urls;
		}
		if ( $ref ) {
			$listing['sku'] = $ref;
		}
		if ( $quartos ) {
			$listing['numberOfRooms']    = (int) $quartos;
			$listing['numberOfBedrooms'] = (int) $quartos;
		}
		if ( $banheiros ) {
			$listing['numberOfBathroomsTotal'] = (int) $banheiros;
		}
		if ( $area_privativa || $area_total ) {
			$listing['floorSize'] = array(
				'@type'    => 'QuantitativeValue',
				'value'    => (float) ( $area_privativa ?: $area_total ),
				'unitCode' => 'MTK',
				'unitText' => 'm²',
			);
		}

		// Endereço e Coordenadas
		$bairro_nome = ( ! empty( $localidades ) && ! is_wp_error( $localidades ) ) ? $localidades[0]->name : 'Campina Grande';
		$listing['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $endereco ?: $bairro_nome,
			'addressLocality' => 'Campina Grande',
			'addressRegion'   => 'PB',
			'addressCountry'  => 'BR',
		);

		if ( ! empty( $ctx['lat'] ) && ! empty( $ctx['lng'] ) ) {
			$listing['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $ctx['lat'],
				'longitude' => (float) $ctx['lng'],
			);
		}

		// Oferta de Preço
		$price_val = $preco_venda ?: $preco_aluguel;
		if ( $price_val ) {
			$listing['offers'] = array(
				'@type'         => 'Offer',
				'url'           => $permalink,
				'priceCurrency' => 'BRL',
				'price'         => (float) $price_val,
				'availability'  => 'https://schema.org/' . $availability,
				'seller'        => array( '@id' => $site_url . '#organization' ),
			);
		}

		// Propriedades adicionais estruturadas para GEO / LLMs (incluindo Registro de Incorporação se preenchido)
		$additional_props = array();
		if ( $ref ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Código do Imóvel', 'value' => $ref );
		}
		if ( '' !== $registro_incorporacao ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Registro de Incorporação', 'value' => $registro_incorporacao );
		}
		if ( $suites ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Suítes', 'value' => (int) $suites );
		}
		if ( $vagas ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Vagas de Garagem', 'value' => (int) $vagas );
		}
		if ( $condominio ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Condomínio (R$)', 'value' => (float) $condominio );
		}
		if ( $iptu ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'IPTU (R$)', 'value' => (float) $iptu );
		}
		if ( $emp_data && ! empty( $emp_data['nome'] ) ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Empreendimento', 'value' => $emp_data['nome'] );
		}
		if ( $const_data && ! empty( $const_data['nome'] ) ) {
			$additional_props[] = array( '@type' => 'PropertyValue', 'name' => 'Construtora', 'value' => $const_data['nome'] );
		}
		if ( ! empty( $additional_props ) ) {
			$listing['additionalProperty'] = $additional_props;
		}

		// Amenidades / Características
		if ( ! empty( $caracs ) && ! is_wp_error( $caracs ) ) {
			$amenities = array();
			foreach ( $caracs as $c_name ) {
				$amenities[] = array(
					'@type' => 'LocationFeatureSpecification',
					'name'  => $c_name,
					'value' => true,
				);
			}
			$listing['amenityFeature'] = $amenities;
		}

		$graph[] = $listing;

	} elseif ( is_singular( 'empreendimento' ) ) {
		$emp_id        = get_queried_object_id();
		$permalink     = get_permalink( $emp_id );
		$previsao      = get_post_meta( $emp_id, '_imob_emp_previsao', true );
		$registro_inc  = trim( (string) get_post_meta( $emp_id, '_imob_emp_registro_incorporacao', true ) );
		$endereco      = get_post_meta( $emp_id, '_imob_emp_endereco', true );
		$estagio_data  = function_exists( 'imob_get_empreendimento_estagio_data' ) ? imob_get_empreendimento_estagio_data( $emp_id ) : null;
		$const_id      = get_post_meta( $emp_id, '_imob_emp_construtora_id', true );
		$const_title   = $const_id ? get_the_title( (int) $const_id ) : '';

		$emp_node = array(
			'@type'       => array( 'ApartmentComplex', 'Place' ),
			'@id'         => $permalink . '#empreendimento',
			'url'         => $permalink,
			'name'        => get_the_title( $emp_id ),
			'description' => ! empty( $ctx['description'] ) ? $ctx['description'] : wp_strip_all_tags( get_the_excerpt( $emp_id ) ),
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $endereco ?: 'Campina Grande',
				'addressLocality' => 'Campina Grande',
				'addressRegion'   => 'PB',
				'addressCountry'  => 'BR',
			),
		);

		if ( ! empty( $ctx['image_url'] ) ) {
			$emp_node['image'] = $ctx['image_url'];
		}
		if ( ! empty( $ctx['lat'] ) && ! empty( $ctx['lng'] ) ) {
			$emp_node['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $ctx['lat'],
				'longitude' => (float) $ctx['lng'],
			);
		}

		$emp_props = array();
		if ( $estagio_data && ! empty( $estagio_data['name'] ) ) {
			$emp_props[] = array( '@type' => 'PropertyValue', 'name' => 'Estágio da Obra', 'value' => $estagio_data['name'] );
		}
		if ( $previsao ) {
			$emp_props[] = array( '@type' => 'PropertyValue', 'name' => 'Previsão de Entrega', 'value' => $previsao );
		}
		if ( $const_title ) {
			$emp_props[] = array( '@type' => 'PropertyValue', 'name' => 'Construtora', 'value' => $const_title );
		}
		if ( '' !== $registro_inc ) {
			$emp_props[] = array( '@type' => 'PropertyValue', 'name' => 'Registro de Incorporação', 'value' => $registro_inc );
		}
		if ( ! empty( $emp_props ) ) {
			$emp_node['additionalProperty'] = $emp_props;
		}

		$graph[] = $emp_node;

	} elseif ( is_singular( 'construtora' ) ) {
		$const_id  = get_queried_object_id();
		$permalink = get_permalink( $const_id );
		$telefone  = get_post_meta( $const_id, '_imob_construtora_telefone', true ) ?: get_post_meta( $const_id, '_imob_construtora_whatsapp', true );
		$site_ext  = get_post_meta( $const_id, '_imob_construtora_site', true );

		$const_node = array(
			'@type'       => 'HomeAndConstructionBusiness',
			'@id'         => $permalink . '#construtora',
			'url'         => $permalink,
			'name'        => get_the_title( $const_id ),
			'description' => ! empty( $ctx['description'] ) ? $ctx['description'] : '',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => 'Campina Grande',
				'addressRegion'   => 'PB',
				'addressCountry'  => 'BR',
			),
		);
		if ( $telefone ) {
			$const_node['telephone'] = $telefone;
		}
		if ( $site_ext ) {
			$const_node['sameAs'] = array( $site_ext );
		}
		if ( ! empty( $ctx['image_url'] ) ) {
			$const_node['image'] = $ctx['image_url'];
		}
		$graph[] = $const_node;

	} elseif ( is_singular( 'post' ) ) {
		// Schema NewsArticle & BlogPosting para Google News e Google Discover
		$post_id    = get_queried_object_id();
		$permalink  = get_permalink( $post_id );
		$author_id  = get_post_field( 'post_author', $post_id );
		$author_nm  = $author_id ? get_the_author_meta( 'display_name', $author_id ) : 'Vinicius Matias';
		$word_count = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );

		$article_images = array();
		if ( has_post_thumbnail( $post_id ) ) {
			foreach ( array( 'full', 'large', 'medium_large' ) as $sz ) {
				$u = get_the_post_thumbnail_url( $post_id, $sz );
				if ( $u ) {
					$article_images[] = function_exists( 'imob_sanitize_seo_url' ) ? imob_sanitize_seo_url( $u ) : $u;
				}
			}
		}
		if ( empty( $article_images ) && ! empty( $ctx['image_url'] ) ) {
			$article_images[] = $ctx['image_url'];
		}
		$article_images = array_values( array_unique( $article_images ) );

		$article_node = array(
			'@type'                => array( 'NewsArticle', 'BlogPosting' ),
			'@id'                  => $permalink . '#article',
			'mainEntityOfPage'     => array(
				'@type' => 'WebPage',
				'@id'   => $permalink,
			),
			'headline'             => get_the_title( $post_id ),
			'description'          => ! empty( $ctx['description'] ) ? $ctx['description'] : wp_strip_all_tags( get_the_excerpt( $post_id ) ),
			'image'                => $article_images,
			'datePublished'        => get_the_date( 'c', $post_id ),
			'dateModified'         => get_the_modified_date( 'c', $post_id ),
			'inLanguage'           => 'pt-BR',
			'isAccessibleForFree'  => true,
			'wordCount'            => $word_count,
			'articleSection'       => ! empty( $ctx['section'] ) ? $ctx['section'] : 'Mercado Imobiliário',
			'keywords'             => ! empty( $ctx['keywords'] ) ? implode( ', ', $ctx['keywords'] ) : 'Mercado Imobiliário, Campina Grande',
			'author'               => array(
				'@type'    => 'Person',
				'name'     => $author_nm,
				'jobTitle' => 'Corretor e Avaliador de Imóveis (CRECI-PB 9972-F)',
				'url'      => home_url( '/sobre/' ),
				'worksFor' => array( '@id' => $site_url . '#organization' ),
			),
			'publisher'            => array( '@id' => $site_url . '#organization' ),
			'speakable'            => array(
				'@type'       => 'SpeakableSpecification',
				'cssSelector' => array( '.imob-single-article-title', '.imob-single-post-content p:first-of-type' ),
			),
		);

		$graph[] = $article_node;

	} elseif ( is_post_type_archive() || is_tax() || is_category() || is_tag() ) {
		// CollectionPage + ItemList para páginas de listagem
		global $wp_query;
		$items = array();
		if ( ! empty( $wp_query->posts ) ) {
			$pos = 1;
			foreach ( array_slice( $wp_query->posts, 0, 12 ) as $p ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos++,
					'name'     => get_the_title( $p->ID ),
					'url'      => get_permalink( $p->ID ),
				);
			}
		}
		if ( ! empty( $items ) ) {
			$graph[] = array(
				'@type'           => 'CollectionPage',
				'@id'             => ( ! empty( $ctx['canonical'] ) ? $ctx['canonical'] : $site_url ) . '#collection',
				'name'            => ! empty( $ctx['title'] ) ? $ctx['title'] : wp_get_document_title(),
				'description'     => ! empty( $ctx['description'] ) ? $ctx['description'] : '',
				'url'             => ! empty( $ctx['canonical'] ) ? $ctx['canonical'] : $site_url,
				'mainEntity'      => array(
					'@type'           => 'ItemList',
					'itemListElement' => $items,
				),
			);
		}
	}

	// Entidade 4: BreadcrumbList
	if ( ! is_front_page() ) {
		$crumbs   = array();
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Início',
			'item'     => $site_url,
		);

		if ( is_singular( 'imovel' ) ) {
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Imóveis',
				'item'     => home_url( '/imoveis/' ),
			);
			$locs = wp_get_post_terms( get_queried_object_id(), 'localidade' );
			if ( ! empty( $locs ) && ! is_wp_error( $locs ) ) {
				$loc_link = get_term_link( $locs[0] );
				if ( ! is_wp_error( $loc_link ) ) {
					$crumbs[] = array(
						'@type'    => 'ListItem',
						'position' => 3,
						'name'     => $locs[0]->name,
						'item'     => $loc_link,
					);
				}
			}
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => count( $crumbs ) + 1,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		} elseif ( is_singular( 'empreendimento' ) ) {
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Empreendimentos',
				'item'     => home_url( '/empreendimentos/' ),
			);
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		} elseif ( is_singular( 'construtora' ) ) {
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Construtoras',
				'item'     => home_url( '/construtoras/' ),
			);
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		} elseif ( is_singular( 'post' ) ) {
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Blog',
				'item'     => home_url( '/nosso-blog/' ),
			);
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		} elseif ( is_singular( 'page' ) ) {
			$crumbs[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title(),
				'item'     => get_permalink(),
			);
		}

		if ( count( $crumbs ) > 1 ) {
			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => ( ! empty( $ctx['canonical'] ) ? $ctx['canonical'] : $site_url ) . '#breadcrumb',
				'itemListElement' => $crumbs,
			);
		}
	}

	$schema_payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo '<script type="application/ld+json" class="imob-schema-graph">' . wp_json_encode( $schema_payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'imob_generate_schema_json', 10 );

/**
 * 3. Renderiza o Sitemap XML Oficial do Google News (/sitemap-news.xml)
 */
function imob_render_google_news_sitemap() {
	status_header( 200 );
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex, follow' );

	$site_name = get_bloginfo( 'name' ) ?: 'IS22 Imóveis | Vinicius Matias';

	$news_posts = get_posts(
		array(
			'post_type'      => array( 'post', 'imovel', 'empreendimento' ),
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php
	foreach ( $news_posts as $p ) :
		$loc      = get_permalink( $p->ID );
		$pub_date = get_the_date( 'c', $p->ID );
		$title    = get_the_title( $p->ID );
		$img_url  = get_the_post_thumbnail_url( $p->ID, 'full' );
		if ( ! $img_url && 'imovel' === $p->post_type ) {
			$gal = get_post_meta( $p->ID, '_imob_galeria', true );
			$gal = is_array( $gal ) ? $gal : ( ! empty( $gal ) ? explode( ',', (string) $gal ) : array() );
			if ( ! empty( $gal[0] ) ) {
				$img_url = wp_get_attachment_image_url( (int) $gal[0], 'full' );
			}
		}
		if ( $img_url && function_exists( 'imob_sanitize_seo_url' ) ) {
			$img_url = imob_sanitize_seo_url( $img_url );
		}
		?>
	<url>
		<loc><?php echo esc_url( $loc ); ?></loc>
		<news:news>
			<news:publication>
				<news:name><?php echo esc_xml( $site_name ); ?></news:name>
				<news:language>pt</news:language>
			</news:publication>
			<news:publication_date><?php echo esc_xml( $pub_date ); ?></news:publication_date>
			<news:title><?php echo esc_xml( $title ); ?></news:title>
		</news:news>
		<?php if ( $img_url ) : ?>
		<image:image>
			<image:loc><?php echo esc_url( $img_url ); ?></image:loc>
			<image:title><?php echo esc_xml( $title ); ?></image:title>
		</image:image>
		<?php endif; ?>
	</url>
<?php endforeach; ?>
</urlset>
<?php
}

/**
 * 4. Gera e renderiza o arquivo /llms.txt para Generative Engine Optimization (GEO - ChatGPT, Perplexity, Gemini, Claude)
 */
function imob_get_llms_txt_content() {
	ob_start();
	$site_name = get_bloginfo( 'name' ) ?: 'IS22 Imóveis | Vinicius Matias';
	$home      = untrailingslashit( home_url() );

	echo "# " . $site_name . "\n\n";
	echo "> Portal imobiliário oficial de Vinicius Matias (CRECI-PB 9972-F) e IS22 Imóveis, referência em compra, venda, lançamentos na planta, condomínios fechados e avaliação técnica de imóveis em Campina Grande - PB e região.\n\n";

	echo "## Informações Institucionais e Contato\n";
	echo "- **Responsável Técnico**: Vinicius Matias (Corretor e Avaliador de Imóveis - CRECI-PB 9972-F)\n";
	echo "- **Cidade / Estado de Atuação**: Campina Grande, Paraíba, Brasil (BR-PB)\n";
	echo "- **Endereço**: Rua Severino Galileu, 296 - Liberdade, Campina Grande - PB\n";
	echo "- **E-mail**: contato@viniciusmatias.com.br\n";
	echo "- **Site Oficial**: " . $home . "/\n";
	echo "- **Catálogo Completo de Imóveis**: " . $home . "/imoveis/\n";
	echo "- **Empreendimentos**: " . $home . "/empreendimentos/\n";
	echo "- **Construtoras Parceiras**: " . $home . "/construtoras/\n";
	echo "- **Avaliação de Imóveis**: " . $home . "/avaliacao-de-imoveis/\n";
	echo "- **Blog e Artigos do Mercado Imobiliário**: " . $home . "/nosso-blog/\n\n";

	echo "## Categorias Principais de Imóveis em Campina Grande\n";
	echo "- [Todos os Imóveis](" . $home . "/imoveis/)\n";
	echo "- [Lançamentos Imobiliários](" . $home . "/lancamentos/)\n";
	echo "- [Apartamentos à Venda](" . $home . "/apartamentos/)\n";
	echo "- [Casas em Condomínio Fechado](" . $home . "/casas-em-condominio-fechado-em-campina-grande/)\n";
	echo "- [Imóveis Minha Casa Minha Vida](" . $home . "/imoveis-minha-casa-minha-vida-em-campina-grande/)\n";
	echo "- [Imóveis com Possibilidade de Entrada Zero](" . $home . "/imoveis-com-possibilidade-de-entrada-zero-em-campina-grande/)\n";
	echo "- [Imóveis com Subsídio da CEHAP](" . $home . "/imoveis-com-subsidio-da-cehap-em-campina-grande/)\n";
	echo "- [Terrenos e Lotes](" . $home . "/terrenos/)\n\n";

	// Empreendimentos Ativos
	$emps = get_posts( array( 'post_type' => 'empreendimento', 'post_status' => 'publish', 'posts_per_page' => 20 ) );
	if ( ! empty( $emps ) ) {
		echo "## Empreendimentos em Destaque\n";
		foreach ( $emps as $e ) {
			$ri   = trim( (string) get_post_meta( $e->ID, '_imob_emp_registro_incorporacao', true ) );
			$prev = get_post_meta( $e->ID, '_imob_emp_previsao', true );
			$meta = array();
			if ( $prev ) {
				$meta[] = "Entrega: $prev";
			}
			if ( '' !== $ri ) {
				$meta[] = "Registro de Incorporação: $ri";
			}
			$suffix = ! empty( $meta ) ? ' (' . implode( ' | ', $meta ) . ')' : '';
			echo "- [" . $e->post_title . "](" . get_permalink( $e->ID ) . ")" . $suffix . "\n";
		}
		echo "\n";
	}

	// Imóveis Recentes
	$imoveis = get_posts( array( 'post_type' => 'imovel', 'post_status' => 'publish', 'posts_per_page' => 25 ) );
	if ( ! empty( $imoveis ) ) {
		echo "## Imóveis Recentes em Campina Grande - PB\n";
		foreach ( $imoveis as $imv ) {
			$preco = get_post_meta( $imv->ID, '_imob_preco_venda', true );
			$area  = get_post_meta( $imv->ID, '_imob_area_privativa', true );
			$qts   = get_post_meta( $imv->ID, '_imob_quartos', true );
			$ri    = trim( (string) get_post_meta( $imv->ID, '_imob_registro_incorporacao', true ) );
			$locs  = wp_get_post_terms( $imv->ID, 'localidade', array( 'fields' => 'names' ) );
			$loc   = ( ! empty( $locs ) && ! is_wp_error( $locs ) ) ? $locs[0] : 'Campina Grande';

			$details = array( $loc );
			if ( $qts ) {
				$details[] = $qts . ' quartos';
			}
			if ( $area ) {
				$details[] = $area . 'm²';
			}
			if ( $preco ) {
				$details[] = 'R$ ' . number_format( (float) $preco, 2, ',', '.' );
			}
			if ( '' !== $ri ) {
				$details[] = 'RI: ' . $ri;
			}
			echo "- [" . $imv->post_title . "](" . get_permalink( $imv->ID ) . ") — " . implode( ' | ', $details ) . "\n";
		}
		echo "\n";
	}

	// Artigos do Blog
	$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 15 ) );
	if ( ! empty( $posts ) ) {
		echo "## Artigos e Guias do Mercado Imobiliário\n";
		foreach ( $posts as $post_item ) {
			echo "- [" . $post_item->post_title . "](" . get_permalink( $post_item->ID ) . ")\n";
		}
	}

	return ob_get_clean();
}

function imob_render_llms_txt() {
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=UTF-8' );
	header( 'X-Robots-Tag: index, follow' );
	echo imob_get_llms_txt_content();
}

/**
 * Sincroniza o arquivo físico llms.txt na raiz (caso exista arquivo legado de temas anteriores)
 */
function imob_sync_physical_llms_txt() {
	$llms_file = ABSPATH . 'llms.txt';
	if ( file_exists( $llms_file ) && is_writable( $llms_file ) ) {
		$current = @file_get_contents( $llms_file );
		if ( false !== $current && ( strpos( $current, '/imovel/' ) !== false || strpos( $current, '/owner/' ) !== false ) ) {
			@file_put_contents( $llms_file, imob_get_llms_txt_content() );
		}
	}
}
add_action( 'init', 'imob_sync_physical_llms_txt', 100 );
add_action( 'save_post', function( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	$llms_file = ABSPATH . 'llms.txt';
	if ( file_exists( $llms_file ) && is_writable( $llms_file ) ) {
		@file_put_contents( $llms_file, imob_get_llms_txt_content() );
	}
}, 99 );

/**
 * 5. Enriquecimento do robots.txt com Sitemap do Google News e permissões explícitas para Crawlers de IA (GEO)
 */
function imob_custom_robots_txt( $output, $public ) {
	if ( '0' == $public ) {
		return $output;
	}

	$home = untrailingslashit( home_url() );

	$output .= "\n# Google News, Discover & Generative Engine Optimization (GEO)\n";
	$output .= "User-agent: Googlebot-News\nAllow: /\n\n";
	$output .= "User-agent: Google-Extended\nAllow: /\n\n";
	$output .= "User-agent: GPTBot\nAllow: /\n\n";
	$output .= "User-agent: OAI-SearchBot\nAllow: /\n\n";
	$output .= "User-agent: ChatGPT-User\nAllow: /\n\n";
	$output .= "User-agent: PerplexityBot\nAllow: /\n\n";
	$output .= "User-agent: ClaudeBot\nAllow: /\n\n";
	$output .= "Sitemap: " . $home . "/sitemap-news.xml\n";

	return $output;
}
add_filter( 'robots_txt', 'imob_custom_robots_txt', 20, 2 );
