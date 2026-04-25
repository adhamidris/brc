<?php
/**
 * Project card.
 *
 * @package BRC
 */

$location = get_the_terms( get_the_ID(), 'brc_location_area' );
$price    = get_post_meta( get_the_ID(), 'brc_starting_price', true );
?>

<article <?php post_class( 'brc-card brc-card--project' ); ?>>
	<a class="brc-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="brc-card__media">
				<?php the_post_thumbnail( 'brc-card' ); ?>
			</figure>
		<?php endif; ?>
		<div class="brc-card__body">
			<?php if ( ! empty( $location ) && ! is_wp_error( $location ) ) : ?>
				<p class="brc-kicker"><?php echo esc_html( $location[0]->name ); ?></p>
			<?php endif; ?>
			<h2><?php the_title(); ?></h2>
			<?php if ( $price ) : ?>
				<p><?php echo esc_html( $price ); ?></p>
			<?php else : ?>
				<p><?php echo brc_theme_excerpt(); ?></p>
			<?php endif; ?>
		</div>
	</a>
</article>

