<?php
/**
 * Homepage launches section.
 *
 * @package BRC
 */

$launches_title    = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'launches_section_title', __( 'Latest Launches', 'brc' ) ) : __( 'Latest Launches', 'brc' );
$launches_intro    = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'launches_section_intro', '' ) : '';
$launches_count    = function_exists( 'brc_core_get_homepage_field' ) ? (int) brc_core_get_homepage_field( 'launches_count', 8 ) : 8;
$launches_selected = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'launches_selected_projects', array() ) : array();
$launches_count    = $launches_count > 0 ? $launches_count : 8;
$projects          = function_exists( 'brc_core_home_get_launch_projects' ) ? brc_core_home_get_launch_projects( $launches_count, is_array( $launches_selected ) ? $launches_selected : array() ) : array();
$terms             = array();
$tabs              = array(
	array(
		'slug'  => 'all',
		'label' => __( 'All', 'brc' ),
	),
);

foreach ( $projects as $project ) {
	if ( empty( $project['location_slugs'] ) || empty( $project['location_names'] ) ) {
		continue;
	}

	foreach ( $project['location_slugs'] as $location_index => $slug ) {
		$terms[ $slug ] = $project['location_names'][ $location_index ] ?? ucfirst( str_replace( '-', ' ', $slug ) );
	}
}

foreach ( $terms as $slug => $label ) {
	$tabs[] = array(
		'slug'  => $slug,
		'label' => $label,
	);
}
?>

<section class="brc-launches alignfull" data-launches>
	<div class="brc-launches__inner">
		<div class="brc-launches__header">
			<div>
				<h2 class="brc-launches__title"><?php echo esc_html( $launches_title ); ?></h2>
				<?php if ( $launches_intro ) : ?>
					<p class="brc-section-intro brc-section-intro--light"><?php echo esc_html( $launches_intro ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<div class="brc-launches__panels">
			<?php foreach ( $tabs as $index => $tab ) : ?>
				<?php
				$tab_projects = array_values(
					array_filter(
						$projects,
						static function ( array $project ) use ( $tab ): bool {
							return 'all' === $tab['slug'] || in_array( $tab['slug'], $project['location_slugs'], true );
						}
					)
				);
				?>
				<div
					class="brc-launches__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
					role="tabpanel"
					data-launch-panel="<?php echo esc_attr( $tab['slug'] ); ?>"
					<?php echo 0 === $index ? '' : 'hidden'; ?>
				>
					<div class="brc-launches__viewport">
						<div class="brc-launches__cards" data-launch-track>
							<?php foreach ( $tab_projects as $slide_index => $project ) : ?>
								<?php $is_pending = empty( $project['id'] ); ?>
								<article class="brc-launch-card<?php echo 0 === $slide_index ? ' is-active' : ''; ?>" data-launch-slide>
									<div class="brc-launch-card__media">
										<div class="brc-launch-card__media-badge"><?php echo esc_html( $project['title'] ); ?></div>
										<?php if ( ! empty( $project['thumbnail'] ) ) : ?>
											<?php echo $project['thumbnail']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php else : ?>
											<div class="brc-launch-card__placeholder"><?php echo esc_html( substr( $project['title'], 0, 1 ) ); ?></div>
										<?php endif; ?>
									</div>

									<div class="brc-launch-card__body">
										<div class="brc-launch-card__heading">
											<h3><?php echo esc_html( $project['title'] ); ?></h3>
											<?php if ( ! empty( $project['location_names'] ) ) : ?>
												<p class="brc-launch-card__location">
													<svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 0C3.134 0 0 3.134 0 7C0 12.25 7 16 7 16C7 16 14 12.25 14 7C14 3.134 10.866 0 7 0ZM7 9.5C5.619 9.5 4.5 8.381 4.5 7C4.5 5.619 5.619 4.5 7 4.5C8.381 4.5 9.5 5.619 9.5 7C9.5 8.381 8.381 9.5 7 9.5Z" fill="currentColor"/></svg>
													<?php echo esc_html( implode( ' / ', $project['location_names'] ) ); ?>
												</p>
											<?php endif; ?>
											<p class="brc-launch-card__excerpt"><?php echo esc_html( $project['excerpt'] ); ?></p>
										</div>

										<dl class="brc-launch-card__facts">
											<?php if ( ! empty( $project['completion_date'] ) ) : ?>
												<div>
													<dt><?php esc_html_e( 'Completion Date', 'brc' ); ?></dt>
													<dd><?php echo esc_html( $project['completion_date'] ); ?></dd>
												</div>
											<?php endif; ?>
											<?php if ( ! empty( $project['property_sizes'] ) ) : ?>
												<div>
													<dt><?php esc_html_e( 'Property Sizes', 'brc' ); ?></dt>
													<dd><?php echo esc_html( $project['property_sizes'] ); ?></dd>
												</div>
											<?php endif; ?>
											<?php if ( ! empty( $project['bedrooms'] ) ) : ?>
												<div>
													<dt><?php esc_html_e( 'Bedrooms', 'brc' ); ?></dt>
													<dd><?php echo esc_html( $project['bedrooms'] ); ?></dd>
												</div>
											<?php endif; ?>
											<?php if ( ! empty( $project['starting_price'] ) ) : ?>
												<div>
													<dt><?php esc_html_e( 'Starting Price', 'brc' ); ?></dt>
													<dd><?php echo esc_html( $project['starting_price'] ); ?></dd>
												</div>
											<?php endif; ?>
										</dl>

										<div class="brc-launch-card__actions">
											<a class="brc-launch-card__cta brc-launch-card__cta--primary" href="#lead-form"><?php esc_html_e( "I'm Interested", 'brc' ); ?></a>
											<a class="brc-launch-card__cta brc-launch-card__cta--secondary<?php echo $is_pending ? ' is-disabled' : ''; ?>" href="<?php echo esc_url( $project['permalink'] ); ?>"<?php echo $is_pending ? ' aria-disabled="true"' : ''; ?>>
												<?php esc_html_e( 'Learn More', 'brc' ); ?>
												<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left:8px;"><path d="M1 11L11 1M11 1H3.5M11 1V8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
											</a>
										</div>
									</div>
								</article>
							<?php endforeach; ?>
						</div>
					</div>
					<?php if ( count( $tab_projects ) > 1 ) : ?>
						<div class="brc-launches__controls">
							<button class="brc-launches__arrow" type="button" data-launch-prev aria-label="<?php esc_attr_e( 'Previous launch', 'brc' ); ?>">
								<span aria-hidden="true"><?php esc_html_e( 'Prev', 'brc' ); ?></span>
							</button>
							<div class="brc-launches__count" aria-live="polite">
								<span data-launch-current>01</span>
								<span>/</span>
								<span><?php echo esc_html( str_pad( (string) count( $tab_projects ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							</div>
							<button class="brc-launches__arrow" type="button" data-launch-next aria-label="<?php esc_attr_e( 'Next launch', 'brc' ); ?>">
								<span aria-hidden="true"><?php esc_html_e( 'Next', 'brc' ); ?></span>
							</button>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
