<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'imobiliaria-tema' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="header-container container">
			<div class="site-branding">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-text">
						IS22 <span>IMÓVEIS</span> | VINICIUS <span>MATIAS</span>
					</a>
				<?php endif; ?>
			</div><!-- .site-branding -->

			<nav id="site-navigation" class="main-navigation">
				<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="material-symbols-outlined">menu</span>
				</button>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
				<!-- Fallback if menu is empty -->
				<?php if ( ! has_nav_menu( 'menu-1' ) ) : ?>
					<ul id="primary-menu" class="menu">
						<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Início</a></li>
						<li><a href="#">Sobre</a></li>
						<li class="menu-item-has-children"><a href="#">Imóveis</a></li>
						<li><a href="#">Avaliações</a></li>
						<li><a href="#">Blog</a></li>
					</ul>
				<?php endif; ?>
			</nav><!-- #site-navigation -->

			<div class="header-cta">
				<a href="#" class="btn-primary">
					<span class="material-symbols-outlined">home</span> Realize o seu sonho
				</a>
			</div>
		</div><!-- .header-container -->
	</header><!-- #masthead -->
