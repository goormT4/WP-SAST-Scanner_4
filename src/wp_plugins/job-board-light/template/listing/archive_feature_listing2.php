<?php
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	$features = array(
	'relation' => 'AND',
	array(
	'key'     => 'jobboard_featured',
	'value'   => 'featured',
	'compare' => 'LIKE'
	),
	);
	$feature_listing_all['posts_per_page']='-1';
	$feature_listing_all['meta_query'] = array(
	$city_mq, $country_mq, $zip_mq,$features,$area_mq,
	);
	$feature_listing = new WP_Query( $feature_listing_all ); 
	$job_top_1_icon=get_option('job_top_1_icon');	
	if($job_top_1_icon==""){$job_top_1_icon='fas fa-home';}
	$job_top_2_icon=get_option('job_top_2_icon');	
	if($job_top_2_icon==""){$job_top_2_icon='fas fa-bed';}
	$job_top_3_icon=get_option('job_top_3_icon');	
	if($job_top_3_icon==""){$job_top_3_icon='fas fa-shower';}	
	$job_top_4_icon=get_option('job_top_4_icon');	
	if($job_top_4_icon==""){$job_top_4_icon='fas fa-expand';}		
	if ( $feature_listing->have_posts() ) : 
	while ( $feature_listing->have_posts() ) : $feature_listing->the_post();
	$dir_data=array();			
	$id = get_the_ID();
	$dir_data['id']=$id;
	$dir_data['featured']='featured';
	$dir_data['link']=get_permalink($id);
	$dir_data['title']=$post->post_title;
	$feature_img='';
	$listing_contact_source=get_post_meta($id,'listing_contact_source',true);
	if($listing_contact_source==''){$listing_contact_source='user_info';}
	if($listing_contact_source=='new_value'){
		$company_logo='';
		}else{
	}
	if(has_post_thumbnail()){
		$feature_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'large' );
		if($feature_image[0]!=""){
			$feature_img =$feature_image[0];
		}
		}else{
		$feature_img= wp_jobboard_URLPATH."assets/images/job.png";
	}
	$dir_data['imageURL']=  $feature_img;
	$cat_arr=array();
	$currentCategory = $main_class->jobboard_get_categories_caching($id,$directory_url);
	$cat_name2='';
	if(isset($currentCategory[0]->slug)){
		$cat_name2 = $currentCategory[0]->name;
		$cc=0;
		foreach($currentCategory as $c){
			$cat_arr[]=ucfirst($c->name);
		}
	}
	$dir_data['category']=$cat_arr;		
	$phone='';
	$listing_contact_source=get_post_meta($id,'listing_contact_source',true);
	if($listing_contact_source==''){$listing_contact_source='new_value';}
	$dir_data['phone']='';
	$dir_data['experience_range']=get_post_meta($id,'experience_range',true);
	$dir_data['educational_requirements']=get_post_meta($id,'educational_requirements',true);
	$post_author_id= get_post_field( 'post_author', $id );	
	$dir_data['company_name']=	get_user_meta($post_author_id,'full_name',true);
	$dir_data['type']=get_post_meta($id,'job_status',true);		
	$dir_data['job_type']= get_post_meta($id,'job_type',true);		
	$dir_data['salary']= get_post_meta($id,'salary',true);		
	$dir_data['p_date']= get_the_date('M d, Y', $id);
	if($listing_contact_source=='new_value'){
			$dir_data['phone']=	get_post_meta($id,'phone',true);
			$phone=get_post_meta($id,'phone',true);
			$dir_data['email']=get_post_meta($id,'email',true);
			$contact_web=get_post_meta($id,'contact_web',true);
			$contact_web=str_replace('https://','',$contact_web);
			$contact_web=str_replace('http://','',$contact_web);
			$dir_data['web']=	esc_url($contact_web);
			if(trim(get_post_meta($id,'company_name',true))==""){
				$post_author_id= get_post_field( 'post_author', $id );				
				$company_name= get_user_meta($post_author_id,'full_name',true);
				update_post_meta($id,'company_name',$company_name);
			}
			$dir_data['company_name']=	get_post_meta($id,'company_name',true);
		}else{
			$post_author_id= get_post_field( 'post_author', $id );
			$agent_info = get_userdata($post_author_id);
			if(get_user_meta($post_author_id,'phone',true)!=""){
				$dir_data['phone']=	get_user_meta($post_author_id,'phone',true);
				$phone=get_user_meta($post_author_id,'phone',true);
			}
			$contact_web=get_user_meta($post_author_id,'web_site',true);
			$contact_web=str_replace('https://','',$contact_web);
			$contact_web=str_replace('http://','',$contact_web);
			$dir_data['web']=	esc_url($contact_web);
			$dir_data['email']=$agent_info->user_email;
			$dir_data['company_name']=	esc_attr(get_user_meta($post_author_id,'full_name',true));
			
		}
	$dir_style5_call=get_option('dir_style5_call');
	if($dir_style5_call==""){$dir_style5_call='yes';}
	$dirpro_call_button=get_post_meta($id,'dirpro_call_button',true);
	if($dirpro_call_button==""){$dirpro_call_button='yes';}
	if($dir_style5_call=="yes" AND $dirpro_call_button=='yes'){
		$dir_data['call_button']='yes';
		if($dir_data['phone']==''){$dir_data['call_button']='no';}
		}else{
		$dir_data['call_button']='no';
	}
	$dir_style5_email=get_option('dir_style5_email');
	if($dir_style5_email==""){$dir_style5_email='yes';}
	$dirpro_email_button=get_post_meta($id,'dirpro_email_button',true);
	if($dirpro_email_button==""){$dirpro_email_button='yes';}
	if($dir_style5_email=="yes" AND $dirpro_email_button=='yes'){
		$dir_data['email_button']='yes';
		}else{
		$dir_data['email_button']='no';
	}
	$loc_arr=array();
	$dir_data['address']= get_post_meta($id,'address',true);
	$dir_data['city']=ucfirst( get_post_meta($id,'city',true));
	if(trim(get_post_meta($id,'city',true))!=""){
		array_push( $loc_arr, get_post_meta($id,'city',true) );
		$dir_data['location']=ucwords(strtolower(trim(get_post_meta($id,'city',true))));
	}
	$dir_data['state']= get_post_meta($id,'state',true);
	if(get_post_meta($id,'postcode',true)!=''){
		$dir_data['zipcode']= ucwords(strtolower(trim(get_post_meta($id,'postcode',true))));
	}
	if(get_post_meta($id,'deadline',true)!=''){
		$dir_data['deadline']=date('M d, Y',strtotime(get_post_meta($id,'deadline',true)));
		}else{
		$dir_data['deadline']='';
	}
	if(get_post_meta($id,'local-area',true)!=''){
		$dir_data['local-area']= ucwords(strtolower(trim(get_post_meta($id,'local-area',true))));
	}
	if(get_post_meta($id,'gender',true)!=''){
		$dir_data['gender']= ucwords(strtolower(trim(get_post_meta($id,'gender',true))));
	}
	if(get_post_meta($id,'job_type',true)!=''){
		$dir_data['jobtype']= ucwords(strtolower(trim(get_post_meta($id,'job_type',true))));
	}
	if(get_post_meta($id,'job_level',true)!=''){
		$dir_data['joblevel']= ucwords(strtolower(trim(get_post_meta($id,'job_level',true))));
	}		
	if(get_post_meta($id,'experience_range',true)!=''){			
		$dir_data['experiencerange']= ucwords(strtolower(trim(get_post_meta($id,'experience_range',true))));
	}
	if(get_post_meta($id,'educational_requirements',true)!=''){			
		$dir_data['educationalrequirements']= ucwords(strtolower(trim(get_post_meta($id,'educational_requirements',true))));
	}
	$postdate=get_the_date('', $id);
	$now = time(); 
	$your_date = strtotime($postdate);
	$datediff = $now - $your_date;
	$datediff_round= round($datediff / (60 * 60 * 24));		
	if($datediff_round<=1 AND $datediff_round<2 ){
		$dir_data['postdate']= esc_html__('Today', 'jobboard'); 
	}
	if($datediff_round>=2 AND $datediff_round<3){
		$dir_data['postdate']=esc_html__('Last 2 Days', 'jobboard');
	}
	if($datediff_round>=3 AND $datediff_round<4){
		$dir_data['postdate']=esc_html__('Last 3 Days', 'jobboard');
	}
	if($datediff_round>=4 AND $datediff_round <8 ){
		$dir_data['postdate']=esc_html__('Last 7 Days', 'jobboard');
	}
	//postdeadline
	if(trim(get_post_meta($id,'deadline',true))!=''){
			$postdeadline=get_post_meta($id,'deadline',true);
			$now = time(); 
			$your_date = strtotime($postdeadline);
			$datediff = $your_date - $now;
			$datediff_round= round($datediff / (60 * 60 * 24));
			
			if($datediff_round<=1 AND $datediff_round<2 ){
				$dir_data['postdeadline']= esc_html__('Today', 'jobboard'); 
			}
			if($datediff_round>=2 AND $datediff_round<3){
				$dir_data['postdeadline']=esc_html__('Next 2 Days', 'jobboard');
			}
			if($datediff_round>=3 AND $datediff_round<4){
				$dir_data['postdeadline']=esc_html__('Next 3 Days', 'jobboard');
			}
			if($datediff_round>=4 ){
				$dir_data['postdeadline']=esc_html__('Over 7 Days', 'jobboard');
			}
		}
		if(trim(get_post_meta($id,'salary',true))!=''){
			$salaryrange= get_post_meta($id,'salary',true);			
			 $salaryrange= preg_replace("/([^0-9\\.])/i", "", $salaryrange);
			
			if($salaryrange>1 AND $salaryrange<=10000 ){
				$dir_data['salaryrange']= esc_html__('>10000', 'jobboard'); 
			}
			if($salaryrange>10000 AND $salaryrange<=30000){
				$dir_data['salaryrange']=esc_html__('10000 - 30000', 'jobboard');
			}
			if($salaryrange>30000 AND $salaryrange<=50000){
				$dir_data['salaryrange']=esc_html__('30000 - 50000', 'jobboard');
			}
			if($salaryrange>50000 AND $salaryrange<=80000){
				$dir_data['salaryrange']=esc_html__('50000 - 80000', 'jobboard');
			}
			if($salaryrange>80000 AND $salaryrange<=100000){
				$dir_data['salaryrange']=esc_html__('80000 - 100000', 'jobboard');
			}
			if($salaryrange>100000 ){
				$dir_data['salaryrange']=esc_html__('Over 100000', 'jobboard');
			}
		}
	
	
	
	$dir_data['country']= get_post_meta($id,'country',true);
	if (!empty($loc_arr)) {
	}
	// Tag***
	$tagg_arr=array();
	$dir_tags=get_option('epjbdir_tags');
	if($dir_tags==""){$dir_tags='yes';}
	if($dir_tags=="yes"){
		$tag_array = $main_class->jobboard_get_tags_caching($id,$directory_url);
		foreach($tag_array as $one_tag){
			if(isset($one_tag->name)){$tagg_arr[]=ucfirst($one_tag->name); }
		}
		}else{
		$tag_array= wp_get_post_tags( $id );
		foreach($tag_array as $one_tag){
			if(isset($one_tag->name)){$tagg_arr[]=ucfirst($one_tag->name); }
		}
	}
	if (!empty($tagg_arr)) {
		$dir_data['feature']=  $tagg_arr;
	}
	$user_ID = get_current_user_id();
	$favourites='no';
	if($user_ID>0){
		$my_favorite = get_post_meta($id,'_favorites',true);
		$all_users = explode(",", $my_favorite);
		if (in_array($user_ID, $all_users)) {
			$favourites='yes';
		}
	}
	$dir_data['favourites']=$favourites;
	array_push( $dirs_data, $dir_data );
	endwhile; 	
	endif; ?>	