<?php
/**
 * Proteção de e-mails contra robôs de spam com antispambot() do WordPress.
 * Substitui o "Email Address Obfuscation" da Cloudflare, que exigia o script
 * email-decode.min.js em todas as páginas.
 *
 * @package ImobiliariaTema
 */

/**
 * E-mail embaralhado para exibir como texto.
 *
 * @param string $email E-mail.
 * @return string HTML seguro (entidades).
 */
function imob_email_text( $email ) {
	$email = sanitize_email( $email );
	return $email ? antispambot( $email ) : '';
}

/**
 * E-mail embaralhado para usar em href="mailto:...".
 *
 * @param string $email E-mail.
 * @return string Valor seguro para o atributo.
 */
function imob_email_href( $email ) {
	$email = sanitize_email( $email );
	return $email ? 'mailto:' . antispambot( $email, 1 ) : '';
}

/**
 * Embaralha e-mails soltos no HTML do conteúdo (texto e links mailto:).
 *
 * @param string $html Conteúdo.
 * @return string
 */
function imob_protect_emails_in_html( $html ) {
	if ( ! is_string( $html ) || false === strpos( $html, '@' ) || is_admin() || is_feed() ) {
		return $html;
	}

	$email_re = '[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}';

	return preg_replace_callback(
		'/(<script\b.*?<\/script>|<style\b.*?<\/style>|<[^>]+>)|(' . $email_re . ')/is',
		function ( $m ) use ( $email_re ) {
			// Tag HTML: só mexe em links mailto:, nunca em scripts/estilos.
			if ( '' !== $m[1] ) {
				if ( 0 === stripos( $m[1], '<script' ) || 0 === stripos( $m[1], '<style' ) ) {
					return $m[1];
				}
				return preg_replace_callback(
					'/mailto:(' . $email_re . ')/i',
					function ( $mm ) {
						return 'mailto:' . antispambot( $mm[1], 1 );
					},
					$m[1]
				);
			}
			// E-mail em texto.
			return antispambot( $m[2] );
		},
		$html
	);
}
add_filter( 'the_content', 'imob_protect_emails_in_html', 99 );
add_filter( 'widget_text', 'imob_protect_emails_in_html', 99 );
add_filter( 'elementor/frontend/the_content', 'imob_protect_emails_in_html', 99 );
