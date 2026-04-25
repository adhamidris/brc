<?php
/**
 * Editor patterns for controlled homepage sections.
 *
 * @package BRC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_theme_register_patterns(): void {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'brc-sections',
		array( 'label' => __( 'BRC Sections', 'brc' ) )
	);

	register_block_pattern(
		'brc/hero',
		array(
			'title'      => __( 'BRC Hero', 'brc' ),
			'categories' => array( 'brc-sections' ),
			'content'    => '<!-- wp:group {"className":"brc-hero brc-hero--image alignfull","layout":{"type":"constrained"}} --><div class="wp-block-group brc-hero brc-hero--image alignfull"><!-- wp:paragraph {"className":"brc-kicker"} --><p class="brc-kicker">BRC Developments</p><!-- /wp:paragraph --><!-- wp:heading {"level":1,"className":"brc-hero__title"} --><h1 class="wp-block-heading brc-hero__title">Architecture-led communities for a new Egyptian address.</h1><!-- /wp:heading --><!-- wp:paragraph {"className":"brc-hero__lead"} --><p class="brc-hero__lead">Premium developments shaped around location, long-term value, and a calmer standard of modern living.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"is-style-fill"} --><div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="#lead-form">Register Interest</a></div><!-- /wp:button --><!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/projects/">Explore Projects</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'brc/success-metrics',
		array(
			'title'      => __( 'BRC Success Metrics', 'brc' ),
			'categories' => array( 'brc-sections' ),
			'content'    => '<!-- wp:group {"className":"brc-section brc-metrics alignfull","layout":{"type":"constrained"}} --><div class="wp-block-group brc-section brc-metrics alignfull"><!-- wp:heading {"className":"brc-section__eyebrow"} --><h2 class="wp-block-heading brc-section__eyebrow">Facts &amp; Figures</h2><!-- /wp:heading --><!-- wp:columns {"className":"brc-metrics__grid"} --><div class="wp-block-columns brc-metrics__grid"><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p>Land Bank</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">0</h3><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p>Projects</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">0</h3><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p>Families</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">0</h3><!-- /wp:heading --></div><!-- /wp:column --></div><!-- /wp:columns --></div><!-- /wp:group -->',
		)
	);

	register_block_pattern(
		'brc/lead-cta',
		array(
			'title'      => __( 'BRC Lead CTA', 'brc' ),
			'categories' => array( 'brc-sections' ),
			'content'    => '<!-- wp:group {"className":"brc-section brc-lead-cta alignfull","layout":{"type":"constrained"}} --><div id="lead-form" class="wp-block-group brc-section brc-lead-cta alignfull"><!-- wp:heading --><h2 class="wp-block-heading">Register your interest</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Share your details and our team will contact you shortly.</p><!-- /wp:paragraph --><!-- wp:shortcode -->[brc_lead_form_placeholder]<!-- /wp:shortcode --></div><!-- /wp:group -->',
		)
	);
}
add_action( 'init', 'brc_theme_register_patterns' );
