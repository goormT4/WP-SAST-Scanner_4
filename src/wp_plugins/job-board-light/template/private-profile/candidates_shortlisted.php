<div class="list">
	<?php
		$directory_url='job_apply';
		$argsshortlisted = array(
		'post_type' => $directory_url, // enter your custom post type
		'post_status' => 'private',
		'posts_per_page'=> '-1',
		'orderby'=>'date',
		'order'=>'DESC',
		);
		
		$candidate_shortlisted= array(
		'relation' => 'AND',
		array(
		'key'     => 'candidate_shortlisted',
		'value'   => 'yes',
		'compare' => '='
		),
		);
		$candidate_reject_meta= array(
		'relation' => 'OR',
		array(
		'key'     => 'candidate_reject',				
		'compare' => 'NOT EXISTS',
		),	
		array(
		'key' => 'candidate_reject',
		'value' => 'yes',
		'compare' => '!=',
		)
		);	
		$argsshortlisted['meta_query'] = array(
		$candidate_shortlisted,$candidate_reject_meta,
		);
	?>
	<table id="candidates-shortlisted" class="table tbl-epmplyer-bookmark" >
		<thead>
			<tr class="">
				<th><?php  esc_html_e('Title','jobboard');?></th>
			</tr>
		</thead>
		<?php	
			$the_query = new WP_Query( $argsshortlisted );
			if ( $the_query->have_posts() ) {?>
			<?php
				while ( $the_query->have_posts() ) : $the_query->the_post();
				$dir_data=array();
				$candidate_post_id = get_the_ID();
				$candidate_user_id= $the_query->post->post_author;
				$useridmain =get_post_meta($candidate_post_id,'user_id',true);
				$job_post_id= get_post_meta($candidate_post_id,'apply_jod_id',true);
				
				if(in_array($job_post_id,$all_job_ids)){
			?>
			<tr id="shortlisted_<?php echo esc_html($candidate_post_id);?>" class="">
				<td class="d-md-table-cell">
					<div class="job-item bookmark">
						<div class="row align-items-center">
							<!-- <div class="col-md-2">
							</div> -->
							<div class="col-md-12 job-info px-0">
								<div class="text px-0 text-left">
									<div class="row mb-3">
										<div class="col-md-1 px-0">
											<div class="img-job text-center circle">
												<?php
													$iv_profile_pic_url=get_user_meta($useridmain, 'iv_profile_pic_thum',true);
													if($iv_profile_pic_url!=''){ ?>
													<img  src="<?php echo esc_url($iv_profile_pic_url); ?>">
													<?php
														}else{
														echo'<img src="'. wp_jobboard_URLPATH.'assets/images/default-user.png">';
													}
												?>	
											</div>
										</div>
										<div class="col-md-8">
											<h4 class="title-job">
												<?php 
													if(get_post_meta($candidate_post_id, 'user_id', true)!=''){
														$userid=get_post_meta($candidate_post_id, 'user_id', true);
														$page_link= get_permalink( $profile_page).'?&id='.$userid;  
														}else{
														$page_link= '#'; 
													}
												?>
												<a href="<?php echo esc_url($page_link);?>">
													<?php
														if($the_query->post->post_author < 1){
															echo  get_post_meta($candidate_post_id, 'candidate_name', true);
															}else{
															$userinfo_data = get_user_by( 'id', $the_query->post->post_author );
															echo esc_html($userinfo_data->display_name);
														}
													?>
												</a>
											</h4>
										</div>
										<div class="col-md-3">
											<div class="action_dropdown">
												<div class="action_box">
													<span><?php esc_html_e( 'Action', 'jobboard' );?></span>
													<span><i class="fa fa-angle-down"></i></span>
												</div>
												<ul>
													<li>												
														<?php
															$upload_dir = wp_upload_dir();
															$file_name=get_post_meta($candidate_post_id, 'file_name', true);
															get_post_meta($candidate_post_id, 'apply_jod_id', true);
															$useridpdf=get_post_meta($candidate_post_id, 'user_id', true);
															if(get_post_meta($candidate_post_id, 'user_id', true)!=''){ ?>
															<a target="_blank" href="?&jobboardpdfcv=<?php echo esc_html($useridpdf);?>"> <i class="far fa-file-alt"></i> <?php esc_html_e( 'Print PDF', 'jobboard' );?></a>
															<?php
															}else{?>
															<a target="_blank" href="<?php echo esc_url(get_post_meta($candidate_post_id,'cv_file_url',true)); ?>"> <i class="far fa-file-alt"></i> <?php esc_html_e( 'Print PDF', 'jobboard' );?></a>
															<?php
															}
														?>
													</li>
													<li>
														<a onclick="candidate_meeting_popup('<?php echo esc_attr($candidate_post_id);?>')">
															<i class="fas fa-user-clock"></i>													
															<?php esc_html_e( 'Meeting', 'jobboard' );?>
														</a>
													</li>
													<li>
														<a onclick="candidate_email_popup('<?php echo esc_attr($candidate_post_id);?>')">
															<i class="far fa-envelope"></i>
															<?php esc_html_e( 'Message', 'jobboard' );?>
														</a>
													</li>
													<li>
														<a  onclick="candidate_reject('<?php echo esc_attr($candidate_post_id);?>','all')">
															<i class="fas fa-user-times"></i>
															<?php esc_html_e( 'Reject', 'jobboard' );?>
														</a>
													</li>
													<li>
														<a onclick="candidate_delete('<?php echo esc_attr($candidate_post_id);?>')">
															<i class="far fa-trash-alt"></i>
															<?php esc_html_e( 'Delete', 'jobboard' );?>
														</a>
													</li>
												</ul>
											</div>
										</div>
										<div class="row mt-3">
											<div class="col-md-12">
												<div class="meta-job">
													<span class="location">
														<i class="fa fa-check-circle"></i>
														<?php esc_html_e( 'Applied For : ', 'jobboard' );?>
														<?php
															
															$post_edit = get_post($job_post_id);
														?>
														<a href="<?php echo get_the_permalink($job_post_id); ?>">
															<?php
																echo esc_html($post_edit->post_title);
															?>
														</a>
													</span>
												</div>
												<div class="meta-job">
													<span class="location">
														<i class="fas fa-clock"></i>
														<?php esc_html_e( 'Meeting Time : ', 'jobboard' ); ?>
														<?php
															echo date('M d, Y h:m a', 
															strtotime(get_post_meta($candidate_post_id,'candidate_schedule_time', true )));
														?>
													</span>
												</div>
												<div class="meta-job">
													<span class="location">
														<i class="fas fa-calendar-day"></i>
														<?php esc_html_e( 'Applied : ', 'jobboard' );?>
														<?php
															echo get_the_time('M d, Y h:m a', $candidate_post_id);
														?>
													</span>
												</div>
												<div class="meta-job">
													<span class="location">
														<i class="fas fa-envelope"></i>
														<?php esc_html_e( 'Email : ', 'jobboard' );?>
														<?php
															if($the_query->post->post_author < 1){
																echo  get_post_meta($candidate_post_id, 'email_address', true);
																}else{
																echo esc_html($userinfo_data->user_email) ;
															}
														?>
													</span>
												</div>
												<div class="meta-job">
													<span class="location">
														<i class="fas fa-phone-volume"></i>
														<?php esc_html_e( 'Phone : ', 'jobboard' );?>
														<?php
															if($the_query->post->post_author < 1){
																echo  get_post_meta($candidate_post_id, 'phone', true);
																}else{
																echo esc_html(get_user_meta($the_query->post->post_author,'phone',true));
															}
														?>
													</span>
												</div>
												<div class="group-button mt-3">
													<?php
														$upload_dir = wp_upload_dir();
														$file_name=get_post_meta($candidate_post_id, 'file_name', true);
														get_post_meta($candidate_post_id, 'apply_jod_id', true);
														$useridpdf=get_post_meta($candidate_post_id, 'user_id', true);
														if(get_post_meta($candidate_post_id, 'user_id', true)!=''){ ?>
														<a target="_blank" href="?&jobboardpdfcv=<?php echo esc_html($useridpdf);?>" class="btn btn-light" data-toggle="tooltip" title="<?php esc_html_e( 'Print PDF', 'jobboard' );?>"> <i class="far fa-file-alt"></i></a>
														<?php
														}else{?>
														<a target="_blank" href="<?php echo esc_url(get_post_meta($candidate_post_id,'cv_file_url',true)); ?>" class="btn btn-light" data-toggle="tooltip" title="<?php esc_html_e( 'Print PDF', 'jobboard' );?>"> <i class="far fa-file-alt"></i></a>
														<?php
														}
													?>
													<span id="shortlistonly<?php echo esc_attr($candidate_post_id); ?>">
														<?php
															if( get_post_meta($candidate_post_id, 'candidate_shortlisted', true)=='yes'){
															?>
															<button id="" class="btn btn-light <?php echo (get_post_meta($candidate_post_id, 'candidate_shortlisted', true)=='yes'?' shortlisted':''); ?>" data-toggle="tooltip"  onclick="candidate_shortlisted_delete('<?php echo esc_attr($candidate_post_id);?>','only')" title="<?php esc_html_e( 'Short Listed', 'jobboard' );?>" ><i class="fas fa-user-check"></i></button>
															<?php
																}else{
															?>
															<button id="" class="btn btn-light <?php echo (get_post_meta($candidate_post_id, 'candidate_shortlisted', true)=='yes'?' shortlisted':''); ?>" data-toggle="tooltip"  onclick="candidate_shortlisted('<?php echo esc_attr($candidate_post_id);?>','only')" title="<?php esc_html_e( 'Make Short Listed', 'jobboard' );?>" ><i class="fas fa-user-check"></i></button>
															<?php
															}
														?>
													</span>
													<button id="schedulebutton<?php echo esc_attr($candidate_post_id);?>"class="btn btn-light <?php echo (get_post_meta($candidate_post_id, 'candidate_schedule', true)=='yes'?' shortlisted':''); ?>" onclick="candidate_meeting_popup('<?php echo esc_attr($candidate_post_id);?>')" data-toggle="tooltip" title="<?php esc_html_e( 'Meeting Schedule', 'jobboard' );?>" ><i class="fas fa-user-clock"></i></button>
													<button class="btn btn-light btn-email" onclick="candidate_email_popup('<?php echo esc_attr($candidate_post_id);?>')" ><i class="far fa-envelope"></i></button>
													<span id="rejectshortlisted<?php echo esc_attr($candidate_post_id); ?>">
														<?php
															if( get_post_meta($candidate_post_id, 'candidate_reject', true)=='yes'){
															?>
															<button class="btn btn-light btn-delete  shortlisted" onclick="candidate_reject_delete('<?php echo esc_attr($candidate_post_id);?>','shortlisted')" data-toggle="tooltip" title="<?php esc_html_e( 'Undo Reject', 'jobboard' );?>"><i class="fas fa-user-times"></i></button>
															<?php
																}else{
															?>
															<button class="btn btn-light btn-delete " onclick="candidate_reject('<?php echo esc_attr($candidate_post_id);?>','shortlisted')" data-toggle="tooltip" title="<?php esc_html_e( 'Make Reject', 'jobboard' );?>"><i class="fas fa-user-times"></i></button>
															<?php
															}
														?>
													</span>	
													<button class="btn btn-light btn-delete" onclick="candidate_delete('<?php echo esc_attr($candidate_post_id);?>')" data-toggle="tooltip" title="<?php esc_html_e( 'Delete', 'jobboard' );?>"><i class="far fa-trash-alt"></i></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</td>
				</tr>
				<?php
				}
					endwhile;
				?>
				<?php
				}
			?>		
		</table>
	</div>		