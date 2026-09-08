<?php
/**
 * The template for displaying archive of imóveis.
 *
 * @package ImobiliariaTema
 */

get_header(); ?>

<main id="primary" class="site-main imob-archive-imovel">
	<header class="page-header">
		<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<div class="archive-description">', '</div>' );
		?>
	</header>

	<div class="imob-imovel-grid">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card' ); ?>>
					<div class="imob-card-thumb">
						<a href="<?php the_permalink(); ?>">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'medium' );
							}
							?>
						</a>
					</div>
					<div class="imob-card-content">
						<?php the_title( '<h2 class="imob-card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
						<?php echo imob_get_formatted_price( get_the_ID(), false ); ?>
					</div>
				</article>
				<?php
			endwhile;
			the_posts_navigation();
		else :
			echo '<p>Nenhum imóvel encontrado.</p>';
		endif;
		?>
	</div>
</main>

<?php
get_footer();
