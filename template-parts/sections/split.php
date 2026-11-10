<?php
/**
 * Seção: apresentação com título de um lado e quadro de texto do outro.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$classes = array( 'hm-split' );
if ( $args['reverse'] ) {
	$classes[] = 'hm-split--reverse';
}
if ( $args['title_large'] ) {
	$classes[] = 'hm-split--large';
}
?>
<section class="hm-section hm-split-section">
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<div class="hm-split-head">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="hm-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( $args['title'] ) : ?>
				<h2 class="hm-split-title"><?php echo nl2br( esc_html( $args['title'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php endif; ?>
			<?php if ( $args['subtitle'] ) : ?>
				<p class="hm-split-subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
			<?php endif; ?>
			<?php
			if ( $args['show_buttons'] ) {
				imob_section_buttons( $args, false );
			}
			?>
		</div>
		<?php if ( $args['content'] ) : ?>
			<div class="hm-split-panel">
				<?php echo wpautop( imob_section_html( $args['content'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $args['quote'] ) : ?>
		<p class="hm-quote-pill">
			&ldquo;<?php echo esc_html( trim( $args['quote'], " \"“”" ) ); ?>&rdquo;
			<?php if ( $args['quote_ref'] ) : ?>
				<strong><?php echo esc_html( $args['quote_ref'] ); ?></strong>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if ( $args['divider'] ) : ?>
		<span class="hm-divider hm-split-divider" aria-hidden="true"></span>
	<?php endif; ?>
</section>
