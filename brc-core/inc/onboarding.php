<?php
/**
 * BRC onboarding and self-healing setup.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the BRC theme is active.
 */
function brc_core_is_brc_theme_active(): bool {
	$theme = wp_get_theme();

	return 'brc-theme' === $theme->get_stylesheet() || 'brc-theme' === $theme->get_template();
}

/**
 * Ensure a page exists and optionally hydrate default content.
 */
function brc_core_ensure_page( string $slug, string $title, string $content = '' ): int {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	$args = array(
		'post_type'   => 'page',
		'post_status' => 'publish',
		'post_name'   => $slug,
		'post_title'  => $title,
	);

	if ( $existing ) {
		$args['ID'] = $existing->ID;

		if ( $content && '' === trim( (string) $existing->post_content ) ) {
			$args['post_content'] = $content;
		}
	} else {
		$args['post_content'] = $content;
	}

	$page_id = wp_insert_post( $args, true );

	if ( is_wp_error( $page_id ) ) {
		return 0;
	}

	return (int) $page_id;
}

/**
 * Check if a post is the untouched WordPress welcome post.
 */
function brc_core_is_default_hello_world_post( WP_Post $post ): bool {
	return 'post' === $post->post_type
		&& 'publish' === $post->post_status
		&& 'hello-world' === $post->post_name;
}

/**
 * Create starter blog posts when the install only has default content.
 */
function brc_core_seed_starter_posts(): void {
	$existing_posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( count( $existing_posts ) > 1 ) {
		return;
	}

	if ( 1 === count( $existing_posts ) && ! brc_core_is_default_hello_world_post( $existing_posts[0] ) ) {
		return;
	}

	$starter_posts = array(
		array(
			'post_title'   => __( 'New Cairo demand brief', 'brc-core' ),
			'post_excerpt' => __( 'A starter editorial post covering buyer appetite, supply quality, and the role of well-positioned communities in New Cairo.', 'brc-core' ),
			'post_content' => __( 'Use this sample article to replace the default Hello World post and begin shaping the editorial voice of the BRC journal. The final version can cover launch strategy, location dynamics, and how demand is evolving across the city.', 'brc-core' ),
		),
		array(
			'post_title'   => __( 'North Coast seasonality and timing', 'brc-core' ),
			'post_excerpt' => __( 'A starter note on second-home demand, launch timing, and the kind of positioning that matters most for coastal projects.', 'brc-core' ),
			'post_content' => __( 'This seeded post gives the Market Notes section something intentional to show on a fresh install. Replace it later with a real editorial angle once the live content calendar is approved.', 'brc-core' ),
		),
	);

	foreach ( $starter_posts as $starter_post ) {
		wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_title'   => $starter_post['post_title'],
				'post_excerpt' => $starter_post['post_excerpt'],
				'post_content' => $starter_post['post_content'],
			)
		);
	}
}

/**
 * Create starter locations when the CPT is empty.
 */
function brc_core_seed_starter_locations(): void {
	$existing_locations = get_posts(
		array(
			'post_type'      => 'brc_location',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing_locations ) ) {
		return;
	}

	$locations = array(
		array(
			'post_title'   => __( 'New Cairo', 'brc-core' ),
			'post_excerpt' => __( 'A primary eastern corridor for premium residential and mixed-use demand.', 'brc-core' ),
			'post_content' => __( 'Replace this placeholder with a fuller overview of why this destination matters to the BRC portfolio, who it attracts, and how the area is evolving.', 'brc-core' ),
		),
		array(
			'post_title'   => __( 'North Coast', 'brc-core' ),
			'post_excerpt' => __( 'A coastal leisure market defined by seasonality, positioning, and destination quality.', 'brc-core' ),
			'post_content' => __( 'Use this placeholder location to show how destination pages will behave once final content, imagery, and SEO copy are available.', 'brc-core' ),
		),
	);

	foreach ( $locations as $index => $location ) {
		wp_insert_post(
			array(
				'post_type'    => 'brc_location',
				'post_status'  => 'publish',
				'post_title'   => $location['post_title'],
				'post_excerpt' => $location['post_excerpt'],
				'post_content' => $location['post_content'],
				'menu_order'   => $index,
			)
		);
	}
}

/**
 * Create starter projects when the CPT is empty.
 */
