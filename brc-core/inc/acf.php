<?php
/**
 * ACF integration and homepage field helpers.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the effective front page ID for homepage-backed fields.
 */
function brc_core_get_homepage_id(): int {
	$front_page_id = (int) get_option( 'page_on_front' );

	if ( $front_page_id > 0 ) {
		return $front_page_id;
	}

	$queried_object_id = get_queried_object_id();

	return $queried_object_id ? (int) $queried_object_id : 0;
}

/**
 * Retrieve a homepage field with a stable fallback when ACF is missing or empty.
 *
 * @param string $key     Field key/name.
 * @param mixed  $default Fallback value.
 * @param int    $post_id Optional post ID override.
 * @return mixed
 */
function brc_core_get_homepage_field( string $key, $default = '', int $post_id = 0 ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$post_id = $post_id ?: brc_core_get_homepage_id();

	if ( ! $post_id ) {
		return $default;
	}

	$value = get_field( $key, $post_id );

	if ( null === $value || '' === $value || array() === $value ) {
		return $default;
	}

	return $value;
}

/**
 * Retrieve the first non-empty homepage field from a list of keys.
 *
 * @param array<int, string> $keys    Candidate field names.
 * @param mixed              $default Fallback value.
 * @param int                $post_id Optional post ID override.
 * @return mixed
 */
function brc_core_get_homepage_field_any( array $keys, $default = '', int $post_id = 0 ) {
	foreach ( $keys as $key ) {
		$value = brc_core_get_homepage_field( $key, null, $post_id );

		if ( null === $value || '' === $value || array() === $value ) {
			continue;
		}

		return $value;
	}

	return $default;
}

/**
 * Return the bilingual homepage editing guidance used in admin notices and helper copy.
 *
 * @param bool $wrap Whether to wrap the guidance in panel markup.
 */
function brc_core_get_homepage_editor_guidance_html( bool $wrap = true ): string {
	$english = '<strong>' . esc_html__( 'Homepage editing lives in the BRC Homepage Content tabs below. Do not use the standard page content area to build the homepage.', 'brc-core' ) . '</strong>';
	$arabic  = '<strong dir="rtl" lang="ar">' . esc_html__( 'تعديل الصفحة الرئيسية يتم من خلال تبويبات محتوى الصفحة الرئيسية بالأسفل. فضلاً لا تستخدم مساحة محتوى الصفحة العادية لبناء الصفحة الرئيسية.', 'brc-core' ) . '</strong>';
	$details = '<p>' . esc_html__( 'Use the tabs for Hero, Metrics, Launches, Projects Selector, Market Notes, About, and Lead Section. Dynamic project and article content is still managed in Projects and Posts.', 'brc-core' ) . '</p>';
	$details .= '<p dir="rtl" lang="ar">' . esc_html__( 'استخدم التبويبات الخاصة بالهيدر، الإحصائيات، أحدث الإطلاقات، مختار المشاريع، ملاحظات السوق، نبذة عنا، وقسم التواصل. أما محتوى المشاريع والمقالات الديناميكي فيتم تعديله من قسم المشاريع والمقالات.', 'brc-core' ) . '</p>';
	$content = $english . $details . $arabic;

	if ( ! $wrap ) {
		return $content;
	}

	return '<div class="brc-homepage-guidance">' . $content . '</div>';
}

/**
 * Default homepage editor content used for onboarding and structured handoff.
 */
function brc_core_get_default_homepage_content(): string {
	return <<<HTML
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
HTML;
}

/**
 * Check whether the supplied homepage content still uses the old shortcode-based editor body.
 */
