<?php
/**
 * Tema Imobiliário Customizado - functions.php
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'IMOB_THEME_VERSION', '1.4.5' );
define( 'IMOB_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'IMOB_THEME_URL', trailingslashit( get_template_directory_uri() ) );

/**
 * Autoload function for classes (if needed later) or require standard files.
 * Taxonomies are loaded before Post Types so rewrite tags (e.g. %localidade%)
 * are available when post type permastructs are registered.
 */
$imob_includes = [
	'inc/core/setup.php',
	'inc/core/enqueue.php',
	'inc/core/search-logic.php',
	'inc/core/theme-options.php',
	'inc/core/user-profile.php',
	'inc/core/seo.php',
	'inc/core/watermark.php',
	'inc/core/image-optimizer.php',
	'inc/taxonomies/tipo_imovel.php',
	'inc/taxonomies/localidade.php',
	'inc/taxonomies/caracteristica.php',
	'inc/taxonomies/estagio_obra.php',
	// 'inc/taxonomies/finalidade.php', // Removido conforme solicitação
	'inc/taxonomies/status_imovel.php',
	'inc/taxonomies/corretor.php',
	'inc/post-types/imovel.php',
	'inc/post-types/construtora.php',
	'inc/post-types/proprietario.php',
	'inc/post-types/empreendimento.php',
	'inc/post-types/depoimento.php',
	'inc/meta-boxes/imovel-meta.php',
	'inc/meta-boxes/empreendimento-meta.php',
	'inc/meta-boxes/construtora-meta.php',
	'inc/meta-boxes/proprietario-meta.php',
	'inc/elementor/widgets-registration.php',
	'inc/elementor/dynamic-tags.php',
	'inc/seo/schema.php',
	'inc/api/xml-feed.php',
	'inc/importers/realhomes-importer.php',
	'inc/home/home-options.php',
	'inc/home/lead-form.php',
	'inc/sections/sections.php',
	'inc/sections/layouts.php',
	'inc/core/theme-updater.php',
	'inc/core/footer.php',
	'inc/core/contact-cta.php',
	'inc/blog/post-cta.php',
	'inc/landing/black-friday.php',
	'inc/content/seed-content.php',
];

foreach ( $imob_includes as $file ) {
	$filepath = IMOB_THEME_DIR . $file;
	if ( file_exists( $filepath ) ) {
		require_once $filepath;
	}
}

/**
 * Garante que as regras de rewrite (permalinks) de todos os CPTs e taxonomias
 * estejam sempre atualizadas no banco de dados sem gerar erros 404.
 */
function imob_maybe_flush_rewrite_rules() {
	$rules = get_option( 'rewrite_rules' );
	$saved_version = get_option( 'imob_rewrite_version' );

	$needs_flush = ( $saved_version !== IMOB_THEME_VERSION )
		|| ! is_array( $rules )
		|| ! isset( $rules['empreendimentos/?$'] )
		|| ! isset( $rules['construtoras/?$'] )
		|| ! isset( $rules['^imoveis/(?!page/)([^/]+)/([^/]+)/?$'] )
		|| ! isset( $rules['^sitemap-news\.xml$'] )
		|| ! isset( $rules['^llms\.txt$'] );

	if ( $needs_flush ) {
		flush_rewrite_rules( false );
		update_option( 'imob_rewrite_version', IMOB_THEME_VERSION );
	}
}
add_action( 'init', 'imob_maybe_flush_rewrite_rules', 99 );

/**
 * Corrige URLs legadas (http://localhost/vinicius) que possam estar salvas em templates do Elementor
 */
function imob_fix_legacy_localhost_urls( $content ) {
	if ( empty( $content ) || strpos( $content, 'localhost/vinicius' ) === false ) {
		return $content;
	}
	$home = untrailingslashit( home_url() );
	$content = str_replace(
		array( 'http://localhost/vinicius', 'https://localhost/vinicius' ),
		$home,
		$content
	);
	return $content;
}
add_filter( 'the_content', 'imob_fix_legacy_localhost_urls', 20 );
add_filter( 'elementor/frontend/builder_content_data', function( $data ) {
	return $data;
}, 10 );
add_filter( 'elementor/frontend/the_content', 'imob_fix_legacy_localhost_urls', 20 );

