<?php
/**
 * Advanced Search Logic
 */

function imob_advanced_search_query( $query ) {
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'imovel' ) ) {

		// Handle Text Search (already handled by WordPress 's' parameter, but we want it for imoveis)
		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$query->set( 's', sanitize_text_field( $_GET['s'] ) );
		}

		$tax_query = array();

		// Handle Tipo de Imóvel
		if ( isset( $_GET['tipo'] ) && ! empty( $_GET['tipo'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'tipo_imovel',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $_GET['tipo'] ),
			);
		}

		// Handle Localidade
		if ( isset( $_GET['localidade'] ) && ! empty( $_GET['localidade'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'localidade',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $_GET['localidade'] ),
			);
		}

		// Handle Finalidade
		if ( isset( $_GET['finalidade'] ) && ! empty( $_GET['finalidade'] ) ) {
			$tax_query[] = array(
				'taxonomy' => 'finalidade',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $_GET['finalidade'] ),
			);
		}

		if ( ! empty( $tax_query ) ) {
			// If there's more than one taxonomy, we need the relation
			if ( count( $tax_query ) > 1 ) {
				$tax_query['relation'] = 'AND';
			}
			$query->set( 'tax_query', $tax_query );
		}
	}
}
add_action( 'pre_get_posts', 'imob_advanced_search_query' );
