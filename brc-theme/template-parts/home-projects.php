<?php
/**
 * Homepage project selector section.
 *
 * @package BRC
 */

$projects_title    = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'projects_section_title', __( 'Projects', 'brc' ) ) : __( 'Projects', 'brc' );
$projects_selected = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'projects_selected_projects', array() ) : array();
$projects          = function_exists( 'brc_core_home_get_project_story_items' ) ? brc_core_home_get_project_story_items( is_array( $projects_selected ) ? $projects_selected : array() ) : array();
?>

<section class="brc-project-story alignfull" data-project-story>
	<div class="brc-project-story__inner">
		<div class="brc-project-story__copy">
			<p class="brc-kicker"><?php esc_html_e( 'Selected Projects', 'brc' ); ?></p>
			<h2 class="brc-project-story__heading"><?php echo esc_html( $projects_title ); ?></h2>

			<div class="brc-project-story__selector" role="tablist" aria-label="<?php esc_attr_e( 'Projects', 'brc' ); ?>">
				<?php foreach ( $projects as $index => $project ) : ?>
					<button
						class="brc-project-story__tab<?php echo 0 === $index ? ' is-active' : ''; ?>"
						type="button"
						role="tab"
						data-project-tab
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						aria-controls="brc-project-panel-<?php echo esc_attr( $index ); ?>"
						tabindex="<?php echo 0 === $index ? '0' : '-1'; ?>"
					>
						<span class="brc-project-story__index"><?php echo esc_html( $project['index'] ); ?></span>
						<span class="brc-project-story__tab-title"><?php echo esc_html( $project['title'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="brc-project-story__details">
				<?php foreach ( $projects as $index => $project ) : ?>
					<div class="brc-project-story__detail<?php echo 0 === $index ? ' is-active' : ''; ?>" data-project-detail>
						<p><?php echo esc_html( $project['subtitle'] ); ?></p>
						<a class="brc-project-story__cta<?php echo 0 === (int) $project['id'] ? ' is-disabled' : ''; ?>" href="<?php echo esc_url( $project['cta_url'] ); ?>"<?php echo 0 === (int) $project['id'] ? ' aria-disabled="true"' : ''; ?>>
							<?php echo esc_html( $project['cta_label'] ); ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="brc-project-story__stage" aria-live="polite">
			<div class="brc-project-story__divider"></div>
			<div class="brc-project-story__panels">
				<?php foreach ( $projects as $index => $project ) : ?>
					<div
						class="brc-project-story__panel<?php echo 0 === $index ? ' is-active' : ''; ?>"
						id="brc-project-panel-<?php echo esc_attr( $index ); ?>"
						role="tabpanel"
						aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>"
						data-project-panel
					>
						<?php foreach ( $project['images'] as $image_index => $image_url ) : ?>
							<figure class="brc-project-story__image<?php echo 0 === $image_index ? ' is-active' : ''; ?>" data-project-image>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $project['title'] ); ?>">
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
