<?php
/**
 * Template Name: Listagem de Imóveis (Filtros Avançados)
 *
 * @package ImobiliariaTema
 */

get_header(); 

// Parâmetros selecionados
$tipo_sel       = isset( $_GET['tipo'] ) ? sanitize_text_field( $_GET['tipo'] ) : '';
$localidade_sel = isset( $_GET['localidade'] ) ? sanitize_text_field( $_GET['localidade'] ) : ( isset( $_GET['bairro'] ) ? sanitize_text_field( $_GET['bairro'] ) : '' );
$finalidade_sel = isset( $_GET['finalidade'] ) ? sanitize_text_field( $_GET['finalidade'] ) : '';
$estagio_sel    = isset( $_GET['estagio'] ) ? sanitize_text_field( $_GET['estagio'] ) : '';
$has_filter     = ! empty( $tipo_sel ) || ! empty( $localidade_sel ) || ! empty( $finalidade_sel ) || ! empty( $estagio_sel );

// Termos para os filtros
$termos_tipo = get_terms( array(
	'taxonomy'   => 'tipo_imovel',
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
) );

$termos_localidade = get_terms( array(
	'taxonomy'   => 'localidade',
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
) );

$termos_estagio = get_terms( array(
	'taxonomy'   => 'estagio_obra',
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
) );

// Construção da query de imóveis
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

$args = array(
	'post_type'      => 'imovel',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => $paged,
);

$tax_query = array();
if ( ! empty( $tipo_sel ) ) {
	$tax_query[] = array(
		'taxonomy' => 'tipo_imovel',
		'field'    => 'slug',
		'terms'    => $tipo_sel,
	);
}

if ( ! empty( $localidade_sel ) ) {
	$tax_query[] = array(
		'taxonomy' => 'localidade',
		'field'    => 'slug',
		'terms'    => $localidade_sel,
	);
}

if ( ! empty( $estagio_sel ) ) {
	$tax_query[] = array(
		'taxonomy' => 'estagio_obra',
		'field'    => 'slug',
		'terms'    => $estagio_sel,
	);
}

if ( ! empty( $finalidade_sel ) ) {
	$fin_val = strtolower( $finalidade_sel );
	$tax_name = taxonomy_exists( 'finalidade' ) ? 'finalidade' : 'status_imovel';
	$tax_terms = ( 'venda' === $fin_val ) ? array( 'venda', 'para-venda', 'vender' ) : array( 'aluguel', 'para-alugar', 'locacao', 'alugar' );
	
	$existing_term = false;
	foreach ( $tax_terms as $tt ) {
		if ( term_exists( $tt, $tax_name ) ) {
			$existing_term = $tt;
			break;
		}
	}

	if ( $existing_term ) {
		$tax_query[] = array(
			'taxonomy' => $tax_name,
			'field'    => 'slug',
			'terms'    => $tax_terms,
		);
	} else {
		$price_key = ( 'venda' === $fin_val ) ? '_imob_preco_venda' : '_imob_preco_aluguel';
		$args['meta_query'] = array(
			array(
				'key'     => $price_key,
				'value'   => 0,
				'compare' => '>',
				'type'    => 'NUMERIC',
			)
		);
	}
}

if ( ! empty( $tax_query ) ) {
	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}
	$args['tax_query'] = $tax_query;
}

$imoveis_query = new WP_Query( $args );
$current_page_url = get_permalink();
?>

