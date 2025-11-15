<main class="main-content">
	<h4 class="lighter-heading border-btm"><?php  esc_html_e('Job Notifications ','jobboard');?>  </h4>
	<section class="content-main-right list-jobs mb-30">
		<form action="" id="nofification_form" name="nofification_form" method="POST" role="form">
			<div class="row">
				<?php
					$job_notifications_all= get_user_meta($current_user->ID ,'job_notifications',true);
					
					$taxonomy = $directory_url.'-category';
					$args = array(
					'orderby'           => 'name', 
					'order'             => 'ASC',
					'hide_empty'        => false, 
					'exclude'           => array(), 
					'exclude_tree'      => array(), 
					'include'           => array(),
					'number'            => '', 
					'fields'            => 'all', 
					'slug'              => '',		
					'hierarchical'      => true, 		
					'childless'         => false,
					'get'               => '', 
					);
					$terms = get_terms($taxonomy,$args); // Get all terms of a taxonomy
					if ( $terms && !is_wp_error( $terms ) ) :
					$i=0;
					$selected='';
					foreach ( $terms as $term_parent ) {  
						$selected='';
						if($job_notifications_all!=''){
							if(in_array($term_parent->slug,$job_notifications_all)){
								$selected='yes';
							}
						}
						?>	
					<div class="col-md-4 ">
						<label for="<?php echo esc_html($term_parent->slug); ?>">
						<input  type="checkbox" name="notificationone[]" id="<?php echo esc_html($term_parent->slug); ?>" value="<?php echo esc_attr($term_parent->slug); ?>" <?php echo ($selected=='yes'?'checked':'' );?>>
						<?php echo esc_html($term_parent->name);?></label>
					</div>	
					<?php
					}
					endif;	
				?>
			</div>
		</form>
		<div class="row">
			<div class="col-md-12  "> <hr/>				
				<button type="button" onclick="iv_save_notification();"  class="btn green-haze"><?php  esc_html_e('Save Notification',	'jobboard'); ?></button>
				<div class="" id="notification_message"></div>
			</div>	
		</div>	
	</section>
</main>