function brc_core_homepage_content_uses_legacy_shortcodes( string $content ): bool {
	$legacy_markers = array(
		'[brc_success_metrics]',
		'[brc_featured_projects]',
		'[brc_lead_section]',
		'"slug":"brc/lead-cta"',
	);

	foreach ( $legacy_markers as $legacy_marker ) {
		if ( false !== strpos( $content, $legacy_marker ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Replace old shortcode-heavy homepage editor content with a cleaner internal note.
 */
function brc_core_sync_homepage_editor_content( int $post_id ): void {
	$post = get_post( $post_id );

	if ( ! $post || 'page' !== $post->post_type ) {
		return;
	}

	$content = (string) $post->post_content;

	if ( '' !== trim( $content ) && ! brc_core_homepage_content_uses_legacy_shortcodes( $content ) ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => $post_id,
			'post_content' => brc_core_get_default_homepage_content(),
		)
	);
}

/**
 * Return the currently edited admin post ID when available.
 */
function brc_core_get_current_admin_post_id(): int {
	if ( ! is_admin() ) {
		return 0;
	}

	$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

	if ( ! $post_id && isset( $_POST['post_ID'] ) ) {
		$post_id = absint( wp_unslash( $_POST['post_ID'] ) );
	}

	return $post_id;
}

/**
 * Check whether a given post ID is the configured homepage.
 */
function brc_core_is_homepage_post( int $post_id ): bool {
	return $post_id > 0 && (int) get_option( 'page_on_front' ) === $post_id;
}

/**
 * Check whether the current admin screen is editing the configured homepage.
 */
function brc_core_is_homepage_edit_screen(): bool {
	$post_id = brc_core_get_current_admin_post_id();

	return brc_core_is_homepage_post( $post_id );
}

/**
 * Register ACF groups for the BRC homepage editing experience.
 */
function brc_core_register_acf_groups(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_brc_homepage_sections',
			'title'    => __( 'BRC Homepage Content', 'brc-core' ),
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'style'    => 'seamless',
			'fields'   => array(
				array(
					'key'          => 'field_brc_homepage_guidance',
					'label'        => __( 'Editing Guidance', 'brc-core' ),
					'name'         => '',
					'type'         => 'message',
					'message'      => brc_core_get_homepage_editor_guidance_html(),
					'new_lines'    => 'wpautop',
					'esc_html'     => 0,
				),
				array(
					'key'   => 'field_brc_tab_hero',
					'label' => __( 'Hero', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_brc_hero_kicker',
					'label'         => __( 'Hero Kicker', 'brc-core' ),
					'name'          => 'hero_kicker',
					'type'          => 'text',
					'instructions'  => __( 'Short label above the headline. Keep it to 2 to 4 words.', 'brc-core' ),
					'default_value' => 'BRC Developments',
				),
				array(
					'key'           => 'field_brc_hero_title',
					'label'         => __( 'Hero Title', 'brc-core' ),
					'name'          => 'hero_title',
					'type'          => 'textarea',
					'rows'          => 3,
					'new_lines'     => 'br',
					'instructions'  => __( 'Main statement. Aim for 6 to 14 words.', 'brc-core' ),
					'default_value' => 'Architecture-led communities for a new Egyptian address.',
				),
				array(
					'key'           => 'field_brc_hero_body',
					'label'         => __( 'Hero Body', 'brc-core' ),
					'name'          => 'hero_body',
					'type'          => 'textarea',
					'rows'          => 4,
					'new_lines'     => 'br',
					'instructions'  => __( 'Supporting copy. Keep it tight, ideally 18 to 35 words.', 'brc-core' ),
					'default_value' => 'Premium real estate developments shaped around location, architecture, and long-term value.',
				),
				array(
					'key'           => 'field_brc_hero_background_image',
					'label'         => __( 'Hero Background Image', 'brc-core' ),
					'name'          => 'hero_background_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => __( 'Landscape image, ideally 16:9 or wider. Use architecture or lifestyle imagery dark enough for white text.', 'brc-core' ),
				),
				array(
					'key'           => 'field_brc_hero_primary_label',
					'label'         => __( 'Primary CTA Label', 'brc-core' ),
					'name'          => 'hero_primary_label',
					'type'          => 'text',
					'instructions'  => __( 'Short action label. 2 to 4 words works best.', 'brc-core' ),
					'default_value' => 'Register Interest',
				),
				array(
					'key'           => 'field_brc_hero_primary_url',
					'label'         => __( 'Primary CTA URL', 'brc-core' ),
					'name'          => 'hero_primary_url',
					'type'          => 'url',
					'instructions'  => __( 'Usually an anchor like #lead-form or a full landing-page URL.', 'brc-core' ),
					'default_value' => '#lead-form',
				),
				array(
					'key'           => 'field_brc_hero_secondary_label',
					'label'         => __( 'Secondary CTA Label', 'brc-core' ),
					'name'          => 'hero_secondary_label',
					'type'          => 'text',
					'instructions'  => __( 'Optional secondary action. Keep it brief.', 'brc-core' ),
					'default_value' => 'Explore Projects',
				),
				array(
					'key'   => 'field_brc_hero_secondary_url',
					'label' => __( 'Secondary CTA URL', 'brc-core' ),
					'name'  => 'hero_secondary_url',
					'type'  => 'url',
				),
				array(
					'key'           => 'field_brc_hero_show_whatsapp',
					'label'         => __( 'Show WhatsApp CTA', 'brc-core' ),
					'name'          => 'hero_show_whatsapp',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
				),
				array(
					'key'          => 'field_brc_hero_whatsapp_url',
					'label'        => __( 'Hero WhatsApp URL', 'brc-core' ),
					'name'         => 'hero_whatsapp_url',
					'type'         => 'url',
					'instructions' => __( 'Optional direct WhatsApp link for the hero section.', 'brc-core' ),
				),
				array(
					'key'   => 'field_brc_tab_metrics',
					'label' => __( 'Metrics', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'          => 'field_brc_metrics_intro',
					'label'        => __( 'Metrics Intro', 'brc-core' ),
					'name'         => 'metrics_intro',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => 'br',
					'instructions' => __( 'Optional line above the metrics. Keep it concise.', 'brc-core' ),
				),
				array(
					'key'          => 'field_brc_metrics_items',
					'label'        => __( 'Metrics', 'brc-core' ),
					'name'         => 'metrics_items',
					'type'         => 'repeater',
					'button_label' => __( 'Add Metric', 'brc-core' ),
					'layout'       => 'table',
					'instructions' => __( 'Add 3 to 4 headline metrics. Use short labels and values like 24, 12+, or 1.8M.', 'brc-core' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_brc_metric_label',
							'label' => __( 'Label', 'brc-core' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_brc_metric_value',
							'label' => __( 'Value', 'brc-core' ),
							'name'  => 'value',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'   => 'field_brc_tab_launches',
					'label' => __( 'Latest Launches', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_brc_launches_section_title',
					'label'         => __( 'Latest Launches Title', 'brc-core' ),
					'name'          => 'launches_section_title',
					'type'          => 'text',
					'instructions'  => __( 'Section heading above the dynamic launches carousel.', 'brc-core' ),
					'default_value' => 'Latest Launches',
				),
				array(
					'key'          => 'field_brc_launches_section_intro',
					'label'        => __( 'Latest Launches Intro', 'brc-core' ),
					'name'         => 'launches_section_intro',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => 'br',
					'instructions' => __( 'Optional editorial intro. 18 to 35 words is ideal.', 'brc-core' ),
				),
				array(
					'key'           => 'field_brc_launches_count',
					'label'         => __( 'Latest Launches Count', 'brc-core' ),
					'name'          => 'launches_count',
					'type'          => 'number',
					'instructions'  => __( 'How many projects to show when no manual selection is set.', 'brc-core' ),
					'default_value' => 8,
					'min'           => 1,
					'max'           => 12,
				),
				array(
					'key'           => 'field_brc_launches_selected_projects',
					'label'         => __( 'Selected Launch Projects', 'brc-core' ),
					'name'          => 'launches_selected_projects',
					'type'          => 'relationship',
					'post_type'     => array( 'brc_project' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
					'instructions'  => __( 'Optional manual override. Leave empty to use featured projects automatically.', 'brc-core' ),
				),
				array(
					'key'   => 'field_brc_tab_projects',
					'label' => __( 'Projects Selector', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_brc_projects_section_title',
					'label'         => __( 'Projects Section Title', 'brc-core' ),
					'name'          => 'projects_section_title',
					'type'          => 'text',
					'instructions'  => __( 'Heading above the selected projects section.', 'brc-core' ),
					'default_value' => 'Projects',
				),
				array(
					'key'           => 'field_brc_projects_section_intro',
					'label'         => __( 'Projects Section Intro', 'brc-core' ),
					'name'          => 'projects_section_intro',
					'type'          => 'textarea',
					'rows'          => 3,
					'new_lines'     => 'br',
					'instructions'  => __( 'Short overview for the selector. Aim for 15 to 30 words.', 'brc-core' ),
					'default_value' => 'A selected view of the addresses shaping the next chapter of the portfolio.',
				),
				array(
					'key'           => 'field_brc_projects_selected_projects',
					'label'         => __( 'Selected Projects', 'brc-core' ),
					'name'          => 'projects_selected_projects',
					'type'          => 'relationship',
					'post_type'     => array( 'brc_project' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
					'instructions'  => __( 'Pick the projects to feature here. Leave empty to use featured projects.', 'brc-core' ),
				),
				array(
					'key'   => 'field_brc_tab_market_notes',
					'label' => __( 'Market Notes', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_brc_market_notes_title',
					'label'         => __( 'Market Notes Title', 'brc-core' ),
					'name'          => 'market_notes_title',
					'type'          => 'text',
					'instructions'  => __( 'Section heading above the editorial feed.', 'brc-core' ),
					'default_value' => 'Market Notes',
				),
				array(
					'key'           => 'field_brc_market_notes_featured_post',
					'label'         => __( 'Featured Market Note', 'brc-core' ),
					'name'          => 'market_notes_featured_post',
					'type'          => 'post_object',
					'post_type'     => array( 'post' ),
					'return_format' => 'id',
					'ui'            => 1,
					'instructions'  => __( 'Optional manual featured article. Leave empty to feature the latest post automatically.', 'brc-core' ),
				),
				array(
					'key'           => 'field_brc_market_notes_posts_count',
					'label'         => __( 'Market Notes Count', 'brc-core' ),
					'name'          => 'market_notes_posts_count',
					'type'          => 'number',
					'instructions'  => __( 'Total number of posts including the featured card.', 'brc-core' ),
					'default_value' => 4,
					'min'           => 2,
					'max'           => 6,
				),
				array(
					'key'   => 'field_brc_tab_about',
					'label' => __( 'About', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_brc_about_kicker',
					'label'         => __( 'About Kicker', 'brc-core' ),
					'name'          => 'about_kicker',
					'type'          => 'text',
					'instructions'  => __( 'Small label above the About heading.', 'brc-core' ),
					'default_value' => 'About BRC',
				),
				array(
					'key'           => 'field_brc_about_title',
					'label'         => __( 'About Title', 'brc-core' ),
					'name'          => 'about_title',
					'type'          => 'textarea',
					'rows'          => 3,
					'new_lines'     => 'br',
					'instructions'  => __( 'Main About heading. Keep it to 8 to 18 words.', 'brc-core' ),
					'default_value' => 'We build quieter, longer-lasting places with an emphasis on proportion, value, and measured growth.',
				),
				array(
					'key'           => 'field_brc_about_body',
					'label'         => __( 'About Body', 'brc-core' ),
					'name'          => 'about_body',
					'type'          => 'textarea',
					'rows'          => 5,
					'new_lines'     => 'br',
					'instructions'  => __( 'One concise brand statement or paragraph. Avoid turning this into a full company profile.', 'brc-core' ),
					'default_value' => 'BRC approaches development as a long-term responsibility. The focus is not only on launch momentum, but on how each address holds its quality over time through architecture, planning, and disciplined execution.',
				),
				array(
					'key'           => 'field_brc_about_image',
					'label'         => __( 'About Image', 'brc-core' ),
					'name'          => 'about_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => __( 'Landscape image, ideally architecture or lifestyle photography. Avoid logos or text-heavy artwork.', 'brc-core' ),
				),
				array(
					'key'   => 'field_brc_tab_lead',
					'label' => __( 'Lead Section', 'brc-core' ),
					'name'  => '',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_brc_lead_title',
					'label'         => __( 'Lead Title', 'brc-core' ),
					'name'          => 'lead_title',
					'type'          => 'textarea',
					'rows'          => 3,
					'new_lines'     => 'br',
					'instructions'  => __( 'Direct closing headline for the conversion section.', 'brc-core' ),
					'default_value' => 'Register your interest',
				),
				array(
					'key'           => 'field_brc_lead_body',
					'label'         => __( 'Lead Body', 'brc-core' ),
					'name'          => 'lead_body',
					'type'          => 'textarea',
					'rows'          => 4,
					'new_lines'     => 'br',
					'instructions'  => __( 'Short explanation before the form or WhatsApp action.', 'brc-core' ),
					'default_value' => 'Share your details and our team will contact you shortly.',
				),
				array(
					'key'           => 'field_brc_lead_background_image',
					'label'         => __( 'Lead Background Image', 'brc-core' ),
					'name'          => 'lead_background_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'instructions'  => __( 'Wide image, ideally atmospheric and dark enough for white text overlays.', 'brc-core' ),
				),
				array(
					'key'          => 'field_brc_lead_form_shortcode',
					'label'        => __( 'Lead Form Shortcode', 'brc-core' ),
					'name'         => 'lead_form_shortcode',
					'type'         => 'text',
					'instructions' => __( 'Paste the chosen form plugin shortcode here when the live form is ready.', 'brc-core' ),
				),
				array(
					'key'           => 'field_brc_lead_whatsapp_label',
					'label'         => __( 'Lead WhatsApp Label', 'brc-core' ),
					'name'          => 'lead_whatsapp_label',
					'type'          => 'text',
					'instructions'  => __( 'Optional support action under the form. Keep it brief.', 'brc-core' ),
					'default_value' => 'Message us on WhatsApp',
				),
				array(
					'key'          => 'field_brc_lead_whatsapp_url',
					'label'        => __( 'Lead WhatsApp URL', 'brc-core' ),
					'name'         => 'lead_whatsapp_url',
					'type'         => 'url',
					'instructions' => __( 'Optional direct WhatsApp link for the lead section.', 'brc-core' ),
				),
			),
		)
	);
}
add_action( 'acf/init', 'brc_core_register_acf_groups' );

/**
 * Disable the block editor for the front page so the structured ACF tabs stay primary.
 */
function brc_core_disable_block_editor_for_homepage( bool $use_block_editor, WP_Post $post ): bool {
	if ( 'page' === $post->post_type && brc_core_is_homepage_post( (int) $post->ID ) ) {
		return false;
	}

	return $use_block_editor;
}
add_filter( 'use_block_editor_for_post', 'brc_core_disable_block_editor_for_homepage', 10, 2 );

/**
 * Print a bilingual admin message on the homepage edit screen.
 */
function brc_core_render_homepage_editor_notice( WP_Post $post ): void {
	if ( ! brc_core_is_homepage_post( (int) $post->ID ) ) {
		return;
	}

	printf(
		'<div class="notice notice-info inline brc-homepage-admin-notice">%s</div>',
		brc_core_get_homepage_editor_guidance_html( false )
	);
}
add_action( 'edit_form_after_title', 'brc_core_render_homepage_editor_notice' );

/**
 * Hide the standard page body editor on the homepage edit screen.
 */
function brc_core_homepage_admin_css(): void {
	if ( ! brc_core_is_homepage_edit_screen() ) {
		return;
	}
	?>
	<style>
		#postdivrich,
		.post-type-page #postdivrich {
			display: none !important;
		}

		.brc-homepage-guidance,
		.brc-homepage-admin-notice p {
			line-height: 1.6;
		}

		.brc-homepage-guidance p,
		.brc-homepage-admin-notice p {
			margin: 0.4em 0;
		}
	</style>
	<?php
}
add_action( 'admin_head-post.php', 'brc_core_homepage_admin_css' );
add_action( 'admin_head-post-new.php', 'brc_core_homepage_admin_css' );

/**
 * Encourage the structured editing stack when it is missing.
 */
function brc_core_acf_admin_notice(): void {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		printf(
			'<div class="notice notice-info"><p>%s</p></div>',
			esc_html__( 'BRC homepage editing works best with ACF Pro. Without it, the site still renders, but homepage fields and structured onboarding controls are unavailable. يفضل تشغيل ACF Pro حتى تظهر أدوات تعديل الصفحة الرئيسية بشكل كامل.', 'brc-core' )
		);
	}
}
add_action( 'admin_notices', 'brc_core_acf_admin_notice' );
