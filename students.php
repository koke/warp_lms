<?php 

function students_page()
{
	global $wpdb;
	$table_name = $wpdb->prefix . "lms_students";
	
	echo '<div class="wrap">';
	
	if (isset($_GET["course_id"])) {
		$students = Student::find_all_by_course($_GET["course_id"]);
	} else {
		$students = Student::find_all();
	}
	if (!empty($students)) {
		?>
		<form action="" method="post" id="students-filter">
			<div class="tablenav">

			<div class="alignleft">
			<input type="submit" value="<?php _e('Delete'); ?>" name="deleteit" class="button-secondary delete" />
			<?php wp_nonce_field('bulk-students'); ?>
			</div>

			<br class="clear" />
			</div>

			<br class="clear" />

		<table class="widefat">
		  <thead>
		  <tr>
			<th class="check-column" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('students-filter'));"/></th>
			<th scope="col"><?php _e( 'First Name', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Last Name', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Company', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Email', 'warp_lms' ); ?></th>
			<th scope="col"><?php _e( 'Phone', 'warp_lms' ); ?></th>
		  </tr>
		  </thead>
		  <tbody>
		  <?php
		  	foreach ($students as $student) {
				?>
				<tr>
					<th class="check-column" scope="row"><input type="checkbox" value="<?php echo $student->id; ?>" name="delete[]" /></th>
					<td><?php echo $student->first_name; ?></td>
					<td><?php echo $student->last_name; ?></td>
					<td><?php echo $student->company; ?></td>
					<td><?php echo "<a href='mailto:$student->email'>$student->email</a>"; ?></td>
					<td><?php echo "<a href='tel://$student->phone'>$student->phone</a>"; ?></td>
				</tr>
				<?php
		  	}
		  ?>
		  </tbody>
		</table>
		</form>
		<?php		
	}
	
	echo '</div>';
}

/**
* Student
*/
class Student
{
	var $first_name;
	var $last_name;
	var $company;
	var $email;
	var $phone;
	var $id;
	var $course;
	
	function __construct($first_name, $last_name, $company, $email, $phone)
	{
		global $wpdb;
		
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->company = $company;
		$this->email = $email;
		$this->phone = $phone;
		
		$this->table_name = $wpdb->prefix . "lms_students";
	}
	
	function save()
	{
		global $wpdb;
		if (isset($this->id)) {
			$sql = $wpdb->prepare("UPDATE $this->table_name SET
				first_name = '%s',
				last_name = '%s',
				company = '%s',
				email = '%s',
				phone = '%s',
			", $this->first_name, $this->last_name, $this->company, $this->email, $this->phone);
		} else {
			$sql = $wpdb->prepare("INSERT INTO $this->table_name 
				(first_name, last_name, company, email, phone)
				VALUES ('%s','%s','%s','%s','%s')
			", $this->first_name, $this->last_name, $this->company, $this->email, $this->phone);
		}
		
		$result = $wpdb->query($sql);
		if (($result !== FALSE) && (!isset($this->id))) {
			$this->id = $wpdb->insert_id;
		}
		return $result;
	}
	
	function registerTo($course_id)
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . "lms_registrations";
		$wpdb->query($wpdb->prepare("INSERT INTO $table_name (course_id, student_id) VALUES (%d, %d)", $course_id, $this->id));
		$this->course = CourseInstance::find($course_id);
	}

	function find_all()
	{
		global $wpdb;
		$results = $wpdb->get_results("
			SELECT s.*, r.course_id 
			FROM " . $wpdb->prefix . "lms_students s
			LEFT JOIN " . $wpdb->prefix . "lms_registrations r ON s.id = r.student_id
			LEFT JOIN " . $wpdb->prefix . "lms_instances i ON i.id = r.course_id
		");
		
		$students = array();
		foreach ($results as $student) {
			$s = new Student($student->first_name, $student->last_name, $student->company, $student->email, $student->phone);
			$s->course = CourseInstance::find($student->course_id);
			$s->id = $student->id;
			$students[] = $s;
		}
		
		return $students;
	}

	function find_all_by_course($course_id)
	{
		global $wpdb;
		$results = $wpdb->get_results($wpdb->prepare("
			SELECT s.*, r.course_id 
			FROM " . $wpdb->prefix . "lms_students s
			LEFT JOIN " . $wpdb->prefix . "lms_registrations r ON s.id = r.student_id
			LEFT JOIN " . $wpdb->prefix . "lms_instances i ON i.id = r.course_id
			WHERE i.id = %d
		", $course_id));
		
		$students = array();
		foreach ($results as $student) {
			$s = new Student($student->first_name, $student->last_name, $student->company, $student->email, $student->phone);
			$s->course = CourseInstance::find($student->course_id);
			$s->id = $student->id;
			$students[] = $s;
		}
		
		return $students;
	}
	
}
