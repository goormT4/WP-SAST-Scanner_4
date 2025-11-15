<?php
	$profile_url=get_permalink();
	global $current_user;
	$user = $current_user->ID;
	$message='';
	if(isset($_GET['delete_id']))  {
		$post_id=sanitize_text_field($_GET['delete_id']);
		$post_edit = get_post($post_id);
		if($post_edit){
			if($post_edit->post_author==$current_user->ID){
				wp_delete_post($post_id);
				delete_post_meta($post_id,true);
				$message=esc_html__("Deleted Successfully",'jobboard');
			}
			if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
				wp_delete_post($post_id);
				delete_post_meta($post_id,true);
				$message=esc_html__("Deleted Successfully",'jobboard');
			}
		}
	}
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	$main_class = new wp_jobboard;
?>
<main class="main-content">
	<div class="row lighter-heading border-btm mb-3">
		<div class="col-md-4"><h4 class=""><?php  esc_html_e('My Job Listings','jobboard');?>
		</h4> </div>
		<div class="col-md-6"><a class="btn btn-secondary btn-sm" href="<?php echo get_permalink(); ?>?&profile=new-post"><?php  esc_html_e('Add New Job','jobboard');?></a>
		</div>
	</div>	
	<section class="content-main-right list-jobs mb-30">
		<div class="list">
			<?php
				global $wpdb;
				
				if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
					$sql="SELECT * FROM $wpdb->posts WHERE post_type IN ('".$directory_url."')  and post_status IN ('publish','pending','draft' )  ORDER BY `ID` DESC";
					}else{
					$sql="SELECT * FROM $wpdb->posts WHERE post_type IN ('".$directory_url."')  and post_author='".$current_user->ID."' and post_status IN ('publish','pending','draft' )  ORDER BY `ID` DESC";
				}
				$authpr_post = $wpdb->get_results($sql);
				$total_post=count($authpr_post);
				if($total_post>0){
				?>
				<table id="job-manage" class="table tbl-job" >
					<thead>
						<tr class="">
							<th><?php  esc_html_e('Title','jobboard');?></th>
						</tr>
					</thead>
					<?php
						$i=0;
						foreach ( $authpr_post as $row )
						{
						?>
						<tr class="my-job-item">
							<td>
								<div class="align-item-center row">
									<div class="text-left col-md-9">
										<h4 class="title-job"><a href="<?php echo get_permalink($row->ID); ?>"><?php echo esc_html($row->post_title); ?></a></h4>
										<div class="meta-job"><span> <i class="fas fa-calendar-alt"></i>
											<?php  esc_html_e('Posted','jobboard');?>
										<?php echo date('M d, Y',strtotime($row->post_date)); ?></span>
										<?php
											$exp_date= get_user_meta($current_user->ID, 'job_exprie_date', true);
											if($exp_date!=''){
												$package_id=get_user_meta($current_user->ID,'jobboard_package_id',true);
												$dir_hide= get_post_meta($package_id, 'jobboard_package_hide_exp', true);
												if($dir_hide=='yes'){?>
												<span> <i class="fas fa-calendar-alt"></i>		
													<?php
														esc_html_e('Expiring','jobboard'); echo" : ";
														echo date('M d, Y',strtotime($exp_date));
													}?>
											</span>
											<?php
											}
										?>
										</div>
										<?php
											if(get_post_meta($row->ID,'salary', true)!=''){
											?>
											<div class="salary-job"><i class="fas fa-money-bill-alt"></i>
												<?php echo esc_html(get_post_meta($row->ID,'salary', true)); ?>
											</div>
											<?php
											}
										?>
										<div class="job-info">
											<span class="number-application"><i class="fas fa-folder-open"></i> 
												<?php
													echo esc_html($main_class ->jobboard_total_applications_count($row->ID));
												?>
												<?php  esc_html_e('Applications','jobboard');?> 
											</span>
											<?php
												if(get_post_meta($row->ID,'job_type', true)!=''){
												?>
												<span> <?php echo esc_html(get_post_meta($row->ID,'job_type', true)); ?> </span>
												<?php
												}
											?>
											<span class="active">
												<?php $post_ststus=get_post_status($row->ID);  
												echo ucfirst($post_ststus);  ?>
											</span>
										</div>
										<div class="job-info">
											<i class="fas fa-eye"></i>
											<?php  esc_html_e('View Count','jobboard');?> :
											<?php echo esc_attr(get_post_meta($row->ID,'job_views_count',true)); ?> 												
										</div>
									</div>
									<div class="job-func_manage_job col-md-3">
										<?php
											$edit_post= $profile_url.'?&profile=post-edit&post-id='.$row->ID;
										?>
										<a href="<?php echo esc_url($edit_post); ?>"  class="btn btn-light btn-edit mb-1 mb-lg-0" ><i class="fas fa-pencil-alt"></i></a>
										<a href="<?php echo esc_url($profile_url).'?&profile=all-post&delete_id='.$row->ID ;?>"  onclick="return confirm('Are you sure to delete this post?');"  class="btn btn-light btn-delete"><i class="far fa-trash-alt"></i>
										</a>
									</div>
								</div>
							</td>
						</tr>
						<?php
						}
					?>
				</table>
				<?php
				}
			?>
		</div>
	</section>
</main>