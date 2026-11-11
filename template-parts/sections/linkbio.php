<?php
/**
 * Seção: página de links estilo Linktree (link da bio do Instagram).
 *
 * Carrega automaticamente os últimos imóveis e posts, o WhatsApp e a landing da Black Friday.
 *
 * @package ImobiliariaTema
 * @var array $args
 */

wp_enqueue_style( 'imob-linkbio' );

/**
 * Acrescenta os parâmetros UTM aos links internos.
 */
$lb_url = function ( $url, $content = '' ) use ( $args ) {
	$url = imob_section_url( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
		$url = home_url( $url );
	}
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( $args['utm'] && false !== strpos( $url, (string) $host ) ) {
		$url = add_query_arg(
			array(
				'utm_source'   => 'instagram',
				'utm_medium'   => 'linkbio',
				'utm_campaign' => 'bio',
				'utm_content'  => $content ? sanitize_title( $content ) : null,
			),
			$url
		);
	}
	return $url;
};

$lb_ext = function ( $url ) {
	return ( 0 === strpos( $url, 'http' ) && false === strpos( $url, wp_parse_url( home_url(), PHP_URL_HOST ) ) ) ? ' target="_blank" rel="noopener noreferrer"' : '';
};

// WhatsApp com mensagem própria da bio.
$wa_num = get_option( 'imob_contact_whatsapp' ) ? get_option( 'imob_contact_whatsapp' ) : get_option( 'imob_contact_phone' );
$wa_num = preg_replace( '/\D/', '', (string) $wa_num );
if ( strlen( $wa_num ) === 10 || strlen( $wa_num ) === 11 ) {
	$wa_num = '55' . $wa_num;
}
$wa_url = 'https://api.whatsapp.com/send?' . ( $wa_num ? 'phone=' . $wa_num . '&' : '' ) . 'text=' . rawurlencode( $args['wa_message'] ? $args['wa_message'] : 'Olá! Vim pelo Instagram.' );
$wa_svg = function_exists( 'imob_home_whatsapp_icon' ) ? str_replace( 'width="16" height="16"', 'width="22" height="22"', imob_home_whatsapp_icon() ) : '';

// Black Friday: aparece só durante a campanha e se a página existir.
$bf_page = get_page_by_path( 'black-friday' );
$bf_end  = function_exists( 'imob_bf_timestamp' ) ? imob_bf_timestamp( 'end' ) : 0;
$bf_on   = $args['bf_show'] && $bf_page && 'publish' === get_post_status( $bf_page ) && ( ! $bf_end || time() <= $bf_end );
$bf_days = $bf_end ? max( 0, (int) ceil( ( $bf_end - time() ) / DAY_IN_SECONDS ) ) : 0;

// Imagem do topo.
// Foto enviada no widget; sem foto, usa o logo do site.
$avatar  = imob_section_image( $args['avatar'], 'medium', array( 'class' => 'lb-avatar-img', 'alt' => $args['name'], 'loading' => 'eager' ) );
$logo_id = $avatar ? 0 : (int) get_theme_mod( 'custom_logo' );

