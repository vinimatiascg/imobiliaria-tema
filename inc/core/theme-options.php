<?php
/**
 * Theme Options Page
 */

function imob_theme_options_page() {
	add_menu_page(
		'Configurações da Imobiliária',
		'Opções do Tema',
		'manage_options',
		'imobiliaria-options',
		'imob_theme_options_html',
		'dashicons-admin-generic',
		99
	);
}
add_action( 'admin_menu', 'imob_theme_options_page' );

function imob_register_theme_options() {
	register_setting( 'imob_theme_options_group', 'imob_gmaps_key' );
	register_setting( 'imob_theme_options_group', 'imob_recaptcha_key' );
	register_setting( 'imob_theme_options_group', 'imob_social_instagram' );
	register_setting( 'imob_theme_options_group', 'imob_social_facebook' );
	register_setting( 'imob_theme_options_group', 'imob_contact_phone' );
	register_setting( 'imob_theme_options_group', 'imob_contact_whatsapp' );
	register_setting( 'imob_theme_options_group', 'imob_whatsapp_default_message' );
	register_setting( 'imob_theme_options_group', 'imob_contact_email' );
	
	// Watermark settings
	register_setting( 'imob_theme_options_group', 'imob_watermark_image' );
	register_setting( 'imob_theme_options_group', 'imob_watermark_opacity' );
	register_setting( 'imob_theme_options_group', 'imob_watermark_position' );
}
add_action( 'admin_init', 'imob_register_theme_options' );

