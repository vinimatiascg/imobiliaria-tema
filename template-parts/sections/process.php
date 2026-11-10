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
			<?php
			$cols = isset( $args['columns'] ) ? (int) $args['columns'] : 5;
			$cols = in_array( $cols, array( 3, 4, 5 ), true ) ? $cols : ( $cols && $cols < 3 ? 3 : 5 );
			?>
			<ol class="hm-steps hm-steps--cols-<?php echo (int) $cols; ?>">
				<?php foreach ( $args['steps'] as $i => $step ) : ?>
					<li class="hm-step">
						<div class="hm-step-media">
							<?php
							$step_html = imob_section_image( $step['image'], 'medium_large', array( 'loading' => 'lazy' ) );
							if ( ! $step_html && imob_home_step_default_image( $i + 1 ) ) {
								// Sem imagem enviada: usa a ilustração do tema para a etapa.
								$step_html = sprintf(
									'<img src="%1$s" alt="%2$s" width="400" height="240" loading="lazy" decoding="async">',
									esc_url( imob_home_step_default_image( $i + 1 ) ),
									esc_attr( trim( $step['label'] . ' - ' . $step['title'], ' -' ) )
								);
							}
							echo $step_html; // phpcs:ignore WordPress.Security.EscapeOutput
							?>
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
