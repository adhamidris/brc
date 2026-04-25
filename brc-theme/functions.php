<?php
/**
 * BRC Developments theme bootstrap.
 *
 * @package BRC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BRC_THEME_VERSION', '0.1.0' );
define( 'BRC_THEME_DIR', get_template_directory() );
define( 'BRC_THEME_URI', get_template_directory_uri() );

require_once BRC_THEME_DIR . '/inc/setup.php';
require_once BRC_THEME_DIR . '/inc/assets.php';
require_once BRC_THEME_DIR . '/inc/template-tags.php';
require_once BRC_THEME_DIR . '/inc/patterns.php';

