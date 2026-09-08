<?php
/**
 * Elementor Widget: Blog Grid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_blog_grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_blog_grid';
	}

	public function get_title() {
		return __( 'Blog Grid', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Conteúdo', 'imobiliaria-tema' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Quantidade de Posts', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 4,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => $settings['posts_per_page'],
		);

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			echo '<div class="imob-blog-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">';
			while ( $query->have_posts() ) {
				$query->the_post();
				$bg_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
				if ( ! $bg_url ) {
					$bg_url = 'https://via.placeholder.com/600x400.png?text=Blog';
				}
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="border-radius: 10px; overflow: hidden; position: relative; height: 350px; background-image: url('<?php echo esc_url($bg_url); ?>'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: space-between; padding: 20px;">
					<div style="position: absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.8)); z-index: 1;"></div>
					
					<div style="position: relative; z-index: 2;">
						<span style="background: var(--accent-color); color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Novo</span>
					</div>
					
					<div style="position: relative; z-index: 2; color: #fff;">
						<?php the_title( '<h3 style="margin: 0 0 10px; font-size: 1.2rem; font-weight: 700; line-height: 1.3;"><a href="' . esc_url( get_permalink() ) . '" style="color: #fff;">', '</a></h3>' ); ?>
						<div style="font-size: 0.8rem; margin-bottom: 10px; line-height: 1.4; opacity: 0.9;">
							<?php echo wp_trim_words( get_the_excerpt(), 15, '...' ); ?>
						</div>
						<div style="display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: var(--accent-color); font-weight: 600;">
							<span class="material-symbols-outlined" style="font-size: 16px;">schedule</span> <?php echo get_the_date(); ?>
						</div>
					</div>
				</article>
				<?php
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>' . __( 'Nenhum post encontrado.', 'imobiliaria-tema' ) . '</p>';
		}
	}
}
