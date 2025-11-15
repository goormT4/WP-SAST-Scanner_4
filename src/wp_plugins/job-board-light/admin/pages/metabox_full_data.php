<?php	
	global $wpdb, $post;
	global $current_user;	
	wp_enqueue_style('bootstrap-myaccount-style-11', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	wp_enqueue_script('bootstrap-js-script-12', wp_jobboard_URLPATH . 'admin/files/js/bootstrap.min.js');
	$directory_url=get_option('epjbjobboard_url');					
	if($directory_url==""){$directory_url='job';}
	$curr_post_id=$post->ID;
	wp_enqueue_style('wp_jobboard-my-account-css', wp_jobboard_URLPATH . 'admin/files/css/my-account.css');
?>		
<div class="bootstrap-wrapper">
	<div class="content">
		<?php	
			$current_post = $curr_post_id;
			$post_edit = get_post($curr_post_id); 
		?>					
		<div class="row">
			<div class="col-md-12">	 
				<div class="form-group">
					<label for="text" class="control-label"><?php  esc_html_e('Education & Experience','jobboard'); ?>  </label>
					<?php
						$content=get_post_meta($post_edit->ID,'job_education', true);
						$settings_a = array(															
						'textarea_rows' =>8,
						'editor_class' => 'form-control'															 
						);
						$editor_id = 'content_education';
						wp_editor( $content, $editor_id,$settings_a );										
					?>
				</div>
				<div class="form-group">
					<label for="text" class="control-label"><?php  esc_html_e('Must Have','jobboard'); ?>  </label>
					<?php
						$content=get_post_meta($post_edit->ID,'job_must_have', true);
						$settings_a = array(															
						'textarea_rows' =>8,
						'editor_class' => 'form-control'															 
						);
						$editor_id = 'content_must_have';
						wp_editor( $content, $editor_id,$settings_a );										
					?>
				</div>							
				<h4>														
					<?php  esc_html_e('Contact Info','jobboard'); ?>
				</h4>
				<hr/>
				<?php
					$listing_contact_source=get_post_meta($post_edit->ID,'listing_contact_source',true);
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
						<div class="col-md-3" id="post_image_div">	
							<?php $feature_image = wp_get_attachment_image_src( get_post_thumbnail_id( $curr_post_id ), 'thumbnail' );
								if(isset($feature_image[0])){ ?>
								<img title="profile image" class=" img-responsive" src="<?php  echo esc_url($feature_image[0]); ?>">
								<?php
								}else{ ?>
								<a href="javascript:void(0);" onclick="edit_post_image('post_image_div');"  >
									<?php  echo '<img src="'. wp_jobboard_URLPATH.'assets/images/image-add-icon.png">'; ?>
								</a>
								<?php
								}
								$feature_image_id=get_post_thumbnail_id( $curr_post_id );
							?>
						</div> 
						<input type="hidden" name="feature_image_id" id="feature_image_id" value="<?php echo esc_attr($feature_image_id); ?>">
						<div class="col-md-3" id="post_image_edit">	
							<button type="button" onclick="edit_post_image('post_image_div');"  class="btn btn-xs green-haze"><?php  esc_html_e('Add/Edit Company Logo','jobboard'); ?> </button>
						</div>									
					</div>										
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Company Name','jobboard'); ?></label>						
						<input type="text" class="form-control" name="company_name" id="company_name" <?php echo esc_attr(get_post_meta($post_edit->ID,'company_name',true)); ?> placeholder="<?php  esc_attr_e('Company name','jobboard'); ?>">
					</div>
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Phone','jobboard'); ?></label>						
						<input type="text" class="form-control" name="phone" id="phone" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'phone',true)); ?>" placeholder="<?php  esc_attr_e('Enter Phone Number','jobboard'); ?>">
					</div>
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Address','jobboard'); ?></label>
						<input type="text" class="form-control" name="address" id="address" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'address',true)); ?>"  placeholder="<?php  esc_attr_e('Enter Address','jobboard'); ?>">
					</div>	
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Email Address','jobboard'); ?></label>
						<input type="text" class="form-control" name="contact-email" id="contact-email" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'contact-email',true)); ?>" placeholder="<?php  esc_html_e('Enter Email Address','jobboard'); ?>">
					</div>
					<div class=" form-group col-md-6">
					<label for="text" class=" control-label"><?php  esc_html_e('city','jobboard'); ?></label>
						<input type="text" class="form-control" name="city" id="city" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'city',true)); ?>" placeholder="<?php  esc_attr_e('Enter city','jobboard'); ?>">
					</div>	
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('State','jobboard'); ?></label>
						<input type="text" class="form-control" name="state" id="state" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'state',true)); ?>" placeholder="<?php  esc_attr_e('Enter State','jobboard'); ?>">
					</div>	
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Zipcode/Postcode','jobboard'); ?></label>
						<input type="text" class="form-control" name="postcode" id="postcode" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'postcode',true)); ?>" placeholder="<?php  esc_attr_e('Enter Zipcode/Postcode','jobboard'); ?>">
					</div>	
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Country','jobboard'); ?></label>
						<input type="text" class="form-control" name="country" id="country" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'country',true)); ?>" placeholder="<?php  esc_attr_e('Enter Country','jobboard'); ?>">
					</div>	
					<div class=" form-group col-md-6">
						<label for="text" class=" control-label"><?php  esc_html_e('Web Site','jobboard'); ?></label>
						<input type="text" class="form-control" name="contact_web" id="contact_web" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'contact_web',true)); ?>"  placeholder="<?php  esc_attr_e('Enter Web Site','jobboard'); ?>">
					</div>
				</div>	
				<hr/>
				<h4>												
					<?php  esc_html_e('Job information','jobboard'); ?>
				</h4>
				<hr/>	
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-md-6  form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Educational Requirements','jobboard'); ?></label>
						<input type="text" class="form-control" name="educational_requirements" id="educational_requirements" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'educational_requirements',true)); ?>" placeholder="<?php  esc_attr_e('Bachelor Degree','jobboard'); ?>">								
					</div>
					<div class="  form-group col-md-6">
						<label for="text" class="  control-label"><?php  esc_attr_e('Job Nature','jobboard'); ?></label>
						<select name="job_type" class="form-control ">		
							<?php
								$job_status=get_post_meta($post_edit->ID,'job_type',true);
								$job_status_all=get_option('job_status');					
								if($job_status_all==""){$job_status_all='Full Time, Part Time,Freelance, Hourly, Project Base';}
								$job_status_all_arr= explode(',',$job_status_all);
								foreach($job_status_all_arr as $job_statusone){ 
									if(!empty($job_statusone)){
										echo' <option '. ($job_status ==$job_statusone ? 'selected':'' ).' value="'.trim($job_statusone).'">'.esc_html__($job_statusone,'jobboard').'</option>';
									}
								}											
							?>	
						</select>									
					</div>
					<div class="  form-group col-md-6">
						<label for="text" class="  control-label"><?php  esc_html_e('Job Level','jobboard'); ?></label>
						<select name="job_level" class="form-control ">		
							<?php
								$job_level=trim(get_post_meta($post_edit->ID,'job_level',true));
								$job_level_all=get_option('job_level');					
								if($job_level_all==""){$job_level_all='Any, Entry Lavel, Mid Level, Top Level';}
								$job_level_arr= explode(',',$job_level_all);
								foreach($job_level_arr as $job_statusone){ 
									if(!empty($job_statusone)){													
										echo' <option '. (trim($job_level) ==trim($job_statusone) ? 'selected':'' ).' value="'.trim(esc_attr($job_statusone)).'">'.esc_html__($job_statusone,'jobboard').'</option>';
									}
								}											
							?>	
						</select>									
					</div>
					<div class="  form-group col-md-6">
						<label for="text" class="  control-label"><?php  esc_html_e('Experience Range','jobboard'); ?></label>
						<select name="experience_range" class="form-control ">		
							<?php
								$experience_range_select=trim(get_post_meta($post_edit->ID,'experience_range',true));
								$experience_range=get_option('experience_range');					
								if($experience_range==""){$experience_range='Any,Below 1 Year,1 - <3 Years,3 - <5 Years,5 - <10 Years,Over 10 Years';}
								$job_arr= array_filter(explode(',',$experience_range));
								foreach($job_arr as $job_1){ 
									if(!empty($job_1)){
										echo'<option '.(trim($experience_range_select)==trim($job_1) ? ' selected':'' ).' value="'.trim(esc_attr($job_1)).'">'.$job_1.'</option>';
									}
								}											
							?>	
						</select>														
					</div>
					<div class="  form-group col-md-6">
						<label for="text" class="  control-label"><?php  esc_html_e('Age Range','jobboard'); ?></label>
						<select name="age_range" class="form-control ">		
							<?php
								$age_range_select=trim(get_post_meta($post_edit->ID,'age_range',true));
								$age_range=get_option('age_range');					
								if($age_range==""){$age_range=esc_html__('Any, Below 20 years, 20 - < 30 Years, 30 - < 40 Years,40 - < 50 Years,Over 50 Years','jobboard');}
								$job_arr= explode(',',$age_range);
								foreach($job_arr as $job_statusone){ 
									if(!empty($job_statusone)){
										echo' <option '. ($age_range_select ==$job_statusone ? 'selected':'' ).' value="'.trim(esc_attr($job_statusone)).'">'.$job_statusone.'</option>';
									}
								}											
							?>	
						</select>														
					</div>
					<div class=" col-md-6 form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Gender','jobboard'); ?></label>
						<select name="gender"  class="form-control">
							<option value="Any" <?php echo ( get_post_meta($post_edit->ID,'gender',true)=='Any'? ' selected':''); ?> ><?php  esc_html_e('Any','jobboard'); ?></option>
							<option value="Male" <?php echo ( get_post_meta($post_edit->ID,'gender',true)=='Male'? ' selected':''); ?> ><?php  esc_html_e('Male','jobboard'); ?></option>
							<option value="Female" <?php echo ( get_post_meta($post_edit->ID,'gender',true)=='Female'? ' selected':''); ?>><?php  esc_html_e('Female','jobboard'); ?></option>
						</select>
					</div>
					<div class="col-md-6  form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Vacancy','jobboard'); ?></label>
						<input type="text" class="form-control" name="vacancy" id="vacancy" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'vacancy',true));?>" placeholder="<?php  esc_html_e('Enter Vacancy, e.g : 2','jobboard'); ?>">								
					</div>
					<div class=" col-md-6 form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Application Deadline','jobboard'); ?></label>
						<input type="text" class="form-control" name="deadline" id="deadline" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'deadline',true));?>" >
					</div>	
					<div class=" col-md-6 form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Workplace','jobboard'); ?></label>
						<input type="text" class="form-control" name="workplace"  value="<?php echo esc_attr(get_post_meta($post_edit->ID,'workplace',true));?>"  placeholder="<?php  esc_html_e('Office, Work from Home','jobboard'); ?>">
					</div>
					<div class="col-md-6  form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Offerd Salary','jobboard'); ?></label>
						<input type="text" class="form-control" name="salary" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'salary',true));?>">								
					</div>
					<div class="col-md-12  form-group">
						<label for="text" class=" control-label"><?php  esc_html_e('Compensation & Other Benefits','jobboard'); ?></label>
						<input type="text" class="form-control" name="other_benefits" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'other_benefits',true));?>">								
					</div>
				</div>
				<h4 >	
					<?php  esc_html_e('Videos ','jobboard'); ?>
				</h4>
				<hr/>
				<?php
					// video, event , coupon , vip_badge
					if($this->check_write_access('video')){
					?>	
					<div class="row">
						<div class=" col-md-6 form-group">
							<label for="text" class=" control-label"><?php  esc_html_e('Youtube','jobboard'); ?></label>
							<input type="text" class="form-control" name="youtube" id="youtube" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'youtube',true));?>" placeholder="<?php  esc_html_e('Enter Youtube video ID, e.g : bU1QPtOZQZU ','jobboard'); ?>">
						</div>
						<div class="col-md-6  form-group">
							<label for="text" class=" control-label"><?php  esc_html_e('vimeo','jobboard'); ?></label>
							<input type="text" class="form-control" name="vimeo" id="vimeo" value="<?php echo esc_attr(get_post_meta($post_edit->ID,'vimeo',true));?>" placeholder="<?php  esc_html_e('Enter vimeo ID, e.g : 134173961','jobboard'); ?>">								
						</div>
					</div>	
					<?php
						}else{
						esc_html_e('Please upgrade your account to add video ','jobboard');
					}
				?>
				<h4>											
					<?php  esc_html_e('Image Gallery','jobboard'); ?>
				</h4>
				<hr/>
				<div class=" row form-group ">	
					<input type="hidden" name="gallery_image_ids" id="gallery_image_ids" value="">
					<div class="col-md-12" id="gallery_image_div">
					</div>									
				</div>
				<div class="row">										
					<div class="  form-group col-md-12">	
						<?php
							$gallery_ids=get_post_meta($curr_post_id ,'image_gallery_ids',true);
							$gallery_ids_array = array_filter(explode(",", $gallery_ids));
						?>
						<input type="hidden" name="gallery_image_ids" id="gallery_image_ids" value="<?php echo esc_attr($gallery_ids); ?>">
						<div class="row" id="gallery_image_div">
							<?php
								if(sizeof($gallery_ids_array)>0){
									foreach($gallery_ids_array as $slide){
									?>
									<div id="gallery_image_div<?php echo esc_html($slide);?>" class="col-md-2"><img  class="img-responsive"  src="<?php echo wp_get_attachment_url( $slide ); ?>"><button type="button" onclick="remove_gallery_image('gallery_image_div<?php echo esc_html($slide);?>', <?php echo esc_html($slide);?>);"  class="btn btn-sm btn-danger"><?php esc_html_e('X','jobboard'); ?> </button> </div>
									<?php
									}
								}
							?>
						</div>
						<button type="button" onclick="edit_gallery_image('gallery_image_div');"  class="btn btn-xs green-haze"><?php  esc_html_e('Add Images','jobboard'); ?></button>
					</label>						
				</div>
			</div>
			<hr/>
			<h4>	
				<?php  esc_html_e('More details ','jobboard'); ?>
			</h4>								
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
						<input type="text" placeholder="<?php   esc_html_e('Enter ', 'jobboard');?><?php echo esc_html($field_value);?>" name="<?php echo esc_html($field_key);?>" id="<?php echo esc_html($field_key);?>"  class="form-control" value="<?php echo esc_attr(get_post_meta($post_edit->ID,$field_key,true)); ?>"/>
					</div>
					<?php
					}
				?>			
			</div>
			<h4>												
				<?php  esc_html_e('Button Setting','jobboard'); ?>
			</h4>
			<hr/>
			<?php											
				$dirpro_web_button=get_option('dirpro_web_button');	
				if($dirpro_web_button==""){$dirpro_web_button='yes';}
				if($dirpro_web_button=="yes"){
					$dirpro_web_button=get_post_meta($post_edit->ID,'dirpro_web_button',true);
					if($dirpro_web_button==""){$dirpro_web_button='yes';}
				?>	
				<div class="form-group row ">
					<label  class="col-md-4 control-label"> <?php  esc_html_e('Web Button','jobboard');  ?></label>
					<div class="col-md-3">
						<label>												
							<input type="radio" name="dirpro_web_button" id="dirpro_web_button" value='yes' <?php echo ($dirpro_web_button=='yes' ? 'checked':'' ); ?> > <?php  esc_html_e('Show Web Button','jobboard');  ?>
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
					$dirpro_email_button=get_post_meta($post_edit->ID,'dirpro_email_button',true);
					if($dirpro_email_button==""){$dirpro_email_button='yes';}
				?>	
				<div class="form-group row ">
					<label  class="col-md-4 control-label"> <?php  esc_html_e('Email Button','jobboard');  ?></label>
					<div class="col-md-3">
						<label>												
							<input type="radio" name="dirpro_email_button" id="dirpro_email_button" value='yes' <?php echo ($dirpro_email_button=='yes' ? 'checked':'' ); ?> > <?php  esc_html_e('Show Email Button','jobboard');  ?>
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
		</div>
	</div>
	<input type="hidden" name="listing_data_submit" id="listing_data_submit" value="yes">
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
	'Set_plan_Image'		=> esc_html__('Set Image ','jobboard'),
	'Set_Event_Image'		=> esc_html__(' Set Image ','jobboard'),
	'Gallery Images'		=> esc_html__('Gallery Images','jobboard'),
	'permalink'					=> get_permalink(),
	'dirwpnonce'				=> wp_create_nonce("addlisting"),
	'theme_name'				=> $theme_name,
	) );
	?> 			