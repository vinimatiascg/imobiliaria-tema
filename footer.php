<?php
// Preparar dados dinâmicos do WhatsApp
$imob_wa_num = '';
if ( is_singular( 'imovel' ) ) {
	// 1. Tenta pegar WhatsApp do corretor associado
	$corretores = get_the_terms( get_the_ID(), 'corretor' );
	if ( ! empty( $corretores ) && ! is_wp_error( $corretores ) ) {
		$wa_user = get_user_by( 'slug', $corretores[0]->slug );
		if ( $wa_user ) {
			$imob_wa_num = get_user_meta( $wa_user->ID, 'imob_whatsapp', true ) ?: get_user_meta( $wa_user->ID, 'whatsapp', true );
		}
	}
	// 2. Tenta pegar WhatsApp do autor do post
	if ( ! $imob_wa_num ) {
		$author_id = get_post_field( 'post_author', get_the_ID() );
		if ( $author_id ) {
			$imob_wa_num = get_user_meta( $author_id, 'imob_whatsapp', true ) ?: get_user_meta( $author_id, 'whatsapp', true );
		}
	}
	// 3. Fallback para opções gerais
	if ( ! $imob_wa_num ) {
		$imob_wa_num = get_option( 'imob_contact_whatsapp' ) ?: get_option( 'imob_contact_phone' );
	}

	$imob_ref = get_post_meta( get_the_ID(), '_imob_ref', true ) ?: get_the_ID();
	$imob_title = get_the_title();
	$imob_wa_msg = "Olá, eu gostaria de mais informações sobre o imóvel {$imob_title} - {$imob_ref}";
} else {
	$imob_wa_num = get_option( 'imob_contact_whatsapp' ) ?: get_option( 'imob_contact_phone' );
	$imob_wa_msg = get_option( 'imob_whatsapp_default_message', 'Olá! Gostaria de mais informações sobre os imóveis.' );
}

$clean_wa_num = preg_replace( '/\D/', '', (string) $imob_wa_num );
if ( ! empty( $clean_wa_num ) ) {
	if ( strlen( $clean_wa_num ) === 10 || strlen( $clean_wa_num ) === 11 ) {
		$clean_wa_num = '55' . $clean_wa_num;
	}
	$whatsapp_click_url = 'https://api.whatsapp.com/send?phone=' . $clean_wa_num . '&text=' . rawurlencode( $imob_wa_msg );
} else {
	// Fallback para abrir WhatsApp direto com o texto pré-definido mesmo sem telefone cadastrado
	$whatsapp_click_url = 'https://api.whatsapp.com/send?text=' . rawurlencode( $imob_wa_msg );
}
?>

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="footer-widgets">
				<div class="footer-widget brand-widget">
					<?php
					if ( is_active_sidebar( 'footer-1' ) ) {
						dynamic_sidebar( 'footer-1' );
					} else {
						imob_footer_brand_html( $whatsapp_click_url );
					}
					?>
				</div>

				<div class="footer-widget links-widget">
					<?php
					if ( is_active_sidebar( 'footer-2' ) ) {
						dynamic_sidebar( 'footer-2' );
					} else {
						imob_footer_menu_html();
					}
					?>
				</div>

				<div class="footer-widget search-widget">
					<?php
					if ( is_active_sidebar( 'footer-3' ) ) {
						dynamic_sidebar( 'footer-3' );
					} else {
						imob_footer_visited_html();
					}
					?>
				</div>
			</div>
		</div>

		<div class="footer-bottom">
			<div class="container footer-bottom-inner">
				<div class="copyright">
					&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?>. <?php echo esc_html( imob_footer_mod( 'copyright' ) ); ?>
				</div>
				<?php imob_footer_developer_html(); ?>
			</div>
		</div>
	</footer>
</div><!-- #page -->

<!-- Botão Flutuante do WhatsApp (Fixo na tela) -->
<aside id="imob-whatsapp-floating-widget" class="imob-whatsapp-float-container" aria-label="Atendimento via WhatsApp">
	<a href="<?php echo esc_url( $whatsapp_click_url ); ?>" 
	   target="_blank" 
	   rel="noopener noreferrer" 
	   class="imob-whatsapp-float-btn" 
	   aria-label="Falar conosco no WhatsApp"
	   title="Falar no WhatsApp">
		<span class="imob-whatsapp-float-tooltip">
			<?php echo is_singular( 'imovel' ) ? 'Dúvidas sobre este imóvel?' : 'Fale conosco no WhatsApp'; ?>
		</span>
		<span class="imob-whatsapp-float-pulse"></span>
		<svg class="imob-whatsapp-float-icon" viewBox="0 0 32 32" width="34" height="34" fill="#ffffff">
			<path d="M16.002 0C7.164 0 0 7.164 0 16c0 2.825.738 5.575 2.138 7.999L0 32l8.223-2.107C10.573 31.282 13.251 32 16.002 32 24.836 32 32 24.836 32 16S24.836 0 16.002 0zm.001 29.333c-2.456 0-4.855-.658-6.953-1.905l-.499-.297-5.167 1.324 1.378-4.954-.326-.519C3.12 20.842 2.453 18.468 2.453 16c0-7.471 6.079-13.55 13.549-13.55 7.47 0 13.547 6.079 13.547 13.55 0 7.471-6.077 13.55-13.547 13.55zm7.427-10.158c-.407-.204-2.411-1.189-2.784-1.324-.374-.136-.645-.204-.916.204-.271.407-1.053 1.324-1.29 1.595-.237.272-.475.306-.882.102-.407-.204-1.72-.634-3.276-2.022-1.211-1.08-2.028-2.414-2.266-2.822-.237-.407-.025-.628.179-.831.183-.183.407-.475.61-.713.204-.237.271-.407.407-.679.136-.271.068-.509-.034-.713-.102-.204-.916-2.206-1.256-3.021-.33-.794-.666-.686-.916-.699-.237-.012-.509-.015-.781-.015-.271 0-.713.102-1.086.509-.374.407-1.426 1.392-1.426 3.395 0 2.003 1.46 3.938 1.663 4.21 2.036 2.716 4.398 4.237 6.84 5.289 1.455.626 2.593.702 3.567.556 1.087-.163 2.411-.984 2.75-1.935.339-.95.339-1.765.237-1.935-.101-.17-.373-.272-.78-.475z"/>
		</svg>
	</a>
</aside>

<?php wp_footer(); ?>
</body>
</html>
