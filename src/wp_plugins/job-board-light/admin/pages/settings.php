<?php
	global $wpdb;
?>

<div class="bootstrap-wrapper">

	<div class="container-fluid">
		<br/>
		
			<?php
			include('footer.php');
			?>
		<div id="update_message"> </div>
		<div class="panel with-nav-tabs panel-info">

			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#dir-demo" data-toggle="tab"><?php  esc_html_e('Import Data','jobboard'); ?> </a></li>
					<li ><a href="#dir-setting" data-toggle="tab"><?php  esc_html_e('Listing Settings','jobboard'); ?> </a></li>
					<li ><a href="#listing-fields" data-toggle="tab"><?php  esc_html_e('Listing Fields','jobboard'); ?> </a></li>
					<li ><a href="#my-account" data-toggle="tab"><?php  esc_html_e('My Account Menu','jobboard'); ?> </a></li>
					<li ><a href="#dir-marker" data-toggle="tab"><?php  esc_html_e('Category Image/Marker','jobboard'); ?></a></li>
					<li ><a href="#city-image" data-toggle="tab"><?php  esc_html_e('Cities Images','jobboard'); ?></a></li>
					
					<li ><a href="#pagesall" data-toggle="tab"><?php  esc_html_e('Pages','jobboard'); ?></a></li>
					<li ><a href="#user-setting" data-toggle="tab"><?php  esc_html_e('User Setting','jobboard'); ?></a></li>
					<li ><a href="#payment" data-toggle="tab"><?php  esc_html_e('Terms CheckBox/Coupon','jobboard'); ?></a></li>
					<li ><a href="#email" data-toggle="tab"><?php  esc_html_e('Email Template','jobboard'); ?> </a></li>
					<li ><a href="#support" data-toggle="tab"><?php  esc_html_e('Support','jobboard'); ?> </a></li>								
					
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div class="tab-pane" id="support">								
						<?php  esc_html_e('If you have any issue then you can send your issue screenshot to my email address: aktar567@gmail.com','jobboard'); ?> 
					</div>
					<div class="tab-pane  " id="listing-fields">								
						<?php require (wp_jobboard_DIR .'/admin/pages/directory_fields.php');?>
					</div>
					<div class="tab-pane" id="dir-setting">								
						<?php
							require(wp_jobboard_DIR .'/admin/pages/dir_setting.php');
						?>
					</div>
					<div class="tab-pane  " id="my-account">								
						<?php require (wp_jobboard_DIR .'/admin/pages/profile-fields.php');?>
					</div>
					<div class="tab-pane  in active " id="dir-demo">								
						<?php require (wp_jobboard_DIR .'/admin/pages/dir-demo.php');?>
					</div>
					<div class="tab-pane  " id="dir-marker">								
						<?php
							require (wp_jobboard_DIR .'/admin/pages/map_marker.php');
						?>
					</div> 
					<div class="tab-pane  " id="city-image">								
						<?php
							require(wp_jobboard_DIR .'/admin/pages/city_image.php');
						?>
					</div>
					
					<div class="tab-pane" id="pagesall">	
						<?php
							require(wp_jobboard_DIR .'/admin/pages/setting-pages-all.php');
						?>
					</div>
					
					<div class="tab-pane  " id="user-setting">								
						<?php
							require(wp_jobboard_DIR .'/admin/pages/user_directory_admin.php');
						?>
					</div>						
					<div class="tab-pane  " id="user_reg">
						<form class="form-horizontal" role="form"  name='account_settings' id='account_settings'>
							<br/>
							<?php
								$args = array(
								'child_of'     => 0,
								'sort_order'   => 'ASC',
								'sort_column'  => 'post_title',
								'hierarchical' => 1,															
								'post_type' => 'page'
								);
							?>
							<div class="form-group">
								<label  class="col-md-3   control-label"><?php esc_html_e( 'User Registration Page Redirect:', 'jobboard' );?> </label>
								<div class="checkbox col-md-3 ">
									<?php
										$iv_redirect = get_option( 'jobboard_signup_redirect');
										if ( $pages = get_pages( $args ) ){
											echo "<select id='signup_redirect' name='signup_redirect' class='form-control'>";
											echo "<option value='defult' ".($iv_redirect=='defult' ? 'selected':'').">Default WP Action</option>";
											foreach ( $pages as $page ) {
												echo "<option value='{$page->ID}' ".($iv_redirect==$page->ID ? 'selected':'').">{$page->post_title}</option>";
											}
											echo "</select>";
										}
									?>
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-3   control-label"><?php esc_html_e( 'Hide Admin Bar for All Users Except for Administrators:', 'jobboard' );?> </label>																	
								<div class=" col-md-3 ">																			
									<?php
										$hide_admin_bar='';
										if( get_option('epjbjobboard_hide_admin_bar' ) ) {
											$hide_admin_bar= get_option('epjbjobboard_hide_admin_bar'); 
										}	 
										?><label>
										<input  class="" type="checkbox" name="hide_admin_bar" id="hide_admin_bar" value="yes" <?php echo ($hide_admin_bar=='yes'? 'checked':'' ); ?> > 
										<?php esc_html_e( 'Hide', 'jobboard' );?> 
									</label>
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-3 control-label"> </label>
								<div class="col-md-8">
									<button type="button" onclick="return  iv_update_account_settings();" class="btn btn-success"><?php esc_html_e( 'Update', 'jobboard' );?></button>
								</div>
							</div>
						</form>			
					</div>
					<div class="tab-pane  " id="payment">
						<!--Payment  -->
						<form class="form-horizontal" role="form"  name='payment_settings' id='payment_settings'>
							<div class="form-group">
								<label  class="col-md-2   control-label"><?php esc_html_e( 'Terms CheckBox', 'jobboard' );?> : </label>
								<div class="col-md-10 col-xs-10 col-sm-10">
									<div class="checkbox col-md-1 ">
										<label><?php
											$t_terms='';
											if( get_option( 'jobboard_payment_terms' ) ) {
												$t_terms= get_option('jobboard_payment_terms'); 
											}	 
										?>
										<input type="checkbox" name="iv_terms" id="iv_terms" value="yes" <?php echo ($t_terms=='yes'? 'checked':'' ); ?> ><?php esc_html_e( 'Dispaly', 'jobboard' );?> 
										</label>
									</div>
									<div class=" col-md-6 col-xs-6 col-sm-6">	
										<?php
											$t_text='I have read & accept the Terms & Conditions';
											if( get_option( 'jobboard_payment_terms_text' ) ) {
												$t_text= get_option('jobboard_payment_terms_text'); 
											}	 
										?>																			
										<textarea class="form-control" rows="3" name='terms_detail' id='terms_detail' >  <?php echo esc_html($t_text); ?></textarea>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"><?php esc_html_e( 'Hide Coupon Buton', 'jobboard' );?> : </label>
								<div class="col-md-10 col-xs-10 col-sm-10">
									<div class="checkbox col-md-1 ">
										<label><?php
											$t_coupon='';
											if( get_option('epjbjobboard_payment_coupon' ) ) {
												$t_coupon= get_option('epjbjobboard_payment_coupon'); 
											}	 
										?>
										<input type="checkbox" name="iv_coupon" id="iv_coupon" value="yes" <?php echo ($t_coupon=='yes'? 'checked':'' ); ?> ><?php esc_html_e( 'Hide', 'jobboard' );?> 
										</label>
									</div>
								</div>
							</div>
							<div class="form-group  row">
								<label  class="col-md-3  control-label"> </label>
								<div class="col-md-4">
									<button type="button" onclick="return  iv_update_payment_settings_terms();" class="btn btn-success"><?php esc_html_e( 'Update', 'jobboard' );?></button>				
								</div>							
							</div>
						</form>
					</div>
					<div class="tab-pane " id="email">
						<form class="form-horizontal" role="form"  name='email_settings' id='email_settings'>	
							<?php
								$form_id='';										
							?>
							<div class="form-group">
								<label  class="col-md-2  control-label"> <?php esc_html_e( 'Email Sender :', 'jobboard' );?> </label>
								<div class="col-md-4 ">
									<?php
										$admin_email_setting='';
										if( get_option( 'admin_email_jobboard' )==FALSE ) {
											$admin_email_setting = get_option('admin_email');						 
											}else{
											$admin_email_setting = get_option('admin_email_jobboard');								
										}	
									?>
									<input type="text" class="form-control" id="jobboard_admin_email" name="jobboard_admin_email" value="<?php echo esc_html($admin_email_setting); ?>" placeholder="">
								</div>
							</div>	
							<div class="form-group">
								<h3  class="col-md-12   page-header"><?php esc_html_e( 'Signup / Forget password Email', 'jobboard' );?> </h3>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"><?php esc_html_e( 'Email Subject', 'jobboard' );?>  : </label>
								<div class="col-md-4 ">
									<?php
										$jobboard_signup_email_subject = get_option( 'jobboard_signup_email_subject');
									?>
									<input type="text" class="form-control" id="jobboard_signup_email_subject" name="jobboard_signup_email_subject" value="<?php echo esc_html($jobboard_signup_email_subject); ?>" placeholder="Enter signup email subject">
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"> <?php esc_html_e( 'Email Tempalte ', 'jobboard' );?>: </label>
								<div class="col-md-10 ">
									<?php
										$settings_a = array(															
										'textarea_rows' =>20,															 
										);
										$content_client = get_option( 'jobboard_signup_email');
										$editor_id = 'signup_email_template';
									?>
									<textarea id="<?php echo esc_html($editor_id) ;?>" name="<?php echo esc_html($editor_id) ;?>" rows="20" class="col-md-12 ">
										<?php echo esc_html($content_client); ?>
									</textarea>		
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"> <?php esc_html_e( 'Forget Subject', 'jobboard' );?> : </label>
								<div class="col-md-4 ">
									<?php
										$jobboard_forget_email_subject = get_option( 'jobboard_forget_email_subject');
									?>
									<input type="text" class="form-control" id="forget_email_subject" name="forget_email_subject" value="<?php echo esc_html($jobboard_forget_email_subject); ?>" placeholder="Enter forget email subject">
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"><?php esc_html_e( 'Forget Tempalte :', 'jobboard' );?>  </label>
								<div class="col-md-10 ">
									<?php
										$settings_forget = array(															
										'textarea_rows' =>'20',	
										'editor_class'  => 'form-control',														 
										);
										$content_client = get_option( 'jobboard_forget_email');
										$editor_id = 'forget_email_template';																				
									?>
									<textarea id="<?php echo esc_attr($editor_id );?>" name="<?php echo esc_attr($editor_id) ;?>" rows="20" class="col-md-12 ">
										<?php echo esc_html($content_client); ?>
									</textarea>		
								</div>
							</div>
							<div class="form-group">
								<h3  class="col-md-12 col-xs-12 col-sm-12  page-header"><?php esc_html_e( 'Order Email', 'jobboard' );?> </h3>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"><?php esc_html_e( 'User Email Subject :', 'jobboard' );?>  </label>
								<div class="col-md-4 ">
									<?php
										$jobboard_order_email_subject = get_option( 'jobboard_order_client_email_sub');
									?>
									<input type="text" class="form-control" id="jobboard_order_email_subject" name="jobboard_order_email_subject" value="<?php echo esc_html($jobboard_order_email_subject); ?>" placeholder="Enter order email subject">
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"> <?php esc_html_e( 'User Email Tempalte :', 'jobboard' );?> </label>
								<div class="col-md-10 ">
									<?php
										$settings_a = array(															
										'textarea_rows' =>20,															 
										);
										$content_client = get_option( 'jobboard_order_client_email');
										$editor_id = 'order_client_email_template';																			
									?>
									<textarea id="<?php echo esc_attr($editor_id);?>" name="<?php echo esc_attr($editor_id) ;?>" rows="20" class="col-md-12 ">
										<?php echo esc_html($content_client); ?>
									</textarea>			
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"> <?php esc_html_e( 'Admin Email Subject :', 'jobboard' );?> </label>
								<div class="col-md-4 ">
									<?php
										$jobboard_order_admin_email_subject = get_option( 'jobboard_order_admin_email_sub');
									?>
									<input type="text" class="form-control" id="jobboard_order_admin_email_subject" name="jobboard_order_admin_email_subject" value="<?php echo esc_attr($jobboard_order_admin_email_subject); ?>" placeholder="Enter order email subject">
								</div>
							</div>
							<div class="form-group">
								<label  class="col-md-2   control-label"> <?php esc_html_e( 'Admin Email Tempalte :', 'jobboard' );?> </label>
								<div class="col-md-10 ">
									<?php
										$settings_a = array(															
										'textarea_rows' =>20,															 
										);
										$content_client = get_option( 'jobboard_order_admin_email');
										$editor_id = 'order_admin_email_template';																							
									?>
									<textarea id="<?php echo esc_attr($editor_id) ;?>" name="<?php echo esc_attr($editor_id );?>" rows="20" class="col-md-12 ">
										<?php echo esc_html($content_client); ?>
									</textarea>		
								</div>
							</div>
							<div class="form-group">
								<h3  class="col-md-12 col-xs-12 col-sm-12  page-header"><?php esc_html_e( 'Reminder Email', 'jobboard' );?> </h3>
							</div>
							<?php
								include (wp_jobboard_DIR .'/admin/pages/reminder_email.php');
							?>
							<div class="form-group">
								<h3  class="col-md-12 col-xs-12 col-sm-12  page-header"><?php esc_html_e( 'New Message Email', 'jobboard' );?> </h3>
							</div>
							<?php
								include (wp_jobboard_DIR .'/admin/pages/new-message.php');
							?>
						</form>
						<div id="email-success"></div>
						<div class="row pull-left">
							<div class="col-md-12 ">
								<button type="button" onclick="return  iv_update_email_settings();" class="btn btn-success"><?php  esc_html_e('Update Email Setting','jobboard');?>  </button>					
							</div>							
						</div>	
					</div>
				
				</div>
			</div>
		</div>
	</div>
</div>