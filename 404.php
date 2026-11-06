<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package ImobiliariaTema
 */

get_header();
?>

<main id="primary" class="site-main imob-404-page">
	<div class="container imob-page-container">
		<section class="imob-404-card">
			<div class="imob-404-icon">
				<span class="material-symbols-outlined">travel_explore</span>
			</div>
			<span class="imob-404-code">404</span>
			<h1 class="imob-404-title"><?php _e( 'Página não encontrada', 'imobiliaria-tema' ); ?></h1>
			<p class="imob-404-desc">
				<?php _e( 'O endereço que você tentou acessar não existe ou foi movido. Utilize os atalhos abaixo para encontrar o imóvel ou conteúdo desejado.', 'imobiliaria-tema' ); ?>
			</p>
			<div class="imob-404-actions">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn-primary">
					<span class="material-symbols-outlined">home</span>
					<?php _e( 'Página Inicial', 'imobiliaria-tema' ); ?>
				</a>
				<a href="<?php echo esc_url( home_url( '/imoveis/' ) ); ?>" class="btn-primary imob-btn-accent">
					<span class="material-symbols-outlined">real_estate_agent</span>
					<?php _e( 'Ver Todos os Imóveis', 'imobiliaria-tema' ); ?>
				</a>
			</div>
		</section>
	</div>
</main>

<?php
get_footer();

