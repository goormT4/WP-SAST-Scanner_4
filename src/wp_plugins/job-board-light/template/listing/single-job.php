<?php
	get_header();
	wp_enqueue_script("jquery");
	wp_enqueue_style('bootstrap-jobboard-110', 			wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	wp_enqueue_style('all', 			wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
	wp_enqueue_style('jquery.fancybox', wp_jobboard_URLPATH . 'admin/files/css/jquery.fancybox.css');
	wp_enqueue_style('colorbox', wp_jobboard_URLPATH . 'admin/files/css/colorbox.css');
	wp_enqueue_script('colorbox', wp_jobboard_URLPATH . 'admin/files/js/jquery.colorbox-min.js');	
	wp_enqueue_script('jquery.fancybox',wp_jobboard_URLPATH . 'admin/files/js/jquery.fancybox.js');
	/**************************** css resources from qdesk ********************************************/
	wp_enqueue_style('main-css', wp_jobboard_URLPATH . 'admin/files/css/main.css');
	wp_enqueue_style('single-job', wp_jobboard_URLPATH . 'admin/files/css/single-job.css');
	/*************************************************************************************************************/
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	global $post,$wpdb, $current_user;
	$jobid = get_the_ID();
	$post_id_1 = get_post($jobid);
	$post_id_1->post_title;
	$wp_directory= new wp_jobboard();
	while ( have_posts() ) : the_post();
	if(has_post_thumbnail()){
		$feature_image = wp_get_attachment_image_src( get_post_thumbnail_id( $jobid ), 'large' );
		if(isset($feature_image[0])){
			$feature_img =$feature_image[0];
		}
		}else{
		$feature_img= wp_jobboard_URLPATH."assets/images/job.png";
	}
	$currentCategory=wp_get_object_terms( $jobid, $directory_url.'-category');
	$cat_name2='';
	if(isset($currentCategory[0]->name)){
		$cat_name2 = $currentCategory[0]->name;
		$cc=0;
		foreach($currentCategory as $c){
			if($cc==0){
				$cat_name2 =$c->name;
				}else{
				$cat_name2 = $cat_name2 .', '.$c->name;
			}
			$cc++;
		}
	}
	$listing_contact_source=get_post_meta($jobid,'listing_contact_source',true);
	if($listing_contact_source==''){$listing_contact_source='user_info';}
	if($listing_contact_source=='new_value'){
		$company_logo='';
		}else{
		$company_logo='';
	}
	// View Count***
	$current_count=get_post_meta($jobid,'job_views_count',true);
	$current_count=(int)$current_count+1;
	update_post_meta($jobid,'job_views_count',$current_count);
?>
<!-- SLIDER SECTION -->
<div class="bootstrap-wrapper">	
	<div class="container pt-4">
		<div class="item-detail-special contentshowep">
			<div class="img col-md-2">
				<img src="<?php echo esc_url($feature_img); ?>" class="img-fluid" alt="Image">
			</div>
			<div class="text p-3">
				<div class="row align-items-lg-center">
					<div class="col-lg-7 col-xl-8  ">
						<h2 class="title-detail"><?php echo get_the_title($jobid); ?></h2>
						<?php
							if($listing_contact_source=='new_value'){ ?>
							<p class="date-style2"><span class="company"><i class="fa fa-check-circle"></i><?php echo esc_attr(get_post_meta($jobid, 'company_name',true));?> </span><span class="location"><i class="far fa-map"></i>
							<?php echo esc_attr(get_post_meta($jobid, 'address',true));?></span></p>
							<p class="meta-job"><span class="website"><i class="fa fa-link"></i> <?php echo esc_attr(get_post_meta($jobid, 'contact_web',true));?> </span><span class="phone"><i class="fa fa-mobile-alt"></i><?php echo esc_attr(get_post_meta($jobid, 'phone',true));?> </span><span class="mail"><i class="far fa-envelope"></i><?php echo esc_attr(get_post_meta($jobid, 'contact-email',true));?></span></p>
							<?php
								}else{
							?>
							<p class="date-style2"><span class="company"><i class="fa fa-check-circle"></i>
								<?php echo esc_html(get_user_meta($current_user->ID,'full_name', true)); ?>
							</span><span class="location"><i class="far fa-map"></i><?php echo esc_attr(get_user_meta($current_user->ID,'Address', true)); ?>  <?php echo esc_attr(get_user_meta($current_user->ID,'city', true)); ?>  <?php echo esc_attr(get_user_meta($current_user->ID,'zipcode', true)); ?> ,<?php echo get_user_meta($current_user->ID,'country', true); ?></span></p>
							<p class="meta-job"><span class="website"><i class="fa fa-link"></i><?php echo esc_attr(get_user_meta($current_user->ID,'website', true)); ?></span><span class="phone"><i class="fa fa-mobile-alt"></i><?php echo esc_attr(get_user_meta($current_user->ID,'phone', true)); ?> </span><span class="mail"><i class="far fa-envelope"></i><?php echo esc_html($current_user->user_email); ?></span></p>
							<?php
							}
						?>
					</div>
					<div class="col-lg-5 col-xl-4">
						<div class="btn-feature">
							<?php
								$user_ID = get_current_user_id();
								$favourites='no';
								if($user_ID>0){
									$my_favorite = get_post_meta($id,'_favorites',true);
									$all_users = explode(",", $my_favorite);
									if (in_array($user_ID, $all_users)) {
										$favourites='yes';
									}
								}
							?>
							<?php
								$job_apply='no';
								$user_ID = get_current_user_id();
								$job_apply_all = get_user_meta($user_ID,'job_apply_all',true);
								$job_apply_all = explode(",", $job_apply_all);
								if (in_array($jobid, $job_apply_all)) {
									$job_apply='yes';
								}										
							?>
							<?php
								$jobboard_apply=get_option('jobboard_apply');	
								if($jobboard_apply==""){$jobboard_apply='yes';}
								if($jobboard_apply=="yes"){										
								?>
								<button onclick="apply_popup('<?php echo esc_attr($jobid);?>')" class="btn btn-light">
									<?php 
										if($job_apply=='yes'){?>
										<i class="fa fa-check-circle"></i>
										<?php
										}
									?>
								<?php esc_html_e('Apply Now','jobboard'); ?></button>	
								<?php
								}
							?>
							<?php
								$jobboard_single_pdf=get_option('jobboard_single_pdf');	
								if($jobboard_single_pdf==""){$jobboard_single_pdf='yes';}
								if($jobboard_single_pdf=="yes"){
								?>
								<a class="btn btn-light-green" href="<?php echo get_permalink();?>?&jobboardpdfpost=<?php echo esc_attr($jobid);?>" target="_blank"><i class="fas fa-download"></i> <?php esc_html_e('PDF', 'jobboard'); ?></a>
								<?php
								}
							?>
							<?php
								$jobboard_single_bookmark=get_option('jobboard_single_bookmark');	
								if($jobboard_single_bookmark==""){$jobboard_single_bookmark='yes';}
								if($jobboard_single_bookmark=="yes"){
								?>
								<span id="fav_dir<?php echo esc_html($jobid); ?>">
									<?php
										if($favourites=='yes'){ ?>
										<a class="btn btn-added-favourites" title="<?php esc_html_e('Added to Favorites','jobboard'); ?>" href="javascript:;" onclick="save_unfavorite('<?php echo esc_attr($jobid); ?>')" >
											<i class="far fa-star" ></i>
										</a>
										<?php
											}else{
										?>
										<a class="btn btn-light btn-add-favourites" title="<?php esc_html_e('Add to Favorites','jobboard'); ?>" href="javascript:;" onclick="save_favorite('<?php echo esc_attr($jobid); ?>')" >
											<i class="far fa-star" ></i>
										</a>
										<?php
										}
									?>
								</span>
								<?php
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 order-lg-12 mb-4 mb-lg-0">
				<div class="sidebar-right contentshowep">
					<div class="sidebar-right-group">
						<div class="job-detail-summary">
							<h3 class="title-block"><?php esc_html_e('Job Summary', 'jobboard'); ?></h3>
							<ul>
								<li class="row">
									<span class="col-6 col-md-6"><?php esc_html_e('Published on', 'jobboard'); ?></span>
									<span class="col-1 col-md-1 px-0">:</span>
									<div class="col-5 col-md-5 px-0"><?php echo get_the_date('M d, Y', $jobid); ?></div>
								</li>
								<?php
									if(get_post_meta($jobid,'vacancy', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Vacancy', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr( get_post_meta($jobid,'vacancy', true)); ?></div>
									</li>
									<?php
									}
								?>
								<?php
									if(get_post_meta($jobid,'job_type', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Employment Status', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr(get_post_meta($jobid,'job_type', true)); ?></div>
									</li>
									<?php
									}
								?>
								<?php
									if(get_post_meta($jobid,'experience_range', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Experience', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr(get_post_meta($jobid,'experience_range', true)); ?></div>
									</li>
									<?php
									}
									?><?php
									if(get_post_meta($jobid,'salary', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Salary', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr(get_post_meta($jobid,'salary', true)); ?></div>
									</li>
									<?php
									}
								?>
								<?php
									if(get_post_meta($jobid,'gender', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Gender', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr(get_post_meta($jobid,'gender', true)); ?></div>
									</li>
									<?php
									}
								?>
								<?php
									if(get_post_meta($jobid,'job_level', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Career Level', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr(get_post_meta($jobid,'job_level', true)); ?></div>
									</li>
									<?php
									}
								?>
								<?php
									if(get_post_meta($jobid,'educational_requirements', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Qualification', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo esc_attr(get_post_meta($jobid,'educational_requirements', true)); ?></div>
									</li>
									<?php
									}
								?>
								<li class="row">
									<span class="col-6 col-md-6"><?php esc_html_e('Industry', 'jobboard'); ?></span>
									<span class="col-1 col-md-1 px-0">:</span>
									<div class="col-5 col-md-5 px-0">
										<?php
											$currentCategory=wp_get_object_terms( $jobid, $directory_url.'-category');
											if(isset($currentCategory[0]->slug)){
												$cat_slug = $currentCategory[0]->slug;
												$cat_name = $currentCategory[0]->name;
												$cc=0;
												foreach($currentCategory as $c){
													echo' <p><a style="text-decoration: none;" href="'.get_tag_link($c->term_id) .'">'.$c->name.'</a></p>';
												}
											}
										?>
									</div>
								</li>
								<?php
									if(get_post_meta($jobid,'deadline', true)!=''){
									?>
									<li class="row">
										<span class="col-6 col-md-6"><?php esc_html_e('Deadline', 'jobboard'); ?></span>
										<span class="col-1 col-md-1 px-0">:</span>
										<div class="col-5 col-md-5 px-0"><?php echo date('M d, Y', strtotime(esc_attr(get_post_meta($jobid,'deadline', true)))); ?></div>
									</li>
									<?php
									}
								?>
							</ul>
							<?php
								$jobboard_apply=get_option('jobboard_apply');	
								if($jobboard_apply==""){$jobboard_apply='yes';}
								if($jobboard_apply=="yes"){										
								?>
								<div class="col-12  mt-0 text-center">
									<button onclick="apply_popup('<?php echo esc_attr($jobid);?>')" class="btn btn-light-green">
										<?php 
											if($job_apply=='yes'){?>
											<i class="fa fa-check-circle"></i>
											<?php
											}
										?>
									<?php esc_html_e('Apply Now','jobboard'); ?></button>	
								</div>
								<?php
								}
							?>
						</div>
						<?php
							$dir_map=get_option('job_dir_map');
							if($dir_map==""){$dir_map='yes';}
							if($dir_map=='yes'){ ?>
							<div class="side-right-map">
								<?php										
									$address=get_post_meta($jobid,'address',true).'+'.get_post_meta($jobid,'city',true).'+'.get_post_meta($jobid,'postcode',true).'+'.get_post_meta($jobid,'country',true);
								?>
								<h3 class="title-block"><?php esc_html_e('Job Location', 'jobboard'); ?></h3>
								<iframe width="100%" height="325" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=<?php echo esc_attr($address); ?>&amp;ie=UTF8&amp;&amp;output=embed"></iframe>
							</div>
							<?php
							}
						?>
						<?php
							$jooboard_single_tag=get_option('jooboard_single_tag');	
							if($jooboard_single_tag==""){$jooboard_single_tag='yes';}
							if($jooboard_single_tag=="yes"){
							?>	
							<div class="side-right-social side-right-tags">
								<h3 class="title-block"><?php esc_html_e('Tags', 'jobboard'); ?></h3>
								<ul>
									<?php
										$tag_array= wp_get_object_terms( $jobid,  $directory_url.'_tag');
										foreach($tag_array as $one_tag){
											echo'<li><a href="'.get_tag_link($one_tag->term_id) .'">'.esc_attr($one_tag->name).'</a>';
										}
									?>
								</ul>
							</div>
							<?php
							}
						?>
						<?php
							$dir_share=get_option('epjbdir_share');	
							if($dir_share==""){$dir_share='yes';}
							if($dir_share=="yes"){
							?>
							<div class="side-right-social">
								<h3 class="title-block"><?php esc_html_e('Share This Job', 'jobboard'); ?></h3>
								<ul>
									<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo  get_the_permalink($jobid );?>"><i class="fab fa-facebook-f"></i></a></li>
									<li><a href="mailto:info@example.com?&subject=&body=<?php echo  get_the_permalink($jobid ); ?> <?php echo  get_the_title($jobid); ?>"><i class="fas fa-envelope"></i></a></li>
									<li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo  get_the_permalink($jobid); ?>&title=&summary=<?php echo  get_the_title($jobid ); ?>&source="><i class="fab fa-linkedin-in"></i></a></li>
									<li><a href="https://pinterest.com/pin/create/button/?url=<?php echo  get_the_permalink($jobid ); ?>&media=&description=<?php echo  get_the_title($jobid ); ?>"><i class="fab fa-pinterest-p"></i></a></li>
									<li><a href="https://twitter.com/home?status=<?php echo  get_the_permalink($jobid );echo  get_the_title($jobid );?>"><i class="fab fa-twitter"></i></a></li>
								</ul>
							</div>
							<?php
							}
						?>
						<!-- Go to www.addthis.com/dashboard to customize your tools -->
						<div class="addthis_inline_share_toolbox"></div>								
					</div>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="content-main-right single-detail contentshowep">
					<?php
						if(get_post_meta($jobid,'vacancy', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Vacancy', 'jobboard'); ?> : <?php echo esc_html(get_post_meta($jobid,'vacancy', true)); ?> </h4>
						<?php
						}
					?>
					<h4 class="title-job"><?php esc_html_e('Job Description', 'jobboard'); ?></h4>
					<p>
						<?php
							$content_post = get_post($jobid);
							$content = $content_post->post_content;
							$content = apply_filters('the_content', $content);
							$content = str_replace(']]>', ']]&gt;', $content);
							echo do_shortcode($content);
						?>
					</p>
					<?php
						if(get_post_meta($jobid,'job_education', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Education & Experience', 'jobboard'); ?></h4>
						<p>
							<?php
								$content=get_post_meta($jobid,'job_education', true);
								$content = apply_filters('the_content', $content);
								$content = str_replace(']]>', ']]&gt;', $content);
								echo do_shortcode($content);
							?>
						</p>
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'job_must_have', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Must Have', 'jobboard'); ?></h4>
						<p>
							<?php
								$content=get_post_meta($jobid,'job_must_have', true);
								$content = apply_filters('the_content', $content);
								$content = str_replace(']]>', ']]&gt;', $content);
								echo do_shortcode($content);
							?>
						</p>
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'job_type', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Employment Status', 'jobboard'); ?></h4>
						<p>
							<?php echo esc_attr(get_post_meta($jobid,'job_type', true)); ?>
						</p>
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'educational_requirements', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Educational Requirements', 'jobboard'); ?></h4>
						<p>
							<?php echo esc_html(get_post_meta($jobid,'educational_requirements', true)); ?>
						</p>	
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'experience_range', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Experience Requirements', 'jobboard'); ?></h4>
						<p>
							<?php echo esc_attr(get_post_meta($jobid,'experience_range', true)); ?>
						</p>
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'workplace', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Job Location', 'jobboard'); ?></h4>
						<p>
							<?php echo esc_attr(get_post_meta($jobid,'workplace', true)); ?>
						</p>
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'salary', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Salary', 'jobboard'); ?></h4>
						<p>
							<?php echo esc_attr(get_post_meta($jobid,'salary', true)); ?>
						</p>
						<?php
						}
					?>
					<?php
						if(get_post_meta($jobid,'other_benefits', true)!=''){
						?>
						<h4 class="title-job"><?php esc_html_e('Compensation & Other Benefits', 'jobboard'); ?></h4>
						<p>
							<?php echo esc_attr(get_post_meta($jobid,'other_benefits', true)); ?>
						</p>
						<?php
						}
					?>
					<!-- video section -->
					<?php
						$dir_video=get_option('job_dir_video');
						if($dir_video==""){$dir_video='yes';}
						if($dir_video=='yes'){
							$video_vimeo_id= esc_attr(get_post_meta($id,'vimeo',true));
							$video_youtube_id=esc_attr(get_post_meta($id,'youtube',true));
							if($video_vimeo_id!='' || $video_youtube_id!=''){
							?>
							<?php
								$v=0;
								$video_vimeo_id= get_post_meta($id,'vimeo',true);
								if($video_vimeo_id!=""){ $v=$v+1; ?>
								<p><iframe src="<?php echo esc_url('//player.vimeo.com/video/');?><?php echo esc_attr($video_vimeo_id); ?>" width="100%" height="415px" class="w-100" frameborder="0"></iframe></p>
								<?php
								}
							?>
							<?php
								$video_youtube_id=get_post_meta($id,'youtube',true);
								if($video_youtube_id!=""){
									echo($v==1?'<hr>':'');
								?>
								<p><iframe width="100%" height="415px" src="<?php echo esc_url('//www.youtube.com/embed/');?><?php echo esc_attr($video_youtube_id); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-100"></iframe></p>
								<?php
								}
							}
						}
					?>
					<!-- end of video section -->
					<?php
						$jobboard_dir_images=get_option('jobboard_dir_images');	
						if($jobboard_dir_images==""){$jobboard_dir_images='yes';}
						if($jobboard_dir_images=="yes"){
						?>
						<div class="row">
							<?php
								$gallery_ids=get_post_meta($id ,'image_gallery_ids',true);
								$gallery_ids_array = array_filter(explode(",", $gallery_ids));
								$i=1;
								foreach($gallery_ids_array as $slide){
									if($slide!=''){ ?>
									<div class=" p-2  col-md-3">
										<a data-fancybox="gallery" href="<?php echo wp_get_attachment_url( $slide ); ?>">
											<img class="img-fluid rounded float" src="<?php echo wp_get_attachment_url( $slide ); ?>" >
										</a>
									</div>
									<?php
										$i++;
									}
								}
								//image_gallery_urls
								$gallery_urls=get_post_meta($id ,'image_gallery_urls',true);
								$gallery_urls_array = array_filter(explode(",", $gallery_urls));
								foreach($gallery_urls_array as $slide){
									if($slide!=''){ ?>
									<div class="p-2  col-md-3">
										<a data-fancybox="gallery" href="<?php echo esc_attr($slide); ?>">
											<img class="img-fluid rounded float" src="<?php echo esc_attr($slide); ?>">
										</a>
									</div>
									<?php
										$i++;
									}
								}
							?>
						</div>
						<?php
						}
					?>
				</div>
				<?php
					$similar_directories=get_option('epjbsimilar_job');
					if($similar_directories==""){$similar_directories='yes';}
					if($similar_directories=="yes"){
						$jobboard_similar = get_posts(array(
						'numberposts'	=> '3',
						'post_type'		=> $directory_url,
						'post__not_in' => array(esc_html($jobid)),
						'post_status'	=> 'publish',
						'orderby'		=> 'rand',
						));
						if ( ! empty( $jobboard_similar ) ) {
						?>
						<div class="simillar-jobs contentshowep">
							<h3 class="title-block pl-3"><?php esc_html_e('Simillar Jobs', 'jobboard'); ?></h3>
							<div class="content-main-right list-jobs">
								<div class="list">
									<?php
										$i=0;
										foreach( $jobboard_similar as $listing ){
											$listing_contact_source=get_post_meta($listing->ID,'listing_contact_source',true);
											if($listing_contact_source==''){$listing_contact_source='user_info';}
											$address='';
											if($listing_contact_source=='new_value'){
												$address= get_post_meta($jobid, 'address',true);
												}else{
												$address= get_user_meta($current_user->ID,'Address', true).', '. get_user_meta($current_user->ID,'city', true).', '. get_user_meta($current_user->ID,'zipcode', true).', '. get_user_meta($current_user->ID,'country', true);
											}
										?>
										<div class="job-item">
											<div class="row align-items-center">
												<div class="col-md-2">
													<?php
														if(has_post_thumbnail($listing->ID)){
															$feature_image = wp_get_attachment_image_src( get_post_thumbnail_id( $listing->ID ), 'large' );
															if(isset($feature_image[0])){
																$feature_img =$feature_image[0];
															}
															}else{
															$feature_img= wp_jobboard_URLPATH."assets/images/job.png";
														}
													?>
													<div class="img-job text-center"><a href="<?php echo get_the_permalink($listing->ID); ?>"><img src="<?php echo esc_attr($feature_img); ?>" class="img-fluid" alt="Image"></a>
													</div>
												</div>
												<div class="col-md-10 job-info">
													<div class="text">
														<h4 class="title-job"><a href="<?php echo  get_the_permalink($listing->ID );?>"><?php echo get_the_title($listing->ID); ?></a></h4>	
														<p class="date-job">
															<?php esc_html_e('Posted', 'jobboard'); ?>
															<?php echo date('M d, Y',strtotime($listing->post_date)); ?>
														</p>
														<p class="date-job"><span class="location"><i class="far fa-map"></i> <?php echo esc_html($address); ?></span></p>
													</div>
												</div>
											</div>
										</div>
										<?php
											$i++;
										}
									?>
								</div>
							</div>
						</div>
						<?php
						}
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php
	endwhile;
	wp_enqueue_script('jobboard-single-listing', wp_jobboard_URLPATH . 'admin/files/js/single-listing.js');
	wp_localize_script('jobboard-single-listing', 'jobboard_data', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'current_user_id'	=>get_current_user_id(),
	'Please_login'=>esc_html__('Please login', 'jobboard' ),
	'Add_to_Favorites'=>esc_html__('Add to Favorites', 'jobboard' ),
	'Added_to_Favorites'=>esc_html__('Added to Favorites', 'jobboard' ),
	'Please_put_your_message'=>esc_html__('Please put your name,email Cover letter & attached file', 'jobboard' ),
	'contact'=> wp_create_nonce("contact"),
	'listing'=> wp_create_nonce("listing"),
	'cv'=> wp_create_nonce("Doc/CV/PDF"),
	'wp_jobboard_URLPATH'=>wp_jobboard_URLPATH,
	) );
?>
<?php
	get_footer();
?>