<?php
/**
 * Homepage data helpers and graceful defaults.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize relationship or post object values into integer IDs.
 *
 * @param mixed $value Raw ACF value.
 * @return array<int>
 */
function brc_core_home_normalize_related_ids( $value ): array {
	if ( empty( $value ) ) {
		return array();
	}

	$items = is_array( $value ) ? $value : array( $value );
	$ids   = array();

	foreach ( $items as $item ) {
		if ( $item instanceof WP_Post ) {
			$ids[] = (int) $item->ID;
			continue;
		}

		if ( is_numeric( $item ) ) {
			$ids[] = (int) $item;
		}
	}

	$ids = array_values(
		array_filter(
			array_unique( $ids ),
			static fn ( int $id ): bool => $id > 0
		)
	);

	return $ids;
}

/**
 * Return a stable fallback image URL for homepage sections.
 */
function brc_core_home_fallback_image_url( string $context = 'generic' ): string {
	$images = array(
		'hero'     => 'assets/img/hero2.jpg',
		'launches' => 'assets/img/carousel1.jpg',
		'projects' => 'assets/img/carousel3.jpg',
		'about'    => 'assets/img/carousel2.jpg',
		'lead'     => 'assets/img/carousel4.jpg',
		'market'   => 'assets/img/carousel4.jpg',
		'generic'  => 'assets/img/carousel1.jpg',
	);

	$path = $images[ $context ] ?? $images['generic'];

	return get_theme_file_uri( $path );
}

/**
 * Build homepage project card data from a real project post.
 *
 * @return array<string, mixed>
 */
function brc_core_home_build_project_card( int $post_id ): array {
	$project_terms = get_the_terms( $post_id, 'brc_location_area' );
	$thumbnail_url = get_the_post_thumbnail_url( $post_id, 'brc-wide' );
	$data          = array(
		'id'              => $post_id,
		'title'           => get_the_title( $post_id ),
		'permalink'       => get_permalink( $post_id ),
		'excerpt'         => has_excerpt( $post_id ) ? wp_trim_words( get_the_excerpt( $post_id ), 24 ) : wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 24 ),
		'thumbnail'       => has_post_thumbnail( $post_id ) ? get_the_post_thumbnail( $post_id, 'brc-wide' ) : '',
		'thumbnail_url'   => $thumbnail_url ?: brc_core_home_fallback_image_url( 'projects' ),
		'starting_price'  => (string) get_post_meta( $post_id, 'brc_starting_price', true ),
		'payment_plan'    => (string) get_post_meta( $post_id, 'brc_payment_plan', true ),
		'completion_date' => (string) get_post_meta( $post_id, 'brc_completion_date', true ),
		'property_sizes'  => (string) get_post_meta( $post_id, 'brc_property_sizes', true ),
		'bedrooms'        => (string) get_post_meta( $post_id, 'brc_bedrooms', true ),
		'location_names'  => array(),
		'location_slugs'  => array(),
	);

	if ( ! empty( $project_terms ) && ! is_wp_error( $project_terms ) ) {
		foreach ( $project_terms as $term ) {
			$data['location_names'][] = $term->name;
			$data['location_slugs'][] = $term->slug;
		}
	}

	return $data;
}

/**
 * Placeholder launches for empty installs.
 *
 * @return array<int, array<string, mixed>>
 */
