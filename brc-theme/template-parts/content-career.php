<?php
/**
 * Career card.
 *
 * @package BRC
 */

$career_location        = (string) get_post_meta( get_the_ID(), 'brc_career_location', true );
$career_department      = (string) get_post_meta( get_the_ID(), 'brc_career_department', true );
$career_employment_type = (string) get_post_meta( get_the_ID(), 'brc_career_employment_type', true );
?>

<article <?php post_class( 'brc-card brc-card--career' ); ?>>
	<a class="brc-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="brc-card__media">
				<?php the_post_thumbnail( 'brc-card' ); ?>
			</figure>
		<?php endif; ?>
		<div class="brc-card__body brc-card__body--career">
			<?php if ( $career_location ) : ?>
				<p class="brc-kicker"><?php echo esc_html( $career_location ); ?></p>
			<?php endif; ?>
			<h2><?php the_title(); ?></h2>
			<?php if ( $career_department || $career_employment_type ) : ?>
				<p class="brc-career-card__meta">
					<?php echo esc_html( implode( ' · ', array_filter( array( $career_department, $career_employment_type ) ) ) ); ?>
				</p>
			<?php endif; ?>
			<p><?php echo esc_html( has_excerpt() ? get_the_excerpt() : brc_theme_excerpt() ); ?></p>
			<span class="brc-card__cta"><?php esc_html_e( 'View Role / عرض الوظيفة', 'brc' ); ?></span>
		</div>
	</a>
</article>
