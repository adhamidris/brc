<?php
/**
 * Homepage lead section.
 *
 * @package BRC
 */

$lead_title          = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'lead_title', __( 'Register your interest', 'brc' ) ) : __( 'Register your interest', 'brc' );
$lead_body           = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'lead_body', __( 'Share your details and our team will contact you shortly.', 'brc' ) ) : __( 'Share your details and our team will contact you shortly.', 'brc' );
$lead_background     = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'lead_background_image', array() ) : array();
$lead_whatsapp_label = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'lead_whatsapp_label', __( 'Message us on WhatsApp', 'brc' ) ) : __( 'Message us on WhatsApp', 'brc' );
$lead_whatsapp_url   = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'lead_whatsapp_url', '' ) : '';
$form_shortcode      = function_exists( 'brc_core_get_homepage_field' ) ? trim( (string) brc_core_get_homepage_field( 'lead_form_shortcode', '' ) ) : '';
$lead_bg_url         = is_array( $lead_background ) && ! empty( $lead_background['url'] )
	? (string) $lead_background['url']
	: ( function_exists( 'brc_core_home_fallback_image_url' ) ? brc_core_home_fallback_image_url( 'lead' ) : get_theme_file_uri( 'assets/img/carousel4.jpg' ) );

$form_markup = $form_shortcode
	? do_shortcode( $form_shortcode )
	: ( function_exists( 'brc_core_lead_form_placeholder' ) ? brc_core_lead_form_placeholder() : '<p>' . esc_html__( 'Form integration pending.', 'brc' ) . '</p>' );

if ( $form_shortcode && trim( wp_strip_all_tags( $form_markup ) ) === $form_shortcode ) {
	$form_markup = function_exists( 'brc_core_lead_form_placeholder' ) ? brc_core_lead_form_placeholder() : '<p>' . esc_html__( 'Form integration pending.', 'brc' ) . '</p>';
}
?>

<section id="lead-form" class="brc-section brc-lead-cta alignfull"<?php echo $lead_bg_url ? ' style="--brc-lead-bg:url(' . esc_url( $lead_bg_url ) . ')"' : ''; ?>>
	<h2><?php echo esc_html( $lead_title ); ?></h2>
	<p><?php echo esc_html( $lead_body ); ?></p>
	<?php echo $form_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( $lead_whatsapp_url ) : ?>
		<div class="brc-lead-cta__actions">
			<a class="brc-button brc-button--glass" href="<?php echo esc_url( $lead_whatsapp_url ); ?>"><?php echo esc_html( $lead_whatsapp_label ); ?></a>
		</div>
	<?php endif; ?>
</section>