function brc_core_home_placeholder_launches(): array {
	return array(
		array(
			'id'              => 0,
			'title'           => __( 'BRC Heights', 'brc-core' ),
			'permalink'       => '#',
			'excerpt'         => __( 'Premium residential positioning copy will appear here once the first project is published.', 'brc-core' ),
			'thumbnail'       => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'launches' ) ) . '" alt="' . esc_attr__( 'BRC Heights placeholder', 'brc-core' ) . '">',
			'thumbnail_url'   => brc_core_home_fallback_image_url( 'launches' ),
			'starting_price'  => __( 'Starting price pending', 'brc-core' ),
			'payment_plan'    => __( 'Plan pending', 'brc-core' ),
			'completion_date' => __( 'Timeline pending', 'brc-core' ),
			'property_sizes'  => __( 'Sizes pending', 'brc-core' ),
			'bedrooms'        => __( 'Beds pending', 'brc-core' ),
			'location_names'  => array( __( 'New Cairo', 'brc-core' ) ),
			'location_slugs'  => array( 'new-cairo' ),
		),
		array(
			'id'              => 0,
			'title'           => __( 'BRC Coast', 'brc-core' ),
			'permalink'       => '#',
			'excerpt'         => __( 'A second launch placeholder keeps the section intentional while client-ready content is still being assembled.', 'brc-core' ),
			'thumbnail'       => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'projects' ) ) . '" alt="' . esc_attr__( 'BRC Coast placeholder', 'brc-core' ) . '">',
			'thumbnail_url'   => brc_core_home_fallback_image_url( 'projects' ),
			'starting_price'  => __( 'Starting price pending', 'brc-core' ),
			'payment_plan'    => __( 'Plan pending', 'brc-core' ),
			'completion_date' => __( 'Timeline pending', 'brc-core' ),
			'property_sizes'  => __( 'Sizes pending', 'brc-core' ),
			'bedrooms'        => __( 'Beds pending', 'brc-core' ),
			'location_names'  => array( __( 'North Coast', 'brc-core' ) ),
			'location_slugs'  => array( 'north-coast' ),
		),
		array(
			'id'              => 0,
			'title'           => __( 'BRC Business District', 'brc-core' ),
			'permalink'       => '#',
			'excerpt'         => __( 'A third placeholder preserves scale and rhythm until the commercial showcase is ready to go live.', 'brc-core' ),
			'thumbnail'       => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'about' ) ) . '" alt="' . esc_attr__( 'BRC Business District placeholder', 'brc-core' ) . '">',
			'thumbnail_url'   => brc_core_home_fallback_image_url( 'about' ),
			'starting_price'  => __( 'Starting price pending', 'brc-core' ),
			'payment_plan'    => __( 'Plan pending', 'brc-core' ),
			'completion_date' => __( 'Timeline pending', 'brc-core' ),
			'property_sizes'  => __( 'Sizes pending', 'brc-core' ),
			'bedrooms'        => __( 'Uses pending', 'brc-core' ),
			'location_names'  => array( __( 'West Cairo', 'brc-core' ) ),
			'location_slugs'  => array( 'west-cairo' ),
		),
	);
}

/**
 * Return launch cards, using selected projects first and featured projects as fallback.
 *
 * @return array<int, array<string, mixed>>
 */
function brc_core_home_get_launch_projects( int $count = 3, array $selected_ids = array() ): array {
	$count        = max( 1, $count );
	$selected_ids = brc_core_home_normalize_related_ids( $selected_ids );
	$projects     = array();

	if ( ! empty( $selected_ids ) ) {
		foreach ( $selected_ids as $post_id ) {
			if ( 'brc_project' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
				continue;
			}

			$projects[] = brc_core_home_build_project_card( $post_id );
		}
	}

	if ( empty( $projects ) ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'brc_project',
				'post_status'    => 'publish',
				'posts_per_page' => $count,
				'meta_query'     => array(
					array(
						'key'   => 'brc_featured',
						'value' => '1',
					),
				),
			)
		);

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $project_post ) {
				$projects[] = brc_core_home_build_project_card( $project_post->ID );
			}
		}

		wp_reset_postdata();
	}

	if ( empty( $projects ) ) {
		$projects = brc_core_home_placeholder_launches();
	}

	return array_slice( $projects, 0, $count );
}

/**
 * Build project selector items for the homepage story section.
 *
 * @return array<int, array<string, mixed>>
 */
function brc_core_home_get_project_story_items( array $selected_ids = array() ): array {
	$selected_ids = brc_core_home_normalize_related_ids( $selected_ids );
	$items        = array();
	$post_ids     = array();

	if ( ! empty( $selected_ids ) ) {
		$post_ids = $selected_ids;
	} else {
		$query = new WP_Query(
			array(
				'post_type'      => 'brc_project',
				'post_status'    => 'publish',
				'posts_per_page' => 2,
				'meta_query'     => array(
					array(
						'key'   => 'brc_featured',
						'value' => '1',
					),
				),
			)
		);

		$post_ids = wp_list_pluck( $query->posts, 'ID' );
		wp_reset_postdata();

		if ( empty( $post_ids ) ) {
			$fallback_query = new WP_Query(
				array(
					'post_type'      => 'brc_project',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
				)
			);

			$post_ids = wp_list_pluck( $fallback_query->posts, 'ID' );
			wp_reset_postdata();
		}
	}

	foreach ( array_values( $post_ids ) as $index => $post_id ) {
		if ( 'brc_project' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
			continue;
		}

		$project        = brc_core_home_build_project_card( $post_id );
		$location_label = ! empty( $project['location_names'] ) ? implode( ' / ', $project['location_names'] ) : __( 'Signature destination', 'brc-core' );
		$subtitle       = $project['excerpt'] ?: sprintf(
			/* translators: %s is the project location label. */
			__( 'A refined development in %s, with the full editorial summary to be completed as final launch copy is approved.', 'brc-core' ),
			$location_label
		);

		$items[] = array(
			'id'        => $post_id,
			'index'     => str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ),
			'title'     => $project['title'],
			'subtitle'  => $subtitle,
			'cta_label' => __( 'Explore Project', 'brc-core' ),
			'cta_url'   => $project['permalink'],
			'images'    => array( $project['thumbnail_url'] ),
		);
	}

	if ( ! empty( $items ) ) {
		return $items;
	}

	return array(
		array(
			'id'        => 0,
			'index'     => '01',
			'title'     => __( 'BRC Heights', 'brc-core' ),
			'subtitle'  => __( 'A premium residential address placeholder, ready to be replaced with the first live project summary.', 'brc-core' ),
			'cta_label' => __( 'Project Pending', 'brc-core' ),
			'cta_url'   => '#',
			'images'    => array( brc_core_home_fallback_image_url( 'projects' ) ),
		),
		array(
			'id'        => 0,
			'index'     => '02',
			'title'     => __( 'BRC Coast', 'brc-core' ),
			'subtitle'  => __( 'A second curated placeholder keeps the selector balanced until the selected project set is confirmed.', 'brc-core' ),
			'cta_label' => __( 'Project Pending', 'brc-core' ),
			'cta_url'   => '#',
			'images'    => array( brc_core_home_fallback_image_url( 'about' ) ),
		),
	);
}