/**
 * UTF-8 Multibyte uppercase helper for badges and labels
 */
function imob_strtoupper( $string ) {
	if ( empty( $string ) ) {
		return '';
	}
	if ( function_exists( 'mb_strtoupper' ) ) {
		return mb_strtoupper( (string) $string, 'UTF-8' );
	}
	return strtoupper( (string) $string );
}

/**
 * Get formatted price with before/after texts and previous price
 */
function imob_get_formatted_price( $post_id, $is_single = false ) {
	$preco_venda = get_post_meta( $post_id, '_imob_preco_venda', true );
	$preco_antes = get_post_meta( $post_id, '_imob_preco_antes_texto', true );
	$preco_depois = get_post_meta( $post_id, '_imob_preco_depois_texto', true );
	$preco_anterior = get_post_meta( $post_id, '_imob_preco_anterior', true );

	if ( ! $preco_venda ) {
		return '';
	}

	$html = '';
	
	if ( $is_single ) {
		$html .= '<div class="imob-single-price-wrap" style="background: #0E1A2B; color: #fff; padding: 10px 25px; border-radius: 8px; display: inline-block; margin-bottom: 20px;">';
		if ( $preco_anterior ) {
			$html .= '<div class="imob-preco-anterior" style="color: rgba(255,255,255,0.7); text-decoration: line-through; font-size: 0.9rem; margin-bottom: 5px;">De: R$ ' . number_format( (float) $preco_anterior, 2, ',', '.' ) . '</div>';
			$html .= '<div class="imob-preco-atual" style="display: flex; align-items: baseline; gap: 8px;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes" style="font-size: 1rem; font-weight: 600;">' . esc_html( $preco_antes ) . ' </span>';
			$html .= '<span class="imob-txt-por" style="font-size: 1rem;">Por: </span>';
			$html .= '<span class="imob-txt-valor" style="font-size: 1.5rem; font-weight: 800; color: #fff;">R$ ' . number_format( (float) $preco_venda, 2, ',', '.' ) . '</span>';
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 1rem;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		} else {
			$html .= '<div class="imob-preco-atual" style="display: flex; align-items: baseline; gap: 8px;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes" style="font-size: 1rem; font-weight: 600;">' . esc_html( $preco_antes ) . ' </span>';
			$html .= '<span class="imob-txt-valor" style="font-size: 1.5rem; font-weight: 800; color: #fff;">R$ ' . number_format( (float) $preco_venda, 2, ',', '.' ) . '</span>';
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 1rem;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		}
		$html .= '</div>';
	} else {
		// Card format
		$html .= '<div class="imob-card-price-badge" style="display: inline-block; line-height: 1.2;">';
		if ( $preco_anterior ) {
			$html .= '<div class="imob-preco-anterior" style="font-size: 0.75rem; text-decoration: line-through; opacity: 0.7;">De: R$ ' . number_format( (float) $preco_anterior, 2, ',', '.' ) . '</div>';
			$html .= '<div class="imob-preco-atual" style="font-size: 1rem; font-weight: 700;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes">' . esc_html( $preco_antes ) . ' </span>';
			$html .= 'Por: R$ ' . number_format( (float) $preco_venda, 2, ',', '.' );
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 0.8rem; font-weight: normal;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		} else {
			$html .= '<div class="imob-preco-atual" style="font-size: 1rem; font-weight: 700;">';
			if ( $preco_antes ) $html .= '<span class="imob-txt-antes" style="font-size: 0.8rem; font-weight: normal;">' . esc_html( $preco_antes ) . ' </span>';
			$html .= 'R$ ' . number_format( (float) $preco_venda, 2, ',', '.' );
			if ( $preco_depois ) $html .= '<span class="imob-txt-depois" style="font-size: 0.8rem; font-weight: normal;"> ' . esc_html( $preco_depois ) . '</span>';
			$html .= '</div>';
		}
		$html .= '</div>';
	}

	return $html;
}

/**
 * Recupera dados do empreendimento vinculado a um imóvel
 *
 * @param int $post_id ID do imóvel
 * @return array|null Array com ['id', 'nome', 'url'] ou null
 */
