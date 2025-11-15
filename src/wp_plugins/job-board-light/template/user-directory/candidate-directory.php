<?php
	global $post;
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui');
	wp_enqueue_style('all-awesome', wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
	wp_enqueue_style('bootstrap-style-11', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');

	wp_enqueue_script('jobboard-script-user-directory', wp_jobboard_URLPATH . 'admin/files/js/user-directory.js');
	/**************************** css resources from qdesk ********************************************/
	wp_enqueue_style('flaticon', wp_jobboard_URLPATH . 'admin/files/css/flaticon-category.css');
	wp_enqueue_style('mCustomScrollbar', wp_jobboard_URLPATH . 'admin/files/css/jquery.mCustomScrollbar.css');
	
	wp_enqueue_style('main-css', wp_jobboard_URLPATH . 'admin/files/css/main.css');
	wp_enqueue_script('mCustomScrollbarJS', wp_jobboard_URLPATH . 'admin/files/js/jquery.mCustomScrollbar.concat.min.js');
	wp_enqueue_script('imagesloaded', wp_jobboard_URLPATH . 'admin/files/js/imagesloaded.pkgd.js');
	wp_enqueue_script('isotope', wp_jobboard_URLPATH . 'admin/files/js/isotope.pkgd.min.js');

	/**************************************************************************************************/
	$main_class = new wp_jobboard;
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
?>
<div class="bootstrap-wrapper wrapper" id="wrapper">
    <form method="GET" action="<?php echo get_permalink($post->ID); ?>">
		<main class="main-content">
			<div class="primary-page">
				<div class="container">
					<div class="header-page">
						<div class="row">
								<div class="col-md-6"></div>
								<div class="col-md-6 input-group mb-3 search-form mt-4">
									  <input type="text" class="form-control " name="user_name_search" value="<?php echo (isset($_REQUEST['user_name_search'])? esc_attr($_REQUEST['user_name_search']) :'');?>" placeholder="<?php esc_html_e('Search...','jobboard'); ?>" >
									  <div class="input-group-append">
										<button class="btn btn-light"><i class="fa fa-search"></i></button>
									  </div>
									</div>
								</div>
					</div>
					<div class="row">
						<div class="col-md-4"> 
							<div class="box-sidebar">
								<div class="header-box d-flex justify-content-between flex-wrap">
									<h4 class="title-box"><?php esc_html_e('Categories','jobboard'); ?></h4>
									<div class="search">
										<i class="fa fa-search"></i>                      
									</div>
								</div>
								<div class="body-box">
									<div class="scroller">
										<ul class="list-check-filter-job">
											<?php
												$argscat = array(
												'type'                     => $directory_url,
												'orderby'                  => 'name',
												'order'                    => 'ASC',
												'hide_empty'               => true,
												'hierarchical'             => 1,
												'exclude'                  => '',
												'include'                  => '',
												'number'                   => '',
												'taxonomy'                 => $directory_url.'-category',
												'pad_counts'               => false
												);
												$categories = get_categories( $argscat );
												$category_input_array= array();
												if(isset($_REQUEST['category_input'])){													
													$category_input_array1 = array_map( 'sanitize_text_field', $_REQUEST['category_input'] );
													foreach($category_input_array1 as $cat_one){
														$category_input_array[]=esc_html($cat_one);
													}
													
												}	
												
												if ( $categories && !is_wp_error( $categories ) ) :
												foreach ( $categories as $term ) {
													if(trim($term->name)!=''){	
														$selected='';
														if(in_array($term->name, $category_input_array)){
															$selected='checked="checked"';
														}
													?>									
													<li>
														<div class="custom-control custom-checkbox">
															<input class="custom-control-input" type="checkbox" name="category_input[]" value="<?php echo esc_attr($term->name);?>" id="<?php echo esc_attr($term->name);?>" <?php echo esc_html($selected); ?>>
															<label class="custom-control-label" for="<?php echo esc_attr($term->name);?>"><?php echo esc_html(ucfirst($term->name)); ?></label>
														</div>
													</li>
													<?php
													}
												}
												endif;
											?>
										</ul>
									</div>
								</div>
							</div>
							<div class="box-sidebar">
								<div class="header-box d-flex justify-content-between flex-wrap">
									<h4 class="title-box"><?php esc_html_e('Locations','jobboard'); ?></h4>
									<div class="search">
										<i class="fa fa-search"></i>                       
									</div>
								</div>
								<div class="body-box">
									<ul class="list-check-filter-job">
										<?php
											$locations=array();
											$args_location = array();
											$args_location['number']='9999999';
											$user_type = array(
											'relation' => 'AND',
												array(
													'key'     => 'user_type',
													'value'   => 'candidate',
													'compare' => '='
												),
											);																						
											$args_location['meta_query'] = array(
												$user_type,
											);
											$user_query_location = new WP_User_Query( $args_location );
											if ( ! empty( $user_query_location->results ) ) {
												foreach ( $user_query_location->results as $user_location ) {						
													if(get_user_meta($user_location->ID,'city',true)!=""){
														$c_location=ucwords(get_user_meta($user_location->ID,'city',true)).', '.ucwords(get_user_meta($user_location->ID,'country',true));							
														if(!in_array($c_location, $locations)){							
															array_push($locations, $c_location);
														}
													}
													// Update full name
													if(trim(get_user_meta($user_location->ID,'full_name',true))==""){
														update_user_meta($user_location->ID,'full_name',$user_location->display_name);
													}
												}
											}	
											$location_input_array= array();
											if(isset($_REQUEST['location_input'])){
												$location_input_array = array_map( 'sanitize_text_field', $_REQUEST['location_input'] );
											}
											$i=0;	
											foreach($locations as $one_location){
												$selected='';
												if(in_array($one_location, $location_input_array)){
													$selected='checked="checked"';
												}
											?>
											<li>
												<div class="custom-control custom-checkbox">
													<input class="custom-control-input" type="checkbox" id="location_<?php echo esc_attr($i); ?>" name="location_input[]"  value="<?php echo esc_attr($one_location); ?>" <?php echo esc_html($selected); ?> >
													<label class="custom-control-label" for="location_<?php echo esc_attr($i); ?>"><?php echo esc_html($one_location); ?> </label>
												</div>
											</li>						   
											<?php
												$i++;
											}
										?>
									</ul>
								</div>
								<button type="submit"  class="btn btn-light-green col-md-12 "><?php esc_html_e('Search ', 'jobboard'); ?></button>
							</div>
						</div>
						<?php				
							
							if(isset($atts['per_page'])){
								$users_per_page=$atts['per_page'];
								}else{
								$users_per_page=15;
							}
							$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
							if($paged==1){
								$offset=0;  
								}else {
								$offset= ($paged-1)*$users_per_page;
							}
							$args = array();
							$args['number']=$users_per_page;
							$args['offset']= $offset; 
							$args['orderby']='display_name';
							$args['order']='ASC'; 
							
							$location_city_arr= array();							
							$location_input_array= array();
							
							if(isset($_REQUEST['location_input'])){
								$location_input_array = array_map( 'sanitize_text_field', $_REQUEST['location_input'] );							
								foreach($location_input_array as $one_value){										
										$city_counrty= explode(',',$one_value);
										$location_city_arr[]=sanitize_text_field($city_counrty[0]);
								}								
							}
														
							$user_name_search='';
							if( isset($_REQUEST['user_name_search'])){								
								if($_REQUEST['user_name_search']!=""){
										$user_name_search = array(
										'relation' => 'AND',
											array(
												'key'     => 'full_name',
												'value'   => sanitize_text_field($_REQUEST['user_name_search']),
												'compare' => 'LIKE'
											),
										);
								}
							}
							$categories_search='';
							if( isset($_REQUEST['category_input'])){								
								if($_REQUEST['category_input']!=""){									
									$categories_arr = array_map( 'sanitize_text_field', $_REQUEST['category_input'] );
										$categories_search = array(
										'relation' => 'AND',
											array(
												'key'     => 'company_type',
												'value'   => $categories_arr,
												'compare' => 'IN'
											),
										);
								}
							}							
							$city_search='';
							if( isset($_REQUEST['location_input'])){								
								if($_REQUEST['location_input']!=""){
										$city_search = array(
										'relation' => 'AND',
											array(
												'key'     => 'city',
												'value'   => $location_city_arr,
												'compare' => 'IN'
											),
										);
								}
							}
							$user_type = array(
							'relation' => 'AND',
								array(
									'key'     => 'user_type',
									'value'   => 'employer',
									'compare' => '!='
								),
							);
							
							
							
							$args['meta_query'] = array(
								$user_name_search,$city_search, $categories_search,$user_type,
							);
							
							$user_query = new WP_User_Query( $args );
							$total_users = $user_query->get_total();	
						?>
						<div class="col-md-8">
							<div class="content-main-right list-jobs">
								<div class="header-list-job d-flex flex-wrap justify-content-between align-items-center">
									<h4><?php echo esc_html($total_users); ?> <?php esc_html_e('Candidate Found', 'jobboard'); ?> </h4>
								</div>
								<div class="list">
									<?php				    
										// User Loop
										if ( ! empty( $user_query->results ) ) {
											foreach ( $user_query->results as $user ) {
											
												$profile_page=get_option('epjbjobboard_candidate_public_page');
												$page_link= get_permalink( $profile_page).'?&id='.$user->ID; 
											?>
											<div class="job-item employer">
												<div class="row">
												<div class="col-md-2 candidate-img px-0">
													<a href="<?php  echo esc_url($page_link); ?>">
														<?php
															$iv_profile_pic_url=get_user_meta($user->ID, 'iv_profile_pic_thum',true);
															if($iv_profile_pic_url!=''){ ?>
															<img  src="<?php echo esc_url($iv_profile_pic_url); ?>">
															<?php
																}else{
																echo'<img src="'. wp_jobboard_URLPATH.'assets/images/default-user.png">';
															}
														?>
													</a>
												</div>
												<div class="col-md-10">
													<div class="row">
														<div class="col-md-12">
															<h3 class="title-job">
																<a href="<?php  echo esc_url($page_link); ?>"><?php echo (get_user_meta($user->ID,'full_name',true)!=''? get_user_meta($user->ID,'full_name',true) : $user->display_name ); ?>
																	</a>
															</h3>
														</div>
														<div class="meta-job col-md-8">	
														<?php
															if(get_user_meta($user->ID,'qualification',true)!=''){
																?>
																<p><i class="fa fa-check-circle"></i>
																<?php echo esc_html(get_user_meta($user->ID,'qualification',true)); ?></p>
																</p>
															<?php
															}
															?>
															
															<p><i class="far fa-envelope"></i>
															<?php echo esc_html($user->user_email); ?></p>
															</p>
														
															
															<?php
																	if(get_user_meta($user->ID,'address',true)!='' OR get_user_meta($user->ID,'city',true)!='' ){
																	?>
																	<p class="location"><i class="far fa-map"></i>  <?php echo esc_html(get_user_meta($user->ID,'address',true)); ?> <?php echo esc_html(get_user_meta($user->ID,'city',true)); ?>, <?php echo esc_html(get_user_meta($user->ID,'zipcode',true)); ?>,<?php echo esc_html(get_user_meta($user->ID,'country',true)); ?></p>
																	<?php
																	}
																?>
															<?php
																if(get_user_meta($user->ID,'hourly_rate',true)!=''){
																?>	
																<p class="cost"><i class="far fa-money-bill-alt"></i> <?php echo esc_html(get_user_meta($user->ID,'hourly_rate',true)); ?></p>
																<?php
																}
																?>
														</div>
														<div class="number-position col-md-4 align-self-center px-0">
															<div class="candidate-button">
																<a class="btn btn-light" href="<?php  echo esc_url($page_link); ?>"><?php esc_html_e('View Profile', 'jobboard'); ?></a>
															</div>
														</div>
													</div>
												  </div>
											  </div>
											</div>
					
									
											<?php
											}
										}
									?>				
								</div>				
								<?php
									
									$params =array();
									$pages = paginate_links( array_merge( [
									'base'         => str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) ),
									'format'       => '?paged=%#%',
									'current'      => max( 1, get_query_var( 'paged' ) ),
									'total'        => round((int)$total_users/$users_per_page),
									'type'         => 'array',
									'show_all'     => false,
									'end_size'     => 3,
									'mid_size'     => 1,
									'prev_next'    => true,
									'prev_text'    => esc_html__( '« Prev','jobboard' ),
									'next_text'    => esc_html__( 'Next »','jobboard' ),
									'add_args'     => $args,
									'add_fragment' => ''
									], $params )
									);			 				
									if ( is_array( $pages ) ) {			
										$pagination = '<div class=" mt-3 pagination justify-content-center"><ul class="pagination">';												
										foreach ( $pages as $page ) {
											$pagination .= '<li class="page-item' . (strpos($page, 'current') !== false ? ' active' : '') . '"> ' . str_replace('page-numbers', 'page-link', $page) . '</li>';
										}
										$pagination .= '</ul></div>';
										echo wp_specialchars_decode($pagination);
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</form>
</div>