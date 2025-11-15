<?php
	wp_enqueue_script("jquery");
	wp_enqueue_style('jquery-ui', wp_jobboard_URLPATH . 'admin/files/css/jquery-ui.css');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('bootstrap-css-jobboard-ep7', wp_jobboard_URLPATH . 'admin/files/css/iv-bootstrap.css');
	wp_enqueue_script('bootstrapjs-jobboard-ep8', wp_jobboard_URLPATH . 'admin/files/js/bootstrap.min.js');
	wp_enqueue_script('popper', 		wp_jobboard_URLPATH . 'admin/files/js/popper.min.js');
	wp_enqueue_style('colorbox', wp_jobboard_URLPATH . 'admin/files/css/colorbox.css');
	wp_enqueue_script('colorbox', wp_jobboard_URLPATH . 'admin/files/js/jquery.colorbox-min.js');
	wp_enqueue_style('all', wp_jobboard_URLPATH . 'admin/files/css/all.min.css');
	wp_enqueue_style('jobboard-my-account-css', wp_jobboard_URLPATH . 'admin/files/css/my-account.css');
	wp_enqueue_style('jobboard-my-menu-css', wp_jobboard_URLPATH . 'admin/files/css/cssmenu.css');
	wp_enqueue_script('jobboard-script-user-directory', wp_jobboard_URLPATH . 'admin/files/js/user-directory.js');
	/**************************** css resources from q-desk ********************************************/
	wp_enqueue_style('flaticon', wp_jobboard_URLPATH . 'admin/files/css/flaticon-category.css');
	wp_enqueue_style('mCustomScrollbar', wp_jobboard_URLPATH . 'admin/files/css/jquery.mCustomScrollbar.css');
	wp_enqueue_style('jobboard-main-css', wp_jobboard_URLPATH . 'admin/files/css/main.css');
	wp_enqueue_script('mCustomScrollbarJS', wp_jobboard_URLPATH . 'admin/files/js/jquery.mCustomScrollbar.concat.min.js');
	/****************************************************************************************************/
	wp_enqueue_style('jquery.dataTables', wp_jobboard_URLPATH . 'admin/files/css/jquery.dataTables.css');
	wp_enqueue_script('jquery.dataTables', wp_jobboard_URLPATH . 'admin/files/js/jquery.dataTables.js');		
	wp_enqueue_script('datetimepicker', wp_jobboard_URLPATH . 'admin/files/js/jquery.datetimepicker.full.js');
	wp_enqueue_style('datetimepicker', wp_jobboard_URLPATH . 'admin/files/css/jquery.datetimepicker.css');
	wp_enqueue_media();
	$main_class = new wp_jobboard;
	
	$directory_url=get_option('epjbjobboard_url');
	if($directory_url==""){$directory_url='job';}
	global $current_user;
	global $wpdb;
	$user = new WP_User( $current_user->ID );
	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role ){
			$crole= ucfirst($role);
			break;
		}
	}
	if(strtoupper($crole)!=strtoupper('administrator')){
		include(wp_jobboard_template.'/private-profile/check_status.php');
	}
	$currencies = array();
	$currencies['AUD'] ='$';$currencies['CAD'] ='$';
	$currencies['EUR'] ='€';$currencies['GBP'] ='£';
	$currencies['JPY'] ='¥';$currencies['USD'] ='$';
	$currencies['NZD'] ='$';$currencies['CHF'] ='Fr';
	$currencies['HKD'] ='$';$currencies['SGD'] ='$';
	$currencies['SEK'] ='kr';$currencies['DKK'] ='kr';
	$currencies['PLN'] ='zł';$currencies['NOK'] ='kr';
	$currencies['HUF'] ='Ft';$currencies['CZK'] ='Kč';
	$currencies['ILS'] ='₪';$currencies['MXN'] ='$';
	$currencies['BRL'] ='R$';$currencies['PHP'] ='₱';
	$currencies['MYR'] ='RM';$currencies['AUD'] ='$';
	$currencies['TWD'] ='NT$';$currencies['THB'] ='฿';
	$currencies['TRY'] ='TRY';	$currencies['CNY'] ='¥';
	$currency= get_option('epjbjobboard_api_currency');
	$currency_symbol=(isset($currencies[$currency]) ? $currencies[$currency] :$currency );
	$user_id= $current_user->ID;
