<?php
/**
 * Meta Boxes for Empreendimento: Detalhes, Galeria de Fotos e Localização / Mapa
 *
 * @package ImobiliariaTema
 */

function imob_add_empreendimento_meta_boxes() {
	add_meta_box(
		'imob_empreendimento_details',
		__( 'Detalhes do Empreendimento', 'imobiliaria-tema' ),
		'imob_empreendimento_meta_box_callback',
		'empreendimento',
		'normal',
		'high'
	);
	add_meta_box(
		'imob_empreendimento_construtora_side',
		__( 'Construtora Responsável', 'imobiliaria-tema' ),
		'imob_empreendimento_construtora_side_callback',
		'empreendimento',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'imob_add_empreendimento_meta_boxes' );

function imob_empreendimento_admin_scripts( $hook ) {
	global $post;
	if ( $hook === 'post-new.php' || $hook === 'post.php' ) {
		if ( 'empreendimento' === $post->post_type ) {
			wp_enqueue_media();
			$gmaps_key = get_option( 'imob_gmaps_key' );
			if ( $gmaps_key ) {
				wp_enqueue_script( 'google-maps-emp', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $gmaps_key ) . '&libraries=places', array( 'jquery' ), null, true );
			}
		}
	}
}
add_action( 'admin_enqueue_scripts', 'imob_empreendimento_admin_scripts' );

