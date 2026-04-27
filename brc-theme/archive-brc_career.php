<?php
/**
 * Career archive template.
 *
 * @package BRC
 */

get_header();
?>

<section class="brc-page-header brc-page-header--careers">
	<p class="brc-kicker"><?php esc_html_e( 'Careers / الوظائف', 'brc' ); ?></p>
	<h1><?php esc_html_e( 'Join BRC / انضم إلى BRC', 'brc' ); ?></h1>
	<p class="brc-careers-intro"><?php esc_html_e( 'Explore current opportunities across development, design, delivery, and operations. اكتشف الفرص المتاحة حالياً داخل فرق التطوير والتصميم والتنفيذ والتشغيل.', 'brc' ); ?></p>
</section>

<?php if ( have_posts() ) : ?>
	<div class="brc-card-grid brc-card-grid--careers">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'career' );
		endwhile;
		?>
	</div>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<section class="brc-empty-state">
		<p class="brc-kicker"><?php esc_html_e( 'Careers / الوظائف', 'brc' ); ?></p>
		<h2><?php esc_html_e( 'No open roles right now / لا توجد وظائف مفتوحة حالياً', 'brc' ); ?></h2>
		<p><?php esc_html_e( 'We are not actively hiring at the moment. Please check back soon for future opportunities. لا توجد وظائف مفتوحة حالياً، يرجى المتابعة لاحقاً لمعرفة الفرص الجديدة.', 'brc' ); ?></p>
	</section>
<?php endif; ?>

<?php
get_footer();
