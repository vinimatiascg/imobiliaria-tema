<?php
/**
 * Taxonomy Template: Corretor
 */

get_header(); 
$term = get_queried_object();

$creci = get_term_meta( $term->term_id, 'imob_corretor_creci', true );
$cnai = get_term_meta( $term->term_id, 'imob_corretor_cnai', true );
$resumo = get_term_meta( $term->term_id, 'imob_corretor_resumo', true );
$whatsapp = get_term_meta( $term->term_id, 'imob_corretor_whatsapp', true );
$instagram = get_term_meta( $term->term_id, 'imob_corretor_instagram', true );
$email = get_term_meta( $term->term_id, 'imob_corretor_email', true );
$foto = get_term_meta( $term->term_id, 'imob_corretor_foto', true );
$foto_url = $foto ? wp_get_attachment_image_url( $foto, 'medium' ) : 'https://via.placeholder.com/150x150.png?text=FOTO';
?>

<main id="primary" class="site-main imob-archive">
	<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
		
		<header class="imob-corretor-header" style="margin-bottom: 40px; text-align: center; padding: 40px; background: #fff; border: 1px solid var(--border-color); border-radius: 8px;">
			<div class="imob-corretor-avatar" style="margin: 0 auto 20px; width: 120px; height: 120px; border-radius: 50%; background: url('<?php echo esc_url($foto_url); ?>') center/cover; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"></div>
			<h1 class="imob-archive-title" style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 5px;"><?php echo esc_html( $term->name ); ?></h1>
			
			<div style="margin-bottom: 20px;">
				<span class="role" style="font-weight: 600; color: var(--text-color);">Corretor de Imóveis</span>
				<?php if ( $creci || $cnai ) : ?>
					<br><span class="creci" style="font-size: 0.9rem; color: var(--text-light);">CRECI <?php echo esc_html($creci); ?> <?php echo $cnai ? ' | CNAI '.esc_html($cnai) : ''; ?></span>
				<?php endif; ?>
			</div>
			
			<?php if ( $resumo ) : ?>
				<div class="imob-corretor-content" style="color: var(--text-color); max-width: 800px; margin: 0 auto 25px; line-height: 1.6;">
					<?php echo wp_kses_post( wpautop($resumo) ); ?>
				</div>
			<?php endif; ?>

			<div class="imob-corretor-contato" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
				<?php if ( $whatsapp ) : ?>
					<a href="https://wa.me/55<?php echo esc_attr(preg_replace('/[^0-9]/', '', $whatsapp)); ?>" target="_blank" class="btn-contato" style="background: #25D366; padding: 10px 20px; border-radius: 4px; color: #fff; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;"><span class="material-symbols-outlined">chat</span> <?php echo esc_html($whatsapp); ?></a>
				<?php endif; ?>
				<?php if ( $instagram ) : ?>
					<a href="https://instagram.com/<?php echo esc_attr(str_replace('@', '', $instagram)); ?>" target="_blank" class="btn-contato" style="background: var(--bg-light); padding: 10px 20px; border-radius: 4px; color: var(--text-color); text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;"><span class="material-symbols-outlined">photo_camera</span> Instagram</a>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<a href="mailto:<?php echo esc_attr($email); ?>" class="btn-contato" style="background: var(--bg-light); padding: 10px 20px; border-radius: 4px; color: var(--text-color); text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 500;"><span class="material-symbols-outlined">mail</span> E-mail</a>
				<?php endif; ?>
			</div>
		</header>

		<div class="imob-corretor-imoveis">
			<h2 style="font-size: 1.8rem; margin-bottom: 30px; text-align: center;">Imóveis de <?php echo esc_html( $term->name ); ?></h2>
			
			<?php if ( have_posts() ) : ?>
				<div class="imob-imoveis-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
					<?php
					while ( have_posts() ) :
						the_post();
						
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
						'prev_text' => '<span class="material-symbols-outlined">chevron_left</span>',
						'next_text' => '<span class="material-symbols-outlined">chevron_right</span>',
					) );
					?>
				</div>

			<?php else : ?>
				<p style="text-align: center; color: var(--text-light);"><?php _e( 'Nenhum imóvel encontrado para este corretor.', 'imobiliaria-tema' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php get_footer(); ?>