function imob_get_imovel_empreendimento( $post_id ) {
	$emp_id = get_post_meta( $post_id, '_imob_empreendimento_id', true );
	if ( ! empty( $emp_id ) && is_numeric( $emp_id ) ) {
		$emp_post = get_post( (int) $emp_id );
		if ( $emp_post && 'empreendimento' === $emp_post->post_type ) {
			return array(
				'id'   => $emp_post->ID,
				'nome' => $emp_post->post_title,
				'url'  => get_permalink( $emp_post->ID ),
			);
		}
	}

	// Fallback para campo legado em texto
	$emp_text = get_post_meta( $post_id, '_imob_empreendimento', true );
	if ( ! empty( $emp_text ) ) {
		$found = get_page_by_title( $emp_text, OBJECT, 'empreendimento' );
		if ( $found ) {
			return array(
				'id'   => $found->ID,
				'nome' => $found->post_title,
				'url'  => get_permalink( $found->ID ),
			);
		}
		return array(
			'id'   => 0,
			'nome' => $emp_text,
			'url'  => '',
		);
	}

	return null;
}

/**
 * Renderiza o badge HTML do empreendimento com formatação e link se disponível
 *
 * @param int $post_id ID do imóvel
 * @param bool $is_single Se está sendo renderizado na página single
 * @return string HTML do badge
 */
function imob_render_empreendimento_badge( $post_id, $is_single = false ) {
	$emp = imob_get_imovel_empreendimento( $post_id );
	if ( ! $emp || empty( $emp['nome'] ) ) {
		return '';
	}

	$nome = esc_html( imob_strtoupper( $emp['nome'] ) );
	$icon = '<span class="material-symbols-outlined" style="font-size: 13px; vertical-align: middle;">domain</span>';

	if ( ! empty( $emp['url'] ) ) {
		$class = $is_single ? 'imob-single-badge-empreendimento badge-empreendimento' : 'badge-empreendimento';
		return sprintf(
			'<a href="%s" class="%s" title="%s">%s %s</a>',
			esc_url( $emp['url'] ),
			esc_attr( $class ),
			esc_attr__( 'Ver Empreendimento', 'imobiliaria-tema' ),
			$icon,
			$nome
		);
	}

	$class = $is_single ? 'imob-single-badge-empreendimento badge-empreendimento' : 'badge-empreendimento';
	return sprintf(
		'<span class="%s">%s %s</span>',
		esc_attr( $class ),
		$icon,
		$nome
	);
}

/**
 * Recupera dados da construtora vinculada a um imóvel
 *
 * @param int $post_id ID do imóvel
 * @return array|null Array com ['id', 'nome', 'url'] ou null
 */
function imob_get_imovel_construtora( $post_id ) {
	$const_id = get_post_meta( $post_id, '_imob_construtora_id', true );
	if ( ! empty( $const_id ) && is_numeric( $const_id ) ) {
		$const_post = get_post( (int) $const_id );
		if ( $const_post && 'construtora' === $const_post->post_type ) {
			return array(
				'id'   => $const_post->ID,
				'nome' => $const_post->post_title,
				'url'  => get_permalink( $const_post->ID ),
			);
		}
	}

	// Fallback por texto legado
	$const_text = get_post_meta( $post_id, '_imob_construtora', true );
	if ( ! empty( $const_text ) ) {
		$found = get_page_by_title( $const_text, OBJECT, 'construtora' );
		if ( $found ) {
			return array(
				'id'   => $found->ID,
				'nome' => $found->post_title,
				'url'  => get_permalink( $found->ID ),
			);
		}
	}

	// Fallback: verificar se o empreendimento vinculado possui construtora
	$emp = imob_get_imovel_empreendimento( $post_id );
	if ( $emp && ! empty( $emp['id'] ) ) {
		$emp_const_id = get_post_meta( $emp['id'], '_imob_emp_construtora_id', true );
		if ( ! empty( $emp_const_id ) ) {
			$const_post = get_post( (int) $emp_const_id );
			if ( $const_post && 'construtora' === $const_post->post_type ) {
				return array(
					'id'   => $const_post->ID,
					'nome' => $const_post->post_title,
					'url'  => get_permalink( $const_post->ID ),
				);
			}
		}
	}

	return null;
}

