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
			<div class="entry-content">
				<?php if ( trim( get_the_content() ) ) : ?>
					<?php the_content(); ?>
				<?php elseif ( function_exists( 'brc_core_get_default_homepage_content' ) ) : ?>
					<?php echo do_blocks( brc_core_get_default_homepage_content() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/home-empty' ); ?>
				<?php endif; ?>
			</div>
		</article>
		<?php
	endwhile;
endif;

get_footer();
