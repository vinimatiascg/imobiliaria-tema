<?php
/**
 * Seção: apresentação com foto (texto à esquerda, foto com fundo transparente à direita).
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$side     = ( 'left' === $args['image_side'] ) ? 'left' : 'right';
$backdrop = in_array( $args['backdrop'], array( 'arch', 'circle', 'none' ), true ) ? $args['backdrop'] : 'arch';

$img_html = imob_section_image(
	$args['image'],
	'full',
	array(
		'class'   => 'hm-photo-img',
		'loading' => 'lazy',
		'sizes'   => '(max-width: 992px) 90vw, 560px',
		'alt'     => $args['image_alt'],
	)
);

$classes = array( 'hm-container', 'hm-photo-split', 'hm-photo-split--' . $side );
if ( $args['title_large'] ) {
	$classes[] = 'hm-photo-split--large';
}
?>
<section class="hm-section hm-photo-section">
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<div class="hm-photo-content">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="hm-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( $args['title'] ) : ?>
				<h2 class="hm-photo-title"><?php echo nl2br( esc_html( $args['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php if ( $args['subtitle'] ) : ?>
				<p class="hm-photo-subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
			<?php endif; ?>
			<?php if ( $args['content'] ) : ?>
				<div class="hm-photo-text">
					<?php echo wpautop( imob_section_html( $args['content'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</div>
			<?php endif; ?>
			<?php
			if ( $args['show_buttons'] ) {
				imob_section_buttons( $args, false );
			}
			?>
		</div>

		<div class="hm-photo-media hm-photo-media--<?php echo esc_attr( $backdrop ); ?><?php echo $img_html ? '' : ' hm-photo-media--empty'; ?>">
			<?php if ( 'none' !== $backdrop ) : ?>
				<span class="hm-photo-backdrop" aria-hidden="true"></span>
			<?php endif; ?>
			<?php if ( $img_html ) : ?>
				<?php echo $img_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php elseif ( imob_sections_is_editor() ) : ?>
				<span class="hm-photo-hint"><span class="material-symbols-outlined" aria-hidden="true">add_photo_alternate</span><?php esc_html_e( 'Envie uma foto em PNG com fundo transparente', 'imobiliaria-tema' ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $args['divider'] ) : ?>
		<span class="hm-divider hm-split-divider" aria-hidden="true"></span>
	<?php endif; ?>
</section>
