<?php
/**
 * Unit archive template.
 *
 * @package BRC
 */

get_header();
?>

<section class="brc-page-header">
	<p class="brc-kicker"><?php esc_html_e( 'Properties', 'brc' ); ?></p>
	<h1><?php esc_html_e( 'Available units', 'brc' ); ?></h1>
</section>

<?php if ( have_posts() ) : ?>
	<div class="brc-card-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'project' );
		endwhile;
		?>
	</div>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<?php get_template_part( 'template-parts/content', 'none' ); ?>
<?php endif; ?>

<?php
get_footer();

