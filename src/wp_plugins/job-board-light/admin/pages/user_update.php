<?php
	global $wpdb,$wp_roles;
	$user_id='';
	if(isset($_GET['id'])){ $user_id=sanitize_text_field($_GET['id']);}
	$user = new WP_User( $user_id );
	$main_class = new wp_jobboard;
?>
<style>
.user_info .form-control{
	height: 40px;
}
</style>
<div class="bootstrap-wrapper">
	<div class="dashboard-eplugin container-fluid">				
		<div class="row">
			<div class="col-md-12"><h3 class=""><?php esc_html_e( 'User Settings: Edit', 'jobboard' );?> </h3>
			</div>	
		</div> 
		<div class="col-md-7 panel panel-info">
			<div class="panel-body">				
				<form id="user_form_iv" name="user_form_iv" class="form-horizontal" role="form" onsubmit="return false;">				
					<div class="form-group">
						<label for="text" class="col-md-3 control-label"></label>
						<div id="iv-loading"></div>
					</div>	
					<div class="form-group">
						<label for="inputEmail3" class="col-md-4 control-label"><?php esc_html_e( 'User Name', 'jobboard' );?></label>
						<div class="col-md-8">
							<label for="inputEmail3" class="control-label"><?php echo esc_html($user->user_login); ?></label>
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-md-4 control-label"><?php esc_html_e( 'Email Address', 'jobboard' );?></label>
						<div class="col-md-8">									
							<label for="inputEmail3" class="control-label"><?php echo esc_html($user->user_email); ?></label>
						</div>
					</div>								 
					<div class="form-group">
						<label for="text" class="col-md-4 control-label"><?php esc_html_e( 'User Role', 'jobboard' );?></label>
						<div class="col-md-8">
							<?php
								$user_role= '';
								if(isset($user->roles[0])){
									$user_role= $user->roles[0];
									}else{
									if(isset($user->roles[1])){
										$user_role= $user->roles[1];
									}
								}
							?>
							<select name="user_role"  class="form-control">
								<?php											
									foreach ( $wp_roles->roles as $key=>$value ){															
										echo'<option value="'.$key.'"  '.($user_role==$key? " selected" : " ") .' >'.esc_html($key).'</option>';	
									}
								?>	
							</select>								
						</div>
					</div> 
					<div class="form-group">
						<label for="text" class="col-md-4 control-label"><?php esc_html_e( 'User Package', 'jobboard' );?></label>
						<div class="col-md-8">									
							<?php
								$post_type='jobboard_pack';
								$membership_pack = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = %s ", $post_type ));	
								$total_package=count($membership_pack);
								if($membership_pack>0){
									$i=0; $current_package_id=get_user_meta($user_id,'jobboard_package_id',true);
									echo'<select name="package_sel"  class=" form-control">'; ?>
									<option value="" ><?php esc_html_e( 'Select Package', 'jobboard' );?></option>
									<?php
									foreach ( $membership_pack as $row )
									{
										if($current_package_id==$row->ID){
											echo '<option value="'. esc_attr($row->ID).'" selected>'. esc_html($row->post_title). esc_html__( '[User Current Package]', 'jobboard' ).' </option>';
											}else{
											echo '<option value="'. esc_attr($row->ID).'" >'. esc_html($row->post_title).'</option>';
										}
										$i++;
									}
									echo '</select>';
								}
							?>
						</div>
					</div> 							  
					<div class="form-group">
						<label for="text" class="col-md-4 control-label"><?php esc_html_e( 'Payment Status', 'jobboard' );?></label>
						<div class="col-md-8">
							<?php
								$payment_status= get_user_meta($user_id, 'jobboard_payment_status', true);
							?>
							<select name="payment_status" id ="payment_status" class="form-control">
								<option value="success" <?php echo ($payment_status == 'success' ? 'selected' : '') ?>><?php esc_html_e( 'Success', 'jobboard' );?></option>
								<option value="pending" <?php echo ($payment_status == 'pending' ? 'selected' : '') ?>><?php esc_html_e( 'Pendinge', 'jobboard' );?></option>
							</select>	
						</div>
					</div>
					<div class="form-group">
						<label for="text" class="col-md-4 control-label"><?php esc_html_e( 'User Type', 'jobboard' );?></label>
						<div class="col-md-8">
							<?php
								$user_type= get_user_meta($user_id, 'user_type', true);
							?>
							<select name="user_type" id ="user_type" class="form-control">
								<option value="employer" <?php echo ($user_type == 'employer' ? 'selected' : '') ?>><?php esc_html_e( 'Employer', 'jobboard' );?></option>
								<option value="candidate" <?php echo ($user_type == 'candidate' ? 'selected' : '') ?>><?php esc_html_e( 'Candidate', 'jobboard' );?></option>
							</select>	
						</div>
					</div>
					<div class="form-group">
						<label for="inputEmail3" class="col-md-4 control-label"><?php esc_html_e( 'Expiry Date', 'jobboard' );?></label>
						<div class="col-md-8">
							<?php
								$exp_date= get_user_meta($user_id, 'jobboard_exprie_date', true);
							?>
							<input type="text"  name="exp_date"  readonly   id="exp_date" class="form-control ctrl-textbox"  value="<?php echo esc_attr($exp_date); ?>" placeholder="">
						</div>
					</div>
					<div class="form-group ">
						<label for="" class="col-md-4 control-label"></label>
							<div class="col-md-8">
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
						$user = new WP_User( $user_id);
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
										echo  $main_class->jobboard_check_field_input_access($field_key, $field_value, 'myaccount', $user_id );
									}
								}
							}else{ 
								echo  $main_class->jobboard_check_field_input_access($field_key, $field_value, 'myaccount', $user_id);
							}
						}
					?>	
						</div>
					</div>
					<input type="hidden"  name="user_id"     id="user_id"   value="<?php echo esc_attr($user_id); ?>" >
					<div class="row">					
						<div class="col-md-12">	
							<label for="" class="col-md-4 control-label"></label>
							<div class="col-md-8">
							<button class="btn btn-info " onclick="return update_user_setting();"><?php esc_html_e( 'Update User', 'jobboard' );?></button></div>
							<p>&nbsp;</p>
						</div>
					</div>
				</div>								
			</form>		
		</div>			
	</div>
</div>