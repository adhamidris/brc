<?php
/**
 * Blog index template.
 *
 * @package BRC
 */

get_header();
?>

<section class="brc-page-header">
	<p class="brc-kicker"><?php esc_html_e( 'Insights', 'brc' ); ?></p>
	<h1><?php esc_html_e( 'Latest writings', 'brc' ); ?></h1>
</section>

<?php if ( have_posts() ) : ?>
	<div class="brc-card-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'post-card' );
		endwhile;
		?>
	</div>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<?php get_template_part( 'template-parts/content', 'none' ); ?>
<?php endif; ?>

<?php
get_footer();

