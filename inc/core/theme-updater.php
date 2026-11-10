<?php
/**
 * Atualizações do tema pelo GitHub.
 *
 * Compara a versão instalada (style.css) com a versão do style.css no repositório
 * do GitHub e, quando houver versão nova, mostra a atualização no WordPress
 * (Painel > Atualizações e Aparência > Temas) igual aos temas da loja oficial,
 * com o botão "Atualizar agora".
 *
 * Configuração: Aparência > Atualizações do tema.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IMOB_UPDATER_REPO', 'vinimatiascg/imobiliaria-tema' );
define( 'IMOB_UPDATER_CACHE', 'imob_theme_updater_remote' );

/**
 * Configurações do atualizador.
 *
 * @return array{branch:string,token:string}
 */
function imob_updater_settings() {
	$opts = get_option( 'imob_theme_updater', array() );
	$opts = is_array( $opts ) ? $opts : array();
	return array(
		'branch' => ! empty( $opts['branch'] ) ? (string) $opts['branch'] : 'main',
		'token'  => ! empty( $opts['token'] ) ? (string) $opts['token'] : '',
	);
}

/**
 * Pasta (slug) do tema instalado.
 *
 * @return string
 */
function imob_updater_slug() {
	return get_template();
}

/**
 * Versão instalada.
 *
 * @return string
 */
function imob_updater_local_version() {
	$theme = wp_get_theme( imob_updater_slug() );
	return (string) $theme->get( 'Version' );
}

/**
 * Cabeçalhos HTTP para a API do GitHub.
 *
 * @param string $accept Tipo de resposta.
 * @return array
 */
function imob_updater_headers( $accept = 'application/vnd.github.raw' ) {
	$headers = array(
		'Accept'     => $accept,
		'User-Agent' => 'WordPress-Theme-Updater',
	);
	$token = imob_updater_settings()['token'];
	if ( $token ) {
		$headers['Authorization'] = 'Bearer ' . $token;
	}
	return $headers;
}

/**
 * Consulta a versão publicada no GitHub (com cache de 6 horas).
 *
 * @param bool $force Ignora o cache.
 * @return array{version:string,error:string,checked:int,sha:string}
 */
function imob_updater_remote( $force = false ) {
	$cached = get_site_transient( IMOB_UPDATER_CACHE );
	if ( ! $force && is_array( $cached ) ) {
		return $cached;
	}

	$settings = imob_updater_settings();
	$result   = array(
		'version' => '',
		'error'   => '',
		'checked' => time(),
		'sha'     => '',
	);

	$url      = sprintf( 'https://api.github.com/repos/%s/contents/style.css?ref=%s', IMOB_UPDATER_REPO, rawurlencode( $settings['branch'] ) );
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 15,
			'headers' => imob_updater_headers(),
		)
	);

	if ( is_wp_error( $response ) ) {
		$result['error'] = $response->get_error_message();
	} else {
		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = (string) wp_remote_retrieve_body( $response );
		if ( 200 === $code && preg_match( '/^[ \t\/*#@]*Version:\s*(.+)$/mi', $body, $m ) ) {
			$result['version'] = trim( $m[1] );
		} elseif ( 404 === $code || 401 === $code || 403 === $code ) {
			$result['error'] = sprintf(
				/* translators: %d: código HTTP. */
				__( 'O GitHub respondeu %d. Se o repositório for privado, informe um token de acesso; confira também o nome da branch.', 'imobiliaria-tema' ),
				$code
			);
		} else {
			/* translators: %d: código HTTP. */
			$result['error'] = sprintf( __( 'Resposta inesperada do GitHub (HTTP %d).', 'imobiliaria-tema' ), $code );
		}
	}

	// Último commit da branch (exibido na tela de configurações).
	if ( ! $result['error'] ) {
		$commit = wp_remote_get(
			sprintf( 'https://api.github.com/repos/%s/commits/%s', IMOB_UPDATER_REPO, rawurlencode( $settings['branch'] ) ),
			array(
				'timeout' => 15,
				'headers' => imob_updater_headers( 'application/vnd.github+json' ),
			)
		);
		if ( ! is_wp_error( $commit ) && 200 === (int) wp_remote_retrieve_response_code( $commit ) ) {
			$data = json_decode( wp_remote_retrieve_body( $commit ), true );
			if ( ! empty( $data['sha'] ) ) {
				$result['sha'] = substr( (string) $data['sha'], 0, 7 );
			}
		}
	}

	set_site_transient( IMOB_UPDATER_CACHE, $result, $result['error'] ? HOUR_IN_SECONDS : 6 * HOUR_IN_SECONDS );
	return $result;
}