<main id="primary" class="site-main imob-archive-imovel">
	<div class="container" style="max-width: 1240px; margin: 0 auto; padding: 40px 15px;">
		
		<!-- CABEÇALHO -->
		<header class="page-header" style="margin-bottom: 25px; text-align: center;">
			<h1 class="page-title" style="font-size: 2.4rem; color: var(--primary-color); font-weight: 800; margin: 0 0 10px;">
				<?php the_title(); ?>
			</h1>
			<p style="color: var(--text-light); max-width: 600px; margin: 0 auto; font-size: 1.05rem;">
				<?php _e( 'Explore as melhores opções de apartamentos, casas e empreendimentos selecionados com exclusividade para você.', 'imobiliaria-tema' ); ?>
			</p>
		</header>

		<!-- BARRA DE FILTROS SUPERIOR -->
		<div class="imob-archive-filters-bar" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 12px; padding: 25px; margin-bottom: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.04);">
			<form method="get" action="<?php echo esc_url( $current_page_url ); ?>" class="imob-filters-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) auto auto; gap: 15px; align-items: end;">
				
				<!-- FILTRO POR TIPO -->
				<div class="imob-filter-group" style="display: flex; flex-direction: column; gap: 6px;">
					<label for="filter-tipo" style="font-size: 0.85rem; font-weight: 700; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
						<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">home_work</span>
						<?php _e( 'Tipo de Imóvel', 'imobiliaria-tema' ); ?>
					</label>
					<select name="tipo" id="filter-tipo" class="imob-filter-select" style="width: 100%; height: 46px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-size: 0.95rem; background: #fafafa; color: var(--text-dark);">
						<option value=""><?php _e( 'Todos os Tipos', 'imobiliaria-tema' ); ?></option>
						<?php if ( ! empty( $termos_tipo ) && ! is_wp_error( $termos_tipo ) ) : ?>
							<?php foreach ( $termos_tipo as $t ) : ?>
								<option value="<?php echo esc_attr( $t->slug ); ?>" <?php selected( $tipo_sel, $t->slug ); ?>>
									<?php echo esc_html( $t->name ); ?> (<?php echo intval( $t->count ); ?>)
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<!-- FILTRO POR LOCALIDADE -->
				<div class="imob-filter-group" style="display: flex; flex-direction: column; gap: 6px;">
					<label for="filter-localidade" style="font-size: 0.85rem; font-weight: 700; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
						<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">location_on</span>
						<?php _e( 'Localidade / Bairro', 'imobiliaria-tema' ); ?>
					</label>
					<select name="localidade" id="filter-localidade" class="imob-filter-select" style="width: 100%; height: 46px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-size: 0.95rem; background: #fafafa; color: var(--text-dark);">
						<option value=""><?php _e( 'Todas as Localidades', 'imobiliaria-tema' ); ?></option>
						<?php if ( ! empty( $termos_localidade ) && ! is_wp_error( $termos_localidade ) ) : ?>
							<?php foreach ( $termos_localidade as $l ) : ?>
								<option value="<?php echo esc_attr( $l->slug ); ?>" <?php selected( $localidade_sel, $l->slug ); ?>>
									<?php echo esc_html( $l->name ); ?> (<?php echo intval( $l->count ); ?>)
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<!-- FILTRO POR MODALIDADE (VENDA OU ALUGUEL) -->
				<div class="imob-filter-group" style="display: flex; flex-direction: column; gap: 6px;">
					<label for="filter-finalidade" style="font-size: 0.85rem; font-weight: 700; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
						<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">sell</span>
						<?php _e( 'Modalidade', 'imobiliaria-tema' ); ?>
					</label>
					<select name="finalidade" id="filter-finalidade" class="imob-filter-select" style="width: 100%; height: 46px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-size: 0.95rem; background: #fafafa; color: var(--text-dark);">
						<option value=""><?php _e( 'Todas as Modalidades', 'imobiliaria-tema' ); ?></option>
						<option value="venda" <?php selected( $finalidade_sel, 'venda' ); ?>><?php _e( 'Comprar (Venda)', 'imobiliaria-tema' ); ?></option>
						<option value="aluguel" <?php selected( $finalidade_sel, 'aluguel' ); ?>><?php _e( 'Alugar (Aluguel)', 'imobiliaria-tema' ); ?></option>
					</select>
				</div>

				<!-- FILTRO POR ESTÁGIO DA OBRA -->
				<div class="imob-filter-group" style="display: flex; flex-direction: column; gap: 6px;">
					<label for="filter-estagio" style="font-size: 0.85rem; font-weight: 700; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
						<span class="material-symbols-outlined" style="font-size: 16px; color: var(--accent-color);">construction</span>
						<?php _e( 'Estágio da Obra', 'imobiliaria-tema' ); ?>
					</label>
					<select name="estagio" id="filter-estagio" class="imob-filter-select" style="width: 100%; height: 46px; border: 1px solid var(--border-color); border-radius: 8px; padding: 0 12px; font-size: 0.95rem; background: #fafafa; color: var(--text-dark);">
						<option value=""><?php _e( 'Todos os Estágios', 'imobiliaria-tema' ); ?></option>
						<?php if ( ! empty( $termos_estagio ) && ! is_wp_error( $termos_estagio ) ) : ?>
							<?php foreach ( $termos_estagio as $e ) : ?>
								<option value="<?php echo esc_attr( $e->slug ); ?>" <?php selected( $estagio_sel, $e->slug ); ?>>
									<?php echo esc_html( $e->name ); ?> (<?php echo intval( $e->count ); ?>)
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<!-- BOTÃO FILTRAR -->
				<div style="display: flex; gap: 8px;">
					<button type="submit" class="imob-btn-filtrar" style="height: 46px; background: var(--accent-color); color: #ffffff; border: none; border-radius: 8px; padding: 0 24px; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s ease;">
						<span class="material-symbols-outlined" style="font-size: 20px;">filter_alt</span>
						<?php _e( 'Filtrar', 'imobiliaria-tema' ); ?>
					</button>

					<!-- BOTÃO LIMPAR -->
					<?php if ( $has_filter ) : ?>
						<a href="<?php echo esc_url( $current_page_url ); ?>" class="imob-btn-limpar" title="<?php esc_attr_e( 'Limpar todos os filtros', 'imobiliaria-tema' ); ?>" style="height: 46px; background: #f1f5f9; color: var(--text-light); border: 1px solid var(--border-color); border-radius: 8px; padding: 0 16px; font-weight: 600; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
							<span class="material-symbols-outlined" style="font-size: 18px;">restart_alt</span>
							<?php _e( 'Limpar', 'imobiliaria-tema' ); ?>
						</a>
					<?php endif; ?>
				</div>

			</form>
		</div>

		<!-- BARRA DE RESULTADOS -->
		<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
			<div style="color: var(--text-light); font-size: 0.95rem; font-weight: 500;">
				<?php 
				$total = $imoveis_query->found_posts;
				printf( _n( 'Encontrado <strong>%s imóvel</strong>', 'Encontrados <strong>%s imóveis</strong>', $total, 'imobiliaria-tema' ), number_format_i18n( $total ) );
				if ( $has_filter ) {
					echo ' <span style="color: var(--accent-color); font-weight: 600;">(com filtros aplicados)</span>';
				}
				?>
			</div>
		</div>

		<!-- GRID DE IMÓVEIS -->
		<div class="imob-imovel-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
			<?php
			if ( $imoveis_query->have_posts() ) :
				while ( $imoveis_query->have_posts() ) :
					$imoveis_query->the_post();
					$price_html = imob_get_formatted_price( get_the_ID(), false );
					$quartos = get_post_meta( get_the_ID(), '_imob_quartos', true );
					$banheiros = get_post_meta( get_the_ID(), '_imob_banheiros', true );
					$area = get_post_meta( get_the_ID(), '_imob_area_privativa', true );

					$tipos = wp_get_post_terms( get_the_ID(), 'tipo_imovel', array( 'fields' => 'names' ) );
					$status_list = wp_get_post_terms( get_the_ID(), 'status_imovel', array( 'fields' => 'names' ) );
					$localidades = wp_get_post_terms( get_the_ID(), 'localidade', array( 'fields' => 'names' ) );

					$tipo = ( ! is_wp_error( $tipos ) && ! empty( $tipos ) ) ? $tipos[0] : 'IMÓVEL';
					$finalidade = ( ! is_wp_error( $status_list ) && ! empty( $status_list ) ) ? $status_list[0] : 'VENDA';
					$bairro = ( ! is_wp_error( $localidades ) && ! empty( $localidades ) ) ? $localidades[0] : '';
					
					// Dados do Empreendimento vinculado (Requisito 4)
					$badge_emp_html = imob_render_empreendimento_badge( get_the_ID() );
					$emp_info = imob_get_imovel_empreendimento( get_the_ID() );
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-card' ); ?>>
						
						<!-- THUMBNAIL -->
						<div class="imob-card-thumb">
							<div class="imob-card-badges">
								<span class="badge-tipo"><?php echo esc_html( imob_strtoupper( $tipo ) ); ?></span>
								<span class="badge-finalidade"><?php echo esc_html( imob_strtoupper( $finalidade ) ); ?></span>
								<?php if ( ! empty( $badge_emp_html ) ) : ?>
									<?php echo $badge_emp_html; ?>
								<?php endif; ?>
							</div>

							<?php if ( $price_html ) : ?>
								<div class="imob-card-price">
									<?php echo $price_html; ?>
								</div>
							<?php endif; ?>

							<a href="<?php the_permalink(); ?>">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'medium_large' );
								} else {
									echo '<div class="imob-card-placeholder"></div>';
								}
								?>
							</a>
						</div>

						<!-- CONTEÚDO -->
						<div class="imob-card-content">
							<?php if ( $bairro ) : ?>
								<div class="imob-card-info-top">
									<span class="info-bairro">
										<span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle; color: var(--accent-color);">location_on</span> 
										Bairro: <?php echo esc_html( $bairro ); ?>
									</span>
								</div>
							<?php endif; ?>

							<?php the_title( '<h3 class="imob-card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
							
							<?php if ( $emp_info && ! empty( $emp_info['nome'] ) ) : ?>
								<p class="imob-card-residencial">
									Empreendimento: 
									<?php if ( ! empty( $emp_info['url'] ) ) : ?>
										<a href="<?php echo esc_url( $emp_info['url'] ); ?>" style="color: var(--primary-color); font-weight: 700; text-decoration: none;">
											<?php echo esc_html( $emp_info['nome'] ); ?>
										</a>
									<?php else : ?>
										<strong><?php echo esc_html( $emp_info['nome'] ); ?></strong>
									<?php endif; ?>
								</p>
							<?php endif; ?>

							<div class="imob-card-features">
								<?php if ( $quartos ) : ?>
									<span title="Quartos"><span class="material-symbols-outlined">bed</span> <?php echo esc_html( $quartos ); ?></span>
								<?php endif; ?>
								<?php if ( $banheiros ) : ?>
									<span title="Banheiros"><span class="material-symbols-outlined">shower</span> <?php echo esc_html( $banheiros ); ?></span>
								<?php endif; ?>
								<?php if ( $area ) : ?>
									<span title="Área Privativa"><span class="material-symbols-outlined">crop</span> <?php echo esc_html( $area ); ?>m²</span>
								<?php endif; ?>
							</div>
							
							<div class="imob-card-footer">
								<span class="imob-card-date">Data do anúncio: <?php echo get_the_date('j \d\e F \d\e Y'); ?></span>
							</div>
						</div>

					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<div style="grid-column: 1 / -1; background: #ffffff; padding: 60px 20px; text-align: center; border-radius: 8px; border: 1px solid var(--border-color);">
					<span class="material-symbols-outlined" style="font-size: 54px; color: var(--text-light); margin-bottom: 15px;">search_off</span>
					<h3 style="margin: 0 0 10px; color: var(--primary-color);"><?php _e( 'Nenhum imóvel encontrado', 'imobiliaria-tema' ); ?></h3>
					<p style="color: var(--text-light); margin: 0 0 20px;"><?php _e( 'Tente alterar os filtros selecionados para encontrar outras opções.', 'imobiliaria-tema' ); ?></p>
					<?php if ( $has_filter ) : ?>
						<a href="<?php echo esc_url( $current_page_url ); ?>" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px; background: var(--accent-color); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 700;">
							<span class="material-symbols-outlined">restart_alt</span> <?php _e( 'Limpar Filtros', 'imobiliaria-tema' ); ?>
						</a>
					<?php endif; ?>
				</div>
				<?php
			endif;
			?>
		</div>

		<!-- PAGINAÇÃO PRESERVANDO FILTROS -->
		<div class="imob-pagination" style="margin-top: 50px; display: flex; justify-content: center; gap: 8px;">
			<?php
			$big = 999999999;
			echo paginate_links( array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, $paged ),
				'total'     => $imoveis_query->max_num_pages,
				'prev_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_left</span>',
				'next_text' => '<span class="material-symbols-outlined" style="vertical-align: middle;">chevron_right</span>',
				'type'      => 'list',
			) );
			?>
		</div>

	</div>
</main>

<?php
get_footer();
