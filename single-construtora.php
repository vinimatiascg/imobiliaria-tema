<?php
/**
 * Single Template: Construtora
 */

get_header(); 
?>

<main id="primary" class="site-main imob-archive">
	<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
		
		<?php while ( have_posts() ) : the_post(); 
			$telefone = get_post_meta( get_the_ID(), '_imob_construtora_telefone', true );
			$whatsapp = get_post_meta( get_the_ID(), '_imob_construtora_whatsapp', true );
			$site = get_post_meta( get_the_ID(), '_imob_construtora_site', true );
			$instagram = get_post_meta( get_the_ID(), '_imob_construtora_instagram', true );
		?>
			<header class="imob-construtora-header">
				<div class="imob-construtora-header-inner">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="imob-construtora-logo">
							<?php the_post_thumbnail( 'large', ['style' => 'max-height: 160px; max-width: 100%; width: auto; object-fit: contain; display: block;'] ); ?>
						</div>
					<?php endif; ?>

					<div class="imob-construtora-info">
						<div class="imob-construtora-tag">
							<span class="material-symbols-outlined" style="font-size: 15px;">apartment</span>
							<?php _e( 'Construtora', 'imobiliaria-tema' ); ?>
						</div>
						
						<h1 class="imob-construtora-title"><?php the_title(); ?></h1>
						
						<?php if ( get_the_content() ) : ?>
							<div class="imob-construtora-content">
								<?php the_content(); ?>
							</div>
						<?php endif; ?>

						<div class="imob-construtora-contato">
							<?php if ( $telefone ) : ?>
								<a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', $telefone)); ?>" class="btn-contato"><span class="material-symbols-outlined">call</span> <?php echo esc_html($telefone); ?></a>
							<?php endif; ?>
							<?php if ( $whatsapp ) : ?>
								<a href="https://wa.me/55<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" class="btn-contato btn-wa"><span class="material-symbols-outlined">chat</span> <?php echo esc_html($whatsapp); ?></a>
							<?php endif; ?>
							<?php if ( $site ) : ?>
								<a href="<?php echo esc_url($site); ?>" target="_blank" class="btn-contato"><span class="material-symbols-outlined">language</span> Site Oficial</a>
							<?php endif; ?>
							<?php if ( $instagram ) : ?>
								<a href="https://instagram.com/<?php echo esc_attr(str_replace('@', '', $instagram)); ?>" target="_blank" class="btn-contato"><span class="material-symbols-outlined">photo_camera</span> <?php echo esc_html($instagram); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</header>

			<style>
				.imob-construtora-header {
					margin-bottom: 40px;
					background: #ffffff;
					border: 1px solid var(--border-color);
					border-radius: 12px;
					padding: 40px;
					box-shadow: 0 4px 20px rgba(0,0,0,0.04);
				}
				.imob-construtora-header-inner {
					display: flex;
					gap: 40px;
					align-items: center;
					text-align: left;
				}
				.imob-construtora-logo {
					flex: 0 0 220px;
					max-width: 250px;
					display: flex;
					align-items: center;
					justify-content: center;
					background: #f8fafc;
					border: 1px solid #edf2f7;
					border-radius: 10px;
					padding: 20px;
				}
				.imob-construtora-info {
					flex: 1;
					min-width: 280px;
				}
				.imob-construtora-tag {
					display: inline-flex;
					align-items: center;
					gap: 5px;
					background: rgba(178, 145, 90, 0.12);
					color: var(--accent-color);
					padding: 4px 12px;
					border-radius: 20px;
					font-size: 0.75rem;
					font-weight: 700;
					text-transform: uppercase;
					letter-spacing: 0.5px;
					margin-bottom: 10px;
				}
				.imob-construtora-title {
					font-size: 2.5rem;
					color: var(--primary-color);
					margin: 0 0 15px;
					font-weight: 800;
					line-height: 1.2;
				}
				.imob-construtora-content {
					color: var(--text-light);
					margin-bottom: 25px;
					line-height: 1.7;
					font-size: 1.05rem;
				}
				.imob-construtora-contato {
					display: flex;
					gap: 12px;
					justify-content: flex-start;
					flex-wrap: wrap;
				}
				.imob-construtora-contato .btn-contato {
					background: #f1f5f9;
					border: 1px solid var(--border-color);
					padding: 8px 16px;
					border-radius: 6px;
					color: var(--text-dark);
					text-decoration: none;
					display: inline-flex;
					align-items: center;
					gap: 6px;
					font-size: 0.9rem;
					font-weight: 600;
					transition: all 0.2s ease;
				}
				.imob-construtora-contato .btn-contato:hover {
					border-color: var(--accent-color);
					color: var(--accent-color);
					transform: translateY(-2px);
				}
				.imob-construtora-contato .btn-wa {
					background: #25D366 !important;
					color: #ffffff !important;
					border-color: #25D366 !important;
				}
				.imob-construtora-contato .btn-wa:hover {
					opacity: 0.9;
					color: #ffffff !important;
				}
				@media (max-width: 768px) {
					.imob-construtora-header {
						padding: 25px 20px;
					}
					.imob-construtora-header-inner {
						flex-direction: column;
						text-align: center;
						gap: 20px;
					}
					.imob-construtora-logo {
						margin: 0 auto;
						width: 100%;
						max-width: 180px;
					}
					.imob-construtora-title {
						font-size: 1.75rem; /* Diminuído no smartphone */
					}
					.imob-construtora-content {
						font-size: 0.95rem;
					}
					.imob-construtora-contato {
						justify-content: center;
					}
				}
			</style>

			<!-- EMPREENDIMENTOS DESTA CONSTRUTORA -->
			<?php
			$emp_args = array(
				'post_type'      => 'empreendimento',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_imob_emp_construtora_id',
						'value'   => get_the_ID(),
						'compare' => '=',
					),
				),
			);
			$emp_query = new WP_Query( $emp_args );
			if ( $emp_query->have_posts() ) :
			?>
				<div class="imob-construtora-empreendimentos" style="margin-bottom: 50px;">
					<h2 style="font-size: 1.8rem; margin-bottom: 25px; text-align: center; color: var(--primary-color); font-weight: 800;">
						<?php _e( 'Empreendimentos desta Construtora', 'imobiliaria-tema' ); ?>
					</h2>
					<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
						<?php while ( $emp_query->have_posts() ) : $emp_query->the_post(); 
							$estagio = get_post_meta( get_the_ID(), '_imob_emp_estagio', true );
							$previsao = get_post_meta( get_the_ID(), '_imob_emp_previsao', true );
						?>
							<div class="imob-card" style="display: flex; flex-direction: column; overflow: hidden; background: #fff; border: 1px solid var(--border-color); border-radius: 8px;">
								<div class="imob-card-thumb" style="height: 200px; position: relative;">
									<div class="imob-card-badges">
										<?php echo imob_render_empreendimento_badges( get_the_ID() ); ?>
									</div>
									<a href="<?php the_permalink(); ?>">
										<?php if ( has_post_thumbnail() ) : ?>
											<?php the_post_thumbnail( 'medium_large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;' ) ); ?>
										<?php else : ?>
											<div class="imob-card-placeholder" style="display:flex; align-items:center; justify-content:center; height:100%; background:#f0f2f5;">
												<span class="material-symbols-outlined" style="font-size: 40px; color:#b0b7c3;">apartment</span>
											</div>
										<?php endif; ?>
									</a>
								</div>
								<div class="imob-card-content" style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
									<?php the_title( '<h3 class="imob-card-title" style="margin: 0 0 10px; font-size: 1.2rem;"><a href="' . esc_url( get_permalink() ) . '" style="color: var(--primary-color); text-decoration: none;">', '</a></h3>' ); ?>
									<?php if ( $previsao ) : ?>
										<div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 15px;">
											<span>Previsão de entrega: <strong><?php echo esc_html( $previsao ); ?></strong></span>
										</div>
									<?php endif; ?>
									<a href="<?php the_permalink(); ?>" class="imob-read-more-link" style="margin-top: auto; color: var(--accent-color); font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
										<?php _e( 'Ver empreendimento', 'imobiliaria-tema' ); ?>
										<span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
									</a>
								</div>
							</div>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="imob-construtora-imoveis">
				<h2 style="font-size: 1.8rem; margin-bottom: 30px; text-align: center;">Imóveis desta Construtora</h2>
				<?php
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$args = array(
					'post_type' => 'imovel',
					'posts_per_page' => 12,
					'paged' => $paged,
					'meta_query' => array(
						array(
							'key' => '_imob_construtora_id',
							'value' => get_the_ID(),
							'compare' => '='
						)
					)
				);
				$imoveis = new WP_Query( $args );

				if ( $imoveis->have_posts() ) : ?>
					<div class="imob-imoveis-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
						<?php
						while ( $imoveis->have_posts() ) : $imoveis->the_post();
							
							$preco_venda = get_post_meta( get_the_ID(), '_imob_preco_venda', true );
							$price_html = imob_get_formatted_price( get_the_ID(), false );
							$quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
							$banheiros = get_post_meta( get_the_ID(), '_imob_banheiros', true );
							$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
							
							$tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel', array( 'fields' => 'names' ) );
							$status_list = wp_get_post_terms( get_the_ID(), 'status_imovel', array( 'fields' => 'names' ) );
							$localidades = wp_get_post_terms( get_the_ID(), 'localidade', array( 'fields' => 'names' ) );
							
							$tipo = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : 'IMÓVEL';
							$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : 'VENDA';
							$bairro = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : '';
							?>
							<article class="imob-card">
								<div class="imob-card-thumb">
									<div class="imob-card-badges">
										<span class="badge-tipo"><?php echo esc_html( imob_strtoupper( $tipo ) ); ?></span>
										<span class="badge-finalidade"><?php echo esc_html( imob_strtoupper( $finalidade ) ); ?></span>
										<?php 
										$badge_emp_html = imob_render_empreendimento_badge( get_the_ID() );
										if ( ! empty( $badge_emp_html ) ) {
											echo $badge_emp_html;
										}
										?>
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
									<div class="imob-card-info-top">
										<span class="info-bairro"><span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; color: var(--accent-color);">location_on</span> Bairro: <?php echo esc_html( $bairro ); ?></span>
									</div>
									<?php the_title( '<h3 class="imob-card-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' ); ?>
									
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
										<span class="imob-card-date">Data do anúncio: <?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
									</div>
								</div>
							</article>
						<?php endwhile; ?>
					</div>

					<div class="imob-pagination" style="margin-top: 40px; text-align: center;">
						<?php
						echo paginate_links( array(
							'total' => $imoveis->max_num_pages,
							'prev_text' => '<span class="material-symbols-outlined">chevron_left</span>',
							'next_text' => '<span class="material-symbols-outlined">chevron_right</span>',
						) );
						?>
					</div>
					<?php wp_reset_postdata(); ?>
				<?php else : ?>
					<p style="text-align: center; color: var(--text-light);"><?php _e( 'Nenhum imóvel encontrado para esta construtora.', 'imobiliaria-tema' ); ?></p>
				<?php endif; ?>
			</div>

		<?php endwhile; ?>
	</div>
</main>

<?php get_footer(); ?>
