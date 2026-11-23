<?php
/**
 * Organização da hierarquia de categorias (termos pai e filhos).
 *
 * 1. Ação em lote "Mover para a categoria pai" nas telas de termos das taxonomias
 *    hierárquicas (Tipos de Imóvel, Localidades, Status, Categorias do blog...).
 *    Marque os filhos, escolha a ação, escolha o pai e clique em Aplicar.
 * 2. imob_restore_realhomes_hierarchy(): refaz os vínculos pai/filho a partir dos
 *    termos antigos do RealHomes, que continuam no banco. O importador copiava só os
 *    nomes, então todos os filhos ficavam soltos no nível raiz.
 *
 * @package ImobiliariaTema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Move termos para dentro de um termo pai.
 *
 * Ignora o próprio pai e qualquer termo que seja ancestral do pai (evita ciclos).
 *
 * @param string $taxonomy  Taxonomia.
 * @param int[]  $term_ids  Termos a mover.
 * @param int    $parent_id Novo pai (0 = nível raiz).
 * @return array{moved:int,skipped:int,unchanged:int}
 */
function imob_move_terms_to_parent( $taxonomy, $term_ids, $parent_id ) {
	$result    = array( 'moved' => 0, 'skipped' => 0, 'unchanged' => 0 );
	$parent_id = (int) $parent_id;

	if ( ! taxonomy_exists( $taxonomy ) || ! is_taxonomy_hierarchical( $taxonomy ) ) {
		return $result;
	}

	if ( $parent_id ) {
		$parent = get_term( $parent_id, $taxonomy );
		if ( ! $parent || is_wp_error( $parent ) ) {
			$result['skipped'] = count( (array) $term_ids );
			return $result;
		}
		$parent_ancestors = array_map( 'intval', get_ancestors( $parent_id, $taxonomy, 'taxonomy' ) );
	} else {
		$parent_ancestors = array();
	}

	foreach ( array_unique( array_map( 'intval', (array) $term_ids ) ) as $term_id ) {
		$term = get_term( $term_id, $taxonomy );
		if ( ! $term || is_wp_error( $term ) ) {
			$result['skipped']++;
			continue;
		}
		// O pai não pode ser o próprio termo nem um descendente dele.
		if ( $term_id === $parent_id || in_array( $term_id, $parent_ancestors, true ) ) {
			$result['skipped']++;
			continue;
		}
		if ( (int) $term->parent === $parent_id ) {
			$result['unchanged']++;
			continue;
		}

		$updated = wp_update_term( $term_id, $taxonomy, array( 'parent' => $parent_id ) );
		if ( is_wp_error( $updated ) ) {
			$result['skipped']++;
		} else {
			$result['moved']++;
		}
	}

	if ( $result['moved'] ) {
		clean_taxonomy_cache( $taxonomy );
	}

	return $result;
}

/**
 * Taxonomias hierárquicas com tela no painel.
 *
 * @return string[]
 */
function imob_hierarchy_taxonomies() {
	return get_taxonomies(
		array(
			'hierarchical' => true,
			'show_ui'      => true,
		)
	);
}

/**
 * Registra a ação em lote em cada taxonomia hierárquica.
 */
function imob_term_hierarchy_register_bulk_actions() {
	foreach ( imob_hierarchy_taxonomies() as $taxonomy ) {
		add_filter( "bulk_actions-edit-{$taxonomy}", 'imob_term_hierarchy_bulk_action' );
		add_filter( "handle_bulk_actions-edit-{$taxonomy}", 'imob_term_hierarchy_handle_bulk_action', 10, 3 );
	}
}
add_action( 'admin_init', 'imob_term_hierarchy_register_bulk_actions' );

/**
 * @param array $actions Ações em lote.
 * @return array
 */
function imob_term_hierarchy_bulk_action( $actions ) {
	$actions['imob_move_parent'] = __( 'Mover para a categoria pai…', 'imobiliaria-tema' );
	return $actions;
}

/**
 * Processa a ação em lote. O nonce "bulk-tags" já foi conferido pelo core em edit-tags.php.
 *
 * @param string $redirect URL de retorno.
 * @param string $action   Ação escolhida.
 * @param int[]  $term_ids Termos marcados.
 * @return string
 */