?>
<div class="bootstrap-wrapper">
	<div id="profile-account2"  class="container ">
		<div class="row p-3">
			<div class="col-md-12 top_row">
				<div class="company_img">
					<?php
						$iv_profile_pic_url=get_user_meta($current_user->ID, 'iv_profile_pic_thum',true);
						if($iv_profile_pic_url!=''){ ?>
						<img class="img-fluid" src="<?php echo esc_url($iv_profile_pic_url); ?>">
						<?php
							}else{
							echo'<img src="'. wp_jobboard_URLPATH.'assets/images/default-user.png">';
						}
					?>
				</div>
				<div class="company_content">
					<?php 
						$user_type=get_user_meta($current_user->ID, 'user_type',true);
						if(get_user_meta($current_user->ID, 'user_type',true)=='employer'){
							$profile_page=get_option('epjbjobboard_employer_public_page');
						}
						if(get_user_meta($current_user->ID, 'user_type',true)=='candidate'){
							$profile_page=get_option('epjbjobboard_candidate_public_page');
						}
						if($user_type==''){
							if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
								$profile_page=get_option('epjbjobboard_employer_public_page');
								}else{
								$profile_page=get_option('epjbjobboard_candidate_public_page');
							}
						}
						$page_link= get_permalink( $profile_page).'?&id='.$current_user->ID; 
					?>
					<h4 class=""><?php echo esc_html(get_user_meta($current_user->ID, 'full_name',true));?></h4>
					<a class="btn btn-dark" href="<?php echo esc_url($page_link); ?>"><?php  esc_html_e('View Profile','jobboard');?> </a>	
					<?php
						if(get_user_meta($current_user->ID, 'user_type',true)!='employer'){?>
						<a class="btn btn-light-green" href="<?php echo get_permalink();?>?&jobboardpdfcv=<?php echo esc_attr($current_user->ID);?>" target="_blank"><i class="fas fa-download"></i> <?php esc_html_e('PDF', 'jobboard'); ?></a>
						<?php
						}
					?>
				</div>
			</div>
		</div>
		<div class="row ">
			<div class="col-lg-3">
				<!-- BEGIN PROFILE SIDEBAR -->
				<div class="profile-sidebar">
					<!-- PORTLET MAIN -->
					<div class="portlet portlet0 light profile-sidebar-portlet">
						<!-- SIDEBAR MENU -->
						<div class="profile-usermenu">
							<?php
								$active='all-post';
								if(isset($_GET['profile']) AND $_GET['profile']=='setting' ){
									$active='setting';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='level' ){
									$active='level';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='all-post' ){
									$active='all-post';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='new-post' ){
									$active='new-post';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='new-post' ){
									$active='new-post';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='dashboard' ){
									$active='dashboard';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='favorites' ){
									$active='favorites';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='who-is-interested' ){
									$active='who-is-interested';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='notification' ){
									$active='notification';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='post-edit' ){
									$active='all-post';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='candidate-bookmarks' ){
									$active='candidate-bookmarks';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='employer_manage_jobs' ){
									$active='employer_manage_jobs';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='employer_bookmarks' ){
									$active='employer_bookmarks';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='employer_post_a_job' ){
									$active='employer_post_a_job';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='employer_manage_candidates' ){
									$active='employer_manage_candidates';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='edit_resume' ){
									$active='edit_resume';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='candidate_edit_profile' ){
									$active='candidate_edit_profile';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='messageboard' ){
									$active='messageboard';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='candidate-applied' ){
									$active='candidate-applied';
								}
								if(isset($_GET['profile']) AND $_GET['profile']=='job_bookmark' ){
									$active='job_bookmark';
								}
								$post_type=  'job';
							?>
							<div id='cssmenu'>
								<?php
									if(get_user_meta($current_user->ID, 'user_type',true)=='candidate'){
										include(  wp_jobboard_template. 'private-profile/candidate-menu.php');
										}else{
										include(  wp_jobboard_template. 'private-profile/employer-menu.php');
									}		
								?>
							</div>
						</div>
						<!-- END MENU -->
					</div>
					<!-- END PORTLET MAIN -->
					<!-- PORTLET MAIN -->
					<!-- END PORTLET MAIN -->
				</div>
			</div>
			<!-- END BEGIN PROFILE SIDEBAR -->
			<!-- BEGIN PROFILE CONTENT -->
			<?php ?>
			<div class="col-lg-9 background-light">
				<?php
					if(isset($_GET['profile']) AND $_GET['profile']=='all-post' ){
						include(  wp_jobboard_template. 'private-profile/profile-all-post-1.php');
					}					
					elseif(isset($_GET['profile']) AND $_GET['profile']=='new-post' ){
						include( wp_jobboard_template. 'private-profile/profile-new-post-1.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='level' ){
						include(  wp_jobboard_template. 'private-profile/profile-level-1.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='post-edit' ){
						include(  wp_jobboard_template. 'private-profile/profile-edit-post-1.php');
					}	
					elseif(isset($_GET['profile']) AND $_GET['profile']=='setting' ){
						include(  wp_jobboard_template. 'private-profile/profile-setting-1.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='candidate-bookmarks' ){
						include(  wp_jobboard_template. 'private-profile/candidate-bookmarks.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='employer_manage_jobs' ){
						include(  wp_jobboard_template. 'private-profile/employer-manage-jobs.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='employer_bookmarks' ){
						include(  wp_jobboard_template. 'private-profile/employer-bookmarks.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='employer_post_a_job' ){
						include(  wp_jobboard_template. 'private-profile/employer-post-a-job.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='employer_manage_candidates' ){
						include(  wp_jobboard_template. 'private-profile/employer-manage-candidates.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='edit_resume' ){
						include(  wp_jobboard_template. 'private-profile/edit-resume.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='candidate_edit_profile' ){
						include(  wp_jobboard_template. 'private-profile/candidate-edit-profile.php');
					}
					elseif(isset($_GET['profile']) AND $_GET['profile']=='job_bookmark' ){
						include(  wp_jobboard_template. 'private-profile/job_bookmark.php');
						}elseif(isset($_GET['profile']) AND $_GET['profile']=='messageboard' ){
						include(  wp_jobboard_template. 'private-profile/messageboard.php');
						}elseif(isset($_GET['profile']) AND $_GET['profile']=='notification' ){
						include(  wp_jobboard_template. 'private-profile/job-notifications.php');
						}elseif(isset($_GET['profile']) AND $_GET['profile']=='candidate-applied' ){
						include(  wp_jobboard_template. 'private-profile/my-applied.php');
					}
					else{
						if(get_user_meta($current_user->ID, 'user_type',true)=='candidate'){
							include(  wp_jobboard_template. 'private-profile/messageboard.php');
							}else{
							include(  wp_jobboard_template. 'private-profile/profile-all-post-1.php');
						}
					}
				?>
			</div>
		</div>
	</div>
</div>
<div id="profile-account2" class="bootstrap-wrapper around-separetor main_content">
	<div class="container">
	</div>
</div>
<?php
	$currencyCode = get_option('epjbjobboard_api_currency');
	wp_enqueue_script('epmyaccount-script-27', wp_jobboard_URLPATH . 'admin/files/js/my-account.js');
	wp_localize_script('epmyaccount-script-27', 'jobboard1', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'wp_iv_directories_URLPATH'		=> wp_jobboard_URLPATH,
	'current_user_id'	=>get_current_user_id(),
	'SetImage'		=>esc_html__('Set Image','jobboard'),
	'GalleryImages'=>esc_html__('Gallery Images','jobboard'),
	'cancel-message' => esc_html__('Are you sure to cancel this Membership','jobboard'),
	'currencyCode'=>  $currencyCode,
	'dirwpnonce'=> wp_create_nonce("myaccount"),
	'dirwpnonce2'=> wp_create_nonce("signup2"),
	'signup'=> wp_create_nonce("signup"),
	'contact'=> wp_create_nonce("contact"),
	'permalink'=> get_permalink(),
	"sProcessing"=>  esc_html__('Processing','jobboard'),
	"sSearch"=>   esc_html__('Search','jobboard'),
	"lengthMenu"=>   esc_html__('Display _MENU_ records per page','jobboard'),
	"zeroRecords"=>  esc_html__('Nothing found - sorry','jobboard'),
	"info"=>  esc_html__('Showing page _PAGE_ of _PAGES_','jobboard'),
	"infoEmpty"=>   esc_html__('No records available','jobboard'),
	"infoFiltered"=>  esc_html__('(filtered from _MAX_ total records)','jobboard'),
	"sFirst"=> esc_html__('First','jobboard'),
	"sLast"=>  esc_html__('Last','jobboard'),
	"sNext"=>     esc_html__('Next','jobboard'),
	"sPrevious"=>  esc_html__('Previous','jobboard'),
	"makeShortListed"=>  esc_html__('Make Shortlisted','jobboard'), 
	"ShortListed"=>  esc_html__('Undo Shortlisted','jobboard'), 
	"Rejected"=>  esc_html__('Rejected','jobboard'), 
	"MakeReject"=>  esc_html__('Make Reject','jobboard'), 		
	) );
	wp_enqueue_script('jobboard-single-listing', wp_jobboard_URLPATH . 'admin/files/js/single-listing.js');
	wp_localize_script('jobboard-single-listing', 'jobboard_data', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',
	'current_user_id'	=>get_current_user_id(),
	'Please_login'=>esc_html__('Please login', 'jobboard' ),
	'Add_to_Favorites'=>esc_html__('Add to Favorites', 'jobboard' ),
	'Added_to_Favorites'=>esc_html__('Added to Favorites', 'jobboard' ),
	'Please_put_your_message'=>esc_html__('Please put your name,email & Cover letter', 'jobboard' ),
	'contact'=> wp_create_nonce("contact"),
	'listing'=> wp_create_nonce("listing"),
	'cv'=> wp_create_nonce("Doc/CV/PDF"),
	'wp_jobboard_URLPATH'=>wp_jobboard_URLPATH,
	) );
	wp_enqueue_script('jobboard_message', wp_jobboard_URLPATH . 'admin/files/js/user-message.js');
	wp_localize_script('jobboard_message', 'jobboard_data_message', array(
	'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
	'loading_image'		=> '<img src="'.wp_jobboard_URLPATH.'admin/files/images/loader.gif">',		
	'Please_put_your_message'=>esc_html__('Please put your name,email & message', 'jobboard' ),
	'contact'=> wp_create_nonce("contact"),
	'listing'=> wp_create_nonce("listing"),
	) );
?>