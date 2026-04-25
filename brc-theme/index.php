<?php
/**
 * Fallback template.
 *
 * @package BRC
 */

get_header();
?>

<section class="brc-page-header">
	<h1><?php bloginfo( 'name' ); ?></h1>
</section>

<?php
if ( have_posts() ) :
	?>
	<div class="brc-card-grid">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;
		?>
	</div>
	<?php
	the_posts_pagination();
else :
	get_template_part( 'template-parts/content', 'none' );
endif;

get_footer();

