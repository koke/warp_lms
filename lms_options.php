<?php

require_once 'i18n.php';

add_action('admin_menu', 'lms_add_options_pages');

function lms_add_options_pages()
{
	add_options_page(__('Courses', 'warp_lms'), __('Courses', 'warp_lms'), 8, 'lms_options', 'lms_course_options');
}

function lms_course_options()
{
  add_option('lms_date_format', 'F j');
	$lms_signup_period = get_option('lms_signup_period');
	$lms_courses_page_id = get_option('lms_courses_page_id');
	$lms_currency = get_option('lms_currency');
	$lms_contact = get_option('lms_contact');
	$lms_template_signup_subject = get_option('lms_template_signup_subject');
	$lms_template_signup = get_option('lms_template_signup');
	$lms_signup_notification = get_option('lms_signup_notification');
	$lms_moodle = get_option('lms_moodle');
	$lms_date_format = get_option('lms_date_format');
	
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
				<tr>
					<th>
						<?php _e( 'Date format', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="lms_date_format" value="<?php echo $lms_date_format; ?>" id="lms_date_format" /><br />
						<?php _e('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date formatting</a>. Click "Save Changes" to update sample output.') ?><br />
						<?php _e('Output:') ?> <strong><?php echo mysql2date(get_option('lms_date_format'), current_time('mysql')); ?></strong>
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
				<tr>
					<th>
						<?php _e( 'Moodle url', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="lms_moodle" value="<?php echo $lms_moodle; ?>" id="lms_moodle" size="40" /><br />
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="page_options" value="lms_courses_page_id, lms_signup_period, lms_currency, lms_contact, lms_template_signup, lms_moodle, lms_template_signup_subject, lms_signup_notification, lms_date_format" />
		<input type="hidden" name="action" value="update" />

		<p class="submit"><input type="submit" value="<?php _e( 'Update Options', 'warp_lms'); ?>"></p>
	</form>
	<?php
	echo "</div>";
}
