<?php
	$directory_url=get_option('epjbjobboard_url');					
	if($directory_url==""){$directory_url='job';}
	global $wpdb, $post;
	$main_class = new wp_jobboard;
	//Strat  Subscription remainder email ********************************
	$sql="SELECT * FROM $wpdb->users ";
	$membership_users = $wpdb->get_results($sql);
	$total_package=count($membership_users);
	if(sizeof($membership_users)>0){
		$i=0;
		foreach ( $membership_users as $row )
		{	
			$user_id= $row->ID;
			$reminder_day = get_option( 'jobboard_reminder_day');
			$exp_date= get_user_meta($user_id, 'jobboard_exprie_date', true);
			$date2 = date("Y-m-d");
			$date1 = $exp_date;
			$diff = abs(strtotime($date2) - strtotime($date1));
			$days = floor($diff / (60*60*24));
			if( $reminder_day >= $days ){
				$exprie_send_email_date= get_user_meta($user_id, 'exprie_send_email_date', true);
				if(strtotime($exprie_send_email_date) != strtotime($exp_date) || $exprie_send_email_date=='' ){
					// Start Email Action
					$email_body = get_option( 'jobboard_reminder_email');
					$signup_email_subject = get_option( 'jobboard_reminder_email_subject');			
					$admin_mail = get_option('admin_email');	
					if( get_option( 'admin_email_jobboard' )==FALSE ) {
						$admin_mail = get_option('admin_email');						 
						}else{
						$admin_mail = get_option('admin_email_jobboard');								
					}						
					$wp_title = get_bloginfo();
					$user_info = get_userdata( $user_id);											
					$email_body = str_replace("[expire_date]", $exp_date, $email_body);	
					$cilent_email_address =$user_info->user_email;			
					$auto_subject=  $signup_email_subject; 
					$headers = array("From: " . $wp_title . " <" . $admin_mail . ">", "Content-Type: text/html");
					$h = implode("\r\n", $headers) . "\r\n";
					wp_mail($cilent_email_address, $auto_subject, $email_body, $h);
					// End Email Action
					update_user_meta($user_id, 'exprie_send_email_date', $exp_date);
				}	
			}	
		}
	}	
	//End Subscription remainder email *************************
	// Start Hide Directory******************
	$sql=$wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type ='%s'  and post_status='publish' ", $directory_url);
	$all_post = $wpdb->get_results($sql);
	$total_post=count($all_post);									
	if($total_post>0){
		$i=0;
		foreach ( $all_post as $row )								
		{			
			$dir_id=$row->ID;
			$post_author_id=$row->post_author;	
			$main_class->check_listing_expire_date($dir_id, $post_author_id,$directory_url);					
		}
	}										
// End  Hide Directory******************

// Start Notification***************	
	
	$email_body_main = get_option( 'jobboard_notification_email');
	$contact_email_subject =  get_option( 'jobboard_notification_email_subject');
	$admin_mail = get_option('admin_email');
	$wp_title = get_bloginfo();
			
	$args_today = array(
	'post_type' => $directory_url, // enter your custom post type
	'post_status' => 'publish',
	'posts_per_page'=> '-1',
	'date_query' => array(
        array(
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d'),
        ),
	 )
	);
		
	$today_posts = new WP_Query( $args_today );
	if ( $today_posts->have_posts() ) :
	while ( $today_posts->have_posts() ) : $today_posts->the_post();
		
			$dir_id=get_the_ID();
			$dir_detail= get_post($dir_id); 
			$job_name= $dir_detail->post_title; 
			$currentCategory=wp_get_object_terms( $dir_id, $directory_url.'-category');
			$deadline='';
			if(get_post_meta($dir_id,'deadline', true)!=''){
				$deadline =gmdate('M d, Y', strtotime(get_post_meta($dir_id,'deadline', true)));
			}
			$job_link= '<a href="'.get_the_permalink($dir_id).'">'.$dir_detail->post_title.'</a>';	
			$args_user = array();
			$args_user['number']='999999999';
			$args_user['orderby']='display_name';
			$args_user['order']='ASC'; 
			
			$user_query = new WP_User_Query( $args_user );
			// User Loop		
			
			if ( ! empty( $user_query->results ) ) {
				foreach ( $user_query->results as $user ) {
					$job_notifications_all='';
					$job_notifications_all= get_user_meta($user->ID ,'job_notifications',true);
					$will_send_email='no';	
					if(is_array($job_notifications_all)){
						foreach($currentCategory as $c){			
							$c->slug;
							if(in_array($c->slug, $job_notifications_all)){
								$will_send_email='yes';
							}
						}
					}
					
					if($will_send_email=='yes'){  
						$email_body	=$email_body_main;		
						$full_name =get_user_meta($user->ID,'full_name',true);
						$cilent_email_address =$user->user_email;
						$email_body = str_replace("[user_name]", $full_name, $email_body);
						$email_body = str_replace("[iv_member_job_name]",$job_name, $email_body);
						$email_body = str_replace("[iv_member_job_deadline]", $deadline, $email_body);
						$email_body = str_replace("[iv_member_job_url]",$job_link, $email_body); 			
						$headers = array("From: " . $wp_title . " <" . $admin_mail . ">", "Reply-To: ".$admin_mail  ,"Content-Type: text/html");
						$h = implode("\r\n", $headers) . "\r\n";
						wp_mail($cilent_email_address, $contact_email_subject, $email_body, $h);
						
					}
				}
			}	
		
		endwhile;
	endif;
	
// End Notification