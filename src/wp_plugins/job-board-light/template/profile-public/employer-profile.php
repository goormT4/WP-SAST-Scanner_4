<?php
	wp_enqueue_script("jquery");	
	wp_enqueue_style('wp-jobboard-public-111', wp_jobboard_URLPATH .'admin/files/css/iv-bootstrap.css');
	wp_enqueue_style('wp-jobboard-piblic-13', wp_jobboard_URLPATH . 'admin/files/css/profile-public.css');
	wp_enqueue_style('all-awesome', wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
	wp_enqueue_style('colorbox', wp_jobboard_URLPATH . 'admin/files/css/colorbox.css');
	wp_enqueue_script('colorbox', wp_jobboard_URLPATH . 'admin/files/js/jquery.colorbox-min.js');
	/**************************** css resources from qdesk ********************************************/
	wp_enqueue_style('main-css', wp_jobboard_URLPATH . 'admin/files/css/main.css');
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	$display_name='';
	$email='';
	$current_page_permalink='';
	$user_id=1;
	if(isset($_REQUEST['id'])){
		$author_name= sanitize_text_field($_REQUEST['id']);
			$user = get_user_by( 'ID', $author_name );
		if(isset($user->ID)){
			$user_id=$user->ID;
			$display_name=$user->display_name;
			$email=$user->user_email;
		}
		}else{
		global $current_user;
		$user_id=$current_user->ID;
		$display_name=$current_user->display_name;
		$email=$current_user->user_email;
		$author_name= $current_user->ID;
		if($user_id==0){
			$user_id=1;
		}
	}
	$iv_profile_pic_url=get_user_meta($user_id, 'iv_profile_pic_thum',true);
	
	
?>
<div class="bootstrap-wrapper wrapper" id="">
<input type="hidden" id="profileID" value="<?php echo esc_attr($user_id); ?>">
	<main class="pt-1">
        <div class="primary-page">
			<div class="container">
				<div class="item-detail-special contentshowep">
					<div class="col-md-2 ">
						<?php
							$iv_profile_pic_url=get_user_meta($user_id, 'iv_profile_pic_thum',true);
							if($iv_profile_pic_url!=''){ ?>
							<img  src="<?php echo esc_url($iv_profile_pic_url); ?>">
							<?php
								}else{
								echo'	 <img src="'. wp_jobboard_URLPATH.'assets/images/company-enterprise.png">';
							}
						?>
					</div>
						<div class="col-md-7">
							<div class="align-items-lg-center">							
								<h2 class="title-detail"><?php echo esc_html(get_user_meta($user_id,'full_name',true)); ?></h2>
									<?php
										if(get_user_meta($user_id,'address',true)!=''){
										?>
									<div class="date-style2">
										<p class="p-0 m-0">
											<span class="location"><i class="far fa-map"></i> <?php echo esc_html(get_user_meta($user_id,'address',true)); ?> <?php echo esc_html(get_user_meta($user_id,'city',true)); ?>, <?php echo esc_html(get_user_meta($user_id,'zipcode',true)); ?>,<?php echo esc_html(get_user_meta($user_id,'country',true)); ?></span>
										</p>
									</div>
									<?php
										}
									?>
									<div class="meta-job">
										<p class="p-0 m-0">
										<?php
										if(get_user_meta($user_id,'website',true)!=''){
										?>
										<span class="website"><i class="fa fa-link"></i><a href="<?php echo esc_url(get_user_meta($user_id,'website',true)); ?>" target="_blank"><?php echo esc_url(get_user_meta($user_id,'website',true)); ?></a></span>
										
										<?php
										}
										?>
											
										<?php
											if(get_user_meta($user_id,'phone',true)!=''){
											?>
											<span class="phone"><i class="fa fa-mobile-alt"></i><?php echo esc_html(get_user_meta($user_id,'phone',true)); ?></span>
											<?php
											}
										?>
										<?php
											if(!empty($email)){
											?>
											<span class="mail"><i class="far fa-envelope"></i><?php echo esc_html($email) ?></span>
											<?php
											}
										?>
										
											
										</p>
									</div>
								
							</div>
						</div>
						<div class="col-md-3">
								<div class="btn-feature p-0"><a class="btn btn-light-green" href="<?php echo get_post_type_archive_link( $directory_url ).'?employer='.$user_id; ?>"><?php $main_class = new wp_jobboard; echo esc_html($main_class->jobboard_total_job_count($user_id, $allusers='no' ));?> <?php esc_html_e('Jobs', 'jobboard'); ?></a>					
									<?php
										$current_ID = get_current_user_id();
										$favourites='no';
										if($current_ID>0){
											$my_favorite = get_post_meta($user_id,'jobboard_employerbookmark',true);											
											$all_users = explode(",", $my_favorite);
											if (in_array($current_ID, $all_users)) {
												$favourites='yes';
											}
										}
										$added_to_Boobmark=esc_html__('Added to Boobmark', 'jobboard');
										$add_to_Boobmark=esc_html__('Add to Boobmark', 'jobboard');
									?>
									<button id="employerbookmark" class="btn <?php echo ($favourites=='yes'?'btn-added-favourites ':'btn-light btn-add-favourites' ); ?>"  title="<?php echo ($favourites=='yes'? $added_to_Boobmark: $add_to_Boobmark ); ?>" ><i class="far fa-star"></i></button>
								</div>
							</div>
						
					
				</div>
				<div class="row">
					<div class="col-lg-8">
						<div class="content-main-right single-detail contentshowep">
							<div class="box-description">
								<h3><?php esc_html_e('About Company', 'jobboard'); ?></h3>
								<?php 		
								$content= get_user_meta($user_id,'description',true);								
								$content = apply_filters('the_content', $content);
								$content = str_replace(']]>', ']]&gt;', $content);
								echo wpautop($content);
								?>
								
							</div>
						</div>
						
						<div class="simillar-jobs">							
							<div class="content-main-right list-jobs contentshowep">							
								<div class="list ">
								<h3 class="p-4"><?php esc_html_e('Open Positions', 'jobboard'); ?></h3>
								<?php
								$directory_url=get_option('epjbjobboard_url');
								if($directory_url==""){$directory_url='job';}
								$args = array(
									'post_type' => $directory_url, // enter your custom post type
									'paged' => '1',
									'author'=> $user_id , 
									'post_status' => 'publish',	
									'posts_per_page'=> '10',  // overrides posts per page in theme settings
								);
								$open_positions = new WP_Query( $args );
								 if ( $open_positions->have_posts() ) :
									while ( $open_positions->have_posts() ) : $open_positions->the_post();
												$jobid = get_the_ID();
									?>			
									<div class="job-item">
										<div class="row align-items-center">
											<div class="col-md-9 col-lg-8 col-xl-9">
												<div class="text">
													<h3 class="title-job"><a href="<?php echo get_the_permalink($jobid); ?>"><?php echo get_the_title($jobid); ?></a></h3>
													<div class="date-job">
														<p><?php esc_html_e('Posted ', 'jobboard'); ?> <?php echo get_the_date('M d, Y', $jobid); ?></p>
													</div>
													<div class="meta-job">
														<p>
														<?php
														if(get_post_meta($jobid,'salary', true)!=''){
														?>
															<span class="salary"><i class="far fa-money-bill-alt"></i><?php echo esc_html(get_post_meta($jobid,'salary', true)); ?></span>
														<?php
														}
														?>
														<?php
														if(get_post_meta($jobid,'job_type', true)!=''){
														?>
															<span class="time"><i class="far fa-clock"></i><?php echo esc_html(get_post_meta($jobid,'job_type', true)); ?></span>
														<?php
														}
														?>
														</p>
													</div>
												</div>
											</div>
											<div class="col-md-3 col-lg-4 col-xl-3 text-md-right">
												<button onclick="apply_popup('<?php echo esc_attr($jobid);?>')" class="btn btn-light"><?php esc_html_e('Apply','jobboard'); ?></button>
												</div>
										</div>
									</div>
										
									
								<?php	
									endwhile;
								endif;
								?>									
								</div>
							</div>
						</div>
						
					</div>
					<div class="col-lg-4">
						<div class="sidebar-right contentshowep">
							<div class="sidebar-right-group">
								<div class="job-detail-summary">
									<h3 class="title-block"><?php esc_html_e('Company Information', 'jobboard'); ?></h3>
									<ul>
									<li class="row">
										<span class="col-6 col-md-5"><?php esc_html_e('Industry', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-6 px-0"><?php echo esc_html(get_user_meta($user_id,'company_type',true));?></div>
									</li>
										
									<?php
									$default_fields = array();
									$field_set=get_option('jobboard_profile_fields' );
									$all_empty='no';
									if($field_set!=""){
										$default_fields=get_option('jobboard_profile_fields' );
										}else{
										$default_fields['full_name']='Full Name';								
										$default_fields['company_since']='Estd Since';
										$default_fields['team_size']='Team Size';									
										$default_fields['phone']='Phone Number';			
										$default_fields['address']='Address';
										$default_fields['city']='City';
										$default_fields['zipcode']='Zipcode';
										$default_fields['state']='State';
										$default_fields['country']='Country';	
										$default_fields['website']='Website Url';
										$default_fields['description']='About';
										$all_empty='yes';
									}
									$field_type_roles=  	get_option( 'jobboard_field_type_roles' );			
									$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );
									
									$not_show= array('description','country','state','zipcode','city','address','full_name');
									
									$user = new WP_User( $user_id);
									$i=1;
									foreach ( $default_fields as $field_key => $field_value ) { 
										$role_access='no';
										if(isset($myaccount_fields_array[$field_key])){ 
											if($myaccount_fields_array[$field_key]=='yes'){
												
												if(in_array('all',$field_type_roles[$field_key] )){
													$role_access='yes';
												}
												if(in_array('administrator',$field_type_roles[$field_key] )){
													$role_access='yes';
												}
												if(in_array('employer',$field_type_roles[$field_key] )){
													$role_access='yes';
												}
												if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
													foreach ( $user->roles as $role ){
														if(in_array($role,$field_type_roles[$field_key] )){
														$role_access='yes';
														}
														if('administrator'==$role){
															$role_access='yes';
														}
													}
												}	
												
											}
										}
										
										if($role_access=='yes' OR $all_empty=='yes' ){
											if(!in_array($field_key,$not_show)){
												if(get_user_meta($user_id,$field_key,true)!=''){?> 
													<li class="row">
														<span class="col-6 col-md-5"><?php echo esc_html($field_value); ?></span>
														<span class="col-1 col-md-1 px-0">:</span>
														<div class="col-5 col-md-6 px-0"><?php echo esc_html( get_user_meta($user_id,$field_key,true)); ?></div>
													</li>
												
												<?php													
												}										
											}
										}
									}	
									?>																				
									</ul>
									<center>										
										<a class="btn btn-light-green w-60" onclick="candidate_email_popup('<?php echo esc_attr($user_id);?>')">
									<?php esc_html_e('Contact Us', 'jobboard'); ?></a>
									</center>                    </div>
									
									<?php
										
										$dir_map=get_option('jobboard_dir_map');
										if($dir_map==""){$dir_map='yes';}
										if($dir_map=='yes'){
											$address=get_user_meta($user_id,'address',true).'+'.get_user_meta($user_id,'city',true).'+'.get_user_meta($user_id,'postcode',true).'+'.get_user_meta($user_id,'country',true);
										?>
											<div class="side-right-map">
												<h3 class="title-block"><?php esc_html_e('Location', 'jobboard'); ?></h3>
												<iframe width="100%" height="225" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo esc_attr($address); ?>&amp;ie=UTF8&amp;&amp;output=embed"></iframe>
											</div>
										<?php
										}
										?>

								
									<div class="side-right-social">
										<h3 class="title-block"><?php esc_html_e('Share This Profile', 'jobboard'); ?></h3>
										<ul>
											<li><a href="<?php echo esc_url('//www.facebook.com/sharer/sharer.php?u');?>=<?php the_permalink();  ?>"><i class="fab fa-facebook-f"></i></a></li>									
											<li><a href="<?php echo esc_url('//www.linkedin.com/shareArticle?mini=true&url=test&title');?>=<?php the_title(); ?>&summary=&source="><i class="fab fa-linkedin-in"></i></a></li>
											<li><a href="<?php echo esc_url('//pinterest.com/pin/create/button/?url');?>=<?php the_permalink();?>&media=<?php echo esc_url($iv_profile_pic_url); ?>&description=<?php the_title(); ?>"><i class="fab fa-pinterest-p"></i></a></li>
											<li><a href="<?php echo esc_url('//twitter.com/home?status');?>=<?php the_permalink(); ?>"><i class="fab fa-twitter"></i></a></li>
										</ul>
									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
	
</div>
<?php
	$currencyCode = get_option('epjbjobboard_api_currency');
	wp_enqueue_script('epmyaccount-script-27', wp_jobboard_URLPATH . 'admin/files/js/public-profile.js');
	wp_localize_script('epmyaccount-script-27', 'jobboard1', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'wp_iv_directories_URLPATH'		=> wp_jobboard_URLPATH,
	'current_user_id'	=>get_current_user_id(),
	'dirwpnonce'=> wp_create_nonce("myaccount"),
	"Please_login"=>  esc_html__('Please Login','jobboard'), 
	'Add_to_Boobmark'=>esc_html__('Add to Boobmark', 'jobboard' ),
	'Added_to_Boobmark'=>esc_html__('Added to Boobmark', 'jobboard' ),	
	
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
		'Please_put_your_message'=>esc_html__('Please put your name,email & Cover letter', 'jobboard' ),
		'contact'=> wp_create_nonce("contact"),
		'listing'=> wp_create_nonce("listing"),
		'cv'=> wp_create_nonce("Doc/CV/PDF"),
		'wp_jobboard_URLPATH'=>wp_jobboard_URLPATH,
		) );
	?>	
	
	
