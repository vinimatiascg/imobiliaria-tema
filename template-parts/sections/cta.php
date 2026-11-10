<?php
/**
 * Seção: chamada para contato.
 *
 * @package ImobiliariaTema
 * @var array $args
 */
?>
<section class="hm-cta-section">
	<div class="hm-container">
		<div class="hm-cta">
			<span class="hm-hero-line hm-hero-line--1" aria-hidden="true"></span>
			<div class="hm-cta-content">
				<?php if ( $args['eyebrow'] ) : ?>
					<p class="hm-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
				<?php endif; ?>
				<?php if ( $args['title'] ) : ?>
					<h2 class="hm-cta-title"><?php echo esc_html( $args['title'] ); ?></h2>
				<?php endif; ?>
				<?php if ( $args['content'] ) : ?>
					<div class="hm-cta-text"><?php echo wpautop( imob_section_html( $args['content'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php endif; ?>
			</div>
			<?php
			$cta_args = $args;
			if ( empty( $cta_args['btn1_url'] ) ) {
				$cta_args['btn1_url'] = '{whatsapp}';
			}
			?>
			<div class="hm-cta-actions">
				<?php imob_section_buttons( $cta_args, true ); ?>
			</div>
		</div>
	</div>
</section>
