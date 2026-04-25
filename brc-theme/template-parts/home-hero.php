<?php
/**
 * Homepage hero.
 *
 * @package BRC
 */
?>

<section class="home-hero" aria-labelledby="home-hero-title">
	<div class="home-hero__media" aria-hidden="true"></div>
	<div class="home-hero__shade" aria-hidden="true"></div>

	<div class="home-hero__inner">
		<div class="home-hero__copy">
			<p class="home-hero__kicker"><?php esc_html_e( 'BRC Developments', 'brc' ); ?></p>
			<h1 id="home-hero-title"><?php esc_html_e( 'Architecture-led communities.', 'brc' ); ?></h1>
			<p><?php esc_html_e( 'Premium real estate developments shaped around location, long-term value, and a calmer standard of modern living.', 'brc' ); ?></p>

			<div class="home-hero__actions">
				<a class="brc-button brc-button--light" href="#lead-form"><?php esc_html_e( 'Register Interest', 'brc' ); ?></a>
				<a class="brc-button brc-button--glass" href="<?php echo esc_url( get_post_type_archive_link( 'brc_project' ) ); ?>"><?php esc_html_e( 'Explore Projects', 'brc' ); ?></a>
			</div>
		</div>
	</div>
</section>
