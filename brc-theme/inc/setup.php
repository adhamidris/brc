<?php
/**
 * Theme setup.
 *
 * @package BRC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_theme_setup(): void {
	load_theme_textdomain( 'brc', BRC_THEME_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 360,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
		'navigation-widgets',
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	add_editor_style( 'assets/css/editor.css' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'brc' ),
		'footer'  => __( 'Footer Menu', 'brc' ),
	) );

	add_image_size( 'brc-hero', 1920, 1080, true );
	add_image_size( 'brc-card', 960, 720, true );
	add_image_size( 'brc-wide', 1440, 810, true );
}
add_action( 'after_setup_theme', 'brc_theme_setup' );

function brc_theme_content_width(): void {
	$GLOBALS['content_width'] = 1280;
}
add_action( 'after_setup_theme', 'brc_theme_content_width', 0 );

