<?php
/**
 * Career metadata list.
 *
 * @package BRC
 */

$career_fields = array(
	'brc_career_location'        => __( 'Location / الموقع', 'brc' ),
	'brc_career_department'      => __( 'Department / القسم', 'brc' ),
	'brc_career_employment_type' => __( 'Employment Type / نوع الوظيفة', 'brc' ),
);

$career_items = array();

foreach ( $career_fields as $career_key => $career_label ) {
	$career_value = get_post_meta( get_the_ID(), $career_key, true );

	if ( $career_value ) {
		$career_items[] = array(
			'label' => $career_label,
			'value' => $career_value,
		);
	}
}

if ( ! $career_items ) {
	return;
}
?>

<dl class="brc-listing-meta brc-listing-meta--career">
	<?php foreach ( $career_items as $career_item ) : ?>
		<div class="brc-listing-meta__item">
			<dt><?php echo esc_html( $career_item['label'] ); ?></dt>
			<dd><?php echo esc_html( $career_item['value'] ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>
