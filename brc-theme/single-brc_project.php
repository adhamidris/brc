<?php
/**
 * Single project template.
 *
 * @package BRC
 */

get_header();

while ( have_posts() ) :
	the_post();
	$location_terms = get_the_terms( get_the_ID(), 'brc_location_area' );
	?>
	<article <?php post_class( 'entry entry--project' ); ?>>
		<header class="brc-detail-hero">
			<div class="brc-detail-hero__content">
				<?php if ( ! empty( $location_terms ) && ! is_wp_error( $location_terms ) ) : ?>
					<p class="brc-kicker"><?php echo esc_html( $location_terms[0]->name ); ?></p>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p><?php the_excerpt(); ?></p>
				<?php endif; ?>
				<div class="brc-detail-hero__actions">
					<a class="brc-button" href="#lead-form"><?php esc_html_e( 'I am interested', 'brc' ); ?></a>
					<a class="brc-button brc-button--ghost" href="<?php echo esc_url( get_post_type_archive_link( 'brc_project' ) ); ?>"><?php esc_html_e( 'All projects', 'brc' ); ?></a>
				</div>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="brc-detail-hero__media">
					<?php the_post_thumbnail( 'brc-hero' ); ?>
				</figure>
			<?php endif; ?>
		</header>

		<div class="brc-detail-layout">
			<aside class="brc-detail-layout__aside">
				<?php get_template_part( 'template-parts/listing-meta' ); ?>
			</aside>
			<div class="entry-content brc-detail-layout__content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();

