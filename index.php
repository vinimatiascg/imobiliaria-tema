<?php
/**
 * The main template file
 *
 * @package ImobiliariaTema
 */

get_header();
?>

<main id="primary" class="site-main imob-index-page">
	<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 15px;">
		<?php if ( have_posts() ) : ?>
			<div class="imob-elementor-imovel-grid imob-elementor-post-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
				<?php
				while ( have_posts() ) :
					the_post();
					$post_categories = get_the_category();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card imob-post-card' ); ?>>
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

						<div class="imob-card-content" style="display: flex; flex-direction: column; flex-grow: 1;">
							<?php the_title( '<h3 class="imob-card-title" style="margin: 0 0 10px; font-size: 1.2rem; line-height: 1.4;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>

							<div class="imob-card-residencial imob-post-excerpt" style="line-height: 1.6; margin-bottom: 15px; color: var(--text-light); font-size: 0.95rem;">
								<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
							</div>

							<?php if ( ! empty( $post_categories ) ) : ?>
								<div class="imob-post-categories-badges" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 15px; margin-top: auto;">
									<?php foreach ( $post_categories as $cat ) : ?>
										<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="badge-categoria"><?php echo esc_html( function_exists( 'imob_strtoupper' ) ? imob_strtoupper( $cat->name ) : strtoupper( $cat->name ) ); ?></a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<div class="imob-card-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 15px; margin-top: auto;">
								<span class="imob-card-date" style="display: inline-flex; align-items: center; gap: 6px; color: var(--text-light); font-size: 0.85rem; font-weight: 500;">
									<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">calendar_today</span>
									<?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?>
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

			<div class="imob-pagination" style="margin-top: 50px;">
				<?php
				echo paginate_links(
					array(
						'prev_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_left</span>',
						'next_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_right</span>',
						'type'      => 'list',
					)
				);
				?>
			</div>
		<?php else : ?>
			<div style="background: #ffffff; padding: 60px 20px; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
				<span class="material-symbols-outlined" style="font-size: 48px; color: var(--text-light); margin-bottom: 15px;">search_off</span>
				<h3 style="margin: 0 0 10px; color: var(--primary-color);"><?php _e( 'Nenhum conteúdo encontrado.', 'imobiliaria-tema' ); ?></h3>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
