<?php
/**
 * Seção: formulário "Procurando por um imóvel?" (enviado para inc/home/lead-form.php).
 *
 * @package ImobiliariaTema
 * @var array $args
 */

$lead_status = isset( $_GET['imob_lead'] ) ? sanitize_key( $_GET['imob_lead'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$lead_msgs   = array(
	'ok'       => array( 'success', __( 'Recebemos seu contato! Em breve nossa equipe vai falar com você.', 'imobiliaria-tema' ) ),
	'invalido' => array( 'error', __( 'Preencha nome, e-mail válido e WhatsApp.', 'imobiliaria-tema' ) ),
	'erro'     => array( 'error', __( 'Não foi possível enviar. Recarregue a página e tente novamente.', 'imobiliaria-tema' ) ),
);
$uid = wp_unique_id( 'hm-f' );
?>
<section class="hm-lead" id="imob-lead">
	<div class="hm-container hm-lead-inner">
		<div class="hm-lead-content">
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="hm-eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h2 class="hm-lead-title"><?php echo esc_html( $args['title'] ); ?></h2>
			<?php if ( $args['text'] ) : ?>
				<p class="hm-lead-text"><?php echo esc_html( $args['text'] ); ?></p>
			<?php endif; ?>
			<?php imob_section_buttons( $args, true ); ?>
		</div>

		<form class="hm-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php if ( isset( $lead_msgs[ $lead_status ] ) ) : ?>
				<p class="hm-form-notice hm-form-notice--<?php echo esc_attr( $lead_msgs[ $lead_status ][0] ); ?>" role="status"><?php echo esc_html( $lead_msgs[ $lead_status ][1] ); ?></p>
			<?php endif; ?>

			<input type="hidden" name="action" value="imob_lead_form">
			<?php wp_nonce_field( 'imob_lead_form', 'imob_lead_nonce' ); ?>
			<label class="hm-hp" aria-hidden="true">Site<input type="text" name="imob_site" tabindex="-1" autocomplete="off"></label>

			<div class="hm-field">
				<label for="<?php echo esc_attr( $uid ); ?>-nome"><?php esc_html_e( 'Nome completo', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
				<input id="<?php echo esc_attr( $uid ); ?>-nome" name="nome" type="text" required autocomplete="name" placeholder="<?php esc_attr_e( 'Digite o seu nome completo', 'imobiliaria-tema' ); ?>">
			</div>
			<div class="hm-field">
				<label for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'E-mail', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
				<input id="<?php echo esc_attr( $uid ); ?>-email" name="email" type="email" required autocomplete="email" placeholder="<?php esc_attr_e( 'Digite o seu melhor e-mail', 'imobiliaria-tema' ); ?>">
			</div>
			<div class="hm-field">
				<label for="<?php echo esc_attr( $uid ); ?>-whatsapp"><?php esc_html_e( 'Celular/WhatsApp', 'imobiliaria-tema' ); ?> <span class="hm-req">*</span></label>
				<input id="<?php echo esc_attr( $uid ); ?>-whatsapp" name="whatsapp" type="tel" required autocomplete="tel" placeholder="<?php esc_attr_e( 'Digite o seu número do WhatsApp', 'imobiliaria-tema' ); ?>">
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
					<label for="<?php echo esc_attr( $uid . '-' . $name ); ?>"><?php echo esc_html( $cfg[0] ); ?></label>
					<select id="<?php echo esc_attr( $uid . '-' . $name ); ?>" name="<?php echo esc_attr( $name ); ?>">
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
				<label for="<?php echo esc_attr( $uid ); ?>-mensagem"><?php esc_html_e( 'Mensagem', 'imobiliaria-tema' ); ?></label>
				<textarea id="<?php echo esc_attr( $uid ); ?>-mensagem" name="mensagem" rows="2" placeholder="<?php esc_attr_e( 'Digite aqui o que você procura no seu futuro imóvel, para agilizar o atendimento.', 'imobiliaria-tema' ); ?>"></textarea>
			</div>
			<div class="hm-field--full hm-form-actions">
				<button type="submit" class="hm-btn hm-btn--gold"><?php echo esc_html( $args['submit_text'] ? $args['submit_text'] : __( 'Enviar mensagem', 'imobiliaria-tema' ) ); ?></button>
			</div>
		</form>
	</div>
</section>
