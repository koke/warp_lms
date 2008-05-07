<?php

require_once 'i18n.php';

add_action('admin_menu', 'lms_add_options_pages');

function lms_add_options_pages()
{
	add_options_page(__('Courses', 'warp_lms'), __('Courses', 'warp_lms'), 8, 'lms_options', 'lms_course_options');
}

function lms_course_options()
{
	$lms_signup_period = get_option('lms_signup_period');
	$lms_courses_page_id = get_option('lms_courses_page_id');
	$lms_currency = get_option('lms_currency');
	$lms_contact = get_option('lms_contact');
	$lms_template_signup_subject = get_option('lms_template_signup_subject');
	$lms_template_signup = get_option('lms_template_signup');
	$lms_signup_notification = get_option('lms_signup_notification');
	
	echo "<div class='wrap'>";
	echo "<h2>" . __( 'Course Options', 'warp_lms') . "</h2>";
	?>
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<h3><?php _e( 'General options', 'warp_lms'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<?php _e( 'Signup available until', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="lms_signup_period" value="<?php echo $lms_signup_period; ?>" id="lms_signup_period" size="3" /><br />
						<?php _e( 'Number of days before course date', 'warp_lms'); ?>					
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Currency symbol', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="lms_currency" value="<?php echo $lms_currency; ?>" id="lms_currency" size="1" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Courses page', 'warp_lms'); ?>
					</th>
					<td>
						<?php wp_dropdown_pages(array('name' => 'lms_courses_page_id', 'selected' => $lms_courses_page_id)); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e( 'Notifications', 'warp_lms') ?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<?php _e( 'Contact email', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="lms_contact" value="<?php echo $lms_contact; ?>" id="lms_contact" size="40" /><br />
						<?php _e( 'Email address for training contact', 'warp_lms'); ?>					
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Signup notification subject', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="lms_template_signup_subject" value="<?php echo $lms_template_signup_subject; ?>" id="lms_template_signup_subject" size="40" /><br />
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Signup notification template', 'warp_lms'); ?>
					</th>
					<td>
						<?php _e( 'This is the mail which will be sent to the student upon registration', 'warp_lms'); ?><br />					
						<textarea name="lms_template_signup" id="lms_template_signup" rows="10", cols="60"><?php echo $lms_template_signup; ?></textarea>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Notify me', 'warp_lms'); ?>
					</th>
					<td>
						<input type="checkbox" name="lms_signup_notification" value="1" <?php if($lms_signup_notification) { echo 'checked="checked"'; } ?> id="lms_signup_notification" />
						<label for="lms_signup_notification"><?php _e( 'Notify the training team about new students', 'warp_lms' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="page_options" value="lms_courses_page_id, lms_signup_period, lms_currency, lms_contact, lms_template_signup, lms_template_signup_subject, lms_signup_notification" />
		<input type="hidden" name="action" value="update" />

		<p class="submit"><input type="submit" value="<?php _e( 'Update Options', 'warp_lms'); ?>"></p>
	</form>
	<?php
	echo "</div>";
}
