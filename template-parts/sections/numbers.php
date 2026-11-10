<?php
/**
 * Seção: números da empresa.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$items = array_values(
	array_filter(
		$args['items'],
		function ( $item ) {
			return '' !== trim( $item['value'] );
		}
	)
);
?>
<section class="hm-section hm-section--dark hm-numbers-section<?php echo $args['tight_top'] ? ' hm-numbers-section--tight' : ''; ?>">
	<?php if ( $args['divider_top'] ) : ?>
		<span class="hm-divider hm-divider--on-dark" aria-hidden="true"></span>
	<?php endif; ?>
	<div class="hm-container">
		<?php imob_section_head( $args['title'], $args['text'] ); ?>
		<?php foreach ( array_chunk( $items, 3 ) as $row ) : ?>
			<div class="hm-numbers">
				<?php foreach ( $row as $item ) : ?>
					<div class="hm-number">
						<span class="hm-number-icon"><span class="material-symbols-outlined"><?php echo esc_html( $item['icon'] ); ?></span></span>
						<div>
							<strong class="hm-number-value"><?php echo esc_html( $item['value'] ); ?></strong>
							<span class="hm-number-label"><?php echo esc_html( $item['label'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
