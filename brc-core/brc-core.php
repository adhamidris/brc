<?php
/**
 * Plugin Name: BRC Core
 * Description: Content types, fields, and schema helpers for BRC Developments.
 * Version: 0.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: BRC Developments
 * Text Domain: brc-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BRC_CORE_VERSION', '0.1.0' );
define( 'BRC_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BRC_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once BRC_CORE_DIR . 'inc/post-types.php';
require_once BRC_CORE_DIR . 'inc/taxonomies.php';
require_once BRC_CORE_DIR . 'inc/meta.php';
require_once BRC_CORE_DIR . 'inc/schema.php';
require_once BRC_CORE_DIR . 'inc/acf.php';
require_once BRC_CORE_DIR . 'inc/homepage.php';
require_once BRC_CORE_DIR . 'inc/shortcodes.php';
require_once BRC_CORE_DIR . 'inc/onboarding.php';

function brc_core_activate(): void {
	brc_core_register_post_types();
	brc_core_register_taxonomies();
	brc_core_run_onboarding( true );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'brc_core_activate' );

function brc_core_deactivate(): void {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'brc_core_deactivate' );