/**
 * Return placeholder editorial cards for empty installs.
 *
 * @return array<int, array<string, string>>
 */
function brc_core_home_placeholder_market_notes(): array {
	return array(
		array(
			'title'      => __( 'Market outlook placeholder', 'brc-core' ),
			'permalink'  => '#',
			'excerpt'    => __( 'Once journal content is published, this area becomes a live editorial feed without changing the homepage layout.', 'brc-core' ),
			'image_html' => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'market' ) ) . '" alt="' . esc_attr__( 'Market placeholder 1', 'brc-core' ) . '">',
		),
		array(
			'title'      => __( 'Location demand snapshot', 'brc-core' ),
			'permalink'  => '#',
			'excerpt'    => __( 'A supporting placeholder keeps the section composed while real editorial material is still being prepared.', 'brc-core' ),
			'image_html' => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'about' ) ) . '" alt="' . esc_attr__( 'Market placeholder 2', 'brc-core' ) . '">',
		),
		array(
			'title'      => __( 'Development notes', 'brc-core' ),
			'permalink'  => '#',
			'excerpt'    => __( 'Use this slot later for launch notes, investment angles, and broader market positioning.', 'brc-core' ),
			'image_html' => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'projects' ) ) . '" alt="' . esc_attr__( 'Market placeholder 3', 'brc-core' ) . '">',
		),
		array(
			'title'      => __( 'Investor brief', 'brc-core' ),
			'permalink'  => '#',
			'excerpt'    => __( 'A final placeholder preserves the editorial rhythm until enough content is ready for a full launch.', 'brc-core' ),
			'image_html' => '<img src="' . esc_url( brc_core_home_fallback_image_url( 'launches' ) ) . '" alt="' . esc_attr__( 'Market placeholder 4', 'brc-core' ) . '">',
		),
	);
}

/**
 * Return featured and secondary market note items.
 *
 * @return array<string, mixed>
 */
function brc_core_home_get_market_notes( int $count = 4, int $featured_post_id = 0 ): array {
	$count   = max( 2, $count );
	$post_ids = array();

	if ( $featured_post_id > 0 && 'post' === get_post_type( $featured_post_id ) && 'publish' === get_post_status( $featured_post_id ) ) {
		$post_ids[] = $featured_post_id;
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'post__not_in'        => $post_ids,
			'ignore_sticky_posts' => true,
		)
	);

	if ( $query->have_posts() ) {
		foreach ( $query->posts as $post_item ) {
			$post_ids[] = (int) $post_item->ID;
		}
	}

	wp_reset_postdata();

	$post_ids = array_slice( array_values( array_unique( $post_ids ) ), 0, $count );

	$cards = array();

	foreach ( $post_ids as $post_id ) {
		$image_html = has_post_thumbnail( $post_id )
			? get_the_post_thumbnail( $post_id, 'large' )
			: '<img src="' . esc_url( brc_core_home_fallback_image_url( 'market' ) ) . '" alt="' . esc_attr( get_the_title( $post_id ) ) . '">';

		$cards[] = array(
			'title'      => get_the_title( $post_id ),
			'permalink'  => get_permalink( $post_id ),
			'excerpt'    => has_excerpt( $post_id ) ? wp_trim_words( get_the_excerpt( $post_id ), 22 ) : wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ), 22 ),
			'image_html' => $image_html,
		);
	}

	if ( count( $cards ) < 2 ) {
		$cards = brc_core_home_placeholder_market_notes();
	}

	while ( count( $cards ) < $count ) {
		$placeholder_cards = brc_core_home_placeholder_market_notes();
		$cards[]           = $placeholder_cards[ count( $cards ) % count( $placeholder_cards ) ];
	}

	$featured = array_shift( $cards );

	return array(
		'featured'    => $featured,
		'posts'       => array_slice( $cards, 0, max( 1, $count - 1 ) ),
		'archive_url' => get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/blog/' ),
	);
}
