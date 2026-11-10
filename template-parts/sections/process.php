<?php
/**
 * Seção: etapas do processo de compra.
 *
 * @package ImobiliariaTema
 * @var array $args
 */
?>
<section class="hm-section hm-section--light">
	<div class="hm-container">
		<?php imob_section_head( $args['title'], $args['text'] ); ?>
		<?php if ( $args['steps'] ) : ?>
			<ol class="hm-steps" style="--hm-steps: <?php echo (int) min( 5, max( 1, count( $args['steps'] ) ) ); ?>">
				<?php foreach ( $args['steps'] as $i => $step ) : ?>
					<li class="hm-step">
						<div class="hm-step-media">
							<?php echo imob_section_image( $step['image'], 'medium', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</div>
						<div class="hm-step-body">
							<span class="hm-step-number"><?php echo (int) $i + 1; ?></span>
							<?php if ( $step['label'] ) : ?>
								<span class="hm-step-label"><?php echo esc_html( $step['label'] ); ?></span>
							<?php endif; ?>
							<h3 class="hm-step-title"><?php echo esc_html( $step['title'] ); ?></h3>
							<p class="hm-step-text"><?php echo esc_html( $step['text'] ); ?></p>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
