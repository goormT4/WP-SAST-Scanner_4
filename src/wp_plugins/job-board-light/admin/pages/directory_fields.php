<?php
	global $wpdb;
	global $current_user;
	$ii=1;
	$main_category='';
	if(isset($_POST['main_category'])){$main_category=sanitize_text_field($_POST['main_category']);}	
?>
<div class="bootstrap-wrapper">
	<div class="dashboard-eplugin container-fluid">
		<form id="dir_fields" name="dir_fields" class="form-horizontal" role="form" onsubmit="return false;">
			<div class="row">					
				<div class="col-xs-12" id="submit-button-holder">					
					<div class="pull-right">
						<?php
							if($main_category!=''){	
							?>	
							<button class="btn btn-info btn-lg" onclick="return update_dir_fields();"><?php esc_html_e( 'Update', 'jobboard' );?> </button>
							<?php
							}
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12"><h3 class="page-header"><?php esc_html_e('Listing Fields','jobboard'); ?>  <br /><small> &nbsp;</small> </h3>
				</div>
			</div> 
			<div id="success_message">	</div>	
			<div class="panel panel-info">
				<div class="panel-heading"><h4><?php 
				esc_html_e('Details Fields','jobboard'); ?> </h4></div>
				<div class="panel-body">	
					<div class="row ">
						<div class="col-sm-5 ">										
							<h4><?php esc_html_e( 'Post Meta Name[no space]', 'jobboard' );?></h4>
						</div>
						<div class="col-sm-5">
							<h4><?php esc_html_e( 'Display Label', 'jobboard' );?></h4>									
						</div>
						<div class="col-sm-2">
							<h4><?php esc_html_e( 'Action', 'jobboard' );?></h4>
						</div>		
					</div>
					<div id="custom_field_div">			
						<?php
							$default_fields = array();
							$field_set=get_option('jobboard_fields' );
							if($field_set!=""){ 
								$default_fields=get_option('jobboard_fields');
								}else{															
								$default_fields['other_link']=esc_html__('Other Link','jobboard');
							}
							$i=1;		
							foreach ( $default_fields as $field_key => $field_value ) {												
								echo '<div class="row form-group " id="field_'.$i.'"><div class=" col-sm-5"> <input type="text" class="form-control" name="meta_name[]" id="meta_name[]" value="'.esc_attr($field_key) . '" placeholder="Enter Post Meta Name "> </div>		
								<div  class=" col-sm-5">
								<input type="text" class="form-control" name="meta_label[]" id="meta_label[]" value="'.esc_attr($field_value) . '" placeholder="'.esc_html__('Enter Post Meta Label','jobboard').'">													
								</div>
								<div  class=" col-sm-2">';
							?>
							<button class="btn btn-danger btn-xs" onclick="return iv_remove_field('<?php echo esc_attr($i); ?>');"><?php esc_html_e( 'Delete', 'jobboard' );?></button>
						</div>
					</div>
					<?php	
						$i++;	
					}						
				?>
			</div>				  
			<div class="col-xs-12">
				<button class="btn btn-warning btn-xs" onclick="return iv_add_field();"><?php esc_html_e( 'Add More', 'jobboard' );?></button>
			</div>	
			<input type="hidden" name="dir_name" id="dir_name" value="<?php echo esc_attr($main_category); ?>">	 
		</div>		 
	</div>			 	
	<div class="panel panel-info">
		<div class="panel-heading"><h4><?php 
		esc_html_e('Job status','jobboard'); ?> </h4></div>
		<div class="panel-body">
			<div class="row ">
				<div class="col-md-8 ">	
					<?php
						$job_status_all=get_option('job_status');					
						if($job_status_all==""){$job_status_all='Full Time, Part Time,Freelance, Hourly, Project Base';}
					?>
					<textarea class="form-control"  id="job_status_all" name="job_status_all" rows="3"><?php echo esc_html($job_status_all); ?></textarea>
				</div>
			</div>
		</div>		 
	</div>
	<div class="panel panel-info">
		<div class="panel-heading"><h4><?php 
		esc_html_e('Job Experience Range','jobboard'); ?> </h4></div>
		<div class="panel-body">
			<div class="row ">
				<div class="col-md-8 ">	
					<?php
						$experience_range='';
						$experience_range=get_option('experience_range');					
						if($experience_range==""){
							$experience_range='Any,Below 1 Year,1 - <3 Years,3 - <5 Years,5 - <10 Years,Over 10 Years';
							}
					?>
					<textarea class="form-control"  id="job_experience_range" name="job_experience_range" rows="3"><?php echo esc_html($experience_range); ?></textarea>
				</div>
			</div>
		</div>		 
	</div>
	
	<div class="panel panel-info">
		<div class="panel-heading"><h4><?php 
		esc_html_e('Job level','jobboard'); ?> </h4></div>
		<div class="panel-body">
			<div class="row ">
				<div class="col-md-8 ">	
					<?php
						$job_level_all=get_option('job_level');					
						if($job_level_all==""){$job_level_all='Any,Entry Lavel,Mid Level,Top Level';}
					?>
					<textarea class="form-control"  id="job_level_all" name="job_level_all" rows="3"><?php echo esc_html($job_level_all); ?></textarea>
				</div>
			</div>
		</div>		 
	</div>
	<div class="row">					
		<div class="col-xs-12">					
			<div align="center">
				<div id="success_message-fields"></div>														
				<button class="btn btn-info btn-lg" onclick="return update_dir_fields();"><?php esc_html_e( 'Update', 'jobboard' );?> </button>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>
</form>					
</div>
</div>	
<?php
	wp_enqueue_script('iv_directory-ar-prifile-fields', wp_jobboard_URLPATH . 'admin/files/js/listing_profile_fields.js');
	wp_localize_script('iv_directory-ar-prifile-fields', 'dirpro', array(		
	'i'=> 	esc_html($i),		
	'ii'=> esc_html($ii),
	) );
	?>	