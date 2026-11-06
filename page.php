<?php
/**
 * The template for displaying all standard pages
 *
 * @package ImobiliariaTema
 */

get_header();
?>

<main id="primary" class="site-main imob-standard-page">
	<?php
	while ( have_posts() ) :
		the_post();
		$post_id        = get_the_ID();
		$post_slug      = get_post_field( 'post_name', $post_id );
		$page_tpl       = get_page_template_slug( $post_id );
		$is_elementor   = ( get_post_meta( $post_id, '_elementor_edit_mode', true ) === 'builder' );
		$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
		$raw_content    = trim( get_the_content() );

		// Se a página foi construída no Elementor e possui dados de layout, renderiza sem duplicar cabeçalho
		if ( $is_elementor && ! empty( $elementor_data ) && $raw_content !== '' ) :
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-elementor-page-article' ); ?>>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		elseif ( $post_slug === 'fale-conosco' || $page_tpl === 'templates/contact.php' ) :
			// Página Fale Conosco (compatibilidade com metadados legados e opções do tema)
			$contact_phone   = get_post_meta( $post_id, 'theme_contact_cell', true ) ?: ( get_option( 'imob_contact_whatsapp' ) ?: '(83) 99926-7089' );
			$contact_email   = get_post_meta( $post_id, 'theme_contact_display_email', true ) ?: 'contato@viniciusmatias.com.br';
			$contact_address = get_post_meta( $post_id, 'theme_contact_address', true ) ?: 'Rua Severino Galileu, 296 - Liberdade, Campina Grande - PB';
			$clean_phone     = preg_replace( '/\D/', '', $contact_phone );
			if ( strlen( $clean_phone ) === 10 || strlen( $clean_phone ) === 11 ) {
				$clean_phone = '55' . $clean_phone;
			}
			$wa_url = 'https://api.whatsapp.com/send?phone=' . $clean_phone . '&text=' . rawurlencode( 'Olá! Gostaria de falar com um consultor da IS22 Imóveis.' );
			?>
			<div class="container imob-page-container">
				<header class="imob-archive-header" style="margin-bottom: 35px; text-align: center; background: #ffffff; padding: 40px 25px; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
					<div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(178, 145, 90, 0.12); color: var(--accent-color); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
						<span class="material-symbols-outlined" style="font-size: 16px;">support_agent</span>
						<?php _e( 'Atendimento Personalizado', 'imobiliaria-tema' ); ?>
					</div>
					<h1 class="imob-archive-title" style="font-size: 2.3rem; color: var(--primary-color); margin: 0 0 10px; font-weight: 800;">
						<?php the_title(); ?>
					</h1>
					<p style="max-width: 650px; margin: 0 auto; color: var(--text-light); font-size: 1.05rem; line-height: 1.6;">
						<?php _e( 'Entre em contato conosco para agendar uma visita, avaliar seu imóvel ou tirar dúvidas sobre financiamento imobiliário em Campina Grande e região.', 'imobiliaria-tema' ); ?>
					</p>
				</header>

				<div class="imob-contact-grid">
					<div class="imob-contact-card">
						<span class="material-symbols-outlined imob-contact-card-icon">chat</span>
						<h3><?php _e( 'WhatsApp & Telefone', 'imobiliaria-tema' ); ?></h3>
						<p><?php echo esc_html( $contact_phone ); ?></p>
						<a href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener noreferrer" class="imob-btn-relation">
							<span class="material-symbols-outlined">chat</span>
							<?php _e( 'Chamar no WhatsApp', 'imobiliaria-tema' ); ?>
						</a>
					</div>

					<div class="imob-contact-card">
						<span class="material-symbols-outlined imob-contact-card-icon">mail</span>
						<h3><?php _e( 'E-mail de Atendimento', 'imobiliaria-tema' ); ?></h3>
						<p><?php echo esc_html( $contact_email ); ?></p>
						<a href="mailto:<?php echo esc_attr( $contact_email ); ?>" class="imob-btn-relation">
							<span class="material-symbols-outlined">send</span>
							<?php _e( 'Enviar E-mail', 'imobiliaria-tema' ); ?>
						</a>
					</div>

					<div class="imob-contact-card">
						<span class="material-symbols-outlined imob-contact-card-icon">location_on</span>
						<h3><?php _e( 'Nosso Endereço', 'imobiliaria-tema' ); ?></h3>
						<p><?php echo esc_html( $contact_address ); ?></p>
						<a href="https://www.google.com/maps/search/?api=1&query=<?php echo rawurlencode( $contact_address ); ?>" target="_blank" rel="noopener noreferrer" class="imob-btn-relation">
							<span class="material-symbols-outlined">map</span>
							<?php _e( 'Ver no Google Maps', 'imobiliaria-tema' ); ?>
						</a>
					</div>
				</div>

				<?php if ( $raw_content !== '' ) : ?>
					<div class="imob-page-card" style="margin-top: 30px;">
						<div class="imob-single-post-content entry-content">
							<?php the_content(); ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php
		else :
			// Páginas institucionais padrão (ex: Termos e Condições, Política de Privacidade, etc.)
			?>
			<div class="container imob-page-container">
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'imob-page-card' ); ?>>
					<header class="imob-page-header">
						<div class="imob-page-badge">
							<span class="material-symbols-outlined" style="font-size: 16px;">description</span>
							<?php _e( 'Institucional', 'imobiliaria-tema' ); ?>
						</div>
						<h1 class="imob-page-title"><?php the_title(); ?></h1>
					</header>

					<div class="imob-single-post-content entry-content">
						<?php
						if ( $raw_content !== '' ) :
							the_content();
						elseif ( $post_slug === 'termos-e-condicoes' ) :
							?>
							<p>Bem-vindo ao portal da <strong>IS22 Imóveis | Vinicius Matias</strong>. Ao acessar e utilizar este site, você concorda com os termos e condições descritos abaixo.</p>

							<h2>1. Objetivo do Portal</h2>
							<p>Este site destina-se à divulgação de imóveis para venda, locação, lançamentos imobiliários, avaliações técnicas e conteúdos informativos sobre o mercado imobiliário em Campina Grande e região.</p>

							<h2>2. Informações dos Imóveis</h2>
							<p>Todos os valores, disponibilidades, metragens e condições de pagamento informados nas páginas dos imóveis e empreendimentos estão sujeitos a alterações sem aviso prévio por parte dos proprietários ou construtoras parceiras. Recomendamos sempre confirmar os dados atualizados diretamente com nossos corretores credenciados.</p>

							<h2>3. Propriedade Intelectual</h2>
							<p>Os textos, fotografias, vídeos, logotipos e materiais gráficos presentes neste site são protegidos pela legislação de direitos autorais e não podem ser reproduzidos sem autorização prévia.</p>

							<h2>4. Privacidade e Proteção de Dados (LGPD)</h2>
							<p>Os dados informados em nossos canais de atendimento e formulários de simulação são utilizados exclusivamente para retorno comercial e consultoria imobiliária, em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018). Para mais detalhes, consulte nossa <a href="<?php echo esc_url( home_url( '/politica-de-privacidade/' ) ); ?>">Política de Privacidade</a>.</p>

							<h2>5. Atendimento e Contato</h2>
							<p>Em caso de dúvidas sobre estes Termos e Condições, entre em contato conosco através da nossa página <a href="<?php echo esc_url( home_url( '/fale-conosco/' ) ); ?>">Fale Conosco</a>.</p>
							<?php
						else :
							?>
							<p><?php _e( 'Para mais informações, entre em contato com nossa equipe de atendimento.', 'imobiliaria-tema' ); ?></p>
							<p>
								<a href="<?php echo esc_url( home_url( '/imoveis/' ) ); ?>" class="btn-primary">
									<span class="material-symbols-outlined">home</span>
									<?php _e( 'Ver Imóveis Disponíveis', 'imobiliaria-tema' ); ?>
								</a>
							</p>
							<?php
						endif;
						?>
					</div>
				</article>
			</div>
			<?php
		endif;
	endwhile;
	?>
</main>

<?php
get_footer();

