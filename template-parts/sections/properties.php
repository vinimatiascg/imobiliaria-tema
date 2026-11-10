<?php
/**
 * Seção: grade de imóveis com paginação no navegador.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$query = imob_sections_property_query( $args['tipo'], $args['count'] ? $args['count'] : 24 );
if ( ! $query->have_posts() && $args['hide_empty'] ) {
	return;
}
$styles = array(
	'light' => 'hm-section--light',
	'dark'  => 'hm-section--dark',
	'split' => 'hm-section--split',
);
$class   = $styles[ $args['style'] ] ?? 'hm-section--light';
$grid_id = 'hm-grid-' . wp_unique_id();
$all_url = imob_section_url( $args['all_url'] ? $args['all_url'] : '{imoveis}' );
?>
<section class="hm-section <?php echo esc_attr( $class ); ?>">
	<div class="hm-container">
		<?php imob_section_head( $args['title'], $args['text'] ); ?>
		<?php if ( $query->have_posts() ) : ?>
			<div class="hm-grid hm-grid--3" id="<?php echo esc_attr( $grid_id ); ?>" data-hm-paginate="<?php echo (int) ( $args['per_page'] ? $args['per_page'] : 6 ); ?>">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					get_template_part( 'template-parts/home/card-imovel' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<nav class="hm-pager" data-hm-pager-for="<?php echo esc_attr( $grid_id ); ?>" aria-label="<?php esc_attr_e( 'Páginas', 'imobiliaria-tema' ); ?>">
				<span class="hm-pager-pages"></span>
				<?php if ( $args['all_text'] ) : ?>
					<a class="hm-btn hm-btn--gold hm-btn--sm" href="<?php echo esc_url( $all_url ); ?>"><?php echo esc_html( $args['all_text'] ); ?></a>
				<?php endif; ?>
			</nav>
		<?php else : ?>
			<p class="hm-empty"><?php esc_html_e( 'Nenhum imóvel encontrado para este filtro.', 'imobiliaria-tema' ); ?></p>
		<?php endif; ?>
	</div>
</section>
