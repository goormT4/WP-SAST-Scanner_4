<?php
	$directory_url=get_option('epjbjobboard_url');					
	if($directory_url==""){$directory_url='job';}
	global $post;
	add_action( 'manage_'.$directory_url.'_posts_custom_column' , 'jobboard_custom_job_column' );
	add_filter( 'manage_edit-'.$directory_url.'_columns',  'jobboard_set_custom_edit_job_columns'  );
	function jobboard_set_custom_edit_job_columns($columns) {	
		$columns['id'] = esc_html__('ID','jobboard'); 
		$columns['salary'] = esc_html__('Salary','jobboard'); 
		$columns['deadline'] = esc_html__('Deadline','jobboard');
		return $columns;
	}
	function jobboard_custom_job_column( $column ) {
		global $post;
		switch ( $column ) {
			case 'id' :		
			echo  esc_html($post->ID);
			break; 
			case 'salary' :		
			echo  get_post_meta($post->ID,'salary',true);		
			break; 
			case 'deadline' :
			echo date('M d, Y',strtotime(get_post_meta($post->ID,'deadline',true)));  
			break;
		}
	}		
	add_action( 'manage_job_apply_posts_custom_column' , 'jobboard_custom_job_apply_column' );
	add_filter( 'manage_edit-job_apply_columns',  'jobboard_set_custom_edit_job_apply_columns'  );
	function jobboard_set_custom_edit_job_apply_columns($columns) {				
		$columns['title'] = esc_html__('Candidate Name','jobboard');
		$columns['email'] = esc_html__('Email','jobboard');
		$columns['phone'] = esc_html__('Phone','jobboard');
		$columns['job'] = esc_html__('Job','jobboard');
		$columns['cv'] = esc_html__('CV','jobboard');
		return $columns;
	}
	function jobboard_custom_job_apply_column( $column ) {
		global $post;
		switch ( $column ) {
			case 'job' :		
			$job_post_id= get_post_meta($post->ID,'apply_jod_id',true);			
			echo get_the_title($job_post_id);
			break; 
			case 'phone' :
			if(get_post_meta($post->ID,'user_id',true)<1){
				echo esc_attr(get_post_meta($post->ID,'phone',true));  
				}else{
				$userid= get_post_meta($post->ID,'user_id',true);
				echo esc_attr(get_user_meta($userid,'phone',true));  
			}
			break;
			case 'email' :
			echo esc_attr(get_post_meta($post->ID,'email_address',true));  
			break;
			case 'cv' :
			$upload_dir = wp_upload_dir();
			$file_name=get_post_meta($post->ID, 'file_name', true);
			$useridpdf=get_post_meta($post->ID, 'user_id', true);
			if(get_post_meta($post->ID, 'user_id', true)!=''){ 
				echo'<a target="_blank" href="?&jobboardpdfcv='.esc_attr($useridpdf).'">'.esc_html__('Print CV','jobboard').' </a>';
				}else{
				echo'<a target="_blank" href="'. esc_url(get_post_meta($post->ID, 'cv_file_url', true) ).'"  > '.esc_html__('Print CV','jobboard').' </a>';
			}
			break;
		}
	}	
	
	add_action( 'manage_jobboard_message_posts_custom_column' , 'jobboard_custom_jobboard_message_column' );
	add_filter( 'manage_edit-jobboard_message_columns',  'jobboard_set_custom_edit_jobboard_message_columns'  );
	function jobboard_set_custom_edit_jobboard_message_columns($columns) {				
		$columns['Message'] = esc_html__('Message','jobboard');
		$columns['email'] = esc_html__('Email','jobboard');
		$columns['phone'] = esc_html__('Phone','jobboard');		
		return $columns;
	}
	function jobboard_custom_jobboard_message_column( $column ) {
		global $post;
		switch ( $column ) {
			case 'Message' :		
				echo esc_html($post->post_content);
			break; 
			case 'phone' :			
				echo esc_html(get_post_meta($post->ID,'from_phone',true));  
			break;
			case 'email' :
				echo esc_html(get_post_meta($post->ID,'from_email',true));  
			break;
			
			
		}
	}	
	
?>