/**
 * Há versão nova?
 *
 * @param array|null $remote Dados remotos.
 * @return bool
 */
function imob_updater_has_update( $remote = null ) {
	$remote = $remote ? $remote : imob_updater_remote();
	return ! empty( $remote['version'] ) && version_compare( $remote['version'], imob_updater_local_version(), '>' );
}

/**
 * URL do pacote .zip da branch.
 *
 * @return string
 */
function imob_updater_package_url() {
	return sprintf( 'https://api.github.com/repos/%s/zipball/%s', IMOB_UPDATER_REPO, rawurlencode( imob_updater_settings()['branch'] ) );
}

/**
 * Injeta a atualização na lista de temas do WordPress.
 *
 * @param object $transient Transient update_themes.
 * @return object
 */
function imob_updater_inject( $transient, $cached_only = false ) {
	if ( ! is_object( $transient ) ) {
		$transient = new stdClass();
	}
	$slug   = imob_updater_slug();
	$remote = $cached_only ? get_site_transient( IMOB_UPDATER_CACHE ) : imob_updater_remote();
	if ( ! is_array( $remote ) ) {
		return $transient;
	}
	$item   = array(
		'theme'       => $slug,
		'new_version' => $remote['version'] ? $remote['version'] : imob_updater_local_version(),
		'url'         => 'https://github.com/' . IMOB_UPDATER_REPO . '/commits/' . imob_updater_settings()['branch'],
		'package'     => imob_updater_package_url(),
	);

	if ( imob_updater_has_update( $remote ) ) {
		if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {
			$transient->response = array();
		}
		$transient->response[ $slug ] = $item;
		if ( isset( $transient->no_update[ $slug ] ) ) {
			unset( $transient->no_update[ $slug ] );
		}
	} else {
		if ( isset( $transient->response[ $slug ] ) ) {
			unset( $transient->response[ $slug ] );
		}
		if ( ! isset( $transient->no_update ) || ! is_array( $transient->no_update ) ) {
			$transient->no_update = array();
		}
		$transient->no_update[ $slug ] = $item;
	}
	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'imob_updater_inject' );

/**
 * Na leitura da lista de atualizações usa só o cache (sem chamadas HTTP no front).
 *
 * @param object $transient Transient update_themes.
 * @return object
 */
function imob_updater_inject_cached( $transient ) {
	return imob_updater_inject( $transient, true );
}
add_filter( 'site_transient_update_themes', 'imob_updater_inject_cached' );

/**
 * Envia o token (repositório privado) ao baixar o pacote do GitHub.
 *
 * @param array  $args HTTP args.
 * @param string $url  URL.
 * @return array
 */
function imob_updater_http_args( $args, $url ) {
	$token = imob_updater_settings()['token'];
	if ( $token && 0 === strpos( $url, 'https://api.github.com/repos/' . IMOB_UPDATER_REPO . '/' ) ) {
		$args['headers']                  = isset( $args['headers'] ) && is_array( $args['headers'] ) ? $args['headers'] : array();
		$args['headers']['Authorization'] = 'Bearer ' . $token;
		$args['headers']['User-Agent']    = 'WordPress-Theme-Updater';
	}
	return $args;
}
add_filter( 'http_request_args', 'imob_updater_http_args', 10, 2 );

/**
 * O .zip do GitHub vem com a pasta "dono-repo-commit". Renomeia para a pasta do tema
 * para o WordPress substituir o tema instalado em vez de criar outro.
 *
 * @param string      $source        Pasta extraída.
 * @param string      $remote_source Pasta temporária.
 * @param WP_Upgrader $upgrader      Upgrader.
 * @param array       $hook_extra    Dados extras.
 * @return string|WP_Error
 */
function imob_updater_fix_folder( $source, $remote_source, $upgrader, $hook_extra = array() ) {
	global $wp_filesystem;

	$slug = imob_updater_slug();
	if ( empty( $hook_extra['theme'] ) || $hook_extra['theme'] !== $slug ) {
		return $source;
	}

	$expected = trailingslashit( $remote_source ) . $slug . '/';
	if ( trailingslashit( $source ) === $expected ) {
		return $source;
	}
	if ( ! $wp_filesystem || ! $wp_filesystem->move( untrailingslashit( $source ), untrailingslashit( $expected ), true ) ) {
		return new WP_Error( 'imob_updater_rename', __( 'Não foi possível preparar a pasta da atualização do tema.', 'imobiliaria-tema' ) );
	}
	return $expected;
}
add_filter( 'upgrader_source_selection', 'imob_updater_fix_folder', 10, 4 );

