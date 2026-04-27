<?php
/**
 * Homepage hero.
 *
 * @package BRC
 */

$hero_kicker          = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_kicker', 'BRC Developments' ) : 'BRC Developments';
$hero_title           = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_title', 'Architecture-led communities for a new Egyptian address.' ) : 'Architecture-led communities for a new Egyptian address.';
$hero_body            = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_body', 'Premium real estate developments shaped around location, architecture, and long-term value.' ) : 'Premium real estate developments shaped around location, architecture, and long-term value.';
$hero_primary_label   = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_primary_label', 'Register Interest' ) : 'Register Interest';
$hero_primary_url     = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_primary_url', '#lead-form' ) : '#lead-form';
$hero_secondary_label = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_secondary_label', 'Explore Projects' ) : 'Explore Projects';
$hero_secondary_url   = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_secondary_url', get_post_type_archive_link( 'brc_project' ) ) : get_post_type_archive_link( 'brc_project' );
$hero_background      = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'hero_background_image', array() ) : array();
$hero_background_url  = '';

if ( is_array( $hero_background ) && ! empty( $hero_background['url'] ) ) {
	$hero_background_url = (string) $hero_background['url'];
}
?>

<section class="home-hero" aria-labelledby="home-hero-title">
	<div class="home-hero__media" aria-hidden="true"<?php echo $hero_background_url ? ' style="--brc-hero-image:url(' . esc_url( $hero_background_url ) . ')"' : ''; ?>></div>
	<div class="home-hero__shade" aria-hidden="true"></div>

	<div class="home-hero__inner">
		<div class="home-hero__copy">
			<p class="home-hero__kicker"><?php echo esc_html( $hero_kicker ); ?></p>
			<h1 id="home-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
			<p><?php echo esc_html( $hero_body ); ?></p>

			<div class="home-hero__actions">
				<a class="brc-button brc-button--light" href="<?php echo esc_url( $hero_primary_url ); ?>"><?php echo esc_html( $hero_primary_label ); ?></a>
				<a class="brc-button brc-button--glass" href="<?php echo esc_url( $hero_secondary_url ); ?>"><?php echo esc_html( $hero_secondary_label ); ?></a>
			</div>
		</div>
	</div>
</section>
