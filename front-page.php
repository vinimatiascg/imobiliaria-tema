<?php
/**
 * Página inicial (layout Figma "Imobiliária").
 *
 * Textos e imagens: Aparência > Personalizar > Página Inicial.
 *
 * @package ImobiliariaTema
 */

get_header();

$imob_archive_url = get_post_type_archive_link( 'imovel' );
$imob_archive_url = $imob_archive_url ? $imob_archive_url : home_url( '/imoveis/' );
$imob_wa_url      = imob_home_whatsapp_url();

/**
 * Cabeçalho padrão de seção (título + texto).
 */
$imob_section_head = function ( $title, $text, $extra_html = '' ) {
	?>
	<header class="hm-section-head">
		<h2 class="hm-section-title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $text || $extra_html ) : ?>
			<p class="hm-section-text"><?php echo esc_html( $text ); ?><?php echo $extra_html; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML fixo montado abaixo. ?></p>
		<?php endif; ?>
	</header>
	<?php
};

/**
 * Grade de imóveis com paginação no navegador (6 por página).
 */
$imob_property_grid = function ( WP_Query $query, $id ) use ( $imob_archive_url ) {
	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<div class="hm-grid hm-grid--3" id="<?php echo esc_attr( $id ); ?>" data-hm-paginate="6">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			get_template_part( 'template-parts/home/card-imovel' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
	<nav class="hm-pager" data-hm-pager-for="<?php echo esc_attr( $id ); ?>" aria-label="<?php esc_attr_e( 'Páginas', 'imobiliaria-tema' ); ?>">
		<span class="hm-pager-pages"></span>
		<a class="hm-btn hm-btn--gold hm-btn--sm" href="<?php echo esc_url( $imob_archive_url ); ?>"><?php esc_html_e( 'Ver todos', 'imobiliaria-tema' ); ?></a>
	</nav>
	<?php
};

$imob_tipo_query = function ( $slug, $count = 24 ) {
	$args = array(
		'post_type'           => 'imovel',
		'posts_per_page'      => $count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		// Na home, só imóveis disponíveis: vendidos e alugados ficam de fora.
		'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array(
				'taxonomy' => 'status_imovel',
				'field'    => 'slug',
				'terms'    => array( 'vendido', 'alugado' ),
				'operator' => 'NOT IN',
			),
		),
	);
	if ( $slug ) {
		$args['tax_query'][] = array(
			'taxonomy' => 'tipo_imovel',
			'field'    => 'slug',
			'terms'    => array_map( 'trim', explode( ',', $slug ) ),
		);
	}
	return new WP_Query( $args );
};
?>

