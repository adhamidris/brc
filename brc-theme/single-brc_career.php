<?php
/**
 * Single career and apply endpoint template.
 *
 * @package BRC
 */

get_header();

while ( have_posts() ) :
	the_post();

	$career_id              = get_the_ID();
	$career_location        = (string) get_post_meta( $career_id, 'brc_career_location', true );
	$career_department      = (string) get_post_meta( $career_id, 'brc_career_department', true );
	$career_employment_type = (string) get_post_meta( $career_id, 'brc_career_employment_type', true );
	$apply_label            = (string) get_post_meta( $career_id, 'brc_career_apply_label', true );
	$apply_label            = $apply_label ?: __( 'Apply Now / قدم الآن', 'brc' );
	$is_apply               = function_exists( 'brc_core_is_career_apply_request' ) && brc_core_is_career_apply_request();
	$apply_url              = function_exists( 'brc_core_get_career_apply_url' ) ? brc_core_get_career_apply_url( $career_id ) : trailingslashit( get_permalink( $career_id ) ) . 'apply/';
	$archive_url            = get_post_type_archive_link( 'brc_career' );
	$status                 = isset( $_GET['application_status'] ) ? sanitize_key( wp_unslash( $_GET['application_status'] ) ) : '';
	$notices                = array(
		'submitted'       => array( 'success', __( 'Your application has been submitted successfully. تم إرسال طلب التقديم بنجاح.', 'brc' ) ),
		'invalid'         => array( 'error', __( 'Please complete the required fields correctly before submitting. يرجى استكمال البيانات المطلوبة بشكل صحيح قبل الإرسال.', 'brc' ) ),
		'resume_required' => array( 'error', __( 'Please attach your CV or resume before submitting. يرجى إرفاق السيرة الذاتية قبل إرسال الطلب.', 'brc' ) ),
		'resume_type'     => array( 'error', __( 'Resume upload must be a PDF, DOC, or DOCX file. يجب أن يكون ملف السيرة الذاتية بصيغة PDF أو DOC أو DOCX.', 'brc' ) ),
		'resume_upload'   => array( 'error', __( 'We could not upload the resume file. Please try again. تعذر رفع ملف السيرة الذاتية، يرجى المحاولة مرة أخرى.', 'brc' ) ),
		'failed'          => array( 'error', __( 'We could not save the application right now. Please try again. تعذر حفظ الطلب حالياً، يرجى المحاولة مرة أخرى.', 'brc' ) ),
		'closed'          => array( 'error', __( 'This role is no longer accepting applications. هذه الوظيفة لم تعد تستقبل طلبات جديدة.', 'brc' ) ),
	);
	?>
	<article <?php post_class( 'entry entry--career' ); ?>>
		<header class="brc-detail-hero brc-detail-hero--career">
			<div class="brc-detail-hero__content">
				<p class="brc-kicker">
					<?php
					echo esc_html(
						$career_department
							? $career_department
							: __( 'Careers / الوظائف', 'brc' )
					);
					?>
				</p>
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<p><?php the_excerpt(); ?></p>
				<?php endif; ?>
				<div class="brc-detail-hero__actions">
					<?php if ( ! $is_apply ) : ?>
						<a class="brc-button" href="<?php echo esc_url( $apply_url ); ?>"><?php echo esc_html( $apply_label ); ?></a>
					<?php endif; ?>
					<a class="brc-button brc-button--ghost" href="<?php echo esc_url( $archive_url ); ?>">
						<?php echo esc_html( $is_apply ? __( 'Back to Careers / العودة للوظائف', 'brc' ) : __( 'All Careers / جميع الوظائف', 'brc' ) ); ?>
					</a>
				</div>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="brc-detail-hero__media">
					<?php the_post_thumbnail( 'brc-hero' ); ?>
				</figure>
			<?php endif; ?>
		</header>

		<div class="brc-detail-layout brc-career-layout<?php echo $is_apply ? ' brc-career-layout--apply' : ''; ?>">
			<aside class="brc-detail-layout__aside">
				<?php get_template_part( 'template-parts/career-meta' ); ?>
			</aside>
			<div class="entry-content brc-detail-layout__content brc-career-content">
				<?php the_content(); ?>

				<?php if ( $is_apply ) : ?>
					<section id="application-form" class="brc-career-application">
						<div class="brc-career-application__intro">
							<p class="brc-kicker"><?php esc_html_e( 'Application Form / نموذج التقديم', 'brc' ); ?></p>
							<h2><?php esc_html_e( 'Apply for this role / التقديم على هذه الوظيفة', 'brc' ); ?></h2>
							<p><?php esc_html_e( 'Share your basic details, CV, and a short note so the hiring team can review your application. شارك بياناتك الأساسية والسيرة الذاتية وملاحظة قصيرة ليتمكن فريق التوظيف من مراجعة طلبك.', 'brc' ); ?></p>
						</div>

						<?php if ( isset( $notices[ $status ] ) ) : ?>
							<div class="brc-form-notice brc-form-notice--<?php echo esc_attr( $notices[ $status ][0] ); ?>">
								<p><?php echo esc_html( $notices[ $status ][1] ); ?></p>
							</div>
						<?php endif; ?>

						<form class="brc-lead-form brc-career-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
							<input type="hidden" name="action" value="brc_submit_career_application">
							<input type="hidden" name="brc_career_id" value="<?php echo esc_attr( $career_id ); ?>">
							<?php wp_nonce_field( 'brc_submit_career_application_' . $career_id, 'brc_career_application_nonce' ); ?>

							<p>
								<label>
									<?php esc_html_e( 'Full Name / الاسم الكامل', 'brc' ); ?>
									<input type="text" name="brc_applicant_name" required>
								</label>
							</p>
							<p>
								<label>
									<?php esc_html_e( 'Email / البريد الإلكتروني', 'brc' ); ?>
									<input type="email" name="brc_applicant_email" required>
								</label>
							</p>
							<p>
								<label>
									<?php esc_html_e( 'Phone / الهاتف', 'brc' ); ?>
									<input type="tel" name="brc_applicant_phone" required>
								</label>
							</p>
							<p>
								<label>
									<?php esc_html_e( 'LinkedIn URL / رابط لينكدإن', 'brc' ); ?>
									<input type="url" name="brc_applicant_linkedin">
								</label>
							</p>
							<p class="brc-career-form__file">
								<label>
									<?php esc_html_e( 'CV / Resume (PDF, DOC, DOCX) / السيرة الذاتية', 'brc' ); ?>
									<input type="file" name="brc_applicant_resume" accept=".pdf,.doc,.docx" required>
								</label>
							</p>
							<p class="brc-career-form__message">
								<label>
									<?php esc_html_e( 'Message / رسالة', 'brc' ); ?>
									<textarea name="brc_applicant_message" rows="6"></textarea>
								</label>
							</p>
							<button type="submit"><?php echo esc_html( $apply_label ); ?></button>
						</form>
					</section>
				<?php else : ?>
					<div class="brc-career-actions">
						<a class="brc-button" href="<?php echo esc_url( $apply_url ); ?>"><?php echo esc_html( $apply_label ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
