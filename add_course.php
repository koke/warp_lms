<?php

	$lms_courses_page_id = get_option('lms_courses_page_id');

?>

	<form method="post" action="">
		<?php wp_nonce_field('manage_courses'); ?>
		<table class="form-table">
			<tbody>
				<tr>
					<th>
						<?php _e( 'Course type', 'warp_lms'); ?>
					</th>
					<td>
						<?php wp_dropdown_pages(array('name' => 'course_id', 'child_of' => $lms_courses_page_id, 'depth' => 1)); ?>
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