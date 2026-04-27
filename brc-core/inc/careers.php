<?php
/**
 * Careers and job applications.
 *
 * @package BRC_Core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register career and application post types plus the apply rewrite route.
 */
function brc_core_register_career_post_types(): void {
	register_post_type(
		'brc_career',
		array(
			'labels'       => array(
				'name'               => __( 'Careers / الوظائف', 'brc-core' ),
				'singular_name'      => __( 'Career / وظيفة', 'brc-core' ),
				'add_new_item'       => __( 'Add New Career / أضف وظيفة جديدة', 'brc-core' ),
				'edit_item'          => __( 'Edit Career / تعديل الوظيفة', 'brc-core' ),
				'new_item'           => __( 'New Career / وظيفة جديدة', 'brc-core' ),
				'view_item'          => __( 'View Career / عرض الوظيفة', 'brc-core' ),
				'search_items'       => __( 'Search Careers / ابحث في الوظائف', 'brc-core' ),
				'not_found'          => __( 'No careers found / لا توجد وظائف', 'brc-core' ),
				'not_found_in_trash' => __( 'No careers found in Trash / لا توجد وظائف في سلة المهملات', 'brc-core' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-id-alt',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'careers' ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		)
	);

	register_post_type(
		'brc_application',
		array(
			'labels'       => array(
				'name'               => __( 'Applications / الطلبات', 'brc-core' ),
				'singular_name'      => __( 'Application / طلب', 'brc-core' ),
				'add_new_item'       => __( 'Add New Application / أضف طلباً جديداً', 'brc-core' ),
				'edit_item'          => __( 'Review Application / مراجعة الطلب', 'brc-core' ),
				'new_item'           => __( 'New Application / طلب جديد', 'brc-core' ),
				'view_item'          => __( 'View Application / عرض الطلب', 'brc-core' ),
				'search_items'       => __( 'Search Applications / ابحث في الطلبات', 'brc-core' ),
				'not_found'          => __( 'No applications found / لا توجد طلبات', 'brc-core' ),
				'not_found_in_trash' => __( 'No applications found in Trash / لا توجد طلبات في سلة المهملات', 'brc-core' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_rest' => false,
			'show_in_menu' => 'edit.php?post_type=brc_career',
			'menu_icon'    => 'dashicons-portfolio',
			'map_meta_cap' => true,
			'supports'     => array( 'title' ),
		)
	);

	add_rewrite_tag( '%brc_career_apply%', '([^&]+)' );
	add_rewrite_rule(
		'^careers/([^/]+)/apply/?$',
		'index.php?brc_career=$matches[1]&brc_career_apply=1',
		'top'
	);
}
add_action( 'init', 'brc_core_register_career_post_types' );

/**
 * Add the custom apply query var.
 *
 * @param array<int, string> $vars Query vars.
 * @return array<int, string>
 */
function brc_core_career_query_vars( array $vars ): array {
	$vars[] = 'brc_career_apply';

	return $vars;
}
add_filter( 'query_vars', 'brc_core_career_query_vars' );

/**
 * Career detail fields.
 *
 * @return array<string, string>
 */
function brc_core_career_meta_fields(): array {
	return array(
		'brc_career_location'        => __( 'Location / الموقع', 'brc-core' ),
		'brc_career_department'      => __( 'Department / القسم', 'brc-core' ),
		'brc_career_employment_type' => __( 'Employment Type / نوع الوظيفة', 'brc-core' ),
		'brc_career_apply_label'     => __( 'Apply CTA Label / نص زر التقديم', 'brc-core' ),
	);
}

/**
 * Status options for careers.
 *
 * @return array<string, string>
 */
function brc_core_career_status_options(): array {
	return array(
		'open'   => __( 'Open / مفتوحة', 'brc-core' ),
		'closed' => __( 'Closed / مغلقة', 'brc-core' ),
	);
}

/**
 * Return whether a career is open for public viewing and applications.
 */
function brc_core_is_career_open( int $career_id ): bool {
	$status = (string) get_post_meta( $career_id, 'brc_career_status', true );

	return '' === $status || 'open' === $status;
}

/**
 * Build the dedicated apply URL for a career.
 */
function brc_core_get_career_apply_url( int $career_id ): string {
	return user_trailingslashit( trailingslashit( get_permalink( $career_id ) ) . 'apply' );
}

/**
 * Return whether the current request is a career apply endpoint.
 */
function brc_core_is_career_apply_request(): bool {
	return is_singular( 'brc_career' ) && '1' === (string) get_query_var( 'brc_career_apply', '' );
}

/**
 * Hide closed careers from public archives.
 */
function brc_core_filter_career_queries( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_post_type_archive( 'brc_career' ) ) {
		$query->set(
			'meta_query',
			array(
				'relation' => 'OR',
				array(
					'key'     => 'brc_career_status',
					'value'   => 'open',
					'compare' => '=',
				),
				array(
					'key'     => 'brc_career_status',
					'compare' => 'NOT EXISTS',
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'brc_core_filter_career_queries' );

/**
 * Prevent closed careers from being publicly visible.
 */
function brc_core_guard_closed_careers(): void {
	if ( ! is_singular( 'brc_career' ) ) {
		return;
	}

	$career_id = get_queried_object_id();

	if ( ! $career_id || brc_core_is_career_open( $career_id ) || current_user_can( 'edit_post', $career_id ) ) {
		return;
	}

	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'brc_core_guard_closed_careers' );

/**
 * Add admin boxes for careers and applications.
 */
function brc_core_add_career_meta_boxes(): void {
	add_meta_box(
		'brc_career_details',
		__( 'Career Details / تفاصيل الوظيفة', 'brc-core' ),
		'brc_core_render_career_meta_box',
		'brc_career',
		'normal',
		'high'
	);

	add_meta_box(
		'brc_application_details',
		__( 'Application Details / تفاصيل الطلب', 'brc-core' ),
		'brc_core_render_application_meta_box',
		'brc_application',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'brc_core_add_career_meta_boxes' );

/**
 * Render the career details meta box.
 */
function brc_core_render_career_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'brc_save_career_meta', 'brc_career_meta_nonce' );
	?>
	<p>
		<?php esc_html_e( 'Use the excerpt for the short summary shown on the careers archive. استخدم الملخص لوصف مختصر يظهر في صفحة الوظائف.', 'brc-core' ); ?>
	</p>
	<p>
		<?php esc_html_e( 'Closed roles are hidden from the public site and stop accepting applications. الوظائف المغلقة تختفي من الموقع وتتوقف عن استقبال الطلبات.', 'brc-core' ); ?>
	</p>
	<?php

	foreach ( brc_core_career_meta_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
		</p>
		<?php
	}

	$current_status = (string) get_post_meta( $post->ID, 'brc_career_status', true );
	$current_status = $current_status ?: 'open';
	?>
	<p>
		<label for="brc_career_status"><strong><?php esc_html_e( 'Status / الحالة', 'brc-core' ); ?></strong></label>
		<select class="widefat" id="brc_career_status" name="brc_career_status">
			<?php foreach ( brc_core_career_status_options() as $value => $label ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_status, $value ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php
}

/**
 * Persist career details.
 */
function brc_core_save_career_meta( int $post_id ): void {
	if (
		! isset( $_POST['brc_career_meta_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['brc_career_meta_nonce'] ) ), 'brc_save_career_meta' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array_keys( brc_core_career_meta_fields() ) as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}

	$status = isset( $_POST['brc_career_status'] ) ? sanitize_key( wp_unslash( $_POST['brc_career_status'] ) ) : 'open';
	$status = array_key_exists( $status, brc_core_career_status_options() ) ? $status : 'open';
	update_post_meta( $post_id, 'brc_career_status', $status );

	$apply_label = (string) get_post_meta( $post_id, 'brc_career_apply_label', true );
	if ( '' === $apply_label ) {
		update_post_meta( $post_id, 'brc_career_apply_label', __( 'Apply Now / قدم الآن', 'brc-core' ) );
	}
}
add_action( 'save_post_brc_career', 'brc_core_save_career_meta' );

/**
 * Render application details in admin.
 */
function brc_core_render_application_meta_box( WP_Post $post ): void {
	$career_id    = (int) get_post_meta( $post->ID, 'brc_application_career_id', true );
	$applicant    = (string) get_post_meta( $post->ID, 'brc_application_name', true );
	$email        = (string) get_post_meta( $post->ID, 'brc_application_email', true );
	$phone        = (string) get_post_meta( $post->ID, 'brc_application_phone', true );
	$linkedin     = (string) get_post_meta( $post->ID, 'brc_application_linkedin', true );
	$resume_id    = (int) get_post_meta( $post->ID, 'brc_application_resume_id', true );
	$resume_url   = $resume_id ? wp_get_attachment_url( $resume_id ) : '';
	$cover_letter = (string) get_post_meta( $post->ID, 'brc_application_message', true );
	?>
	<div class="brc-application-admin">
		<p><strong><?php esc_html_e( 'Career / الوظيفة:', 'brc-core' ); ?></strong>
			<?php if ( $career_id ) : ?>
				<a href="<?php echo esc_url( get_edit_post_link( $career_id ) ); ?>"><?php echo esc_html( get_the_title( $career_id ) ); ?></a>
			<?php else : ?>
				<?php esc_html_e( 'Not linked', 'brc-core' ); ?>
			<?php endif; ?>
		</p>
		<p><strong><?php esc_html_e( 'Applicant / المتقدم:', 'brc-core' ); ?></strong> <?php echo esc_html( $applicant ); ?></p>
		<p><strong><?php esc_html_e( 'Email / البريد الإلكتروني:', 'brc-core' ); ?></strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
		<p><strong><?php esc_html_e( 'Phone / الهاتف:', 'brc-core' ); ?></strong> <?php echo esc_html( $phone ); ?></p>
		<?php if ( $linkedin ) : ?>
			<p><strong><?php esc_html_e( 'LinkedIn:', 'brc-core' ); ?></strong> <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $linkedin ); ?></a></p>
		<?php endif; ?>
		<?php if ( $resume_url ) : ?>
			<p><strong><?php esc_html_e( 'Resume / السيرة الذاتية:', 'brc-core' ); ?></strong> <a href="<?php echo esc_url( $resume_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View file / عرض الملف', 'brc-core' ); ?></a></p>
		<?php endif; ?>
		<?php if ( $cover_letter ) : ?>
			<p><strong><?php esc_html_e( 'Message / الرسالة:', 'brc-core' ); ?></strong></p>
			<div class="widefat" style="padding:12px 14px; background:#fff; min-height:120px;"><?php echo nl2br( esc_html( $cover_letter ) ); ?></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Customize the career admin columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function brc_core_career_columns( array $columns ): array {
	$columns['brc_career_location']   = __( 'Location / الموقع', 'brc-core' );
	$columns['brc_career_department'] = __( 'Department / القسم', 'brc-core' );
	$columns['brc_career_status']     = __( 'Status / الحالة', 'brc-core' );

	return $columns;
}
add_filter( 'manage_brc_career_posts_columns', 'brc_core_career_columns' );

/**
 * Render the custom career admin columns.
 */
function brc_core_render_career_columns( string $column, int $post_id ): void {
	if ( 'brc_career_location' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, 'brc_career_location', true ) );
	}

	if ( 'brc_career_department' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, 'brc_career_department', true ) );
	}

	if ( 'brc_career_status' === $column ) {
		$status  = (string) get_post_meta( $post_id, 'brc_career_status', true );
		$options = brc_core_career_status_options();
		echo esc_html( $options[ $status ?: 'open' ] ?? $options['open'] );
	}
}
add_action( 'manage_brc_career_posts_custom_column', 'brc_core_render_career_columns', 10, 2 );

/**
 * Customize application admin columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function brc_core_application_columns( array $columns ): array {
	return array(
		'cb'                         => $columns['cb'] ?? '<input type="checkbox" />',
		'title'                      => __( 'Application / الطلب', 'brc-core' ),
		'brc_application_career_id'  => __( 'Career / الوظيفة', 'brc-core' ),
		'brc_application_email'      => __( 'Email / البريد الإلكتروني', 'brc-core' ),
		'brc_application_phone'      => __( 'Phone / الهاتف', 'brc-core' ),
		'date'                       => __( 'Date', 'brc-core' ),
	);
}
add_filter( 'manage_brc_application_posts_columns', 'brc_core_application_columns' );

/**
 * Render application columns.
 */
function brc_core_render_application_columns( string $column, int $post_id ): void {
	if ( 'brc_application_career_id' === $column ) {
		$career_id = (int) get_post_meta( $post_id, 'brc_application_career_id', true );
		if ( $career_id ) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( get_edit_post_link( $career_id ) ),
				esc_html( get_the_title( $career_id ) )
			);
		}
	}

	if ( 'brc_application_email' === $column ) {
		$email = (string) get_post_meta( $post_id, 'brc_application_email', true );
		if ( $email ) {
			printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $email ) );
		}
	}

	if ( 'brc_application_phone' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, 'brc_application_phone', true ) );
	}
}
add_action( 'manage_brc_application_posts_custom_column', 'brc_core_render_application_columns', 10, 2 );

/**
 * Register the careers settings page and option.
 */
function brc_core_register_career_settings(): void {
	register_setting(
		'brc_career_settings',
		'brc_career_notification_emails',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'brc_core_sanitize_notification_emails',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'brc_core_register_career_settings' );

/**
 * Sanitize a comma, semicolon, or line-separated email list.
 */
function brc_core_sanitize_notification_emails( string $emails ): string {
	$list = preg_split( '/[\r\n,;]+/', $emails ) ?: array();
	$list = array_values(
		array_filter(
			array_map(
				static fn ( string $email ): string => sanitize_email( trim( $email ) ),
				$list
			),
			static fn ( string $email ): bool => (bool) is_email( $email )
		)
	);

	return implode( ', ', array_unique( $list ) );
}

/**
 * Add the careers settings screen.
 */
function brc_core_add_career_settings_page(): void {
	add_submenu_page(
		'edit.php?post_type=brc_career',
		__( 'Career Settings / إعدادات الوظائف', 'brc-core' ),
		__( 'Settings / الإعدادات', 'brc-core' ),
		'manage_options',
		'brc-career-settings',
		'brc_core_render_career_settings_page'
	);
}
add_action( 'admin_menu', 'brc_core_add_career_settings_page' );

/**
 * Render the careers settings page.
 */
function brc_core_render_career_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$emails = (string) get_option( 'brc_career_notification_emails', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Career Settings / إعدادات الوظائف', 'brc-core' ); ?></h1>
		<form action="options.php" method="post">
			<?php settings_fields( 'brc_career_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="brc_career_notification_emails"><?php esc_html_e( 'Notification Emails / إيميلات الإشعارات', 'brc-core' ); ?></label>
					</th>
					<td>
						<textarea class="large-text" rows="5" id="brc_career_notification_emails" name="brc_career_notification_emails"><?php echo esc_textarea( $emails ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Enter one or more email addresses separated by commas, semicolons, or new lines. If left empty, notifications go to the site admin email. أدخل بريداً أو أكثر مفصولاً بفواصل أو فاصلة منقوطة أو سطر جديد. إذا تُرك الحقل فارغاً سيتم الإرسال إلى بريد مدير الموقع.', 'brc-core' ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Settings / حفظ الإعدادات', 'brc-core' ) ); ?>
		</form>
	</div>
	<?php
}

/**
 * Resolve notification recipients for new applications.
 *
 * @return array<int, string>
 */
function brc_core_career_notification_emails(): array {
	$configured = (string) get_option( 'brc_career_notification_emails', '' );
	$emails     = '' !== trim( $configured ) ? $configured : (string) get_option( 'admin_email', '' );
	$emails     = (string) apply_filters( 'brc_core_career_notification_emails', $emails );
	$list   = preg_split( '/[\s,;]+/', $emails ) ?: array();

	$list = array_values(
		array_filter(
			array_map( 'sanitize_email', $list ),
			static fn ( string $email ): bool => is_email( $email )
		)
	);

	return $list;
}

/**
 * Handle public career applications.
 */
function brc_core_handle_career_application(): void {
	$career_id = isset( $_POST['brc_career_id'] ) ? absint( wp_unslash( $_POST['brc_career_id'] ) ) : 0;

	if (
		! $career_id ||
		! isset( $_POST['brc_career_application_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['brc_career_application_nonce'] ) ),
			'brc_submit_career_application_' . $career_id
		)
	) {
		wp_die( esc_html__( 'Invalid application request.', 'brc-core' ) );
	}

	$career = get_post( $career_id );

	if ( ! $career || 'brc_career' !== $career->post_type || ! brc_core_is_career_open( $career_id ) ) {
		wp_safe_redirect( add_query_arg( 'application_status', 'closed', brc_core_get_career_apply_url( $career_id ) ) );
		exit;
	}

	$name     = isset( $_POST['brc_applicant_name'] ) ? sanitize_text_field( wp_unslash( $_POST['brc_applicant_name'] ) ) : '';
	$email    = isset( $_POST['brc_applicant_email'] ) ? sanitize_email( wp_unslash( $_POST['brc_applicant_email'] ) ) : '';
	$phone    = isset( $_POST['brc_applicant_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['brc_applicant_phone'] ) ) : '';
	$linkedin = isset( $_POST['brc_applicant_linkedin'] ) ? esc_url_raw( wp_unslash( $_POST['brc_applicant_linkedin'] ) ) : '';
	$message  = isset( $_POST['brc_applicant_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['brc_applicant_message'] ) ) : '';

	if ( '' === $name || ! is_email( $email ) || '' === $phone ) {
		wp_safe_redirect( add_query_arg( 'application_status', 'invalid', brc_core_get_career_apply_url( $career_id ) ) . '#application-form' );
		exit;
	}

	if ( empty( $_FILES['brc_applicant_resume']['name'] ) ) {
		wp_safe_redirect( add_query_arg( 'application_status', 'resume_required', brc_core_get_career_apply_url( $career_id ) ) . '#application-form' );
		exit;
	}

	$filename     = sanitize_file_name( wp_unslash( $_FILES['brc_applicant_resume']['name'] ) );
	$filetype     = wp_check_filetype_and_ext( $_FILES['brc_applicant_resume']['tmp_name'], $filename );
	$allowed_exts = array( 'pdf', 'doc', 'docx' );

	if ( empty( $filetype['ext'] ) || ! in_array( $filetype['ext'], $allowed_exts, true ) ) {
		wp_safe_redirect( add_query_arg( 'application_status', 'resume_type', brc_core_get_career_apply_url( $career_id ) ) . '#application-form' );
		exit;
	}

	$application_id = wp_insert_post(
		array(
			'post_type'   => 'brc_application',
			'post_status' => 'private',
			'post_title'  => sprintf(
				/* translators: 1: applicant name, 2: career title. */
				__( '%1$s - %2$s', 'brc-core' ),
				$name,
				get_the_title( $career_id )
			),
		),
		true
	);

	if ( is_wp_error( $application_id ) || ! $application_id ) {
		wp_safe_redirect( add_query_arg( 'application_status', 'failed', brc_core_get_career_apply_url( $career_id ) ) . '#application-form' );
		exit;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$resume_id = media_handle_upload( 'brc_applicant_resume', $application_id );

	if ( is_wp_error( $resume_id ) ) {
		wp_delete_post( $application_id, true );
		wp_safe_redirect( add_query_arg( 'application_status', 'resume_upload', brc_core_get_career_apply_url( $career_id ) ) . '#application-form' );
		exit;
	}

	update_post_meta( $application_id, 'brc_application_career_id', $career_id );
	update_post_meta( $application_id, 'brc_application_name', $name );
	update_post_meta( $application_id, 'brc_application_email', $email );
	update_post_meta( $application_id, 'brc_application_phone', $phone );
	update_post_meta( $application_id, 'brc_application_linkedin', $linkedin );
	update_post_meta( $application_id, 'brc_application_message', $message );
	update_post_meta( $application_id, 'brc_application_resume_id', (int) $resume_id );

	$recipients = brc_core_career_notification_emails();
	if ( ! empty( $recipients ) ) {
		$subject = sprintf(
			/* translators: %s: career title. */
			__( 'New career application: %s', 'brc-core' ),
			get_the_title( $career_id )
		);
		$body    = array(
			__( 'A new career application has been submitted.', 'brc-core' ),
			__( 'تم إرسال طلب توظيف جديد.', 'brc-core' ),
			'',
			sprintf( __( 'Career: %s', 'brc-core' ), get_the_title( $career_id ) ),
			sprintf( __( 'Applicant: %s', 'brc-core' ), $name ),
			sprintf( __( 'Email: %s', 'brc-core' ), $email ),
			sprintf( __( 'Phone: %s', 'brc-core' ), $phone ),
			$linkedin ? sprintf( __( 'LinkedIn: %s', 'brc-core' ), $linkedin ) : '',
			$resume_id ? sprintf( __( 'Resume: %s', 'brc-core' ), wp_get_attachment_url( $resume_id ) ) : '',
			$message ? sprintf( __( 'Message: %s', 'brc-core' ), $message ) : '',
			'',
			sprintf( __( 'Review in admin: %s', 'brc-core' ), admin_url( 'post.php?post=' . $application_id . '&action=edit' ) ),
		);

		wp_mail( $recipients, $subject, implode( "\n", array_filter( $body ) ) );
	}

	wp_safe_redirect( add_query_arg( 'application_status', 'submitted', brc_core_get_career_apply_url( $career_id ) ) . '#application-form' );
	exit;
}
add_action( 'admin_post_nopriv_brc_submit_career_application', 'brc_core_handle_career_application' );
add_action( 'admin_post_brc_submit_career_application', 'brc_core_handle_career_application' );
