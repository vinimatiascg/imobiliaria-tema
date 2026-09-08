<?php
/**
 * SEO Optimization for Imobiliária Tema
 */

// Injeta Open Graph, Twitter Cards e Schema.org no cabeçalho
function imob_seo_head() {
	if ( ! is_singular( 'imovel' ) ) {
		return;
	}

	global $post;
	
	$title = get_the_title( $post->ID );
	$description = wp_trim_words( strip_shortcodes( $post->post_content ), 25 );
	$url = get_permalink( $post->ID );
	$image = get_the_post_thumbnail_url( $post->ID, 'large' );
	if ( ! $image ) {
		$galeria = get_post_meta( $post->ID, '_imob_galeria', true );
		if ( !empty($galeria) && is_array($galeria) ) {
			$image = wp_get_attachment_image_url( $galeria[0], 'large' );
		}
	}

	$preco = get_post_meta( $post->ID, '_imob_preco_venda', true );
	$mapa = get_post_meta( $post->ID, '_imob_mapa', true );
	$status_imovel = wp_get_post_terms( $post->ID, 'status_imovel', array( 'fields' => 'names' ) );
	$status = !empty($status_imovel) ? strtolower($status_imovel[0]) : '';
	$availability = ( strpos($status, 'vendido') !== false || strpos($status, 'alugado') !== false ) ? 'OutOfStock' : 'InStock';

	// Open Graph & Twitter Cards (somente se plugins SEO não estiverem ativos)
	if ( ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) ) {
		?>
		<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
		<meta property="og:description" content="<?php echo esc_attr( $description ); ?>" />
		<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
		<meta property="og:type" content="article" />
		<?php if ( $image ) : ?>
			<meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
			<meta name="twitter:card" content="summary_large_image">
			<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
		<?php endif; ?>
		<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
		<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
		<meta name="description" content="<?php echo esc_attr( $description ); ?>">
		<title><?php echo esc_html( $title ); ?> - <?php bloginfo('name'); ?></title>
		<?php
	}

	// Schema.org RealEstateListing JSON-LD (Sempre injetado pois é específico do nicho)
	$schema = [
		"@context" => "https://schema.org",
		"@type" => "RealEstateListing",
		"name" => $title,
		"description" => $description,
		"url" => $url,
	];

	if ( $image ) {
		$schema["image"] = $image;
	}

	if ( $mapa ) {
		list($lat, $lng) = explode(',', $mapa);
		$schema["geo"] = [
			"@type" => "GeoCoordinates",
			"latitude" => trim($lat),
			"longitude" => trim($lng)
		];
	}

	if ( $preco ) {
		$schema["offers"] = [
			"@type" => "Offer",
			"priceCurrency" => "BRL",
			"price" => $preco,
			"availability" => "https://schema.org/" . $availability,
			"url" => $url
		];
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';

	// BreadcrumbList JSON-LD
	$localidades = wp_get_post_terms( $post->ID, 'localidade' );
	$breadcrumb = [
		"@context" => "https://schema.org",
		"@type" => "BreadcrumbList",
		"itemListElement" => [
			[
				"@type" => "ListItem",
				"position" => 1,
				"name" => "Home",
				"item" => home_url()
			]
		]
	];
	
	if ( ! empty( $localidades ) && ! is_wp_error( $localidades ) ) {
		$breadcrumb["itemListElement"][] = [
			"@type" => "ListItem",
			"position" => 2,
			"name" => $localidades[0]->name,
			"item" => get_term_link( $localidades[0] )
		];
		$breadcrumb["itemListElement"][] = [
			"@type" => "ListItem",
			"position" => 3,
			"name" => $title,
			"item" => $url
		];
	} else {
		$breadcrumb["itemListElement"][] = [
			"@type" => "ListItem",
			"position" => 2,
			"name" => $title,
			"item" => $url
		];
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'imob_seo_head', 5 );

// Forçar atributo lazy load nativo nas imagens anexadas
function imob_add_lazy_loading( $attr, $attachment, $size ) {
	$attr['loading'] = 'lazy';
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'imob_add_lazy_loading', 10, 3 );
