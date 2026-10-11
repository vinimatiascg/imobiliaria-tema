<?php
/**
 * The template for displaying all single imóveis.
 *
 * @package ImobiliariaTema
 */

get_header(); ?>

<main id="primary" class="site-main imob-single-imovel">
	<?php
	while ( have_posts() ) :
		the_post();
		$ref = get_post_meta( get_the_ID(), '_imob_ref', true );
		$preco_venda = get_post_meta( get_the_ID(), '_imob_preco_venda', true );
		$preco_antes = get_post_meta( get_the_ID(), '_imob_preco_antes_texto', true );
		$preco_depois = get_post_meta( get_the_ID(), '_imob_preco_depois_texto', true );
		$preco_anterior = get_post_meta( get_the_ID(), '_imob_preco_anterior', true );
		$quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
		$banheiros = get_post_meta( get_the_ID(), '_imob_banheiros', true );
		$suites = get_post_meta( get_the_ID(), '_imob_suites', true );
		$vagas = get_post_meta( get_the_ID(), '_imob_vagas', true );
		$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
		$galeria = get_post_meta( get_the_ID(), '_imob_galeria', true ); // array of attachment IDs
		$mapa = get_post_meta( get_the_ID(), '_imob_mapa', true ); // lat,lng string
		$endereco_completo = get_post_meta( get_the_ID(), '_imob_endereco', true );
		$empreendimento = get_post_meta( get_the_ID(), '_imob_empreendimento', true );
		
		$tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel' );
		$localidades = wp_get_post_terms( get_the_ID(), 'localidade' );
		$status_terms = wp_get_post_terms( get_the_ID(), 'status_imovel' );

		$tipo = (!empty($tipos) && !is_wp_error($tipos)) ? $tipos[0]->name : 'IMÓVEL';
		$bairro = (!empty($localidades) && !is_wp_error($localidades)) ? $localidades[0]->name : 'Bairro não informado';
		$status_imovel = (!empty($status_terms) && !is_wp_error($status_terms)) ? $status_terms[0]->name : '';
		
		// Contact WhatsApp: check corretor, then user profile, then global option
		$corretores = wp_get_post_terms( get_the_ID(), 'corretor' );
		$contact_whatsapp = '';
		if ( ! empty($corretores) && ! is_wp_error($corretores) ) {
			$contact_whatsapp = get_term_meta( $corretores[0]->term_id, 'imob_corretor_whatsapp', true );
		}
		if ( empty($contact_whatsapp) && function_exists('imob_get_user_contact_data') ) {
			$u_data = imob_get_user_contact_data( get_the_author_meta('ID') );
			if ( $u_data && ! empty($u_data['whatsapp']) ) {
				$contact_whatsapp = $u_data['whatsapp'];
			}
		}
		if ( empty($contact_whatsapp) ) {
			$contact_whatsapp = get_option( 'imob_contact_whatsapp' );
		}
		if ( empty($contact_whatsapp) ) {
			$contact_whatsapp = '5583999999999'; // fallback number
		}
		
		$wa_number = preg_replace('/[^0-9]/', '', $contact_whatsapp);
		if (substr($wa_number, 0, 2) !== '55' && strlen($wa_number) <= 11) {
			$wa_number = '55' . $wa_number;
		}

		$wa_text = urlencode("Gostaria de mais informações sobre o imóvel " . $ref . " - " . get_the_title() . ": " . get_permalink());
		$whatsapp_link = "https://wa.me/{$wa_number}?text={$wa_text}";

		// Build gallery array including featured image
		$images = [];
		if ( has_post_thumbnail() ) {
			$images[] = get_post_thumbnail_id();
		}
		if ( !empty($galeria) && is_array($galeria) ) {
			$images = array_merge($images, $galeria);
		} elseif ( !empty($galeria) && is_string($galeria) ) {
			// fallback if stored as comma separated string
			$galeria_ids = explode(',', $galeria);
			$images = array_merge($images, $galeria_ids);
		}
		$images = array_unique($images);
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<!-- Galeria de Imagens -->
			<?php if ( !empty($images) ) : ?>
				<div class="imob-single-gallery-section">
					<div class="imob-gallery-main">
						<?php
						$full_url = wp_get_attachment_image_url( $images[0], 'full' );
						echo '<a href="'.esc_url($full_url).'" class="glightbox" data-gallery="imovel-gallery">';
						echo wp_get_attachment_image( $images[0], 'full' );
						echo '</a>';
						?>
					</div>
					<?php if ( count($images) > 1 ) : ?>
						<div class="imob-gallery-thumbs">
							<?php foreach ( array_slice($images, 1, 6) as $index => $img_id ) : 
								$thumb_full = wp_get_attachment_image_url( $img_id, 'full' );
								if ( ! $thumb_full ) continue;
							?>
								<div class="imob-thumb-item">
									<a href="<?php echo esc_url($thumb_full); ?>" class="glightbox" data-gallery="imovel-gallery">
										<?php echo wp_get_attachment_image( $img_id, 'medium' ); ?>
									</a>
									<?php if ( $index === 5 && count($images) > 7 ) : ?>
										<div class="imob-thumb-overlay" style="pointer-events: none;">
											<span class="material-symbols-outlined">photo_camera</span>
											<span><?php echo count($images); ?><br>fotos</span>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>

						<!-- Links ocultos para que todas as fotos adicionais participem do slideshow -->
						<?php if ( count( $images ) > 7 ) : ?>
							<div class="imob-gallery-hidden" style="display: none;">
								<?php foreach ( array_slice( $images, 7 ) as $hidden_id ) : 
									$hidden_full = wp_get_attachment_image_url( $hidden_id, 'full' );
									if ( ! $hidden_full ) continue;
								?>
									<a href="<?php echo esc_url( $hidden_full ); ?>" class="glightbox" data-gallery="imovel-gallery"></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<!-- Script para iniciar o Lightbox com loop e navegação completa -->
				<script>
				document.addEventListener('DOMContentLoaded', function() {
					if (typeof GLightbox !== 'undefined') {
						const lightbox = GLightbox({ 
							selector: '.glightbox',
							loop: true,
							zoomable: true,
							touchNavigation: true
						});
					}
				});
				</script>
			<?php endif; ?>

			<div class="container imob-single-container">
				<div class="imob-single-content">
					
					<!-- Header do Imóvel -->
					<header class="imob-single-header imob-section-box" style="padding-top: 30px;">
						<div class="imob-badges-preco-wrap">
							<div class="imob-badges" style="display: flex; gap: 10px; flex-wrap: wrap;">
								<?php
								// Status
								if (!empty($status_terms) && !is_wp_error($status_terms)) {
									foreach($status_terms as $term) {
										echo '<a href="'.esc_url(get_term_link($term)).'" class="badge badge-status">'.esc_html(imob_strtoupper($term->name)).'</a>';
									}
								}
								// Tipos
								if (!empty($tipos) && !is_wp_error($tipos)) {
									foreach($tipos as $term) {
										echo '<a href="'.esc_url(get_term_link($term)).'" class="badge badge-tipo">'.esc_html(imob_strtoupper($term->name)).'</a>';
									}
								}
								// Estágio da Obra
								$estagios = wp_get_post_terms( get_the_ID(), 'estagio_obra' );
								if ( ! empty($estagios) && ! is_wp_error($estagios) ) {
									foreach($estagios as $stg) {
										echo '<a href="'.esc_url(get_term_link($stg)).'" class="badge badge-estagio">'.esc_html(imob_strtoupper($stg->name)).'</a>';
									}
								}
								// Empreendimento vinculado (com link para a listagem do empreendimento)
								$badge_emp_single = imob_render_empreendimento_badge( get_the_ID(), true );
								if ( ! empty( $badge_emp_single ) ) {
									echo $badge_emp_single;
								}

								// Localidades
								if (!empty($localidades) && !is_wp_error($localidades)) {
									foreach($localidades as $term) {
										echo '<a href="'.esc_url(get_term_link($term)).'" class="badge badge-localidade" style="background: #999; color: #fff; text-decoration: none;">'.esc_html(imob_strtoupper($term->name)).'</a>';
									}
								}
								?>
							</div>
						</div>

						<?php the_title( '<h1 class="imob-single-title">', '</h1>' ); ?>
						
						<div style="display: flex; justify-content: flex-end;">
							<?php echo imob_get_formatted_price( get_the_ID(), true ); ?>
						</div>
						
						<div class="imob-single-location">
							<span class="material-symbols-outlined">location_on</span> 
							<?php 
							if ( $endereco_completo ) {
								echo esc_html($endereco_completo);
							} else {
								echo esc_html($bairro) . " - Campina Grande";
							}
							?>
						</div>

						<div class="imob-single-features-bar">
							<?php if ( $quartos ) : ?>
								<div class="feature-item">
									<div class="feature-label">Quartos</div>
									<div class="feature-val">
										<span class="feature-icon"><span class="material-symbols-outlined">bed</span></span>
										<span class="feature-num"><?php echo esc_html( $quartos ); ?></span>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $banheiros ) : ?>
								<div class="feature-item">
									<div class="feature-label">Banheiros</div>
									<div class="feature-val">
										<span class="feature-icon"><span class="material-symbols-outlined">shower</span></span>
										<span class="feature-num"><?php echo esc_html( $banheiros ); ?></span>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $suites ) : ?>
								<div class="feature-item">
									<div class="feature-label">Suítes</div>
									<div class="feature-val">
										<span class="feature-icon"><span class="material-symbols-outlined">bathroom</span></span>
										<span class="feature-num"><?php echo esc_html( $suites ); ?></span>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $vagas ) : ?>
								<div class="feature-item">
									<div class="feature-label">Garagem</div>
									<div class="feature-val">
										<span class="feature-icon"><span class="material-symbols-outlined">directions_car</span></span>
										<span class="feature-num"><?php echo esc_html( $vagas ); ?></span>
									</div>
								</div>
							<?php endif; ?>
							<?php if ( $area ) : ?>
								<div class="feature-item">
									<div class="feature-label">Área</div>
									<div class="feature-val">
										<span class="feature-icon"><span class="material-symbols-outlined">crop</span></span>
										<span class="feature-num"><?php echo esc_html( $area ); ?>m²</span>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<!-- Informações de Vínculo com Empreendimento e Construtora -->
						<?php 
						$emp_vinculo   = imob_get_imovel_empreendimento( get_the_ID() );
						$const_vinculo = imob_get_imovel_construtora( get_the_ID() );
						if ( $emp_vinculo || $const_vinculo ) : ?>
							<div class="imob-imovel-relations-banner" style="display: flex; flex-wrap: wrap; gap: 15px; margin-top: 25px; padding: 16px 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
								<?php if ( $emp_vinculo && ! empty( $emp_vinculo['nome'] ) ) : ?>
									<div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem;">
										<span class="material-symbols-outlined" style="color: var(--accent-color); font-size: 20px;">domain</span>
										<span style="color: var(--text-light);"><?php _e( 'Empreendimento:', 'imobiliaria-tema' ); ?></span>
										<?php if ( ! empty( $emp_vinculo['url'] ) ) : ?>
											<a href="<?php echo esc_url( $emp_vinculo['url'] ); ?>" style="color: var(--primary-color); font-weight: 700; text-decoration: none;" title="<?php _e( 'Ver todos os imóveis deste empreendimento', 'imobiliaria-tema' ); ?>">
												<?php echo esc_html( $emp_vinculo['nome'] ); ?> &rarr;
											</a>
										<?php else : ?>
											<strong style="color: var(--primary-color);"><?php echo esc_html( $emp_vinculo['nome'] ); ?></strong>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( $const_vinculo && ! empty( $const_vinculo['nome'] ) ) : ?>
									<div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem;">
										<span class="material-symbols-outlined" style="color: var(--accent-color); font-size: 20px;">apartment</span>
										<span style="color: var(--text-light);"><?php _e( 'Construtora:', 'imobiliaria-tema' ); ?></span>
										<?php if ( ! empty( $const_vinculo['url'] ) ) : ?>
											<a href="<?php echo esc_url( $const_vinculo['url'] ); ?>" style="color: var(--primary-color); font-weight: 700; text-decoration: none;" title="<?php _e( 'Ver todos os imóveis desta construtora', 'imobiliaria-tema' ); ?>">
												<?php echo esc_html( $const_vinculo['nome'] ); ?> &rarr;
											</a>
										<?php else : ?>
											<strong style="color: var(--primary-color);"><?php echo esc_html( $const_vinculo['nome'] ); ?></strong>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</header>

					<!-- Descrição -->
					<div class="imob-section-box">
						<div class="imob-section-header">
							<h2>Descrição</h2>
							<span class="imob-date">Data do anúncio: <?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
						</div>
						<div class="imob-section-content">
							<?php the_content(); ?>
							<br>
							<div class="imob-cta-box" style="text-align: center; margin-top: 30px;">
								<a href="<?php echo esc_url($whatsapp_link); ?>" target="_blank" class="btn-primary btn-cta-blue"><span class="material-symbols-outlined">chat</span> Entre em contato</a>
							</div>
						</div>
					</div>

					<!-- Mapa -->
					<?php if ( $mapa ) : ?>
						<div class="imob-section-box imob-map-section" style="padding: 0; overflow: hidden; border: none;">
							<div class="imob-section-header">
								<h2>Localização</h2>
							</div>
							<div class="imob-section-content">
								<?php
								$gmaps_key = get_option( 'imob_gmaps_key' );
								if ( $gmaps_key ) {
									list($lat, $lng) = explode(',', $mapa);
									$lat = trim($lat);
									$lng = trim(str_replace(',18', '', $lng)); // Realhomes sometimes saves zoom
									$lng = trim(str_replace(',16', '', $lng));
									$lng = trim(str_replace(',14', '', $lng));
									?>
									<div id="imob-map" style="width:100%; height: 400px; border-radius: 10px;"></div>
									<script>
									function initImobMap() {
										var location = { lat: <?php echo esc_attr($lat); ?>, lng: <?php echo esc_attr($lng); ?> };
										var map = new google.maps.Map(document.getElementById('imob-map'), {
											zoom: 18,
											center: location,
											mapTypeId: 'satellite'
										});
										var marker = new google.maps.Marker({
											position: location,
											map: map
										});
									}
									window.addEventListener('load', initImobMap);
									</script>
								<?php } else { ?>
									<img src="https://via.placeholder.com/800x300.png?text=Mapa+do+Im%C3%B3vel+(API+Key+necess%C3%A1ria)" style="width:100%; display:block; border-radius: 10px;" alt="Mapa">
								<?php } ?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Características -->
					<?php if ( !empty($caracteristicas) ) : ?>
						<div class="imob-section-box">
							<div class="imob-section-header">
								<h2>Características</h2>
							</div>
							<div class="imob-section-content">
								<ul class="imob-caracteristicas-list">
									<?php 
									$caracteristicas = wp_get_post_terms( get_the_ID(), 'caracteristica', array( 'fields' => 'names' ) );
									if ( !empty($caracteristicas) && !is_wp_error($caracteristicas) ) :
										foreach ( $caracteristicas as $carac ) : ?>
											<li><span class="material-symbols-outlined">check_circle</span> <?php echo esc_html($carac); ?></li>
										<?php endforeach;
									endif; ?>
								</ul>
								<br>
								<div class="imob-cta-box" style="text-align: center; margin-top: 30px;">
									<a href="<?php echo esc_url($whatsapp_link); ?>" target="_blank" class="btn-primary btn-cta-blue"><span class="material-symbols-outlined">chat</span> Entre em contato</a>
								</div>
							</div>
						</div>
					<?php endif; ?>

				</div><!-- .imob-single-content -->

				<!-- Sidebar -->
				<aside class="imob-single-sidebar">
					<!-- Widgets Dedicados de Empreendimento e Construtora -->
					<?php 
					$emp_side   = imob_get_imovel_empreendimento( get_the_ID() );
					$const_side = imob_get_imovel_construtora( get_the_ID() );
					?>

					<?php if ( $emp_side && ! empty( $emp_side['nome'] ) ) : ?>
						<div class="imob-sidebar-widget imob-sidebar-emp-widget" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 22px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
							<div style="font-size: 0.75rem; text-transform: uppercase; color: var(--accent-color); font-weight: 700; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
								<span class="material-symbols-outlined" style="font-size: 18px; color: var(--accent-color);">domain</span>
								<?php _e( 'Empreendimento', 'imobiliaria-tema' ); ?>
							</div>
							<h3 style="margin: 0 0 12px; font-size: 1.15rem; color: var(--primary-color); font-weight: 700;">
								<?php echo esc_html( $emp_side['nome'] ); ?>
							</h3>
							<?php if ( ! empty( $emp_side['url'] ) ) : ?>
								<a href="<?php echo esc_url( $emp_side['url'] ); ?>" class="imob-btn-relation" title="<?php _e( 'Ver todos os imóveis deste empreendimento', 'imobiliaria-tema' ); ?>">
									<span class="material-symbols-outlined" style="font-size: 18px;">domain</span>
									<?php _e( 'Ver imóveis deste empreendimento', 'imobiliaria-tema' ); ?> &rarr;
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $const_side && ! empty( $const_side['nome'] ) ) : ?>
						<div class="imob-sidebar-widget imob-sidebar-const-widget" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 8px; padding: 22px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
							<div style="font-size: 0.75rem; text-transform: uppercase; color: var(--accent-color); font-weight: 700; letter-spacing: 0.5px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
								<span class="material-symbols-outlined" style="font-size: 18px; color: var(--accent-color);">apartment</span>
								<?php _e( 'Construtora', 'imobiliaria-tema' ); ?>
							</div>
							<h3 style="margin: 0 0 12px; font-size: 1.15rem; color: var(--primary-color); font-weight: 700;">
								<?php echo esc_html( $const_side['nome'] ); ?>
							</h3>
							<?php if ( ! empty( $const_side['url'] ) ) : ?>
								<a href="<?php echo esc_url( $const_side['url'] ); ?>" class="imob-btn-relation" title="<?php _e( 'Ver todos os imóveis desta construtora', 'imobiliaria-tema' ); ?>">
									<span class="material-symbols-outlined" style="font-size: 18px;">apartment</span>
									<?php _e( 'Ver imóveis desta construtora', 'imobiliaria-tema' ); ?> &rarr;
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<!-- Corretor Responsável / Contato -->
					<div class="imob-sidebar-widget">
						<?php
						$corretores = wp_get_post_terms( get_the_ID(), 'corretor' );
						if ( ! empty( $corretores ) && ! is_wp_error( $corretores ) ) :
							foreach ( $corretores as $corretor ) :
								$creci = get_term_meta( $corretor->term_id, 'imob_corretor_creci', true );
								$cnai = get_term_meta( $corretor->term_id, 'imob_corretor_cnai', true );
								$resumo = get_term_meta( $corretor->term_id, 'imob_corretor_resumo', true );
								$whatsapp = get_term_meta( $corretor->term_id, 'imob_corretor_whatsapp', true );
								$instagram = get_term_meta( $corretor->term_id, 'imob_corretor_instagram', true );
								$email = get_term_meta( $corretor->term_id, 'imob_corretor_email', true );
								$foto = get_term_meta( $corretor->term_id, 'imob_corretor_foto', true );
								$foto_url = $foto ? wp_get_attachment_image_url( $foto, 'thumbnail' ) : 'https://via.placeholder.com/70x70.png?text=FOTO';
								$corretor_link = get_term_link( $corretor );
								?>
								<h3 class="widget-title">Corretor responsável:</h3>
								<div class="imob-corretor-card" style="margin-bottom: 20px;">
									<div class="imob-corretor-info" style="display: flex; justify-content: space-between; align-items: flex-start;">
										<div class="imob-corretor-details" style="flex: 1;">
											<h4 style="font-size: 1.2rem;"><a href="<?php echo esc_url($corretor_link); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html( $corretor->name ); ?></a></h4>
											<p class="role" style="color: var(--text-light);">Corretor e avaliador de imóveis</p>
											<?php if ( $creci || $cnai ) : ?>
												<p class="creci" style="color: #999;">CRECI <?php echo esc_html($creci); ?> <?php echo $cnai ? 'CNAI '.esc_html($cnai) : ''; ?></p>
											<?php endif; ?>
										</div>
										<a href="<?php echo esc_url($corretor_link); ?>" class="imob-corretor-avatar" style="flex-shrink: 0; margin-left: 15px;">
											<div class="avatar-placeholder" style="width: 70px; height: 70px; border-radius: 10px; background: url('<?php echo esc_url($foto_url); ?>') center/cover;"></div>
										</a>
									</div>
									<?php if ( $resumo ) : ?>
										<div class="imob-corretor-resumo" style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 10px;">
											<?php echo wp_kses_post( $resumo ); ?>
										</div>
									<?php endif; ?>
									<div class="imob-corretor-contact" style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px;">
										<p style="margin: 0; font-size: 0.9rem; font-weight: 600; line-height: 1.3; max-width: 150px;"><strong>Entre em contato</strong> e realize o sonho do imóvel próprio:</p>
										<div class="imob-social-icons">
											<?php if ( $whatsapp ) : ?>
												<a href="https://wa.me/55<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" class="social-icon" title="WhatsApp"><span class="material-symbols-outlined">chat</span></a>
											<?php endif; ?>
											<?php if ( $instagram ) : ?>
												<a href="https://instagram.com/<?php echo esc_attr(str_replace('@', '', $instagram)); ?>" target="_blank" class="social-icon" title="Instagram"><span class="material-symbols-outlined">photo_camera</span></a>
											<?php endif; ?>
											<?php if ( $email ) : ?>
												<a href="mailto:<?php echo esc_attr($email); ?>" class="social-icon" title="E-mail"><span class="material-symbols-outlined">mail</span></a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else: 
							// Carrega os dados de contato do usuário (autor do post)
							$user_contact = function_exists( 'imob_get_user_contact_data' ) ? imob_get_user_contact_data( get_the_author_meta( 'ID' ) ) : false;
							if ( $user_contact ) :
							?>
								<h3 class="widget-title">Corretor responsável:</h3>
								<div class="imob-corretor-card" style="margin-bottom: 20px;">
									<div class="imob-corretor-info" style="display: flex; justify-content: space-between; align-items: flex-start;">
										<div class="imob-corretor-details" style="flex: 1;">
											<h4 style="font-size: 1.2rem;"><?php echo esc_html( $user_contact['name'] ); ?></h4>
											<p class="role" style="color: var(--text-light);"><?php echo esc_html( $user_contact['role'] ); ?></p>
											<?php if ( ! empty( $user_contact['creci'] ) || ! empty( $user_contact['cnai'] ) ) : ?>
												<p class="creci" style="color: #999;">
													<?php if ( ! empty( $user_contact['creci'] ) ) : ?>CRECI <?php echo esc_html( $user_contact['creci'] ); ?><?php endif; ?>
													<?php if ( ! empty( $user_contact['cnai'] ) ) : ?> CNAI <?php echo esc_html( $user_contact['cnai'] ); ?><?php endif; ?>
												</p>
											<?php endif; ?>
										</div>
										<div class="imob-corretor-avatar" style="flex-shrink: 0; margin-left: 15px;">
											<div class="avatar-placeholder" style="width: 70px; height: 70px; border-radius: 10px; background: url('<?php echo esc_url($user_contact['foto_url']); ?>') center/cover;"></div>
										</div>
									</div>
									<?php if ( ! empty( $user_contact['resumo'] ) ) : ?>
										<div class="imob-corretor-resumo" style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 10px;">
											<?php echo wp_kses_post( $user_contact['resumo'] ); ?>
										</div>
									<?php endif; ?>
									<div class="imob-corretor-contact" style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px;">
										<p style="margin: 0; font-size: 0.9rem; font-weight: 600; line-height: 1.3; max-width: 150px;"><strong>Entre em contato</strong> e realize o sonho do imóvel próprio:</p>
										<div class="imob-social-icons">
											<?php if ( ! empty( $user_contact['whatsapp'] ) ) : 
												$u_wa = preg_replace('/[^0-9]/', '', $user_contact['whatsapp']);
												if ( substr($u_wa, 0, 2) !== '55' && strlen($u_wa) <= 11 ) {
													$u_wa = '55' . $u_wa;
												}
												$u_wa_text = urlencode('Olá, gostaria de mais informações sobre o imóvel: ' . get_the_title() . ' - ' . get_permalink());
											?>
												<a href="https://wa.me/<?php echo esc_attr($u_wa); ?>?text=<?php echo $u_wa_text; ?>" target="_blank" class="social-icon" title="WhatsApp"><span class="material-symbols-outlined">chat</span></a>
											<?php endif; ?>
											<?php if ( ! empty( $user_contact['instagram'] ) ) : ?>
												<a href="https://instagram.com/<?php echo esc_attr(str_replace('@', '', $user_contact['instagram'])); ?>" target="_blank" class="social-icon" title="Instagram"><span class="material-symbols-outlined">photo_camera</span></a>
											<?php endif; ?>
											<?php if ( ! empty( $user_contact['email'] ) ) : ?>
												<a href="mailto:<?php echo esc_attr($user_contact['email']); ?>?subject=<?php echo urlencode('Interesse no imóvel: ' . get_the_title()); ?>" class="social-icon" title="E-mail"><span class="material-symbols-outlined">mail</span></a>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php else : ?>
								<h3 class="widget-title">Contato:</h3>
								<div class="imob-corretor-card">
									<p>Fale conosco para mais detalhes sobre este imóvel.</p>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<!-- Widget de Busca Simplificado -->
					<div class="imob-sidebar-widget imob-sidebar-search">
						<h3 class="widget-title" style="margin-bottom: 15px;">Buscar imóveis</h3>
						<form role="search" method="get" class="imob-sb-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-top: 0;">
							<input type="hidden" name="post_type" value="imovel" />
							
							<div class="imob-search-tabs">
								<label class="imob-search-tab">
									<input type="radio" name="finalidade" value="aluguel" <?php checked( isset($_GET['finalidade']) && $_GET['finalidade'] === 'aluguel' ); ?>>
									<span>Aluguel</span>
								</label>
								<label class="imob-search-tab">
									<input type="radio" name="finalidade" value="venda" <?php checked( !isset($_GET['finalidade']) || $_GET['finalidade'] !== 'aluguel' ); ?>>
									<span>Venda</span>
								</label>
							</div>

							<div class="imob-sb-field">
								<span class="material-symbols-outlined search-icon">search</span>
								<input type="text" name="s" placeholder="O que você procura?" value="<?php echo esc_attr( get_search_query() ); ?>">
							</div>

							<div class="imob-sb-field">
								<span class="material-symbols-outlined" style="color: var(--text-light);">location_on</span>
								<select name="localidade" class="imob-select" style="cursor: pointer;">
									<option value="">Todas as Localidades</option>
									<?php
									$loc_terms = get_terms( array(
										'taxonomy'   => 'localidade',
										'hide_empty' => false,
									) );
									if ( ! empty( $loc_terms ) && ! is_wp_error( $loc_terms ) ) {
										$selected_loc = isset( $_GET['localidade'] ) ? sanitize_text_field( $_GET['localidade'] ) : '';
										foreach ( $loc_terms as $loc_term ) {
											echo '<option value="' . esc_attr( $loc_term->slug ) . '" ' . selected( $selected_loc, $loc_term->slug, false ) . '>' . esc_html( $loc_term->name ) . '</option>';
										}
									}
									?>
								</select>
								<span class="material-symbols-outlined" style="color: var(--text-light); margin-left: auto; margin-right: 0; pointer-events: none;">keyboard_arrow_down</span>
							</div>

							<div class="imob-sb-field">
								<span class="material-symbols-outlined" style="color: var(--text-light);">home_work</span>
								<select name="tipo" class="imob-select" style="cursor: pointer;">
									<option value="">Todos os Tipos de Imóvel</option>
									<?php
									$tipo_terms = get_terms( array(
										'taxonomy'   => 'tipo_imovel',
										'hide_empty' => false,
									) );
									if ( ! empty( $tipo_terms ) && ! is_wp_error( $tipo_terms ) ) {
										$selected_tipo = isset( $_GET['tipo'] ) ? sanitize_text_field( $_GET['tipo'] ) : '';
										foreach ( $tipo_terms as $tipo_term ) {
											echo '<option value="' . esc_attr( $tipo_term->slug ) . '" ' . selected( $selected_tipo, $tipo_term->slug, false ) . '>' . esc_html( $tipo_term->name ) . '</option>';
										}
									}
									?>
								</select>
								<span class="material-symbols-outlined" style="color: var(--text-light); margin-left: auto; margin-right: 0; pointer-events: none;">keyboard_arrow_down</span>
							</div>

							<button type="submit" class="imob-btn-sidebar-filter" style="width: 100%; justify-content: center;">
								<span class="material-symbols-outlined">search</span>
								<?php _e( 'MOSTRAR RESULTADOS', 'imobiliaria-tema' ); ?>
							</button>
						</form>
					</div>

					<!-- Imóveis Semelhantes -->
					<div class="imob-sidebar-widget">
						<h3 class="widget-title">Imóveis semelhantes</h3>
						<div class="imob-related-properties">
							<?php
							// Simple query for related properties based on type
							$args = array(
								'post_type' => 'imovel',
								'posts_per_page' => 2,
								'post__not_in' => array( get_the_ID() ),
							);
							if ( !empty($tipos) ) {
								$args['tax_query'] = array(
									array(
										'taxonomy' => 'tipo_imovel',
										'field'    => 'name',
										'terms'    => $tipos[0],
									),
								);
							}
							$related = new WP_Query( $args );
							if ( $related->have_posts() ) {
								while ( $related->have_posts() ) {
									$related->the_post();
									$rel_tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel' );
									$rel_status = wp_get_post_terms( get_the_ID(), 'status_imovel' );
									$rel_locs = wp_get_post_terms( get_the_ID(), 'localidade' );
									$rel_tipo = ( ! empty( $rel_tipos ) && ! is_wp_error( $rel_tipos ) ) ? $rel_tipos[0]->name : 'IMÓVEL';
									$rel_bairro = ( ! empty( $rel_locs ) && ! is_wp_error( $rel_locs ) ) ? $rel_locs[0]->name : 'Bairro não informado';
									$rel_status_name = ( ! empty( $rel_status ) && ! is_wp_error( $rel_status ) ) ? $rel_status[0]->name : '';
									?>
									<article class="imob-card imob-related-card">
										<div class="imob-card-thumb" style="height: 150px;">
											<div class="imob-card-badges">
												<span class="badge-tipo"><?php echo esc_html( imob_strtoupper( $rel_tipo ) ); ?></span>
												<?php if ( $rel_status_name ) : ?>
													<span class="badge-status"><?php echo esc_html( imob_strtoupper( $rel_status_name ) ); ?></span>
												<?php endif; ?>
												<?php 
												$rel_emp_badge = imob_render_empreendimento_badge( get_the_ID() );
												if ( ! empty( $rel_emp_badge ) ) {
													echo $rel_emp_badge;
												}
												?>
											</div>
											
											<?php $price_html = imob_get_formatted_price( get_the_ID(), false ); ?>
											<?php if ( $price_html ) : ?>
												<div class="imob-card-price">
													<?php echo $price_html; ?>
												</div>
											<?php endif; ?>
											
											<a href="<?php echo esc_url( get_permalink() ); ?>">
												<?php
												if ( has_post_thumbnail() ) {
													the_post_thumbnail( 'medium' );
												} else {
													echo '<div class="imob-card-placeholder"></div>';
												}
												?>
											</a>
										</div>
										<div class="imob-card-content" style="padding: 15px;">
											<div class="imob-card-info-top" style="position: static; background: transparent; padding: 0 0 10px 0;">
												<span class="info-bairro"><span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; color: var(--accent-color);">location_on</span> Bairro: <?php echo esc_html( $rel_bairro ); ?></span>
											</div>
											
											<?php the_title( '<h3 class="imob-card-title" style="font-size: 1rem; margin-bottom: 10px;"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' ); ?>
											
											<?php 
											$rel_empreendimento = get_post_meta( get_the_ID(), '_imob_empreendimento', true );
											if ( $rel_empreendimento ) : ?>
												<p style="font-size: 0.8rem; margin: 0 0 10px 0;"><strong><?php echo esc_html($rel_empreendimento); ?></strong></p>
											<?php endif; ?>

											<div class="imob-card-features" style="margin-bottom: 15px; font-size: 0.8rem;">
												<?php 
												$p_quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
												$p_baths = get_post_meta( get_the_ID(), '_imob_banheiros', true );
												$p_area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
												if ( $p_quartos ) echo '<span title="Quartos"><span class="material-symbols-outlined" style="font-size:14px; color: var(--text-light);">bed</span> ' . $p_quartos . '</span>';
												if ( $p_baths ) echo '<span title="Banheiros"><span class="material-symbols-outlined" style="font-size:14px; color: var(--text-light);">shower</span> ' . $p_baths . '</span>';
												if ( $p_area ) echo '<span title="Área Privativa"><span class="material-symbols-outlined" style="font-size:14px; color: var(--text-light);">crop</span> ' . $p_area . 'm²</span>';
												?>
											</div>
											<div class="imob-card-footer" style="font-size: 0.7rem; border-top: 1px solid #eee; padding-top: 10px; color: var(--text-light);">
												<span class="imob-card-date">Data do anúncio: <?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
											</div>
										</div>
									</article>
									<?php
								}
								wp_reset_postdata();
							}
							?>
						</div>
					</div>

				</aside><!-- .imob-single-sidebar -->
			</div><!-- .imob-single-container -->

		</article>

	<?php
	endwhile; // End of the loop.
	?>
</main>

<?php
get_footer();
