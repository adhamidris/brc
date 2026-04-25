<?php
/**
 * Structured listing fields.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function brc_core_meta_fields(): array {
	return array(
		'brc_starting_price'  => __( 'Starting Price', 'brc-core' ),
		'brc_payment_plan'    => __( 'Payment Plan', 'brc-core' ),
		'brc_completion_date' => __( 'Completion Date', 'brc-core' ),
		'brc_property_sizes'  => __( 'Property Sizes', 'brc-core' ),
		'brc_bedrooms'        => __( 'Bedrooms', 'brc-core' ),
		'brc_whatsapp_text'   => __( 'WhatsApp Message', 'brc-core' ),
	);
}

function brc_core_register_meta(): void {
	foreach ( array( 'brc_project', 'brc_unit' ) as $post_type ) {
		foreach ( array_keys( brc_core_meta_fields() ) as $field ) {
			register_post_meta(
				$post_type,
				$field,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => static function (): bool {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}

		register_post_meta(
			$post_type,
			'brc_featured',
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => static function (): bool {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'brc_core_register_meta' );

function brc_core_add_meta_boxes(): void {
	foreach ( array( 'brc_project', 'brc_unit' ) as $post_type ) {
		add_meta_box(
			'brc_listing_details',
			__( 'Listing Details', 'brc-core' ),
			'brc_core_render_listing_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'brc_core_add_meta_boxes' );

function brc_core_render_listing_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'brc_save_listing_meta', 'brc_listing_meta_nonce' );

	foreach ( brc_core_meta_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
		</p>
		<?php
	}

	$featured = (bool) get_post_meta( $post->ID, 'brc_featured', true );
	?>
	<p>
		<label>
			<input type="checkbox" name="brc_featured" value="1" <?php checked( $featured ); ?>>
			<?php esc_html_e( 'Feature this item in showcase sections', 'brc-core' ); ?>
		</label>
	</p>
	<?php
}

function brc_core_save_listing_meta( int $post_id ): void {
	if (
		! isset( $_POST['brc_listing_meta_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brc_listing_meta_nonce'] ) ), 'brc_save_listing_meta' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array_keys( brc_core_meta_fields() ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}

	update_post_meta( $post_id, 'brc_featured', isset( $_POST['brc_featured'] ) ? '1' : '0' );
}
add_action( 'save_post_brc_project', 'brc_core_save_listing_meta' );
add_action( 'save_post_brc_unit', 'brc_core_save_listing_meta' );
