<?php
/**
 * Seção: topo (hero).
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$layout = isset( $args['layout'] ) ? $args['layout'] : 'auto';
if ( 'auto' === $layout || '' === $layout ) {
	$layout = is_front_page() ? 'offer' : 'classic';
}

if ( 'offer' === $layout && function_exists( 'imob_bf_hero_args' ) ) {
	$bf   = imob_bf_hero_args();
	$url1 = ! empty( $args['offer_btn1_url'] ) ? imob_section_url( $args['offer_btn1_url'] ) : $bf['btn1_url'];
	$url2 = ! empty( $args['offer_btn2_url'] ) ? imob_section_url( $args['offer_btn2_url'] ) : $bf['btn2_url'];
	get_template_part(
		'template-parts/sections/hero-offer',
		null,
		array(
			'tag'         => $args['offer_tag'] ?? $bf['tag'],
			'title_big'   => $args['offer_title_big'] ?? $bf['title_big'],
			'title_sub'   => $args['offer_title_sub'] ?? $bf['title_sub'],
			'text'        => $args['offer_text'] ?? $bf['text'],
			'btn1_text'   => $args['offer_btn1_text'] ?? $bf['btn1_text'],
			'btn1_url'    => $url1,
			'btn2_text'   => $args['offer_btn2_text'] ?? $bf['btn2_text'],
			'btn2_url'    => $url2,
			'period'      => ( ! isset( $args['offer_period'] ) || $args['offer_period'] ) ? $bf['period'] : '',
			'card_label'  => $args['card_label'] ?? $bf['card_label'],
			'card_prefix' => $args['card_prefix'] ?? $bf['card_prefix'],
			'card_value'  => $args['card_value'] ?? $bf['card_value'],
			'card_text'   => $args['card_text'] ?? $bf['card_text'],
			'countdown'   => ( ! isset( $args['card_countdown'] ) || $args['card_countdown'] ) ? $bf['countdown'] : 0,
			'is_over'     => $bf['is_over'],
		)
	);
	return;
}

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
<section class="hm-hero hm-hero--<?php echo esc_attr( $style ); ?><?php echo $construtoras ? ' hm-hero--has-partners' : ''; ?><?php echo ( ! $img_html && ! $bg_html ) ? ' hm-hero--no-media' : ''; ?>">
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

		<?php if ( $img_html || $bg_html ) : ?>
			<div class="hm-hero-media">
				<?php
				echo $bg_html; // phpcs:ignore WordPress.Security.EscapeOutput
				echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</div>
		<?php endif; ?>
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
