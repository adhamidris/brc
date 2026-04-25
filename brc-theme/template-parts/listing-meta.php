<?php
/**
 * Listing metadata.
 *
 * @package BRC
 */

$fields = array(
	'brc_starting_price'  => __( 'Starting Price', 'brc' ),
	'brc_payment_plan'    => __( 'Payment Plan', 'brc' ),
	'brc_completion_date' => __( 'Completion Date', 'brc' ),
	'brc_property_sizes'  => __( 'Property Sizes', 'brc' ),
	'brc_bedrooms'        => __( 'Bedrooms', 'brc' ),
);

$items = array();

foreach ( $fields as $key => $label ) {
	$value = get_post_meta( get_the_ID(), $key, true );

	if ( $value ) {
		$items[] = array(
			'label' => $label,
			'value' => $value,
		);
	}
}

if ( ! $items ) {
	return;
}
?>

<dl class="brc-listing-meta">
	<?php foreach ( $items as $item ) : ?>
		<div class="brc-listing-meta__item">
			<dt><?php echo esc_html( $item['label'] ); ?></dt>
			<dd><?php echo esc_html( $item['value'] ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>

