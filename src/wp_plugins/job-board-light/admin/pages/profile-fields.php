<?php
	global $wpdb;
	global $current_user;
	$ii=1;
?>
<div class="bootstrap-wrapper">
	<div class="dashboard-eplugin container-fluid">
		<div class="row">					
			<div class="col-xs-12" id="submit-button-holder">					
				<div class="pull-right"><button class="btn btn-info btn-lg" onclick="return update_profile_fields();"><?php  esc_html_e('Update','jobboard');?> </button>
				</div>
			</div>
		</div>
		
		<form id="profile_fields" name="profile_fields" class="form-horizontal" role="form" onsubmit="return false;">
			<div id="success_message">	</div>	
			<div class="panel panel-success">
				<div class="panel-heading"><h4> <?php  esc_html_e('My Account Menu','jobboard');?> </h4></div>
				<div class="panel-body">
					<div class="row ">
						<div class="col-sm-3 ">										
							<h4><strong><?php  esc_html_e('Menu Title / Label','jobboard');?> </strong> </h4>
						</div>
						<div class="col-sm-7">
							<h4><strong><?php  esc_html_e('Link','jobboard');?> </strong></h4>									
						</div>
						<div class="col-sm-2">
							<h4><strong><?php  esc_html_e('Action','jobboard');?></strong> </h4>
						</div>		
					</div>
					<?php
						$profile_page=get_option('epjbjobboard_profile_page'); 	
						$page_link= get_permalink( $profile_page); 
					?>
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Job Search','jobboard');?>  
						</div>
						<div class="col-sm-7">
							<a href="<?php echo get_post_type_archive_link( 'job' ) ; ?>">
								<?php echo get_post_type_archive_link( 'job' ) ; ?>
							</a>									
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_menu_listinghome' ) ) {
										$account_menu_check= get_option('epjbjobboard_menu_listinghome'); 
									}	 
								?>
								<input type="checkbox" name="listinghome" id="listinghome" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> > <?php  esc_html_e('Hide','jobboard');?>  
								</label>
							</div>											
						</div>					  
					</div>
					
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Membership','jobboard');	 ?> 
						</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=level">
								<?php echo esc_url($page_link); ?>?&profile=level
							</a>									
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_mylevel' ) ) {
										$account_menu_check= get_option('epjbjobboard_mylevel'); 
									}	 
								?>
								<input type="checkbox" name="mylevel" id="mylevel" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?>  
								</label>
							</div>											
						</div>					  
					</div>
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Edit Profile','jobboard');?>  
						</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=setting">
								<?php echo esc_url($page_link); ?>?&profile=setting
							</a>									
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_menusetting' ) ) {
										$account_menu_check= get_option('epjbjobboard_menusetting'); 
									}	 
								?>
								<input type="checkbox" name="menusetting" id="menusetting" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>		
					
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Manage Jobs','jobboard');?>  
						</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=all-post">
								<?php echo esc_url($page_link); ?>?&profile=all-post
							</a>										
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_menuallpost' ) ) {
										$account_menu_check= get_option('epjbjobboard_menuallpost'); 
									}	 
								?>
								<input type="checkbox" name="menuallpost" id="menuallpost" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>		
					
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Manage Candidates','jobboard');?>  
						</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=new-post">
								<?php echo esc_url($page_link); ?>?&profile=employer_manage_candidates
							</a>										
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_menunecandidates' ) ) {
										$account_menu_check= get_option('epjbjobboard_menunecandidates'); 
									}	 
								?>
								<input type="checkbox" name="menunecandidates" id="menunecandidates" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>										
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Message board','jobboard');?>  
						</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=messageboard">
								<?php echo esc_url($page_link); ?>?&profile=messageboard
							</a>									
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_messageboard' ) ) {
										$account_menu_check= get_option('epjbjobboard_messageboard'); 
									}	 
								?>
								<input type="checkbox" name="messageboard" id="messageboard" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>										
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Notification','jobboard');?>  
						</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=notification">
								<?php echo esc_url($page_link); ?>?&profile=notification
							</a>										
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_notification' ) ) {
										$account_menu_check= get_option('epjbjobboard_notification'); 
									}	 
								?>
								<input type="checkbox" name="notification" id="notification" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>		
					
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Candidate Bookmarks','jobboard');?>  
							</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=candidate-bookmarks">
								<?php echo esc_url($page_link); ?>?&profile=candidate-bookmarks
							</a>										
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_candidate_bookmarks' ) ) {
										$account_menu_check= get_option('epjbjobboard_candidate_bookmarks'); 
									}	 
								?>
								<input type="checkbox" name="candidate_bookmarks" id="candidate_bookmarks" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>		
					
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Employer Bookmarks','jobboard');?>  
							</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=employer_bookmarks">
								<?php echo esc_url($page_link); ?>?&profile=employer_bookmarks
							</a>										
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_employer_bookmarks' ) ) {
										$account_menu_check= get_option('epjbjobboard_employer_bookmarks'); 
									}	 
								?>
								<input type="checkbox" name="employer_bookmarks" id="employer_bookmarks" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>		
					
					
					<div class="row ">
						<div class="col-sm-3 ">										
							<?php  esc_html_e('Job Bookmarks','jobboard');?>  
							</div>
						<div class="col-sm-7">
							<a href="<?php echo esc_url($page_link); ?>?&profile=job_bookmark">
								<?php echo esc_url($page_link); ?>?&profile=job_bookmark
							</a>										
						</div>
						<div class="col-sm-2">
							<div class="checkbox ">
								<label><?php
									$account_menu_check='';
									if( get_option('epjbjobboard_job_bookmarks' ) ) {
										$account_menu_check= get_option('epjbjobboard_job_bookmarks'); 
									}	 
								?>
								<input type="checkbox" name="job_bookmark" id="job_bookmark" value="yes" <?php echo ($account_menu_check=='yes'? 'checked':'' ); ?> >  <?php  esc_html_e('Hide','jobboard');?> 
								</label>
							</div>											
						</div>					  
					</div>	
							
				</div>
			</div>				
		</form>
		<div class="row">					
			<div class="col-xs-12">					
				<div align="center">
					<div id="loading"></div>
					<div id="messageprofile"></div>
					
					<button class="btn btn-info btn-lg" onclick="return update_profile_fields();"><?php  esc_html_e('Update','jobboard');?>  </button>
				</div>
				<p>&nbsp;</p>
			</div>
		</div>
	</div>
</div>		 
		