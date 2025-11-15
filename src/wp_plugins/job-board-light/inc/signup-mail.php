<?php
	global $wpdb;			
	$email_body = get_option( 'jobboard_signup_email');
	$signup_email_subject = get_option( 'jobboard_signup_email_subject');			
	$admin_mail = get_option('admin_email');	
	if( get_option( 'admin_email_jobboard' )==FALSE ) {
		$admin_mail = get_option('admin_email');						 
		}else{
		$admin_mail = get_option('admin_email_jobboard');								
	}						
	$wp_title = get_bloginfo();
	$user_info = get_userdata( $user_id);	
	// Email for Admin		
	$email_body = str_replace("[user_name]", $user_info->display_name, $email_body);
	$email_body = str_replace("[iv_member_user_name]", $user_info->display_name, $email_body);
	$email_body = str_replace("[iv_member_password]", $userdata['user_pass'], $email_body);	
	$cilent_email_address =$user_info->user_email; 
	$auto_subject=  $signup_email_subject; 
	$headers = array("From: " . $wp_title . " <" . $admin_mail . ">", "Content-Type: text/html");
	$h = implode("\r\n", $headers) . "\r\n";
	wp_mail($cilent_email_address, $auto_subject, $email_body, $h);
	
				
