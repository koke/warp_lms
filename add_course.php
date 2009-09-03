<?php

	$lms_courses_page_id = get_option('lms_courses_page_id');

?>

	<form method="post" action="">
		<?php wp_nonce_field('manage_courses'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<?php _e( 'Course', 'warp_lms'); ?>
					</th>
					<td>
						<?php wp_dropdown_pages(array('name' => 'course_id', 'child_of' => $lms_courses_page_id, 'depth' => 1)); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Course type', 'warp_lms'); ?>
					</th>
					<td>
						<select name="online" id="online">
							<option value="0">In-person</option>
							<option value="1">Online</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Code', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="code" id="code" size="10" maxlength="10" />
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Location', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="location" id="location" size="20" />
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Price', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="price" id="price" size="10" maxlength="10" />
						<select name="currency" id="currency">
							<option id="EUR">Euro</option>
							<option id="USD">US Dollar</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>
						<?php _e( 'Start date', 'warp_lms'); ?>
					</th>
					<td>
						<input type="text" name="start_date" id="start_date" size="10" />
						<script type="text/javascript" charset="utf-8">
							jQuery("#start_date").datepicker({dateFormat: 'yy-mm-dd', beforeShowDay: jQuery.datepicker.noWeekends, defaultDate: '3w', firstDay: 1});
						</script>
					</td>
				</tr>
			</tbody>
		</table>

		<input type="hidden" name="action" value="add" />

		<p class="submit"><input type="submit" value="<?php _e( 'Add Course', 'warp_lms'); ?>"></p>
	</form>
