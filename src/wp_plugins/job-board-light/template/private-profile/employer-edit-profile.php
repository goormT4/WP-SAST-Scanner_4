<div class="upload-avatar">
	<div class="avatar" id="profile_image_main">
		<?php
			$iv_profile_pic_url=get_user_meta($current_user->ID, 'iv_profile_pic_thum',true);
			if($iv_profile_pic_url!=''){ ?>
			<img src="<?php echo esc_url($iv_profile_pic_url); ?>">
			<?php
				}else{
				echo'	 <img src="'. wp_jobboard_URLPATH.'assets/images/company-enterprise.png">';
			}
		?>
	</div>

	<div class="upload">
		<div class="btn-upload">
			<button type="button" onclick="edit_profile_image('profile_image_main');"  class="btn green-haze btn-sm">
			<?php esc_html_e('Change Image','jobboard'); ?> </button>
		</div>
	</div>
</div>
<div class="row"> 
	<div class="col-md-12">
		<div class="form-group">
			<label class=""><?php esc_html_e('Industry', 'jobboard'); ?></label>
			<select name="company_type" id="company_type" class="form-control ">								
				<?php
					$argscat = array(
					'type'                     => $directory_url,									
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
					$categories = get_categories( $argscat );
					$category_input_array= array();
					if(isset($_REQUEST['category_input'])){						
						$category_input_array = array_map( 'sanitize_text_field', $_REQUEST['category_input'] );
					}						
					if ( $categories && !is_wp_error( $categories ) ) :
					foreach ( $categories as $term ) {
						if(trim($term->name)!=''){	
							$selected='';
							if( get_user_meta($current_user->ID,'company_type',true)==$term->name){ 
								$selected='selected';
							}
						?>
						<option value="<?php echo esc_attr($term->name);?>" <?php echo esc_html($selected); ?> ><?php echo esc_html($term->name);?></option>					
						<?php
						}
					}
					endif;
				?>
			</select>				
		</div>
	</div>
	<?php
		$default_fields = array();
		$field_set=get_option('jobboard_profile_fields' );
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
		}
		$field_type_opt=  get_option( 'jobboard_field_type' );
		if($field_type_opt!=''){
			$field_type=get_option('jobboard_field_type' );
			}else{
			$field_type= array();
			$field_type['full_name']='text';								
			$field_type['company_since']='datepicker';
			$field_type['team_size']='text';									
			$field_type['phone']='text';
			$field_type['mobile']='text';
			$field_type['address']='text';
			$field_type['city']='text';
			$field_type['zipcode']='text';
			$field_type['state']='text';
			$field_type['country']='text';										
			$field_type['job_title']='text';									
			$field_type['hourly_rate']='text';
			$field_type['experience']='textarea';
			$field_type['age']='text';
			$field_type['qualification']='text';								
			$field_type['gender']='radio';	
			$field_type['website']='url';
			$field_type['description']='textarea';			
		}
		
		$field_type_roles=  	get_option( 'jobboard_field_type_roles' );			
		$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );							
		$user = new WP_User( $current_user->ID );
		$i=1;
		foreach ( $default_fields as $field_key => $field_value ) { 		
			if(isset($myaccount_fields_array[$field_key])){ 
				if($myaccount_fields_array[$field_key]=='yes'){
					$role_access='no';
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
					if($role_access=='yes'){
						echo  $main_class->jobboard_check_field_input_access($field_key, $field_value, 'myaccount', $current_user->ID );
					}
				}
			}else{ 
				echo  $main_class->jobboard_check_field_input_access($field_key, $field_value, 'myaccount', $current_user->ID );
			}
		}
	?>
</div>
<div class="margin-top-10">
	<div class="" id="update_message"></div>
	<button type="button" onclick="update_profile_setting();"  class="btn green-haze"><?php   esc_html_e('Save Changes','jobboard');?></button>
</div>