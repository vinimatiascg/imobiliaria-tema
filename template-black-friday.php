<?php
/**
 * Template Name: Landing Page - Black Friday
 * Template Post Type: page
 *
 * Landing page da Black Friday: todos os imóveis em oferta, desconto extra mínimo
 * em qualquer imóvel à venda, documentação grátis, descontos, brindes e
 * 1 ano de condomínio grátis nas casas em condomínio.
 *
 * Datas, valor do desconto e mensagem do WhatsApp: Personalizar > Black Friday.
 *
 * @package ImobiliariaTema
 */

get_header();

$bf_wa_url       = imob_bf_whatsapp_url();
$bf_discount     = imob_bf_mod( 'extra_discount' );
$bf_end_ts       = imob_bf_timestamp( 'end' );
$bf_start_ts     = imob_bf_timestamp( 'start' );
$bf_archive_url  = get_post_type_archive_link( 'imovel' );
$bf_archive_url  = $bf_archive_url ? $bf_archive_url : home_url( '/imoveis/' );
$bf_wa_icon      = imob_home_whatsapp_icon();
$bf_is_over      = $bf_end_ts && time() > $bf_end_ts;
$bf_period_label = ( $bf_start_ts && $bf_end_ts )
	? sprintf(
		/* translators: 1: data de início, 2: data de fim. */
		__( 'De %1$s a %2$s', 'imobiliaria-tema' ),
		wp_date( 'j \d\e F', $bf_start_ts ),
		wp_date( 'j \d\e F \d\e Y', $bf_end_ts )
	)
	: '';

/**
 * Consulta de imóveis à venda (exclui vendidos e alugados).
 *
 * @param string $tipo_slug Slug(s) do tipo de imóvel, separados por vírgula.
 * @param int    $count     Quantidade.
 * @return WP_Query
 */
$bf_query = function ( $tipo_slug = '', $count = 24 ) {
	$args = array(
		'post_type'           => 'imovel',
		'posts_per_page'      => $count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'status_imovel',
				'field'    => 'slug',
				'terms'    => array( 'vendido', 'alugado' ),
				'operator' => 'NOT IN',
			),
		),
	);
	if ( $tipo_slug ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'tipo_imovel',
			'field'    => 'slug',
			'terms'    => array_map( 'trim', explode( ',', $tipo_slug ) ),
		);
	}
	return new WP_Query( $args );
};

$bf_grid = function ( WP_Query $query, $id ) use ( $bf_archive_url ) {
	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<div class="hm-grid hm-grid--3 bf-grid" id="<?php echo esc_attr( $id ); ?>" data-hm-paginate="6">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			?>
			<div class="bf-card-wrap">
				<span class="bf-card-ribbon"><?php esc_html_e( 'Black Friday', 'imobiliaria-tema' ); ?></span>
				<?php get_template_part( 'template-parts/home/card-imovel' ); ?>
			</div>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<nav class="hm-pager" data-hm-pager-for="<?php echo esc_attr( $id ); ?>" aria-label="<?php esc_attr_e( 'Páginas', 'imobiliaria-tema' ); ?>">
		<span class="hm-pager-pages"></span>
		<a class="hm-btn hm-btn--gold hm-btn--sm" href="<?php echo esc_url( $bf_archive_url ); ?>"><?php esc_html_e( 'Ver todos os imóveis', 'imobiliaria-tema' ); ?></a>
	</nav>
	<?php
};
?>

