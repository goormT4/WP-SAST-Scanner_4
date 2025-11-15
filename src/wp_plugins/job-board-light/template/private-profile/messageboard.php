<main class="main-content">
	<h4 class="lighter-heading border-btm"><?php  esc_html_e('Message','jobboard');?>  </h4>
	<section class="content-main-right list-jobs mb-30">
		<div class="list">
			<?php
							
				$args = array(
				'post_type' => 'jobboard_message', 
				'post_status' => 'private',
				'posts_per_page'=> '-1',
				'orderby' => 'date',
				'order'   => 'DESC',
				);
						
				$user_to = array(
				'relation' => 'AND',
				array(
				'key'     => 'user_to',
				'value'   => $current_user->ID,
				'compare' => '='
				),
				);			
				$args['meta_query'] = array(
					$user_to,
					);
				$the_query = new WP_Query( $args );
				
				
				?>
				
				<table id="candidate-bookmark" class="table tbl-epmplyer-bookmark" >
					<thead>
						<tr class="">
							<th><?php  esc_html_e('Message','jobboard');?></th>
						</tr>
					</thead>
					<?php
						$i=0;
						if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) : $the_query->the_post();					
						$id = get_the_ID();
												
						?>
						<tr id="companybookmark_<?php echo esc_html(trim($id));?>" >
							<td class="d-md-table-cell">
								<div class="job-item bookmark">
									<div class="row align-items-center">										
										<div class="col-md-12 job-info px-0">
											<div class="text px-0 text-left">	
												<h4 class="title-job"><?php echo esc_html($the_query->post->post_title); ?>
												</h4>	
												<div class="title-job"><span class="location"><i class="fas fa-calendar-day"></i><span class="p-2"><?php  echo get_the_time('M d, Y h:m a', $id); ?></span></span>
												</div>
												<div class="title-job"><span class="location"><i class="far fa-envelope"></i><span class="p-2"><?php  echo esc_html(get_post_meta( $id, 'from_email', true)); ?></span></span> <i class="fas fa-phone-volume"></i><span class="p-2"> <?php  esc_html_e('Phone','jobboard');?> : <?php echo esc_attr(get_post_meta($id,'from_phone',true)); ?></span></div>	
												<?php
												if(get_post_meta($id,'dir_url',true)!=''){
												?>
												<div class="title-job"><?php  esc_html_e('Job Listing','jobboard');?> : <?php  echo esc_url(get_post_meta( $id, 'dir_url', true)); ?></div>
												<?php
												}
												?>
												
												
												<div class="title-job">
												<?php												
														echo do_shortcode($the_query->post->post_content);
													?>
													
												</div>			
												
												<div class="group-button">														
													<button class="btn btn-light btn-delete" onclick="delete_message_myaccount('<?php echo esc_attr($id);?>','companybookmark')"><i class="far fa-trash-alt"></i></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
						<?php
							
						endwhile;
						}	
					?>
				</table>
		</div>
	</section>
</main>