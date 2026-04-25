<?php
/**
 * JSON-LD helpers.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_core_output_organization_schema(): void {
	if ( is_admin() ) {
		return;
	}

	$schema = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $logo ) {
			$schema['logo'] = $logo;
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'brc_core_output_organization_schema', 30 );

function brc_core_output_project_schema(): void {
	if ( ! is_singular( array( 'brc_project', 'brc_unit', 'brc_location' ) ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	$schema  = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Place',
		'name'        => get_the_title( $post_id ),
		'url'         => get_permalink( $post_id ),
		'description' => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
	);

	$image = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( $image ) {
		$schema['image'] = $image;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'brc_core_output_project_schema', 31 );

