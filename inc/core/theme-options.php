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
			<div class="card imob-bg-card" data-type="optimize" style="padding: 20px; border-radius: 8px; border: 1px solid #ccd0d4; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
				<h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
					<span class="dashicons dashicons-format-gallery" style="color: #2271b1;"></span>
					Otimizar Imagens dos Imóveis
				</h3>
				<p style="color: #555; line-height: 1.5; font-size: 13px;">
					Varre todas as imagens dos imóveis e empreendimentos, <strong>converte para JPG</strong> com no máximo <strong>1280px</strong>, aplica a marca d'água nas que ainda não têm, gera só a miniatura <strong>400x300</strong> e <strong>apaga do disco</strong> o arquivo original e todas as outras resoluções.
				</p>
				<p style="color: #555; line-height: 1.5; font-size: 13px;">
					Roda <strong>em segundo plano</strong>: pode fechar esta página que o processo continua no servidor. Fotos que já têm marca d'água não recebem outra.
				</p>
				<div style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
					<button type="button" class="button button-primary button-large imob-bg-start" style="display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-update"></span> Iniciar Otimização das Imagens
					</button>
					<button type="button" class="button button-large imob-bg-cancel" style="display: none; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-no"></span> Cancelar
					</button>
				</div>
				<div class="imob-bg-progress" style="display: none; margin-top: 15px;">
					<div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 6px; font-size: 13px; gap: 10px;">
						<span class="imob-bg-status">Iniciando...</span>
						<span class="imob-bg-percent">0%</span>
					</div>
					<div style="background: #e2e4e7; height: 16px; border-radius: 8px; overflow: hidden;">
						<div class="imob-bg-bar" style="background: #2271b1; width: 0%; height: 100%; transition: width 0.3s ease;"></div>
					</div>
					<div class="imob-bg-log" style="margin-top: 15px; max-height: 200px; overflow-y: auto; background: #1e1e1e; color: #a6e22e; font-family: monospace; font-size: 11px; padding: 10px; border-radius: 4px; line-height: 1.5;"></div>
				</div>
			</div>

			<!-- CARD 2: LIMPEZA DE IMAGENS ÓRFÃS -->
			<div class="card imob-bg-card" data-type="orphans" style="padding: 20px; border-radius: 8px; border: 1px solid #ccd0d4; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
				<h3 style="margin-top: 0; display: flex; align-items: center; gap: 8px;">
					<span class="dashicons dashicons-trash" style="color: #d63638;"></span>
					Limpeza de Imagens Órfãs
				</h3>
				<p style="color: #555; line-height: 1.5; font-size: 13px;">
					Localiza e apaga permanentemente imagens da biblioteca de mídia que <strong>não pertencem a nenhum imóvel</strong>, construtora, empreendimento, proprietário, corretor, logo ou página do site. Antes de apagar cada uma, confere de novo se ela não aparece no conteúdo das páginas (Elementor, blocos) ou nas configurações.
				</p>
				<p style="color: #555; line-height: 1.5; font-size: 13px;">
					A exclusão roda <strong>em segundo plano</strong>: pode fechar esta página que o processo continua no servidor.
				</p>
				<div style="margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap;">
					<button type="button" id="btn-scan-orphans" class="button button-secondary button-large" style="display: inline-flex; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-search"></span> Verificar Imagens Órfãs
					</button>
					<button type="button" class="button button-link-delete button-large imob-bg-start" style="display: none; align-items: center; gap: 6px; background: #d63638; color: #fff; border: none; padding: 0 15px; border-radius: 4px; cursor: pointer;">
						<span class="dashicons dashicons-trash"></span> Excluir Imagens Órfãs (<span id="orphan-count-label">0</span>)
					</button>
					<button type="button" class="button button-large imob-bg-cancel" style="display: none; align-items: center; gap: 6px;">
						<span class="dashicons dashicons-no"></span> Cancelar
					</button>
				</div>
				<div class="imob-bg-progress" style="display: none; margin-top: 15px;">
					<div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 6px; font-size: 13px; gap: 10px;">
						<span class="imob-bg-status">Analisando...</span>
						<span class="imob-bg-percent">0%</span>
					</div>
					<div style="background: #e2e4e7; height: 16px; border-radius: 8px; overflow: hidden;">
						<div class="imob-bg-bar" style="background: #d63638; width: 0%; height: 100%; transition: width 0.3s ease;"></div>
					</div>
					<div class="imob-bg-log" style="margin-top: 15px; max-height: 200px; overflow-y: auto; background: #1e1e1e; color: #e6db74; font-family: monospace; font-size: 11px; padding: 10px; border-radius: 4px; line-height: 1.5;"></div>
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
		// TAREFAS EM SEGUNDO PLANO (otimização e limpeza de órfãs)
		// O servidor processa a fila; aqui só iniciamos e acompanhamos.
		// -------------------------------------------------------------
		var optimizerNonce = '<?php echo esc_js( $optimizer_nonce ); ?>';
		var pollTimers = {};

		function escHtml(str) {
			return $('<div>').text(str == null ? '' : String(str)).html();
		}

		function cardFor(type) {
			return $('.imob-bg-card[data-type="' + type + '"]');
		}

		function renderJob(type, job) {
			var $card = cardFor(type);
			if ( ! job || job.status === 'idle' ) {
				return;
			}

			var total = parseInt(job.total, 10) || 0;
			var done  = parseInt(job.done, 10) || 0;
			var pct   = total ? Math.round((done / total) * 100) : 100;
			var running = job.status === 'running';

			$card.find('.imob-bg-progress').show();
			$card.find('.imob-bg-bar').css('width', pct + '%');
			$card.find('.imob-bg-percent').text(pct + '%');

			var status;
			if ( running ) {
				status = 'Em segundo plano: ' + done + ' de ' + total + ' processadas' + (job.source === 'auto' ? ' (fila automática)' : '') + '...';
			} else if ( job.status === 'cancelled' ) {
				status = 'Cancelada: ' + done + ' de ' + total + ' processadas.';
			} else {
				status = 'Concluída: ' + (job.ok || 0) + ' processadas, ' + (job.skipped || 0) + ' sem alteração, ' + (job.errors || 0) + ' com erro.';
			}
			$card.find('.imob-bg-status').text(status);

			var html = '';
			$.each(job.log || [], function(i, line) {
				var color = line.e ? '#f92672' : (type === 'orphans' ? '#e6db74' : '#a6e22e');
				html += '<div style="color:' + color + ';">[' + escHtml(line.t) + '] ' + escHtml(line.m) + '</div>';
			});
			var $log = $card.find('.imob-bg-log');
			var atBottom = $log[0].scrollHeight - $log.scrollTop() - $log.outerHeight() < 30;
			$log.html(html);
			if ( atBottom ) {
				$log.scrollTop($log[0].scrollHeight);
			}

			$card.find('.imob-bg-cancel').css('display', running ? 'inline-flex' : 'none');
			$card.find('.imob-bg-start').prop('disabled', running).toggleClass('disabled', running);
			if ( type === 'orphans' ) {
				$('#btn-scan-orphans').prop('disabled', running).toggleClass('disabled', running);
				if ( running ) {
					$card.find('.imob-bg-start').hide();
				}
			}

			clearTimeout(pollTimers[type]);
			if ( running ) {
				pollTimers[type] = setTimeout(function() { poll(type); }, 3000);
			}
		}

		function poll(type) {
			$.post(ajaxurl, { action: 'imob_bg_status', type: type, nonce: optimizerNonce }, function(res) {
				if ( res && res.success ) {
					renderJob(type, res.data);
				}
			}).fail(function() {
				clearTimeout(pollTimers[type]);
				pollTimers[type] = setTimeout(function() { poll(type); }, 6000);
			});
		}

		function startJob(type) {
			var $card = cardFor(type);
			$card.find('.imob-bg-progress').show();
			$card.find('.imob-bg-status').text('Montando a fila...');
			$card.find('.imob-bg-start').prop('disabled', true).addClass('disabled');

			$.post(ajaxurl, { action: 'imob_bg_start', type: type, nonce: optimizerNonce }, function(res) {
				if ( res && res.success ) {
					renderJob(type, res.data);
				} else {
					$card.find('.imob-bg-status').text('Erro: ' + ((res && res.data && res.data.message) || 'falha ao iniciar'));
					$card.find('.imob-bg-start').prop('disabled', false).removeClass('disabled');
				}
			}).fail(function() {
				$card.find('.imob-bg-status').text('Falha de conexão com o servidor.');
				$card.find('.imob-bg-start').prop('disabled', false).removeClass('disabled');
			});
		}

		cardFor('optimize').find('.imob-bg-start').on('click', function() {
			if ( $(this).prop('disabled') ) return;
			if ( ! confirm('Iniciar a otimização de todas as imagens dos imóveis?\n\nAs imagens serão convertidas para JPG com no máximo 1280px, receberão a marca d\'água (se ainda não tiverem) e o arquivo original e as outras resoluções serão APAGADOS do disco. Não dá para desfazer.') ) {
				return;
			}
			startJob('optimize');
		});

		cardFor('orphans').find('.imob-bg-start').on('click', function() {
			if ( $(this).prop('disabled') ) return;
			var total = $('#orphan-count-label').text();
			if ( ! confirm('ATENÇÃO: Deseja realmente excluir permanentemente até ' + total + ' imagens órfãs? Os arquivos serão removidos do disco e não dá para desfazer. Cada imagem é conferida de novo antes de ser apagada.') ) {
				return;
			}
			startJob('orphans');
		});

		$('.imob-bg-cancel').on('click', function() {
			var type = $(this).closest('.imob-bg-card').data('type');
			$(this).prop('disabled', true);
			var $btn = $(this);
			$.post(ajaxurl, { action: 'imob_bg_cancel', type: type, nonce: optimizerNonce }, function(res) {
				$btn.prop('disabled', false);
				if ( res && res.success ) {
					renderJob(type, res.data);
				}
			}).fail(function() { $btn.prop('disabled', false); });
		});

		$('#btn-scan-orphans').on('click', function() {
			var $btn = $(this);
			var $card = cardFor('orphans');
			$btn.prop('disabled', true).addClass('disabled');
			$card.find('.imob-bg-start').hide();
			$card.find('.imob-bg-progress').show();
			$card.find('.imob-bg-bar').css('width', '0%');
			$card.find('.imob-bg-percent').text('');
			$card.find('.imob-bg-log').empty();
			$card.find('.imob-bg-status').text('Buscando imagens sem vínculo...');

			$.post(ajaxurl, { action: 'imob_get_orphan_image_ids', nonce: optimizerNonce }, function(res) {
				$btn.prop('disabled', false).removeClass('disabled');
				if ( ! res || ! res.success ) {
					$card.find('.imob-bg-status').text('Erro ao buscar imagens órfãs.');
					return;
				}
				var total = res.data.total || 0;
				if ( total === 0 ) {
					$card.find('.imob-bg-status').text('Nenhuma imagem órfã encontrada. A biblioteca está limpa!');
				} else {
					$card.find('.imob-bg-status').text('Encontradas ' + total + ' imagens sem vínculo.');
					$('#orphan-count-label').text(total);
					$card.find('.imob-bg-start').css('display', 'inline-flex').prop('disabled', false).removeClass('disabled');
				}
			}).fail(function() {
				$btn.prop('disabled', false).removeClass('disabled');
				$card.find('.imob-bg-status').text('Erro de conexão ao buscar imagens.');
			});
		});

		// Ao abrir a página, mostra tarefas em andamento ou a última executada.
		poll('optimize');
		poll('orphans');
	});
	</script>
	<?php
}