/**
 * Renderiza o badge HTML da construtora com link para a página da construtora
 *
 * @param int $post_id ID do imóvel
 * @param bool $is_single
 * @return string HTML do badge
 */
function imob_render_construtora_badge( $post_id, $is_single = false ) {
	$const = imob_get_imovel_construtora( $post_id );
	if ( ! $const || empty( $const['nome'] ) ) {
		return '';
	}

	$nome = esc_html( imob_strtoupper( $const['nome'] ) );
	$icon = '<span class="material-symbols-outlined" style="font-size: 13px; vertical-align: middle;">apartment</span>';
	$class = $is_single ? 'imob-single-badge-construtora badge-construtora' : 'badge-construtora';

	if ( ! empty( $const['url'] ) ) {
		return sprintf(
			'<a href="%s" class="%s" title="%s">%s %s</a>',
			esc_url( $const['url'] ),
			esc_attr( $class ),
			esc_attr__( 'Ver todos os imóveis desta Construtora', 'imobiliaria-tema' ),
			$icon,
			$nome
		);
	}

	return sprintf(
		'<span class="%s">%s %s</span>',
		esc_attr( $class ),
		$icon,
		$nome
	);
}

/**
 * Retorna os dados do estágio da obra do empreendimento com nome e link
 *
 * @param int $emp_id ID do empreendimento
 * @return array|null
 */
function imob_get_empreendimento_estagio_data( $emp_id ) {
	if ( empty( $emp_id ) ) {
		return null;
	}

	// 1. Tenta obter da taxonomia estagio_obra associada diretamente
	$terms = wp_get_post_terms( $emp_id, 'estagio_obra' );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$term = $terms[0];
		$term_link = get_term_link( $term );
		return array(
			'name' => $term->name,
			'url'  => ! is_wp_error( $term_link ) ? $term_link : '',
			'slug' => $term->slug,
		);
	}

	// 2. Fallback para meta campo _imob_emp_estagio
	$estagio = get_post_meta( $emp_id, '_imob_emp_estagio', true );
	if ( ! empty( $estagio ) ) {
		$labels = array(
			'Lancamento'    => __( 'Lançamento', 'imobiliaria-tema' ),
			'lancamento'    => __( 'Lançamento', 'imobiliaria-tema' ),
			'Em Construcao' => __( 'Em Construção', 'imobiliaria-tema' ),
			'em-construcao' => __( 'Em Construção', 'imobiliaria-tema' ),
			'Pronto'        => __( 'Pronto para Morar', 'imobiliaria-tema' ),
			'pronto'        => __( 'Pronto para Morar', 'imobiliaria-tema' ),
			'Na Planta'     => __( 'Na Planta', 'imobiliaria-tema' ),
			'na-planta'     => __( 'Na Planta', 'imobiliaria-tema' ),
		);
		$name = isset( $labels[ $estagio ] ) ? $labels[ $estagio ] : $estagio;
		$slug = sanitize_title( $name );

		// Tenta encontrar o termo na taxonomia
		$found_term = get_term_by( 'slug', $slug, 'estagio_obra' );
		if ( ! $found_term ) {
			$found_term = get_term_by( 'name', $name, 'estagio_obra' );
		}

		$url = '';
		if ( $found_term && ! is_wp_error( $found_term ) ) {
			$term_link = get_term_link( $found_term );
			if ( ! is_wp_error( $term_link ) ) {
				$url = $term_link;
			}
		}

		if ( empty( $url ) ) {
			$tax_obj = get_taxonomy( 'estagio_obra' );
			$url = add_query_arg( 'estagio', $slug, home_url( '/imoveis/' ) );
		}

		return array(
			'name' => $name,
			'url'  => $url,
			'slug' => $slug,
		);
	}

	return null;
}

/**
 * Retorna os tipos de imóvel do empreendimento com nomes e links
 *
 * @param int $emp_id ID do empreendimento
 * @return array
 */
