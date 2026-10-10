<?php
/**
 * Archive Template: Empreendimentos
 *
 * @package ImobiliariaTema
 */

get_header(); ?>

<main id="primary" class="site-main imob-archive-empreendimentos">
	<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 15px;">
		
		<!-- CABEÇALHO DA PÁGINA -->
		<header class="imob-archive-header" style="text-align: center; margin-bottom: 40px;">
			<h1 style="font-size: 2.2rem; color: var(--primary-color); margin: 0 0 10px; font-weight: 800;">
				<span class="material-symbols-outlined" style="vertical-align: middle; color: var(--accent-color); font-size: 32px;">apartment</span>
				<?php _e( 'Nossos Empreendimentos', 'imobiliaria-tema' ); ?>
			</h1>
			<p style="color: var(--text-light); max-width: 650px; margin: 0 auto; font-size: 1.05rem;">
				<?php _e( 'Descubra os melhores lançamentos, condomínios e empreendimentos com infraestrutura completa para você e sua família.', 'imobiliaria-tema' ); ?>
			</p>
		</header>

		<!-- GRID DE EMPREENDIMENTOS -->
		<?php if ( have_posts() ) : ?>
			<div class="imob-emp-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
				<?php while ( have_posts() ) : the_post();
					$emp_id         = get_the_ID();
					$previsao       = get_post_meta( $emp_id, '_imob_emp_previsao', true );
					$endereco       = get_post_meta( $emp_id, '_imob_emp_endereco', true );
					$construtora_id = get_post_meta( $emp_id, '_imob_emp_construtora_id', true );
					$construtora    = ( ! empty( $construtora_id ) ) ? get_post( (int) $construtora_id ) : null;
				?>
					<article class="imob-card" style="display: flex; flex-direction: column; overflow: hidden; background: #fff; border: 1px solid var(--border-color); border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: transform 0.2s, box-shadow 0.2s;">
						<div class="imob-card-thumb" style="height: 220px; position: relative;">
							<div class="imob-card-badges">
								<?php echo imob_render_empreendimento_badges( $emp_id ); ?>
							</div>
							<a href="<?php the_permalink(); ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium_large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;' ) ); ?>
								<?php else : ?>
									<div class="imob-card-placeholder" style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f1f5f9;">
										<span class="material-symbols-outlined" style="font-size: 48px; color: #94a3b8;">domain</span>
									</div>
								<?php endif; ?>
							</a>
						</div>

						<div class="imob-card-content" style="padding: 22px; display: flex; flex-direction: column; flex-grow: 1;">
							<?php if ( $construtora ) : ?>
								<div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--accent-color); font-weight: 700; margin-bottom: 6px;">
									<?php echo esc_html( $construtora->post_title ); ?>
								</div>
							<?php endif; ?>

							<h3 class="imob-card-title" style="margin: 0 0 10px; font-size: 1.3rem; font-weight: 700; line-height: 1.3;">
								<a href="<?php the_permalink(); ?>" style="color: var(--primary-color); text-decoration: none;">
									<?php the_title(); ?>
								</a>
							</h3>

							<?php if ( $endereco ) : ?>
								<p style="margin: 0 0 15px; font-size: 0.9rem; color: var(--text-light); display: flex; align-items: center; gap: 4px;">
									<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">location_on</span>
									<?php echo esc_html( wp_trim_words( $endereco, 7, '...' ) ); ?>
								</p>
							<?php endif; ?>

							<div style="margin-top: auto; padding-top: 15px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
								<?php if ( $previsao ) : ?>
									<span style="font-size: 0.85rem; color: var(--text-dark); display: inline-flex; align-items: center; gap: 4px;">
										<span class="material-symbols-outlined" style="font-size: 15px; color: var(--accent-color);">event</span>
										<?php echo esc_html( $previsao ); ?>
									</span>
								<?php else : ?>
									<span></span>
								<?php endif; ?>

								<a href="<?php the_permalink(); ?>" class="imob-read-more-link" style="color: var(--accent-color); font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
									<?php _e( 'Detalhes', 'imobiliaria-tema' ); ?> &rarr;
								</a>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<!-- PAGINAÇÃO -->
			<div class="imob-pagination" style="margin-top: 50px;">
				<?php
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_left</span>',
					'next_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_right</span>',
				) );
				?>
			</div>

		<?php else : ?>
			<div style="background: #fff; padding: 50px 20px; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
				<span class="material-symbols-outlined" style="font-size: 48px; color: var(--text-light); margin-bottom: 12px;">domain_disabled</span>
				<h3 style="margin: 0 0 10px; color: var(--primary-color); font-size: 1.3rem;"><?php _e( 'Nenhum empreendimento cadastrado', 'imobiliaria-tema' ); ?></h3>
				<p style="color: var(--text-light); margin: 0;"><?php _e( 'Em breve novos lançamentos estarão disponíveis por aqui.', 'imobiliaria-tema' ); ?></p>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
