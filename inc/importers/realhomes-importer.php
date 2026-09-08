<?php
/**
 * RealHomes Importer
 */

function imob_add_importer_menu() {
	add_submenu_page(
		'tools.php',
		'Importar do RealHomes',
		'Importar do RealHomes',
		'manage_options',
		'imob-realhomes-importer',
		'imob_render_importer_page'
	);
}
add_action( 'admin_menu', 'imob_add_importer_menu' );

function imob_render_importer_page() {
	?>
	<div class="wrap">
		<h1>Importar do RealHomes</h1>
		<p>Esta ferramenta irá migrar imóveis do tema RealHomes para o novo formato do tema Imobiliária.</p>
		<button id="imob-start-import" class="button button-primary">Iniciar Importação</button>
		<div id="imob-import-log" style="margin-top: 20px; background: #fff; border: 1px solid #ccc; padding: 15px; height: 300px; overflow-y: auto; display: none;"></div>
	</div>
	<script>
		document.getElementById('imob-start-import').addEventListener('click', function() {
			const logDiv = document.getElementById('imob-import-log');
			logDiv.style.display = 'block';
			logDiv.innerHTML += '<p>Iniciando importação (processo em lote)...</p>';
			
			let offset = 0;
			const batchSize = 10;

			function runBatch( currentOffset ) {
				fetch(ajaxurl, { 
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded',
					},
					body: 'action=imob_run_import_batch&offset=' + currentOffset + '&batch_size=' + batchSize
				})
				.then(res => res.json())
				.then(data => {
					if ( data.success ) {
						logDiv.innerHTML += '<p>' + data.message + '</p>';
						logDiv.scrollTop = logDiv.scrollHeight;
						
						if ( data.data.has_more ) {
							runBatch( data.data.next_offset );
						} else {
							logDiv.innerHTML += '<p><strong>Importação concluída com sucesso!</strong></p>';
						}
					} else {
						logDiv.innerHTML += '<p style="color:red;">Erro: ' + data.data + '</p>';
					}
				})
				.catch(err => {
					logDiv.innerHTML += '<p style="color:red;">Erro na requisição AJAX.</p>';
				});
			}

			runBatch( offset );
		});
	</script>
	<?php
}

function imob_run_import_batch() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Permissão negada.' );
	}

	global $wpdb;

	$offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
	$batch_size = isset( $_POST['batch_size'] ) ? intval( $_POST['batch_size'] ) : 10;
	
	// Query properties that haven't been imported yet
	$args = array(
		'post_type' => array( 'property', 'imovel' ), // RealHomes CPT and already converted Imoveis
		'posts_per_page' => $batch_size,
		'offset' => $offset,
		'post_status' => 'any',
		'orderby' => 'ID',
		'order' => 'ASC',
		'meta_query' => array(
			array(
				'key'     => '_imob_importado',
				'compare' => 'NOT EXISTS',
			),
		),
	);
	$properties = get_posts($args);
	
	if ( empty( $properties ) ) {
		wp_send_json_success( array( 'has_more' => false, 'message' => "Nenhum imóvel pendente de migração restante." ) );
	}

	$count = 0;
	foreach ( $properties as $prop ) {
		// 1. Change post type
		set_post_type( $prop->ID, 'imovel' );
		
		// 2. Map Metas
		$price = get_post_meta( $prop->ID, 'REAL_HOMES_property_price', true );
		if ( $price ) update_post_meta( $prop->ID, '_imob_preco_venda', preg_replace('/[^0-9.]/', '', $price) ); // strip currency symbols if needed
		
		$beds = get_post_meta( $prop->ID, 'REAL_HOMES_property_bedrooms', true );
		if ( $beds ) update_post_meta( $prop->ID, '_imob_quartos', $beds );
		
		$baths = get_post_meta( $prop->ID, 'REAL_HOMES_property_bathrooms', true );
		if ( $baths ) update_post_meta( $prop->ID, '_imob_banheiros', $baths );
		
		$size = get_post_meta( $prop->ID, 'REAL_HOMES_property_size', true );
		if ( $size ) update_post_meta( $prop->ID, '_imob_area_privativa', $size );

		$garage = get_post_meta( $prop->ID, 'REAL_HOMES_property_garage', true );
		if ( $garage ) update_post_meta( $prop->ID, '_imob_vagas', $garage );

		$location = get_post_meta( $prop->ID, 'REAL_HOMES_property_location', true );
		if ( $location ) update_post_meta( $prop->ID, '_imob_mapa', $location );

		$images = get_post_meta( $prop->ID, 'REAL_HOMES_property_images', false );
		if ( !empty($images) ) {
			// Some versions save it as multiple meta rows, others as an array in one row.
			if ( is_array($images[0]) ) {
				$images = $images[0];
			}
			update_post_meta( $prop->ID, '_imob_galeria', $images );
		}
		
		// 3. Map Taxonomies using raw DB queries because they are not registered
		$tax_map = [
			'property-type' => 'tipo_imovel',
			'property-city' => 'localidade',
			'property-feature' => 'caracteristica',
			'property-status' => 'status_imovel',
		];

		foreach ( $tax_map as $old_tax => $new_tax ) {
			// Get raw terms from DB
			$query = $wpdb->prepare(
				"SELECT t.name FROM {$wpdb->terms} t 
				 INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
				 INNER JOIN {$wpdb->term_relationships} tr ON tt.term_taxonomy_id = tr.term_taxonomy_id 
				 WHERE tt.taxonomy = %s AND tr.object_id = %d",
				$old_tax, $prop->ID
			);
			$term_names = $wpdb->get_col($query);

			if ( ! empty( $term_names ) ) {
				wp_set_object_terms( $prop->ID, $term_names, $new_tax );
			}
		}

		// Prevent duplicate imports on subsequent runs
		update_post_meta( $prop->ID, '_imob_importado', 'yes' );

		$count++;
	}

	$next_offset = $offset + $batch_size;
	
	wp_send_json_success( array( 
		'has_more' => ( count($properties) == $batch_size ),
		'next_offset' => $next_offset,
		'message' => "Lote processado (Offset: $offset): $count propriedades migradas com sucesso." 
	) );
}
add_action( 'wp_ajax_imob_run_import_batch', 'imob_run_import_batch' );