function brc_core_seed_starter_projects(): void {
	$existing_projects = get_posts(
		array(
			'post_type'      => 'brc_project',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing_projects ) ) {
		return;
	}

	$new_cairo_term = term_exists( 'new-cairo', 'brc_location_area' );
	if ( ! $new_cairo_term ) {
		$new_cairo_term = wp_insert_term( __( 'New Cairo', 'brc-core' ), 'brc_location_area', array( 'slug' => 'new-cairo' ) );
	}

	$north_coast_term = term_exists( 'north-coast', 'brc_location_area' );
	if ( ! $north_coast_term ) {
		$north_coast_term = wp_insert_term( __( 'North Coast', 'brc-core' ), 'brc_location_area', array( 'slug' => 'north-coast' ) );
	}

	$starter_projects = array(
		array(
			'post_title'      => __( 'BRC Heights', 'brc-core' ),
			'post_excerpt'    => __( 'A premium residential launch placeholder for New Cairo, focused on calm planning and long-term value.', 'brc-core' ),
			'post_content'    => __( 'Use this seeded project to demonstrate the archive, homepage launch cards, and project selector before final commercial content is ready.', 'brc-core' ),
			'starting_price'  => __( 'Starting from EGP 0', 'brc-core' ),
			'payment_plan'    => __( 'Flexible plan pending', 'brc-core' ),
			'completion_date' => __( 'Completion pending', 'brc-core' ),
			'property_sizes'  => __( 'Sizes pending', 'brc-core' ),
			'bedrooms'        => __( 'Mix pending', 'brc-core' ),
			'location_slug'   => 'new-cairo',
		),
		array(
			'post_title'      => __( 'BRC Coast', 'brc-core' ),
			'post_excerpt'    => __( 'A coastal showcase placeholder that keeps the homepage launch section fully composed on a fresh install.', 'brc-core' ),
			'post_content'    => __( 'This starter project exists to prove the handoff flow and can be replaced cleanly once the real launch stack is available.', 'brc-core' ),
			'starting_price'  => __( 'Starting from EGP 0', 'brc-core' ),
			'payment_plan'    => __( 'Flexible plan pending', 'brc-core' ),
			'completion_date' => __( 'Completion pending', 'brc-core' ),
			'property_sizes'  => __( 'Sizes pending', 'brc-core' ),
			'bedrooms'        => __( 'Mix pending', 'brc-core' ),
			'location_slug'   => 'north-coast',
		),
	);

	foreach ( $starter_projects as $starter_project ) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'brc_project',
				'post_status'  => 'publish',
				'post_title'   => $starter_project['post_title'],
				'post_excerpt' => $starter_project['post_excerpt'],
				'post_content' => $starter_project['post_content'],
			)
		);

		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}

		update_post_meta( $post_id, 'brc_featured', '1' );
		update_post_meta( $post_id, 'brc_starting_price', $starter_project['starting_price'] );
		update_post_meta( $post_id, 'brc_payment_plan', $starter_project['payment_plan'] );
		update_post_meta( $post_id, 'brc_completion_date', $starter_project['completion_date'] );
		update_post_meta( $post_id, 'brc_property_sizes', $starter_project['property_sizes'] );
		update_post_meta( $post_id, 'brc_bedrooms', $starter_project['bedrooms'] );

		wp_set_object_terms( $post_id, array( $starter_project['location_slug'] ), 'brc_location_area', false );
	}
}

/**
 * Seed starter content across homepage-facing content types.
 */
function brc_core_seed_starter_content(): void {
	brc_core_seed_starter_posts();
	brc_core_seed_starter_locations();
	brc_core_seed_starter_projects();
}

/**
 * Build or repair the primary navigation.
 *
 * @param int $home_id Home page ID.
 * @param int $blog_id Blog page ID.
 */