// Últimos imóveis e posts.
$imoveis = $args['imoveis_count'] > 0 ? imob_sections_property_query( $args['imoveis_tipo'], min( 12, (int) $args['imoveis_count'] ) ) : null;
$posts   = $args['posts_count'] > 0 ? get_posts(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => min( 10, (int) $args['posts_count'] ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
) : array();
$archive = function_exists( 'imob_sections_archive_url' ) ? imob_sections_archive_url() : home_url( '/imoveis/' );
?>
<section class="lb">
	<div class="lb-inner">

		<header class="lb-profile">
			<?php if ( $avatar ) : ?>
				<span class="lb-avatar"><?php echo $avatar; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<?php endif; ?>
			<?php if ( $logo_id ) : ?>
				<?php echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'lb-logo', 'alt' => get_bloginfo( 'name' ), 'loading' => 'eager' ) ); ?>
			<?php endif; ?>
			<h1 class="lb-name"><?php echo esc_html( $args['name'] ); ?> <span class="lb-verified material-symbols-outlined" aria-label="<?php esc_attr_e( 'Corretor credenciado', 'imobiliaria-tema' ); ?>">verified</span></h1>
			<?php if ( $args['bio'] ) : ?>
				<p class="lb-bio"><?php echo esc_html( $args['bio'] ); ?></p>
			<?php endif; ?>
			<?php
			if ( $args['show_social'] && function_exists( 'imob_social_icons_html' ) ) {
				echo str_replace( 'footer-social-icons', 'footer-social-icons lb-social', imob_social_icons_html( array( 'facebook', 'instagram' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		</header>

		<a class="lb-btn lb-btn--wa" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="lb-btn-icon"><?php echo $wa_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="lb-btn-text"><?php echo esc_html( $args['wa_text'] ); ?><small><?php esc_html_e( 'Resposta rápida, sem compromisso', 'imobiliaria-tema' ); ?></small></span>
			<span class="lb-btn-arrow material-symbols-outlined" aria-hidden="true">arrow_forward</span>
		</a>

		<?php if ( $bf_on ) : ?>
			<a class="lb-btn lb-btn--bf" href="<?php echo esc_url( $lb_url( get_permalink( $bf_page ), 'black-friday' ) ); ?>">
				<span class="lb-btn-icon material-symbols-outlined" aria-hidden="true">local_offer</span>
				<span class="lb-btn-text"><?php echo esc_html( $args['bf_text'] ); ?>
					<small>
						<?php
						if ( $bf_days > 1 ) {
							/* translators: %d: dias restantes. */
							printf( esc_html__( 'Termina em %d dias · todos os imóveis em oferta', 'imobiliaria-tema' ), (int) $bf_days );
						} else {
							esc_html_e( 'Últimas horas · todos os imóveis em oferta', 'imobiliaria-tema' );
						}
						?>
					</small>
				</span>
				<span class="lb-btn-arrow material-symbols-outlined" aria-hidden="true">arrow_forward</span>
			</a>
		<?php endif; ?>

		<?php if ( $imoveis && $imoveis->have_posts() ) : ?>
			<h2 class="lb-title"><span class="material-symbols-outlined" aria-hidden="true">home</span><?php echo esc_html( $args['imoveis_title'] ); ?></h2>
			<div class="lb-cards">
				<?php
				while ( $imoveis->have_posts() ) :
					$imoveis->the_post();
					$pid    = get_the_ID();
					$preco  = (float) get_post_meta( $pid, '_imob_preco_venda', true );
					$bairro = function_exists( 'imob_get_localidade_name' ) ? imob_get_localidade_name( $pid ) : '';
					$quartos = get_post_meta( $pid, '_imob_quartos', true );
					$is_new = ( time() - get_post_time( 'U', true ) ) < 15 * DAY_IN_SECONDS;
					?>
					<a class="lb-card" href="<?php echo esc_url( $lb_url( get_permalink(), 'imovel-' . $pid ) ); ?>">
						<span class="lb-card-thumb">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'imob_thumb', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) );
							}
							?>
							<?php if ( $is_new ) : ?>
								<span class="lb-badge"><?php esc_html_e( 'Novo', 'imobiliaria-tema' ); ?></span>
							<?php endif; ?>
						</span>
						<span class="lb-card-body">
							<strong class="lb-card-title"><?php the_title(); ?></strong>
							<span class="lb-card-meta">
								<?php if ( $bairro ) : ?>
									<span><span class="material-symbols-outlined" aria-hidden="true">location_on</span><?php echo esc_html( $bairro ); ?></span>
								<?php endif; ?>
								<?php if ( $quartos ) : ?>
									<span><span class="material-symbols-outlined" aria-hidden="true">bed</span><?php echo esc_html( $quartos ); ?></span>
								<?php endif; ?>
							</span>
							<?php if ( $preco ) : ?>
								<span class="lb-card-price">R$ <?php echo esc_html( number_format( $preco, 0, ',', '.' ) ); ?></span>
							<?php endif; ?>
						</span>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<?php if ( $args['all_text'] ) : ?>
				<a class="lb-btn lb-btn--outline" href="<?php echo esc_url( $lb_url( $archive, 'todos-imoveis' ) ); ?>">
					<span class="lb-btn-icon material-symbols-outlined" aria-hidden="true">search</span>
					<span class="lb-btn-text"><?php echo esc_html( $args['all_text'] ); ?></span>
					<span class="lb-btn-arrow material-symbols-outlined" aria-hidden="true">arrow_forward</span>
				</a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( $args['links'] ) : ?>
			<div class="lb-links">
				<?php
				foreach ( $args['links'] as $link ) :
					$u = $lb_url( $link['url'], $link['text'] );
					if ( ! $u || ! $link['text'] ) {
						continue;
					}
					?>
					<a class="lb-btn" href="<?php echo esc_url( $u ); ?>"<?php echo $lb_ext( $u ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
						<span class="lb-btn-icon material-symbols-outlined" aria-hidden="true"><?php echo esc_html( $link['icon'] ? $link['icon'] : 'link' ); ?></span>
						<span class="lb-btn-text"><?php echo esc_html( $link['text'] ); ?></span>
						<span class="lb-btn-arrow material-symbols-outlined" aria-hidden="true">arrow_forward</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $posts ) : ?>
			<h2 class="lb-title"><span class="material-symbols-outlined" aria-hidden="true">article</span><?php echo esc_html( $args['posts_title'] ); ?></h2>
			<div class="lb-posts">
				<?php foreach ( $posts as $post_item ) : ?>
					<a class="lb-post" href="<?php echo esc_url( $lb_url( get_permalink( $post_item ), 'post-' . $post_item->ID ) ); ?>">
						<span class="lb-post-thumb">
							<?php
							if ( has_post_thumbnail( $post_item ) ) {
								echo get_the_post_thumbnail( $post_item, 'imob_thumb', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $post_item ) ) ) );
							}
							?>
						</span>
						<span class="lb-post-body">
							<strong><?php echo esc_html( get_the_title( $post_item ) ); ?></strong>
							<small><?php echo esc_html( get_the_date( 'j \d\e F', $post_item ) ); ?></small>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<footer class="lb-footer">
			<a href="<?php echo esc_url( $lb_url( home_url( '/' ), 'site' ) ); ?>"><?php echo esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></a>
			<?php if ( $args['footer_text'] ) : ?>
				<span><?php echo esc_html( $args['footer_text'] ); ?></span>
			<?php endif; ?>
		</footer>
	</div>

	<a class="lb-sticky" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Falar no WhatsApp', 'imobiliaria-tema' ); ?>"><?php echo $wa_svg; // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Falar agora no WhatsApp', 'imobiliaria-tema' ); ?></span></a>
	<script>
	( function () {
		var bar = document.currentScript.previousElementSibling, btn = document.querySelector( '.lb-btn--wa' );
		if ( ! bar || ! btn || ! ( 'IntersectionObserver' in window ) ) { return; }
		new IntersectionObserver( function ( entries ) {
			bar.classList.toggle( 'is-visible', ! entries[0].isIntersecting && entries[0].boundingClientRect.top < 0 );
		} ).observe( btn );
	}() );
	</script>
</section>
