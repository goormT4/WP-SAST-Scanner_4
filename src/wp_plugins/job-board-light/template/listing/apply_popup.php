<?php	
	wp_enqueue_style('bootstrap-jobboard-110', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	wp_enqueue_style('single-job', wp_jobboard_URLPATH . 'admin/files/css/single-job.css');
	$dir_id=0; if(isset($_REQUEST['dir_id'])){$dir_id=sanitize_text_field($_REQUEST['dir_id']);}
	$id=$dir_id;	
	
?>
<div class="bootstrap-wrapper popup0margin "id="popup-contact" >		
	<div class="container" >
		<div class="row" >
		
			<div class="col-md-12">
				<div class="modal-header">
					<h4 class="modal-title"><?php esc_html_e('Apply Now','jobboard'); ?></h4>							
						<button type="button" onclick="contact_close();" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
				</div>
				<div class="modal-body">
					<div class="row vertical-divider">
						<div class="col-md-6 col-sm-12">
							<?php
							include( wp_jobboard_template. 'listing/apply-form.php');						
							?>
						</div>
						
						<div class="col-md-6 col-sm-12">
								<h4 class="modal-title"><?php esc_html_e('Apply From Your Account ','jobboard'); ?></h4>
								<hr/>
								<?php
								 if(is_user_logged_in()){
								
									$job_apply='no';
									$userID = get_current_user_id();
									$job_apply_all = get_user_meta($userID,'job_apply_all',true);
									$job_apply_all = explode(",", $job_apply_all);
									if (in_array($id, $job_apply_all)) {
										$job_apply='yes';
									}										
									if($job_apply=='yes'){ ?>
										<div class="col-md-12 alert alert-info alert-dismissable"><h4><?php  esc_html_e( 'Applied Already', 'jobboard' ); ?></h4></div>
										
									<?php	
									}	
									?>
								<form action="#" id="apply-pop2" name="apply-pop2"   method="POST" >
									<div class="form-group ">
										<label for="message" ><?php  esc_html_e( 'Cover Letter', 'jobboard' ); ?></label>
										 <input type="hidden" name="dir_id" id="dir_id" value="<?php echo esc_attr($id);?>">
										<textarea  class="form-control" name="cover-content2" id="cover-content2"  cols="20" rows="3"></textarea>
									 </div>
									 <div class="form-group ">									
									  <button type="button" class="btn btn-secondary ml-2"  onclick="job_apply_user();" ><?php  esc_html_e( 'Submit', 'jobboard' ); ?></button>									 
									  </div>
								</form>
								 <div  id="message_popupjob_apply_user"></div> 
								<?php
								}else{
								$login_page=get_option('epjbjobboard_login_page'); 
								?>
								<h5 ><a href="<?php echo get_permalink( $login_page);?>"><?php esc_html_e('Please Login & Apply','jobboard'); ?></a></h5>
							<?php
							
							}
							?>
						</div>
					</div>				
				</div>	
												
			</div>				
		</div>	
	</div>	
</div>	
