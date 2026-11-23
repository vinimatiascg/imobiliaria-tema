<?php
/**
 * Advanced Search Logic
 */

function imob_advanced_search_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() ) {
		$is_imovel_search = is_post_type_archive( 'imovel' ) 
			|| ( $query->is_search() && ( $query->get( 'post_type' ) === 'imovel' || ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'imovel' ) ) );

		if ( $is_imovel_search ) {
			if ( $query->is_search() ) {
				$query->set( 'post_type', 'imovel' );
			}

			// Busca por termo (campo "termo" dos filtros ou "s" dos widgets de busca).
			// A busca nativa do WP olha só título/conteúdo; aqui ela é trocada pela
			// busca ampliada (código, endereço, bairro, tipo, características).
			$termo = imob_get_search_term();
			if ( '' !== $termo ) {
				$query->set( 's', '' );
				$query->set( 'imob_termo', $termo );
			}

			$tax_query = (array) $query->get( 'tax_query' );

			// Handle Tipo de Imóvel
			if ( isset( $_GET['tipo'] ) && ! empty( $_GET['tipo'] ) ) {
				$tax_query[] = array(
					'taxonomy' => 'tipo_imovel',
					'field'    => 'slug',
					'terms'    => sanitize_text_field( $_GET['tipo'] ),
				);
			}

			// Handle Estágio da Obra
			if ( isset( $_GET['estagio'] ) && ! empty( $_GET['estagio'] ) && 'todos' !== $_GET['estagio'] ) {
				$tax_query[] = array(
					'taxonomy' => 'estagio_obra',
					'field'    => 'slug',
					'terms'    => sanitize_text_field( $_GET['estagio'] ),
				);
			}

			// Handle Localidade
			$loc_slug = '';
			if ( ! empty( $_GET['localidade'] ) ) {
				$loc_slug = sanitize_text_field( $_GET['localidade'] );
			} elseif ( ! empty( $_GET['bairro'] ) ) {
				$loc_slug = sanitize_text_field( $_GET['bairro'] );
			} elseif ( ! empty( $_GET['cidade'] ) ) {
				$loc_slug = sanitize_text_field( $_GET['cidade'] );
			}

			if ( ! empty( $loc_slug ) ) {
				$tax_query[] = array(
					'taxonomy' => 'localidade',
					'field'    => 'slug',
					'terms'    => $loc_slug,
				);
			}

			// Handle Finalidade / Modalidade (Venda ou Aluguel)
			if ( isset( $_GET['finalidade'] ) && ! empty( $_GET['finalidade'] ) ) {
				$fin_val = strtolower( sanitize_text_field( $_GET['finalidade'] ) );
				if ( in_array( $fin_val, array( 'venda', 'aluguel' ), true ) ) {
					$tax_name = taxonomy_exists( 'finalidade' ) ? 'finalidade' : 'status_imovel';
					$tax_terms = ( 'venda' === $fin_val )
						? array( 'a-venda', 'para-vender', 'venda', 'para-venda', 'vender', 'disponivel' )
						: array( 'aluguel', 'para-alugar', 'locacao', 'alugar', 'alugado' );
					
					// Verifica se existe algum termo cadastrado
					$existing_term = false;
					foreach ( $tax_terms as $tt ) {
						if ( term_exists( $tt, $tax_name ) ) {
							$existing_term = $tt;
							break;
						}
					}

					if ( $existing_term ) {
						$tax_query[] = array(
							'taxonomy' => $tax_name,
							'field'    => 'slug',
							'terms'    => $tax_terms,
						);
					} else {
						// Fallback por meta campo de preço de venda / aluguel
						$meta_query = (array) $query->get( 'meta_query' );
						$price_key = ( 'venda' === $fin_val ) ? '_imob_preco_venda' : '_imob_preco_aluguel';
						$meta_query[] = array(
							'key'     => $price_key,
							'value'   => 0,
							'compare' => '>',
							'type'    => 'NUMERIC',
						);
						$query->set( 'meta_query', $meta_query );
					}
				}
			}

			if ( ! empty( $tax_query ) ) {
				if ( count( $tax_query ) > 1 && ! isset( $tax_query['relation'] ) ) {
					$tax_query['relation'] = 'AND';
				}
				$query->set( 'tax_query', $tax_query );
			}
		}
	}
}
add_action( 'pre_get_posts', 'imob_advanced_search_query' );

/**
 * Termo digitado na busca de imóveis (parâmetro "termo" ou, por compatibilidade, "s").
 *
 * @return string
 */
function imob_get_search_term() {
	$raw = '';
	if ( isset( $_GET['termo'] ) && '' !== $_GET['termo'] ) {
		$raw = $_GET['termo'];
	} elseif ( isset( $_GET['s'] ) && '' !== $_GET['s'] ) {
		$raw = $_GET['s'];
	}
	if ( ! is_string( $raw ) ) {
		return '';
	}
	$termo = trim( sanitize_text_field( wp_unslash( $raw ) ) );
	return function_exists( 'mb_substr' ) ? mb_substr( $termo, 0, 100 ) : substr( $termo, 0, 100 );
}

/**
 * Busca ampliada de imóveis pela query var "imob_termo".
 *
 * Cada palavra do termo precisa aparecer em algum destes lugares: título, conteúdo,
 * resumo, código de referência, endereço ou nome de localidade, tipo e característica.
 * Funciona na query principal (arquivo/busca) e em WP_Query próprias (template-imoveis.php)
 * que passem 'imob_termo' => '...'.
 *
 * @param array    $clauses Cláusulas SQL.
 * @param WP_Query $query   Query.
 * @return array
 */
