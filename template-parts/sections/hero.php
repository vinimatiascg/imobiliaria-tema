<?php
/**
 * Seção: topo (hero).
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$style    = ( 'portrait' === $args['image_style'] ) ? 'portrait' : 'angled';
$img_html = imob_section_image(
	$args['image'],
	'full',
	array(
		'class'         => 'hm-hero-img',
		'fetchpriority' => 'high',
		'loading'       => 'eager',
		'sizes'         => '(max-width: 900px) 100vw, 60vw',
	)
);
$bg_html  = ( 'portrait' === $style ) ? imob_section_image( $args['bg_image'], 'full', array( 'class' => 'hm-hero-bg', 'loading' => 'eager' ) ) : '';

$construtoras = array();
if ( $args['show_partners'] ) {
	$construtoras = get_posts(
		array(
			'post_type'      => 'construtora',
			'posts_per_page' => 30,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);
}
$track_id = 'hm-partners-' . wp_unique_id();
?>
<section class="hm-hero hm-hero--<?php echo esc_attr( $style ); ?><?php echo $construtoras ? ' hm-hero--has-partners' : ''; ?>">
	<span class="hm-hero-line hm-hero-line--1" aria-hidden="true"></span>
	<span class="hm-hero-line hm-hero-line--2" aria-hidden="true"></span>

	<div class="hm-hero-inner">
		<div class="hm-hero-content">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="hm-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="hm-hero-title"><?php echo esc_html( $args['title'] ); ?></h1>
			<?php if ( $args['text'] ) : ?>
				<p class="hm-hero-text"><?php echo esc_html( $args['text'] ); ?></p>
			<?php endif; ?>
			<?php imob_section_buttons( $args, true ); ?>
		</div>

		<div class="hm-hero-media">
			<?php
			echo $bg_html; // phpcs:ignore WordPress.Security.EscapeOutput
			echo $img_html ? $img_html : '<span class="hm-hero-img hm-hero-img--placeholder"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		</div>
	</div>

	<?php if ( $construtoras ) : ?>
		<div class="hm-partners">
			<p class="hm-partners-title"><?php echo esc_html( $args['partners_title'] ); ?></p>
			<div class="hm-partners-panel">
				<button class="hm-scroll-btn hm-scroll-btn--prev" type="button" data-hm-scroll="<?php echo esc_attr( $track_id ); ?>" data-dir="-1" aria-label="<?php esc_attr_e( 'Anterior', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">chevron_left</span></button>
				<div class="hm-partners-track" id="<?php echo esc_attr( $track_id ); ?>">
					<?php foreach ( $construtoras as $c ) : ?>
						<a class="hm-partner" href="<?php echo esc_url( get_permalink( $c ) ); ?>" title="<?php echo esc_attr( $c->post_title ); ?>">
							<?php
							if ( has_post_thumbnail( $c ) ) {
								echo get_the_post_thumbnail( $c, 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( $c->post_title ) ) );
							} else {
								echo '<span class="hm-partner-name">' . esc_html( $c->post_title ) . '</span>';
							}
							?>
						</a>
					<?php endforeach; ?>
				</div>
				<button class="hm-scroll-btn hm-scroll-btn--next" type="button" data-hm-scroll="<?php echo esc_attr( $track_id ); ?>" data-dir="1" aria-label="<?php esc_attr_e( 'Próximo', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">chevron_right</span></button>
			</div>
		</div>
	<?php endif; ?>
</section>
