<?php
/**
 * Hero de oferta (mesmo layout da landing page da Black Friday).
 *
 * Usado pela landing da Black Friday e pela seção/widget "Topo (hero)" no layout "Oferta".
 *
 * @package ImobiliariaTema
 * @var array $args {
 *     @type string $tag         Texto da etiqueta superior.
 *     @type string $title_big   Título grande (H1).
 *     @type string $title_sub   Subtítulo (dentro do H1).
 *     @type string $text        Texto (HTML simples permitido).
 *     @type string $btn1_text   Botão principal.
 *     @type string $btn1_url    Link do botão principal.
 *     @type string $btn2_text   Botão secundário.
 *     @type string $btn2_url    Link do botão secundário.
 *     @type string $period      Texto do período (opcional).
 *     @type string $card_label  Quadro: etiqueta.
 *     @type string $card_prefix Quadro: texto acima do valor.
 *     @type string $card_value  Quadro: valor em destaque.
 *     @type string $card_text   Quadro: texto abaixo do valor.
 *     @type int    $countdown   Timestamp do fim da contagem (0 = sem contagem).
 *     @type bool   $is_over     Campanha encerrada.
 * }
 */

$args = wp_parse_args(
	$args,
	array(
		'tag'         => '',
		'title_big'   => '',
		'title_sub'   => '',
		'text'        => '',
		'btn1_text'   => '',
		'btn1_url'    => '',
		'btn2_text'   => '',
		'btn2_url'    => '',
		'period'      => '',
		'card_label'  => '',
		'card_prefix' => '',
		'card_value'  => '',
		'card_text'   => '',
		'countdown'   => 0,
		'is_over'     => false,
	)
);

$is_wa = function ( $url ) {
	return ( false !== strpos( $url, 'whatsapp' ) || false !== strpos( $url, 'wa.me' ) );
};
$ext = function ( $url ) {
	return ( 0 === strpos( $url, 'http' ) && false === strpos( $url, wp_parse_url( home_url(), PHP_URL_HOST ) ) ) ? ' target="_blank" rel="noopener noreferrer"' : '';
};
$has_card = $args['card_value'] || $args['card_label'] || $args['countdown'];

if ( $args['countdown'] && ! $args['is_over'] ) {
	wp_enqueue_script( 'imob-hero-offer' );
}
?>
<section class="bf-hero<?php echo $has_card ? '' : ' bf-hero--no-card'; ?>">
	<div class="hm-container bf-hero-inner">
		<div class="bf-hero-content">
			<?php if ( $args['tag'] ) : ?>
				<p class="bf-tag"><span class="material-symbols-outlined" aria-hidden="true">local_offer</span><?php echo esc_html( $args['tag'] ); ?></p>
			<?php endif; ?>
			<h1 class="bf-hero-title">
				<?php if ( $args['title_big'] ) : ?>
					<span class="bf-hero-title-big"><?php echo esc_html( $args['title_big'] ); ?></span>
				<?php endif; ?>
				<?php if ( $args['title_sub'] ) : ?>
					<span class="bf-hero-title-sub"><?php echo esc_html( $args['title_sub'] ); ?></span>
				<?php endif; ?>
			</h1>
			<?php if ( $args['text'] ) : ?>
				<p class="bf-hero-text"><?php echo wp_kses( $args['text'], array( 'strong' => array(), 'b' => array(), 'em' => array(), 'br' => array(), 'a' => array( 'href' => array() ) ) ); ?></p>
			<?php endif; ?>

			<?php if ( $args['btn1_text'] || $args['btn2_text'] ) : ?>
				<div class="hm-actions">
					<?php if ( $args['btn1_text'] ) : ?>
						<a class="hm-btn bf-btn-primary" href="<?php echo esc_url( $args['btn1_url'] ); ?>"<?php echo $ext( $args['btn1_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $is_wa( $args['btn1_url'] ) ? imob_home_whatsapp_icon() : '<span class="material-symbols-outlined">local_offer</span>'; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $args['btn1_text'] ); ?></a>
					<?php endif; ?>
					<?php if ( $args['btn2_text'] ) : ?>
						<a class="hm-btn hm-btn--ghost" href="<?php echo esc_url( $args['btn2_url'] ); ?>"<?php echo $ext( $args['btn2_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo $is_wa( $args['btn2_url'] ) ? imob_home_whatsapp_icon() : '<span class="material-symbols-outlined">home</span>'; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $args['btn2_text'] ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $args['period'] ) : ?>
				<p class="bf-period"><span class="material-symbols-outlined" aria-hidden="true">event</span><?php echo esc_html( $args['period'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $has_card ) : ?>
			<div class="bf-hero-card">
				<?php if ( $args['card_label'] ) : ?>
					<p class="bf-hero-card-label"><?php echo esc_html( $args['card_label'] ); ?></p>
				<?php endif; ?>
				<?php if ( $args['card_value'] ) : ?>
					<p class="bf-hero-card-value">
						<?php if ( $args['card_prefix'] ) : ?>
							<small><?php echo esc_html( $args['card_prefix'] ); ?></small>
						<?php endif; ?>
						<?php echo esc_html( $args['card_value'] ); ?>
					</p>
				<?php endif; ?>
				<?php if ( $args['card_text'] ) : ?>
					<p class="bf-hero-card-text"><?php echo esc_html( $args['card_text'] ); ?></p>
				<?php endif; ?>

				<?php if ( $args['countdown'] && ! $args['is_over'] ) : ?>
					<div class="bf-countdown" data-bf-countdown="<?php echo esc_attr( (int) $args['countdown'] ); ?>" aria-live="polite">
						<p class="bf-countdown-title"><?php esc_html_e( 'A oferta termina em', 'imobiliaria-tema' ); ?></p>
						<div class="bf-countdown-grid">
							<span><strong data-bf-unit="d">--</strong><?php esc_html_e( 'dias', 'imobiliaria-tema' ); ?></span>
							<span><strong data-bf-unit="h">--</strong><?php esc_html_e( 'horas', 'imobiliaria-tema' ); ?></span>
							<span><strong data-bf-unit="m">--</strong><?php esc_html_e( 'min', 'imobiliaria-tema' ); ?></span>
							<span><strong data-bf-unit="s">--</strong><?php esc_html_e( 'seg', 'imobiliaria-tema' ); ?></span>
						</div>
					</div>
				<?php elseif ( $args['countdown'] && $args['is_over'] ) : ?>
					<p class="bf-countdown-over"><?php esc_html_e( 'A campanha foi encerrada. Fale conosco e veja as condições especiais vigentes.', 'imobiliaria-tema' ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
