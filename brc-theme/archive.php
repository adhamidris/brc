<?php
/**
 * Archive template.
 *
 * @package BRC
 */

get_header();
?>

<section class="brc-page-header">
	<p class="brc-kicker"><?php esc_html_e( 'Archive', 'brc' ); ?></p>
	<h1><?php the_archive_title(); ?></h1>
	<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
</section>

<?php if ( have_posts() ) : ?>
	<div class="brc-card-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;
		?>
	</div>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<?php get_template_part( 'template-parts/content', 'none' ); ?>
<?php endif; ?>

<?php
get_footer();

