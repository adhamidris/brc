<?php
/**
 * Theme assets.
 *
 * @package BRC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_theme_asset_version( string $relative_path ): string {
	$path = BRC_THEME_DIR . '/' . ltrim( $relative_path, '/' );

	return file_exists( $path ) ? (string) filemtime( $path ) : BRC_THEME_VERSION;
}

function brc_theme_enqueue_assets(): void {
	wp_enqueue_style(
		'brc-main',
		BRC_THEME_URI . '/assets/css/main.css',
		array(),
		brc_theme_asset_version( 'assets/css/main.css' )
	);

	wp_enqueue_script(
		'brc-main',
		BRC_THEME_URI . '/assets/js/main.js',
		array(),
		brc_theme_asset_version( 'assets/js/main.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'brc_theme_enqueue_assets' );

