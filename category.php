<?php
/**
 * The template for displaying Category pages
 *
 * @package ImobiliariaTema
 */

get_header(); 
$current_category = get_queried_object();
?>

<main id="primary" class="site-main imob-category-page">
	<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 15px;">
		
		<!-- CABEÇALHO DA CATEGORIA -->
		<header class="imob-archive-header" style="margin-bottom: 40px; text-align: center; background: #ffffff; padding: 40px 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
			<div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(178, 145, 90, 0.12); color: var(--accent-color); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
				<span class="material-symbols-outlined" style="font-size: 16px;">category</span>
				<?php _e( 'Categoria', 'imobiliaria-tema' ); ?>
			</div>
			
			<h1 class="imob-archive-title" style="font-size: 2.2rem; color: var(--primary-color); margin: 0 0 10px; font-weight: 800;">
				<?php single_cat_title(); ?>
			</h1>
			
			<?php if ( ! empty( $current_category->description ) ) : ?>
				<div class="imob-archive-description" style="max-width: 700px; margin: 0 auto 15px; color: var(--text-light); font-size: 1rem; line-height: 1.6;">
					<?php echo wp_kses_post( $current_category->description ); ?>
				</div>
			<?php endif; ?>

			<div style="font-size: 0.85rem; color: var(--text-light); font-weight: 500;">
				<?php 
				global $wp_query;
				$total = $wp_query->found_posts;
				printf( _n( '%s artigo publicado', '%s artigos publicados', $total, 'imobiliaria-tema' ), number_format_i18n( $total ) );
				?>
			</div>
		</header>

		<!-- GRID DE POSTS DA CATEGORIA -->
		<?php if ( have_posts() ) : ?>
			<div class="imob-elementor-imovel-grid imob-elementor-post-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
				<?php 
				while ( have_posts() ) : the_post(); 
					$post_categories = get_the_category();
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card imob-post-card' ); ?>>
						
						<!-- THUMBNAIL -->
						<div class="imob-card-thumb">
							<a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'imob_thumb' );
								} else {
									echo '<div class="imob-card-placeholder" style="display:flex; align-items:center; justify-content:center; background:#f0f2f5;">';
									echo '<span class="material-symbols-outlined" style="font-size: 44px; color: #b0b7c3;">article</span>';
									echo '</div>';
								}
								?>
							</a>
						</div>

						<!-- CONTEÚDO DO CARD -->
						<div class="imob-card-content" style="display: flex; flex-direction: column; flex-grow: 1;">
							<?php the_title( '<h3 class="imob-card-title" style="margin: 0 0 10px; font-size: 1.2rem; line-height: 1.4;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>

							<div class="imob-card-residencial imob-post-excerpt" style="line-height: 1.6; margin-bottom: 15px; color: var(--text-light); font-size: 0.95rem;">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</div>

							<!-- BADGES DE TODAS AS CATEGORIAS DO POST -->
							<?php if ( ! empty( $post_categories ) ) : ?>
								<div class="imob-post-categories-badges" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 15px; margin-top: auto;">
									<?php foreach ( $post_categories as $cat ) : ?>
										<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="badge-categoria"><?php echo esc_html( imob_strtoupper( $cat->name ) ); ?></a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<!-- RODAPÉ DO CARD -->
							<div class="imob-card-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: auto;">
								<span class="imob-card-date" style="display: inline-flex; align-items: center; gap: 6px; color: var(--text-light); font-size: 0.85rem; font-weight: 500;">
									<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">calendar_today</span>
									<?php echo get_the_date( 'j \d\e F \d\e Y' ); ?>
								</span>
								
								<a href="<?php the_permalink(); ?>" class="imob-read-more-link" style="color: var(--accent-color) !important; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; text-decoration: none;">
									<?php _e( 'Ler artigo', 'imobiliaria-tema' ); ?>
									<span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
								</a>
							</div>
						</div>

					</article>
				<?php endwhile; ?>
			</div>

			<!-- PAGINAÇÃO -->
			<div class="imob-pagination" style="margin-top: 50px; display: flex; justify-content: center; gap: 8px;">
				<?php
				echo paginate_links( array(
					'prev_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_left</span>',
					'next_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_right</span>',
					'type'      => 'list',
				) );
				?>
			</div>

		<?php else : ?>
			<div style="background: #ffffff; padding: 60px 20px; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
				<span class="material-symbols-outlined" style="font-size: 48px; color: var(--text-light); margin-bottom: 15px;">folder_off</span>
				<h3 style="margin: 0 0 10px; color: var(--primary-color);"><?php _e( 'Nenhum artigo encontrado', 'imobiliaria-tema' ); ?></h3>
				<p style="color: var(--text-light); margin: 0 0 20px;"><?php _e( 'Ainda não foram publicados artigos nesta categoria.', 'imobiliaria-tema' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px; background: var(--primary-color); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
					<span class="material-symbols-outlined">home</span> <?php _e( 'Voltar à Página Inicial', 'imobiliaria-tema' ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php 
get_footer();
