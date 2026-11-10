<?php
/**
 * Seção: depoimentos (CPT "depoimento").
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$depoimentos = get_posts(
	array(
		'post_type'      => 'depoimento',
		'posts_per_page' => $args['count'] ? $args['count'] : 12,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);
$btn_url = imob_section_url( $args['btn1_url'] ? $args['btn1_url'] : '{whatsapp}' );
?>
<section class="hm-testimonials">
	<div class="hm-container">
		<?php if ( $depoimentos ) : ?>
			<?php imob_section_head( $args['title'], $args['text'] ); ?>
			<div class="hm-testimonials-track">
				<?php
				foreach ( $depoimentos as $dep ) :
					$nome   = get_post_meta( $dep->ID, '_imob_dep_nome', true );
					$cidade = get_post_meta( $dep->ID, '_imob_dep_cidade', true );
					$nota   = (int) get_post_meta( $dep->ID, '_imob_dep_nota', true );
					$nota   = $nota ? $nota : 5;
					?>
					<figure class="hm-testimonial">
						<span class="hm-quote" aria-hidden="true">&ldquo;</span>
						<h3 class="hm-testimonial-title"><?php echo esc_html( get_the_title( $dep ) ); ?></h3>
						<blockquote class="hm-testimonial-text"><?php echo wp_kses_post( wpautop( $dep->post_content ) ); ?></blockquote>
						<figcaption class="hm-testimonial-author">
							<span class="hm-avatar">
								<?php
								if ( has_post_thumbnail( $dep ) ) {
									echo get_the_post_thumbnail( $dep, 'thumbnail', array( 'loading' => 'lazy' ) );
								} else {
									echo esc_html( mb_substr( $nome ? $nome : get_the_title( $dep ), 0, 1 ) );
								}
								?>
							</span>
							<span class="hm-author-info">
								<strong><?php echo esc_html( $nome ); ?></strong>
								<?php if ( $cidade ) : ?>
									<small><?php echo esc_html( $cidade ); ?></small>
								<?php endif; ?>
							</span>
							<span class="hm-stars" aria-label="<?php echo esc_attr( sprintf( __( 'Nota %d de 5', 'imobiliaria-tema' ), $nota ) ); ?>"><?php echo esc_html( str_repeat( '★', $nota ) . str_repeat( '☆', 5 - $nota ) ); ?></span>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php elseif ( imob_sections_is_editor() ) : ?>
			<?php imob_section_head( $args['title'], __( 'Cadastre depoimentos no menu Depoimentos do painel para exibi-los aqui.', 'imobiliaria-tema' ) ); ?>
		<?php endif; ?>
		<?php if ( $args['btn1_text'] ) : ?>
			<div class="hm-testimonials-cta">
				<a class="hm-btn hm-btn--blue" href="<?php echo esc_url( $btn_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo imob_home_whatsapp_icon(); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $args['btn1_text'] ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
