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
 * Build or repair the primary navigation.
 *
 * @param int $home_id Home page ID.
 * @param int $blog_id Blog page ID.
 */
function brc_core_ensure_primary_menu( int $home_id, int $blog_id ): void {
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
			'title' => __( 'Insights', 'brc-core' ),
			'url'   => $blog_id ? get_permalink( $blog_id ) : '#',
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
 * Run idempotent onboarding and repair missing install-time structure.
 */
function brc_core_run_onboarding( bool $force = false ): void {
	if ( ! brc_core_is_brc_theme_active() ) {
		return;
	}

	$version = get_option( 'brc_core_onboarding_version', '' );

	if ( ! $force && BRC_CORE_VERSION === $version ) {
		$front_page_id = (int) get_option( 'page_on_front' );
		$blog_page_id  = (int) get_option( 'page_for_posts' );

		if ( $front_page_id > 0 && $blog_page_id > 0 ) {
			return;
		}
	}

	$home_id     = brc_core_ensure_page( 'home', __( 'Home', 'brc-core' ), brc_core_get_default_homepage_content() );
	$blog_id     = brc_core_ensure_page( 'insights', __( 'Insights', 'brc-core' ) );
	$about_id    = brc_core_ensure_page( 'about', __( 'About', 'brc-core' ), '<!-- wp:paragraph --><p>' . esc_html__( 'Replace this placeholder with BRC brand narrative, leadership, and development philosophy.', 'brc-core' ) . '</p><!-- /wp:paragraph -->' );
	$contact_id  = brc_core_ensure_page( 'contact', __( 'Contact', 'brc-core' ), '<!-- wp:paragraph --><p>' . esc_html__( 'Use this page for contact details, WhatsApp, office locations, and lead routing notes.', 'brc-core' ) . '</p><!-- /wp:paragraph -->' );
	$projects_id = brc_core_ensure_page( 'projects', __( 'Projects', 'brc-core' ) );
	$locations_id = brc_core_ensure_page( 'locations', __( 'Locations', 'brc-core' ) );

	unset( $about_id, $contact_id, $projects_id, $locations_id );

	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	if ( $blog_id ) {
		update_option( 'page_for_posts', $blog_id );
	}

	global $wp_rewrite;

	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	brc_core_ensure_primary_menu( $home_id, $blog_id );
	flush_rewrite_rules( true );
	update_option( 'brc_core_onboarding_version', BRC_CORE_VERSION );
}

/**
 * Repair onboarding on admin requests when needed.
 */
function brc_core_maybe_run_onboarding(): void {
	if ( wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	brc_core_run_onboarding();
}
add_action( 'admin_init', 'brc_core_maybe_run_onboarding' );