function imob_term_hierarchy_handle_bulk_action( $redirect, $action, $term_ids ) {
	if ( 'imob_move_parent' !== $action ) {
		return $redirect;
	}

	$screen   = get_current_screen();
	$taxonomy = $screen && ! empty( $screen->taxonomy ) ? $screen->taxonomy : '';
	if ( ! $taxonomy && isset( $_REQUEST['taxonomy'] ) ) {
		$taxonomy = sanitize_key( $_REQUEST['taxonomy'] );
	}
	$tax_obj  = $taxonomy ? get_taxonomy( $taxonomy ) : null;

	if ( ! $tax_obj || ! current_user_can( $tax_obj->cap->edit_terms ) ) {
		return $redirect;
	}

	$parent_id = isset( $_REQUEST['imob_new_parent'] ) ? (int) $_REQUEST['imob_new_parent'] : 0;
	$parent_id = max( 0, $parent_id );
	$result    = imob_move_terms_to_parent( $taxonomy, $term_ids, $parent_id );

	return add_query_arg(
		array(
			'imob_moved'     => $result['moved'],
			'imob_skipped'   => $result['skipped'],
			'imob_unchanged' => $result['unchanged'],
		),
		remove_query_arg( array( 'message', 'imob_moved', 'imob_skipped', 'imob_unchanged' ), $redirect )
	);
}

/**
 * Remove os parâmetros do aviso da barra de endereço após exibir.
 *
 * @param string[] $args Parâmetros removíveis.
 * @return string[]
 */
function imob_term_hierarchy_removable_args( $args ) {
	return array_merge( $args, array( 'imob_moved', 'imob_skipped', 'imob_unchanged', 'imob_rh_restored' ) );
}
add_filter( 'removable_query_args', 'imob_term_hierarchy_removable_args' );

/**
 * Aviso com o resultado da ação em lote.
 */
function imob_term_hierarchy_notice() {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-tags' !== $screen->base || ! isset( $_GET['imob_moved'] ) ) {
		return;
	}

	$moved     = (int) $_GET['imob_moved'];
	$skipped   = isset( $_GET['imob_skipped'] ) ? (int) $_GET['imob_skipped'] : 0;
	$unchanged = isset( $_GET['imob_unchanged'] ) ? (int) $_GET['imob_unchanged'] : 0;

	$parts = array(
		sprintf( _n( '%d termo movido.', '%d termos movidos.', $moved, 'imobiliaria-tema' ), $moved ),
	);
	if ( $unchanged ) {
		$parts[] = sprintf( _n( '%d já estava nesse pai.', '%d já estavam nesse pai.', $unchanged, 'imobiliaria-tema' ), $unchanged );
	}
	if ( $skipped ) {
		$parts[] = sprintf( _n( '%d ignorado (o pai não pode ser o próprio termo nem um filho dele).', '%d ignorados (o pai não pode ser o próprio termo nem um filho dele).', $skipped, 'imobiliaria-tema' ), $skipped );
	}

	printf(
		'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
		$moved ? 'success' : 'warning',
		esc_html( implode( ' ', $parts ) )
	);
}
add_action( 'admin_notices', 'imob_term_hierarchy_notice' );

/**
 * Seletor do pai ao lado do menu de ações em lote (aparece só com a ação escolhida).
 */
