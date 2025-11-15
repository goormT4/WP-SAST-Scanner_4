<?php
	global $post,$wpdb,$tag;
	$main_class = new wp_jobboard;
	wp_enqueue_script("jquery");
	wp_enqueue_style('bootstrap-jobboard-110', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	wp_enqueue_style('bootstrap-tagsinput', wp_jobboard_URLPATH . 'admin/files/css/bootstrap-tagsinput.css');
	wp_enqueue_style('jobboard-single-job', wp_jobboard_URLPATH . 'admin/files/css/single-job.css');
	wp_enqueue_style('jobboard-archive-job-style-grid-popup', wp_jobboard_URLPATH . 'admin/files/css/archive-job-style-grid-popup.css');
	
	wp_enqueue_style('all', wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
	wp_enqueue_style('jquery-ui', wp_jobboard_URLPATH . 'admin/files/css/jquery-ui.css');
	wp_enqueue_style('colorbox', wp_jobboard_URLPATH . 'admin/files/css/colorbox.css');
	wp_enqueue_script('colorbox', wp_jobboard_URLPATH . 'admin/files/js/jquery.colorbox-min.js');

	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	$current_post_type=$directory_url;
	$form_action='';
	if ( is_front_page() ) {
		$form_action='action='.get_post_type_archive_link($current_post_type).'';
	}
	$locations='';
	$pos='';
	$dirsearch='';
	$dirsearchtype='';
	$locationtype='';
	$location='';
	if(isset($_REQUEST['dirsearchtype'])){
		$dirsearch=sanitize_text_field($_REQUEST['dirsearch']);
		$dirsearchtype=sanitize_text_field($_REQUEST['dirsearchtype']);
	}
	if(isset($_REQUEST['locationtype'])){
		$locationtype=sanitize_text_field($_REQUEST['locationtype']);
		$location=sanitize_text_field($_REQUEST['location']);
	}
	
	$dir5_background_color=get_option('dir5_background_color');
	if($dir5_background_color==""){$dir5_background_color='#fff';}
	
	
	//*******************	
	if(isset($atts['main_background_color'])){
		$dir5_background_color=$atts['main_background_color'];
		if($dir5_background_color==""){$dir5_background_color='#EBEBEB';}
	}
	
	
?>
<style>	
	
	
	.site-content .ast-container {
	display: flex-row!important;
	}
</style>
<div class="bootstrap-wrapper">
	<div class="container">		
		<section class="whole-container">
			<div class="row bottomline-parent">
				<!-- The image set for Astra theme -->
				<img class="col-md-12" src="<?php echo wp_jobboard_URLPATH."assets/images/astra.png"; ?>">
				
				<div class="col-md-12 ">
					<button type="button" class="btn btn-secondary float-right mt-3" onclick="call_filter()" >
						<i class="fas fa-align-left mr-2"></i> <?php    esc_html_e('Filter Result','jobboard');?>
					</button>
				</div>	
				
				<div style="display:none"  tabindex="-1" >	
				
					<div class="whole-container " id="listingfilter">						
						<div class="filter" ></div>
						<div id=facets></div>
					</div>
					
				</div>
				<div class="col-lg-12 result-parent">
					<div id="results" class="row"></div>
				</div>
			</div>
		</section>
	</div>
</div>
<?php
	$dirs_data=array();
	$tag_arr= array();
	$args = array(
	'post_type' => $directory_url, // enter your custom post type
	'post_status' => 'publish',
	'posts_per_page'=> '-1',
	'orderby' => 'date',
    'order'   => 'DESC',
	);
	$dirsearch='';
	$dirsearchtype='';
	$locationtype='';
	$location='';
	if(isset($_REQUEST['dirsearchtype'])){
		$dirsearch=sanitize_text_field($_REQUEST['dirsearch']);
		$dirsearchtype=sanitize_text_field($_REQUEST['dirsearchtype']);	
		$args['s']=$dirsearch;
	}
	if(isset($_REQUEST['locationtype'])){		
		$locationtype=sanitize_text_field($_REQUEST['locationtype']);
		$location=sanitize_text_field($_REQUEST['location']);
	}
	$dir_facet_title=get_option('dir_facet_cat_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Categories','jobboard');}
	if(strtolower($dir_facet_title)==strtolower($dirsearchtype)){
		$args[$directory_url.'-category']=$dirsearch;
	}
	$dir_facet_title=get_option('dir_facet_features_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Features','jobboard');}
	if(strtolower($dir_facet_title)==strtolower($dirsearchtype)){
		$args[$directory_url.'_tag']=$dirsearch;
	}
	$dir_facet_title= esc_html__('Title','jobboard');
	if(strtolower($dir_facet_title)==strtolower($dirsearchtype)){
		$args['s']= $dirsearch;
	}
	$dir_facet_title=get_option('dir_facet_location_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('City','jobboard');}
	$city_mq ='';
	if(strtolower($dir_facet_title)==strtolower($locationtype)){
		$city_mq = array(
		'relation' => 'AND',
		array(
		'key'     => 'city',
		'value'   => $location,
		'compare' => 'LIKE'
		),
		);
	}
	$area_mq='';
	$dir_facet_title=get_option('dir_facet_area_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Area','jobboard');}
	if(strtolower($dir_facet_title)==strtolower($locationtype)){
		$area_mq = array(
		'relation' => 'AND',
		array(
		'key'     => 'area',
		'value'   => $location,
		'compare' => 'LIKE'
		),
		);
	}
	$country_mq='';
	$zip_mq='';
	$dir_facet_title=get_option('dir_facet_zipcode_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Zipcode','jobboard');}
	if(strtolower($dir_facet_title)==strtolower($locationtype)){
		$zip_mq = array(
		'relation' => 'AND',
		array(
		'key'     => 'postcode',
		'value'   => $location,
		'compare' => 'LIKE'
		),
		);
	}
	if(isset($atts['category']) and $atts['category']!="" ){
		$postcats = $atts['category'];
		$args[$directory_url.'-category']=$postcats;
	}
	if(get_query_var($directory_url.'-category')!=''){
		$postcats = get_query_var($directory_url.'-category');
		$args[$directory_url.'-category']=$postcats;
		$selected=$postcats;
		$search_show=1;
	}
	if( isset($_POST[$directory_url.'-category'])){
		if($_POST[$directory_url.'-category']!=''){
			$postcats = sanitize_text_field($_POST[$directory_url.'-category']);
			$args[$directory_url.'-category']=$postcats;
			$selected=$postcats;
		}
	}
	if(get_query_var($directory_url.'_tag')!=''){
		$postcats = get_query_var($directory_url.'_tag');
		$args[$directory_url.'_tag']=$postcats;
		$search_show=1;
	}
	if(get_query_var('employer')!=''){
		$author = get_query_var('employer');
		$args['author']=(int) sanitize_text_field($author);		
	}
	if( isset($_REQUEST['employer'])){ 
		$author = $_REQUEST['employer'];
		$args['author']= (int)sanitize_text_field($author);		
	}
	if( isset($_REQUEST['keyword'])){ 
		if($_REQUEST['keyword']!=""){  
			$args['s']= sanitize_text_field($_REQUEST['keyword']);
			$keyword_post=sanitize_text_field($_REQUEST['keyword']);
		}
	}
	if( isset($_REQUEST['tag_arr'])){
		if($_REQUEST['tag_arr']!=""){
			$tag_arr= sanitize_text_field($_REQUEST['tag_arr']);
			$tags_string= implode("+", $tag_arr);
			$args['tag']= $tags_string;
		}
	}
	// Meta Query***********************
	$city_mq2 ='';
	if(isset($_REQUEST['dir_city']) AND $_REQUEST['dir_city']!=''){
		$city_mq = array(
		'relation' => 'AND',
		array(
		'key'     => 'city',
		'value'   => sanitize_text_field($_REQUEST['dir_city']),
		'compare' => 'LIKE'
		),
		);
	}
	$country_mq2='';
	if(isset($_REQUEST['dir_country']) AND $_REQUEST['dir_country']!=''){
		$country_mq = array(
		'relation' => 'AND',
		array(
		'key'     => 'country',
		'value'   => sanitize_text_field($_REQUEST['dir_country']),
		'compare' => 'LIKE'
		),
		);
	}
	$zip_mq2='';
	if(isset($_REQUEST['zipcode']) AND $_REQUEST['zipcode']!=''){
		$zip_mq = array(
		'relation' => 'AND',
		array(
		'key'     => 'postcode',
		'value'   => sanitize_text_field($_REQUEST['zipcode']),
		'compare' => 'LIKE'
		),
		);
	}
	// For featrue listing***********
	$feature_listing_all =array();
	$feature_listing_all =$args;
	$args['meta_query'] = array(
	$city_mq, $country_mq, $zip_mq,$area_mq,$city_mq2, $country_mq2, $zip_mq2,
	);
	include( wp_jobboard_template. 'listing/archive_feature_listing2.php');
	$the_query = new WP_Query( $args );
	if ( $the_query->have_posts() ) :
	while ( $the_query->have_posts() ) : $the_query->the_post();
	$dir_data=array();
	$id = get_the_ID();
	$post_author_id= get_post_field( 'post_author', $id );
	
	$main_class->check_listing_expire_date($id, $post_author_id, $directory_url);		
	
	if(get_post_meta($id, 'jobboard_featured', true)!='featured'){
		$dir_data['id']=$id;
		$dir_data['link']=get_permalink($id);
		$dir_data['title']=esc_html($post->post_title);			
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
			$dir_data['web']=	$contact_web;				
			if(trim(get_post_meta($id,'company_name',true))==""){					
				$company_name= get_user_meta($post_author_id,'full_name',true);
				update_post_meta($id,'company_name',$company_name);
			}
			$dir_data['company_name']=	get_post_meta($id,'company_name',true);
		}else{		
			$agent_info = get_userdata($post_author_id);
			if(get_user_meta($post_author_id,'phone',true)!=""){
				$dir_data['phone']=	get_user_meta($post_author_id,'phone',true);
				$phone=esc_html(get_user_meta($post_author_id,'phone',true));
			}
			$contact_web=esc_url(get_user_meta($post_author_id,'web_site',true));
			$contact_web=str_replace('https://','',$contact_web);
			$contact_web=str_replace('http://','',$contact_web);
			$dir_data['web']=	esc_url($contact_web);
			$dir_data['email']=$agent_info->user_email;
			$dir_data['company_name']=	esc_html(get_user_meta($post_author_id,'full_name',true));
		}
		$dirpro_web_button=get_post_meta($id,'dirpro_web_button',true);
		if($dirpro_web_button==""){$dirpro_web_button='yes';}
		if($dirpro_web_button=="yes" ){
			$dir_data['web_button']='yes';			
			}else{
			$dir_data['web_button']='no';
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
		$dir_data['country']= esc_html(get_post_meta($id,'country',true));
		if (!empty($loc_arr)) {
		}
		// Tag***
		$tagg_arr=array();
		$tag_array = $main_class->jobboard_get_tags_caching($id,$directory_url);
		foreach($tag_array as $one_tag){
			if(isset($one_tag->name)){$tagg_arr[]=ucfirst($one_tag->name); }
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
	}
	endwhile;
	endif;
	$dirs_data_json= json_encode($dirs_data);
	$facets = array();
	$dir_facet_show=get_option('dir_facet_cat_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_cat_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Categories','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['category']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_postdeadline_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_postdeadline_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Deadline','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['postdeadline']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_jobtype_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_jobtype_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Job Nature','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['jobtype']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_joblevel_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_joblevel_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Job Level','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['joblevel']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_features_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_features_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Tags','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['feature']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_gender_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_gender_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Gender','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['gender']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_postdate_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_postdate_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Post Date','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['postdate']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_salary_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_salary_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Salary Range','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['salaryrange']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_experiencerange_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_experiencerange_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Experience','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['experiencerange']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_educational_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_educational_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Qualification','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['educationalrequirements']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_location_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_location_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('City','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['location']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_area_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_area_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Area','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['local-area']=$dir_facet_title;
	}
	$dir_facet_show=get_option('dir_facet_zipcode_show');
	if($dir_facet_show==""){$dir_facet_show='yes';}
	$dir_facet_title=get_option('dir_facet_zipcode_title');
	if($dir_facet_title==""){$dir_facet_title= esc_html__('Zipcode','jobboard');}
	if($dir_facet_show=="yes"){
		$facets['zipcode']=$dir_facet_title;
	}
	$facets_json= json_encode($facets);
	$dir_style5_perpage=get_option('dir_style5_perpage');
	if($dir_style5_perpage==""){$dir_style5_perpage=20;}
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('underscore-1.1.7', wp_jobboard_URLPATH . 'admin/files/js/underscore-1.1.7.js');
	wp_enqueue_script('popper', wp_jobboard_URLPATH . 'admin/files/js/popper.min.js');
	wp_enqueue_script('bootstrap.min-4-script-24', wp_jobboard_URLPATH . 'admin/files/js/bootstrap.min.js');
	wp_enqueue_script('iv_directory-ar-script-30', wp_jobboard_URLPATH . 'admin/files/js/facetedsearch.js');
	wp_localize_script('iv_directory-ar-script-30', 'dirpro_data', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loadmore'=>esc_html__('Load More','jobboard'),
	'nolisting'=>esc_html__("Sorry, but no items match these criteria",'jobboard'),
	'Sortby'=>esc_html__("Sort by",'jobboard'),
	'Results'=>esc_html__("Results",'jobboard'),
	'Deselect'=>esc_html__("Deselect all filters",'jobboard'),
	'perpage'=>$dir_style5_perpage,
	) );
	wp_enqueue_script('iv_directory-ar-script-27', wp_jobboard_URLPATH . 'admin/files/js/archive-listing-faceted-grid.js');
	wp_localize_script('iv_directory-ar-script-27', 'dirpro_data', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'wp_jobboard_URLPATH'		=> wp_jobboard_URLPATH,
	'current_user_id'	=>get_current_user_id(),
	'facets_json'		=>$facets_json,
	'dirpro_items'		=>$dirs_data_json,
	'apply'		=>esc_html__('Apply','jobboard'),
	'featured'=>esc_html__('featured','jobboard'),
	'Add_to_Favorites'=>esc_html__('Add to Favorites', 'jobboard' ),
	'Added_to_Favorites'=>esc_html__('Added to Favorites', 'jobboard' ),		
	'email'=>esc_html__('Email','jobboard'),
	'SMS'=>esc_html__('SMS','jobboard'),
	'message'=>esc_html__('Please put your name,email & content','jobboard'),
	'detail'=>esc_html__('Detail','jobboard'),
	'web'=>esc_html__('Web','jobboard'),
	'deadline'=>esc_html__('Deadline','jobboard'),
	'title'=>esc_html__('Title','jobboard'),
	'category'=>esc_html__('Category','jobboard'),
	'random'=>esc_html__('Random','jobboard'),
	'Posted'=>esc_html__('Posted', 'jobboard' ), 
	'perpage'=>$dir_style5_perpage,	
	'pos'=>$pos,
	'SMSbody'=>esc_html__('I would like to inquire about the listing. The listing can be found on the site :','jobboard').site_url(),
	'contact'=> wp_create_nonce("contact"),
	) );
	wp_enqueue_script('jobboard_message', wp_jobboard_URLPATH . 'admin/files/js/user-message.js');
	wp_localize_script('jobboard_message', 'jobboard_data_message', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',		
	'Please_put_your_message'=>esc_html__('Please put your name,email & message', 'jobboard' ),
	'contact'=> wp_create_nonce("contact"),
	'listing'=> wp_create_nonce("listing"),
	) );
	wp_enqueue_script('jobboard-ar-script-38', wp_jobboard_URLPATH . 'admin/files/js/single-listing.js');
	wp_localize_script('jobboard-ar-script-38', 'jobboard_data', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'current_user_id'	=>get_current_user_id(),
	'Please_login'=>esc_html__('Please login', 'jobboard' ),
	'Add_to_Favorites'=>esc_html__('Add to Favorites', 'jobboard' ),
	'Added_to_Favorites'=>esc_html__('Added to Favorites', 'jobboard' ),		
	'Please_put_your_message'=>esc_html__('Please put your name,email,Cover letter & attached file', 'jobboard' ),
	'contact'=> wp_create_nonce("contact"),
	'dirwpnonce'=> wp_create_nonce("myaccount"),
	'listing'=> wp_create_nonce("listing"),
	'cv'=> wp_create_nonce("Doc/CV/PDF"),
	'wp_jobboard_URLPATH'=>wp_jobboard_URLPATH,
	) );
?>	