<?php
	
	wp_enqueue_style('bootstrap-jobboard-110', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	$user_id=0; if(isset($_REQUEST['user_id'])){$user_id=sanitize_text_field($_REQUEST['user_id']);}	
	$dir_id=0; if(isset($_REQUEST['dir_id'])){$dir_id=sanitize_text_field($_REQUEST['dir_id']);}
?>
<div class="bootstrap-wrapper popup0margin "id="popup-contact" >		
	<div class="container" >
		<div class="row" >
			<div class="col-md-12">
				<div class="modal-header">
					<h5 class="modal-title"><?php esc_html_e('Message','jobboard'); ?></h5>							
					<button type="button" onclick="contact_close();" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<form action="#" id="message-pop" name="candidate-message"   method="POST" >
								<div class="form-group  ">
									<label  for="Name"><?php  esc_html_e( 'Subject', 'jobboard' ); ?></label>
									<input  class=" form-control" id="subject" name ="subject" type="text">
								</div>
								<div class="form-group  ">
									<label  for="Name"><?php  esc_html_e( 'Email', 'jobboard' ); ?></label>
									<input  class=" form-control" id="email_address" name ="email_address" type="text">
								</div>
								<div class="form-group  ">
									<label  for="Name"><?php  esc_html_e( 'Phone#', 'jobboard' ); ?></label>
									<input  class=" form-control" id="visitorphone" name ="visitorphone" type="text">
								</div>
								<div class="form-group ">
									<label for="message" ><?php  esc_html_e( 'Message', 'jobboard' ); ?></label>
									<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($user_id);?>">	
									<input type="hidden" name="dir_id" id="dir_id" value="<?php echo esc_attr($dir_id);?>">									
									<textarea    name="message-content" id="message-content"  cols="20" rows="3"></textarea>
								</div>									
								<div class="form-group ">
									<button type="button" class="btn btn-secondary ml-2"  onclick="jobboard_user_message();" ><?php  esc_html_e( 'Submit', 'jobboard' ); ?></button>
									<div class="ml-2" id="update_message_popup"></div> 
								</div> 
							</form>
						</div>
					</div>				
				</div>	
			</div>				
		</div>	
	</div>	
</div>	