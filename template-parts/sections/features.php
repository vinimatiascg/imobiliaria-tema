<?php
/**
 * Seção: grade de diferenciais com ícones.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$dark = ( 'dark' === $args['style'] );
?>
<section class="hm-section hm-features-section <?php echo $dark ? 'hm-section--dark' : 'hm-section--soft'; ?>">
	<div class="hm-container">
		<?php if ( $args['eyebrow'] ) : ?>
			<p class="hm-eyebrow hm-eyebrow--center"><?php echo esc_html( $args['eyebrow'] ); ?></p>
		<?php endif; ?>
		<?php imob_section_head( $args['title'], $args['text'] ); ?>
		<div class="hm-features">
			<?php foreach ( $args['items'] as $item ) : ?>
				<div class="hm-feature">
					<span class="hm-feature-icon"><span class="material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $item['icon'] ); ?></span></span>
					<h3 class="hm-feature-title"><?php echo esc_html( $item['title'] ); ?></h3>
					<p class="hm-feature-text"><?php echo esc_html( $item['text'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