function imob_term_hierarchy_parent_selector() {
	$screen = get_current_screen();
	if ( ! $screen || 'edit-tags' !== $screen->base || empty( $screen->taxonomy ) ) {
		return;
	}
	if ( ! in_array( $screen->taxonomy, imob_hierarchy_taxonomies(), true ) ) {
		return;
	}

	$dropdown = wp_dropdown_categories(
		array(
			'taxonomy'          => $screen->taxonomy,
			'hide_empty'        => 0,
			'hierarchical'      => 1,
			'orderby'           => 'name',
			'name'              => 'imob_new_parent',
			'id'                => 'imob-new-parent',
			'class'             => 'imob-new-parent',
			'show_option_none'  => __( '— Nível raiz (sem pai) —', 'imobiliaria-tema' ),
			'option_none_value' => 0,
			'echo'              => 0,
		)
	);
	?>
	<template id="imob-new-parent-tpl">
		<span class="imob-new-parent-wrap" style="display:none;">
			<label for="imob-new-parent" class="screen-reader-text"><?php esc_html_e( 'Nova categoria pai', 'imobiliaria-tema' ); ?></label>
			<?php echo $dropdown; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- gerado pelo core. ?>
		</span>
	</template>
	<script>
	( function () {
		var tpl = document.getElementById( 'imob-new-parent-tpl' );
		var top = document.getElementById( 'bulk-action-selector-top' );
		var bottom = document.getElementById( 'bulk-action-selector-bottom' );
		if ( ! tpl || ! top ) {
			return;
		}
		top.parentNode.insertBefore( tpl.content.cloneNode( true ), top.nextSibling );
		var wrap = document.querySelector( '.imob-new-parent-wrap' );

		function sync( e ) {
			var other = e.target === top ? bottom : top;
			if ( other ) {
				other.value = e.target.value;
			}
			wrap.style.display = 'imob_move_parent' === e.target.value ? 'inline-block' : 'none';
			if ( e.target === bottom && 'imob_move_parent' === bottom.value ) {
				wrap.scrollIntoView( { block: 'center' } );
			}
		}
		top.addEventListener( 'change', sync );
		if ( bottom ) {
			bottom.addEventListener( 'change', sync );
		}
	} )();
	</script>
	<?php
}
add_action( 'admin_footer-edit-tags.php', 'imob_term_hierarchy_parent_selector' );

/**
 * Normaliza nomes de termos para comparação (entidades HTML, caixa e espaços).
 *
 * @param string $name Nome.
 * @return string
 */
function imob_hierarchy_normalize_name( $name ) {
	$name = html_entity_decode( (string) $name, ENT_QUOTES, 'UTF-8' );
	$name = preg_replace( '/\s+/u', ' ', trim( $name ) );
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $name, 'UTF-8' ) : strtolower( $name );
}

/**
 * Refaz a hierarquia pai/filho das taxonomias importadas do RealHomes.
 *
 * Lê os pares filho → pai das taxonomias antigas direto do banco (elas não estão mais
 * registradas) e aplica nas taxonomias novas, casando os termos pelo nome. Nomes de
 * filhos que tinham pais diferentes no RealHomes são ignorados (ambíguos).
 *
 * @return array<string,array{moved:int,skipped:int,unchanged:int,missing:int,ambiguous:int}>
 */
function imob_restore_realhomes_hierarchy() {
	global $wpdb;

	$map = array(
		'property-type'   => 'tipo_imovel',
		'property-city'   => 'localidade',
		'property-status' => 'status_imovel',
	);

	$report = array();

	foreach ( $map as $old_tax => $new_tax ) {
		if ( ! taxonomy_exists( $new_tax ) || ! is_taxonomy_hierarchical( $new_tax ) ) {
			continue;
		}

		$pairs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT c.name AS child_name, p.name AS parent_name
				 FROM {$wpdb->term_taxonomy} ctt
				 INNER JOIN {$wpdb->terms} c ON c.term_id = ctt.term_id
				 INNER JOIN {$wpdb->term_taxonomy} ptt ON ptt.term_id = ctt.parent AND ptt.taxonomy = ctt.taxonomy
				 INNER JOIN {$wpdb->terms} p ON p.term_id = ptt.term_id
				 WHERE ctt.taxonomy = %s AND ctt.parent > 0",
				$old_tax
			)
		);

		$stats = array( 'moved' => 0, 'skipped' => 0, 'unchanged' => 0, 'missing' => 0, 'ambiguous' => 0 );

		if ( empty( $pairs ) ) {
			$report[ $new_tax ] = $stats;
			continue;
		}

		// Índice nome normalizado → term_id na taxonomia nova.
		$new_terms = get_terms(
			array(
				'taxonomy'   => $new_tax,
				'hide_empty' => false,
				'fields'     => 'id=>name',
			)
		);
		$by_name = array();
		if ( ! is_wp_error( $new_terms ) ) {
			foreach ( $new_terms as $id => $name ) {
				$key = imob_hierarchy_normalize_name( $name );
				// Nome repetido na taxonomia nova: marca como ambíguo.
				$by_name[ $key ] = isset( $by_name[ $key ] ) ? false : (int) $id;
			}
		}

		// Agrupa filho → pais distintos.
		$child_parents = array();
		foreach ( $pairs as $pair ) {
			$child  = imob_hierarchy_normalize_name( $pair->child_name );
			$parent = imob_hierarchy_normalize_name( $pair->parent_name );

			$child_parents[ $child ][ $parent ] = true;
		}

		// Agrupa por pai para mover em lote.
		$groups = array();
		foreach ( $child_parents as $child => $parents ) {
			if ( count( $parents ) > 1 ) {
				$stats['ambiguous']++;
				continue;
			}
			$parent    = key( $parents );
			$child_id  = isset( $by_name[ $child ] ) ? $by_name[ $child ] : null;
			$parent_id = isset( $by_name[ $parent ] ) ? $by_name[ $parent ] : null;

			if ( false === $child_id || false === $parent_id ) {
				$stats['ambiguous']++;
				continue;
			}
			if ( ! $child_id || ! $parent_id ) {
				$stats['missing']++;
				continue;
			}
			$groups[ $parent_id ][] = $child_id;
		}

		foreach ( $groups as $parent_id => $child_ids ) {
			$res                 = imob_move_terms_to_parent( $new_tax, $child_ids, $parent_id );
			$stats['moved']     += $res['moved'];
			$stats['skipped']   += $res['skipped'];
			$stats['unchanged'] += $res['unchanged'];
		}

		$report[ $new_tax ] = $stats;
	}

	return $report;
}

