<?php
/**
 * Template helper functions.
 *
 * @package BRC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_theme_logo(): void {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	printf(
		'<a class="site-brand__text" href="%1$s" rel="home"><span>BRC</span><small>%2$s</small></a>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Developments', 'brc' )
	);
}

function brc_theme_posted_on(): void {
	printf(
		'<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

function brc_theme_excerpt( int $words = 24 ): string {
	return wp_kses_post( wp_trim_words( get_the_excerpt(), $words ) );
}

