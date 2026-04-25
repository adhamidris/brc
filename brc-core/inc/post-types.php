<?php
/**
 * Custom post types.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_core_register_post_types(): void {
	$common_supports = array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields' );

	register_post_type(
		'brc_project',
		array(
			'labels'       => array(
				'name'          => __( 'Projects', 'brc-core' ),
				'singular_name' => __( 'Project', 'brc-core' ),
				'add_new_item'  => __( 'Add New Project', 'brc-core' ),
				'edit_item'     => __( 'Edit Project', 'brc-core' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-building',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'projects' ),
			'supports'     => $common_supports,
		)
	);

	register_post_type(
		'brc_location',
		array(
			'labels'       => array(
				'name'          => __( 'Locations', 'brc-core' ),
				'singular_name' => __( 'Location', 'brc-core' ),
				'add_new_item'  => __( 'Add New Location', 'brc-core' ),
				'edit_item'     => __( 'Edit Location', 'brc-core' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-location-alt',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'locations' ),
			'supports'     => $common_supports,
		)
	);

	register_post_type(
		'brc_unit',
		array(
			'labels'       => array(
				'name'          => __( 'Units', 'brc-core' ),
				'singular_name' => __( 'Unit', 'brc-core' ),
				'add_new_item'  => __( 'Add New Unit', 'brc-core' ),
				'edit_item'     => __( 'Edit Unit', 'brc-core' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-home',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'units' ),
			'supports'     => $common_supports,
		)
	);
}
add_action( 'init', 'brc_core_register_post_types' );

