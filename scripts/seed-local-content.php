<?php
/**
 * Seed local WordPress content for BRC development.
 *
 * Run inside the WordPress container:
 * php /var/www/html/wp-content/themes/brc-theme/../../../../scripts/seed-local-content.php
 */

require_once '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

if ( ! function_exists( 'wp_insert_post' ) ) {
	fwrite( STDERR, "WordPress is not loaded.\n" );
	exit( 1 );
}

function brc_seed_post( array $args ): int {
	$existing = get_page_by_path( $args['post_name'], OBJECT, $args['post_type'] );

	if ( $existing ) {
		$args['ID'] = $existing->ID;
	}

	$post_id = wp_insert_post( $args, true );

	if ( is_wp_error( $post_id ) ) {
		fwrite( STDERR, $post_id->get_error_message() . "\n" );
		exit( 1 );
	}

	return (int) $post_id;
}

function brc_seed_term( string $name, string $taxonomy, string $slug ): int {
	$term = term_exists( $slug, $taxonomy );

	if ( $term ) {
		return (int) $term['term_id'];
	}

	$result = wp_insert_term(
		$name,
		$taxonomy,
		array(
			'slug' => $slug,
		)
	);

	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, $result->get_error_message() . "\n" );
		exit( 1 );
	}

	return (int) $result['term_id'];
}

function brc_seed_attachment( string $source_path, int $post_id, string $title ): int {
	if ( ! file_exists( $source_path ) ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'title'          => $title,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( $existing ) {
		return (int) $existing[0];
	}

	$file_array = array(
		'name'     => basename( $source_path ),
		'tmp_name' => wp_tempnam( basename( $source_path ) ),
	);

	copy( $source_path, $file_array['tmp_name'] );

	$attachment_id = media_handle_sideload( $file_array, $post_id, $title );

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $file_array['tmp_name'] );
		return 0;
	}

	return (int) $attachment_id;
}

$asset_dir = get_template_directory() . '/assets/img';

$home_id = brc_seed_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => 'home',
		'post_title'   => 'Home',
		'post_content' => <<<HTML
<!-- wp:group {"className":"brc-homepage-editor-note"} -->
<div class="wp-block-group brc-homepage-editor-note">
<!-- wp:paragraph -->
<p><strong>Homepage content is managed below in the BRC Homepage Content sections.</strong></p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"direction":"rtl"} -->
<p dir="rtl"><strong>محتوى الصفحة الرئيسية يتم تعديله من أقسام محتوى الصفحة الرئيسية الموجودة بالأسفل.</strong></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
HTML,
	)
);

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );

global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );

$blog_id = brc_seed_post(
	array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => 'blog',
		'post_title'   => 'Blog',
		'post_content' => '',
	)
);
update_option( 'page_for_posts', $blog_id );

$new_cairo_id    = brc_seed_term( 'New Cairo', 'brc_location_area', 'new-cairo' );
$north_coast_id  = brc_seed_term( 'North Coast', 'brc_location_area', 'north-coast' );
$residential_id  = brc_seed_term( 'Residential', 'brc_project_type', 'residential' );
$commercial_id   = brc_seed_term( 'Commercial', 'brc_project_type', 'commercial' );
$apartment_id    = brc_seed_term( 'Apartment', 'brc_property_type', 'apartment' );
$villa_id        = brc_seed_term( 'Villa', 'brc_property_type', 'villa' );

$location_posts = array(
	array( 'New Cairo', 'new-cairo', 'A strategic east Cairo destination with strong access, lifestyle demand, and long-term residential growth.', 'location-new-cairo.png' ),
	array( 'North Coast', 'north-coast', 'A premium coastal destination shaped around seasonal living, hospitality, and second-home demand.', 'location-north-coast.png' ),
);

foreach ( $location_posts as $location ) {
	$location_id = brc_seed_post(
		array(
			'post_type'    => 'brc_location',
			'post_status'  => 'publish',
			'post_name'    => $location[1],
			'post_title'   => $location[0],
			'post_excerpt' => $location[2],
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $location[2] ) . '</p><!-- /wp:paragraph -->',
		)
	);

	$attachment_id = brc_seed_attachment( $asset_dir . '/' . $location[3], $location_id, $location[0] . ' Preview' );
	if ( $attachment_id ) {
		set_post_thumbnail( $location_id, $attachment_id );
	}
}

