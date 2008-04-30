<?php

add_action('admin_menu', 'lms_add_pages');

function lms_add_pages()
{
	add_menu_page(__( 'Courses', 'warp_lms'), __( 'Courses', 'warp_lms'), 'manage_courses', __FILE__, 'courses_page');
	// add_submenu_page(__FILE__, __( 'Dashboard', 'warp_lms'), __( 'Dashboard', 'warp_lms'), 'manage_courses', __FILE__, 'courses_page');
	add_submenu_page(__FILE__, __( 'Students', 'warp_lms'), __( 'Students', 'warp_lms'), 'manage_courses', 'warp_lms/students.php', 'students_page');
}

function courses_page()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "lms_instances";
	
	echo '<div class="wrap">';
	
	if (isset($_POST['action'])) {
		$course_id = (int) $_POST["course_id"];
		$duration = get_post_meta($course_id, 'course_duration', true);
		if (empty($duration)) {
			echo "<div class='error'><p><strong>". __( 'Error:', 'warp_lms') . "</strong> " . __( 'There is no duration defined for that course', 'warp_lms' ) . "</p></div>";
		} else {
			$wpdb->query($wpdb->prepare("INSERT INTO $table_name (start_date, end_date, course_id) VALUES ('%s','%s' + INTERVAL %d DAY, %d)", $_POST['start_date'], $_POST['start_date'], $duration-1, $_POST['course_id']));
			echo "<div class='updated'><p>" . __( 'Course added successfully', 'warp_lms' ) . "</p></div>";
		}
	}
	list_courses();
	echo "<h2>" . __( 'Add course', 'warp_lms') . "</h2>";
	
	require 'add_course.php';
	echo '</div>';
}

function list_courses()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "lms_instances";	

	if ( isset($_POST['deleteit']) && isset($_POST['delete']) ) {
		check_admin_referer('bulk-courses');
		foreach( (array) $_POST['delete'] as $course_id_del ) {
			if ( !current_user_can('manage_courses', $course_id_del) )
				wp_die( __('You are not allowed to delete this course.') );
			
			$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %d", $course_id_del));
		}
		echo "<div class='updated'><p>" . __( 'Course removed successfully', 'warp_lms' ) . "</p></div>";
	}

	$courses = CourseInstance::find_active();
	if ($courses) {
		?>
		<form action="" method="post" id="courses-filter">
			<div class="tablenav">

			<div class="alignleft">
			<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
			<?php wp_nonce_field('bulk-courses'); ?>
			</div>

			<br class="clear" />
			</div>

			<br class="clear" />

		<table class="widefat">
		  <thead>
		  <tr>
			<th class="check-column" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('courses-filter'));"/></th>
			<th scope="col"><?php _e( 'Starts on', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Ends on', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Course Name', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Students', 'warp_lms' ); ?></th>
		  </tr>
		  </thead>
		  <tbody>
		  <?php
		  	foreach ($courses as $course) {
				?>
				<tr>
					<th class="check-column" scope="row"><input type="checkbox" value="<?php echo $course->id; ?>" name="delete[]" /></th>
					<td><?php echo mysql2date(__('Y/m/d'), $course->start_date); ?></td>
					<td><?php echo mysql2date(__('Y/m/d'), $course->end_date); ?></td>
					<td><?php echo $course->course->post_title; ?></td>
					<td><?php if (count($course->students) > 0) {
						echo "<a href='?page=warp_lms/students.php&course_id=$course->id'>" . count($course->students) . "</a>";
					} else {
						echo count($course->students);
					}
					?></td>
				</tr>
				<?php
		  	}
		  ?>
		  </tbody>
		</table>
		</form>
		<?php		
	}
}

/**
* CourseInstance
*/
class CourseInstance
{
	var $id;
	var $start_date;
	var $end_date;
	var $course;
	
	function __construct($id, $start_date, $end_date, $course_id)
	{
		global $wpdb;
		$wpdb->query("SELECT 'New CourseInstance...'");
		$this->id = $id;
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->course = get_post($course_id);
		$wpdb->query("SELECT 'Getting students...'");
		$this->students = $this->fetch_students();
	}
	
	public function price()
	{
		return get_post_meta($this->course->ID, 'course_price', true);
	}
	
	public static function find_by_course($course_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "lms_instances";	
		
		$result = array();
		$courses = $wpdb->get_results($wpdb->prepare("SELECT id, start_date, end_date, course_id FROM $table_name WHERE course_id = %d AND start_date > CURRENT_DATE ORDER BY start_date", $course_id));
		foreach ($courses as $course) {
			$result[] = new CourseInstance($course->id, $course->start_date, $course->end_date, $course->course_id);
		}
		
		return $result;
	}
	
	public static function find($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "lms_instances";	
		
		$result = array();
		$course = $wpdb->get_row($wpdb->prepare("SELECT id, start_date, end_date, course_id FROM $table_name WHERE id = %d", $id));
		$result = new CourseInstance($course->id, $course->start_date, $course->end_date, $course->course_id);
		
		return $result;		
	}
	
	public static function find_active()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "lms_instances";	
		
		$result = array();
		$courses = $wpdb->get_results($wpdb->prepare("SELECT id, start_date, end_date, course_id FROM $table_name WHERE start_date > CURRENT_DATE ORDER BY start_date"));
		foreach ($courses as $course) {
			$result[] = new CourseInstance($course->id, $course->start_date, $course->end_date, $course->course_id);
		}
		
		return $result;
	}
	
	private function fetch_students()
	{
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT s.* 
			FROM " . $wpdb->prefix . "lms_registrations r
			LEFT JOIN " . $wpdb->prefix . "lms_students s ON s.id = r.student_id
			WHERE r.course_id = $this->id
		");
		
		$students = array();
		foreach ($results as $student) {
			$students[] = new Student($student->first_name, $student->last_name, $student->company, $student->email, $student->phone);
		}
		
		return $students;
	}
}
