<?php
	$dir_map_api=get_option('epjbdir_map_api');	
	if($dir_map_api==""){$dir_map_api='';}	
	$directory_url=get_option('epjbjobboard_url');					
	if($directory_url==""){$directory_url='job';}
	$map_api_have='no';
?>
<div class="profile-content">
	<div class="portlet light">		
		<h4 class="lighter-heading border-btm"><?php  esc_html_e('Add New Job','jobboard');?></h4>
		<div class="portlet-body">
			<div class="tab-content">
				
								
						<div class="row">
							<div class="col-md-12">	 
								<form action="" id="new_post" name="new_post"  method="POST" role="form">
									<div class=" form-group">
										<label for="text" class=" control-label"><?php  esc_html_e('Title','jobboard'); ?></label>
										<div class="  "> 
											<input type="text" class="form-control" name="title" id="title" value="" placeholder="<?php  esc_html_e('Enter Title Here','jobboard'); ?>">
										</div>																		
									</div>
									<div class=" form-group ">																
											<div class="col-md-3" id="post_image_div">				
											</div> 
											<input type="hidden" name="feature_image_id" id="feature_image_id" value="">
											<div class="col-md-3" id="post_image_edit">	
												<button type="button" onclick="edit_post_image('post_image_div');"  class="btn btn-xs green-haze"><?php  esc_html_e('Feature Image','jobboard'); ?> </button>
											</div>									
									</div>
									<div class="form-group">
										<label for="text" class="control-label"><?php  esc_html_e('Job Description','jobboard'); ?>  </label>
										<?php
											$settings_a = array(															
											'textarea_rows' =>8,
											'editor_class' => 'form-control'															 
											);
											$editor_id = 'new_post_content';
											wp_editor( '', $editor_id,$settings_a );										
										?>
									</div>
									<div class="form-group">
										<label for="text" class="control-label"><?php  esc_html_e('Education & Experience','jobboard'); ?>  </label>
										<?php
											$settings_a = array(															
											'textarea_rows' =>8,
											'editor_class' => 'form-control'															 
											);
											$editor_id = 'content_education';
											wp_editor( '', $editor_id,$settings_a );										
										?>
									</div>
									<div class="form-group">
										<label for="text" class="control-label"><?php  esc_html_e('Must Have','jobboard'); ?>  </label>
										<?php
											$settings_a = array(															
											'textarea_rows' =>8,
											'editor_class' => 'form-control'															 
											);
											$editor_id = 'content_must_have';
											wp_editor( '', $editor_id,$settings_a );										
										?>
									</div>							
									
									
									<div class="  form-group ">
										<label for="text" class="  control-label"><?php  esc_html_e('Status','jobboard'); ?>  </label>
										<select name="post_status" id="post_status"  class="form-control">
											<?php
													$user_can_publish=get_option('user_can_publish');	
													if($user_can_publish==""){$user_can_publish='yes';}	
													if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){?>
													<option value="publish"><?php esc_html_e('Publish','jobboard'); ?></option>
													<?php
													}
													if(isset($current_user->roles[0]) and $current_user->roles[0]!='administrator'){
														if($user_can_publish=="yes"){
														?>
														<option value="publish"><?php esc_html_e('Publish','jobboard'); ?></option>
														<?php
														}
													}
												?>											
											<option value="pending"><?php esc_html_e('Pending Review','jobboard'); ?></option>
											<option value="draft" ><?php esc_html_e('Draft','jobboard'); ?></option>	
										</select>	
									</div>										
									
									
									
									<span class="caption-subject">														
										<?php  esc_html_e('Contact Info','jobboard'); ?>
									</span>
									<hr/>
									<?php
									
										$listing_contact_source='';
										if($listing_contact_source==''){$listing_contact_source='user_info';}
									?>
									<div class=" form-group">	
										<div class="radio">											
											<label><input type="radio" name="contact_source" value="user_info"  <?php echo ($listing_contact_source=='user_info'?'checked':''); ?> > <?php  esc_html_e(' Use The company Info ->','jobboard'); ?> <?php echo ucfirst($current_user->display_name); ?><?php  esc_html_e(' : Logo, Email, Phone, Website','jobboard'); ?> <a href="<?php echo get_permalink().'?profile=setting';?>" target="_blank"> <?php  esc_html_e('Edit','jobboard'); ?> </a></label>
										</div>
										<div class="radio">
											<label><input type="radio" name="contact_source" value="new_value" <?php echo ($listing_contact_source=='new_value'?'checked':''); ?>><?php  esc_html_e(' New Contact Info','jobboard'); ?>  </label>
										</div>
									</div>
									<div  class="row" id="new_contact_div" <?php echo ($listing_contact_source=='user_info'?'style="display:none"':''); ?> >
										
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Company Name','jobboard'); ?></label>						
											<input type="text" class="form-control" name="company_name" id="company_name" value="" placeholder="<?php  esc_attr_e('Company name','jobboard'); ?>">
										</div>
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Phone','jobboard'); ?></label>						
											<input type="text" class="form-control" name="phone" id="phone" value="" placeholder="<?php  esc_attr_e('Enter Phone Number','jobboard'); ?>">
										</div>
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Address','jobboard'); ?></label>
											<input type="text" class="form-control" name="address" id="address" value="" placeholder="<?php  esc_attr_e('Enter Address','jobboard'); ?>">
										</div>	
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('city','jobboard'); ?></label>
											<input type="text" class="form-control" name="city" id="city" value="" placeholder="<?php  esc_attr_e('Enter city','jobboard'); ?>">
										</div>	
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('State','jobboard'); ?></label>
											<input type="text" class="form-control" name="state" id="state" value="" placeholder="<?php  esc_attr_e('Enter State','jobboard'); ?>">
										</div>	
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Zipcode/Postcode','jobboard'); ?></label>
											<input type="text" class="form-control" name="postcode" id="postcode" value="" placeholder="<?php  esc_attr_e('Enter Zipcode/Postcode','jobboard'); ?>">
										</div>	
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Country','jobboard'); ?></label>
											<input type="text" class="form-control" name="country" id="country" value="" placeholder="<?php  esc_attr_e('Enter Country','jobboard'); ?>">
										</div>	
										
										
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Email Address','jobboard'); ?></label>
											<input type="text" class="form-control" name="contact-email" id="contact-email" value="" placeholder="<?php  esc_attr_e('Enter Email Address','jobboard'); ?>">
										</div>
										<div class=" form-group col-md-6">
											<label for="text" class=" control-label"><?php  esc_html_e('Web Site','jobboard'); ?></label>
											<input type="text" class="form-control" name="contact_web" id="contact_web" value="" placeholder="<?php  esc_attr_e('Enter Web Site','jobboard'); ?>">
										</div>
									</div>	
									
									
									<hr/>
									<div class="clearfix"></div>
									<span class="caption-subject">												
										<?php  esc_html_e('Categories','jobboard'); ?>
									</span>
									<hr/>
									
										<div class=" form-group ">																	
											<?php $selected='';
												echo '<select name="postcats"   multiple="multiple" size="10" class="form-control epselectminheight"  >';
												echo '<option  value="">'.esc_html__('Choose a Category','jobboard').'</option>';
											
												if( isset($_POST['submit'])){
													$selected = sanitize_text_field($_POST['postcats']);
												}
												//job
												$taxonomy = $directory_url.'-category';
												$args = array(
												'orderby'           => 'name', 
												'order'             => 'ASC',
												'hide_empty'        => false, 
												'exclude'           => array(), 
												'exclude_tree'      => array(), 
												'include'           => array(),
												'number'            => '', 
												'fields'            => 'all', 
												'slug'              => '',
												'parent'            => '0',
												'hierarchical'      => true, 
												'child_of'          => 0,
												'childless'         => false,
												'get'               => '', 
												);
												$terms = get_terms($taxonomy,$args); // Get all terms of a taxonomy
												if ( $terms && !is_wp_error( $terms ) ) :
												$i=0;
												foreach ( $terms as $term_parent ) {  ?>												
												<?php  
													echo '<option  value="'.$term_parent->slug.'" '.($selected==$term_parent->slug?'selected':'' ).'><strong>'.$term_parent->name.'<strong></option>';
												?>	
												<?php
													$args2 = array(
													'type'                     => $directory_url,						
													'parent'                   => $term_parent->term_id,
													'orderby'                  => 'name',
													'order'                    => 'ASC',
													'hide_empty'               => 0,
													'hierarchical'             => 1,
													'exclude'                  => '',
													'include'                  => '',
													'number'                   => '',
													'taxonomy'                 => $directory_url.'-category',
													'pad_counts'               => false 
													); 											
													$categories = get_categories( $args2 );	
													if ( $categories && !is_wp_error( $categories ) ) :
													foreach ( $categories as $term ) { 
														echo '<option  value="'.$term->slug.'" '.($selected==$term->slug?'selected':'' ).'>--'.$term->name.'</option>';
													} 	
													endif;		
												?>
												<?php
													$i++;
												} 								
												endif;	
												echo '</select>';	
											?>		
										</div>
										
									<div class="clearfix"></div>
									<span class="caption-subject">												
										<?php  esc_html_e('Tags','jobboard'); ?>
									</span>
									<hr/>
									
									<div class=" row">		
									<?php
										$args2 = array(
										'type'                     => $directory_url,
										'orderby'                  => 'name',
										'order'                    => 'ASC',
										'hide_empty'               => 0,
										'hierarchical'             => 1,
										'exclude'                  => '',
										'include'                  => '',
										'number'                   => '',
										'taxonomy'                 => $directory_url.'_tag',
										'pad_counts'               => false
										);
										$main_tag = get_categories( $args2 );	
										$tags_all= '';													
										if ( $main_tag && !is_wp_error( $main_tag ) ) :
										foreach ( $main_tag as $term_m ) {
										?>
										<div class="col-md-6">
											<label class="form-group"> 
												<input type="checkbox" name="tag_arr[]" id="tag_arr[]"  value="<?php echo esc_attr($term_m->slug); ?>"> <?php echo esc_html($term_m->name); ?> </label>  
										</div>
										<?php	
										}
										endif;	
									?>
									</div>
									<div class=" form-group">	
											<input type="text" class="form-control" name="new_tag" id="new_tag" value="" placeholder="<?php  esc_html_e('Enter New Tags: Separate tags with commas','jobboard'); ?>">
									</div>															
									
										
									
									<span class="caption-subject">												
										<?php  esc_html_e('Job information','jobboard'); ?>
									</span>
									<hr/>	
									<div class="clearfix"></div>
									<div class="row">
									
									<div class="col-md-6  form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Educational Requirements','jobboard'); ?></label>
											<input type="text" class="form-control" name="educational_requirements" id="educational_requirements" value="" placeholder="<?php  esc_html_e('Bachelor Degree','jobboard'); ?>">								
										</div>
										
										<div class="  form-group col-md-6">
											<label for="text" class="  control-label"><?php  esc_html_e('Job Nature','jobboard'); ?></label>
											<select name="job_type" class="form-control ">		
												<?php
													$job_status='';
													$job_status_all=get_option('job_status');					
													if($job_status_all==""){$job_status_all='Full Time, Part Time,Freelance, Hourly, Project Base';}
													$job_status_all_arr= explode(',',$job_status_all);
													foreach($job_status_all_arr as $job_statusone){ 
														if(!empty($job_statusone)){
															echo' <option '. ($job_status ==$job_statusone ? 'selected':'' ).' value="'.trim(esc_attr($job_statusone)).'">'.esc_html__($job_statusone,'jobboard').'</option>';
														}
													}											
												?>	
											</select>									
										</div>
										<div class="  form-group col-md-6">
											<label for="text" class="  control-label"><?php  esc_html_e('Job Level','jobboard'); ?></label>
											<select name="job_level" class="form-control ">		
												<?php
													$job_level='';
													$job_level_all=get_option('job_level');					
													if($job_level_all==""){$job_level_all='Any,Entry Lavel,Mid Level,Top Level';}
													$job_level_arr= explode(',',$job_level_all);
													foreach($job_level_arr as $job_statusone){ 
														if(!empty($job_statusone)){
															echo' <option '. ($job_level ==$job_statusone ? 'selected':'' ).' value="'.trim(esc_attr($job_statusone)).'">'.esc_html__($job_statusone,'jobboard').'</option>';
														}
													}											
												?>	
											</select>									
										</div>
										<div class="  form-group col-md-6">
											<label for="text" class="  control-label"><?php  esc_html_e('Experience Range','jobboard'); ?></label>
											<select name="experience_range" class="form-control ">		
												<?php
													$experience_range='';
													$experience_range=get_option('experience_range');					
													if($experience_range==""){$experience_range='Any,Below 1 Year,1 - <3 Years,3 - <5 Years,5 - <10 Years,Over 10 Years';}
													$job_arr= explode(',',$experience_range);
													foreach($job_arr as $job_statusone){ 
														if(!empty($job_statusone)){
															echo' <option '. ($job_status ==$job_statusone ? 'selected':'' ).' value="'.trim($job_statusone).'">'.esc_html__($job_statusone,'jobboard').'</option>';
														}
													}											
												?>	
											</select>														
										</div>
										<div class="  form-group col-md-6">
											<label for="text" class="  control-label"><?php  esc_html_e('Age Range','jobboard'); ?></label>
											<select name="age_range" class="form-control ">		
												<?php
													$age_range='';
													$age_range=get_option('age_range');					
													if($age_range==""){$age_range=esc_html__('Any, Below 20 years, 20 - < 30 Years, 30 - < 40 Years,40 - < 50 Years,Over 50 Years','jobboard');}
													
													$job_arr= explode(',',$age_range);
													foreach($job_arr as $job_statusone){ 
														if(!empty($job_statusone)){
															echo' <option '. ($job_status ==$job_statusone ? 'selected':'' ).' value="'.trim(esc_attr($job_statusone)).'">'.esc_html__($job_statusone,'jobboard').'</option>';
														}
													}											
												?>	
											</select>														
										</div>
										<div class=" col-md-6 form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Gender','jobboard'); ?></label>
											<select name="gender"  class="form-control">
												<option value="Any"><?php  esc_html_e('Any','jobboard'); ?></option>
												<option value="Male"><?php  esc_html_e('Male','jobboard'); ?></option>
												<option value="Female"><?php  esc_html_e('Female','jobboard'); ?></option>
											</select>
										</div>
										<div class="col-md-6  form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Vacancy','jobboard'); ?></label>
											<input type="text" class="form-control" name="vacancy" id="vacancy" value="" placeholder="<?php  esc_html_e('Enter Vacancy, e.g : 2','jobboard'); ?>">								
										</div>
										<div class=" col-md-6 form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Application Deadline','jobboard'); ?></label>
											<input type="text" class="form-control" name="deadline" id="deadline" value="" >
										</div>	
										<div class=" col-md-6 form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Workplace','jobboard'); ?></label>
											<input type="text" class="form-control" name="workplace"  value=""  placeholder="<?php  esc_html_e('Office, Work from Home','jobboard'); ?>">
										</div>
										<div class="col-md-6  form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Offerd Salary','jobboard'); ?></label>
											<input type="text" class="form-control" name="salary" value="" >								
										</div>
										<div class="col-md-12  form-group">
											<label for="text" class=" control-label"><?php  esc_html_e('Compensation & Other Benefits','jobboard'); ?></label>
											<input type="text" class="form-control" name="other_benefits" value="" >								
										</div>
										
										
									</div>
									<span class="caption-subject">	
										<?php  esc_html_e('Videos ','jobboard'); ?>
									</span>
									
									<hr/>
								
										<div class="row">
											<div class=" col-md-6 form-group">
												<label for="text" class=" control-label"><?php  esc_html_e('Youtube','jobboard'); ?></label>
												<input type="text" class="form-control" name="youtube" id="youtube" value="" placeholder="<?php  esc_html_e('Enter Youtube video ID, e.g : bU1QPtOZQZU ','jobboard'); ?>">
											</div>
											<div class="col-md-6  form-group">
												<label for="text" class=" control-label"><?php  esc_html_e('vimeo','jobboard'); ?></label>
												<input type="text" class="form-control" name="vimeo" id="vimeo" value="" placeholder="<?php  esc_html_e('Enter vimeo ID, e.g : 134173961','jobboard'); ?>">								
											</div>
										</div>	
										
									<span class="caption-subject">											
										<?php  esc_html_e('Image Gallery','jobboard'); ?>
									</span>
									<hr/>
									<div class=" row form-group ">	
										<input type="hidden" name="gallery_image_ids" id="gallery_image_ids" value="">
										<div class="col-md-12" id="gallery_image_div">
										</div>									
									</div>
									<div class="row">										
										<div class="  form-group col-md-12">									
											<button type="button" onclick="edit_gallery_image('gallery_image_div');"  class="btn btn-xs green-haze"><?php  esc_html_e('Add Images','jobboard'); ?></button>
										</label>						
										</div>
									</div>
									
									<hr/>
									<span class="caption-subject">	
										<?php  esc_html_e('More details ','jobboard'); ?>
									</span>								
									<hr/>
									<div class="row">
										<?php							
											$default_fields = array();
											$field_set=get_option('jobboard_fields' );
											if($field_set!=""){ 
												$default_fields=get_option('jobboard_fields' );
												}else{															
												$default_fields['other_link']=esc_html__('Other Link','jobboard');
											}
											$i=1;							
											foreach ( $default_fields as $field_key => $field_value ) { ?>	
											<div class="form-group col-md-6">												
												<input type="text" placeholder="<?php   esc_html_e('Enter ', 'jobboard');?><?php echo esc_html($field_value);?>" name="<?php echo esc_html($field_key);?>" id="<?php echo esc_html($field_key);?>"  class="form-control" value=""/>
											</div>
											<?php
											}
										?>			
									</div>
									<span class="caption-subject">												
										<?php  esc_html_e('Button Setting','jobboard'); ?>
									</span>
									<hr/>
									<?php											
										$dirpro_web_button=get_option('dirpro_web_button');	
										if($dirpro_web_button==""){$dirpro_web_button='yes';}
										if($dirpro_web_button=="yes"){
											$dirpro_web_button='';
											if($dirpro_web_button==""){$dirpro_web_button='yes';}
										?>	
										<div class="form-group row ">
											<label  class="col-md-4 control-label"> <?php  esc_html_e('Web Button','jobboard');  ?></label>
											<div class="col-md-3">
												<label>												
													<input type="radio" name="dirpro_web_button" id="dirpro_web_button" value='yes' <?php echo ($dirpro_web_button=='yes' ? 'checked':'' ); ?> ><?php  esc_html_e('Show Web Button','jobboard');  ?>
												</label>	
											</div>
											<div class="col-md-5">	
												<label>											
													<input type="radio"  name="dirpro_web_button" id="dirpro_web_button" value='no' <?php echo ($dirpro_web_button=='no' ? 'checked':'' );  ?> > <?php  esc_html_e('Hide Web Button','jobboard');  ?>
												</label>
											</div>	
										</div>
										<?php
										}
										$dir_style5_email=get_option('dir_style5_email');	
										if($dir_style5_email==""){$dir_style5_email='yes';}
										if($dir_style5_email=="yes"){
											$dirpro_email_button='';
											if($dirpro_email_button==""){$dirpro_email_button='yes';}
										?>	
										<div class="form-group row ">
											<label  class="col-md-4 control-label"> <?php  esc_html_e('Email Button','jobboard');  ?></label>
											<div class="col-md-3">
												<label>												
													<input type="radio" name="dirpro_email_button" id="dirpro_email_button" value='yes' <?php echo ($dirpro_email_button=='yes' ? 'checked':'' ); ?> ><?php  esc_html_e('Show Email Button','jobboard');  ?>
												</label>	
											</div>
											<div class="col-md-5">	
												<label>											
													<input type="radio"  name="dirpro_email_button" id="dirpro_email_button" value='no' <?php echo ($dirpro_email_button=='no' ? 'checked':'' );  ?> > <?php  esc_html_e('Hide Email Button','jobboard');  ?>
												</label>
											</div>	
										</div>		
										<?php
										}	
										?>
										
									
									
									<div class="clearfix"></div>	
									<div class="row">
										<div class="col-md-12  "> <hr/>
											<div class="" id="update_message"></div>
											
											<button type="button" onclick="iv_save_post();"  class="btn green-haze"><?php  esc_html_e('Save Post',	'jobboard'); ?></button>
											
										</div>	
										
									</div>	
								</form>
							</div>
						</div>
						
				
			</div>
		</div>
	</div>
</div>
<!-- END PROFILE CONTENT -->
<?php
	$my_theme = wp_get_theme();
	$theme_name= strtolower($my_theme->get( 'Name' ));
	wp_enqueue_script('jobboard-ar-script-27', wp_jobboard_URLPATH . 'admin/files/js/add-edit-listing.js');
	wp_localize_script('jobboard-ar-script-27', 'realpro_data', array(
	'ajaxurl' 					=> admin_url( 'admin-ajax.php' ),
	'loading_image'			=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'current_user_id'		=>get_current_user_id(),
	'Set_Feature_Image'	=> esc_html__('Set Feature Image','jobboard'),
	'Set_plan_Image'		=> esc_html__('Set plan Image','jobboard'),
	'Set_Event_Image'		=> esc_html__('Set Event Image','jobboard'),
	'Gallery Images'		=> esc_html__('Gallery Images','jobboard'),
	'permalink'					=> get_permalink(),
	'dirwpnonce'				=> wp_create_nonce("addlisting"),
	'theme_name'				=> $theme_name,
	) );
?> 