<?php
	wp_enqueue_script("jquery");	
	wp_enqueue_style('wp-jobboard-public-111', wp_jobboard_URLPATH .'admin/files/css/iv-bootstrap.css');
	wp_enqueue_style('wp-jobboard-piblic-13', wp_jobboard_URLPATH . 'admin/files/css/profile-public.css');
	wp_enqueue_style('all-awesome', wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
	wp_enqueue_style('colorbox', wp_jobboard_URLPATH . 'admin/files/css/colorbox.css');
	wp_enqueue_script('colorbox', wp_jobboard_URLPATH . 'admin/files/js/jquery.colorbox-min.js');
	/**************************** css resources from qdesk ********************************************/
	wp_enqueue_style('main-css', wp_jobboard_URLPATH . 'admin/files/css/main.css');
	/******************************************************************************************************/
	$display_name='';
	$email='';
	$current_page_permalink='';
	$user_id=1;
	global $current_user;
	
	if(isset($_REQUEST['id'])){ 
		$author_name= sanitize_text_field($_REQUEST['id']);
		$user = get_user_by( 'ID', $author_name );
		if(isset($user->ID)){
			$user_id=$user->ID;
			$display_name=$user->display_name;
			$email=$user->user_email;
		}
	}else{
		 
		$user_id=$current_user->ID;
		$display_name=$current_user->display_name;
		$email=$current_user->user_email;
		$author_name= $current_user->ID;
		if($user_id==0){
			$user_id=1;
		}
	}
?>
<div class="bootstrap-wrapper wrapper" id="">
	<input type="hidden" id="profileID" value="<?php echo esc_attr($user_id); ?>">
	<main class=" pt-1">
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
								echo'<img src="'. wp_jobboard_URLPATH.'assets/images/default-user.png">';
							}
						?>
					</div>
					<div class="col-md-7">
						<div class="row align-items-lg-center">
							<div class="col-lg-7 col-xl-8 ">
								<h2 class="title-detail"><?php echo esc_html(get_user_meta($user_id,'full_name',true)); ?></h2>
								<?php
									if(get_user_meta($user_id,'qualification',true)!=''){
									?>
									<div class="date-job">
									<?php echo esc_html(get_user_meta($user_id,'qualification',true)); ?>
									</div>
								<?php
								}
							?>
							<div class="meta-job">
								<p>
									<?php
										if(get_user_meta($user_id,'phone',true)!=''){
										?>
										<span class="phone"><i class="fa fa-mobile-alt"></i><?php echo esc_html(get_user_meta($user_id,'phone',true)); ?></span>
										<?php
										}
									?>
									<?php
										if(get_user_meta($user_id,'whatsapp',true)!=''){
										?>
										<span class="phone"><i class="fa fa-phone"></i><?php echo esc_html(get_user_meta($user_id,'whatsapp',true)); ?></span>
										<?php
										}
									?>
									<?php
										if(!empty($email)){
										?>
										<span class="mail"><i class="far fa-envelope"></i><?php echo esc_html($email); ?></span>
										<?php
										}
									?>
								</p>
							</div>
						</div>
						
					</div>
				</div>
				<div class="col-md-3">
					<div class="btn-feature"><a class="btn btn-light-green" href="<?php echo get_permalink($current_page_permalink);?>?&jobboardpdfcv=<?php echo esc_attr($user_id);?>" target="_blank"><i class="fas fa-download"></i> <?php esc_html_e('PDF', 'jobboard'); ?></a>
						<?php						
							$current_ID = get_current_user_id();
							$favourites='no';
							if($current_ID>0){
								$my_favorite = get_post_meta($user_id,'jobboard_profilebookmark',true);								
								$all_users = explode(",", $my_favorite);
								if (in_array($current_ID, $all_users)) {
									$favourites='yes';
								}
							}
							$added_to_Boobmark=esc_html__('Added to Boobmark', 'jobboard');
							$add_to_Boobmark=esc_html__('Add to Boobmark', 'jobboard');
						?>
						<button id="candidatebookmark" class="btn <?php echo ($favourites=='yes'?'btn-added-favourites ':'btn-light btn-add-favourites' ); ?>"  title="<?php echo ($favourites=='yes'? $added_to_Boobmark: $add_to_Boobmark ); ?>" ><i class="far fa-star"></i></button>
						
						
					</div> 
				</div>                   
			</div>
			
			<div class="row">
				<div class="col-lg-8">
					<div class="content-main-right single-detail contentshowep">
						<div class="box-description">
							<h3><?php esc_html_e('Description', 'jobboard'); ?></h3>
							<?php 		$content= get_user_meta($user_id,'coverletter',true);
								$content = apply_filters('the_content', $content);
								$content = str_replace(']]>', ']]&gt;', $content);
								echo wpautop($content);
							?>
						</div>
						<?php
						if(get_user_meta($user_id,'educationtitle1',true)!=''){?>
						<hr>
						<div class="intro-profile">
							<h3 class="title-box"><?php esc_html_e('Education', 'jobboard'); ?> </h3>
							<div class="candidate-box">
								<?php					   
									for($i=0;$i<20;$i++){
										if(get_user_meta($user_id,'educationtitle'.$i,true)!=''){?>
										<div class="item-list">
											<div class="item-body">
											<p><span class="since"><?php
												echo esc_html(get_user_meta($user_id,'edustartdate'.$i,true)); ?> - <?php
											echo esc_html(get_user_meta($user_id,'eduenddate'.$i,true)); ?></span></p>
											<div class="item-title">
												<p><?php echo esc_html(get_user_meta($user_id,'educationtitle'.$i,true)); ?><span class="item-position">- <?php
												echo esc_html(get_user_meta($user_id,'institute'.$i,true)); ?></span></p>
											</div>
											<div class="item-text">
												<p>
													<?php
													echo wpautop(get_user_meta($user_id,'edudescription'.$i,true)); ?>
												</p>
												</div>
											</div>
										</div>
										<?php
										}
									}
								?>
							</div>
						</div>
						<?php
						}
						?>
						<?php
						if(get_user_meta($user_id,'experience_title1',true)!=''){?>
						<hr>
						<div class="intro-profile">
							<h3 class="title-box"><?php esc_html_e('Work & Experiance', 'jobboard'); ?></h3>
							<div class="candidate-box">
								<?php			
									for($i=0;$i<30;$i++){
										if(get_user_meta($user_id,'experience_title'.$i,true)!=''){?>
										<div class="item-list">										
											<div class="item-body">
											<p><span class="since"><?php
												echo esc_html(get_user_meta($user_id,'experience_start'.$i,true)); ?> - <?php
											echo esc_html(get_user_meta($user_id,'experience_end'.$i,true)); ?></span></p>
											<div class="item-title">
												<p><?php
													echo esc_html(get_user_meta($user_id,'experience_title'.$i,true)); ?><span class="item-position">- <?php
													echo esc_html(get_user_meta($user_id,'experience_company'.$i,true)); ?></span></p>
											</div>
											<div class="item-text">
												<p><?php
												echo esc_html(get_user_meta($user_id,'experience_description'.$i,true)); ?></p>
											</div>
											</div>
										</div>
										
										<?php
										}
									}	 
									
								?>
								
							</div>
						</div>
						<?php
						}
						?>
						<?php
						if(get_user_meta($user_id,'award_title1',true)!=''){?>
						<hr>
						<div class="intro-profile">
							<h3 class="title-box"><?php esc_html_e('Honors & Awards', 'jobboard'); ?></h3>
							<div class="candidate-box">
								<?php			
									for($i=0;$i<30;$i++){
										if(get_user_meta($user_id,'award_title'.$i,true)!=''){?>
										<div class="item-list">										
											<div class="item-body">
											<div class="item-title">
											<p>
											<?php
												echo esc_html(get_user_meta($user_id,'award_title'.$i,true)); ?></span>
											</p>
												<p><?php
													echo esc_html(get_user_meta($user_id,'award_year'.$i,true)); ?><span class="item-position"></span>
												</p>
											
												
											</div>
											<div class="item-text">
												<p><?php
												echo esc_textarea(get_user_meta($user_id,'award_description'.$i,true)); ?></p>
											</div>
											</div>
										</div>
										
										<?php
										}
									}	 
									
								?>
								
							</div>
						</div>
						<?php
						}
						?>
						
						
						<?php
						if(trim(get_user_meta($user_id,'professional_skills',true))!=''){?>
						<hr>
						<div class="intro-profile">
							<h3 class="title-box"><?php esc_html_e('Professional Skill', 'jobboard'); ?></h3>
							<div class="candidate-box">
								<div class="tags">
									<p>
										<?php
											$tags_user= get_user_meta($user_id,'professional_skills',true); 				
											$tags_user_arr=  array_filter( explode(",", $tags_user), 'strlen' );
											foreach ( $tags_user_arr as $tag ) {
											?>
											<a href="#"><?php echo esc_html($tag);?></a>
											<?php
											}
										?>
										
										
									</p>
								</div>
							</div>
						</div>
						<?php
						}
						?>
						<?php
						if(get_user_meta($user_id,'language0',true)!=''){?>
						<hr>
						<div class="intro-profile">
							<h3 class="title-box"><?php esc_html_e('Languages', 'jobboard'); ?></h3>
							<div class="candidate-box">									
								<p>
									<?php
										for($i=0;$i<5;$i++){	
										 if(get_user_meta($user_id,'language'.$i,true)!=''){
										?>
										<div class="row">
											<div class="col-lg-12">
												<div class="row">
													<div class="col-sm-6">
														<div class="form-group">
															<label><?php echo esc_html(get_user_meta($user_id,'language'.$i,true)); ?></label>
															
														</div>
													</div>
													<div class="col-sm-6">
														<div class="form-group">
															<label><?php echo esc_html(get_user_meta($user_id,'language_level'.$i,true));?></label>														
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php
										 }
										}
									?>													
								</p>
								
							</div>
						</div>						
						<?php
						}
						?>
					</div>
					
				</div>
				<div class="col-lg-4">
					<div class="sidebar-right contentshowep">
						<div class="sidebar-right-group">
							<div class="job-detail-summary">
								<h3 class="title-block"><?php esc_html_e('Personal Information', 'jobboard'); ?></h3>
								<ul>
								
										<?php
									$default_fields = array();
									$field_set=get_option('jobboard_profile_fields' );
									$all_empty='no';
									if($field_set!=""){
										$default_fields=get_option('jobboard_profile_fields' );
										}else{
										$default_fields['full_name']='Full Name';	
										$default_fields['phone']='Phone Number';
										$default_fields['mobile']='Mobile Number';
										$default_fields['address']='Address';
										$default_fields['city']='City';
										$default_fields['zipcode']='Zipcode';
										$default_fields['state']='State';
										$default_fields['country']='Country';										
										$default_fields['job_title']='Job title';									
										$default_fields['hourly_rate']='Hourly Rate';
										$default_fields['experience']='Experience';
										$default_fields['age']='Age';
										$default_fields['qualification']='Qualification';								
										$default_fields['gender']='Gender';	
										$default_fields['website']='Website Url';
										$default_fields['description']='About';
										$all_empty='yes';
									}
									$field_type_roles=  	get_option( 'jobboard_field_type_roles' );			
									$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );
									
									$not_show= array('description');
									
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
									<?php esc_html_e('Make an Offer', 'jobboard'); ?></a>
								</center>
							</div>
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
	
	?>	