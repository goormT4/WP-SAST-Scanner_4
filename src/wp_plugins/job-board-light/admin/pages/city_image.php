<?php
	wp_enqueue_media(); 
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
?>
<h3  class=""><?php  esc_html_e('Cities Images','jobboard'); ?>  <small><?php  esc_html_e('[Cities will get from your listings]','jobboard'); ?> </small>	
</h3>
<br/>
<div id="update_message-city"> </div>	
<form class="form-horizontal" role="form"  name='map_marker' id='map_marker'>
	<div class="row ">
		<div class="col-md-12 ">
			<table class="table table-bordered table-hover table-responsive">												  
				<thead>
					<tr>
						<th><?php  esc_html_e('City Name','jobboard'); ?>  </th>			
						<th><?php  esc_html_e('City Image','jobboard'); ?> </th>
					</tr>
				</thead>
				<tbody>			 
					<?php		
						// City
						$args_citys = array(
						'post_type'  => $directory_url,
						'posts_per_page' => -1,
						'meta_query' => array(
						array(
						'key'     => 'city',
						'orderby' => 'meta_value',
						'order' => 'ASC',
						),
						),
						);
						$citys = new WP_Query( $args_citys );
						$citys_all = $citys->posts;
						$get_cityies =array();
						foreach ( $citys_all as $term ) {
							$new_city="";
							$new_city=strtolower(get_post_meta($term->ID,'city',true));
							if (!in_array($new_city, $get_cityies)) {
								$get_cityies[]=$new_city;
							}
						}
						// City
						if(count($get_cityies)) {
							asort($get_cityies);
							foreach($get_cityies as $row1){
								if($row1!=''){ ?>
								<tr>							  
									<th>				
										<?php echo esc_html(ucfirst($row1));  ?>									
										<input type="hidden" name="<?php echo esc_attr(str_replace(' ','-',strtolower($row1)));?>" value="<?php echo str_replace(' ','-',strtolower($row1));?>">
									</th>					
									<th> 
										<div id="city_<?php echo str_replace(' ','-',strtolower($row1));?>" class="">
											<?php
												$marker = get_option('city_main_image_'.str_replace(' ','-',strtolower($row1)));
												if($marker!=''){
													echo wp_get_attachment_image($marker);																
												} 
											?>
										</div>	
										<br/>
										<button type="button" onclick="change_city_image('<?php echo str_replace(' ','-',strtolower($row1));?>');"  class="btn btn-success btn-xs">
										<?php   esc_html_e('Set Image','jobboard');?> </button>
									</th>
								</tr>
								<?php
								}
							}	
						}
					?>	
				</tbody>
			</table>  
		</div> 
	</div>	
</form>