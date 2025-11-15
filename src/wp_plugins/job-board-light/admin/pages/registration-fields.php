<?php
	$ii=1;
	global $wp_roles;
	
	wp_enqueue_style('dataTables', wp_jobboard_URLPATH . 'admin/files/css/jquery.dataTables.css');
	wp_enqueue_script('dataTables', wp_jobboard_URLPATH . 'admin/files/js/jquery.dataTables.js');
?> 

<div class="bootstrap-wrapper">
	<div class="dashboard-eplugin container-fluid">
		
		<?php
		include('footer.php');
		?>
		<div class="row">
			<div class="col-md-12"><h3 class="page-header"><?php esc_html_e('Update Profile Setting','jobboard');?><br /><small> &nbsp;</small> </h3>
			</div>
			<div class="row">
			<div class="col-xs-12" id="submit-button-holder">
				<div class="pull-right"><button class="btn btn-info btn-lg" onclick="return update_profile_fields();"><?php esc_html_e('Update','jobboard');?></button>
				</div>
			</div>
		</div>
		</div>
		<form id="profile_fields" name="profile_fields" class="form-horizontal" role="form" onsubmit="return false;">
			
	
		<div class="panel panel-info">
				<div class="panel-heading"><h4><?php esc_html_e('Registration / User Profile Fields','jobboard');?></h4></div>
				<div class="panel-body">
					<table id="all_fieldsdatatable" name="all_fieldsdatatable"  class="display table" width="100%">					
						<thead>
							<tr>
								<th> <?php  esc_html_e('Input Name','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Label','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Type','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Value','jobboard')	;?> <br/>
									<?php  esc_html_e('[Dropdown,checkbox & Radio Button]','jobboard')	;?>
									</th>
								<th> <?php  esc_html_e('User Role','jobboard')	;?> <br/>
								<?php  esc_html_e('[Show on My Account/Profile]','jobboard')	;?> 	
									</th>						
								<th> <?php  esc_html_e('Registration','jobboard')	;?></th>
								<th> <?php  esc_html_e('My Account / Profile','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Require','jobboard')	;?> </th>
								<th><?php  esc_html_e('Action','jobboard')	;?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<?php  esc_html_e('User Profile Pic Uploader','jobboard');
											$jobboard_signup_profile_pic=get_option('jobboard_signup_profile_pic');
											if($jobboard_signup_profile_pic=='' ){ $jobboard_signup_profile_pic='yes';	}		
											?>
									</td>
									<td> </td>
									<td> </td>
									<td> </td>
									<td> </td>
									<td> <label>
											<input type="checkbox" name="signup_profile_pic" id="signup_profile_pic" value="yes" <?php echo($jobboard_signup_profile_pic=='yes'? 'checked':'' );?> >
										</label></td>
									<td> </td>
									<td> </td>
									<td> </td>
							</tr>	
							
							<tr  >
									<td>
										<?php  esc_html_e('Terms CheckBox','jobboard')	;
											$jobboard_payment_terms=get_option('jobboard_payment_terms');
											if($jobboard_payment_terms=='' ){ $jobboard_payment_terms='yes';	}
											?>
									</td>
									<td> </td>
									<td> </td>
									<td> </td>
									<td> </td>
									<td> <label>
											<input type="checkbox" name="jobboard_payment_terms" id="jobboard_payment_terms" value="yes" <?php echo($jobboard_payment_terms=='yes'? 'checked':'' );?> >
										</label></td>
									<td> </td>
									<td> </td>
									<td> </td>
							</tr>	
							<tr  >
									<td>
										<?php  esc_html_e('Coupon Buton','jobboard')	;
											$jobboard_payment_coupon=get_option('_jobboard_payment_coupon');
											if($jobboard_payment_coupon=='' ){ $jobboard_payment_coupon='yes';	}
											?>
									</td>
									<td> </td>
									<td> </td>
									<td> </td>
									<td> </td>
									<td> <label>
											<input type="checkbox" name="jobboard_payment_coupon" id="jobboard_payment_coupon" value="yes" <?php echo($jobboard_payment_coupon=='yes'? 'checked':'' );?> >
										</label></td>
									<td> </td>
									<td> </td>
									<td> </td>
							</tr>	
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
								}
								$i=0;								
								
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
														

								$field_type_value= get_option( 'jobboard_field_type_value' );
								if($field_type_value==''){
									$field_type_value=array();
									$field_type_value['gender']=esc_html__('Female,Male,Other', 'jobboard');	
								}
								
								$field_type_roles=  	get_option( 'jobboard_field_type_roles' );
								$sign_up_array=  get_option( 'jobboard_signup_fields' );
								$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );
								$require_array=  get_option( 'jobboard_signup_require' );								
								
								
								
								foreach ( $default_fields as $field_key => $field_value ) {
									$sign_up='';									
									if(isset($sign_up_array[$field_key]) && $sign_up_array[$field_key] == 'yes') {
										$sign_up=$sign_up_array[$field_key] ;
									}
									$require='';
									if(isset($require_array[$field_key]) && $require_array[$field_key] == 'yes') {
										$require=$require_array[$field_key];
									}
									$myaccount_one='';									
									if(isset($myaccount_fields_array[$field_key]) && $myaccount_fields_array[$field_key] == 'yes') {
										$myaccount_one=$myaccount_fields_array[$field_key];
									}
									
									
								?>
								<tr  id="wpdatatablefield_<?php echo esc_attr($i);?>">
									<td>
										<input type="text" class="form-control" name="meta_name[]" id="meta_name[]" value="<?php echo esc_attr($field_key); ?>"> 
									</td>
									<td>
										<input type="text" class="form-control" name="meta_label[]" id="meta_label[]" value="<?php echo esc_attr($field_value);?>" >
									</td>
									<td id="inputtypell_<?php echo esc_attr($i);?>">
										
										<?php $field_type_saved= (isset($field_type[$field_key])?$field_type[$field_key]:'' );?>
										<select class="form-select" name="field_type[]" id="field_type[]">
											<option value="text" <?php echo ($field_type_saved=='text'? "selected":'');?> ><?php esc_html_e('Text','jobboard');?></option>
											<option value="textarea" <?php echo ($field_type_saved=='textarea'? "selected":'');?> ><?php esc_html_e('Text Area','jobboard');?></option>
											<option value="dropdown" <?php echo ($field_type_saved=='dropdown'? "selected":'');?> ><?php esc_html_e('Dropdown','jobboard');?></option>
											<option value="radio" <?php echo ($field_type_saved=='radio'? "selected":'');?> ><?php esc_html_e('Radio button','jobboard');?></option>
											<option value="datepicker" <?php echo ($field_type_saved=='datepicker'? "selected":'');?> ><?php esc_html_e('Date Picker','jobboard');?></option>
											<option value="checkbox" <?php echo ($field_type_saved=='checkbox'? "selected":'');?> ><?php esc_html_e('Checkbox','jobboard');?></option>
											<option value="url" <?php echo ($field_type_saved=='url'? "selected":'');?> ><?php esc_html_e('URL','jobboard');?></option>
										</select>
										
									</td>
									<td>
										<textarea class="form-control" rows="3" name="field_type_value[]" id="field_type_value[]" placeholder="<?php  esc_html_e('Separated by comma','jobboard');?> "><?php echo esc_attr((isset($field_type_value[$field_key])?$field_type_value[$field_key]:''));?></textarea>
									</td>
									<td id="roleall_<?php echo esc_attr($i);?>">									
									<?php $field_user_role_saved= (isset($field_type_roles[$field_key])?$field_type_roles[$field_key]:'' );
										if($field_user_role_saved==''){$field_user_role_saved=array('all');}
										
										?>									
									<select name="field_user_role<?php echo esc_attr($i);?>[]" multiple="multiple" class="form-select" size="7">
										<option value="all" <?php echo (in_array('all',$field_user_role_saved)? "selected":'');?>> 
											<?php esc_html_e('All Users','jobboard');?> </option>
										<option value="employer" <?php echo (in_array('employer',$field_user_role_saved)? "selected":'');?>> 
												<?php esc_html_e('Employer','jobboard');?> </option>	
										<option value="candidate" <?php echo (in_array('candidate',$field_user_role_saved)? "selected":'');?>> 
											<?php esc_html_e('Candidate','jobboard');?> </option>		
											
										<?php										
											foreach ( $wp_roles->roles as $key_role=>$value_role ){?>
												<option value="<?php echo esc_attr($key_role); ?>" <?php echo (in_array($key_role,$field_user_role_saved)? "selected":'');?>> <?php echo esc_html($key_role);?> </option>
											
											<?php												
											}
										?>
									</select>
										
									</td>
									
									<td>
										<label>
											<input type="checkbox" name="signup<?php echo esc_attr($i); ?>" id="signup<?php echo esc_attr($i); ?>" value="yes" <?php echo($sign_up=='yes'? 'checked':'' );?> >
										</label>
									</td>
									<td>
									
										<label>
											<input type="checkbox" name="myaccountprofile<?php echo esc_attr($i); ?>" id="myaccountprofile<?php echo esc_attr($i); ?>" value="yes" <?php echo ($myaccount_one=='yes'? 'checked':'' );?>  class="text-center">
										</label>
									</td>
									<td>
										<label>
											<input type="checkbox" name="srequire<?php echo esc_attr($i); ?>" id="srequire<?php echo esc_attr($i); ?>" value="yes" <?php echo ($require=='yes'? 'checked':'' );?>  class="text-center">
										</label>
									</td>
									<td>
									<?php
									if($i>=1){
									?>
										<button class="btn btn-danger btn-xs" onclick="return iv_remove_field('<?php echo esc_attr($i); ?>');"><?php esc_html_e('Delete','jobboard');?> </button>
									<?php
									}
									?>
									</td>
								</tr>	
								<?php
									$i++;
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th> <?php  esc_html_e('Input Name','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Label','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Type','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Value[Dropdown,checkbox & Radio Button]','jobboard')	;?> </th>
								<th> <?php  esc_html_e('User Role [Show on My Account/Profile]','jobboard')	;?> </th>						
								<th> <?php  esc_html_e('Registration','jobboard')	;?></th>
								<th> <?php  esc_html_e('My Account / Profile','jobboard')	;?> </th>
								<th> <?php  esc_html_e('Require','jobboard')	;?> </th>
								<th><?php  esc_html_e('Action','jobboard')	;?></th>
							</tr>
						</tfoot>
					</table>
					
					<div id="custom_field_div">
					</div>
					<div class="col-xs-12">
						<button class="btn btn-warning " onclick="return iv_add_field();"><?php esc_html_e('Add More Field','jobboard');?></button>
					</div>
				</div>
			</div>
		
			
		</form>
		<div class="row">
			<div class="col-xs-12">
				<div align="center">
					<div id="success_message_profile"></div>
					<button class="btn btn-info btn-lg" onclick="return update_profile_fields();"><?php esc_html_e('Update','jobboard');?> </button>
				</div>
				<p>&nbsp;</p>
			</div>
		</div>
		
		
	</div>
</div>
<?php
	wp_enqueue_script('wp_jobboard-dashboard5', wp_jobboard_URLPATH.'admin/files/js/profile-fields.js', array('jquery'), $ver = true, true );
	wp_localize_script('wp_jobboard-dashboard5', 'profile_data', array( 			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'redirecturl'	=>  wp_jobboard_ADMINPATH.'admin.php?&page=wp-jobboard-profile-fields',
	'adminnonce'=> wp_create_nonce("admin"),
	'pii'	=>$ii,
	'pi'	=> $i,
	"sProcessing"=>  esc_html__('Processing','jobboard'),
	"sSearch"=>   esc_html__('Search','jobboard'),
	"lengthMenu"=>   esc_html__('Display _MENU_ records per page','jobboard'),
	"zeroRecords"=>  esc_html__('Nothing found - sorry','jobboard'),
	"info"=>  esc_html__('Showing page _PAGE_ of _PAGES_','jobboard'),
	"infoEmpty"=>   esc_html__('No records available','jobboard'),
	"infoFiltered"=>  esc_html__('(filtered from _MAX_ total records)','jobboard'),
	"sFirst"=> esc_html__('First','jobboard'),
	"sLast"=>  esc_html__('Last','jobboard'),
	"sNext"=>     esc_html__('Next','jobboard'),
	"sPrevious"=>  esc_html__('Previous','jobboard'),
	) );
?>	