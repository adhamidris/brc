<?php
/**
 * Single content template.
 *
 * @package BRC
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'entry entry--single' ); ?>>
		<header class="brc-page-header">
			<p class="brc-kicker"><?php brc_theme_posted_on(); ?></p>
			<h1><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="entry-hero-image">
				<?php the_post_thumbnail( 'brc-wide' ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();

