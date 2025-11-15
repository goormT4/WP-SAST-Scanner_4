<?php
	wp_enqueue_media(); 
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	$cpt_category_link=get_admin_url().'edit-tags.php?taxonomy='.$directory_url.'-category&post_type='.$directory_url;
?>
<h3  class=""><?php esc_html_e('Category Image / Map Marker','jobboard'); ?>  <small><a href="<?php echo esc_attr($cpt_category_link);?>" class="btn btn-info btn-xs"><?php esc_html_e('Create New Caterory','jobboard'); ?></a></small>	
</h3>
<br/>
<div id="update_message"> </div>	
<form class="form-horizontal" role="form"  name='map_marker' id='map_marker'>
	<div class="row ">
		<div class="col-md-12 ">
			<table class="table table-bordered table-hover table-responsive">												  
				<thead>
					<tr>
						<th><?php esc_html_e('Category Main','jobboard'); ?>  </th>
						<th><?php esc_html_e('Sub Category','jobboard'); ?>  </th>				 
						<th><?php esc_html_e('Category Image','jobboard'); ?> </th>
					</tr>
				</thead>
				<tbody>
					<?php				
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
						'parent'            => '0',
						'hierarchical'      => true, 
						'child_of'          => 0,
						'childless'         => false,
						'get'               => '', 
						);
						$terms = get_terms($taxonomy,$args); // Get all terms of a taxonomy
						if ( $terms && !is_wp_error( $terms ) ) :
						foreach ( $terms as $term ) {  ?>
						<tr>							  
							<th>							
								<?php echo strtoupper($term->name);  ?>									
								<input type="hidden" name="<?php echo esc_attr($term->slug);?>" id="<?php echo esc_attr($term->slug);?>" value="<?php echo esc_attr($term->term_id);?>">
							</th>
							<th>
							</th>
							<th> 
								<div id="cate_<?php echo esc_html($term->term_id);?>" class="">
									<?php
										$marker = get_option('epjbcate_main_image_'.$term->term_id);
										if($marker!=''){
											echo wp_get_attachment_image($marker);																	
										}else{ ?>
										<?php									
										}
									?>
								</div>	
								<br/>
								<button type="button" onclick="change_cate_image('<?php echo esc_html($term->term_id);?>');"  class="btn btn-success btn-xs"><?php esc_html_e('Set Image','jobboard');  ?></button>
							</th>
						</tr>
						<?php
							$category_id=$term->term_id;
							$args2 = array(
							'type'                     => $directory_url,
							'child_of'                 => $category_id,
							'parent'                   => '',
							'orderby'                  => 'name',
							'order'                    => 'ASC',
							'hide_empty'               => 0,
							'hierarchical'             => 1,
							'exclude'                  => '',
							'include'                  => '',
							'number'                   => '',
							'taxonomy'                 => $directory_url.'-category',
							'pad_counts'               => false 
							); 											
							$categories = get_categories( $args2 );
							if ( $categories && !is_wp_error( $categories ) ) :										
							foreach ( $categories as $term_sub ) { ?>
							<tr>							  
								<th>	
								</th>
								<th>
									<?php echo esc_html($term_sub->name);  ?>		
								</th>
								<th> 
									<div id="marker_<?php echo esc_attr($term_sub->term_id);?>" class="col-md-2">
										<?php
											$marker = get_option('epjbcat_map_marker_'.$term_sub->term_id);
											if($marker!=''){
												echo wp_get_attachment_image($marker);																	
											}else{ ?>
											<img  width="20px" src="<?php echo  wp_jobboard_URLPATH."assets/images/map-marker/map-marker.png";?>">	
											<?php									
											}
										?>
									</div>	
									<button type="button" onclick="change_marker_image('<?php echo esc_attr($term_sub->term_id);?>');"  class="btn btn-success btn-xs"><?php esc_html_e('Change Image','jobboard');  ?></button>
								</th>	
								<th> 
									<div id="cate_<?php echo esc_attr($term_sub->term_id);?>" class="">
										<?php
											$marker = get_option('epjbcate_main_image_'.$term_sub->term_id);
											if($marker!=''){
												echo wp_get_attachment_image($marker);																	
											}else{ ?>
											<?php									
											}
										?>
									</div>
									<br/>	
									<br/>	
									<button type="button" onclick="change_cate_image('<?php echo esc_attr($term_sub->term_id);?>');"  class="btn btn-success btn-xs"><?php esc_html_e('Set Image','jobboard');  ?></button>
								</th>
							</tr>
							<?php
							} 	
							endif;			
						} 								
						endif;	
					?>
				</tbody>
			</table>  
		</div> 
	</div>	 
</form>