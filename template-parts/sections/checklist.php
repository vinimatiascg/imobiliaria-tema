<?php
/**
 * Seção: texto à esquerda e lista com ícones à direita (ex.: trajetória e formação).
 *
 * @package ImobiliariaTema
 * @var array $args
 */
?>
<section class="hm-section hm-checklist-section">
	<div class="hm-container hm-checklist">
		<div class="hm-checklist-intro">
			<?php if ( $args['title'] ) : ?>
				<h2 class="hm-checklist-title"><?php echo esc_html( $args['title'] ); ?></h2>
			<?php endif; ?>
			<?php echo wpautop( imob_section_html( $args['content'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php if ( $args['items'] ) : ?>
			<ul class="hm-checklist-list">
				<?php foreach ( $args['items'] as $item ) : ?>
					<li>
						<span class="material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $args['icon'] ? $args['icon'] : 'check' ); ?></span>
						<span><?php echo esc_html( $item['text'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
