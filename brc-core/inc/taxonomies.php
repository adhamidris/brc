<?php
/**
 * Custom taxonomies.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_core_register_taxonomies(): void {
	register_taxonomy(
		'brc_location_area',
		array( 'brc_project', 'brc_unit' ),
		array(
			'labels'       => array(
				'name'          => __( 'Location Areas', 'brc-core' ),
				'singular_name' => __( 'Location Area', 'brc-core' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'area' ),
		)
	);

	register_taxonomy(
		'brc_project_type',
		array( 'brc_project' ),
		array(
			'labels'       => array(
				'name'          => __( 'Project Types', 'brc-core' ),
				'singular_name' => __( 'Project Type', 'brc-core' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'project-type' ),
		)
	);

	register_taxonomy(
		'brc_property_type',
		array( 'brc_unit' ),
		array(
			'labels'       => array(
				'name'          => __( 'Property Types', 'brc-core' ),
				'singular_name' => __( 'Property Type', 'brc-core' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'property-type' ),
		)
	);
}
add_action( 'init', 'brc_core_register_taxonomies' );

