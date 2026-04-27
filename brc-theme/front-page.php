<?php
/**
 * Front page template.
 *
 * The homepage is section-driven so layout stays stable even before page content is configured.
 *
 * @package BRC
 */

get_header();
?>

<article class="entry entry--front-page">
	<?php get_template_part( 'template-parts/home-hero' ); ?>
	<?php get_template_part( 'template-parts/home-metrics' ); ?>
	<?php get_template_part( 'template-parts/home-launches' ); ?>
	<?php get_template_part( 'template-parts/home-projects' ); ?>
	<?php get_template_part( 'template-parts/home-market-notes' ); ?>
	<?php get_template_part( 'template-parts/home-about' ); ?>
	<?php get_template_part( 'template-parts/home-lead' ); ?>
</article>

<?php
get_footer();
