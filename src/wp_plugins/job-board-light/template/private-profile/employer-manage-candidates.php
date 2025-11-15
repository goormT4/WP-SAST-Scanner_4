<?php
	wp_enqueue_script('edit_resume_js', wp_jobboard_URLPATH . 'admin/files/js/custom_dropdown.js');
	wp_enqueue_style('custom_select_box', wp_jobboard_URLPATH . 'admin/files/css/custom_select.css');
	
	
?>



<div class="profile-content">
	<div class="portlet row light">
			<div class="col-md-12">
				<div class="portlet-title tabbable-line clearfix">
					<div class="caption caption-md">
						<h4 class="lighter-heading"><?php  esc_html_e('Applicants','jobboard');?> </h4>
						
					</div>
					<ul class="nav nav-tabs">
						<li class=" nav-item active ">
							<a href="#tab_1_1" data-toggle="tab" class=""><?php   esc_html_e('All','jobboard');?> </a>
						</li >
						<li class=" nav-item ">
							<a href="#tab_1_6" data-toggle="tab"><?php   esc_html_e('Shortlisted','jobboard');?> </a>
						</li>
						<li class=" nav-item  ">
							<a href="#tab_1_3" data-toggle="tab"><?php   esc_html_e('Schedule Meeting','jobboard');?> </a>
						</li>
						<li class=" nav-item  ">
							<a href="#tab_1_5" data-toggle="tab"><?php   esc_html_e('Rejected','jobboard');?> </a>
						</li>
					</ul>
				</div>
			</div>
			
			<div class="portlet-body col-md-12">
				<div class="tab-content">
					<div class="tab-pane active" id="tab_1_1">					
					<?php								
						include('candidates_all.php');
					?>				
					</div>
					<div class="tab-pane" id="tab_1_6">
							<?php								
						include('candidates_shortlisted.php');
					?>
					</div>
					<div class="tab-pane" id="tab_1_3">
							<?php								
						include('candidates_meeting_schedule.php');
					?>
					</div>
					<div class="tab-pane" id="tab_1_5">
						<?php								
						include('candidates_deleted.php');
					?>
					</div>
					
			</div>
		
		</div>
	</div>
</div>
          <!-- END PROFILE CONTENT -->

