<?php
	global $current_user;	
	$email_body = get_option( 'jobboard_apply_email');
	$contact_email_subject = get_option( 'jobboard_apply_email_subject');	
	$admin_mail = get_option('admin_email');
	$wp_title = get_bloginfo();
	$bcc_message='';
	if( get_option('epjbjobboard_bcc_message' ) ) {
		$bcc_message= get_option('epjbjobboard_bcc_message'); 
	}
	if($form_data['dir_id']!=''){
		$job_id= $form_data['dir_id'];
		$job_title=get_the_title($job_id);		
		$dir_detail= get_post($job_id); 
		$dir_title= '<a href="'.get_permalink($job_id).'">'.$dir_detail->post_title.'</a>';
		$user_id=$dir_detail->post_author;
		$user_info = get_userdata( $user_id);
		$listing_contact_source=get_post_meta($job_id,'listing_contact_source',true);
		if($listing_contact_source==''){$listing_contact_source='user_info';}
		if($listing_contact_source=='new_value'){
			$client_email_address = get_post_meta($job_id, 'contact-email',true);
			}else{
			$client_email_address =$user_info->user_email;
		}
	}
	$contact_email_subject = $contact_email_subject .' '.$job_title; 
	$dir_id=$newpost_id;	
	$dir_detail= get_post($dir_id); 
	$dir_title= '<a href="'.get_edit_post_link($dir_id).'">'.$dir_detail->post_title.'</a>';		
	// Email for Client			
	$name= get_user_meta($current_user->ID,'full_name',true);		
	$email_body = str_replace("[iv_member_sender_name]", $name, $email_body);
	$email= $current_user->user_email;	
	$email_body = str_replace("[iv_member_sender_email]", $email, $email_body);
	$iv_member_sender_phone=get_user_meta($current_user->ID,'phone',true);
	$email_body = str_replace("[iv_member_sender_phone]", $iv_member_sender_phone, $email_body);	
	$email_body = str_replace("New Application for", $contact_email_subject, $email_body);
	$cv_link=  get_site_url().'?&jobboardpdfcv='.$current_user->ID;
	$email_body = str_replace("[iv_member_sender_cv_link]", $cv_link, $email_body);
	$message =$form_data['cover-content2']; 	  
	$email_body = str_replace("[iv_member_message]", $message, $email_body);	
	$headers = array("From: " . $wp_title . " <" . $admin_mail . ">", "Reply-To: ".$admin_mail  ,"Content-Type: text/html");
	$h = implode("\r\n", $headers) . "\r\n";
	wp_mail($client_email_address, $contact_email_subject, $email_body, $h,$cv_link);
	if($bcc_message=='yes'){
		$h = implode("\r\n", $headers) . "\r\n";
		wp_mail($admin_mail, $contact_email_subject, $email_body, $h,$cv_link);	
	}		