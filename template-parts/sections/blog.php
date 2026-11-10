<?php
/**
 * Seção: últimas do blog.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$blog_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => $args['count'] ? $args['count'] : 4,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( ! $blog_query->have_posts() ) {
	return;
}
?>
<section class="hm-section hm-section--light">
	<div class="hm-container">
		<?php imob_section_head( $args['title'], $args['text'] ); ?>
		<div class="hm-grid hm-grid--4">
			<?php
			while ( $blog_query->have_posts() ) :
				$blog_query->the_post();
				$is_new = ( time() - get_post_time( 'U', true ) ) < 30 * DAY_IN_SECONDS;
				?>
				<article class="hm-post">
					<a class="hm-post-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php
						if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) );
						} else {
							echo '<span class="hm-card-placeholder"></span>';
						}
						?>
						<?php if ( $is_new ) : ?>
							<span class="hm-badge hm-badge--gold"><?php esc_html_e( 'Novo', 'imobiliaria-tema' ); ?></span>
						<?php endif; ?>
					</a>
					<div class="hm-post-body">
						<h3 class="hm-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="hm-post-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '...' ) ); ?></p>
						<time class="hm-post-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><span class="material-symbols-outlined">schedule</span><?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?></time>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
