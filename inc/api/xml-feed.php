<?php
/**
 * XML Feed for Real Estate Portals (ZAP, VivaReal, OLX)
 */

function imob_add_feed_endpoint() {
	add_feed( 'imoveis-xml', 'imob_render_xml_feed' );
}
add_action( 'init', 'imob_add_feed_endpoint' );

function imob_render_xml_feed() {
	header( 'Content-Type: application/xml; charset=' . get_option( 'blog_charset' ), true );
	
	echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
	?>
	<Carga>
		<Imoveis>
			<?php
			$args = array(
				'post_type'      => 'imovel',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			);
			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = get_the_ID();
					$ref = get_post_meta( $post_id, '_imob_ref', true );
					$preco_venda = get_post_meta( $post_id, '_imob_preco_venda', true );
					$area = get_post_meta( $post_id, '_imob_area_privativa', true );
					$quartos = get_post_meta( $post_id, '_imob_quartos', true );
					$suites = get_post_meta( $post_id, '_imob_suites', true );
					$banheiros = get_post_meta( $post_id, '_imob_banheiros', true );
					$vagas = get_post_meta( $post_id, '_imob_vagas', true );
					
					// Basic mapping for example
					?>
					<Imovel>
						<CodigoImovel><?php echo esc_html( $ref ? $ref : $post_id ); ?></CodigoImovel>
						<TipoImovel>Apartamento</TipoImovel> <!-- simplified -->
						<SubTipoImovel>Padrão</SubTipoImovel>
						<TituloImovel><![CDATA[<?php the_title(); ?>]]></TituloImovel>
						<Observacao><![CDATA[<?php echo wp_strip_all_tags( get_the_excerpt() ); ?>]]></Observacao>
						<PrecoVenda><?php echo esc_html( $preco_venda ); ?></PrecoVenda>
						<AreaUtil><?php echo esc_html( $area ); ?></AreaUtil>
						<QtdDormitorios><?php echo esc_html( $quartos ); ?></QtdDormitorios>
						<QtdSuites><?php echo esc_html( $suites ); ?></QtdSuites>
						<QtdBanheiros><?php echo esc_html( $banheiros ); ?></QtdBanheiros>
						<QtdVagas><?php echo esc_html( $vagas ); ?></QtdVagas>
						<URLImovel><?php the_permalink(); ?></URLImovel>
					</Imovel>
					<?php
				}
				wp_reset_postdata();
			}
			?>
		</Imoveis>
	</Carga>
	<?php
}