<main id="primary" class="site-main hm bf">

	<?php while ( have_posts() ) : the_post(); ?>

	<!-- ============ HERO ============ -->
	<section class="bf-hero">
		<span class="bf-hero-glow" aria-hidden="true"></span>
		<div class="hm-container bf-hero-inner">
			<div class="bf-hero-content">
				<p class="bf-tag"><span class="material-symbols-outlined">local_offer</span><?php esc_html_e( 'Black Friday de Imóveis em Campina Grande', 'imobiliaria-tema' ); ?></p>
				<h1 class="bf-hero-title">
					<span class="bf-hero-title-big"><?php esc_html_e( 'Black Friday', 'imobiliaria-tema' ); ?></span>
					<span class="bf-hero-title-sub"><?php esc_html_e( 'Todos os imóveis entraram em oferta', 'imobiliaria-tema' ); ?></span>
				</h1>
				<p class="bf-hero-text">
					<?php
					printf(
						/* translators: %s: valor do desconto extra. */
						esc_html__( 'Descontos e brindes conforme o imóvel e, no mínimo, %s de desconto extra em qualquer imóvel à venda. Tem documentação grátis, descontos especiais e 1 ano de condomínio grátis nas casas em condomínio.', 'imobiliaria-tema' ),
						'<strong>' . esc_html( $bf_discount ) . '</strong>'
					);
					?>
				</p>
				<div class="hm-actions">
					<a class="hm-btn bf-btn-primary" href="<?php echo esc_url( $bf_wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $bf_wa_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG fixo. ?><?php esc_html_e( 'Quero aproveitar a oferta', 'imobiliaria-tema' ); ?></a>
					<a class="hm-btn hm-btn--ghost" href="#bf-imoveis"><span class="material-symbols-outlined">home</span><?php esc_html_e( 'Ver imóveis em oferta', 'imobiliaria-tema' ); ?></a>
				</div>
				<?php if ( $bf_period_label ) : ?>
					<p class="bf-period"><span class="material-symbols-outlined">event</span><?php echo esc_html( $bf_period_label ); ?></p>
				<?php endif; ?>
			</div>

			<div class="bf-hero-card">
				<p class="bf-hero-card-label"><?php esc_html_e( 'Desconto extra garantido', 'imobiliaria-tema' ); ?></p>
				<p class="bf-hero-card-value"><small><?php esc_html_e( 'mínimo de', 'imobiliaria-tema' ); ?></small><?php echo esc_html( $bf_discount ); ?></p>
				<p class="bf-hero-card-text"><?php esc_html_e( 'em qualquer imóvel à venda, somado às demais condições da campanha.', 'imobiliaria-tema' ); ?></p>

				<?php if ( $bf_end_ts && ! $bf_is_over ) : ?>
					<div class="bf-countdown" data-bf-countdown="<?php echo esc_attr( $bf_end_ts ); ?>" aria-live="polite">
						<p class="bf-countdown-title"><?php esc_html_e( 'A oferta termina em', 'imobiliaria-tema' ); ?></p>
						<div class="bf-countdown-grid">
							<span><strong data-bf-unit="d">--</strong><?php esc_html_e( 'dias', 'imobiliaria-tema' ); ?></span>
							<span><strong data-bf-unit="h">--</strong><?php esc_html_e( 'horas', 'imobiliaria-tema' ); ?></span>
							<span><strong data-bf-unit="m">--</strong><?php esc_html_e( 'min', 'imobiliaria-tema' ); ?></span>
							<span><strong data-bf-unit="s">--</strong><?php esc_html_e( 'seg', 'imobiliaria-tema' ); ?></span>
						</div>
					</div>
				<?php elseif ( $bf_is_over ) : ?>
					<p class="bf-countdown-over"><?php esc_html_e( 'A campanha foi encerrada. Fale conosco e veja as condições especiais vigentes.', 'imobiliaria-tema' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- ============ BENEFÍCIOS ============ -->
	<section class="hm-section bf-benefits">
		<div class="hm-container">
			<header class="hm-section-head">
				<p class="bf-kicker"><?php esc_html_e( 'O que você ganha', 'imobiliaria-tema' ); ?></p>
				<h2 class="hm-section-title"><?php esc_html_e( 'Ofertas para todos os imóveis, com vantagens conforme cada um', 'imobiliaria-tema' ); ?></h2>
				<p class="hm-section-text"><?php esc_html_e( 'Cada imóvel tem a sua condição de Black Friday. Em todos eles, o desconto extra mínimo está garantido.', 'imobiliaria-tema' ); ?></p>
			</header>

			<div class="bf-benefits-grid">
				<article class="bf-benefit bf-benefit--highlight">
					<span class="bf-benefit-icon material-symbols-outlined">savings</span>
					<h3><?php printf( esc_html__( '%s de desconto extra', 'imobiliaria-tema' ), esc_html( $bf_discount ) ); ?></h3>
					<p><?php esc_html_e( 'No mínimo, em qualquer imóvel à venda. Vale para apartamentos, casas, terrenos, lançamentos e imóveis comerciais.', 'imobiliaria-tema' ); ?></p>
				</article>
				<article class="bf-benefit">
					<span class="bf-benefit-icon material-symbols-outlined">description</span>
					<h3><?php esc_html_e( 'Documentação grátis', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Em imóveis selecionados, os custos de documentação da compra ficam por nossa conta.', 'imobiliaria-tema' ); ?></p>
				</article>
				<article class="bf-benefit">
					<span class="bf-benefit-icon material-symbols-outlined">sell</span>
					<h3><?php esc_html_e( 'Descontos especiais', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Outros imóveis terão descontos diretos no valor de venda, negociados com proprietários e construtoras parceiras.', 'imobiliaria-tema' ); ?></p>
				</article>
				<article class="bf-benefit">
					<span class="bf-benefit-icon material-symbols-outlined">holiday_village</span>
					<h3><?php esc_html_e( '1 ano de condomínio grátis', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Nas casas em condomínio, você ganha 12 meses de taxa condominial pagos para começar a vida nova sem esse custo.', 'imobiliaria-tema' ); ?></p>
				</article>
				<article class="bf-benefit">
					<span class="bf-benefit-icon material-symbols-outlined">redeem</span>
					<h3><?php esc_html_e( 'Brindes conforme o imóvel', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Alguns imóveis ainda vêm com brindes exclusivos. Pergunte quais estão disponíveis no imóvel que você gostou.', 'imobiliaria-tema' ); ?></p>
				</article>
				<article class="bf-benefit">
					<span class="bf-benefit-icon material-symbols-outlined">support_agent</span>
					<h3><?php esc_html_e( 'Assessoria completa', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Simulação, análise de crédito, financiamento, Minha Casa Minha Vida e assinatura: acompanhamos todas as etapas.', 'imobiliaria-tema' ); ?></p>
				</article>
			</div>

			<?php if ( trim( get_the_content() ) ) : ?>
				<div class="bf-intro entry-content">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- ============ IMÓVEIS EM OFERTA ============ -->
	<?php
	$bf_all = $bf_query( '', 36 );
	if ( $bf_all->have_posts() ) :
		?>
		<section class="hm-section hm-section--dark bf-offers" id="bf-imoveis">
			<div class="hm-container">
				<header class="hm-section-head">
					<p class="bf-kicker"><?php esc_html_e( 'Imóveis em oferta', 'imobiliaria-tema' ); ?></p>
					<h2 class="hm-section-title"><?php esc_html_e( 'Escolha o seu imóvel com preço de Black Friday', 'imobiliaria-tema' ); ?></h2>
					<p class="hm-section-text"><?php printf( esc_html__( 'Todos os imóveis abaixo participam da campanha, com pelo menos %s de desconto extra. Clique e confira os detalhes.', 'imobiliaria-tema' ), esc_html( $bf_discount ) ); ?></p>
				</header>
				<?php $bf_grid( $bf_all, 'bf-all' ); ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ CASAS EM CONDOMÍNIO ============ -->
	<?php
	$bf_condo_tipo = function_exists( 'imob_home_mod' ) ? imob_home_mod( 'condo_tipo' ) : 'casa-em-condominio';
	$bf_condo      = $bf_query( $bf_condo_tipo, 24 );
	?>
	<section class="hm-section bf-condo">
		<div class="hm-container">
			<div class="bf-condo-banner">
				<span class="bf-condo-badge">12x</span>
				<div>
					<h2><?php esc_html_e( 'Casas em condomínio com 1 ano de condomínio grátis', 'imobiliaria-tema' ); ?></h2>
					<p><?php esc_html_e( 'Comprou casa em condomínio na Black Friday? Os primeiros 12 meses de taxa condominial são por nossa conta. Segurança, lazer e tranquilidade sem esse custo no primeiro ano.', 'imobiliaria-tema' ); ?></p>
				</div>
				<a class="hm-btn bf-btn-primary" href="<?php echo esc_url( imob_bf_whatsapp_url( __( 'Tenho interesse em casa em condomínio com 1 ano de condomínio grátis.', 'imobiliaria-tema' ) ) ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $bf_wa_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Quero minha casa', 'imobiliaria-tema' ); ?></a>
			</div>
			<?php $bf_grid( $bf_condo, 'bf-condo' ); ?>
		</div>
	</section>

	<span class="hm-divider" aria-hidden="true"></span>

	<!-- ============ COMO FUNCIONA ============ -->
	<section class="hm-section bf-how">
		<div class="hm-container">
			<header class="hm-section-head">
				<p class="bf-kicker"><?php esc_html_e( 'Como aproveitar', 'imobiliaria-tema' ); ?></p>
				<h2 class="hm-section-title"><?php esc_html_e( 'Garanta sua oferta em 3 passos', 'imobiliaria-tema' ); ?></h2>
			</header>
			<ol class="bf-how-list">
				<li>
					<span class="bf-how-number">1</span>
					<h3><?php esc_html_e( 'Escolha o imóvel', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Navegue pelos imóveis em oferta ou conte para a gente o que você procura.', 'imobiliaria-tema' ); ?></p>
				</li>
				<li>
					<span class="bf-how-number">2</span>
					<h3><?php esc_html_e( 'Chame no WhatsApp', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Um corretor informa o desconto, os brindes e as condições exclusivas daquele imóvel.', 'imobiliaria-tema' ); ?></p>
				</li>
				<li>
					<span class="bf-how-number">3</span>
					<h3><?php esc_html_e( 'Feche durante a campanha', 'imobiliaria-tema' ); ?></h3>
					<p><?php esc_html_e( 'Envie a proposta dentro do período da Black Friday e garanta todas as vantagens.', 'imobiliaria-tema' ); ?></p>
				</li>
			</ol>
		</div>
	</section>

	<!-- ============ FORMULÁRIO ============ -->
	<section class="hm-lead bf-lead" id="imob-lead">
		<div class="hm-container hm-lead-inner">
			<div class="hm-lead-content">
				<p class="hm-eyebrow"><?php esc_html_e( 'Black Friday de Imóveis', 'imobiliaria-tema' ); ?></p>
				<h2 class="hm-lead-title"><?php esc_html_e( 'Receba as ofertas do seu perfil', 'imobiliaria-tema' ); ?></h2>
				<p class="hm-lead-text"><?php esc_html_e( 'Deixe seus dados e um corretor envia os imóveis em oferta que combinam com você, com o desconto e os brindes de cada um.', 'imobiliaria-tema' ); ?></p>
				<div class="hm-actions">
					<a class="hm-btn bf-btn-primary" href="<?php echo esc_url( $bf_wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $bf_wa_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Falar no WhatsApp', 'imobiliaria-tema' ); ?></a>
				</div>
			</div>

			<form class="hm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php
				$lead_status = isset( $_GET['imob_lead'] ) ? sanitize_key( $_GET['imob_lead'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				$lead_msgs   = array(
					'ok'       => array( 'success', __( 'Recebemos seu contato! Em breve um corretor envia as ofertas da Black Friday para você.', 'imobiliaria-tema' ) ),
					'invalido' => array( 'error', __( 'Preencha nome, e-mail válido e WhatsApp.', 'imobiliaria-tema' ) ),
					'erro'     => array( 'error', __( 'Não foi possível enviar. Recarregue a página e tente novamente.', 'imobiliaria-tema' ) ),
				);
				if ( isset( $lead_msgs[ $lead_status ] ) ) :
					?>
					<p class="hm-form-notice hm-form-notice--<?php echo esc_attr( $lead_msgs[ $lead_status ][0] ); ?>" role="status"><?php echo esc_html( $lead_msgs[ $lead_status ][1] ); ?></p>
				<?php endif; ?>

				<input type="hidden" name="action" value="imob_lead_form">
				<input type="hidden" name="origem" value="black-friday">
				<?php wp_nonce_field( 'imob_lead_form', 'imob_lead_nonce' ); ?>
				<label class="hm-hp" aria-hidden="true">Site<input type="text" name="imob_site" tabindex="-1" autocomplete="off"></label>

				<div class="hm-field">
					<label for="bf-nome"><?php esc_html_e( 'Nome completo', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
					<input id="bf-nome" name="nome" type="text" required autocomplete="name" placeholder="<?php esc_attr_e( 'Digite o seu nome completo', 'imobiliaria-tema' ); ?>">
				</div>
				<div class="hm-field">
					<label for="bf-email"><?php esc_html_e( 'E-mail', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
					<input id="bf-email" name="email" type="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'Digite o seu melhor e-mail', 'imobiliaria-tema' ); ?>">
				</div>
				<div class="hm-field">
					<label for="bf-whatsapp"><?php esc_html_e( 'Celular/WhatsApp', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
					<input id="bf-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" placeholder="<?php esc_attr_e( 'Digite o seu número do WhatsApp', 'imobiliaria-tema' ); ?>">
				</div>
				<?php
				$bf_tipos = get_terms(
					array(
						'taxonomy'   => 'tipo_imovel',
						'hide_empty' => false,
					)
				);
				?>
				<div class="hm-field">
					<label for="bf-tipo"><?php esc_html_e( 'Tipo de imóvel', 'imobiliaria-tema' ); ?></label>
					<select id="bf-tipo" name="tipo">
						<option value=""><?php esc_html_e( 'Qualquer tipo', 'imobiliaria-tema' ); ?></option>
						<?php if ( ! is_wp_error( $bf_tipos ) ) : ?>
							<?php foreach ( $bf_tipos as $t ) : ?>
								<option value="<?php echo esc_attr( $t->name ); ?>"><?php echo esc_html( $t->name ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="hm-field hm-field--full">
					<label for="bf-mensagem"><?php esc_html_e( 'O que você procura?', 'imobiliaria-tema' ); ?></label>
					<textarea id="bf-mensagem" name="mensagem" rows="2" placeholder="<?php esc_attr_e( 'Ex.: casa em condomínio com 3 quartos, até R$ 500 mil, no bairro Catolé.', 'imobiliaria-tema' ); ?>"></textarea>
				</div>
				<div class="hm-field--full hm-form-actions">
					<button type="submit" class="hm-btn hm-btn--gold"><?php esc_html_e( 'Quero receber as ofertas', 'imobiliaria-tema' ); ?></button>
				</div>
			</form>
		</div>
	</section>

	<!-- ============ PERGUNTAS / CONDIÇÕES ============ -->
	<section class="hm-section bf-faq">
		<div class="hm-container">
			<header class="hm-section-head">
				<h2 class="hm-section-title"><?php esc_html_e( 'Dúvidas sobre a Black Friday', 'imobiliaria-tema' ); ?></h2>
			</header>
			<div class="bf-faq-list">
				<details open>
					<summary><?php esc_html_e( 'Todos os imóveis estão em oferta?', 'imobiliaria-tema' ); ?></summary>
					<p><?php printf( esc_html__( 'Sim. Todos os imóveis à venda participam da campanha, com pelo menos %s de desconto extra. Cada imóvel pode ter ainda outro benefício: desconto direto, documentação grátis, brindes ou 1 ano de condomínio grátis.', 'imobiliaria-tema' ), esc_html( $bf_discount ) ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Como sei qual benefício vale para o imóvel que eu quero?', 'imobiliaria-tema' ); ?></summary>
					<p><?php esc_html_e( 'Os benefícios variam conforme o imóvel. Chame um corretor no WhatsApp informando o imóvel e enviamos todas as condições daquela unidade.', 'imobiliaria-tema' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'O que é a documentação grátis?', 'imobiliaria-tema' ); ?></summary>
					<p><?php esc_html_e( 'Em imóveis selecionados, os custos de documentação da compra são cobertos pela campanha. O corretor informa quais imóveis têm esse benefício e o que está incluído em cada caso.', 'imobiliaria-tema' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Como funciona o 1 ano de condomínio grátis?', 'imobiliaria-tema' ); ?></summary>
					<p><?php esc_html_e( 'Na compra de casas em condomínio durante a campanha, as 12 primeiras taxas condominiais ordinárias ficam por nossa conta, conforme as condições da proposta.', 'imobiliaria-tema' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Posso usar financiamento, FGTS ou Minha Casa Minha Vida?', 'imobiliaria-tema' ); ?></summary>
					<p><?php esc_html_e( 'Pode. As ofertas valem para compra à vista ou financiada, inclusive com FGTS e Minha Casa Minha Vida, conforme o enquadramento de cada imóvel e a aprovação do banco.', 'imobiliaria-tema' ); ?></p>
				</details>
				<details>
					<summary><?php esc_html_e( 'Até quando vale a campanha?', 'imobiliaria-tema' ); ?></summary>
					<p><?php echo esc_html( $bf_period_label ? $bf_period_label . '. ' . __( 'Valem as propostas formalizadas dentro desse período, sujeitas à disponibilidade dos imóveis.', 'imobiliaria-tema' ) : __( 'Valem as propostas formalizadas dentro do período da campanha, sujeitas à disponibilidade dos imóveis.', 'imobiliaria-tema' ) ); ?></p>
				</details>
			</div>
			<p class="bf-legal"><?php esc_html_e( 'Condições válidas durante o período da campanha, sujeitas à disponibilidade, à análise de crédito e às regras de cada imóvel. Benefícios definidos conforme o imóvel e não conversíveis em dinheiro. Consulte um corretor.', 'imobiliaria-tema' ); ?></p>
		</div>
	</section>

	<!-- ============ CTA FINAL ============ -->
	<section class="bf-final">
		<div class="hm-container bf-final-inner">
			<h2><?php printf( esc_html__( 'Seu imóvel com no mínimo %s de desconto extra está esperando', 'imobiliaria-tema' ), esc_html( $bf_discount ) ); ?></h2>
			<a class="hm-btn bf-btn-primary" href="<?php echo esc_url( $bf_wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo $bf_wa_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Garantir minha oferta', 'imobiliaria-tema' ); ?></a>
		</div>
	</section>

	<?php endwhile; ?>

</main>

<?php
get_footer();
