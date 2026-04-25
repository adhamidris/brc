<?php
/**
 * Shortcodes.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_core_lead_form_placeholder(): string {
	if ( shortcode_exists( 'fluentform' ) || shortcode_exists( 'gravityform' ) ) {
		return '<p>' . esc_html__( 'Replace this placeholder with the selected form plugin shortcode.', 'brc-core' ) . '</p>';
	}

	ob_start();
	?>
	<form class="brc-lead-form" action="#" method="post">
		<p>
			<label>
				<?php esc_html_e( 'Name', 'brc-core' ); ?>
				<input type="text" name="brc_name" autocomplete="name" disabled>
			</label>
		</p>
		<p>
			<label>
				<?php esc_html_e( 'Phone', 'brc-core' ); ?>
				<input type="tel" name="brc_phone" autocomplete="tel" disabled>
			</label>
		</p>
		<p>
			<label>
				<?php esc_html_e( 'Email', 'brc-core' ); ?>
				<input type="email" name="brc_email" autocomplete="email" disabled>
			</label>
		</p>
		<p>
			<label>
				<?php esc_html_e( 'Interested Location', 'brc-core' ); ?>
				<input type="text" name="brc_location" disabled>
			</label>
		</p>
		<p>
			<label>
				<?php esc_html_e( 'Message', 'brc-core' ); ?>
				<textarea name="brc_message" rows="5" disabled></textarea>
			</label>
		</p>
		<button type="button" disabled><?php esc_html_e( 'Form plugin pending', 'brc-core' ); ?></button>
	</form>
	<?php

	return ob_get_clean();
}
add_shortcode( 'brc_lead_form_placeholder', 'brc_core_lead_form_placeholder' );

function brc_core_featured_projects_shortcode(): string {
	$query = new WP_Query(
		array(
			'post_type'      => 'brc_project',
			'posts_per_page' => 8,
			'meta_query'     => array(
				array(
					'key'   => 'brc_featured',
					'value' => '1',
				),
			),
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	$projects = array();
	$terms    = array();

	while ( $query->have_posts() ) :
		$query->the_post();
		$project_terms = get_the_terms( get_the_ID(), 'brc_location_area' );
		$project_data  = array(
			'id'              => get_the_ID(),
			'title'           => get_the_title(),
			'permalink'       => get_permalink(),
			'excerpt'         => wp_trim_words( get_the_excerpt(), 24 ),
			'thumbnail'       => has_post_thumbnail() ? get_the_post_thumbnail( get_the_ID(), 'brc-wide' ) : '',
			'starting_price'  => get_post_meta( get_the_ID(), 'brc_starting_price', true ),
			'payment_plan'    => get_post_meta( get_the_ID(), 'brc_payment_plan', true ),
			'completion_date' => get_post_meta( get_the_ID(), 'brc_completion_date', true ),
			'property_sizes'  => get_post_meta( get_the_ID(), 'brc_property_sizes', true ),
			'bedrooms'        => get_post_meta( get_the_ID(), 'brc_bedrooms', true ),
			'location_names'  => array(),
			'location_slugs'  => array(),
		);

		if ( ! empty( $project_terms ) && ! is_wp_error( $project_terms ) ) {
			foreach ( $project_terms as $term ) {
				$terms[ $term->slug ] = $term;
				$project_data['location_names'][] = $term->name;
				$project_data['location_slugs'][] = $term->slug;
			}
		}

		$projects[] = $project_data;
	endwhile;
	wp_reset_postdata();

	$tabs = array(
		array(
			'slug'  => 'all',
			'label' => __( 'All', 'brc-core' ),
		),
	);

	foreach ( $terms as $term ) {
		$tabs[] = array(
			'slug'  => $term->slug,
			'label' => $term->name,
		);
	}

	ob_start();
	?>
	<section class="brc-launches alignfull" data-launches>
		<div class="brc-launches__inner">
			<div class="brc-launches__header">
				<h2 class="brc-launches__title"><?php esc_html_e( 'Latest Launches', 'brc-core' ); ?></h2>
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
									<article class="brc-launch-card<?php echo 0 === $slide_index ? ' is-active' : ''; ?>" data-launch-slide>
										<div class="brc-launch-card__media">
											<div class="brc-launch-card__media-badge"><?php echo esc_html( $project['title'] ); ?></div>
											<?php if ( $project['thumbnail'] ) : ?>
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
												<?php if ( $project['completion_date'] ) : ?>
													<div>
														<dt><?php esc_html_e( 'Completion Date', 'brc-core' ); ?></dt>
														<dd><?php echo esc_html( $project['completion_date'] ); ?></dd>
													</div>
												<?php endif; ?>
												<?php if ( $project['property_sizes'] ) : ?>
													<div>
														<dt><?php esc_html_e( 'Property Sizes', 'brc-core' ); ?></dt>
														<dd><?php echo esc_html( $project['property_sizes'] ); ?></dd>
													</div>
												<?php endif; ?>
												<?php if ( $project['bedrooms'] ) : ?>
													<div>
														<dt><?php esc_html_e( 'Bedrooms', 'brc-core' ); ?></dt>
														<dd><?php echo esc_html( $project['bedrooms'] ); ?></dd>
													</div>
												<?php endif; ?>
												<?php if ( $project['starting_price'] ) : ?>
													<div>
														<dt><?php esc_html_e( 'Starting Price', 'brc-core' ); ?></dt>
														<dd><?php echo esc_html( $project['starting_price'] ); ?></dd>
													</div>
												<?php endif; ?>
											</dl>

											<div class="brc-launch-card__actions">
												<a class="brc-launch-card__cta brc-launch-card__cta--primary" href="#lead-form"><?php esc_html_e( "I'm Interested", 'brc-core' ); ?></a>
												<a class="brc-launch-card__cta brc-launch-card__cta--secondary" href="<?php echo esc_url( $project['permalink'] ); ?>">
													<?php esc_html_e( 'Learn More', 'brc-core' ); ?>
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
								<button class="brc-launches__arrow" type="button" data-launch-prev aria-label="<?php esc_attr_e( 'Previous launch', 'brc-core' ); ?>">
									<span aria-hidden="true"><?php esc_html_e( 'Prev', 'brc-core' ); ?></span>
								</button>
								<div class="brc-launches__count" aria-live="polite">
									<span data-launch-current>01</span>
									<span>/</span>
									<span><?php echo esc_html( str_pad( (string) count( $tab_projects ), 2, '0', STR_PAD_LEFT ) ); ?></span>
								</div>
								<button class="brc-launches__arrow" type="button" data-launch-next aria-label="<?php esc_attr_e( 'Next launch', 'brc-core' ); ?>">
									<span aria-hidden="true"><?php esc_html_e( 'Next', 'brc-core' ); ?></span>
								</button>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean() . brc_core_projects_story_markup() . brc_core_insights_journal_markup() . brc_core_about_brc_markup();
}
add_shortcode( 'brc_featured_projects', 'brc_core_featured_projects_shortcode' );

function brc_core_projects_story_markup(): string {
	$projects = array(
		array(
			'index'       => '01',
			'title'       => 'Dar Misr',
			'subtitle'    => __( 'A demo project used to illustrate the narrative projects section.', 'brc-core' ),
			'cta_label'   => __( 'Explore Project', 'brc-core' ),
			'cta_url'     => '#',
			'images'      => array(
				get_theme_file_uri( 'assets/img/carousel1.jpg' ),
				get_theme_file_uri( 'assets/img/carousel2.jpg' ),
			),
		),
		array(
			'index'       => '02',
			'title'       => 'Dar Ghorab',
			'subtitle'    => __( 'A second demo project that takes over the stage as the section scroll progresses.', 'brc-core' ),
			'cta_label'   => __( 'Explore Project', 'brc-core' ),
			'cta_url'     => '#',
			'images'      => array(
				get_theme_file_uri( 'assets/img/carousel3.jpg' ),
				get_theme_file_uri( 'assets/img/carousel4.jpg' ),
			),
		),
	);

	ob_start();
	?>
	<section class="brc-project-story alignfull" data-project-story>
		<div class="brc-project-story__track">
			<div class="brc-project-story__sticky">
				<?php foreach ( $projects as $index => $project ) : ?>
					<article class="brc-project-story__item<?php echo 0 === $index ? ' is-active' : ''; ?>" data-project-story-item>
						<div class="brc-project-story__copy">
							<div class="brc-project-story__content">
								<span class="brc-project-story__index"><?php echo esc_html( $project['index'] ); ?></span>
								<h2><?php echo esc_html( $project['title'] ); ?></h2>
								<p><?php echo esc_html( $project['subtitle'] ); ?></p>
								<a class="brc-project-story__cta" href="<?php echo esc_url( $project['cta_url'] ); ?>">
									<?php echo esc_html( $project['cta_label'] ); ?>
								</a>
							</div>
						</div>

						<div class="brc-project-story__stage" aria-hidden="true">
							<div class="brc-project-story__divider"></div>
							<div class="brc-project-story__image-stack">
								<?php foreach ( $project['images'] as $image_index => $image_url ) : ?>
									<figure class="brc-project-story__image<?php echo 0 === $image_index ? ' is-active' : ''; ?>" data-project-story-image>
										<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $project['title'] ); ?>">
									</figure>
								<?php endforeach; ?>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

function brc_core_insights_journal_markup(): string {
	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	$fallback_images = array(
		get_theme_file_uri( 'assets/img/carousel1.jpg' ),
		get_theme_file_uri( 'assets/img/carousel2.jpg' ),
		get_theme_file_uri( 'assets/img/carousel3.jpg' ),
		get_theme_file_uri( 'assets/img/carousel4.jpg' ),
	);

	$posts     = array();
	$post_index = 0;

	while ( $query->have_posts() ) :
		$query->the_post();

		$categories = get_the_category();
		$posts[]    = array(
			'title'      => get_the_title(),
			'permalink'  => get_permalink(),
			'excerpt'    => wp_trim_words( get_the_excerpt(), 22 ),
			'date'       => get_the_date( 'd M Y' ),
			'category'   => ! empty( $categories ) ? $categories[0]->name : __( 'Insights', 'brc-core' ),
			'image_html' => has_post_thumbnail()
				? get_the_post_thumbnail( get_the_ID(), 'large' )
				: '<img src="' . esc_url( $fallback_images[ $post_index % count( $fallback_images ) ] ) . '" alt="' . esc_attr( get_the_title() ) . '">',
		);

		$post_index++;
	endwhile;
	wp_reset_postdata();

	$featured   = array_shift( $posts );
	$archive_id = (int) get_option( 'page_for_posts' );
	$archive_url = $archive_id ? get_permalink( $archive_id ) : get_post_type_archive_link( 'post' );

	ob_start();
	?>
	<section class="brc-insights alignfull">
		<div class="brc-insights__inner">
			<div class="brc-insights__header">
				<p class="brc-kicker"><?php esc_html_e( 'Journal', 'brc-core' ); ?></p>
				<h2><?php esc_html_e( 'Market Notes', 'brc-core' ); ?></h2>
			</div>

			<div class="brc-insights__layout">
				<article class="brc-insights__featured">
					<a class="brc-insights__featured-media" href="<?php echo esc_url( $featured['permalink'] ); ?>">
						<?php echo $featured['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div class="brc-insights__featured-body">
						<div class="brc-insights__eyebrow">
							<span><?php echo esc_html( $featured['category'] ); ?></span>
							<span><?php echo esc_html( $featured['date'] ); ?></span>
						</div>
						<h3><a href="<?php echo esc_url( $featured['permalink'] ); ?>"><?php echo esc_html( $featured['title'] ); ?></a></h3>
						<p><?php echo esc_html( $featured['excerpt'] ); ?></p>
						<a class="brc-insights__link" href="<?php echo esc_url( $featured['permalink'] ); ?>">
							<?php esc_html_e( 'Read Article', 'brc-core' ); ?>
						</a>
					</div>
				</article>

				<div class="brc-insights__list">
					<?php foreach ( $posts as $post_item ) : ?>
						<article class="brc-insights__list-item">
							<a class="brc-insights__row-link" href="<?php echo esc_url( $post_item['permalink'] ); ?>">
								<div class="brc-insights__list-copy">
									<div class="brc-insights__eyebrow">
										<span><?php echo esc_html( $post_item['category'] ); ?></span>
										<span><?php echo esc_html( $post_item['date'] ); ?></span>
									</div>
									<h3><?php echo esc_html( $post_item['title'] ); ?></h3>
								</div>
								<span class="brc-insights__thumb">
									<?php echo $post_item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
							</a>
						</article>
					<?php endforeach; ?>

					<?php if ( $archive_url ) : ?>
						<div class="brc-insights__footer">
							<a class="brc-insights__archive" href="<?php echo esc_url( $archive_url ); ?>">
								<?php esc_html_e( 'View All Insights', 'brc-core' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

function brc_core_about_brc_markup(): string {
	ob_start();
	?>
	<section class="brc-about alignfull">
		<div class="brc-about__inner">
			<div class="brc-about__intro">
				<p class="brc-kicker"><?php esc_html_e( 'About BRC', 'brc-core' ); ?></p>
				<h2><?php esc_html_e( 'We build quieter, longer-lasting places with an emphasis on proportion, value, and measured growth.', 'brc-core' ); ?></h2>
				<p><?php esc_html_e( 'BRC approaches development as a long-term responsibility. The focus is not only on launch momentum, but on how each address holds its quality over time through architecture, planning, and disciplined execution.', 'brc-core' ); ?></p>
			</div>

			<div class="brc-about__media">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/carousel2.jpg' ) ); ?>" alt="<?php esc_attr_e( 'BRC architecture preview', 'brc-core' ); ?>">
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

function brc_core_locations_shortcode(): string {
	$query = new WP_Query(
		array(
			'post_type'      => 'brc_location',
			'posts_per_page' => 4,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	if ( ! $query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<section class="brc-locations-strip alignfull">
		<div class="brc-locations-strip__inner">
			<div class="brc-locations-strip__intro">
				<p class="brc-kicker"><?php esc_html_e( 'Locations', 'brc-core' ); ?></p>
				<h2><?php esc_html_e( 'Strategic destinations with dedicated SEO landing pages.', 'brc-core' ); ?></h2>
			</div>
			<div class="brc-locations-strip__list">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					?>
					<a class="brc-location-pill" href="<?php the_permalink(); ?>">
						<span><?php the_title(); ?></span>
						<small><?php echo esc_html( wp_trim_words( get_the_excerpt(), 12 ) ); ?></small>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}
add_shortcode( 'brc_locations', 'brc_core_locations_shortcode' );

function brc_core_success_metrics_shortcode(): string {
	$metrics = array(
		array( __( 'Land Bank', 'brc-core' ), '7.4M' ),
		array( __( 'Projects', 'brc-core' ), '12+' ),
		array( __( 'Families', 'brc-core' ), '17K+' ),
		array( __( 'Investment', 'brc-core' ), '5B' ),
	);

	ob_start();
	?>
	<section class="brc-metrics brc-metrics--premium alignfull">
		<div class="brc-metrics__inner">
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
	<?php

	return ob_get_clean();
}
add_shortcode( 'brc_success_metrics', 'brc_core_success_metrics_shortcode' );

function brc_core_whatsapp_url( string $message = '' ): string {
	$phone = apply_filters( 'brc_core_whatsapp_phone', '' );

	if ( ! $phone ) {
		return '#';
	}

	$url = 'https://wa.me/' . preg_replace( '/\D+/', '', $phone );

	if ( $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}

	return esc_url( $url );
}