function imob_termo_posts_clauses( $clauses, $query ) {
	$termo = (string) $query->get( 'imob_termo' );
	if ( '' === $termo ) {
		return $clauses;
	}

	global $wpdb;

	$words = preg_split( '/\s+/u', $termo, -1, PREG_SPLIT_NO_EMPTY );
	$words = array_slice( array_unique( $words ), 0, 8 );

	foreach ( $words as $word ) {
		$like = '%' . $wpdb->esc_like( $word ) . '%';

		$clauses['where'] .= $wpdb->prepare(
			" AND (
				{$wpdb->posts}.post_title LIKE %s
				OR {$wpdb->posts}.post_content LIKE %s
				OR {$wpdb->posts}.post_excerpt LIKE %s
				OR EXISTS (
					SELECT 1 FROM {$wpdb->postmeta} imob_pm
					WHERE imob_pm.post_id = {$wpdb->posts}.ID
					AND imob_pm.meta_key IN ( '_imob_ref', '_imob_endereco' )
					AND imob_pm.meta_value LIKE %s
				)
				OR EXISTS (
					SELECT 1 FROM {$wpdb->term_relationships} imob_tr
					INNER JOIN {$wpdb->term_taxonomy} imob_tt ON imob_tt.term_taxonomy_id = imob_tr.term_taxonomy_id
					INNER JOIN {$wpdb->terms} imob_t ON imob_t.term_id = imob_tt.term_id
					WHERE imob_tr.object_id = {$wpdb->posts}.ID
					AND imob_tt.taxonomy IN ( 'localidade', 'tipo_imovel', 'caracteristica' )
					AND imob_t.name LIKE %s
				)
			)",
			$like,
			$like,
			$like,
			$like,
			$like
		);
	}

	return $clauses;
}
add_filter( 'posts_clauses', 'imob_termo_posts_clauses', 10, 2 );

/**
 * Retorna filtros padrão para páginas de listagem de imóveis baseadas no slug da página
 *
 * @param string $post_slug Slug da página atual
 * @return array
 */
function imob_get_page_default_property_filters( $post_slug ) {
	$map = array(
		'imoveis'                                                     => array(),
		'todos-os-imoveis'                                            => array(),
		'resultados-da-pesquisa'                                      => array(),
		'imoveis-em-destaque'                                         => array(),
		'imoveis-a-venda'                                             => array( 'finalidade' => 'venda' ),
		'lancamentos'                                                 => array( 'tipo' => 'lancamento' ),
		'imoveis-com-possibilidade-de-entrada-zero-em-campina-grande' => array( 'tipo' => 'zero-de-entrada' ),
		'imoveis-minha-casa-minha-vida-em-campina-grande'             => array( 'tipo' => 'mcmv' ),
		'imoveis-com-subsidio-da-cehap-em-campina-grande'             => array( 'tipo' => 'subsidio-cehap' ),
		'apartamentos'                                                => array( 'tipo' => 'apartamento' ),
		'apartamentos-em-campina-grande'                              => array( 'tipo' => 'apartamento', 'localidade' => 'campina-grande' ),
		'casas'                                                       => array( 'tipo' => 'casa' ),
		'casas-em-condominio'                                         => array( 'tipo' => 'casa-em-condominio' ),
		'casas-em-condominio-fechado-em-campina-grande'               => array( 'tipo' => 'casa-em-condominio' ),
		'condominios-fechados-em-campina-grande'                      => array( 'tipo' => 'casa-em-condominio' ),
		'imoveis-comerciais'                                          => array( 'tipo' => 'comercial' ),
		'imoveis-rurais'                                              => array( 'tipo' => 'outros' ),
		'terrenos'                                                    => array( 'tipo' => 'terreno' ),
		'imoveis-no-ecoville'                                         => array( 'localidade' => 'ecoville' ),
	);

	return isset( $map[ $post_slug ] ) ? $map[ $post_slug ] : null;
}

/**
 * Use archive-imovel.php / template-imoveis.php / template-blog.php for search and listing pages
 * Executa com prioridade 99 para prevalecer sobre templates legados do Elementor / RealHomes
 */
function imob_search_template_include( $template ) {
	if ( is_admin() ) {
		return $template;
	}

	// Busca de imóveis
	if ( is_search() && ( get_query_var( 'post_type' ) === 'imovel' || ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'imovel' ) ) ) {
		$archive_template = locate_template( array( 'archive-imovel.php' ) );
		if ( $archive_template ) {
			return $archive_template;
		}
	}

	// Páginas de Blog e Listagens de Imóveis
	if ( is_page() ) {
		$queried_obj = get_queried_object();
		if ( $queried_obj && ! empty( $queried_obj->post_name ) ) {
			$slug = $queried_obj->post_name;

			// Página Nosso Blog
			if ( 'nosso-blog' === $slug ) {
				$blog_tpl = locate_template( array( 'template-blog.php' ) );
				if ( $blog_tpl ) {
					return $blog_tpl;
				}
			}

			// Páginas de listagem de imóveis por slug ou template legado do RealHomes
			$page_tpl_meta = get_post_meta( $queried_obj->ID, '_wp_page_template', true );
			$is_legacy_prop_tpl = ! empty( $page_tpl_meta ) && (
				strpos( $page_tpl_meta, 'templates/properties' ) === 0 ||
				'template-imoveis.php' === $page_tpl_meta
			);

			if ( null !== imob_get_page_default_property_filters( $slug ) || $is_legacy_prop_tpl ) {
				$imoveis_tpl = locate_template( array( 'template-imoveis.php' ) );
				if ( $imoveis_tpl ) {
					return $imoveis_tpl;
				}
			}
		}
	}

	return $template;
}
add_filter( 'template_include', 'imob_search_template_include', 99 );
