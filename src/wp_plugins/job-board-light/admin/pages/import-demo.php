<?php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Are you cheating:user Permission?' );
}
global $current_user; global $wpdb;	
$directory_url=get_option('epjbjobboard_url');					
if($directory_url==""){$directory_url='job';}
	$post_names = array('PHP Developer','WordPress Developer','Warehouse Handler','Plant Technician', 'Warehouse Worker');
	$post_cat = array('Accounting','Commercial','IT & Telecommunication','Support Service','Sales & Marketing');	
	$post_tag = array('WordPress','Php','C++','Electrical','Dairy ','Food','Law & Legal','Creative','Beauty','Care/ Health','Fitness');
	$post_city = array('New York ','Dubai','Bretagne','New South Wales','London','Paris');	
	$post_aear = array('Central Brooklyn','Chelsea','Midtown','Shoreditch' , 'Upper Manhattan');
$i=0;	
	foreach($post_names as $one_post){ 
	$my_post = array();
	$my_post['post_title'] = $one_post;
	$my_post['post_content'] = '<ul><li><b>
4+ years building modern web applications/sites in a digital agency or consulting environment</b></li><li><b>
Minimum of 4 years </b>in semantic HTML5 and CSS3, and comfortable with preprocessors</li><li><b>
Minimum of 4 years </b>with JavaScript and proficient in at least one modern JavaScript framework (e.g. React, Vue.js, AngularJS) - including the ability to work with remote APIs and third-party web services, loading data asynchronously, understanding state management, using JavaScript templates and dynamic views</li><li>
Comfortable with version control, preferably Git</li><li>
Proficient in responsive design development methodologies and best practices</li><li>
Must be legally authorized to work in the United States without the need for employer sponsorship, now or at any time in the future<br>
</li></ul>
 ';	
	$my_post['post_status'] = 'publish';	
	$my_post['post_type'] = $directory_url;	
	$newpost_id= wp_insert_post( $my_post );		
	
	$rand_keys = array_rand($post_cat, 2);	
	$new_post_arr=array();
	$new_post_arr[]=$post_cat[$rand_keys[0]];
	$new_post_arr[]=$post_cat[$rand_keys[1]];
	wp_set_object_terms( $newpost_id, $new_post_arr, $directory_url.'-category');	
	
	// For Tag Save tag_arr	
	$rand_keys = array_rand($post_tag, 6);	
	$new_post_arr=array();
	$new_post_arr[]=$post_tag[$rand_keys[0]];
	$new_post_arr[]=$post_tag[$rand_keys[1]];
	$new_post_arr[]=$post_tag[$rand_keys[2]];
	$new_post_arr[]=$post_tag[$rand_keys[3]];
	$new_post_arr[]=$post_tag[$rand_keys[4]];
	$new_post_arr[]=$post_tag[$rand_keys[5]];
	wp_set_object_terms( $newpost_id, $new_post_arr, $directory_url.'_tag');	
	update_post_meta($newpost_id, 'address', '129-133 West 22nd Street'); 
	$rand_keys = array_rand($post_aear, 1);	
	update_post_meta($newpost_id, 'local-area', $post_aear[$rand_keys]); 
	update_post_meta($newpost_id, 'latitude', '40.7427704'); 
	update_post_meta($newpost_id, 'longitude','-73.99455039999998');
	$rand_keys = array_rand($post_city, 1);		
	update_post_meta($newpost_id, 'city', $post_city[$rand_keys]); 
	update_post_meta($newpost_id, 'postcode', '10011'); 
	update_post_meta($newpost_id, 'country', 'USA'); 
	update_post_meta($newpost_id, 'phone', '212245-4606'); 
	update_post_meta($newpost_id, 'fax', '212245-4606'); 
		
	update_post_meta($newpost_id, 'company_name', 'Apple Inc'); 
	update_post_meta($newpost_id, 'contact-email', 'test@test.com'); 
	update_post_meta($newpost_id, 'contact_web', 'www.e-plugin.com'); 
	update_post_meta($newpost_id, 'listing_contact_source', 'new_value'); 	
	update_post_meta($newpost_id, 'youtube', '0y4rXoWrJlw'); 
	
	update_post_meta($newpost_id, 'vacancy', '5'); 
	update_post_meta($newpost_id, 'job_type', 'Full Time'); 
	update_post_meta($newpost_id, 'experience_range', '3 - <5 Years');	
	update_post_meta($newpost_id, 'salary', '$10000');
	update_post_meta($newpost_id, 'gender', 'Male');
	update_post_meta($newpost_id, 'job_level', 'Mid Level'); 
	update_post_meta($newpost_id, 'educational_requirements', 'MBA');
	$date = date('Y-m-d', strtotime('+'.$i.' days'));
	update_post_meta($newpost_id, 'deadline', $date); 
	update_post_meta($newpost_id, 'job_education', 'Strong understanding of Java 8, Microservices, Spring-boot, API Development and AWS
Proficient in Core Java /J2EE technologies & Spring framework Experience in Pair programming'); 
	update_post_meta($newpost_id, 'job_must_have', '<ul><li>
Ability to work independently, with minimal supervision and guidance</li><li>
Experience using Docker to package and deploy web applications</li><li>
Experience with cloud-based web services and database systems (e.g. AWS, Google Cloud, Microsoft Azure)</li><li>
Familiarity with server-side programming (e.g. Node.js, Python)</li><li>
Experience customizing Content Management Systems</li><li>
Experience working in an agile environment</li><li>
Background in user experience and/or design</li><li>
Involvement in open source projects</li></ul>'); 
	
	update_post_meta($newpost_id, 'other_benefits', 'As per company policy');  
 $i++; 
}
?>