$projects = array(
	array(
		'title'     => 'BRC Heights',
		'slug'      => 'brc-heights',
		'location'  => $new_cairo_id,
		'type'      => $residential_id,
		'excerpt'   => 'A modern residential address designed around quiet luxury, access, and daily convenience.',
		'price'     => 'Starting from EGP 4,850,000',
		'plan'      => '10% DP / 8 years',
		'date'      => 'Q4 2027',
		'sizes'     => '95 - 220 m2',
		'bedrooms'  => '2 - 4',
		'image'     => 'carousel1.jpg',
		'image_alt' => 'Carousel 1',
	),
	array(
		'title'     => 'BRC Coast',
		'slug'      => 'brc-coast',
		'location'  => $north_coast_id,
		'type'      => $residential_id,
		'excerpt'   => 'A coastal community with refined architecture, generous views, and resort-style amenities.',
		'price'     => 'Starting from EGP 7,200,000',
		'plan'      => '5% DP / 9 years',
		'date'      => 'Q2 2028',
		'sizes'     => '110 - 280 m2',
		'bedrooms'  => '2 - 5',
		'image'     => 'carousel2.jpg',
		'image_alt' => 'Carousel 2',
	),
	array(
		'title'     => 'BRC Business District',
		'slug'      => 'brc-business-district',
		'location'  => $new_cairo_id,
		'type'      => $commercial_id,
		'excerpt'   => 'A premium business destination for offices, clinics, and high-visibility retail.',
		'price'     => 'Starting from EGP 3,100,000',
		'plan'      => '15% DP / 6 years',
		'date'      => 'Q1 2027',
		'sizes'     => '45 - 180 m2',
		'bedrooms'  => '',
		'image'     => 'carousel3.jpg',
		'image_alt' => 'Carousel 3',
	),
);

foreach ( $projects as $project ) {
	$post_id = brc_seed_post(
		array(
			'post_type'    => 'brc_project',
			'post_status'  => 'publish',
			'post_name'    => $project['slug'],
			'post_title'   => $project['title'],
			'post_excerpt' => $project['excerpt'],
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $project['excerpt'] ) . '</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Project Overview</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Replace this seeded copy with real project positioning, masterplan details, amenities, and investment highlights.</p><!-- /wp:paragraph -->',
		)
	);

	wp_set_object_terms( $post_id, array( $project['location'] ), 'brc_location_area' );
	wp_set_object_terms( $post_id, array( $project['type'] ), 'brc_project_type' );

	update_post_meta( $post_id, 'brc_starting_price', $project['price'] );
	update_post_meta( $post_id, 'brc_payment_plan', $project['plan'] );
	update_post_meta( $post_id, 'brc_completion_date', $project['date'] );
	update_post_meta( $post_id, 'brc_property_sizes', $project['sizes'] );
	update_post_meta( $post_id, 'brc_bedrooms', $project['bedrooms'] );
	update_post_meta( $post_id, 'brc_featured', '1' );

	$attachment_id = brc_seed_attachment(
		$asset_dir . '/' . $project['image'],
		$post_id,
		$project['title'] . ' ' . $project['image_alt']
	);
	if ( $attachment_id ) {
		set_post_thumbnail( $post_id, $attachment_id );
	}
}

$unit_id = brc_seed_post(
	array(
		'post_type'    => 'brc_unit',
		'post_status'  => 'publish',
		'post_name'    => 'sample-two-bedroom-unit',
		'post_title'   => 'Sample Two-Bedroom Unit',
		'post_excerpt' => 'A sample unit detail page for testing SEO, lead capture, and listing metadata.',
		'post_content' => '<!-- wp:paragraph --><p>This is seeded local content for testing the unit detail template.</p><!-- /wp:paragraph -->',
	)
);
wp_set_object_terms( $unit_id, array( $new_cairo_id ), 'brc_location_area' );
wp_set_object_terms( $unit_id, array( $apartment_id ), 'brc_property_type' );
update_post_meta( $unit_id, 'brc_starting_price', 'EGP 4,850,000' );
update_post_meta( $unit_id, 'brc_payment_plan', '10% DP / 8 years' );
update_post_meta( $unit_id, 'brc_property_sizes', '135 m2' );
update_post_meta( $unit_id, 'brc_bedrooms', '2' );

for ( $i = 1; $i <= 3; $i++ ) {
	brc_seed_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_name'    => 'market-insight-' . $i,
			'post_title'   => 'Market Insight ' . $i,
			'post_excerpt' => 'A sample article excerpt for testing blog cards and SEO layouts.',
			'post_content' => '<!-- wp:paragraph --><p>This seeded article helps test the blog index and single post template.</p><!-- /wp:paragraph -->',
		)
	);
}

$menu_id = wp_get_nav_menu_object( 'Primary' );

if ( ! $menu_id ) {
	$menu_id = wp_create_nav_menu( 'Primary' );
} else {
	$menu_id = $menu_id->term_id;
}

$menu_items = array(
	array( 'Home', home_url( '/' ) ),
	array( 'Projects', get_post_type_archive_link( 'brc_project' ) ),
	array( 'Locations', get_post_type_archive_link( 'brc_location' ) ),
	array( 'Units', get_post_type_archive_link( 'brc_unit' ) ),
	array( 'Insights', get_permalink( $blog_id ) ),
);

foreach ( $menu_items as $menu_item ) {
	$existing_items = wp_get_nav_menu_items( $menu_id );
	$exists         = false;

	if ( $existing_items ) {
		foreach ( $existing_items as $existing_item ) {
			if ( $existing_item->title === $menu_item[0] ) {
				$exists = true;
				break;
			}
		}
	}

	if ( ! $exists ) {
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'  => $menu_item[0],
				'menu-item-url'    => $menu_item[1],
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			)
		);
	}
}

$locations = get_theme_mod( 'nav_menu_locations' );
$locations = is_array( $locations ) ? $locations : array();
$locations['primary'] = (int) $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

flush_rewrite_rules( true );
save_mod_rewrite_rules();

echo "Seeded local BRC content.\n";
