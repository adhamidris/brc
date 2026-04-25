<?php
/**
 * Single location template.
 *
 * @package BRC
 */

get_header();

while ( have_posts() ) :
	the_post();
	$location_slug = get_post_field( 'post_name', get_the_ID() );
	$projects      = new WP_Query(
		array(
			'post_type'      => 'brc_project',
			'posts_per_page' => 6,
			'tax_query'      => array(
				array(
					'taxonomy' => 'brc_location_area',
					'field'    => 'slug',
					'terms'    => $location_slug,
				),
			),
		)
	);
	?>
	<article <?php post_class( 'entry entry--location' ); ?>>
		<header class="brc-detail-hero">
			<div class="brc-detail-hero__content">
				<p class="brc-kicker"><?php esc_html_e( 'Location', 'brc' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p><?php the_excerpt(); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="brc-detail-hero__media">
					<?php the_post_thumbnail( 'brc-hero' ); ?>
				</figure>
			<?php endif; ?>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>

		<?php if ( $projects->have_posts() ) : ?>
			<section class="brc-section">
				<div class="brc-section__inner">
					<p class="brc-kicker"><?php esc_html_e( 'Projects', 'brc' ); ?></p>
					<h2><?php esc_html_e( 'Explore this location', 'brc' ); ?></h2>
				</div>
				<div class="brc-card-grid">
					<?php
					while ( $projects->have_posts() ) :
						$projects->the_post();
						get_template_part( 'template-parts/content', 'project' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>
	</article>
	<?php
endwhile;

get_footer();