function brc_core_ensure_primary_menu( int $home_id, int $blog_id, int $about_id, int $contact_id ): void {
	$menu = wp_get_nav_menu_object( 'Primary' );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( 'Primary' );
	} else {
		$menu_id = (int) $menu->term_id;
	}

	if ( ! $menu_id ) {
		return;
	}

	$menu_items = array(
		array(
			'title' => __( 'Home', 'brc-core' ),
			'url'   => $home_id ? get_permalink( $home_id ) : home_url( '/' ),
		),
		array(
			'title' => __( 'Projects', 'brc-core' ),
			'url'   => get_post_type_archive_link( 'brc_project' ) ?: '#',
		),
		array(
			'title' => __( 'Locations', 'brc-core' ),
			'url'   => get_post_type_archive_link( 'brc_location' ) ?: '#',
		),
		array(
			'title' => __( 'Units', 'brc-core' ),
			'url'   => get_post_type_archive_link( 'brc_unit' ) ?: '#',
		),
		array(
			'title' => __( 'Blog', 'brc-core' ),
			'url'   => $blog_id ? get_permalink( $blog_id ) : '#',
		),
		array(
			'title' => __( 'About', 'brc-core' ),
			'url'   => $about_id ? get_permalink( $about_id ) : '#',
		),
		array(
			'title' => __( 'Contact', 'brc-core' ),
			'url'   => $contact_id ? get_permalink( $contact_id ) : '#',
		),
	);

	$existing_items = wp_get_nav_menu_items( $menu_id );
	$existing_map   = array();

	if ( $existing_items ) {
		foreach ( $existing_items as $existing_item ) {
			$existing_map[ $existing_item->title ] = (int) $existing_item->ID;
		}
	}

	foreach ( $menu_items as $menu_item ) {
		wp_update_nav_menu_item(
			$menu_id,
			$existing_map[ $menu_item['title'] ] ?? 0,
			array(
				'menu-item-title'  => $menu_item['title'],
				'menu-item-url'    => $menu_item['url'],
				'menu-item-status' => 'publish',
				'menu-item-type'   => 'custom',
			)
		);
	}

	$locations            = get_theme_mod( 'nav_menu_locations' );
	$locations            = is_array( $locations ) ? $locations : array();
	$locations['primary'] = $menu_id;

	set_theme_mod( 'nav_menu_locations', $locations );
}

/**
 * Check whether onboarding needs to repair the site structure.
 */
function brc_core_onboarding_needs_repair(): bool {
	if ( ! brc_core_is_brc_theme_active() ) {
		return false;
	}

	$home_page    = get_page_by_path( 'home', OBJECT, 'page' );
	$blog_page    = get_page_by_path( 'blog', OBJECT, 'page' );
	$about_page   = get_page_by_path( 'about', OBJECT, 'page' );
	$contact_page = get_page_by_path( 'contact', OBJECT, 'page' );
	$locations    = get_theme_mod( 'nav_menu_locations' );
	$locations    = is_array( $locations ) ? $locations : array();

	if ( ! $home_page || ! $blog_page || ! $about_page || ! $contact_page ) {
		return true;
	}

	if ( (int) get_option( 'page_on_front' ) !== (int) $home_page->ID ) {
		return true;
	}

	if ( (int) get_option( 'page_for_posts' ) !== (int) $blog_page->ID ) {
		return true;
	}

	if ( empty( $locations['primary'] ) ) {
		return true;
	}

	if ( BRC_CORE_VERSION !== get_option( 'brc_core_onboarding_version', '' ) ) {
		return true;
	}

	return false;
}

/**
 * Run idempotent onboarding and repair missing install-time structure.
 */
function brc_core_run_onboarding( bool $force = false ): void {
	if ( ! brc_core_is_brc_theme_active() ) {
		return;
	}

	if ( ! $force && ! brc_core_onboarding_needs_repair() ) {
		return;
	}

	$home_id    = brc_core_ensure_page( 'home', __( 'Home', 'brc-core' ), brc_core_get_default_homepage_content() );
	$blog_id    = brc_core_ensure_page( 'blog', __( 'Blog', 'brc-core' ) );
	$about_id   = brc_core_ensure_page( 'about', __( 'About', 'brc-core' ), '<!-- wp:paragraph --><p>' . esc_html__( 'Replace this placeholder with BRC brand narrative, leadership, and development philosophy.', 'brc-core' ) . '</p><!-- /wp:paragraph -->' );
	$contact_id = brc_core_ensure_page( 'contact', __( 'Contact', 'brc-core' ), '<!-- wp:paragraph --><p>' . esc_html__( 'Use this page for contact details, WhatsApp, office locations, and lead routing notes.', 'brc-core' ) . '</p><!-- /wp:paragraph -->' );

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	if ( $blog_id ) {
		update_option( 'page_for_posts', $blog_id );
	}

	global $wp_rewrite;

	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	brc_core_ensure_primary_menu( $home_id, $blog_id, $about_id, $contact_id );
	brc_core_seed_starter_content();
	flush_rewrite_rules( true );
	update_option( 'brc_core_onboarding_version', BRC_CORE_VERSION );
}

/**
 * Repair onboarding on admin requests when needed.
 */
function brc_core_maybe_run_onboarding(): void {
	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	brc_core_run_onboarding();
}
add_action( 'init', 'brc_core_maybe_run_onboarding', 20 );
