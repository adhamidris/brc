<?php
/**
 * Homepage metrics section.
 *
 * @package BRC
 */

$metrics_intro = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'metrics_intro', '' ) : '';
$metrics       = array(
	array( __( 'Land Bank', 'brc' ), '0' ),
	array( __( 'Projects', 'brc' ), '0' ),
	array( __( 'Families', 'brc' ), '0' ),
);
$rows          = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'metrics_items', array() ) : array();

if ( is_array( $rows ) && ! empty( $rows ) ) {
	$metrics = array();

	foreach ( $rows as $row ) {
		if ( empty( $row['label'] ) && empty( $row['value'] ) ) {
			continue;
		}

		$metrics[] = array(
			(string) ( $row['label'] ?? '' ),
			(string) ( $row['value'] ?? '' ),
		);
	}

	if ( empty( $metrics ) ) {
		$metrics = array(
			array( __( 'Land Bank', 'brc' ), '0' ),
			array( __( 'Projects', 'brc' ), '0' ),
			array( __( 'Families', 'brc' ), '0' ),
		);
	}
}
?>

<section class="brc-metrics brc-metrics--premium alignfull">
	<div class="brc-metrics__inner">
		<?php if ( $metrics_intro ) : ?>
			<p class="brc-section-intro brc-section-intro--metrics"><?php echo esc_html( $metrics_intro ); ?></p>
		<?php endif; ?>
		<div class="brc-metrics__grid">
			<?php foreach ( $metrics as $metric ) : ?>
				<div class="brc-metric-card">
					<p><?php echo esc_html( $metric[0] ); ?></p>
					<strong data-metric-value="<?php echo esc_attr( $metric[1] ); ?>"><?php echo esc_html( $metric[1] ); ?></strong>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