function imob_get_empreendimento_tipo_data( $emp_id ) {
	if ( empty( $emp_id ) ) {
		return array();
	}

	$tipos = array();

	// 1. Termos atribuídos diretamente ao empreendimento
	$terms = wp_get_post_terms( $emp_id, 'tipo_imovel' );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$link = get_term_link( $term );
			$tipos[ $term->term_id ] = array(
				'name' => $term->name,
				'url'  => ! is_wp_error( $link ) ? $link : '',
				'slug' => $term->slug,
			);
		}
	}

	// 2. Se não houver termos diretos, busca das unidades deste empreendimento
	if ( empty( $tipos ) ) {
		$imoveis = get_posts( array(
			'post_type'      => 'imovel',
			'posts_per_page' => 15,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_imob_empreendimento_id',
					'value'   => $emp_id,
					'compare' => '=',
				),
				array(
					'key'     => '_imob_empreendimento',
					'value'   => get_the_title( $emp_id ),
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
		) );

		if ( ! empty( $imoveis ) ) {
			foreach ( $imoveis as $imv_id ) {
				$imv_terms = wp_get_post_terms( $imv_id, 'tipo_imovel' );
				if ( ! empty( $imv_terms ) && ! is_wp_error( $imv_terms ) ) {
					foreach ( $imv_terms as $it ) {
						if ( ! isset( $tipos[ $it->term_id ] ) ) {
							$link = get_term_link( $it );
							$tipos[ $it->term_id ] = array(
								'name' => $it->name,
								'url'  => ! is_wp_error( $link ) ? $link : '',
								'slug' => $it->slug,
							);
						}
					}
				}
			}
		}
	}

	return array_values( $tipos );
}

/**
 * Renderiza os badges do empreendimento (Estágio da Obra e Tipo de Imóvel) com links para listagens
 *
 * @param int $emp_id ID do empreendimento
 * @return string HTML dos badges
 */
function imob_render_empreendimento_badges( $emp_id ) {
	$output = '';

	// 1. Badge do Estágio da Obra com Link
	$estagio = imob_get_empreendimento_estagio_data( $emp_id );
	if ( $estagio && ! empty( $estagio['name'] ) ) {
		$estagio_name = esc_html( imob_strtoupper( $estagio['name'] ) );
		$icon = '<span class="material-symbols-outlined" style="font-size: 13px; vertical-align: middle;">construction</span>';
		if ( ! empty( $estagio['url'] ) ) {
			$output .= sprintf(
				'<a href="%s" class="badge-estagio" title="%s">%s %s</a>',
				esc_url( $estagio['url'] ),
				esc_attr( sprintf( __( 'Ver imóveis em estágio: %s', 'imobiliaria-tema' ), $estagio['name'] ) ),
				$icon,
				$estagio_name
			);
		} else {
			$output .= sprintf(
				'<span class="badge-estagio">%s %s</span>',
				$icon,
				$estagio_name
			);
		}
	}

	// 2. Badges dos Tipos de Imóvel com Link
	$tipos = imob_get_empreendimento_tipo_data( $emp_id );
	if ( ! empty( $tipos ) ) {
		foreach ( $tipos as $tipo ) {
			$tipo_name = esc_html( imob_strtoupper( $tipo['name'] ) );
			$icon = '<span class="material-symbols-outlined" style="font-size: 13px; vertical-align: middle;">apartment</span>';
			if ( ! empty( $tipo['url'] ) ) {
				$output .= sprintf(
					'<a href="%s" class="badge-tipo" title="%s">%s %s</a>',
					esc_url( $tipo['url'] ),
					esc_attr( sprintf( __( 'Ver imóveis do tipo: %s', 'imobiliaria-tema' ), $tipo['name'] ) ),
					$icon,
					$tipo_name
				);
			} else {
				$output .= sprintf(
					'<span class="badge-tipo">%s %s</span>',
					$icon,
					$tipo_name
				);
			}
		}
	}

	return $output;
}

/**
 * Slugs das localidades que são cidades (não devem aparecer como "Bairro").
 * Configurável em Personalizar > Imóveis: localidades. Termos que têm termos filhos também contam como cidade.
 *
 * @return string[]
 */
function imob_get_city_slugs() {
	$raw = (string) get_theme_mod( 'imob_city_slugs', 'campina-grande, joao-pessoa' );
	return array_filter( array_map( 'sanitize_title', array_map( 'trim', explode( ',', $raw ) ) ) );
}

/**
 * O termo é uma cidade (está na lista de cidades ou tem bairros filhos)?
 *
 * @param WP_Term $term Termo.
 * @return bool
 */
