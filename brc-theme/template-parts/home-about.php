<?php
/**
 * Homepage about section.
 *
 * @package BRC
 */

$about_kicker = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'about_kicker', __( 'About BRC', 'brc' ) ) : __( 'About BRC', 'brc' );
$about_title  = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'about_title', __( 'We build quieter, longer-lasting places with an emphasis on proportion, value, and measured growth.', 'brc' ) ) : __( 'We build quieter, longer-lasting places with an emphasis on proportion, value, and measured growth.', 'brc' );
$about_body   = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'about_body', __( 'BRC approaches development as a long-term responsibility. The focus is not only on launch momentum, but on how each address holds its quality over time through architecture, planning, and disciplined execution.', 'brc' ) ) : __( 'BRC approaches development as a long-term responsibility. The focus is not only on launch momentum, but on how each address holds its quality over time through architecture, planning, and disciplined execution.', 'brc' );
$about_image  = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'about_image', array() ) : array();
$about_image_url = is_array( $about_image ) && ! empty( $about_image['url'] )
	? (string) $about_image['url']
	: ( function_exists( 'brc_core_home_fallback_image_url' ) ? brc_core_home_fallback_image_url( 'about' ) : get_theme_file_uri( 'assets/img/carousel2.jpg' ) );
?>

<section class="brc-about alignfull">
	<div class="brc-about__inner">
		<div class="brc-about__intro">
			<p class="brc-kicker"><?php echo esc_html( $about_kicker ); ?></p>
			<h2><?php echo esc_html( $about_title ); ?></h2>
			<p><?php echo esc_html( $about_body ); ?></p>
		</div>

		<div class="brc-about__media">
			<img src="<?php echo esc_url( $about_image_url ); ?>" alt="<?php esc_attr_e( 'BRC architecture preview', 'brc' ); ?>">
		</div>
	</div>
</section>
