<?php
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
?>
<main class="main-content">
	<h4 class="lighter-heading border-btm"><?php  esc_html_e('Applied Jobs','jobboard');?>  </h4>
	<section class="content-main-right list-jobs mb-30">
		<div class="list">
			<?php
				$job_apply_all = get_user_meta(get_current_user_id(),'job_apply_all',true);
				$job_apply_all = explode(",", $job_apply_all);
				if(sizeof($job_apply_all)>0){
				?>
				<table id="candidate-bookmark" class="table tbl-epmplyer-bookmark" >
					<thead>
						<tr class="">
							<th><?php  esc_html_e('Title','jobboard');?></th>
						</tr>
					</thead>
					<?php
						$i=0;
						foreach ($job_apply_all as $job_id){	 
							if((int)$job_id>0){
								$page_link= get_permalink( $profile_page).'?&id='.$job_id; 
								$job_id=trim($job_id);
								if ( get_post_status ( $job_id ) ) {
								?>
								<tr id="jobbookmark_<?php echo esc_html(trim($job_id));?>" >
									<td class="d-md-table-cell">
										<div class="job-item bookmark">
											<div class="row align-items-center">
												<div class="col-md-2">
													<div class="img-job text-center circle">												
														<a href="<?php  echo get_permalink($job_id); ?>">
															<?php
																$feature_image = wp_get_attachment_image_src( get_post_thumbnail_id( $job_id ), 'large' );
																if(isset($feature_image[0])){?>														
																<img  src="<?php echo esc_url($feature_image[0]); ?>">
																<?php															
																	}else{														
																	echo'<img src="'. wp_jobboard_URLPATH.'assets/images/job.png">';
																}													
															?>
														</a>
													</div>
												</div>
												<div class="col-md-10 job-info px-0">
													<div class="text px-0 text-left">
														<h4 class="title-job"><a href="<?php  echo get_permalink($job_id); ?>">
															<?php echo get_the_title($job_id); ?>
														</a></h4>
														<?php
															if(get_post_meta($job_id,'deadline',true)!=''){
															?>					
															<div class="date-job"><i class="fas fa-hourglass-end"></i><span class="p-1"></span><?php  esc_html_e('Deadline','jobboard');?>:<span class="p-2"><?php echo date('M d, Y',strtotime(get_post_meta($job_id,'deadline',true))); ?></span>
															</div>
															<?php
															}
															if(get_post_meta($job_id,'address',true)!=''){
															?>
															<div class="date-job"><span class="location"><i class="far fa-map"></i><span class="p-2"><?php echo esc_attr(get_post_meta($job_id,'address',true)); ?> <?php echo esc_attr(get_post_meta($job_id,'city',true)); ?>, <?php echo esc_attr(get_post_meta($job_id,'zipcode',true)); ?>,<?php echo esc_attr(get_post_meta($job_id,'country',true)); ?></span></span>
															</div>
															<?php
															}
														?>
														<?php
															if(get_post_meta($job_id,'salary',true)!=''){
															?>
															<div class="date-job"><span class="location"><i class="far fa-money-bill-alt"></i><span class="p-1"></span><?php  esc_html_e('Salary','jobboard');?>:<span class="p-2"><?php echo esc_attr(get_post_meta($job_id,'salary',true)); ?></span></span>
															</div>
															<?php
															}
														?>
														<div class="group-button">	
															<?php
																$dirpro_email_button=get_post_meta($job_id,'dirpro_email_button',true);
																if($dirpro_email_button==""){$dirpro_email_button='yes';}
																if($dirpro_email_button=='yes'){
																?>
																<button class="btn btn-light btn-email" onclick="job_email_popup('<?php echo (trim(esc_attr($job_id)));?>')" ><i class="far fa-envelope"></i></button>
																<?php
																}
															?>
															<button class="btn btn-light btn-delete" onclick="job_applied_delete_myaccount('<?php echo esc_attr($job_id);?>','jobbookmark')"><i class="far fa-trash-alt"></i></button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</td>
								</tr>
								<?php
								}
							}
						}
					?>
				</table>
				<?php
				}
			?>
		</div>
	</section>
</main>