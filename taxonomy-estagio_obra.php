<?php
/**
 * Taxonomy Template: Estágio da Obra
 *
 * @package ImobiliariaTema
 */

get_header(); 
$term = get_queried_object();
?>

<main id="primary" class="site-main imob-archive">
	<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
		<header class="imob-archive-header" style="margin-bottom: 40px; text-align: center; background: #ffffff; padding: 40px 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
			<div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(178, 145, 90, 0.12); color: var(--accent-color); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
				<span class="material-symbols-outlined" style="font-size: 16px;">construction</span>
				<?php _e( 'Estágio da Obra', 'imobiliaria-tema' ); ?>
			</div>
			<h1 class="imob-archive-title" style="font-size: 2.3rem; color: var(--primary-color); font-weight: 800; margin: 0 0 10px;">
				<?php printf( __( 'Imóveis em %s', 'imobiliaria-tema' ), esc_html( $term->name ) ); ?>
			</h1>
			<?php if ( ! empty( $term->description ) ) : ?>
				<div class="imob-archive-description" style="color: var(--text-light); max-width: 650px; margin: 10px auto 0; font-size: 1rem; line-height: 1.6;"><?php echo wp_kses_post( $term->description ); ?></div>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="imob-imoveis-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
				<?php
				while ( have_posts() ) :
					the_post();
					
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

			<div class="imob-pagination" style="margin-top: 50px;">
				<?php
				echo paginate_links( array(
					'prev_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_left</span>',
					'next_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_right</span>',
					'type'      => 'list',
				) );
				?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<div style="background: #ffffff; padding: 60px 20px; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
				<span class="material-symbols-outlined" style="font-size: 48px; color: var(--text-light); margin-bottom: 12px;">search_off</span>
				<h3 style="color: var(--primary-color); margin: 0 0 10px;"><?php _e( 'Nenhum imóvel encontrado neste estágio de obra', 'imobiliaria-tema' ); ?></h3>
				<p style="color: var(--text-light); margin: 0 0 20px;"><?php _e( 'Não há imóveis disponíveis cadastrados nesta categoria no momento.', 'imobiliaria-tema' ); ?></p>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'imovel' ) ); ?>" class="btn-primary" style="background: var(--accent-color); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
					<span class="material-symbols-outlined">restart_alt</span> <?php _e( 'Ver Todos os Imóveis', 'imobiliaria-tema' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
