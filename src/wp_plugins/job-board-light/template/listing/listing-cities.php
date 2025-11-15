<?php
  wp_enqueue_style('bootstrap-jobboard-110', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
  wp_enqueue_style('jobboard-style-city', wp_jobboard_URLPATH . 'admin/files/css/styles.css');
	
	
	global $post,$wpdb,$tag;
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	$post_limit='9999';
	if(isset($atts['post_limit']) and $atts['post_limit']!="" ){
		$post_limit=$atts['post_limit'];
	}
	$cities='';
	if(isset($atts['cities']) and $atts['cities']!="" ){
		$cities=$atts['cities'];
	}
	if($cities==''){
		$dir_search_city=get_option('epjbdir_search_city');
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
			$new_city=get_post_meta($term->ID,'city',true);
			if (!in_array($new_city, $get_cityies)) {
				$get_cityies[]=$new_city;
			}
		}
		}else{
		$get_cityies= explode(',',$cities);
	}
?>
<section id="destination background-transparent" >
	<section class="bootstrap-wrapper background-transparent" >
		<div class="container dynamic-bg-city">
			<div class="row">
				<?php
					if(count($get_cityies)) {
						asort($get_cityies);
						$i=1;
						foreach($get_cityies as $row1) {
							if($row1!=''){
								if($i<=$post_limit){
								?>
								<div class="col-md-4 mb-5">
									<div class="img_overlay_container">
										<div class="img_overlay rounded mr-5"></div>
										<a href="<?php echo get_post_type_archive_link( $directory_url ).'?dir_city='.esc_attr(strtolower(trim($row1))); ?>">
											<?php
												$attach_id= get_option('city_main_image_'.str_replace(' ','-',strtolower(trim($row1))));
												if($attach_id!=''){
													$img_src= wp_get_attachment_image_src($attach_id,'large');
													if(isset($img_src[0])){
													?>
													<img   src="<?php echo esc_url($img_src[0]);?>" class="rounded cities_img w-100 img-fluid">
													<?php
													}
												}else{?>
												<img   src="<?php echo  wp_jobboard_URLPATH."assets/images/city.jpg";?>" class="rounded cities_img w-100 img-fluid">
												<?php
												}
											?>
										</a>
										<h6 class="cities_title text-center text-white"><?php echo esc_html($row1);?></h6>
									</div>
								</div>
								<?php
								}
								$i++;
							}
						}
					}
				?>
			</div>
		</div>
	</section>
</section>