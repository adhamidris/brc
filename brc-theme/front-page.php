<?php
/**
 * Front page template.
 *
 * The homepage is editor-driven so admins can reorder, remove, or replace sections.
 *
 * @package BRC
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'entry entry--front-page' ); ?>>
			<?php get_template_part( 'template-parts/home-hero' ); ?>
			<?php if ( trim( get_the_content() ) ) : ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			<?php else : ?>
				<?php get_template_part( 'template-parts/home-empty' ); ?>
			<?php endif; ?>
		</article>
		<?php
	endwhile;
endif;

get_footer();
