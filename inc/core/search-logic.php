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

			// Handle Finalidade
			if ( isset( $_GET['finalidade'] ) && ! empty( $_GET['finalidade'] ) ) {
				$fin_val = sanitize_text_field( $_GET['finalidade'] );
				$tax_name = taxonomy_exists( 'finalidade' ) ? 'finalidade' : 'status_imovel';
				$tax_query[] = array(
					'taxonomy' => $tax_name,
					'field'    => 'slug',
					'terms'    => $fin_val,
				);
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
