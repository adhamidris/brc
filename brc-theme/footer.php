<?php
/**
 * Site footer.
 *
 * @package BRC
 */
?>
</main>

<footer class="site-footer">
	<div class="site-footer__inner">
		<div class="site-footer__brand">
			<?php brc_theme_logo(); ?>
			<p class="site-footer__lead">
				<?php esc_html_e( 'Development guided by proportion, long-term value, and quieter architectural presence.', 'brc' ); ?>
			</p>
		</div>

		<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer menu', 'brc' ); ?>">
			<p class="site-footer__eyebrow"><?php esc_html_e( 'Navigation', 'brc' ); ?></p>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => has_nav_menu( 'footer' ) ? 'footer' : 'primary',
					'container'      => false,
					'menu_class'     => 'site-footer__menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<div class="site-footer__meta">
			<p class="site-footer__eyebrow"><?php esc_html_e( 'BRC Developments', 'brc' ); ?></p>
			<p class="site-footer__note">
				<?php esc_html_e( 'Based in Egypt. Built for editorial clarity today, ready for real contact details and legal copy at launch.', 'brc' ); ?>
			</p>
			<a class="site-footer__link" href="#lead-form"><?php esc_html_e( 'Register Interest', 'brc' ); ?></a>
		</div>

		<div class="site-footer__bottom">
			<p class="site-footer__copy">
				&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			</p>
			<p class="site-footer__legal">
				<?php esc_html_e( 'All rights reserved.', 'brc' ); ?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
