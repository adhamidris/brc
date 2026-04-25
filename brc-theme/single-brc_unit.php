<?php
/**
 * Single unit template.
 *
 * @package BRC
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'entry entry--unit' ); ?>>
		<header class="brc-detail-hero">
			<div class="brc-detail-hero__content">
				<p class="brc-kicker"><?php esc_html_e( 'Available Unit', 'brc' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p><?php the_excerpt(); ?></p>
				<?php endif; ?>
				<a class="brc-button" href="#lead-form"><?php esc_html_e( 'Request details', 'brc' ); ?></a>
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

