<?php 
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
	
	public function save()
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
	
	public function registerTo($course_id)
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . "registrations";
		$wpdb->query($wpdb->prepare("INSERT INTO $table_name (course_id, student_id) VALUES (%d, %d)", $course_id, $this->id));
		$this->course = CourseInstance::find($course_id);
	}
	
}
