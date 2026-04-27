<?php
/**
 * Homepage market notes section.
 *
 * @package BRC
 */

$market_notes_title    = function_exists( 'brc_core_get_homepage_field' ) ? brc_core_get_homepage_field( 'market_notes_title', __( 'Market Notes', 'brc' ) ) : __( 'Market Notes', 'brc' );
$market_notes_count    = function_exists( 'brc_core_get_homepage_field_any' ) ? (int) brc_core_get_homepage_field_any( array( 'market_notes_posts_count', 'market_notes_count' ), 4 ) : 4;
$market_notes_featured = function_exists( 'brc_core_get_homepage_field' ) ? (int) brc_core_get_homepage_field( 'market_notes_featured_post', 0 ) : 0;
$market_notes_count    = $market_notes_count >= 2 ? $market_notes_count : 4;
$market_notes_data     = function_exists( 'brc_core_home_get_market_notes' ) ? brc_core_home_get_market_notes( $market_notes_count, $market_notes_featured ) : array(
	'featured'    => array(
		'title'      => __( 'Market outlook placeholder', 'brc' ),
		'permalink'  => '#',
		'excerpt'    => __( 'Market Notes content pending.', 'brc' ),
		'image_html' => '',
	),
	'posts'       => array(),
	'archive_url' => home_url( '/blog/' ),
);
?>

<section class="brc-insights alignfull">
	<div class="brc-insights__inner">
		<div class="brc-insights__header">
			<p class="brc-kicker"><?php esc_html_e( 'Journal', 'brc' ); ?></p>
			<h2><?php echo esc_html( $market_notes_title ); ?></h2>
		</div>

		<div class="brc-insights__layout">
			<article class="brc-insights__featured">
				<a class="brc-insights__featured-card" href="<?php echo esc_url( $market_notes_data['featured']['permalink'] ); ?>">
					<span class="brc-insights__featured-media">
						<?php echo $market_notes_data['featured']['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="brc-insights__featured-body">
						<h3><?php echo esc_html( $market_notes_data['featured']['title'] ); ?></h3>
						<span class="brc-insights__featured-excerpt"><?php echo esc_html( $market_notes_data['featured']['excerpt'] ); ?></span>
					</span>
				</a>
			</article>

			<div class="brc-insights__list">
				<?php foreach ( $market_notes_data['posts'] as $post_item ) : ?>
					<article class="brc-insights__list-item">
						<a class="brc-insights__row-link" href="<?php echo esc_url( $post_item['permalink'] ); ?>">
							<div class="brc-insights__list-copy">
								<h3><?php echo esc_html( $post_item['title'] ); ?></h3>
							</div>
							<span class="brc-insights__thumb">
								<?php echo $post_item['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</a>
					</article>
				<?php endforeach; ?>

				<?php if ( ! empty( $market_notes_data['archive_url'] ) ) : ?>
					<div class="brc-insights__footer">
						<a class="brc-insights__archive" href="<?php echo esc_url( $market_notes_data['archive_url'] ); ?>">
							<?php esc_html_e( 'View All Insights', 'brc' ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