/**
 * Limpa o cache depois de atualizar o tema.
 *
 * @param WP_Upgrader $upgrader Upgrader.
 * @param array       $options  Dados.
 */
function imob_updater_after_update( $upgrader, $options ) {
	if ( isset( $options['type'] ) && 'theme' === $options['type'] ) {
		delete_site_transient( IMOB_UPDATER_CACHE );
	}
}
add_action( 'upgrader_process_complete', 'imob_updater_after_update', 10, 2 );

/* -------------------------------------------------------------------------
 * Tela "Aparência > Atualizações do tema"
 * ---------------------------------------------------------------------- */

function imob_updater_menu() {
	add_theme_page(
		__( 'Atualizações do tema', 'imobiliaria-tema' ),
		__( 'Atualizações do tema', 'imobiliaria-tema' ),
		'update_themes',
		'imob-theme-updater',
		'imob_updater_page'
	);
}
add_action( 'admin_menu', 'imob_updater_menu' );

/**
 * Salvar configurações / verificar agora.
 */
function imob_updater_handle_post() {
	if ( ! current_user_can( 'update_themes' ) ) {
		wp_die( esc_html__( 'Sem permissão.', 'imobiliaria-tema' ) );
	}
	check_admin_referer( 'imob_updater' );

	if ( isset( $_POST['imob_updater_save'] ) ) {
		$current = imob_updater_settings();
		$branch  = isset( $_POST['branch'] ) ? sanitize_text_field( wp_unslash( $_POST['branch'] ) ) : 'main';
		$token   = isset( $_POST['token'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['token'] ) ) ) : '';
		if ( isset( $_POST['token_clear'] ) ) {
			$token = '';
		} elseif ( '' === $token ) {
			$token = $current['token']; // Campo vazio mantém o token salvo.
		}
		update_option(
			'imob_theme_updater',
			array(
				'branch' => $branch ? $branch : 'main',
				'token'  => $token,
			),
			false
		);
	}

	delete_site_transient( IMOB_UPDATER_CACHE );
	imob_updater_remote( true );
	delete_site_transient( 'update_themes' );
	wp_update_themes();

	wp_safe_redirect( add_query_arg( array( 'page' => 'imob-theme-updater', 'checked' => 1 ), admin_url( 'themes.php' ) ) );
	exit;
}
add_action( 'admin_post_imob_updater', 'imob_updater_handle_post' );

