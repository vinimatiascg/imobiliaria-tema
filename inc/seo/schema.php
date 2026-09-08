<?php
/**
 * SEO: Schema.org JSON-LD Generation
 */

function imob_generate_schema_json() {
	if ( ! is_singular( 'imovel' ) ) {
		return;
	}

	$post_id = get_the_ID();
	$preco = get_post_meta( $post_id, '_imob_preco_venda', true );
	$preco_aluguel = get_post_meta( $post_id, '_imob_preco_aluguel', true );
	$status_terms = wp_get_post_terms( $post_id, 'status_imovel' );
	
	$status = 'InStock'; // Default for available
	if ( ! empty( $status_terms ) && ! is_wp_error( $status_terms ) ) {
		$status_slug = $status_terms[0]->slug;
		if ( in_array( $status_slug, [ 'vendido', 'alugado' ] ) ) {
			$status = 'SoldOut';
		}
	}

	$image = has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'full' ) : '';

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'RealEstateListing',
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ),
		'image'       => $image,
	);

	if ( $preco ) {
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'priceCurrency' => 'BRL',
			'price'         => $preco,
			'availability'  => 'https://schema.org/' . $status,
		);
	} elseif ( $preco_aluguel ) {
		// Schema doesn't have a direct "rent" property on RealEstateListing commonly used, but Offer can have PriceSpecification or similar.
		$schema['offers'] = array(
			'@type'         => 'Offer',
			'priceCurrency' => 'BRL',
			'price'         => $preco_aluguel,
			'availability'  => 'https://schema.org/' . $status,
		);
	}

	// Address based on localidade taxonomy (Simplistic approach)
	$localidades = wp_get_post_terms( $post_id, 'localidade' );
	if ( ! empty( $localidades ) && ! is_wp_error( $localidades ) ) {
		$schema['address'] = array(
			'@type'           => 'PostalAddress',
			'addressLocality' => $localidades[0]->name,
			'addressCountry'  => 'BR',
		);
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
}
add_action( 'wp_head', 'imob_generate_schema_json' );
