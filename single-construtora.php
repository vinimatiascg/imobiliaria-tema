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
			<header class="imob-construtora-header" style="margin-bottom: 40px; text-align: center; padding: 40px; background: #fff; border: 1px solid var(--border-color); border-radius: 8px;">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="imob-construtora-logo" style="margin-bottom: 20px;">
						<?php the_post_thumbnail( 'medium', ['style' => 'max-height: 120px; width: auto;'] ); ?>
					</div>
				<?php endif; ?>
				<h1 class="imob-archive-title" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 15px;"><?php the_title(); ?></h1>
				
				<div class="imob-construtora-content" style="color: var(--text-light); max-width: 800px; margin: 0 auto 20px;">
					<?php the_content(); ?>
				</div>

				<div class="imob-construtora-contato" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
					<?php if ( $telefone ) : ?>
						<a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', $telefone)); ?>" class="btn-contato" style="background: var(--bg-light); padding: 8px 15px; border-radius: 4px; color: var(--text-color); text-decoration: none; display: flex; align-items: center; gap: 5px;"><span class="material-symbols-outlined">call</span> <?php echo esc_html($telefone); ?></a>
					<?php endif; ?>
					<?php if ( $whatsapp ) : ?>
						<a href="https://wa.me/55<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" class="btn-contato" style="background: #25D366; padding: 8px 15px; border-radius: 4px; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 5px;"><span class="material-symbols-outlined">chat</span> <?php echo esc_html($whatsapp); ?></a>
					<?php endif; ?>
					<?php if ( $site ) : ?>
						<a href="<?php echo esc_url($site); ?>" target="_blank" class="btn-contato" style="background: var(--bg-light); padding: 8px 15px; border-radius: 4px; color: var(--text-color); text-decoration: none; display: flex; align-items: center; gap: 5px;"><span class="material-symbols-outlined">language</span> Site</a>
					<?php endif; ?>
					<?php if ( $instagram ) : ?>
						<a href="https://instagram.com/<?php echo esc_attr(str_replace('@', '', $instagram)); ?>" target="_blank" class="btn-contato" style="background: var(--bg-light); padding: 8px 15px; border-radius: 4px; color: var(--text-color); text-decoration: none; display: flex; align-items: center; gap: 5px;"><span class="material-symbols-outlined">photo_camera</span> <?php echo esc_html($instagram); ?></a>
					<?php endif; ?>
				</div>
			</header>

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
								<div class="imob-card-thumb" style="height: 200px;">
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
									<?php if ( $previsao || $estagio ) : ?>
										<div style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 15px;">
											<?php if ( $estagio ) : ?><span>Estágio: <strong><?php echo esc_html( $estagio ); ?></strong></span><br><?php endif; ?>
											<?php if ( $previsao ) : ?><span>Previsão: <strong><?php echo esc_html( $previsao ); ?></strong></span><?php endif; ?>
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
