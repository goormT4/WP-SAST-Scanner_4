<?php
wp_enqueue_style('bootstrap-jobboard-110', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
wp_enqueue_style('fontawesome', wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
wp_enqueue_style('wp-jobboard-piblic-13', wp_jobboard_URLPATH . 'admin/files/css/profile-public.css');

global $post,$wpdb,$tag;
$directory_url=get_option('epjbjobboard_url');
if($directory_url==""){$directory_url='job';}
$current_post_type=$directory_url;
$post_limit='999999';
if(isset($atts['post_limit']) and $atts['post_limit']!="" ){
 $post_limit=$atts['post_limit'];
}	
	$dirs_data =array();
	$tag_arr= array();
	$args = array(
		'post_type' => $directory_url, // enter your custom post type		
		'post_status' => 'publish',		
		'posts_per_page'=> $post_limit,  // overrides posts per page in theme settings
	);

	$features = array(
	'relation' => 'AND',
		array(
			'key'     => 'jobboard_featured',
			'value'   => 'featured',
			'compare' => '='
		),
	);
	$args['meta_query'] = array(
		$features,
	);
$the_query = new WP_Query( $args );
	
?>

<section id="destination" class="background-transparent">
	<section class="bootstrap-wrapper background-transparent">
		<div class="container dynamic-bg">
			<div class="row ">
			
		  <?php
		$i=1;	
		 if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) : $the_query->the_post();
					$id = get_the_ID();
						$feature_img='';
						if(has_post_thumbnail()){
							$feature_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'thumbnail' );
							if($feature_image[0]!=""){
								$feature_img =$feature_image[0];
							}
						}else{
							$feature_img= wp_jobboard_URLPATH."assets/images/job.png";
						}
						$listing_contact_source=get_post_meta($id,'listing_contact_source',true);
						if($listing_contact_source==''){$listing_contact_source='new_value';}
						if($listing_contact_source=='new_value'){					
						$company_name=	get_post_meta($id,'company_name',true);
						}else{
							$post_author_id= get_post_field( 'post_author', $id );															
							$company_name=	get_user_meta($post_author_id,'full_name',true);
						}
					?>
						<div class="col-md-4 col-6   ">
							<div class="row  ml-0 contentshowep rounded ">
								<div class="col-md-3 col-12 rounded mt-2">
									<a href="<?php echo get_permalink($id); ?>" >
										<img   src="<?php echo esc_url($feature_img);?>" class="rounded img-fluid ">
									</a>						
								</div>								
								<div class="col-md-9 col-12 mt-2 title-job">	
									<a href="<?php echo get_permalink($id); ?>" >
									<p class="mb-0"><i class="fas fa-check-circle"></i><span class="p-2  ">
										<?php echo esc_html($company_name); ?></span></p>									
									<p class="mb-0"><i class="fas fa-briefcase"></i><span class="p-2"><?php echo get_the_title($id); ?></span></p>
									
									<p class="mb-2">
									<?php
										if(get_post_meta($id,'salary', true)!=''){
										?>
									<i class="far fa-money-bill-alt"></i><span class="p-2"><?php echo esc_html(get_post_meta($id,'salary', true)); ?></span>
									<?php
										}
									?>
									<?php
										if(get_post_meta($id,'deadline', true)!=''){
										?>
									<i class="fas fa-hourglass-half"></i><span class="p-2"><?php echo date('d M, y', strtotime(get_post_meta($id,'deadline', true))); ?></span>
									
									<?php
										}
									?>
									</p>
								
								
									</a>
								</div>
							</div>
							
						</div>
				<?php
				$i++;
		endwhile;
			?>
		  <?php endif; ?>
			</div>
		</div>
	</section>
</section>
<?php
	wp_reset_query();
?>



