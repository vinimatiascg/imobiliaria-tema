<?php
/**
 * Seção: slider de largura total com transição em fade.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$slides = array_values(
	array_filter(
		(array) $args['slides'],
		function ( $s ) {
			return ! empty( $s['title'] ) || ! empty( $s['text'] ) || ! empty( $s['image']['id'] ) || ! empty( $s['image']['url'] );
		}
	)
);
if ( ! $slides ) {
	return;
}

wp_enqueue_style( 'imob-slider' );
wp_enqueue_script( 'imob-slider' );

$height   = in_array( $args['height'], array( 'sm', 'md', 'lg', 'full' ), true ) ? $args['height'] : 'md';
$overlay  = in_array( (string) $args['overlay'], array( '0', '30', '50', '70' ), true ) ? (int) $args['overlay'] : 50;
$interval = max( 2, (int) $args['interval'] ) * 1000;
$multi    = count( $slides ) > 1;
$slider_id = 'hm-slider-' . wp_unique_id();

$is_wa = function ( $url ) {
	return ( false !== strpos( $url, 'whatsapp' ) || false !== strpos( $url, 'wa.me' ) );
};
$ext = function ( $url ) {
	return ( 0 === strpos( $url, 'http' ) && false === strpos( $url, wp_parse_url( home_url(), PHP_URL_HOST ) ) ) ? ' target="_blank" rel="noopener noreferrer"' : '';
};

$classes = array( 'hm-slider', 'hm-slider--' . $height );
if ( $args['full_bleed'] ) {
	$classes[] = 'hm-slider--bleed';
}
?>
<section id="<?php echo esc_attr( $slider_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" style="--hm-slider-overlay: <?php echo esc_attr( $overlay / 100 ); ?>;" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Destaques', 'imobiliaria-tema' ); ?>"
	<?php if ( $multi ) : ?>
		data-hm-slider data-autoplay="<?php echo $args['autoplay'] ? '1' : '0'; ?>" data-interval="<?php echo (int) $interval; ?>"
	<?php endif; ?>>
	<div class="hm-slider-track">
		<?php
		foreach ( $slides as $i => $slide ) :
			$align = in_array( $slide['align'], array( 'left', 'center', 'right' ), true ) ? $slide['align'] : 'left';
			$img   = imob_section_image(
				$slide['image'],
				'full',
				array(
					'class'         => 'hm-slide-bg',
					'loading'       => 0 === $i ? 'eager' : 'lazy',
					'fetchpriority' => 0 === $i ? 'high' : 'low',
					'sizes'         => '100vw',
				)
			);
			$tag   = ( 0 === $i && $args['first_is_h1'] ) ? 'h1' : 'h2';
			$u1    = imob_section_url( $slide['btn1_url'] );
			$u2    = imob_section_url( $slide['btn2_url'] );
			$link  = imob_section_url( $slide['link_url'] );
			?>
			<div class="hm-slide hm-slide--<?php echo esc_attr( $align ); ?><?php echo 0 === $i ? ' is-active' : ''; ?><?php echo $img ? '' : ' hm-slide--no-image'; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( __( '%1$d de %2$d', 'imobiliaria-tema' ), $i + 1, count( $slides ) ) ); ?>"<?php echo 0 === $i ? '' : ' aria-hidden="true"'; ?>>
				<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php if ( $link ) : ?>
					<a class="hm-slide-link" href="<?php echo esc_url( $link ); ?>"<?php echo $ext( $link ); // phpcs:ignore WordPress.Security.EscapeOutput ?> tabindex="-1" aria-hidden="true"></a>
				<?php endif; ?>
				<div class="hm-container hm-slide-inner">
					<div class="hm-slide-content">
						<?php if ( $slide['eyebrow'] ) : ?>
							<p class="hm-eyebrow hm-slide-eyebrow"><?php echo esc_html( $slide['eyebrow'] ); ?></p>
						<?php endif; ?>
						<?php if ( $slide['title'] ) : ?>
							<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> class="hm-slide-title"><?php echo esc_html( $slide['title'] ); ?></<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<?php endif; ?>
						<?php if ( $slide['text'] ) : ?>
							<p class="hm-slide-text"><?php echo nl2br( esc_html( $slide['text'] ) ); ?></p>
						<?php endif; ?>
						<?php if ( ( $slide['btn1_text'] && $u1 ) || ( $slide['btn2_text'] && $u2 ) ) : ?>
							<div class="hm-actions">
								<?php if ( $slide['btn1_text'] && $u1 ) : ?>
									<a class="hm-btn hm-btn--blue" href="<?php echo esc_url( $u1 ); ?>"<?php echo $ext( $u1 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $is_wa( $u1 ) ? imob_home_whatsapp_icon() : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $slide['btn1_text'] ); ?></a>
								<?php endif; ?>
								<?php if ( $slide['btn2_text'] && $u2 ) : ?>
									<a class="hm-btn hm-btn--ghost" href="<?php echo esc_url( $u2 ); ?>"<?php echo $ext( $u2 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $is_wa( $u2 ) ? imob_home_whatsapp_icon() : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $slide['btn2_text'] ); ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $multi && $args['arrows'] ) : ?>
		<button class="hm-slider-arrow hm-slider-arrow--prev" type="button" data-hm-slider-prev aria-controls="<?php echo esc_attr( $slider_id ); ?>" aria-label="<?php esc_attr_e( 'Slide anterior', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined" aria-hidden="true">chevron_left</span></button>
		<button class="hm-slider-arrow hm-slider-arrow--next" type="button" data-hm-slider-next aria-controls="<?php echo esc_attr( $slider_id ); ?>" aria-label="<?php esc_attr_e( 'Próximo slide', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined" aria-hidden="true">chevron_right</span></button>
	<?php endif; ?>

	<?php if ( $multi && $args['dots'] ) : ?>
		<div class="hm-slider-dots">
			<?php foreach ( $slides as $i => $slide ) : ?>
				<button type="button" class="hm-slider-dot<?php echo 0 === $i ? ' is-active' : ''; ?>" data-hm-slider-dot="<?php echo (int) $i; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Ir para o slide %d', 'imobiliaria-tema' ), $i + 1 ) ); ?>"<?php echo 0 === $i ? ' aria-current="true"' : ''; ?>></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
