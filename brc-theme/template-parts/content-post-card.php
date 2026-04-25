<?php
/**
 * Blog card.
 *
 * @package BRC
 */
?>

<article <?php post_class( 'brc-card brc-card--post' ); ?>>
	<a class="brc-card__link" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="brc-card__media">
				<?php the_post_thumbnail( 'brc-card' ); ?>
			</figure>
		<?php endif; ?>
		<div class="brc-card__body">
			<p class="brc-kicker"><?php brc_theme_posted_on(); ?></p>
			<h2><?php the_title(); ?></h2>
			<p><?php echo brc_theme_excerpt(); ?></p>
		</div>
	</a>
</article>

