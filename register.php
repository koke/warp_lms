<?php


function load_course_roles()
{
	global $wp_roles;

	if (!$wp_roles->is_role('course_manager')) {
		_c('Course Manager|User role');
		$author = get_role('author');
		add_role('course_manager', 'Course Manager|User role', $author->capabilities);
		$role = get_role('course_manager');
		$role->add_cap('manage_courses');

		$role = get_role('administrator');
		$role->add_cap('manage_courses');
	}
}

function unload_course_roles()
{
	global $wp_roles;
 
	if ($wp_roles->is_role('course_manager')) {
		$role = get_role('administrator');
	  	$role->remove_cap('manage_courses');

	    $wp_roles->remove_role('course_manager');
	  }
}

function load_course_options()
{
	add_option('lms_signup_period', 18);
	add_option('lms_courses_page_id');
	add_option('lms_currency', '€');
	add_option('lms_contact', '');
	add_option('lms_template_signup_subject', __( 'Your training info', 'warp_lms' ));
	add_option('lms_template_signup', '');
	add_option('lms_signup_notification', true);
}

function unload_course_options()
{
	// delete_option('lms_signup_period');
	// delete_option('lms_courses_page_id');
}

function create_lms_tables()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix . "lms_instances";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
					start_date date NULL,
					end_date date NULL,
					location varchar(255) NULL,
					price varchar(10) NOT NULL,
					course_id bigint(20) UNSIGNED NOT NULL,
					code VARCHAR(10) NOT NULL,
					online TINYINT(1) NOT NULL DEFAULT 0,
					currency CHAR(3) not null default 'EUR',
					PRIMARY KEY(id),
					UNIQUE KEY(code),
					FOREIGN KEY (course_id) REFERENCES " . $wpdb->prefix . "posts (ID)
				)";
		dbDelta($sql);
	}
	$table_name = $wpdb->prefix . "lms_students";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
					id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
					first_name varchar(50) NOT NULL,
					last_name varchar(100) NOT NULL,
					company varchar(100) NOT NULL,
					email varchar(255) NOT NULL,
					phone varchar(35) NOT NULL,
					PRIMARY KEY (id)
			)";
		dbDelta($sql);
	}	

	$table_name = $wpdb->prefix . "lms_registrations";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE $table_name (
				course_id mediumint(9) UNSIGNED NOT NULL,
				student_id mediumint(9) UNSIGNED NOT NULL,
				PRIMARY KEY (course_id, student_id),
				FOREIGN KEY (course_id) REFERENCES " . $wpdb->prefix . "lms_instances (id),
				FOREIGN KEY (student_id) REFERENCES " . $wpdb->prefix . "lms_students (id)
			)";
		dbDelta($sql);
	}	
	
	add_option('warp_lms_version', warp_lms_version);
	update_option('warp_lms_version', warp_lms_version);
}
