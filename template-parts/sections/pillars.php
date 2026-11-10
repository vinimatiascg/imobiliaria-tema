<?php
/**
 * Seção: blocos coloridos lado a lado (missão, visão, valores, listas de serviços...).
 *
 * @package ImobiliariaTema
 * @var array $args
 */

if ( ! $args['items'] ) {
	return;
}
?>
<section class="hm-pillars-section">
	<?php if ( $args['title'] || $args['text'] ) : ?>
		<div class="hm-container hm-pillars-head">
			<?php imob_section_head( $args['title'], $args['text'] ); ?>
		</div>
	<?php endif; ?>
	<div class="hm-container">
	<div class="hm-pillars hm-pillars--<?php echo (int) count( $args['items'] ); ?>">
		<?php foreach ( $args['items'] as $item ) : ?>
			<div class="hm-pillar hm-pillar--<?php echo esc_attr( $item['width'] ); ?> hm-pillar--<?php echo esc_attr( $item['tone'] ); ?>">
				<div class="hm-pillar-inner">
					<?php if ( $item['icon'] ) : ?>
						<span class="hm-pillar-icon material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $item['icon'] ); ?></span>
					<?php endif; ?>
					<?php if ( $item['title'] ) : ?>
						<h3 class="hm-pillar-title"><?php echo esc_html( $item['title'] ); ?></h3>
					<?php endif; ?>
					<div class="hm-pillar-text"><?php echo wpautop( imob_section_html( $item['content'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	</div>
</section>
