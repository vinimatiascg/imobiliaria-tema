<?php
/**
 * The template for displaying archive of imóveis.
 *
 * @package ImobiliariaTema
 */

get_header(); ?>

<main id="primary" class="site-main imob-archive-imovel">
	<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 15px;">
		<header class="page-header" style="margin-bottom: 30px;">
			<?php
			if ( is_search() ) {
				echo '<h1 class="page-title">' . sprintf( __( 'Resultados da busca para: %s', 'imobiliaria-tema' ), '<span>' . esc_html( get_search_query() ) . '</span>' ) . '</h1>';
			} else {
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
			}
			?>
		</header>

		<div class="imob-imovel-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$price_html = imob_get_formatted_price( get_the_ID(), false );
					$quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
					$banheiros = get_post_meta( get_the_ID(), '_imob_banheiros', true );
					$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );
					$empreendimento = get_post_meta( get_the_ID(), '_imob_empreendimento', true );

					$tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel', array( 'fields' => 'names' ) );
					$status_list = wp_get_post_terms( get_the_ID(), 'status_imovel', array( 'fields' => 'names' ) );
					$localidades = wp_get_post_terms( get_the_ID(), 'localidade', array( 'fields' => 'names' ) );

					$tipo = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : 'IMÓVEL';
					$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : 'VENDA';
					$bairro = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : '';
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card' ); ?>>
						<div class="imob-card-thumb">
							<div class="imob-card-badges">
								<span class="badge-tipo"><?php echo esc_html( imob_strtoupper( $tipo ) ); ?></span>
								<span class="badge-finalidade"><?php echo esc_html( imob_strtoupper( $finalidade ) ); ?></span>
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
							
							<?php if ( $empreendimento ) : ?>
								<p class="imob-card-residencial">Residencial: <strong><?php echo esc_html( $empreendimento ); ?></strong></p>
							<?php endif; ?>

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
					<?php
				endwhile;
				the_posts_navigation();
			else :
				echo '<p>' . __( 'Nenhum imóvel encontrado.', 'imobiliaria-tema' ) . '</p>';
			endif;
			?>
		</div>
	</div>
</main>

<?php
get_footer();
