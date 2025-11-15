<div class="profile-content">
	<div class="portlet row light">
			<div class="col-md-12">
				<div class="portlet-title tabbable-line clearfix">
				
					<div class="caption caption-md">
						<h4 class="lighter-heading "><?php  esc_html_e('My Profile','jobboard');?></h4>
					</div>
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#tab_1_1" data-toggle="tab"><?php   esc_html_e('Personal Info','jobboard');?> </a>
						</li>
						<li>
							<a href="#tab_1_3" data-toggle="tab"><?php   esc_html_e('Change Password','jobboard');?> </a>
						</li>
						
					</ul>
				</div>
			</div>
			<div class="portlet-body col-md-12">
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1_1">
					<form role="form" name="profile_setting_form" id="profile_setting_form" action="#">
					<?php
						$user_type= get_user_meta($current_user->ID , 'user_type', true);						
						if($user_type =='employer'){							
							include('employer-edit-profile.php');
						}
						if($user_type =='candidate'){
							?>
							<a class="btn btn-xs green-haze" href="<?php echo get_permalink(); ?>?&profile=edit_resume"><?php  esc_html_e('Edit Resume','jobboard');?></a>
							<?php							
						}
						if($user_type==''){
							if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
								include('employer-edit-profile.php');
							}else{
								?>
								<a class="btn btn-xs green-haze" href="<?php echo get_permalink(); ?>?&profile=edit_resume"><?php  esc_html_e('Edit Resume','jobboard');?></a>
								<?php
							}
						}
					?>
					</form>
					</div>
					<div class="tab-pane" id="tab_1_3">
							<form action="" name="pass_word" id="pass_word">
								<div class="form-group">
									<label class="control-label"><?php   esc_html_e('Current Password','jobboard');?> </label>
									<input type="password" id="c_pass" name="c_pass" class="form-control"/>
								</div>
								<div class="form-group">
									<label class="control-label"><?php   esc_html_e('New Password','jobboard');?> </label>
									<input type="password" id="n_pass" name="n_pass" class="form-control"/>
								</div>
								<div class="form-group">
									<label class="control-label"><?php   esc_html_e('Re-type New Password','jobboard');?> </label>
									<input type="password" id="r_pass" name="r_pass" class="form-control"/>
								</div>
								<div class="margin-top-10">
									<div class="" id="update_message_pass"></div>
								<button type="button" onclick="iv_update_password();"  class="btn green-haze"><?php   esc_html_e('Change Password','jobboard');?> </button>
								</div>
							</form>
					</div>
					
					
			</div>
		
		</div>
	</div>
</div>
          <!-- END PROFILE CONTENT -->
