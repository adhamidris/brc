<?php
/**
 * 404 template.
 *
 * @package BRC
 */

get_header();
?>

<section class="brc-page-header brc-page-header--center">
	<p class="brc-kicker"><?php esc_html_e( '404', 'brc' ); ?></p>
	<h1><?php esc_html_e( 'Page not found', 'brc' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for may have moved or no longer exists.', 'brc' ); ?></p>
	<a class="brc-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'brc' ); ?></a>
</section>

<?php
get_footer();

