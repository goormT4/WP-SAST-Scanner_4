<?php
	$main_class = new wp_jobboard;	
	global $wpdb;	
	if( isset($_REQUEST['iv-submit-listing']) && isset($_REQUEST['payment_gateway']) && $_REQUEST['iv-submit-listing']=='register' && $_REQUEST['payment_gateway']=='paypal'){	
		
	
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'signup1' ) ) {
			wp_die( 'Are you cheating:wpnonce1?' );
		}
		$package_id='';
		$package_id=sanitize_text_field($_POST['package_id']);
		$return_page_url=sanitize_text_field($_POST['return_page']);
		$userdata = array();
		$user_name='';
		if(isset($_POST['iv_member_user_name'])){
			$userdata['user_login']=sanitize_text_field($_POST['iv_member_user_name']);
		}					
		if(isset($_POST['iv_member_email'])){
			$userdata['user_email']=sanitize_text_field($_POST['iv_member_email']);
		}					
		if(isset($_POST['iv_member_password'])){
			$userdata['user_pass']=sanitize_text_field($_POST['iv_member_password']);
		}
		if($userdata['user_login']!='' and $userdata['user_email']!='' and $userdata['user_pass']!='' ){
			$user_id = username_exists( $userdata['user_login'] );
			if ( !$user_id and email_exists($userdata['user_email']) == false ) {							
				$user_id = wp_insert_user( $userdata ) ;
				$user = new WP_User( $user_id );
				$user->set_role('basic');
				$userId=$user_id;
				// profile image uploader 				
				$main_class->user_profile_image_upload($userId);				
				update_user_meta($user_id, 'user_type',sanitize_text_field($_POST['user_type'])); 	
				$default_fields = array();
				$default_fields=get_option('jobboard_profile_fields');
				$sign_up_array=get_option( 'jobboard_signup_fields');
				$field_type=  	get_option( 'jobboard_field_type' );
				if(is_array($default_fields)){
					foreach ( $default_fields as $field_key => $field_value ) {
						$sign_up='no';
						if(isset($sign_up_array[$field_key]) && $sign_up_array[$field_key] == 'yes') {
							$sign_up='yes';
						}
						if($sign_up=='yes'){
							if(strtolower(trim($field_key))!='wp_capabilities'){
								if(isset($field_type[$field_key]) && $field_type[$field_key]=='textarea'){
									update_user_meta($user_id,sanitize_text_field($field_key), sanitize_textarea_field($_POST[$field_key]));
								}elseif(isset($field_type[$field_key]) && $field_type[$field_key]=='url'){
									update_user_meta($user_id,sanitize_text_field($field_key), sanitize_url($_POST[$field_key]));
								}else{
									update_user_meta($user_id,sanitize_text_field($field_key), sanitize_text_field($_POST[$field_key]));
								}
							}
						}
					}
				}
							
				require_once( wp_jobboard_ABSPATH. 'inc/signup-mail.php');
				} else {
				$iv_redirect = get_option('epjbjobboard_registration');
				if(trim($iv_redirect)!=''){
					$reg_page= get_permalink( $iv_redirect).'?&package_id='.$package_id.'&message-error=User_or_Email_Exists'; 
					wp_redirect( $reg_page );
					exit;
				}	
			}
		}		
		if($userdata['user_login']=='' or $userdata['user_email']=='' or $userdata['user_pass']=='' ){
			$iv_redirect = get_option('epjbjobboard_registration');
			if(trim($iv_redirect)!=''){
				$reg_page= get_permalink( $iv_redirect).'?&package_id='.$package_id.'&message-error=exists'; 
				wp_redirect( $reg_page );
				exit;
			}	
		}
		// Cheek here Coupon ************
		$package_cost=0;	
		if(isset($userId)){
			$user = new WP_User( $userId );
			
				$role_package='basic';
			
				if ($role_package !== 'administrator') {
					$user->set_role($role_package);
				}
			update_user_meta($userId, 'jobboard_package_id',$package_id);
			// success Page
			$iv_redirect = get_option('epjbjobboard_profile_page');
			if(trim($iv_redirect)!=''){
				$reg_page= get_permalink( $iv_redirect); 
				wp_clear_auth_cookie();
				wp_set_current_user ( $user->ID );
				wp_set_auth_cookie  ( $user->ID );
				wp_safe_redirect( $reg_page );
				exit;
			}
		}	
		
	}