function imob_empreendimento_meta_box_callback( $post ) {
	wp_nonce_field( 'imob_save_empreendimento_meta', 'imob_empreendimento_meta_nonce' );

	$estagio        = get_post_meta( $post->ID, '_imob_emp_estagio', true );
	$previsao       = get_post_meta( $post->ID, '_imob_emp_previsao', true );
	$construtora_id = get_post_meta( $post->ID, '_imob_emp_construtora_id', true );
	$endereco       = get_post_meta( $post->ID, '_imob_emp_endereco', true );
	$lat            = get_post_meta( $post->ID, '_imob_emp_lat', true );
	$lng            = get_post_meta( $post->ID, '_imob_emp_lng', true );
	$galeria        = get_post_meta( $post->ID, '_imob_emp_galeria', true );
	$galeria        = is_array( $galeria ) ? $galeria : ( ! empty( $galeria ) ? explode( ',', $galeria ) : array() );
	$gmaps_key      = get_option( 'imob_gmaps_key' );
	?>
	<style>
		.imob-meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin-bottom: 20px; }
		.imob-meta-field { display: flex; flex-direction: column; gap: 5px; }
		.imob-meta-field label { font-weight: bold; color: #1e293b; font-size: 0.9rem; }
		.imob-meta-field input[type="text"], .imob-meta-field select { width: 100%; height: 36px; border-radius: 4px; border: 1px solid #cbd5e1; }
		.imob-galeria-preview { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
		.imob-galeria-item { position: relative; width: 90px; height: 90px; border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0; }
		.imob-galeria-item img { width: 100%; height: 100%; object-fit: cover; }
		.imob-galeria-item .remove-img { position: absolute; top: 2px; right: 2px; background: rgba(220,38,38,0.9); color: #fff; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; cursor: pointer; font-size: 12px; font-weight: bold; }
		
		/* Botões com cor dourada e hover azul escuro arredondados estilo badges */
		.imob-meta-btn,
		#btn-upload-emp-galeria,
		#btn_search_emp_map {
			background: #B2915A !important;
			color: #FFFFFF !important;
			border: 1px solid #B2915A !important;
			border-radius: 25px !important;
			padding: 7px 18px !important;
			font-weight: 700 !important;
			font-size: 13px !important;
			display: inline-flex !important;
			align-items: center !important;
			gap: 6px !important;
			text-decoration: none !important;
			cursor: pointer !important;
			box-shadow: 0 3px 10px rgba(178, 145, 90, 0.25) !important;
			transition: all 0.25s ease !important;
			height: auto !important;
			line-height: normal !important;
		}
		.imob-meta-btn:hover,
		#btn-upload-emp-galeria:hover,
		#btn_search_emp_map:hover {
			background: #0E1A2B !important;
			color: #FFFFFF !important;
			border-color: #0E1A2B !important;
			box-shadow: 0 4px 14px rgba(14, 26, 43, 0.35) !important;
			transform: translateY(-2px) !important;
		}
		.imob-meta-btn .dashicons,
		#btn-upload-emp-galeria .dashicons,
		#btn_search_emp_map .dashicons {
			color: #FFFFFF !important;
		}
	</style>
	
	<!-- DADOS GERAIS -->
	<div class="imob-meta-grid">
		<div class="imob-meta-field">
			<label for="imob_emp_estagio"><?php _e( 'Estágio da Obra:', 'imobiliaria-tema' ); ?></label>
			<select name="imob_emp_estagio" id="imob_emp_estagio">
				<option value=""><?php _e( 'Selecione o Estágio', 'imobiliaria-tema' ); ?></option>
				<?php
				$estagio_terms = get_terms( array( 'taxonomy' => 'estagio_obra', 'hide_empty' => false ) );
				$post_terms = wp_get_post_terms( $post->ID, 'estagio_obra', array( 'fields' => 'slugs' ) );
				$current_slug = ! empty( $post_terms ) && ! is_wp_error( $post_terms ) ? $post_terms[0] : sanitize_title( $estagio );
				if ( ! empty( $estagio_terms ) && ! is_wp_error( $estagio_terms ) ) {
					foreach ( $estagio_terms as $sterm ) {
						echo '<option value="' . esc_attr( $sterm->slug ) . '" ' . selected( $current_slug, $sterm->slug, false ) . '>' . esc_html( $sterm->name ) . '</option>';
					}
				} else {
					echo '<option value="lancamento" ' . selected( $current_slug, 'lancamento', false ) . '>' . __( 'Lançamento', 'imobiliaria-tema' ) . '</option>';
					echo '<option value="em-construcao" ' . selected( $current_slug, 'em-construcao', false ) . '>' . __( 'Em Construção', 'imobiliaria-tema' ) . '</option>';
					echo '<option value="pronto" ' . selected( $current_slug, 'pronto', false ) . '>' . __( 'Pronto para Morar', 'imobiliaria-tema' ) . '</option>';
				}
				?>
			</select>
		</div>

		<div class="imob-meta-field">
			<label for="imob_emp_previsao"><?php _e( 'Previsão de Entrega:', 'imobiliaria-tema' ); ?></label>
			<input type="text" id="imob_emp_previsao" name="imob_emp_previsao" value="<?php echo esc_attr( $previsao ); ?>" placeholder="Ex: Dezembro/2026">
		</div>

		<div class="imob-meta-field">
			<label for="imob_emp_construtora_id"><?php _e( 'Construtora Responsável:', 'imobiliaria-tema' ); ?></label>
			<select name="imob_emp_construtora_id" id="imob_emp_construtora_id">
				<option value=""><?php _e( 'Selecione a Construtora', 'imobiliaria-tema' ); ?></option>
				<?php
				$construtoras = get_posts( array( 'post_type' => 'construtora', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
				foreach ( $construtoras as $const ) {
					echo '<option value="' . esc_attr( $const->ID ) . '" ' . selected( $construtora_id, $const->ID, false ) . '>' . esc_html( $const->post_title ) . '</option>';
				}
				?>
			</select>
		</div>
	</div>

	<!-- GALERIA DE FOTOS DO EMPREENDIMENTO -->
	<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
		<h4 style="margin: 0 0 8px; color: #0f172a; display: flex; align-items: center; gap: 6px;">
			<span class="dashicons dashicons-format-gallery"></span>
			<?php _e( 'Galeria de Fotos do Empreendimento', 'imobiliaria-tema' ); ?>
		</h4>
		<p class="description" style="margin-bottom: 12px;">
			<?php _e( 'Adicione fotos da fachada, áreas de lazer, decorado e andamento da obra.', 'imobiliaria-tema' ); ?>
		</p>
		
		<button type="button" class="button button-secondary" id="btn-upload-emp-galeria">
			<span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span>
			<?php _e( 'Adicionar Fotos à Galeria', 'imobiliaria-tema' ); ?>
		</button>

		<div class="imob-galeria-preview" id="emp-galeria-container">
			<?php foreach ( $galeria as $img_id ) : 
				$thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
				if ( ! $thumb ) continue;
			?>
				<div class="imob-galeria-item">
					<img src="<?php echo esc_url( $thumb ); ?>">
					<span class="remove-img">&times;</span>
					<input type="hidden" name="imob_emp_galeria[]" value="<?php echo esc_attr( $img_id ); ?>">
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- LOCALIZAÇÃO E MAPA -->
	<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;">
		<h4 style="margin: 0 0 8px; color: #0f172a; display: flex; align-items: center; gap: 6px;">
			<span class="dashicons dashicons-location"></span>
			<?php _e( 'Localização e Mapa do Empreendimento', 'imobiliaria-tema' ); ?>
		</h4>
		<p class="description" style="margin-bottom: 12px;">
			<?php _e( 'Informe o endereço completo ou selecione diretamente as coordenadas para renderizar o mapa do empreendimento.', 'imobiliaria-tema' ); ?>
		</p>

		<div style="display: flex; gap: 10px; margin-bottom: 12px;">
			<input type="text" id="emp_map_search" placeholder="<?php _e( 'Buscar rua, bairro ou endereço...', 'imobiliaria-tema' ); ?>" style="flex: 1; height: 36px;">
			<button type="button" class="button button-primary" id="btn_search_emp_map">
				<span class="dashicons dashicons-search" style="vertical-align: middle;"></span> <?php _e( 'Localizar', 'imobiliaria-tema' ); ?>
			</button>
		</div>

		<?php if ( $gmaps_key ) : ?>
			<div id="imob_emp_admin_map" style="width: 100%; height: 320px; border-radius: 6px; border: 1px solid #cbd5e1; background: #e2e8f0; margin-bottom: 15px;"></div>
		<?php else : ?>
			<div style="padding: 12px; background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; color: #92400e; margin-bottom: 15px; font-size: 0.9rem;">
				<strong><?php _e( 'Dica:', 'imobiliaria-tema' ); ?></strong> <?php _e( 'Para usar o mapa interativo com satélite, insira a Google Maps API Key nas Opções do Tema. Você também pode preencher os campos abaixo manualmente.', 'imobiliaria-tema' ); ?>
			</div>
		<?php endif; ?>

		<div class="imob-meta-grid" style="margin-bottom: 0;">
			<div class="imob-meta-field" style="grid-column: 1 / -1;">
				<label for="imob_emp_endereco"><?php _e( 'Endereço Completo:', 'imobiliaria-tema' ); ?></label>
				<input type="text" id="imob_emp_endereco" name="imob_emp_endereco" value="<?php echo esc_attr( $endereco ); ?>" placeholder="Ex: Av. Brasília, 1000 - Catolé, Campina Grande - PB">
			</div>
			<div class="imob-meta-field">
				<label for="imob_emp_lat"><?php _e( 'Latitude:', 'imobiliaria-tema' ); ?></label>
				<input type="text" id="imob_emp_lat" name="imob_emp_lat" value="<?php echo esc_attr( $lat ); ?>" placeholder="-7.2307">
			</div>
			<div class="imob-meta-field">
				<label for="imob_emp_lng"><?php _e( 'Longitude:', 'imobiliaria-tema' ); ?></label>
				<input type="text" id="imob_emp_lng" name="imob_emp_lng" value="<?php echo esc_attr( $lng ); ?>" placeholder="-35.8811">
			</div>
		</div>
	</div>

	<script>
	jQuery(document).ready(function($){
		// --- Upload Galeria ---
		var empFrame;
		$('#btn-upload-emp-galeria').on('click', function(e){
			e.preventDefault();
			if (empFrame) { empFrame.open(); return; }
			empFrame = wp.media({
				title: '<?php _e( "Selecione fotos para o empreendimento", "imobiliaria-tema" ); ?>',
				button: { text: '<?php _e( "Adicionar à Galeria", "imobiliaria-tema" ); ?>' },
				multiple: true
			});
			empFrame.on('select', function(){
				var attachments = empFrame.state().get('selection').toJSON();
				attachments.forEach(function(att){
					var url = (att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url;
					$('#emp-galeria-container').append(
						'<div class="imob-galeria-item">' +
							'<img src="'+url+'">' +
							'<span class="remove-img">&times;</span>' +
							'<input type="hidden" name="imob_emp_galeria[]" value="'+att.id+'">' +
						'</div>'
					);
				});
			});
			empFrame.open();
		});

		$(document).on('click', '#emp-galeria-container .remove-img', function(){
			$(this).closest('.imob-galeria-item').remove();
		});

		// --- Google Maps Admin ---
		<?php if ( $gmaps_key ) : ?>
		var initLat = <?php echo !empty($lat) && is_numeric($lat) ? floatval($lat) : -7.2307; ?>;
		var initLng = <?php echo !empty($lng) && is_numeric($lng) ? floatval($lng) : -35.8811; ?>;
		var mapEl = document.getElementById('imob_emp_admin_map');
		if (mapEl && typeof google !== 'undefined' && google.maps) {
			var map = new google.maps.Map(mapEl, {
				center: { lat: initLat, lng: initLng },
				zoom: <?php echo !empty($lat) ? '16' : '13'; ?>,
				mapTypeId: 'roadmap'
			});
			var marker = new google.maps.Marker({
				position: { lat: initLat, lng: initLng },
				map: map,
				draggable: true
			});

			google.maps.event.addListener(marker, 'dragend', function() {
				var pos = marker.getPosition();
				$('#imob_emp_lat').val(pos.lat().toFixed(6));
				$('#imob_emp_lng').val(pos.lng().toFixed(6));
			});

			map.addListener('click', function(e) {
				marker.setPosition(e.latLng);
				$('#imob_emp_lat').val(e.latLng.lat().toFixed(6));
				$('#imob_emp_lng').val(e.latLng.lng().toFixed(6));
			});

			$('#btn_search_emp_map').on('click', function(e){
				e.preventDefault();
				var query = $('#emp_map_search').val();
				if (!query) return;
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({ address: query }, function(results, status) {
					if (status === 'OK' && results[0]) {
						map.setCenter(results[0].geometry.location);
						map.setZoom(16);
						marker.setPosition(results[0].geometry.location);
						$('#imob_emp_lat').val(results[0].geometry.location.lat().toFixed(6));
						$('#imob_emp_lng').val(results[0].geometry.location.lng().toFixed(6));
						if (!$('#imob_emp_endereco').val()) {
							$('#imob_emp_endereco').val(results[0].formatted_address);
						}
					}
				});
			});
		}
		<?php endif; ?>
	});
	</script>
	<?php
}

/**
 * Callback para a metabox lateral de Construtora Responsável
 */
function imob_empreendimento_construtora_side_callback( $post ) {
	$construtora_id = get_post_meta( $post->ID, '_imob_emp_construtora_id', true );
	$construtoras   = get_posts( array(
		'post_type'      => 'construtora',
		'numberposts'    => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'post_status'    => 'any',
	) );
	?>
	<div class="imob-side-construtora-box" style="padding: 4px 0;">
		<p style="margin: 0 0 10px; font-size: 13px; color: #64748b; line-height: 1.4;">
			<?php _e( 'Vincule este empreendimento à construtora responsável pela execução do projeto.', 'imobiliaria-tema' ); ?>
		</p>

		<label for="imob_emp_construtora_id_side" style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 12px; text-transform: uppercase; color: #1e293b;">
			<?php _e( 'Selecione a Construtora:', 'imobiliaria-tema' ); ?>
		</label>
		<select name="imob_emp_construtora_id_side" id="imob_emp_construtora_id_side" style="width: 100%; height: 38px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 600; color: #0f172a;">
			<option value=""><?php _e( '— Nenhuma / Selecione —', 'imobiliaria-tema' ); ?></option>
			<?php foreach ( $construtoras as $const ) : ?>
				<option value="<?php echo esc_attr( $const->ID ); ?>" <?php selected( $construtora_id, $const->ID ); ?>>
					<?php echo esc_html( $const->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>

		<?php if ( ! empty( $construtora_id ) ) : 
			$const_post = get_post( (int) $construtora_id );
			if ( $const_post ) :
		?>
			<div style="margin-top: 14px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px;">
				<div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">
					<?php _e( 'Construtora Atual:', 'imobiliaria-tema' ); ?>
				</div>
				<strong style="color: #0f172a; font-size: 14px; display: block; margin-bottom: 8px;">
					<?php echo esc_html( $const_post->post_title ); ?>
				</strong>
				<div style="display: flex; gap: 12px; font-size: 12px;">
					<a href="<?php echo esc_url( get_edit_post_link( $const_post->ID ) ); ?>" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 600;">
						<?php _e( 'Editar Construtora', 'imobiliaria-tema' ); ?> &nearr;
					</a>
					<a href="<?php echo esc_url( get_permalink( $const_post->ID ) ); ?>" target="_blank" style="color: #64748b; text-decoration: none;">
						<?php _e( 'Ver Perfil', 'imobiliaria-tema' ); ?> &nearr;
					</a>
				</div>
			</div>
		<?php endif; endif; ?>

		<div style="margin-top: 14px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=construtora' ) ); ?>" target="_blank" class="imob-meta-btn" style="width: 100%; justify-content: center; box-sizing: border-box;">
				<span class="dashicons dashicons-plus-alt" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></span>
				<?php _e( 'Cadastrar Nova Construtora', 'imobiliaria-tema' ); ?>
			</a>
		</div>
	</div>
	<script>
	jQuery(document).ready(function($){
		// Sincroniza selects se ambos existirem na página
		$('#imob_emp_construtora_id_side').on('change', function(){
			var val = $(this).val();
			if ($('#imob_emp_construtora_id').length) {
				$('#imob_emp_construtora_id').val(val);
			}
		});
		$('#imob_emp_construtora_id').on('change', function(){
			var val = $(this).val();
			if ($('#imob_emp_construtora_id_side').length) {
				$('#imob_emp_construtora_id_side').val(val);
			}
		});
	});
	</script>
	<?php
}

function imob_save_empreendimento_meta( $post_id ) {
	if ( ! isset( $_POST['imob_empreendimento_meta_nonce'] ) || ! wp_verify_nonce( $_POST['imob_empreendimento_meta_nonce'], 'imob_save_empreendimento_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'imob_emp_estagio',
		'imob_emp_previsao',
		'imob_emp_construtora_id',
		'imob_emp_endereco',
		'imob_emp_lat',
		'imob_emp_lng',
	);

	foreach ( $fields as $field ) {
		if ( isset( $_POST[ $field ] ) ) {
			$val = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
			update_post_meta( $post_id, '_' . $field, $val );
			if ( 'imob_emp_estagio' === $field && ! empty( $val ) ) {
				wp_set_object_terms( $post_id, $val, 'estagio_obra' );
			}
		}
	}

	// Fallback para construtora vinda da metabox lateral se não enviada pelo formulário principal
	if ( isset( $_POST['imob_emp_construtora_id_side'] ) && ( ! isset( $_POST['imob_emp_construtora_id'] ) || empty( $_POST['imob_emp_construtora_id'] ) ) ) {
		$side_const_id = intval( $_POST['imob_emp_construtora_id_side'] );
		update_post_meta( $post_id, '_imob_emp_construtora_id', $side_const_id );
	}

	// Salvar galeria de fotos
	if ( isset( $_POST['imob_emp_galeria'] ) && is_array( $_POST['imob_emp_galeria'] ) ) {
		$galeria = array_map( 'intval', $_POST['imob_emp_galeria'] );
		update_post_meta( $post_id, '_imob_emp_galeria', $galeria );

		// Aplicar marca d'água e redimensionamento caso ainda não processadas
		if ( function_exists( 'imob_process_optimize_attachment' ) ) {
			foreach ( $galeria as $img_id ) {
				if ( ! get_post_meta( $img_id, '_imob_optimized', true ) ) {
					imob_process_optimize_attachment( $img_id );
					update_post_meta( $img_id, '_imob_optimized', '1' );
				}
			}
		}
	} else {
		delete_post_meta( $post_id, '_imob_emp_galeria' );
	}
}
add_action( 'save_post_empreendimento', 'imob_save_empreendimento_meta' );
