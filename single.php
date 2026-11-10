<?php
/**
 * The template for displaying all single blog posts
 *
 * @package ImobiliariaTema
 */

get_header();
?>

<main id="primary" class="site-main imob-single-post-page">
	<div class="container imob-single-post-container">

		<?php
		while ( have_posts() ) :
			the_post();
			$post_id         = get_the_ID();
			$post_categories = get_the_category();
			$post_tags       = get_the_tags();

			// Tempo estimado de leitura
			$raw_content  = wp_strip_all_tags( get_the_content() );
			$word_count   = str_word_count( $raw_content );
			$reading_time = max( 1, (int) ceil( $word_count / 200 ) );

			$blog_page    = get_page_by_path( 'nosso-blog' );
			$blog_url     = $blog_page ? get_permalink( $blog_page->ID ) : home_url( '/nosso-blog/' );
			$share_url    = rawurlencode( get_permalink() );
			$share_title  = rawurlencode( get_the_title() );
			?>

			<!-- Breadcrumb -->
			<nav class="imob-breadcrumb" aria-label="Breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php _e( 'Início', 'imobiliaria-tema' ); ?></a>
				<span class="imob-breadcrumb-sep">/</span>
				<a href="<?php echo esc_url( $blog_url ); ?>"><?php _e( 'Blog', 'imobiliaria-tema' ); ?></a>
				<?php if ( ! empty( $post_categories ) ) : ?>
					<span class="imob-breadcrumb-sep">/</span>
					<a href="<?php echo esc_url( get_category_link( $post_categories[0]->term_id ) ); ?>">
						<?php echo esc_html( $post_categories[0]->name ); ?>
					</a>
				<?php endif; ?>
				<span class="imob-breadcrumb-sep">/</span>
				<span class="imob-breadcrumb-current"><?php the_title(); ?></span>
			</nav>

			<div class="imob-single-post-grid">

				<!-- COLUNA PRINCIPAL DO ARTIGO -->
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-single-article-card' ); ?>>

					<header class="imob-single-article-header">
						<?php if ( ! empty( $post_categories ) ) : ?>
							<div class="imob-post-categories-badges" style="margin-bottom: 14px;">
								<?php foreach ( $post_categories as $cat ) : ?>
									<a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="badge-categoria">
										<?php echo esc_html( function_exists( 'imob_strtoupper' ) ? imob_strtoupper( $cat->name ) : strtoupper( $cat->name ) ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<h1 class="imob-single-article-title"><?php the_title(); ?></h1>

						<div class="imob-single-article-meta">
							<span class="imob-meta-item">
								<span class="material-symbols-outlined">calendar_today</span>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?></time>
							</span>
							<?php if ( get_the_modified_time( 'U' ) - get_the_time( 'U' ) > DAY_IN_SECONDS ) : ?>
								<span class="imob-meta-item">
									<span class="material-symbols-outlined">update</span>
									<?php esc_html_e( 'Atualizado em', 'imobiliaria-tema' ); ?>
									<time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j \d\e F \d\e Y' ) ); ?></time>
								</span>
							<?php endif; ?>
							<span class="imob-meta-item">
								<span class="material-symbols-outlined">schedule</span>
								<?php printf( esc_html__( '%d min de leitura', 'imobiliaria-tema' ), $reading_time ); ?>
							</span>
							<span class="imob-meta-item">
								<span class="material-symbols-outlined">person</span>
								<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo esc_html( get_the_author() ); ?></a>
							</span>
						</div>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="imob-single-article-featured">
							<?php the_post_thumbnail( 'full', array( 'class' => 'imob-single-featured-img', 'sizes' => '(max-width: 900px) 100vw, 860px' ) ); ?>
						</div>
					<?php endif; ?>

					<div class="imob-single-post-content entry-content">
						<?php
						the_content();

						wp_link_pages(
							array(
								'before' => '<div class="page-links">' . esc_html__( 'Páginas:', 'imobiliaria-tema' ),
								'after'  => '</div>',
							)
						);
						?>
					</div>

					<!-- Tags & Compartilhamento -->
					<footer class="imob-single-article-footer">
						<?php if ( ! empty( $post_tags ) && ! is_wp_error( $post_tags ) ) : ?>
							<div class="imob-single-tags">
								<span class="imob-tags-label">
									<span class="material-symbols-outlined" style="font-size: 18px;">sell</span>
									<?php _e( 'Tags:', 'imobiliaria-tema' ); ?>
								</span>
								<?php foreach ( $post_tags as $tag ) : ?>
									<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="imob-tag-chip">
										#<?php echo esc_html( $tag->name ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<div class="imob-single-share">
							<span class="imob-share-label"><?php _e( 'Compartilhar este artigo:', 'imobiliaria-tema' ); ?></span>
							<div class="imob-share-buttons">
								<a href="https://api.whatsapp.com/send?text=<?php echo $share_title . '%20' . $share_url; ?>" target="_blank" rel="noopener noreferrer" class="imob-share-btn imob-share-wa" title="Compartilhar no WhatsApp">
									<span class="material-symbols-outlined">chat</span> WhatsApp
								</a>
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="imob-share-btn imob-share-fb" title="Compartilhar no Facebook">
									<span class="material-symbols-outlined">share</span> Facebook
								</a>
								<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank" rel="noopener noreferrer" class="imob-share-btn imob-share-in" title="Compartilhar no LinkedIn">
									<span class="material-symbols-outlined">work</span> LinkedIn
								</a>
							</div>
						</div>
					</footer>

					<!-- Navegação entre posts -->
					<?php
					$prev_post = get_previous_post();
					$next_post = get_next_post();
					if ( $prev_post || $next_post ) :
						?>
						<nav class="imob-post-nav-cards" aria-label="<?php esc_attr_e( 'Navegação de artigos', 'imobiliaria-tema' ); ?>">
							<?php if ( $prev_post ) : ?>
								<a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="imob-post-nav-card prev">
									<span class="imob-post-nav-label">
										<span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
										<?php _e( 'Artigo Anterior', 'imobiliaria-tema' ); ?>
									</span>
									<strong class="imob-post-nav-title"><?php echo esc_html( get_the_title( $prev_post->ID ) ); ?></strong>
								</a>
							<?php else : ?>
								<div></div>
							<?php endif; ?>

							<?php if ( $next_post ) : ?>
								<a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="imob-post-nav-card next">
									<span class="imob-post-nav-label">
										<?php _e( 'Próximo Artigo', 'imobiliaria-tema' ); ?>
										<span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
									</span>
									<strong class="imob-post-nav-title"><?php echo esc_html( get_the_title( $next_post->ID ) ); ?></strong>
								</a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>

				</article>

				<!-- SIDEBAR DO BLOG -->
				<aside class="imob-single-post-sidebar">

					<!-- Card CTA Buscar Imóveis -->
					<div class="imob-blog-sidebar-card imob-blog-cta-box">
						<div class="imob-blog-cta-icon">
							<span class="material-symbols-outlined">real_estate_agent</span>
						</div>
						<h3><?php _e( 'Procurando o imóvel ideal em Campina Grande?', 'imobiliaria-tema' ); ?></h3>
						<p><?php _e( 'Confira nossa seleção exclusiva de casas, apartamentos, terrenos e lançamentos com condições especiais.', 'imobiliaria-tema' ); ?></p>
						<a href="<?php echo esc_url( home_url( '/imoveis/' ) ); ?>" class="imob-btn-relation">
							<span class="material-symbols-outlined">search</span>
							<?php _e( 'Ver Imóveis Disponíveis', 'imobiliaria-tema' ); ?>
						</a>
					</div>

					<!-- Últimos Artigos -->
					<?php
					$recent_posts = new WP_Query(
						array(
							'post_type'           => 'post',
							'post_status'         => 'publish',
							'posts_per_page'      => 4,
							'post__not_in'        => array( $post_id ),
							'ignore_sticky_posts' => true,
						)
					);
					if ( $recent_posts->have_posts() ) :
						?>
						<div class="imob-blog-sidebar-card">
							<h3 class="imob-blog-sidebar-title">
								<span class="material-symbols-outlined">article</span>
								<?php _e( 'Últimos Artigos', 'imobiliaria-tema' ); ?>
							</h3>
							<div class="imob-blog-recent-list">
								<?php
								while ( $recent_posts->have_posts() ) :
									$recent_posts->the_post();
									?>
									<a href="<?php the_permalink(); ?>" class="imob-blog-recent-item">
										<div class="imob-blog-recent-thumb">
											<?php if ( has_post_thumbnail() ) : ?>
												<?php the_post_thumbnail( 'thumbnail' ); ?>
											<?php else : ?>
												<span class="material-symbols-outlined">article</span>
											<?php endif; ?>
										</div>
										<div class="imob-blog-recent-info">
											<h4><?php the_title(); ?></h4>
											<span><?php echo esc_html( get_the_date( 'd/m/Y' ) ); ?></span>
										</div>
									</a>
								<?php endwhile; ?>
								<?php wp_reset_postdata(); ?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Categorias do Blog -->
					<?php
					$all_categories = get_categories(
						array(
							'orderby'    => 'count',
							'order'      => 'DESC',
							'hide_empty' => true,
						)
					);
					if ( ! empty( $all_categories ) ) :
						?>
						<div class="imob-blog-sidebar-card">
							<h3 class="imob-blog-sidebar-title">
								<span class="material-symbols-outlined">folder</span>
								<?php _e( 'Categorias', 'imobiliaria-tema' ); ?>
							</h3>
							<ul class="imob-blog-cat-list">
								<?php foreach ( $all_categories as $cat_item ) : ?>
									<li>
										<a href="<?php echo esc_url( get_category_link( $cat_item->term_id ) ); ?>">
											<span><?php echo esc_html( $cat_item->name ); ?></span>
											<span class="imob-cat-count"><?php echo intval( $cat_item->count ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

				</aside>

			</div><!-- .imob-single-post-grid -->

			<!-- Artigos Relacionados -->
			<?php
			$related_args = array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 3,
				'post__not_in'        => array( $post_id ),
				'ignore_sticky_posts' => true,
			);
			if ( ! empty( $post_categories ) ) {
				$related_args['category__in'] = wp_list_pluck( $post_categories, 'term_id' );
			}
			$related_query = new WP_Query( $related_args );
			if ( ! $related_query->have_posts() ) {
				unset( $related_args['category__in'] );
				$related_query = new WP_Query( $related_args );
			}

			if ( $related_query->have_posts() ) :
				?>
				<section class="imob-related-posts-section">
					<h2 class="imob-section-heading">
						<?php _e( 'Continue Lendo', 'imobiliaria-tema' ); ?>
					</h2>
					<div class="imob-elementor-imovel-grid imob-elementor-post-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
						<?php
						while ( $related_query->have_posts() ) :
							$related_query->the_post();
							$rel_cats = get_the_category();
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
									<?php the_title( '<h3 class="imob-card-title" style="margin: 0 0 10px; font-size: 1.15rem; line-height: 1.4;"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>

									<div class="imob-card-residencial imob-post-excerpt" style="line-height: 1.6; margin-bottom: 15px; color: var(--text-light); font-size: 0.95rem;">
										<?php echo wp_trim_words( get_the_excerpt(), 18, '...' ); ?>
									</div>

									<?php if ( ! empty( $rel_cats ) ) : ?>
										<div class="imob-post-categories-badges" style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 15px; margin-top: auto;">
											<?php foreach ( $rel_cats as $rcat ) : ?>
												<a href="<?php echo esc_url( get_category_link( $rcat->term_id ) ); ?>" class="badge-categoria"><?php echo esc_html( function_exists( 'imob_strtoupper' ) ? imob_strtoupper( $rcat->name ) : strtoupper( $rcat->name ) ); ?></a>
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
						<?php wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>

		<?php endwhile; ?>

	</div>
</main>

<?php
get_footer();

