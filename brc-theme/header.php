<?php
/**
 * Site header.
 *
 * @package BRC
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'brc' ); ?></a>

<header class="site-header" data-site-header>
	<div class="site-header__inner">
		<div class="site-brand">
			<?php brc_theme_logo(); ?>
		</div>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary menu', 'brc' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__menu',
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>
		</nav>

		<div class="site-header__actions">
			<a class="site-header__utility site-header__utility--lang" href="#">
				<?php esc_html_e( 'عربي', 'brc' ); ?>
			</a>
			<a class="site-header__icon-link site-header__icon-link--search" href="#" aria-label="<?php esc_attr_e( 'Search', 'brc' ); ?>">
				<span></span>
			</a>
			<a class="site-header__icon-link site-header__icon-link--whatsapp" href="#" aria-label="<?php esc_attr_e( 'Contact on WhatsApp', 'brc' ); ?>">
				<span></span>
			</a>
			<a class="site-header__help" href="#lead-form">
				<?php esc_html_e( 'Need Help', 'brc' ); ?>
			</a>
			<button class="site-nav-toggle" type="button" aria-expanded="false" aria-controls="site-mobile-nav" data-nav-toggle>
				<span><?php esc_html_e( 'Menu', 'brc' ); ?></span>
			</button>
		</div>
	</div>

	<nav id="site-mobile-nav" class="site-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile menu', 'brc' ); ?>" hidden data-mobile-nav>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'site-mobile-nav__menu',
				'fallback_cb'    => false,
				'depth'          => 2,
			)
		);
		?>
	</nav>
</header>

<main id="primary" class="site-main">
