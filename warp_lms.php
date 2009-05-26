<?php
/*
Plugin Name: Warp Learning Management System
Plugin URI: http://people.warp.es/~koke/wp-plugins/warp-lms
Description: Allow management of training schedule
Version: 0.1
Author: Jorge Bernal
Author URI: http://koke.amedias.org/
*/

/*  Copyright 2008  Jorge Bernal  <jbernal@warp.es>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define(warp_lms_version, '0.1');

require_once 'i18n.php';
require_once 'register.php';
require_once 'lms_options.php';
require_once 'course_instances.php';
require_once 'students.php';

register_activation_hook(__FILE__, 'load_course_roles');
register_deactivation_hook(__FILE__, 'unload_course_roles');
register_activation_hook(__FILE__, 'load_course_options');
register_deactivation_hook(__FILE__, 'unload_course_options');
register_activation_hook(__FILE__, 'create_lms_tables');

add_filter('the_content', 'filter_lms_course_schedule');
add_filter('the_content', 'filter_lms_join_form');
add_action('admin_head', 'lms_js_calendar' );
add_action('wp_head', 'lms_load_jquery');


function lms_js_calendar()
{
	echo '
<script type="text/javascript" src="' . get_option('siteurl') . '/wp-content/plugins/warp_lms/ui.datepicker.js"></script>
<link rel="stylesheet" href="' . get_option('siteurl') . '/wp-content/plugins/warp_lms/ui.datepicker.css" type="text/css" />
';
} 

function lms_load_jquery()
{
		echo '
<script type="text/javascript" src="' . get_option('siteurl') . '/wp-includes/js/jquery/jquery.js"></script>
<script type="text/javascript" src="' . get_option('siteurl') . '/wp-content/plugins/warp_lms/livevalidation.js"></script>
';
}

function filter_lms_course_schedule($content) {
	global $post;
	$course_schedule = "<table class='course_listing'>";
	$courses = get_posts("post_parent=$post->ID&post_type=page&menu_order=99");
	foreach ($courses as $course) {
		$permalink = get_page_link($course->ID);
		$price = get_post_meta($course->ID, 'course_price', true);
		$course_instances = CourseInstance::find_by_course($course->ID);
		if ($course_instances) {
//      $course_schedule.= <<<EOC
//        <tr class="course">
//          <td><a href="$permalink">$course->post_title</a></td>
//          <td class="price">$price €</td>
//        </tr>
// EOC;
			foreach ($course_instances as $course_instance) {
        // $register_link = get_option('siteurl') . '/inscripcion/' . $course_instance->id;
				$course_schedule.= <<<EOI
				  <tr class="course_instance">
				    <td>
				      <div class="vevent">
				        <a class="url" href="$permalink">
				          <span class="summary">$course->post_title</span>
				        </a>
EOI;
        $df = get_option('lms_date_format');
        $sdate = mysql2date($df, $course_instance->start_date);
        $sdate = "<abbr class='dtstart' title='$course_instance->start_date'>$sdate</abbr>";
        $edate = mysql2date($df, $course_instance->end_date);
        $edate = "<abbr class='dtend' title='$course_instance->end_date'>$edate</abbr>";
        $date = sprintf(__( 'From %s to %s', 'warp_lms'), $sdate, $edate);
				$course_schedule.= <<<EOI
				        $date
				      </div>
				    </td>
				  </tr>
EOI;
      }
		}
	}
	$course_schedule.= "</table>";
	$content = preg_replace('/(<p>)?%lms_course_schedule%(<\/p>)?/', $course_schedule, $content);
	return $content;
}

function lms_join_form()
{
	global $wp_query;
	
	$course_id = $wp_query->get('page');
	$courses = CourseInstance::find_active();

	$content.= "
		<form action='" . $_SERVER["REQUEST_URI"] . "' method='POST' class='bpform'>
			<fieldset>
				<legend>" . __( 'Student info', 'warp_lms' ) . "</legend>
				<label for='first_name'>" . __( 'First name', 'warp_lms' ) . "</label><input type='text' name='first_name' value='' id='first_name'  />
				<label for='last_name'>" . __( 'Last name', 'warp_lms' ) . "</label><input type='text' name='last_name' value='' id='last_name'  />
				<label for='company'>" . __( 'Company', 'warp_lms' ) . "</label><input type='text' name='company' value='' id='company'  />
				<label for='email'>" . __( 'Email', 'warp_lms' ) . "</label><input type='text' name='email' value='' id='email'  />
				<label for='confirm_email'>" . __( 'Confirm email', 'warp_lms' ) . "</label><input type='text' name='confirm_email' value='' id='confirm_email'  />
				<label for='phone'>" . __( 'Phone', 'warp_lms' ) . "</label><input type='text' name='phone' value='' id='phone'  />
			</fieldset>
			
			<script type='text/javascript'>
				f_fn = new LiveValidation('first_name');
				f_fn.add( Validate.Presence );
				f_ln = new LiveValidation('last_name');
				f_ln.add( Validate.Presence );
				f_co = new LiveValidation('company');
				f_co.add( Validate.Presence );
				f_em = new LiveValidation('email');
				f_em.add( Validate.Presence );
				f_em.add( Validate.Email );
				f_ce = new LiveValidation('confirm_email');
				f_ce.add( Validate.Presence );
				f_ce.add( Validate.Confirmation, { match: 'email' })
				f_ph = new LiveValidation('phone');
				f_ph.add( Validate.Presence );
				
				var price_array = new Array();";
	foreach ($courses as $course) {
		$content.= sprintf("price_array[%d] = '%s';", $course->id, $course->price());
	}
	
	$content.= "
				
				function updatePrice(course) {
					jQuery('#course_price').html(price_array[course.options[course.selectedIndex].value]);
				}
			</script>
			
			<fieldset>
				<legend>" . __( 'Course info', 'warp_lms' ) . "</legend>
				<label for='course_id'>" . __( 'Course type', "warp_lms" ) . "</label>
				<select name='course_id' id='course_id'>";
	
	
	foreach ($courses as $course) {
		$content.= "<option value='" . $course->id . "'";
		if ($course_id == $course->id) {
			$content.= " selected='selected'";
		}
		$content.= ">" . sprintf(__( '%s starting on %s', 'warp_lms'), $course->course->post_title, mysql2date(get_option('date_format'), $course->start_date)) . "</option>";
	}
	
	$content.= "
				</select>
				
				<label>" . __( 'Course price', 'warp_lms') . "</label>
				<p><span id='course_price'>" . $course->price() ."</span> " . get_option('lms_currency') . "</p>
			</fieldset>
			
			<script type='text/javascript'>
				jQuery('#course_id').change(function(){
					jQuery('#course_price').html(price_array[jQuery('#course_id option:selected').attr('value')]);
				})
			</script>
			
			<input type='hidden' name='action' value='register_student' id='action' />
			<p class='buttons'>
		        <button type='submit' class='button positive'><img src='" . get_option('siteurl') . "/wp-content/plugins/warp_lms/tick.png' alt='' /> " . __( 'Register for this course', 'warp_lms') . "</button>
		    </p>

		</form>
	";
	
	return $content;
}

function filter_lms_join_form($text)
{
	$content = "";
	if (isset($_POST["action"]) && ($_POST["action"] == "register_student")) {
		$student = new Student($_POST["first_name"], $_POST["last_name"], $_POST["company"], $_POST["email"], $_POST["phone"]);
		if ($student->save() === FALSE) {
			$content.="<div class='error'>" . sprintf(__('There was an error registering the student. Please contact %s to register', 'warp_lms'), get_option('lms_contact')) . "</div>";
		}  else {
			$student->registerTo($_POST["course_id"]);
			// email confirmation
			$body = get_option('lms_template_signup');
			$body = preg_replace('/%student_name%/', $student->first_name, $body);
			$body = preg_replace('/%student_surname%/', $student->last_name, $body);
			$body = preg_replace('/%student_company%/', $student->company, $body);
			$body = preg_replace('/%student_email%/', $student->email, $body);
			$body = preg_replace('/%student_phone%/', $student->phone, $body);
			
			$body = preg_replace('/%course_name%/', $student->course->course->post_title, $body);
			$body = preg_replace('/%course_start%/', $student->course->start_date, $body);
			$body = preg_replace('/%course_end%/', $student->course->end_date, $body);
			$body = preg_replace('/%course_price%/', $student->course->price(), $body);
			
			$to = sprintf("To: %s %s <%s>", $student->first_name, $student->last_name, $student->email);
			wp_mail($student->email, get_option('lms_template_signup_subject'), $body, 'From: Training <' . get_option('lms_contact') . '>');
			
			if (get_option('lms_signup_notification') && get_option('lms_contact')) {
				wp_mail(get_option('lms_contact'), get_option('lms_template_signup_subject'), $body, 'From: Training <' . get_option('lms_contact') . '>');				
			}
			$content.="<div class='success'>" . __('We have received your registration info. You\'ll receive an email with more information soon', 'warp_lms') . "</div>";
		}
	} else {
		$content.= lms_join_form();
	}
	
	return preg_replace('/%lms_join_form%/', $content, $text);
}

function lms_rewrite($wp_rewrite)
{
	$lms_rules = array(
		'cursos/inscripcion' => ''
		);
}


?>