function imob_theme_options_enqueue( $hook ) {
	if ( $hook !== 'toplevel_page_imobiliaria-options' ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'imob_theme_options_enqueue' );

function imob_theme_options_html() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$optimizer_nonce = wp_create_nonce( 'imob_optimizer_nonce' );
	?>
	<div class="wrap">
		<h1>Configurações da Imobiliária</h1>
		
		<form method="post" action="options.php">
			<?php settings_fields( 'imob_theme_options_group' ); ?>
			<?php do_settings_sections( 'imob_theme_options_group' ); ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Google Maps API Key</th>
					<td>
						<input type="text" name="imob_gmaps_key" value="<?php echo esc_attr( get_option('imob_gmaps_key') ); ?>" class="regular-text" />
						<p class="description">Chave com suporte a Places API e Maps JavaScript API para habilitar os mapas interativos e buscas por satélite.</p>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row">reCAPTCHA v3 Key</th>
					<td><input type="text" name="imob_recaptcha_key" value="<?php echo esc_attr( get_option('imob_recaptcha_key') ); ?>" class="regular-text" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Instagram URL</th>
					<td><input type="url" name="imob_social_instagram" value="<?php echo esc_attr( get_option('imob_social_instagram') ); ?>" class="regular-text" placeholder="https://instagram.com/..." /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Facebook URL</th>
					<td><input type="url" name="imob_social_facebook" value="<?php echo esc_attr( get_option('imob_social_facebook') ); ?>" class="regular-text" placeholder="https://facebook.com/..." /></td>
				</tr>

				<tr valign="top">
					<th scope="row">Telefone Principal</th>
					<td><input type="text" name="imob_contact_phone" value="<?php echo esc_attr( get_option('imob_contact_phone') ); ?>" class="regular-text" placeholder="(83) 0000-0000" /></td>
				</tr>

				<tr valign="top">
					<th scope="row">WhatsApp Principal</th>
					<td>
						<input type="text" name="imob_contact_whatsapp" value="<?php echo esc_attr( get_option('imob_contact_whatsapp') ); ?>" class="regular-text" placeholder="(83) 99999-9999" />
						<p class="description">Número utilizado no botão flutuante e canais de atendimento direto.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Mensagem Padrão do WhatsApp</th>
					<td>
						<input type="text" name="imob_whatsapp_default_message" value="<?php echo esc_attr( get_option('imob_whatsapp_default_message', 'Olá! Gostaria de mais informações sobre os imóveis.') ); ?>" class="large-text" />
						<p class="description">Mensagem inicial no botão flutuante para páginas gerais do site (Home, listagens, sobre, etc.).</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">E-mail Principal</th>
					<td><input type="email" name="imob_contact_email" value="<?php echo esc_attr( get_option('imob_contact_email') ); ?>" class="regular-text" /></td>
				</tr>

				<tr><td colspan="2"><hr><h2>Marca D'água (Imóveis)</h2></td></tr>

				<tr valign="top">
					<th scope="row">Imagem da Marca D'água</th>
					<td>
						<input type="hidden" name="imob_watermark_image" id="imob_watermark_image" value="<?php echo esc_attr( get_option('imob_watermark_image') ); ?>" />
						<div id="imob_watermark_preview" style="margin-bottom: 10px;">
							<?php if ( get_option('imob_watermark_image') ) : ?>
								<img src="<?php echo esc_url( wp_get_attachment_url( get_option('imob_watermark_image') ) ); ?>" style="max-width: 200px; max-height: 200px; background: #eee; padding: 5px; border-radius: 4px;" />
							<?php endif; ?>
						</div>
						<button type="button" class="button" id="imob_watermark_upload_btn">Selecionar Imagem</button>
						<button type="button" class="button" id="imob_watermark_remove_btn" style="<?php echo get_option('imob_watermark_image') ? '' : 'display:none;'; ?>">Remover Imagem</button>
						<p class="description">Selecione uma imagem PNG com fundo transparente.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Transparência (%)</th>
					<td>
						<input type="number" name="imob_watermark_opacity" value="<?php echo esc_attr( get_option('imob_watermark_opacity', 100) ); ?>" min="0" max="100" />
						<p class="description">Ex: 50 para metade transparente, 100 para sólida.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Posição</th>
					<td>
						<?php $pos = get_option('imob_watermark_position', 'center'); ?>
						<select name="imob_watermark_position">
							<option value="center" <?php selected($pos, 'center'); ?>>Centro</option>
							<option value="bottom_right" <?php selected($pos, 'bottom_right'); ?>>Canto Inferior Direito</option>
							<option value="bottom_left" <?php selected($pos, 'bottom_left'); ?>>Canto Inferior Esquerdo</option>
							<option value="top_right" <?php selected($pos, 'top_right'); ?>>Canto Superior Direito</option>
							<option value="top_left" <?php selected($pos, 'top_left'); ?>>Canto Superior Esquerdo</option>
						</select>
					</td>
				</tr>
			</table>
			
			<?php submit_button(); ?>
		</form>

		<hr style="margin: 40px 0 30px;">

		<h2>Ferramentas de Otimização de Mídia</h2>
		<p class="description" style="font-size: 14px; margin-bottom: 25px;">
			Gerencie as imagens dos imóveis cadastrados e elimine arquivos órfãos sem uso no site para economizar espaço e melhorar a performance.
		</p>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 25px;">
			<!-- CARD 1: OTIMIZADOR DE IMAGENS DE IMÓVEIS -->
			<div class="card" style="padding: 20px; border-radius: 8px; border: 1px solid #ccd0d4; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
				<h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
					<span class="dashicons dashicons-format-gallery" style="color: #2271b1;"></span>
					Otimizar Imagens dos Imóveis
				</h3>
				<p style="color: #555; line-height: 1.5; font-size: 13px;">
					Esta ferramenta varre todas as imagens de todos os imóveis cadastrados, aplica a marca d'água configurada na imagem original, gera apenas as 2 resoluções oficiais (<strong>1280px</strong> para detalhes e <strong>400x300</strong> para miniatura) e <strong>apaga do disco</strong> todas as outras resoluções antigas não utilizadas.
				</p>
				<div style="margin: 20px 0;">
					<button type="button" id="btn-start-imovel-optimizer" class="button button-primary button-large" style="display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-update"></span> Iniciar Otimização das Imagens
					</button>
				</div>
				<div id="imovel-optimizer-progress-wrap" style="display: none; margin-top: 15px;">
					<div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
						<span id="imovel-opt-status">Iniciando...</span>
						<span id="imovel-opt-percent">0%</span>
					</div>
					<div style="background: #e2e4e7; height: 16px; border-radius: 8px; overflow: hidden;">
						<div id="imovel-opt-bar" style="background: #2271b1; width: 0%; height: 100%; transition: width 0.3s ease;"></div>
					</div>
					<div id="imovel-opt-log" style="margin-top: 15px; max-height: 160px; overflow-y: auto; background: #1e1e1e; color: #a6e22e; font-family: monospace; font-size: 11px; padding: 10px; border-radius: 4px; line-height: 1.5;"></div>
				</div>
			</div>

			<!-- CARD 2: LIMPEZA DE IMAGENS ÓRFÃS -->
			<div class="card" style="padding: 20px; border-radius: 8px; border: 1px solid #ccd0d4; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
				<h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
					<span class="dashicons dashicons-trash" style="color: #d63638;"></span>
					Limpeza de Imagens Órfãs
				</h3>
				<p style="color: #555; line-height: 1.5; font-size: 13px;">
					Localiza e apaga permanentemente arquivos de imagem na biblioteca de mídia que <strong>não pertencem a nenhum imóvel</strong>, construtora, empreendimento, proprietário, avatar de corretor, logo ou página do site.
				</p>
				<div style="margin: 20px 0; display: flex; gap: 10px;">
					<button type="button" id="btn-scan-orphans" class="button button-secondary button-large" style="display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-search"></span> Verificar Imagens Órfãs
					</button>
					<button type="button" id="btn-delete-orphans" class="button button-link-delete button-large" style="display: none; align-items: center; gap: 6px; background: #d63638; color: #fff; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer;">
						<span class="dashicons dashicons-trash"></span> Excluir Imagens Órfãs (<span id="orphan-count-label">0</span>)
					</button>
				</div>
				<div id="orphan-progress-wrap" style="display: none; margin-top: 15px;">
					<div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 6px; font-size: 13px;">
						<span id="orphan-status">Analisando...</span>
						<span id="orphan-percent">0%</span>
					</div>
					<div style="background: #e2e4e7; height: 16px; border-radius: 8px; overflow: hidden;">
						<div id="orphan-bar" style="background: #d63638; width: 0%; height: 100%; transition: width 0.3s ease;"></div>
					</div>
					<div id="orphan-log" style="margin-top: 15px; max-height: 160px; overflow-y: auto; background: #1e1e1e; color: #f92672; font-family: monospace; font-size: 11px; padding: 10px; border-radius: 4px; line-height: 1.5;"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
	jQuery(document).ready(function($){
		var frame;
		$('#imob_watermark_upload_btn').on('click', function(e) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media({
				title: 'Selecione a Marca D\'água',
				button: { text: 'Usar esta imagem' },
				multiple: false
			});
			frame.on('select', function() {
				var attachment = frame.state().get('selection').first().toJSON();
				$('#imob_watermark_image').val(attachment.id);
				$('#imob_watermark_preview').html('<img src="'+attachment.url+'" style="max-width: 200px; max-height: 200px; background: #eee; padding: 5px; border-radius: 4px;" />');
				$('#imob_watermark_remove_btn').show();
			});
			frame.open();
		});

		$('#imob_watermark_remove_btn').on('click', function(e) {
			e.preventDefault();
			$('#imob_watermark_image').val('');
			$('#imob_watermark_preview').html('');
			$(this).hide();
		});

		// -------------------------------------------------------------
		// OTIMIZAÇÃO DE IMAGENS DE IMÓVEIS (AJAX)
		// -------------------------------------------------------------
		var optimizerNonce = '<?php echo esc_js( $optimizer_nonce ); ?>';

		$('#btn-start-imovel-optimizer').on('click', function() {
			var $btn = $(this);
			if ( $btn.hasClass('disabled') ) return;

			if ( ! confirm('Deseja iniciar a otimização de todas as imagens de imóveis? Este processo aplicará a marca d\'água configurada, criará os tamanhos de 1280px e 400x300, e removerá os tamanhos antigos desnecessários.') ) {
				return;
			}

			$btn.addClass('disabled').prop('disabled', true);
			$('#imovel-optimizer-progress-wrap').slideDown();
			$('#imovel-opt-log').empty();
			$('#imovel-opt-status').text('Carregando lista de imagens dos imóveis...');
			$('#imovel-opt-bar').css('width', '0%');
			$('#imovel-opt-percent').text('0%');

			function logMsg(msg, isErr) {
				var color = isErr ? '#f92672' : '#a6e22e';
				$('#imovel-opt-log').append('<div style="color:'+color+';">[' + new Date().toLocaleTimeString() + '] ' + msg + '</div>');
				var logElem = document.getElementById('imovel-opt-log');
				if (logElem) logElem.scrollTop = logElem.scrollHeight;
			}

			$.post(ajaxurl, {
				action: 'imob_get_imovel_image_ids',
				nonce: optimizerNonce
			}, function(response) {
				if ( ! response.success || ! response.data.ids || response.data.ids.length === 0 ) {
					$('#imovel-opt-status').text('Nenhuma imagem encontrada.');
					logMsg('Nenhuma imagem associada a imóveis foi encontrada para otimizar.');
					$btn.removeClass('disabled').prop('disabled', false);
					return;
				}

				var ids = response.data.ids;
				var total = ids.length;
				var current = 0;
				logMsg('Encontradas ' + total + ' imagens para processar.');

				function processNext() {
					if ( current >= total ) {
						$('#imovel-opt-status').text('Concluído com sucesso!');
						$('#imovel-opt-bar').css('width', '100%');
						$('#imovel-opt-percent').text('100%');
						logMsg('✔ Todas as ' + total + ' imagens foram processadas com sucesso!');
						$btn.removeClass('disabled').prop('disabled', false);
						return;
					}

					var attId = ids[current];
					$('#imovel-opt-status').text('Processando imagem ' + (current + 1) + ' de ' + total + ' (ID #' + attId + ')...');

					$.post(ajaxurl, {
						action: 'imob_optimize_single_image',
						attachment_id: attId,
						nonce: optimizerNonce
					}, function(res) {
						current++;
						var pct = Math.round((current / total) * 100);
						$('#imovel-opt-bar').css('width', pct + '%');
						$('#imovel-opt-percent').text(pct + '%');

						if ( res.success ) {
							var wtmTxt = res.data.watermark ? ' com marca d\'água' : '';
							logMsg('Imagem #' + attId + ' (' + res.data.title + ') otimizada' + wtmTxt + ' (' + res.data.deleted_old_sizes + ' resoluções antigas removidas).');
						} else {
							var errMsg = (res.data && res.data.message) ? res.data.message : 'Falha ao processar';
							logMsg('Erro na imagem #' + attId + ': ' + errMsg, true);
						}

						processNext();
					}).fail(function() {
						current++;
						logMsg('Erro de conexão ao processar imagem #' + attId, true);
						processNext();
					});
				}

				processNext();
			}).fail(function() {
				$('#imovel-opt-status').text('Erro na requisição inicial.');
				logMsg('Falha ao comunicar com o servidor.', true);
				$btn.removeClass('disabled').prop('disabled', false);
			});
		});

		// -------------------------------------------------------------
		// LIMPEZA DE IMAGENS ÓRFÃS (AJAX)
		// -------------------------------------------------------------
		var orphanIdsList = [];

		$('#btn-scan-orphans').on('click', function() {
			var $btn = $(this);
			$btn.addClass('disabled').prop('disabled', true);
			$('#orphan-progress-wrap').slideDown();
			$('#orphan-log').empty();
			$('#orphan-status').text('Buscando imagens sem vínculo...');
			$('#btn-delete-orphans').hide();

			function logOrphan(msg, isErr) {
				var color = isErr ? '#f92672' : '#e6db74';
				$('#orphan-log').append('<div style="color:'+color+';">[' + new Date().toLocaleTimeString() + '] ' + msg + '</div>');
				var logElem = document.getElementById('orphan-log');
				if (logElem) logElem.scrollTop = logElem.scrollHeight;
			}

			$.post(ajaxurl, {
				action: 'imob_get_orphan_image_ids',
				nonce: optimizerNonce
			}, function(response) {
				$btn.removeClass('disabled').prop('disabled', false);

				if ( ! response.success ) {
					$('#orphan-status').text('Erro ao buscar imagens órfãs.');
					logOrphan('Erro: ' + (response.data || 'Falha na resposta'), true);
					return;
				}

				orphanIdsList = response.data.ids || [];
				var total = orphanIdsList.length;

				if ( total === 0 ) {
					$('#orphan-status').text('Nenhuma imagem órfã encontrada. A biblioteca está limpa!');
					logOrphan('✔ Parabéns! Nenhuma imagem órfã foi detectada.');
				} else {
					$('#orphan-status').text('Encontradas ' + total + ' imagens órfãs.');
					$('#orphan-count-label').text(total);
					$('#btn-delete-orphans').css('display', 'inline-flex');
					logOrphan('Encontradas ' + total + ' imagens órfãs sem nenhum vínculo no site.');
				}
			}).fail(function() {
				$btn.removeClass('disabled').prop('disabled', false);
				$('#orphan-status').text('Erro de conexão ao buscar imagens.');
				logOrphan('Falha de conexão com o servidor.', true);
			});
		});

		$('#btn-delete-orphans').on('click', function() {
			var $btn = $(this);
			if ( orphanIdsList.length === 0 ) return;

			if ( ! confirm('ATENÇÃO: Deseja realmente excluir permanentemente ' + orphanIdsList.length + ' imagens órfãs? Esta ação removerá os arquivos físicos do disco e não pode ser desfeita.') ) {
				return;
			}

			$btn.addClass('disabled').prop('disabled', true);
			$('#btn-scan-orphans').addClass('disabled').prop('disabled', true);

			var total = orphanIdsList.length;
			var current = 0;
			var deletedCount = 0;

			function logOrphan(msg, isErr) {
				var color = isErr ? '#f92672' : '#a6e22e';
				$('#orphan-log').append('<div style="color:'+color+';">[' + new Date().toLocaleTimeString() + '] ' + msg + '</div>');
				var logElem = document.getElementById('orphan-log');
				if (logElem) logElem.scrollTop = logElem.scrollHeight;
			}

			logOrphan('Iniciando exclusão permanente de ' + total + ' imagens...');

			function deleteNext() {
				if ( current >= total ) {
					$('#orphan-status').text('Limpeza concluída! ' + deletedCount + ' imagens excluídas.');
					$('#orphan-bar').css('width', '100%');
					$('#orphan-percent').text('100%');
					logOrphan('✔ Limpeza finalizada! Total de ' + deletedCount + ' imagens excluídas do disco e do banco.');
					$('#btn-delete-orphans').hide();
					$('#btn-scan-orphans').removeClass('disabled').prop('disabled', false);
					orphanIdsList = [];
					return;
				}

				var attId = orphanIdsList[current];
				$('#orphan-status').text('Excluindo imagem ' + (current + 1) + ' de ' + total + ' (ID #' + attId + ')...');

				$.post(ajaxurl, {
					action: 'imob_delete_single_orphan',
					attachment_id: attId,
					nonce: optimizerNonce
				}, function(res) {
					current++;
					var pct = Math.round((current / total) * 100);
					$('#orphan-bar').css('width', pct + '%');
					$('#orphan-percent').text(pct + '%');

					if ( res.success ) {
						deletedCount++;
						logOrphan('Imagem #' + attId + ' (' + res.data.title + ') excluída permanentemente.');
					} else {
						logOrphan('Falha ao excluir anexo #' + attId, true);
					}

					deleteNext();
				}).fail(function() {
					current++;
					logOrphan('Erro ao requisitar exclusão da imagem #' + attId, true);
					deleteNext();
				});
			}

			deleteNext();
		});
	});
	</script>
	<?php
}

