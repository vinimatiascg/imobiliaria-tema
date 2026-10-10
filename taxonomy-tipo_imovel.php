<?php
/**
 * Taxonomy Template: Tipo de Imóvel
 */

get_header(); 
$term = get_queried_object();
?>

<main id="primary" class="site-main imob-archive">
	<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
		<header class="imob-archive-header" style="margin-bottom: 40px; text-align: center;">
			<h1 class="imob-archive-title" style="font-size: 2.5rem; color: var(--primary-color);">Opções de <?php echo esc_html( $term->name ); ?></h1>
			<?php if ( $term->description ) : ?>
				<div class="imob-archive-description" style="color: var(--text-light); max-width: 600px; margin: 10px auto 0;"><?php echo wp_kses_post( $term->description ); ?></div>
			<?php endif; ?>
		</header>

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
			<p style="text-align: center; color: var(--text-light);"><?php _e( 'Nenhum imóvel deste tipo encontrado.', 'imobiliaria-tema' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
