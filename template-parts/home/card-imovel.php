<?php
/**
 * Card de imóvel no estilo da home (layout Figma): foto em cima, base azul-marinho.
 * Usar dentro do loop.
 *
 * @package ImobiliariaTema
 */

$post_id    = get_the_ID();
$quartos    = get_post_meta( $post_id, '_imob_quartos', true );
$banheiros  = get_post_meta( $post_id, '_imob_banheiros', true );
$area       = get_post_meta( $post_id, '_imob_area_privativa', true );
$price_html = imob_get_formatted_price( $post_id, false );

$tipos       = wp_get_post_terms( $post_id, 'tipo_imovel', array( 'fields' => 'names' ) );
$status_list = wp_get_post_terms( $post_id, 'status_imovel', array( 'fields' => 'names' ) );
$localidades = wp_get_post_terms( $post_id, 'localidade', array( 'fields' => 'names' ) );

$tipo       = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : __( 'Imóvel', 'imobiliaria-tema' );
$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : __( 'Venda', 'imobiliaria-tema' );
$bairro     = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : '';
$emp_info   = imob_get_imovel_empreendimento( $post_id );
?>
<article <?php post_class( 'hm-card' ); ?>>
	<a class="hm-card-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) );
		} else {
			echo '<span class="hm-card-placeholder"></span>';
		}
		?>
		<span class="hm-card-badges">
			<span class="hm-badge hm-badge--outline"><?php echo esc_html( imob_strtoupper( $tipo ) ); ?></span>
			<span class="hm-badge hm-badge--solid"><?php echo esc_html( imob_strtoupper( $finalidade ) ); ?></span>
		</span>
		<span class="hm-card-cta"><?php esc_html_e( 'Ver detalhes', 'imobiliaria-tema' ); ?> <span class="material-symbols-outlined">arrow_forward</span></span>
	</a>

	<div class="hm-card-body">
		<div class="hm-card-meta">
			<?php if ( $bairro ) : ?>
				<span class="hm-card-bairro"><?php echo esc_html( sprintf( __( 'Bairro: %s', 'imobiliaria-tema' ), $bairro ) ); ?></span>
			<?php endif; ?>
			<?php if ( $price_html ) : ?>
				<span class="hm-card-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML montado e escapado em imob_get_formatted_price(). ?></span>
			<?php endif; ?>
		</div>

		<span class="hm-divider hm-divider--small" aria-hidden="true"></span>

		<h3 class="hm-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<div class="hm-card-row">
			<?php if ( $emp_info && ! empty( $emp_info['nome'] ) ) : ?>
				<span class="hm-card-residencial">
					<?php esc_html_e( 'Residencial:', 'imobiliaria-tema' ); ?>
					<strong><?php echo esc_html( $emp_info['nome'] ); ?></strong>
				</span>
			<?php else : ?>
				<span></span>
			<?php endif; ?>

			<span class="hm-card-features">
				<?php if ( $quartos ) : ?>
					<span title="<?php esc_attr_e( 'Quartos', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">bed</span><?php echo esc_html( $quartos ); ?></span>
				<?php endif; ?>
				<?php if ( $banheiros ) : ?>
					<span title="<?php esc_attr_e( 'Banheiros', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">shower</span><?php echo esc_html( $banheiros ); ?></span>
				<?php endif; ?>
				<?php if ( $area ) : ?>
					<span title="<?php esc_attr_e( 'Área privativa', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">crop</span><?php echo esc_html( $area ); ?>m²</span>
				<?php endif; ?>
			</span>
		</div>

		<p class="hm-card-date"><?php esc_html_e( 'Data do anúncio:', 'imobiliaria-tema' ); ?> <strong><?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?></strong></p>
	</div>
</article>
