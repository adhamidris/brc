<?php
/**
 * Page template.
 *
 * @package BRC
 */

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'entry entry--page' ); ?>>
		<header class="brc-page-header">
			<h1><?php the_title(); ?></h1>
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();

