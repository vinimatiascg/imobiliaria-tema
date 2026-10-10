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

			// Handle Text Search
			if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
				$query->set( 's', sanitize_text_field( $_GET['s'] ) );
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
					$tax_terms = ( 'venda' === $fin_val ) ? array( 'venda', 'para-venda', 'vender' ) : array( 'aluguel', 'para-alugar', 'locacao', 'alugar' );
					
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
 * Use archive-imovel.php template for search results of imoveis
 */
function imob_search_template_include( $template ) {
	if ( ! is_admin() && is_search() && ( get_query_var( 'post_type' ) === 'imovel' || ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'imovel' ) ) ) {
		$archive_template = locate_template( array( 'archive-imovel.php' ) );
		if ( $archive_template ) {
			return $archive_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'imob_search_template_include' );
