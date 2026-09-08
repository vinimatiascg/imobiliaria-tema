	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="footer-widgets">
				<div class="footer-widget brand-widget">
					<div class="site-branding" style="margin-bottom: 20px;">
						<?php if ( has_custom_logo() ) : ?>
							<?php the_custom_logo(); ?>
						<?php else : ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-text" style="color: #fff; font-size: 1.5rem; font-weight: 700;">
								IS22 <span style="font-weight: 400;">IMÓVEIS</span> | VINICIUS <span style="font-weight: 400;">MATIAS</span>
							</a>
						<?php endif; ?>
					</div>
					<p>Compre seu imóvel com quem é referência em qualidade de atendimento em Campina Grande e região.<br>Os melhores imóveis estão aqui.</p>
					<div class="social-links" style="margin-top: 20px; display: flex; gap: 10px;">
						<a href="#" style="background: rgba(255,255,255,0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;"><span class="material-symbols-outlined">chat</span></a>
						<a href="#" style="background: rgba(255,255,255,0.1); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;"><span class="material-symbols-outlined">photo_camera</span></a>
						<a href="#" class="btn-primary" style="background-color: var(--accent-color); color: var(--primary-color);">Fale conosco</a>
					</div>
				</div>

				<div class="footer-widget links-widget">
					<h3>Acesso rápido</h3>
					<ul>
						<li><a href="#">Página inicial</a></li>
						<li><a href="#">Avaliação de imóveis</a></li>
						<li><a href="#">Todos os imóveis</a></li>
						<li><a href="#">Perguntas frequentes</a></li>
						<li><a href="#">Termos e condições</a></li>
						<li><a href="#">Nosso blog</a></li>
					</ul>
				</div>

				<div class="footer-widget search-widget">
					<h3>Mais pesquisados</h3>
					<ul>
						<li><a href="#">Imóveis com entrada zero</a></li>
						<li><a href="#">Imóveis "Minha casa, minha vida"</a></li>
						<li><a href="#">Imóveis com subsídio da CEHAP em Campina Grande</a></li>
						<li><a href="#">Apartamentos à venda</a></li>
						<li><a href="#">Imóveis comerciais</a></li>
						<li><a href="#">Imóveis Família Ville</a></li>
						<li><a href="#">Terrenos</a></li>
					</ul>
				</div>
			</div>

			<div class="footer-bottom">
				<div class="copyright">
					© <?php echo date('Y'); ?>. Todos os Direitos Reservados
				</div>
				<div class="developer">
					Desenvolvido por <strong>Vinicius Matias</strong>
				</div>
				<a href="#" class="footer-whatsapp-btn">
					<span class="material-symbols-outlined">chat</span> Como posso te ajudar?
				</a>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