function imob_updater_page() {
	$settings = imob_updater_settings();
	$remote   = imob_updater_remote();
	$local    = imob_updater_local_version();
	$slug     = imob_updater_slug();
	$update   = imob_updater_has_update( $remote );
	$up_url   = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&theme=' . rawurlencode( $slug ) ), 'upgrade-theme_' . $slug );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Atualizações do tema', 'imobiliaria-tema' ); ?></h1>

		<?php if ( isset( $_GET['checked'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-info is-dismissible"><p><?php esc_html_e( 'Verificação concluída.', 'imobiliaria-tema' ); ?></p></div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:760px;margin-top:16px">
			<tbody>
				<tr><th style="width:220px"><?php esc_html_e( 'Versão instalada', 'imobiliaria-tema' ); ?></th><td><strong><?php echo esc_html( $local ); ?></strong></td></tr>
				<tr><th><?php esc_html_e( 'Versão no GitHub', 'imobiliaria-tema' ); ?></th><td>
					<?php if ( $remote['version'] ) : ?>
						<strong><?php echo esc_html( $remote['version'] ); ?></strong>
						<?php if ( $remote['sha'] ) : ?>
							<code style="margin-left:6px"><?php echo esc_html( $remote['sha'] ); ?></code>
						<?php endif; ?>
					<?php else : ?>
						&mdash;
					<?php endif; ?>
				</td></tr>
				<tr><th><?php esc_html_e( 'Repositório / branch', 'imobiliaria-tema' ); ?></th><td><a href="<?php echo esc_url( 'https://github.com/' . IMOB_UPDATER_REPO . '/tree/' . $settings['branch'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( IMOB_UPDATER_REPO . ' @ ' . $settings['branch'] ); ?></a></td></tr>
				<tr><th><?php esc_html_e( 'Última verificação', 'imobiliaria-tema' ); ?></th><td><?php echo esc_html( wp_date( 'd/m/Y H:i', (int) $remote['checked'] ) ); ?></td></tr>
				<tr><th><?php esc_html_e( 'Situação', 'imobiliaria-tema' ); ?></th><td>
					<?php if ( $remote['error'] ) : ?>
						<span style="color:#b32d2e">&#9888; <?php echo esc_html( $remote['error'] ); ?></span>
					<?php elseif ( $update ) : ?>
						<span style="color:#996800;font-weight:600">&#9679; <?php printf( esc_html__( 'Nova versão %s disponível.', 'imobiliaria-tema' ), esc_html( $remote['version'] ) ); ?></span>
					<?php else : ?>
						<span style="color:#008a20;font-weight:600">&#10003; <?php esc_html_e( 'O tema está atualizado.', 'imobiliaria-tema' ); ?></span>
					<?php endif; ?>
				</td></tr>
			</tbody>
		</table>

		<p style="margin-top:16px">
			<?php if ( $update ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( $up_url ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Atualizar o tema agora? Faça um backup antes, se possível.', 'imobiliaria-tema' ) ); ?>');"><?php printf( esc_html__( 'Atualizar para a versão %s', 'imobiliaria-tema' ), esc_html( $remote['version'] ) ); ?></a>
			<?php endif; ?>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:760px">
			<input type="hidden" name="action" value="imob_updater">
			<?php wp_nonce_field( 'imob_updater' ); ?>
			<p><button type="submit" class="button"><?php esc_html_e( 'Verificar agora', 'imobiliaria-tema' ); ?></button></p>

			<h2><?php esc_html_e( 'Configurações', 'imobiliaria-tema' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="imob-upd-branch"><?php esc_html_e( 'Branch', 'imobiliaria-tema' ); ?></label></th>
					<td><input id="imob-upd-branch" type="text" class="regular-text" name="branch" value="<?php echo esc_attr( $settings['branch'] ); ?>">
						<p class="description"><?php esc_html_e( 'Branch do GitHub usada como fonte das atualizações (padrão: main).', 'imobiliaria-tema' ); ?></p></td>
				</tr>
				<tr>
					<th scope="row"><label for="imob-upd-token"><?php esc_html_e( 'Token do GitHub', 'imobiliaria-tema' ); ?></label></th>
					<td><input id="imob-upd-token" type="password" class="regular-text" name="token" value="" autocomplete="off" placeholder="<?php echo esc_attr( $settings['token'] ? __( 'Token salvo (deixe vazio para manter)', 'imobiliaria-tema' ) : 'github_pat_...' ); ?>">
						<?php if ( $settings['token'] ) : ?>
							<label style="margin-left:8px"><input type="checkbox" name="token_clear" value="1"> <?php esc_html_e( 'Remover token', 'imobiliaria-tema' ); ?></label>
						<?php endif; ?>
						<p class="description"><?php esc_html_e( 'Só é necessário se o repositório for privado. Crie um token "fine-grained" com permissão de leitura em Contents apenas para este repositório.', 'imobiliaria-tema' ); ?></p></td>
				</tr>
			</table>
			<p><button type="submit" name="imob_updater_save" value="1" class="button button-primary"><?php esc_html_e( 'Salvar e verificar', 'imobiliaria-tema' ); ?></button></p>
		</form>

		<p class="description" style="max-width:760px"><?php esc_html_e( 'Quando houver uma versão nova, ela também aparece em Painel > Atualizações e em Aparência > Temas, com o botão "Atualizar agora". Arquivos alterados manualmente dentro da pasta do tema serão substituídos na atualização.', 'imobiliaria-tema' ); ?></p>
	</div>
	<?php
}

/**
 * Aviso no painel quando houver versão nova.
 */
function imob_updater_notice() {
	if ( ! current_user_can( 'update_themes' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && in_array( $screen->id, array( 'appearance_page_imob-theme-updater', 'update-core', 'update' ), true ) ) {
		return;
	}
	$remote = get_site_transient( IMOB_UPDATER_CACHE );
	if ( ! is_array( $remote ) || ! imob_updater_has_update( $remote ) ) {
		return;
	}
	printf(
		'<div class="notice notice-warning"><p>%1$s <a href="%2$s">%3$s</a></p></div>',
		esc_html( sprintf( __( 'Imobiliária Tema: a versão %1$s está disponível (instalada: %2$s).', 'imobiliaria-tema' ), $remote['version'], imob_updater_local_version() ) ),
		esc_url( admin_url( 'themes.php?page=imob-theme-updater' ) ),
		esc_html__( 'Ver atualização', 'imobiliaria-tema' )
	);
}
add_action( 'admin_notices', 'imob_updater_notice' );
