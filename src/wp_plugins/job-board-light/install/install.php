<?php $blog_title = get_bloginfo(); 
	global $wpdb;
	// Create Basic Role
	global $wp_roles;												
	$role_name_new= 'basic';
	$wp_roles->remove_role( $role_name_new );						 
	$role_display_name = 'Basic';						
	$wp_roles->add_role($role_name_new, $role_display_name, array(
    'read' => true, // True allows that capability, False specifically removes it.
    'upload_files' => true //last in array needs no comma!
	));
	require_once ('install-signup-email.php');
	require_once ('install-order-email.php');
	require_once ('install-reminder-email.php'); 
	update_option('jobboard_payment_gateway', 'paypal-express' ); 
	update_option('jobboard_payment_terms', 'yes' ); 
	update_option('jobboard_price-table', 'style-1' ); 
	update_option('epjbjobboard_api_currency', 'USD' );
	update_option('jobboard_payment_terms_text', ' I have read & accept the  Terms & Conditions' ); 
	update_option('epjbjobboard_hide_admin_bar', 'yes' ); 
			
	// **** Create Account Form For Registration Page******
	$page_title='Registration';
	$page_name='registration';
	$page_content='[jobboard_form_wizard]';
	$post_iv = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $post_iv );
	update_option('epjbjobboard_registration', $newpost_id); 	
	/// **** Create Page for User Profile******
	$page_title='My Account';
	$page_name='my-account';
	$page_content='[jobboard_profile_template]';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	update_option('epjbjobboard_profile_page', $newpost_id); 	
	/// **** Create Page for User public Profile****** 
	$page_title='Candidate Profile';
	$page_name='candidate-public';
	$page_content='[jobboard_candidate_profile_public]';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	update_option('epjbjobboard_candidate_public_page', $newpost_id);
	/// **** Create Page for Employer public Profile****** 
	$page_title='Employer Profile';
	$page_name='employer-public';
	$page_content='[jobboard_employer_profile_public]';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	update_option('epjbjobboard_employer_public_page', $newpost_id);
	// Login Page *******************
	$page_title='Login';
	$page_name='login';
	$page_content='[jobboard_login]';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	$reg_login_page= get_permalink( $newpost_id);
	update_option('epjbjobboard_login_page', $newpost_id);
	/// **** Create Page for Thank you ****** 
	$reg_login_page= get_permalink(get_option('epjbjobboard_login_page'));
	$page_title='Thank You';
	$page_name='thank-you';
	$page_content='<h3>Thank You For Your Signup & Payment. Please login <a href="'.$reg_login_page.'"> here </a>.</h3>';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	update_option('epjbjobboard_thank_you_page', $newpost_id);
	/// **** Create Page for  Employer Directory ******	
	$page_title='Employer Directory';
	$page_name='employer-directory';
	$page_content='[jobs_employer_directory]';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	update_option('epjbjobboard_employer_dir_page', $newpost_id);
	/// **** Create Page for  Candidate Directory ******	
	$page_title='Candidate Directory';
	$page_name='candidate-directory';
	$page_content='[jobs_candidate_directory]';
	$my_post_form = array(
	'post_title'    => wp_strip_all_tags( $page_title),
	'post_name'    => wp_strip_all_tags( $page_name),
	'post_content'  => $page_content,
	'post_status'   => 'publish',
	'post_author'   =>  get_current_user_id(),	
	'post_type'		=> 'page',
	);
	$newpost_id= wp_insert_post( $my_post_form );	
	update_option('epjbjobboard_candidate_dir_page', $newpost_id);