/**
 * Botão "Restaurar hierarquia" em Ferramentas > Importar do RealHomes.
 */
function imob_render_hierarchy_restore_box() {
	$report = get_transient( 'imob_rh_hierarchy_report_' . get_current_user_id() );
	?>
	<hr style="margin: 30px 0;">
	<h2><?php esc_html_e( 'Restaurar hierarquia das categorias', 'imobiliaria-tema' ); ?></h2>
	<p>
		<?php esc_html_e( 'A importação trouxe tipos, localidades e status sem os vínculos de pai e filho. Este botão lê a estrutura original do RealHomes (ainda salva no banco) e move cada filho para dentro do pai correspondente, casando pelo nome.', 'imobiliaria-tema' ); ?>
	</p>
	<p>
		<?php esc_html_e( 'Para ajustes manuais, abra a lista de termos (ex.: Imóveis > Localidades), marque os filhos, escolha "Mover para a categoria pai…" nas ações em lote, selecione o pai e clique em Aplicar.', 'imobiliaria-tema' ); ?>
	</p>

	<?php if ( isset( $_GET['imob_rh_restored'] ) && is_array( $report ) ) : ?>
		<div class="notice notice-success inline"><p><strong><?php esc_html_e( 'Hierarquia restaurada:', 'imobiliaria-tema' ); ?></strong></p>
			<ul style="list-style: disc; margin-left: 20px;">
				<?php foreach ( $report as $tax => $s ) : ?>
					<?php $tax_obj = get_taxonomy( $tax ); ?>
					<li>
						<strong><?php echo esc_html( $tax_obj ? $tax_obj->labels->name : $tax ); ?>:</strong>
						<?php
						echo esc_html(
							sprintf(
								/* translators: 1: movidos, 2: já corretos, 3: não encontrados, 4: ambíguos, 5: ignorados */
								__( '%1$d movidos, %2$d já estavam corretos, %3$d não encontrados, %4$d ambíguos, %5$d ignorados.', 'imobiliaria-tema' ),
								$s['moved'],
								$s['unchanged'],
								$s['missing'],
								$s['ambiguous'],
								$s['skipped']
							)
						);
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="imob_restore_hierarchy">
		<?php wp_nonce_field( 'imob_restore_hierarchy' ); ?>
		<?php submit_button( __( 'Restaurar hierarquia do RealHomes', 'imobiliaria-tema' ), 'secondary', 'submit', false ); ?>
	</form>
	<?php
}

/**
 * Handler do botão de restauração.
 */
function imob_handle_restore_hierarchy() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permissão negada.', 'imobiliaria-tema' ) );
	}
	check_admin_referer( 'imob_restore_hierarchy' );

	$report = imob_restore_realhomes_hierarchy();
	set_transient( 'imob_rh_hierarchy_report_' . get_current_user_id(), $report, 10 * MINUTE_IN_SECONDS );

	wp_safe_redirect( admin_url( 'tools.php?page=imob-realhomes-importer&imob_rh_restored=1' ) );
	exit;
}
add_action( 'admin_post_imob_restore_hierarchy', 'imob_handle_restore_hierarchy' );