<main id="primary" class="site-main hm">

	<!-- ============ HERO ============ -->
	<section class="hm-hero">
		<span class="hm-hero-line hm-hero-line--1" aria-hidden="true"></span>
		<span class="hm-hero-line hm-hero-line--2" aria-hidden="true"></span>

		<div class="hm-hero-inner">
			<div class="hm-hero-content">
				<p class="hm-eyebrow"><?php echo esc_html( imob_home_mod( 'hero_eyebrow' ) ); ?></p>
				<h1 class="hm-hero-title"><?php echo esc_html( imob_home_mod( 'hero_title' ) ); ?></h1>
				<p class="hm-hero-text"><?php echo esc_html( imob_home_mod( 'hero_text' ) ); ?></p>
				<div class="hm-actions">
					<a class="hm-btn hm-btn--blue" href="<?php echo esc_url( $imob_archive_url ); ?>">
						<span class="material-symbols-outlined">home</span><?php esc_html_e( 'Ver imóveis', 'imobiliaria-tema' ); ?>
					</a>
					<a class="hm-btn hm-btn--ghost" href="<?php echo esc_url( $imob_wa_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo imob_home_whatsapp_icon(); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG fixo. ?><?php esc_html_e( 'Entre em contato', 'imobiliaria-tema' ); ?>
					</a>
				</div>
			</div>

			<div class="hm-hero-media">
				<?php
				$hero_img = (int) imob_home_mod( 'hero_image' );
				if ( $hero_img ) {
					echo wp_get_attachment_image(
						$hero_img,
						'full',
						false,
						array(
							'class'         => 'hm-hero-img',
							'fetchpriority' => 'high',
							'loading'       => 'eager',
							'sizes'         => '(max-width: 900px) 100vw, 60vw',
						)
					);
				} else {
					echo '<span class="hm-hero-img hm-hero-img--placeholder"></span>';
				}
				?>
			</div>
		</div>

		<?php
		$construtoras = get_posts(
			array(
				'post_type'      => 'construtora',
				'posts_per_page' => 30,
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
			)
		);
		if ( $construtoras ) :
			?>
			<div class="hm-partners">
				<p class="hm-partners-title"><?php echo esc_html( imob_home_mod( 'construtoras_title' ) ); ?></p>
				<div class="hm-partners-panel">
					<button class="hm-scroll-btn hm-scroll-btn--prev" type="button" data-hm-scroll="hm-partners-track" data-dir="-1" aria-label="<?php esc_attr_e( 'Anterior', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">chevron_left</span></button>
					<div class="hm-partners-track" id="hm-partners-track">
						<?php foreach ( $construtoras as $c ) : ?>
							<a class="hm-partner" href="<?php echo esc_url( get_permalink( $c ) ); ?>" title="<?php echo esc_attr( $c->post_title ); ?>">
								<?php
								if ( has_post_thumbnail( $c ) ) {
									echo get_the_post_thumbnail( $c, 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( $c->post_title ) ) );
								} else {
									echo '<span class="hm-partner-name">' . esc_html( $c->post_title ) . '</span>';
								}
								?>
							</a>
						<?php endforeach; ?>
					</div>
					<button class="hm-scroll-btn hm-scroll-btn--next" type="button" data-hm-scroll="hm-partners-track" data-dir="1" aria-label="<?php esc_attr_e( 'Próximo', 'imobiliaria-tema' ); ?>"><span class="material-symbols-outlined">chevron_right</span></button>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<!-- ============ PROCESSO ============ -->
	<section class="hm-section hm-section--light">
		<div class="hm-container">
			<?php $imob_section_head( imob_home_mod( 'process_title' ), imob_home_mod( 'process_text' ) ); ?>
			<?php $imob_step_cols = imob_home_process_columns(); ?>
			<ol class="hm-steps hm-steps--cols-<?php echo (int) $imob_step_cols; ?>" style="--hm-step-cols: <?php echo (int) $imob_step_cols; ?>;">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<li class="hm-step">
						<div class="hm-step-media">
							<?php
							$step_img = (int) get_theme_mod( "imob_home_step_{$i}_image", 0 );
							if ( $step_img ) {
								echo wp_get_attachment_image( $step_img, $imob_step_cols <= 2 ? 'imob_large' : 'medium_large', false, array( 'loading' => 'lazy' ) );
							} else {
								printf(
									'<img src="%1$s" alt="%2$s" width="400" height="240" loading="lazy" decoding="async">',
									esc_url( imob_home_step_default_image( $i ) ),
									esc_attr( imob_home_mod( "step_{$i}_label" ) . ' - ' . imob_home_mod( "step_{$i}_title" ) )
								);
							}
							?>
						</div>
						<div class="hm-step-body">
							<span class="hm-step-number"><?php echo (int) $i; ?></span>
							<span class="hm-step-label"><?php echo esc_html( imob_home_mod( "step_{$i}_label" ) ); ?></span>
							<h3 class="hm-step-title"><?php echo esc_html( imob_home_mod( "step_{$i}_title" ) ); ?></h3>
							<p class="hm-step-text"><?php echo esc_html( imob_home_mod( "step_{$i}_text" ) ); ?></p>
						</div>
					</li>
				<?php endfor; ?>
			</ol>
		</div>
	</section>

	<span class="hm-divider" aria-hidden="true"></span>

	<!-- ============ ÚLTIMOS IMÓVEIS ============ -->
	<section class="hm-section hm-section--split">
		<div class="hm-container">
			<?php
			$imob_section_head( imob_home_mod( 'latest_title' ), imob_home_mod( 'latest_text' ) );
			$imob_property_grid( $imob_tipo_query( '' ), 'hm-latest' );
			?>
		</div>
	</section>

	<!-- ============ NÚMEROS ============ -->
	<section class="hm-section hm-section--dark hm-numbers-section">
		<span class="hm-divider hm-divider--on-dark" aria-hidden="true"></span>
		<div class="hm-container">
			<?php $imob_section_head( imob_home_mod( 'numbers_title' ), imob_home_mod( 'numbers_text' ) ); ?>
			<?php foreach ( array( array( 1, 2, 3 ), array( 4, 5, 6 ) ) as $row ) : ?>
				<div class="hm-numbers">
					<?php
					foreach ( $row as $i ) :
						$value = imob_home_mod( "number_{$i}_value" );
						if ( ! $value ) {
							continue;
						}
						?>
						<div class="hm-number">
							<span class="hm-number-icon"><span class="material-symbols-outlined"><?php echo esc_html( imob_home_mod( "number_{$i}_icon" ) ); ?></span></span>
							<div>
								<strong class="hm-number-value"><?php echo esc_html( $value ); ?></strong>
								<span class="hm-number-label"><?php echo esc_html( imob_home_mod( "number_{$i}_label" ) ); ?></span>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ============ LANÇAMENTOS ============ -->
	<?php
	$launch_query = $imob_tipo_query( imob_home_mod( 'launch_tipo' ) );
	if ( $launch_query->have_posts() ) :
		?>
		<section class="hm-section hm-section--dark">
			<div class="hm-container">
				<?php
				$fale = ' <a href="' . esc_url( $imob_wa_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Fale conosco', 'imobiliaria-tema' ) . '</a> ' . esc_html__( 'e descubra as opções disponíveis e as melhores condições para o seu perfil.', 'imobiliaria-tema' );
				$imob_section_head( imob_home_mod( 'launch_title' ), imob_home_mod( 'launch_text' ), $fale );
				$imob_property_grid( $launch_query, 'hm-launch' );
				?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============ FORMULÁRIO ============ -->
	<section class="hm-lead" id="imob-lead">
		<div class="hm-container hm-lead-inner">
			<div class="hm-lead-content">
				<p class="hm-eyebrow"><?php echo esc_html( imob_home_mod( 'form_eyebrow' ) ); ?></p>
				<h2 class="hm-lead-title"><?php echo esc_html( imob_home_mod( 'form_title' ) ); ?></h2>
				<p class="hm-lead-text"><?php echo esc_html( imob_home_mod( 'form_text' ) ); ?></p>
				<div class="hm-actions">
					<a class="hm-btn hm-btn--blue" href="<?php echo esc_url( $imob_archive_url ); ?>"><span class="material-symbols-outlined">home</span><?php esc_html_e( 'Ver imóveis', 'imobiliaria-tema' ); ?></a>
					<a class="hm-btn hm-btn--ghost" href="<?php echo esc_url( $imob_wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo imob_home_whatsapp_icon(); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php esc_html_e( 'Entre em contato', 'imobiliaria-tema' ); ?></a>
				</div>
			</div>

			<form class="hm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php
				$lead_status = isset( $_GET['imob_lead'] ) ? sanitize_key( $_GET['imob_lead'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				$lead_msgs   = array(
					'ok'       => array( 'success', __( 'Recebemos seu contato! Em breve nossa equipe vai falar com você.', 'imobiliaria-tema' ) ),
					'invalido' => array( 'error', __( 'Preencha nome, e-mail válido e WhatsApp.', 'imobiliaria-tema' ) ),
					'erro'     => array( 'error', __( 'Não foi possível enviar. Recarregue a página e tente novamente.', 'imobiliaria-tema' ) ),
				);
				if ( isset( $lead_msgs[ $lead_status ] ) ) :
					?>
					<p class="hm-form-notice hm-form-notice--<?php echo esc_attr( $lead_msgs[ $lead_status ][0] ); ?>" role="status"><?php echo esc_html( $lead_msgs[ $lead_status ][1] ); ?></p>
				<?php endif; ?>

				<input type="hidden" name="action" value="imob_lead_form">
				<?php wp_nonce_field( 'imob_lead_form', 'imob_lead_nonce' ); ?>
				<label class="hm-hp" aria-hidden="true">Site<input type="text" name="imob_site" tabindex="-1" autocomplete="off"></label>

				<div class="hm-field">
					<label for="hm-nome"><?php esc_html_e( 'Nome completo', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
					<input id="hm-nome" name="nome" type="text" required autocomplete="name" placeholder="<?php esc_attr_e( 'Digite o seu nome completo', 'imobiliaria-tema' ); ?>">
				</div>
				<div class="hm-field">
					<label for="hm-email"><?php esc_html_e( 'E-mail', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
					<input id="hm-email" name="email" type="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'Digite o seu melhor e-mail', 'imobiliaria-tema' ); ?>">
				</div>
				<div class="hm-field">
					<label for="hm-whatsapp"><?php esc_html_e( 'Celular/WhatsApp', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
					<input id="hm-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" placeholder="<?php esc_attr_e( 'Digite o seu número do WhatsApp', 'imobiliaria-tema' ); ?>">
				</div>
				<?php
				$selects = array(
					'estado'     => array( __( 'Estado do imóvel', 'imobiliaria-tema' ), 'estagio_obra' ),
					'tipo'       => array( __( 'Tipo de imóvel', 'imobiliaria-tema' ), 'tipo_imovel' ),
					'localidade' => array( __( 'Localização do imóvel', 'imobiliaria-tema' ), 'localidade' ),
				);
				foreach ( $selects as $name => $cfg ) :
					$terms = get_terms(
						array(
							'taxonomy'   => $cfg[1],
							'hide_empty' => false,
						)
					);
					?>
					<div class="hm-field">
						<label for="hm-<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $cfg[0] ); ?></label>
						<select id="hm-<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>">
							<option value=""><?php esc_html_e( 'Nenhum', 'imobiliaria-tema' ); ?></option>
							<?php if ( ! is_wp_error( $terms ) ) : ?>
								<?php foreach ( $terms as $t ) : ?>
									<option value="<?php echo esc_attr( $t->name ); ?>"><?php echo esc_html( $t->name ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</div>
				<?php endforeach; ?>
				<div class="hm-field hm-field--full">
					<label for="hm-mensagem"><?php esc_html_e( 'Mensagem', 'imobiliaria-tema' ); ?></label>
					<textarea id="hm-mensagem" name="mensagem" rows="2" placeholder="<?php esc_attr_e( 'Digite aqui o que você procura no seu futuro imóvel, para agilizar o atendimento.', 'imobiliaria-tema' ); ?>"></textarea>
				</div>
				<div class="hm-field--full hm-form-actions">
					<button type="submit" class="hm-btn hm-btn--gold"><?php esc_html_e( 'Enviar mensagem', 'imobiliaria-tema' ); ?></button>
				</div>
			</form>
		</div>
	</section>

	<!-- ============ CASAS EM CONDOMÍNIO ============ -->
	<?php
	$condo_query = $imob_tipo_query( imob_home_mod( 'condo_tipo' ) );
	if ( $condo_query->have_posts() ) :
		?>
		<section class="hm-section hm-section--light">
			<div class="hm-container">
				<?php
				$imob_section_head( imob_home_mod( 'condo_title' ), imob_home_mod( 'condo_text' ) );
				$imob_property_grid( $condo_query, 'hm-condo' );
				?>
			</div>
		</section>
		<span class="hm-divider" aria-hidden="true"></span>
	<?php endif; ?>

	<!-- ============ BLOG ============ -->
	<?php
	$blog_query = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $blog_query->have_posts() ) :
		?>
		<section class="hm-section hm-section--light">
			<div class="hm-container">
				<?php $imob_section_head( imob_home_mod( 'blog_title' ), imob_home_mod( 'blog_text' ) ); ?>
				<div class="hm-grid hm-grid--4">
					<?php
					while ( $blog_query->have_posts() ) :
						$blog_query->the_post();
						$is_new = ( time() - get_post_time( 'U', true ) ) < 30 * DAY_IN_SECONDS;
						?>
						<article class="hm-post">
							<a class="hm-post-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) );
								} else {
									echo '<span class="hm-card-placeholder"></span>';
								}
								?>
								<?php if ( $is_new ) : ?>
									<span class="hm-badge hm-badge--gold"><?php esc_html_e( 'Novo', 'imobiliaria-tema' ); ?></span>
								<?php endif; ?>
							</a>
							<div class="hm-post-body">
								<h3 class="hm-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="hm-post-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22, '...' ) ); ?></p>
								<time class="hm-post-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><span class="material-symbols-outlined">schedule</span><?php echo esc_html( get_the_date( 'j \d\e F \d\e Y' ) ); ?></time>
							</div>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<span class="hm-divider" aria-hidden="true"></span>
	<?php endif; ?>

	<!-- ============ DEPOIMENTOS ============ -->
	<?php
	$depoimentos = get_posts(
		array(
			'post_type'      => 'depoimento',
			'posts_per_page' => 12,
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		)
	);
	?>
	<section class="hm-testimonials">
		<div class="hm-container">
			<?php
			if ( $depoimentos ) :
				$imob_section_head( imob_home_mod( 'testimonials_title' ), imob_home_mod( 'testimonials_text' ) );
				?>
				<div class="hm-testimonials-track" id="hm-testimonials-track">
					<?php
					foreach ( $depoimentos as $dep ) :
						$nome   = get_post_meta( $dep->ID, '_imob_dep_nome', true );
						$cidade = get_post_meta( $dep->ID, '_imob_dep_cidade', true );
						$nota   = (int) get_post_meta( $dep->ID, '_imob_dep_nota', true );
						$nota   = $nota ? $nota : 5;
						?>
						<figure class="hm-testimonial">
							<span class="hm-quote" aria-hidden="true">&ldquo;</span>
							<h3 class="hm-testimonial-title"><?php echo esc_html( get_the_title( $dep ) ); ?></h3>
							<blockquote class="hm-testimonial-text"><?php echo wp_kses_post( wpautop( $dep->post_content ) ); ?></blockquote>
							<figcaption class="hm-testimonial-author">
								<span class="hm-avatar">
									<?php
									if ( has_post_thumbnail( $dep ) ) {
										echo get_the_post_thumbnail( $dep, 'thumbnail', array( 'loading' => 'lazy' ) );
									} else {
										echo esc_html( mb_substr( $nome ? $nome : get_the_title( $dep ), 0, 1 ) );
									}
									?>
								</span>
								<span class="hm-author-info">
									<strong><?php echo esc_html( $nome ); ?></strong>
									<?php if ( $cidade ) : ?>
										<small><?php echo esc_html( $cidade ); ?></small>
									<?php endif; ?>
								</span>
								<span class="hm-stars" aria-label="<?php echo esc_attr( sprintf( __( 'Nota %d de 5', 'imobiliaria-tema' ), $nota ) ); ?>"><?php echo esc_html( str_repeat( '★', $nota ) . str_repeat( '☆', 5 - $nota ) ); ?></span>
							</figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( get_theme_mod( 'imob_home_testimonials_whatsapp', true ) ) : ?>
				<div class="hm-testimonials-cta">
					<a class="hm-btn hm-btn--blue" href="<?php echo esc_url( $imob_wa_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo imob_home_whatsapp_icon(); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( get_theme_mod( 'imob_home_testimonials_whatsapp_label', __( 'Realize o seu sonho', 'imobiliaria-tema' ) ) ?: __( 'Realize o seu sonho', 'imobiliaria-tema' ) ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>

</main>

<?php
get_footer();
