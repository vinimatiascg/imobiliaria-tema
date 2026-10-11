<?php
/**
 * Single Template: Empreendimento
 *
 * @package ImobiliariaTema
 */

get_header(); 
$gmaps_key = get_option( 'imob_gmaps_key' );
?>

<main id="primary" class="site-main imob-single-empreendimento">
	<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
		
		<?php while ( have_posts() ) : the_post(); 
			$emp_id         = get_the_ID();
			$estagio        = get_post_meta( $emp_id, '_imob_emp_estagio', true );
			$previsao       = get_post_meta( $emp_id, '_imob_emp_previsao', true );
			$construtora_id = get_post_meta( $emp_id, '_imob_emp_construtora_id', true );
			$endereco       = get_post_meta( $emp_id, '_imob_emp_endereco', true );
			$lat            = get_post_meta( $emp_id, '_imob_emp_lat', true );
			$lng            = get_post_meta( $emp_id, '_imob_emp_lng', true );
			$galeria        = get_post_meta( $emp_id, '_imob_emp_galeria', true );
			$galeria        = is_array( $galeria ) ? $galeria : ( ! empty( $galeria ) ? explode( ',', $galeria ) : array() );

			$construtora_post = ( ! empty( $construtora_id ) ) ? get_post( (int) $construtora_id ) : null;
			$estagio_labels = array(
				'Lancamento'   => __( 'Lançamento', 'imobiliaria-tema' ),
				'Em Construcao' => __( 'Em Construção', 'imobiliaria-tema' ),
				'Pronto'       => __( 'Pronto para Morar', 'imobiliaria-tema' ),
			);
			$estagio_label = isset( $estagio_labels[ $estagio ] ) ? $estagio_labels[ $estagio ] : ( ! empty( $estagio ) ? $estagio : '' );
		?>

			<!-- CABEÇALHO DO EMPREENDIMENTO -->
			<header class="imob-emp-header" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
				<div style="display: flex; gap: 30px; align-items: center; flex-wrap: wrap;">
					
					<!-- LOGO OU FOTO PRINCIPAL -->
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="imob-emp-logo-box" style="flex: 0 0 160px; max-width: 180px; text-align: center;">
							<?php the_post_thumbnail( 'medium', array( 'style' => 'max-height: 140px; width: auto; object-fit: contain; border-radius: 8px;' ) ); ?>
						</div>
					<?php endif; ?>

					<div style="flex: 1; min-width: 260px;">
						<!-- BADGES DO EMPREENDIMENTO -->
						<div class="imob-emp-badges-header" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; align-items: center;">
							<span style="background: rgba(178, 145, 90, 0.15); color: var(--accent-color); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">
								<span class="material-symbols-outlined" style="font-size: 14px;">domain</span>
								<?php _e( 'Empreendimento', 'imobiliaria-tema' ); ?>
							</span>

							<?php 
							// Badges de Estágio da Obra e Tipo(s) de Imóvel com links para suas listagens
							echo imob_render_empreendimento_badges( $emp_id );
							?>

							<?php if ( $previsao ) : ?>
								<span style="background: #f1f5f9; color: var(--text-dark); border: 1px solid var(--border-color); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
									<span class="material-symbols-outlined" style="font-size: 14px; color: var(--accent-color);">event</span>
									Entrega: <?php echo esc_html( $previsao ); ?>
								</span>
							<?php endif; ?>
						</div>

						<!-- TÍTULO -->
						<h1 class="imob-emp-title" style="font-size: 2.4rem; color: var(--primary-color); margin: 0 0 10px; font-weight: 800; line-height: 1.2;">
							<?php the_title(); ?>
						</h1>

						<!-- CONSTRUTORA & ENDEREÇO -->
						<div style="display: flex; flex-wrap: wrap; gap: 20px; color: var(--text-light); font-size: 0.95rem;">
							<?php if ( $construtora_post ) : ?>
								<div style="display: inline-flex; align-items: center; gap: 6px;">
									<span class="material-symbols-outlined" style="font-size: 18px; color: var(--accent-color);">apartment</span>
									Construtora: 
									<a href="<?php echo esc_url( get_permalink( $construtora_post->ID ) ); ?>" style="color: var(--primary-color); font-weight: 700; text-decoration: none;">
										<?php echo esc_html( $construtora_post->post_title ); ?> &rarr;
									</a>
								</div>
							<?php endif; ?>

							<?php if ( $endereco ) : ?>
								<div style="display: inline-flex; align-items: center; gap: 6px;">
									<span class="material-symbols-outlined" style="font-size: 18px; color: var(--accent-color);">location_on</span>
									<?php echo esc_html( $endereco ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>

				</div>

				<!-- CONTEÚDO / DESCRIÇÃO -->
				<?php if ( get_the_content() ) : ?>
					<div class="imob-emp-content" style="margin-top: 25px; padding-top: 25px; border-top: 1px solid var(--border-color); color: var(--text-dark); line-height: 1.8; font-size: 1.05rem;">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>
			</header>

			<!-- ITENS DE LAZER / CARACTERÍSTICAS DO EMPREENDIMENTO -->
			<?php 
			$emp_caracteristicas = wp_get_post_terms( $emp_id, 'caracteristica' );
			if ( ! empty( $emp_caracteristicas ) && ! is_wp_error( $emp_caracteristicas ) ) : ?>
				<section class="imob-emp-features-section" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
					<h2 style="font-size: 1.6rem; color: var(--primary-color); margin: 0 0 20px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
						<span class="material-symbols-outlined" style="color: var(--accent-color);">deck</span>
						<?php _e( 'Itens de Lazer e Diferenciais', 'imobiliaria-tema' ); ?>
					</h2>
					<div class="imob-emp-features-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px;">
						<?php foreach ( $emp_caracteristicas as $carac ) : ?>
							<div class="imob-emp-feature-item" style="display: flex; align-items: center; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 18px; border-radius: 8px; transition: transform 0.2s, box-shadow 0.2s;">
								<span class="material-symbols-outlined" style="color: var(--accent-color); font-size: 22px;">check_circle</span>
								<span style="font-size: 0.95rem; font-weight: 600; color: var(--text-dark);"><?php echo esc_html( $carac->name ); ?></span>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- FOTOS DO EMPREENDIMENTO (GALERIA INTERATIVA COM NAVEGAÇÃO) -->
			<?php if ( ! empty( $galeria ) ) : 
				$total_fotos = count( $galeria );
			?>
				<section class="imob-emp-gallery-section" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
						<h2 style="font-size: 1.6rem; color: var(--primary-color); margin: 0; font-weight: 800; display: flex; align-items: center; gap: 8px;">
							<span class="material-symbols-outlined" style="color: var(--accent-color);">photo_library</span>
							<?php _e( 'Fotos do Empreendimento', 'imobiliaria-tema' ); ?>
						</h2>
						<span class="imob-emp-gallery-counter" style="background: #f1f5f9; color: var(--primary-color); font-weight: 700; font-size: 0.88rem; padding: 6px 14px; border-radius: 20px; border: 1px solid #e2e8f0;">
							<span id="emp-gallery-current">1</span> / <span id="emp-gallery-total"><?php echo esc_html( $total_fotos ); ?></span> <?php _e( 'fotos', 'imobiliaria-tema' ); ?>
						</span>
					</div>

					<!-- Visualizador Principal / Slider com Botões de Navegação -->
					<div class="imob-emp-slider-wrapper" style="position: relative; width: 100%; border-radius: 12px; overflow: hidden; background: #0f172a; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
						
						<!-- Container dos Slides -->
						<div class="imob-emp-slides" id="emp-slides-container" style="position: relative; width: 100%; height: 480px;">
							<?php foreach ( $galeria as $idx => $img_id ) : 
								$img_full  = wp_get_attachment_image_url( $img_id, 'full' );
								$img_large = wp_get_attachment_image_url( $img_id, 'large' );
								if ( ! $img_large ) continue;
							?>
								<div class="imob-emp-slide <?php echo ( $idx === 0 ) ? 'active' : ''; ?>" data-slide-index="<?php echo esc_attr( $idx ); ?>" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: <?php echo ( $idx === 0 ) ? '1' : '0'; ?>; visibility: <?php echo ( $idx === 0 ) ? 'visible' : 'hidden'; ?>; transition: opacity 0.35s ease, visibility 0.35s ease; z-index: <?php echo ( $idx === 0 ) ? '2' : '1'; ?>;">
									<a href="<?php echo esc_url( $img_full ); ?>" class="glightbox" data-gallery="emp-gallery" data-title="<?php echo esc_attr( get_the_title() . ' - Foto ' . ( $idx + 1 ) ); ?>" style="display: block; width: 100%; height: 100%; cursor: zoom-in;">
										<img src="<?php echo esc_url( $img_large ); ?>" alt="<?php the_title_attribute(); ?> - Foto <?php echo esc_attr( $idx + 1 ); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
									</a>
								</div>
							<?php endforeach; ?>
						</div>

						<!-- Botão Navegação Anterior ( < ) -->
						<button type="button" id="emp-gallery-btn-prev" class="emp-nav-btn emp-nav-prev" aria-label="<?php _e( 'Foto Anterior', 'imobiliaria-tema' ); ?>" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 255, 255, 0.9); border: none; box-shadow: 0 4px 14px rgba(0,0,0,0.25); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s ease; color: var(--primary-color);">
							<span class="material-symbols-outlined" style="font-size: 28px; line-height: 1;">chevron_left</span>
						</button>

						<!-- Botão Navegação Próximo ( > ) -->
						<button type="button" id="emp-gallery-btn-next" class="emp-nav-btn emp-nav-next" aria-label="<?php _e( 'Próxima Foto', 'imobiliaria-tema' ); ?>" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 255, 255, 0.9); border: none; box-shadow: 0 4px 14px rgba(0,0,0,0.25); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s ease; color: var(--primary-color);">
							<span class="material-symbols-outlined" style="font-size: 28px; line-height: 1;">chevron_right</span>
						</button>

						<!-- Botão Ampliar / Tela Cheia -->
						<button type="button" id="emp-gallery-btn-fullscreen" class="emp-nav-zoom" aria-label="<?php _e( 'Ampliar Foto', 'imobiliaria-tema' ); ?>" style="position: absolute; right: 16px; bottom: 16px; background: rgba(15, 23, 42, 0.75); color: #ffffff; border: none; border-radius: 20px; padding: 6px 14px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; z-index: 10; backdrop-filter: blur(4px); transition: background 0.2s ease;">
							<span class="material-symbols-outlined" style="font-size: 18px;">zoom_in</span>
							<?php _e( 'Ampliar', 'imobiliaria-tema' ); ?>
						</button>
					</div>

					<!-- Trilha de Miniaturas (Thumbnails) -->
					<?php if ( $total_fotos > 1 ) : ?>
						<div class="imob-emp-thumbs-track" id="emp-thumbs-track" style="display: flex; gap: 10px; overflow-x: auto; padding: 15px 2px 5px; scrollbar-width: thin;">
							<?php foreach ( $galeria as $idx => $img_id ) : 
								$img_thumb = wp_get_attachment_image_url( $img_id, 'medium' );
								if ( ! $img_thumb ) continue;
							?>
								<button type="button" class="emp-thumb-btn <?php echo ( $idx === 0 ) ? 'active' : ''; ?>" data-thumb-index="<?php echo esc_attr( $idx ); ?>" style="flex: 0 0 100px; height: 75px; border-radius: 8px; overflow: hidden; padding: 0; cursor: pointer; border: 2px solid <?php echo ( $idx === 0 ) ? 'var(--accent-color)' : 'transparent'; ?>; opacity: <?php echo ( $idx === 0 ) ? '1' : '0.65'; ?>; transition: all 0.2s ease; background: #e2e8f0;">
									<img src="<?php echo esc_url( $img_thumb ); ?>" alt="Thumb <?php echo esc_attr( $idx + 1 ); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none;">
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Script de Controle do Slider e Lightbox -->
					<script>
					document.addEventListener('DOMContentLoaded', function() {
						var slides = document.querySelectorAll('.imob-emp-slide');
						var thumbs = document.querySelectorAll('.emp-thumb-btn');
						var btnPrev = document.getElementById('emp-gallery-btn-prev');
						var btnNext = document.getElementById('emp-gallery-btn-next');
						var btnZoom = document.getElementById('emp-gallery-btn-fullscreen');
						var counterEl = document.getElementById('emp-gallery-current');
						var thumbsTrack = document.getElementById('emp-thumbs-track');
						var total = slides.length;
						var currentIndex = 0;

						if (total <= 1) {
							if (btnPrev) btnPrev.style.display = 'none';
							if (btnNext) btnNext.style.display = 'none';
						}

						function goToSlide(index) {
							if (index < 0) {
								index = total - 1;
							} else if (index >= total) {
								index = 0;
							}
							currentIndex = index;

							slides.forEach(function(slide, idx) {
								if (idx === currentIndex) {
									slide.style.opacity = '1';
									slide.style.visibility = 'visible';
									slide.style.zIndex = '2';
									slide.classList.add('active');
								} else {
									slide.style.opacity = '0';
									slide.style.visibility = 'hidden';
									slide.style.zIndex = '1';
									slide.classList.remove('active');
								}
							});

							thumbs.forEach(function(thumb, idx) {
								if (idx === currentIndex) {
									thumb.classList.add('active');
									thumb.style.borderColor = 'var(--accent-color)';
									thumb.style.opacity = '1';
									if (thumbsTrack) {
										var left = thumb.offsetLeft - thumbsTrack.offsetLeft - 40;
										thumbsTrack.scrollTo({ left: left, behavior: 'smooth' });
									}
								} else {
									thumb.classList.remove('active');
									thumb.style.borderColor = 'transparent';
									thumb.style.opacity = '0.65';
								}
							});

							if (counterEl) {
								counterEl.textContent = currentIndex + 1;
							}
						}

						if (btnPrev) {
							btnPrev.addEventListener('click', function(e) {
								e.preventDefault();
								goToSlide(currentIndex - 1);
							});
						}

						if (btnNext) {
							btnNext.addEventListener('click', function(e) {
								e.preventDefault();
								goToSlide(currentIndex + 1);
							});
						}

						if (btnZoom) {
							btnZoom.addEventListener('click', function(e) {
								e.preventDefault();
								var activeLink = slides[currentIndex] ? slides[currentIndex].querySelector('a.glightbox') : null;
								if (activeLink) {
									activeLink.click();
								}
							});
						}

						thumbs.forEach(function(thumb) {
							thumb.addEventListener('click', function(e) {
								e.preventDefault();
								var idx = parseInt(this.getAttribute('data-thumb-index'), 10);
								goToSlide(idx);
							});
						});

						// Navegação por teclado
						document.addEventListener('keydown', function(e) {
							if (document.body.classList.contains('glightbox-open')) return;
							if (e.key === 'ArrowLeft') {
								goToSlide(currentIndex - 1);
							} else if (e.key === 'ArrowRight') {
								goToSlide(currentIndex + 1);
							}
						});

						// Suporte a swipe em touch
						var startX = 0;
						var slidesContainer = document.getElementById('emp-slides-container');
						if (slidesContainer) {
							slidesContainer.addEventListener('touchstart', function(e) {
								startX = e.touches[0].clientX;
							}, { passive: true });

							slidesContainer.addEventListener('touchend', function(e) {
								var endX = e.changedTouches[0].clientX;
								var diff = startX - endX;
								if (Math.abs(diff) > 40) {
									if (diff > 0) {
										goToSlide(currentIndex + 1);
									} else {
										goToSlide(currentIndex - 1);
									}
								}
							}, { passive: true });
						}

						// Inicializar GLightbox
						if (typeof GLightbox !== 'undefined') {
							GLightbox({
								selector: '.glightbox',
								loop: true,
								zoomable: true,
								touchNavigation: true
							});
						}
					});
					</script>
				</section>
			<?php endif; ?>

			<!-- LOCALIZAÇÃO EM MAPA -->
			<?php if ( ( ! empty( $lat ) && ! empty( $lng ) ) || ! empty( $endereco ) ) : ?>
				<section class="imob-emp-map-section" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 35px; margin-bottom: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
					<h2 style="font-size: 1.6rem; color: var(--primary-color); margin: 0 0 10px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
						<span class="material-symbols-outlined" style="color: var(--accent-color);">map</span>
						<?php _e( 'Localização do Empreendimento', 'imobiliaria-tema' ); ?>
					</h2>
					<?php if ( $endereco ) : ?>
						<p style="color: var(--text-light); margin: 0 0 20px; font-size: 0.95rem;">
							<span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle; color: var(--accent-color);">pin_drop</span>
							<?php echo esc_html( $endereco ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $gmaps_key && ! empty( $lat ) && ! empty( $lng ) ) : ?>
						<div id="imob-emp-single-map" style="width: 100%; height: 420px; border-radius: 10px; border: 1px solid #e2e8f0;"></div>
						<script>
							function initEmpMap() {
								var loc = { lat: <?php echo floatval( $lat ); ?>, lng: <?php echo floatval( $lng ); ?> };
								var map = new google.maps.Map(document.getElementById('imob-emp-single-map'), {
									zoom: 16,
									center: loc,
									mapTypeId: 'roadmap'
								});
								var marker = new google.maps.Marker({
									position: loc,
									map: map,
									title: '<?php echo esc_js( get_the_title() ); ?>'
								});
							}
							window.addEventListener('load', initEmpMap);
						</script>
					<?php else : 
						// Fallback dinâmico com OpenStreetMap ou Google Maps Search Embed para nunca quebrar
						$map_query = ( ! empty( $lat ) && ! empty( $lng ) ) ? $lat . ',' . $lng : urlencode( $endereco );
					?>
						<div style="border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; height: 400px; background: #e2e8f0;">
							<iframe 
								width="100%" 
								height="100%" 
								frameborder="0" 
								scrolling="no" 
								marginheight="0" 
								marginwidth="0" 
								src="https://maps.google.com/maps?q=<?php echo esc_attr( $map_query ); ?>&t=&z=15&ie=UTF8&iwloc=&output=embed"
								style="border:0;"
								loading="lazy">
							</iframe>
						</div>
					<?php endif; ?>
				</section>
			<?php endif; ?>

			<!-- LISTA DE IMÓVEIS DESTE EMPREENDIMENTO -->
			<section class="imob-emp-imoveis-section" style="margin-bottom: 50px;">
				<div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
					<h2 style="font-size: 1.8rem; color: var(--primary-color); margin: 0; font-weight: 800;">
						<?php _e( 'Imóveis neste Empreendimento', 'imobiliaria-tema' ); ?>
					</h2>
					<span style="color: var(--text-light); font-size: 0.95rem;">
						<?php _e( 'Unidades disponíveis para compra ou locação', 'imobiliaria-tema' ); ?>
					</span>
				</div>

				<?php
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				
				// Busca por ID de empreendimento OU por texto no nome
				$args = array(
					'post_type'      => 'imovel',
					'posts_per_page' => 12,
					'paged'          => $paged,
					'meta_query'     => array(
						'relation' => 'OR',
						array(
							'key'     => '_imob_empreendimento_id',
							'value'   => $emp_id,
							'compare' => '=',
						),
						array(
							'key'     => '_imob_empreendimento',
							'value'   => get_the_title(),
							'compare' => '=',
						),
					),
				);
				$imoveis = new WP_Query( $args );

				if ( $imoveis->have_posts() ) : ?>
					<div class="imob-imoveis-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
						<?php
						while ( $imoveis->have_posts() ) : $imoveis->the_post();
							$price_html = imob_get_formatted_price( get_the_ID(), false );
							$quartos    = get_post_meta( get_the_ID(), '_imob_quartos', true );
							$banheiros  = get_post_meta( get_the_ID(), '_imob_banheiros', true );
							$area       = get_post_meta( get_the_ID(), '_imob_area_privativa', true );

							$tipos       = wp_get_post_terms( get_the_ID(), 'tipo_imovel', array( 'fields' => 'names' ) );
							$status_list = wp_get_post_terms( get_the_ID(), 'status_imovel', array( 'fields' => 'names' ) );
							$localidades = wp_get_post_terms( get_the_ID(), 'localidade', array( 'fields' => 'names' ) );

							$tipo       = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : 'IMÓVEL';
							$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : 'VENDA';
							$bairro     = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : '';
							$badge_emp_html = imob_render_empreendimento_badge( get_the_ID() );
							?>
							<article class="imob-card">
								<div class="imob-card-thumb">
									<div class="imob-card-badges">
										<span class="badge-tipo"><?php echo esc_html( imob_strtoupper( $tipo ) ); ?></span>
										<span class="badge-finalidade"><?php echo esc_html( imob_strtoupper( $finalidade ) ); ?></span>
										<?php if ( ! empty( $badge_emp_html ) ) : ?>
											<?php echo $badge_emp_html; ?>
										<?php endif; ?>
									</div>
									<?php if ( $price_html ) : ?>
										<div class="imob-card-price">
											<?php echo $price_html; ?>
										</div>
									<?php endif; ?>
									<a href="<?php the_permalink(); ?>">
										<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail( 'medium_large' );
										} else {
											echo '<div class="imob-card-placeholder"></div>';
										}
										?>
									</a>
								</div>
								
								<div class="imob-card-content">
									<?php if ( $bairro ) : ?>
										<div class="imob-card-info-top">
											<span class="info-bairro"><span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; color: var(--accent-color);">location_on</span> Bairro: <?php echo esc_html( $bairro ); ?></span>
										</div>
									<?php endif; ?>
									
									<?php the_title( '<h3 class="imob-card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>

									<div class="imob-card-features">
										<?php if ( $quartos ) : ?>
											<span title="Quartos"><span class="material-symbols-outlined">bed</span> <?php echo esc_html( $quartos ); ?></span>
										<?php endif; ?>
										<?php if ( $banheiros ) : ?>
											<span title="Banheiros"><span class="material-symbols-outlined">shower</span> <?php echo esc_html( $banheiros ); ?></span>
										<?php endif; ?>
										<?php if ( $area ) : ?>
											<span title="Área Privativa"><span class="material-symbols-outlined">crop</span> <?php echo esc_html( $area ); ?>m²</span>
										<?php endif; ?>
									</div>
									
									<div class="imob-card-footer">
										<span class="imob-card-date">Data: <?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>

					<!-- PAGINAÇÃO -->
					<div class="imob-pagination" style="margin-top: 40px; display: flex; justify-content: center; gap: 8px;">
						<?php
						$big = 999999999;
						echo paginate_links( array(
							'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format'    => '?paged=%#%',
							'current'   => max( 1, $paged ),
							'total'     => $imoveis->max_num_pages,
							'prev_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_left</span>',
							'next_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_right</span>',
							'type'      => 'list',
						) );
						?>
					</div>
					<?php wp_reset_postdata(); ?>

				<?php else : ?>
					<div style="background: #ffffff; padding: 40px 20px; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
						<span class="material-symbols-outlined" style="font-size: 40px; color: var(--text-light); margin-bottom: 10px;">info</span>
						<h3 style="margin: 0 0 8px; color: var(--primary-color); font-size: 1.2rem;"><?php _e( 'Nenhum imóvel disponível no momento', 'imobiliaria-tema' ); ?></h3>
						<p style="color: var(--text-light); margin: 0;"><?php _e( 'As unidades deste empreendimento estão sob consulta ou foram comercializadas.', 'imobiliaria-tema' ); ?></p>
					</div>
				<?php endif; ?>
			</section>

		<?php endwhile; ?>

	</div>
</main>

<?php 
get_footer();