function imob_localidade_is_city( $term ) {
	static $cache = array();
	$id = (int) $term->term_id;
	if ( ! isset( $cache[ $id ] ) ) {
		$cache[ $id ] = in_array( $term->slug, imob_get_city_slugs(), true )
			|| (bool) get_terms(
				array(
					'taxonomy'   => 'localidade',
					'parent'     => $id,
					'hide_empty' => false,
					'number'     => 1,
					'fields'     => 'ids',
				)
			);
	}
	return $cache[ $id ];
}

/**
 * Localidade a exibir de um imóvel (bairro).
 *
 * 1. Descarta cidades (lista em Personalizar ou termos com filhos) e termos que são pais de outro
 *    termo marcado, desde que reste algum bairro: com "Campina Grande" + "Catolé", mostra "Catolé".
 * 2. Entre os bairros que sobraram, dá preferência ao marcado como primário (Yoast SEO ou Rank Math).
 * 3. Sem primário válido, usa o bairro mais específico (mais fundo na hierarquia) ou o primeiro.
 * 4. Se o imóvel só tiver a cidade, mostra a cidade.
 *
 * @param int $post_id ID do imóvel.
 * @return WP_Term|null
 */
function imob_get_localidade_term( $post_id ) {
	$terms = wp_get_post_terms( $post_id, 'localidade' );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	$candidates = array();
	foreach ( $terms as $t ) {
		$is_parent_of_other = false;
		foreach ( $terms as $other ) {
			if ( (int) $other->parent === (int) $t->term_id ) {
				$is_parent_of_other = true;
				break;
			}
		}
		if ( ! $is_parent_of_other && ! imob_localidade_is_city( $t ) ) {
			$candidates[ (int) $t->term_id ] = $t;
		}
	}
	if ( ! $candidates ) {
		// Só há cidade(s): mantém o primário, se houver, ou o primeiro termo.
		foreach ( $terms as $t ) {
			$candidates[ (int) $t->term_id ] = $t;
		}
	}

	foreach ( array( '_yoast_wpseo_primary_localidade', 'rank_math_primary_localidade' ) as $meta_key ) {
		$primary = (int) get_post_meta( $post_id, $meta_key, true );
		if ( $primary && isset( $candidates[ $primary ] ) ) {
			return $candidates[ $primary ];
		}
	}

	$best       = null;
	$best_depth = -1;
	foreach ( $candidates as $t ) {
		$depth = count( get_ancestors( $t->term_id, 'localidade', 'taxonomy' ) );
		if ( $depth > $best_depth ) {
			$best       = $t;
			$best_depth = $depth;
		}
	}
	return $best;
}

/**
 * Personalizar > Imóveis: localidades.
 *
 * @param WP_Customize_Manager $wp_customize Gerenciador do Customizer.
 */
function imob_localidade_customize( $wp_customize ) {
	$wp_customize->add_section(
		'imob_localidades',
		array(
			'title'       => __( 'Imóveis: localidades', 'imobiliaria-tema' ),
			'description' => __( 'Define quais localidades são cidades. Nas listagens, "Bairro" mostra o bairro marcado no imóvel e nunca a cidade (a cidade só aparece quando o imóvel não tem bairro).', 'imobiliaria-tema' ),
			'priority'    => 125,
		)
	);
	$wp_customize->add_setting(
		'imob_city_slugs',
		array(
			'default'           => 'campina-grande, joao-pessoa',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'imob_city_slugs',
		array(
			'label'       => __( 'Slugs das cidades (separados por vírgula)', 'imobiliaria-tema' ),
			'description' => __( 'Ex.: campina-grande, joao-pessoa, cabedelo. Localidades com bairros filhos já são tratadas como cidade automaticamente.', 'imobiliaria-tema' ),
			'section'     => 'imob_localidades',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'imob_localidade_customize' );

/**
 * Nome da localidade a exibir (ver imob_get_localidade_term()).
 *
 * @param int    $post_id  ID do imóvel.
 * @param string $fallback Texto quando não houver localidade.
 * @return string
 */
function imob_get_localidade_name( $post_id, $fallback = '' ) {
	$term = imob_get_localidade_term( $post_id );
	return $term ? $term->name : $fallback;
}
