<?php
/**
 * Página inicial.
 *
 * A home é montada no Elementor com os widgets "Imobiliária - Seções do site".
 * Este arquivo só é usado quando a página inicial não foi montada no Elementor:
 * mostra as mesmas seções com os textos padrão do layout do Figma.
 *
 * @package ImobiliariaTema
 */

get_header();

$imob_front_id = (int) get_option( 'page_on_front' );
$imob_built    = $imob_front_id && class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( $imob_front_id ) && \Elementor\Plugin::$instance->documents->get( $imob_front_id )->is_built_with_elementor();
?>

<main id="primary" class="site-main hm">
	<?php
	if ( $imob_built ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	} else {
		foreach ( imob_home_default_layout() as $imob_section ) {
			imob_render_section( $imob_section[0], $imob_section[1] );
		}
	}
	?>
</main>

<?php
get_footer();
