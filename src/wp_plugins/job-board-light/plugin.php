<?php
	/**
		*
		*
		* @version 1.2.8
		* @package Main
		* @author themeglow
	*/
	/*
		Plugin Name: JobBoard Light
		Plugin URI: http://e-plugins.com/jobboard-light
		Description: Build Paid job board Listing using Wordpress.No programming knowledge required.
		Author: ThemeGlow
		Author URI: http://e-plugins.com/
		Version: 1.2.8
		Text Domain: jobboard
		License: GPLv2 or later
	*/
	// Exit if accessed directly
	if (!defined('ABSPATH')) {
		exit;
	}
	if (!class_exists('wp_jobboard')) {  	
		final class wp_jobboard {
			private static $instance;
			/**
				* The Plug-in version.
				*
				* @var string
			*/
			public $version = "1.2.8";
			/**
				* The minimal required version of WordPress for this plug-in to function correctly.
				*
				* @var string
			*/
			public $wp_version = "3.5";
			public static function instance() {
				if (!isset(self::$instance) && !(self::$instance instanceof wp_jobboard)) {
					self::$instance = new wp_jobboard;
				}
				return self::$instance;
			}
			/**
				* Construct and start the other plug-in functionality
			*/
			public function __construct() {
				//
				// 1. Plug-in requirements
				//
				if (!$this->check_requirements()) {
					return;
				}
				//
				// 2. Declare constants and load dependencies
				//
				$this->define_constants();
				$this->load_dependencies();
				//
				// 3. Activation Hooks
				//
				register_activation_hook(__FILE__, array($this, 'activate'));
				register_deactivation_hook(__FILE__, array($this, 'deactivate'));
				register_uninstall_hook(__FILE__, 'wp_jobboard::uninstall');
				//
				// 4. Load Widget
				//
				add_action('widgets_init', array($this, 'register_widget'));
				//
				// 5. i18n
				//
				add_action('init', array($this, 'i18n'));
				//
				// 6. Actions
				//	
				add_action('wp_ajax_jobboard_check_coupon', array($this, 'jobboard_check_coupon'));
				add_action('wp_ajax_nopriv_jobboard_check_coupon', array($this, 'jobboard_check_coupon'));					
				add_action('wp_ajax_jobboard_check_package_amount', array($this, 'jobboard_check_package_amount'));
				add_action('wp_ajax_nopriv_jobboard_check_package_amount', array($this, 'jobboard_check_package_amount'));
				add_action('wp_ajax_jobboard_update_profile_pic', array($this, 'jobboard_update_profile_pic'));					
				add_action('wp_ajax_jobboard_update_profile_setting', array($this, 'jobboard_update_profile_setting'));
				add_action('wp_ajax_jobboard_update_wp_post', array($this, 'jobboard_update_wp_post'));					
				add_action('wp_ajax_jobboard_save_wp_post', array($this, 'jobboard_save_wp_post'));									
				add_action('wp_ajax_jobboard_update_setting_fb', array($this, 'jobboard_update_setting_fb'));				
								
				add_action('wp_ajax_jobboard_update_setting_password', array($this, 'jobboard_update_setting_password'));add_action('wp_ajax_jobboard_check_login', array($this, 'jobboard_check_login'));
				add_action('wp_ajax_nopriv_jobboard_check_login', array($this, 'jobboard_check_login'));
				add_action('wp_ajax_jobboard_forget_password', array($this, 'jobboard_forget_password'));
				add_action('wp_ajax_nopriv_jobboard_forget_password', array($this, 'jobboard_forget_password'));					
				add_action('wp_ajax_jobboard_cancel_stripe', array($this, 'jobboard_cancel_stripe'));								
				add_action('wp_ajax_jobboard_cancel_paypal', array($this, 'jobboard_cancel_paypal'));					
				add_action('wp_ajax_jobboard_profile_stripe_upgrade', array($this, 'jobboard_profile_stripe_upgrade'));
				add_action('wp_ajax_jobboard_save_favorite', array($this, 'jobboard_save_favorite'));						
				add_action('wp_ajax_jobboard_save_un_favorite', array($this, 'jobboard_save_un_favorite'));				
				add_action('wp_ajax_jobboard_applied_delete', array($this, 'jobboard_applied_delete'));	
				add_action('wp_ajax_jobboard_save_notification', array($this, 'jobboard_save_notification'));							
				add_action('wp_ajax_jobboard_delete_favorite', array($this, 'jobboard_delete_favorite'));
				add_action('wp_ajax_jobboard_candidate_delete', array($this, 'jobboard_candidate_delete'));
				add_action('wp_ajax_jobboard_candidate_reject', array($this, 'jobboard_candidate_reject'));
				add_action('wp_ajax_jobboard_candidate_shortlisted', array($this, 'jobboard_candidate_shortlisted'));
				add_action('wp_ajax_jobboard_candidate_schedule', array($this, 'jobboard_candidate_schedule'));
				add_action('wp_ajax_jobboard_profile_bookmark', array($this, 'jobboard_profile_bookmark'));
				add_action('wp_ajax_jobboard_profile_bookmark_delete', array($this, 'jobboard_profile_bookmark_delete'));
				add_action('wp_ajax_jobboard_employer_bookmark', array($this, 'jobboard_employer_bookmark'));
				add_action('wp_ajax_jobboard_employer_bookmark_delete', array($this, 'jobboard_employer_bookmark_delete'));
				add_action('wp_ajax_jobboard_message_delete', array($this, 'jobboard_message_delete'));
				add_action('wp_ajax_jobboard_message_send', array($this, 'jobboard_message_send'));
				add_action('wp_ajax_nopriv_jobboard_message_send', array($this, 'jobboard_message_send'));
				add_action('wp_ajax_jobboard_claim_send', array($this, 'jobboard_claim_send'));
				add_action('wp_ajax_nopriv_jobboard_claim_send', array($this, 'jobboard_claim_send'));					
				add_action('wp_ajax_jobboard_cron_job', array($this, 'jobboard_cron_job'));
				add_action('wp_ajax_nopriv_jobboard_cron_job', array($this, 'jobboard_cron_job'));	
				add_action('wp_ajax_jobboard_apply_submit_login', array($this, 'jobboard_apply_submit_login'));
				add_action('wp_ajax_jobboard_apply_submit_nonlogin', array($this, 'jobboard_apply_submit_nonlogin'));
				add_action('wp_ajax_nopriv_jobboard_apply_submit_nonlogin', array($this, 'jobboard_apply_submit_nonlogin'));
				add_action('wp_ajax_jobboard_candidate_meeting_popup', array($this, 'jobboard_candidate_meeting_popup'));
				add_action('wp_ajax_jobboard_candidate_email_popup', array($this, 'jobboard_candidate_email_popup'));
				add_action('wp_ajax_nopriv_jobboard_candidate_email_popup', array($this, 'jobboard_candidate_email_popup'));
				add_action('wp_ajax_jobboard_apply_popup', array($this, 'jobboard_apply_popup'));
				add_action('wp_ajax_nopriv_jobboard_apply_popup', array($this, 'jobboard_apply_popup'));
				add_action('wp_ajax_finalerp_csv_product_upload', array($this, 'finalerp_csv_product_upload'));
				add_action('wp_ajax_save_csv_file_to_database', array($this, 'save_csv_file_to_database'));
				add_action('wp_ajax_eppro_get_import_status', array($this, 'eppro_get_import_status'));		
				add_action('wp_ajax_jobboard_contact_popup', array($this, 'jobboard_contact_popup'));
				add_action('wp_ajax_jobboard_listing_contact_popup', array($this, 'jobboard_listing_contact_popup'));
				add_action('wp_ajax_nopriv_jobboard_listing_contact_popup', array($this, 'jobboard_listing_contact_popup'));
				add_action('plugins_loaded', array($this, 'start'));
				add_action('add_meta_boxes', array($this, 'prfx_custom_meta_jobboard'));
				add_action('save_post', array($this, 'jobboard_meta_save'));	
				add_action('wp_login', array($this, 'check_expiry_date'));					
				add_action('pre_get_posts',array($this, 'iv_restrict_media_library') );				
				// 7. Shortcode
				add_shortcode('jobboard_price_table', array($this, 'jobboard_price_table_func'));				
				add_shortcode('jobboard_form_wizard', array($this, 'jobboard_form_wizard_func'));
				add_shortcode('jobboard_profile_template', array($this, 'jobboard_profile_template_func'));
				add_shortcode('jobboard_candidate_profile_public', array($this, 'jobboard_candidate_profile_public_func'));
				add_shortcode('jobboard_employer_profile_public', array($this, 'jobboard_employer_profile_public_func'));	
				add_shortcode('jobboard_login', array($this, 'jobboard_login_func'));
				add_shortcode('jobs_employer_directory', array($this, 'jobs_employer_directory_func'));					
				add_shortcode('jobs_candidate_directory', array($this, 'jobs_candidate_directory_func'));
				add_shortcode('jobboard_categories', array($this, 'jobboard_categories_func'));
				add_shortcode('jobboard_featured', array($this, 'jobboard_featured_func'));					
				add_shortcode('jobboard_map', array($this, 'jobboard_map_func'));												
				add_shortcode('jobboard_all_jobs', array($this, 'jobboard_all_jobs_func'));
				add_shortcode('jobboard_all_jobs_grid', array($this, 'jobboard_all_jobs_grid_func'));
				add_shortcode('jobboard_all_jobs_grid_popup', array($this, 'jobboard_all_jobs_grid_popup_func'));
				add_shortcode('slider_search', array($this, 'slider_search_func'));
				add_shortcode('listing_filter', array($this, 'listing_filter_func'));					
				add_shortcode('listing_carousel', array($this, 'listing_carousel_func'));
				add_shortcode('jobboard_cities', array($this, 'jobboard_cities_func'));						
				add_shortcode('jobboard_reminder_email_cron', array($this, 'jobboard_reminder_email_cron_func'));
				// 8. Filter						
				add_filter( 'template_include', array($this, 'include_template_function'), 9, 2  );
				add_filter('request', array($this, 'post_type_tags_fix'));						
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'jobboard_plugin_action_links' ) );
				//---- COMMENT FILTERS ----//		
				add_action('init', array($this, 'remove_admin_bar') );	
				add_action( 'init', array($this, 'jobboard_paypal_form_submit') );				
				add_action( 'init', array($this, 'iv_dir_post_type') );
				add_action( 'init', array($this, 'tr_create_my_taxonomy'));
				add_action( 'init', array($this, 'ep_create_my_taxonomy_tags'));
				add_action( 'init', array($this, 'ep_jobboard_pdf_cv') );				
				add_action( 'init', array($this, 'ep_jobboard_cpt_columns') );
			}
			/**
				* Define constants needed across the plug-in.
			*/
			private function define_constants() {
				if (!defined('wp_jobboard_BASENAME')) define('wp_jobboard_BASENAME', plugin_basename(__FILE__));
				if (!defined('wp_jobboard_DIR')) define('wp_jobboard_DIR', dirname(__FILE__));
				if (!defined('wp_jobboard_FOLDER'))define('wp_jobboard_FOLDER', plugin_basename(dirname(__FILE__)));
				if (!defined('wp_jobboard_ABSPATH'))define('wp_jobboard_ABSPATH', trailingslashit(str_replace("\\", "/", WP_PLUGIN_DIR . '/' . plugin_basename(dirname(__FILE__)))));
				if (!defined('wp_jobboard_URLPATH'))define('wp_jobboard_URLPATH', trailingslashit(plugins_url() . '/' . plugin_basename(dirname(__FILE__))));
				if (!defined('wp_jobboard_ADMINPATH'))define('wp_jobboard_ADMINPATH', get_admin_url());
				$filename = get_stylesheet_directory()."/jobboard/";
				if (!file_exists($filename)) {					
					if (!defined('wp_jobboard_template'))define( 'wp_jobboard_template', wp_jobboard_ABSPATH.'template/' );
					}else{
					if (!defined('wp_jobboard_template'))define( 'wp_jobboard_template', $filename);
				}	
			}				
			public function remove_admin_bar() {
				$iv_hide = get_option('epjbjobboard_hide_admin_bar');
				if (!current_user_can('administrator') && !is_admin()) {
					if($iv_hide=='yes'){							
						show_admin_bar(false);
					}
				}	
			}
			public function include_template_function( $template_path ) { 
				$directory_url=get_option('epjbjobboard_url');					
				if($directory_url==""){$directory_url='job';}
				if ( get_post_type() ==$directory_url ) { 
					if ( is_single() ) {
						$template_path =  wp_jobboard_template. 'listing/single-job.php';	
					}				
					if( is_tag() || is_category() || is_archive() ){	
						$template_path =  wp_jobboard_template. 'listing/listing-layout.php';
					}
				}
				return $template_path;
			}
			public function tr_create_my_taxonomy() {
				$directory_url=get_option('epjbjobboard_url');					
				if($directory_url==""){$directory_url='job';}
				register_taxonomy(
				$directory_url.'-category',
				$directory_url,
				array(
				'label' => esc_html__( 'Categories','jobboard' ),
				'rewrite' => array( 'slug' => $directory_url.'-category' ),
				'hierarchical' => true,
				'show_in_rest' =>	true,
				)
				);
			}
			public function iv_dir_post_type() {
				$directory_url=get_option('epjbjobboard_url');					
				if($directory_url==""){$directory_url='job';}
				$directory_url_name=ucfirst($directory_url);
				$labels = array(
				'name'                => _x( $directory_url_name, 'Post Type General Name', 'jobboard' ),
				'singular_name'       => _x( $directory_url_name, 'Post Type Singular Name', 'jobboard' ),
				'menu_name'           => esc_html__( $directory_url_name, 'jobboard' ),
				'name_admin_bar'      => esc_html__( $directory_url_name, 'jobboard' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'jobboard' ),
				'all_items'           => esc_html__( 'All Items', 'jobboard' ),
				'add_new_item'        => esc_html__( 'Add New Item', 'jobboard' ),
				'add_new'             => esc_html__( 'Add New', 'jobboard' ),
				'new_item'            => esc_html__( 'New Item', 'jobboard' ),
				'edit_item'           => esc_html__( 'Edit Item', 'jobboard' ),
				'update_item'         => esc_html__( 'Update Item', 'jobboard' ),
				'view_item'           => esc_html__( 'View Item', 'jobboard' ),
				'search_items'        => esc_html__( 'Search Item', 'jobboard' ),
				'not_found'           => esc_html__( 'Not found', 'jobboard' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'jobboard' ),
				);
				$args = array(
				'label'               => esc_html__( $directory_url_name, 'jobboard' ),
				'description'         => esc_html__( $directory_url_name, 'jobboard' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'post-formats','custom-fields' ),					
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'show_in_rest' =>	true,	
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				);
				register_post_type( $directory_url, $args );
				// For job_apply
				$labels3 = array(
				'name'                => _x( 'Applicants', 'Post Type General Name', 'jobboard' ),
				'singular_name'       => _x( 'Applicants', 'Post Type Singular Name', 'jobboard' ),
				'menu_name'           => esc_html__( 'Applicants', 'jobboard' ),
				'name_admin_bar'      => esc_html__( 'Applicants', 'jobboard' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'jobboard' ),
				'all_items'           => esc_html__( 'All Items', 'jobboard' ),
				'add_new_item'        => esc_html__( 'Add New Item', 'jobboard' ),
				'add_new'             => esc_html__( 'Add New', 'jobboard' ),
				'new_item'            => esc_html__( 'New Item', 'jobboard' ),
				'edit_item'           => esc_html__( 'Edit Item', 'jobboard' ),
				'update_item'         => esc_html__( 'Update Item', 'jobboard' ),
				'view_item'           => esc_html__( 'View Item', 'jobboard' ),
				'search_items'        => esc_html__( 'Search Item', 'jobboard' ),
				'not_found'           => esc_html__( 'Not found', 'jobboard' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'jobboard' ),
				);
				$args3 = array(
				'label'               => esc_html__( 'Applicants', 'jobboard' ),
				'description'         => esc_html__( 'Applicants', 'jobboard' ),
				'labels'              => $labels3,
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'post-formats','custom-fields' ),					
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'show_in_rest' =>true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				);
				register_post_type( 'job_apply', $args3 );
				// Message 
				$labels4 = array(
				'name'                => _x( 'Message', 'Post Type General Name', 'jobboard' ),
				'singular_name'       => _x( 'Message', 'Post Type Singular Name', 'jobboard' ),
				'menu_name'           => esc_html__( 'Message', 'jobboard' ),
				'name_admin_bar'      => esc_html__( 'Message', 'jobboard' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'jobboard' ),
				'all_items'           => esc_html__( 'All Items', 'jobboard' ),
				'add_new_item'        => esc_html__( 'Add New Item', 'jobboard' ),
				'add_new'             => esc_html__( 'Add New', 'jobboard' ),
				'new_item'            => esc_html__( 'New Item', 'jobboard' ),
				'edit_item'           => esc_html__( 'Edit Item', 'jobboard' ),
				'update_item'         => esc_html__( 'Update Item', 'jobboard' ),
				'view_item'           => esc_html__( 'View Item', 'jobboard' ),
				'search_items'        => esc_html__( 'Search Item', 'jobboard' ),
				'not_found'           => esc_html__( 'Not found', 'jobboard' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'jobboard' ),
				);
				$args4 = array(
				'label'               => esc_html__( 'Message', 'jobboard' ),
				'description'         => esc_html__( 'Message', 'jobboard' ),
				'labels'              => $labels4,
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'post-formats','custom-fields' ),					
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'show_in_rest' =>true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				);
				register_post_type( 'jobboard_message', $args4 );
			}
			public function post_type_tags_fix($request) {
				$directory_url=get_option('epjbjobboard_url');					
				if($directory_url==""){$directory_url='job';}
				if ( isset($request['tag']) && !isset($request['post_type']) ){
					$request['post_type'] = $directory_url;
				}
				return $request;
			} 
			public function ep_jobboard_cpt_columns(){ 				
				require_once(wp_jobboard_DIR . '/admin/pages/manage-cpt-columns.php');				
			}
			public function jobboard_plugin_action_links( $links ) {	
				return array_merge( array(
					'settings' => '<a href="admin.php?page=wp-jobboard-settings">' . esc_html__( 'Settings', 'jobboard' ).'</a>',
					'doc'  => '<a href="http://help.eplug-ins.com/jobboard">' . esc_html__( 'Docs', 'jobboard' ) . '</a>',
				), $links );
			}	
			public function author_public_profile() {
				$author = get_the_author();	
				$iv_redirect = get_option('epjbjobboard_profile_public_page');
				if($iv_redirect!='defult'){ 
					$reg_page= get_permalink( $iv_redirect) ; 
					return    $reg_page.'?&id='.$author; 
					exit;
				}
			}
			public function iv_registration_redirect(){
				$iv_redirect = get_option( 'jobboard_signup_redirect');
				if($iv_redirect!='defult'){
					$reg_page= get_permalink( $iv_redirect); 
					wp_redirect( $reg_page );
					exit;
				}	
			}
			public function jobboard_login_func($atts = ''){
				global $current_user;
				ob_start();	
				global $current_user;
				ob_start();
				if($current_user->ID==0){
					include(wp_jobboard_template. 'private-profile/profile-login.php');
					}else{	
					include( wp_jobboard_template. 'private-profile/profile-template-1.php');
				}	
				$content = ob_get_clean();	
				return $content;
			}
			public function jobboard_forget_password(){
				parse_str($_POST['form_data'], $data_a);
				if( ! email_exists(sanitize_email($data_a['forget_email']))) {
					echo json_encode(array("code" => "not-success","msg"=>"There is no user registered with that email address."));
					exit(0);
					} else {
					require_once( wp_jobboard_ABSPATH. 'inc/forget-mail.php');
					echo json_encode(array("code" => "success","msg"=>"Updated Successfully"));
					exit(0);
				}
			}
			public function jobboard_check_login(){
				parse_str($_POST['form_data'], $form_data);
				global $user;
				$creds = array();
				$creds['user_login'] =sanitize_text_field($form_data['username']);
				$creds['user_password'] =  sanitize_text_field($form_data['password']);
				$creds['remember'] = 'true';
				$secure_cookie = is_ssl() ? true : false;
				$user = wp_signon( $creds, $secure_cookie );
				if ( is_wp_error($user) ) {
					echo json_encode(array("code" => "not-success","msg"=>$user->get_error_message()));
					exit(0);
				}
				if ( !is_wp_error($user) ) {
					$iv_redirect = get_option('epjbjobboard_profile_page');
					if($iv_redirect!='defult'){
						$reg_page= get_permalink( $iv_redirect); 
						echo json_encode(array("code" => "success","msg"=>$reg_page));
						exit(0);
					}
				}		
			}
			public function get_unique_keyword_values( $post_type, $key = 'keyword'  ){
				global $wpdb;
				if( empty( $key ) ){
					return;
				}	
				$res=array();
				$args = array(
				'post_type' => $post_type, // enter your custom post type						
				'post_status' => 'publish',						
				'posts_per_page'=> -1,  // overrides posts per page in theme settings
				);
				$query_auto = new WP_Query( $args );
				$posts_auto = $query_auto->posts;						
				foreach($posts_auto as $post_a) {
					$res[]=$post_a->post_title;
				}	
				return $res;
			}
			public function get_unique_post_meta_values( $post_type, $key = 'postcode' ){
				global $wpdb;
				$directory_url=get_option('epjbjobboard_url');
				if($directory_url==""){$directory_url='job';}
				if( empty( $key ) ){
					return;
				}	
				$res = $wpdb->get_col( $wpdb->prepare( "
				SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type='{$post_type}' AND  pm.meta_key = '%s'						
				", $key) );
				return $res;
			}  
			public function jobboard_check_field_input_access($field_key_pass, $field_value, $user_id, $template='myaccount' ){ 
				if($template=='myaccount'){				
					$current_user_id=$user_id;					
					}else{
					$current_user_id=0;		
				}					
				$field_type_opt=  get_option( 'jobboard_field_type' );
				if($field_type_opt!=''){
					$field_type=get_option('jobboard_field_type' );
					}else{
					$field_type= array();
					$field_type['full_name']='text';								
					$field_type['company_since']='datepicker';
					$field_type['team_size']='text';									
					$field_type['phone']='text';
					$field_type['mobile']='text';
					$field_type['address']='text';
					$field_type['city']='text';
					$field_type['zipcode']='text';
					$field_type['state']='text';
					$field_type['country']='text';										
					$field_type['job_title']='text';									
					$field_type['hourly_rate']='text';
					$field_type['experience']='textarea';
					$field_type['age']='text';
					$field_type['qualification']='text';								
					$field_type['gender']='radio';	
					$field_type['website']='url';
					$field_type['description']='textarea';			
				}
				$field_type_value= get_option( 'jobboard_field_type_value' );
				if($field_type_value==''){
					$field_type_value=array();
					$field_type_value['gender']=esc_html__('Female,Male,Other', 'jobboard');	
				}			
				$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );
				$return_value='';
								
			
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='dropdown'){	 								
					$dropdown_value= explode(',',$field_type_value[$field_key_pass]);
					$return_value=$return_value.'<div class="col-md-6"><div class="form-group">
					<label class="control-label">'. esc_html($field_value).'</label>
					<select name="'. esc_html($field_key_pass).'" id="'.esc_attr($field_key_pass).'" class="form-control col-md-12"  >';				
					foreach($dropdown_value as $one_value){	 
						if(trim($one_value)!=''){
							$return_value=$return_value.'<option '.(trim(get_user_meta($current_user_id,$field_key_pass,true))==trim($one_value)?' selected':'').' value="'. esc_attr($one_value).'">'. esc_html($one_value).'</option>';
						}
					}	
					$return_value=$return_value.'</select></div></div>';					
				}
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='checkbox'){	 								
					$dropdown_value= explode(',',$field_type_value[$field_key_pass]);
					$return_value=$return_value.'<div class="col-md-6"><div class="form-group">
					<label class="control-label ">'. esc_html($field_value).'</label>						
					
					';
					$saved_checkbox_value =	explode(',',get_user_meta($current_user_id,$field_key_pass,true));
					foreach($dropdown_value as $one_value){
						if(trim($one_value)!=''){
							$return_value=$return_value.'
							<div class="form-check form-check-inline">
							<label class="form-check-label" for="'. esc_attr($one_value).'">
							<input '.( in_array($one_value,$saved_checkbox_value)?' checked':'').' class=" form-check-input" type="checkbox" name="'. esc_attr($field_key_pass).'[]"  id="'. esc_attr($one_value).'" value="'. esc_attr($one_value).'">
							'. esc_attr($one_value).' </label>
							</div>';
						}
					}	
					$return_value=$return_value.'</div></div>';						
				}
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='radio'){	 								
					$dropdown_value= explode(',',$field_type_value[$field_key_pass]);
					$return_value=$return_value.'<div class="col-md-6"><div class="form-group ">
					<label class="control-label ">'. esc_html($field_value).'</label>
					';						
					foreach($dropdown_value as $one_value){	 
						if(trim($one_value)!=''){
							$return_value=$return_value.'
							<div class="form-check form-check-inline">
							<label class="form-check-label" for="'. esc_attr($one_value).'">
							<input '.(get_user_meta($current_user_id,$field_key_pass,true)==$one_value?' checked':'').' class="form-check-input" type="radio" name="'. esc_attr($field_key_pass).'"  id="'. esc_attr($one_value).'" value="'. esc_attr($one_value).'">
							'. esc_attr($one_value).'</label>
							</div>														
							';
						}
					}	
					$return_value=$return_value.'</div></div>';					
				}					 
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='textarea'){	 
					$return_value=$return_value.'<div class="col-md-12"><div class="form-group">';
					$return_value=$return_value.'<label class="control-label ">'. esc_html($field_value).'</label>';
					$return_value=$return_value.'<textarea  placeholder="'.esc_html__('Enter ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-textarea "  rows="4"/>'.esc_attr(get_user_meta($current_user_id,$field_key_pass,true)).'</textarea></div></div>';
				}
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='datepicker'){	 
					$return_value=$return_value.'<div class="col-md-6"><div class="form-group ">';
					$return_value=$return_value.'<label class="control-label ">'. esc_html($field_value).'</label>';
					$return_value=$return_value.'<input type="text" placeholder="'.esc_html__('Select ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-control epinputdate " value="'.esc_attr(get_user_meta($current_user_id,$field_key_pass,true)).'"/></div></div>';
				}
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='text'){ 	 
					$return_value=$return_value.'<div class="col-md-6"><div class="form-group ">';
					$return_value=$return_value.'<label class="control-label ">'. esc_html($field_value).'</label>';
					$return_value=$return_value.'<input type="text" placeholder="'.esc_html__('Enter ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-control " value="'.esc_attr(get_user_meta($current_user_id,$field_key_pass,true)).'"/></div></div>';
				}
				if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='url'){	 
					$return_value=$return_value.'<div class="col-md-6"><div class="form-group ">';
					$return_value=$return_value.'<label class="control-label ">'. esc_html($field_value).'</label>';
					$return_value=$return_value.'<input type="text" placeholder="'.esc_html__('Enter ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-control " value="'.esc_url(get_user_meta($current_user_id,$field_key_pass,true)).'"/></div></div>';
				}
			
				return $return_value;
			}
			public function jobboard_check_field_input_access_signup($field_key_pass, $field_value){ 
				$sign_up_array=		get_option( 'jobboard_signup_fields');
				$require_array=		get_option( 'jobboard_signup_require');
				$field_type=  		get_option( 'jobboard_field_type' );
				$field_type_value=  get_option( 'jobboard_field_type_value' );
				$field_type_roles=  get_option( 'jobboard_field_type_roles' );
				$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );
				$return_value='';
				$require='no';				
				if(isset($require_array[$field_key_pass]) && $require_array[$field_key_pass] == 'yes') {
					$require='yes';
				}
				if(isset($sign_up_array[$field_key_pass]) && $sign_up_array[$field_key_pass]=='yes'){
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='dropdown'){	 								
						$dropdown_value= explode(',',$field_type_value[$field_key_pass]);
						$return_value=$return_value.'<div class="form-group row">
						<label class="control-label col-md-4">'. esc_html($field_value).'</label>
						<div class="col-md-8"><select name="'. esc_html($field_key_pass).'" id="'.esc_attr($field_key_pass).'" class="form-dropdown col-md-12" '.($require=='yes'?'data-validation="required" data-validation-error-msg="'. esc_html__('This field cannot be left blank','jobboard').'"':'').'>';				
						foreach($dropdown_value as $one_value){	 	
							if(trim($one_value)!=''){
								$return_value=$return_value.'<option value="'. esc_attr($one_value).'">'. esc_html($one_value).'</option>';
							}
						}	
						$return_value=$return_value.'</select></div></div>';					
					}
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='checkbox'){	 								
						$dropdown_value= explode(',',$field_type_value[$field_key_pass]);
						$return_value=$return_value.'<div class="form-group row">
						<label class="control-label col-md-4">'. esc_html($field_value).'</label>
						<div class="col-md-8">
						<div class="" >
						';
						foreach($dropdown_value as $one_value){
							if(trim($one_value)!=''){
								$return_value=$return_value.'
								<div class="form-check form-check-inline col-md-4">
								<input class=" form-check-input" type="checkbox" name="'. esc_attr($field_key_pass).'[]"  id="'. esc_attr($one_value).'" value="'. esc_attr($one_value).'" '.($require=='yes'?'data-validation="required" data-validation-error-msg="'. esc_html__('Required','jobboard').'"':'').'>
								<label class="form-check-label" for="'. esc_attr($one_value).'">							
								'. esc_attr($one_value).' </label>
								</div>';
							}
						}	
						$return_value=$return_value.'</div></div></div>';						
					}
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='radio'){	 								
						$dropdown_value= explode(',',$field_type_value[$field_key_pass]);
						$return_value=$return_value.'<div class="form-group row ">
						<label class="control-label col-md-4">'. esc_html($field_value).'</label>
						<div class="col-md-8">
						<div class="" >
						';						
						foreach($dropdown_value as $one_value){	 		
							if(trim($one_value)!=''){
								$return_value=$return_value.'
								<div class="form-check form-check-inline col-md-4">
								<label class="form-check-label" for="'. esc_attr($one_value).'">
								<input class="form-check-input" type="radio" name="'. esc_attr($field_key_pass).'"  id="'. esc_attr($one_value).'" value="'. esc_attr($one_value).'" '.($require=='yes'?'data-validation="required" data-validation-error-msg="'. esc_html__('Required','jobboard').'"':'').'>
								'. esc_attr($one_value).'</label>
								</div>';
							}
						}	
						$return_value=$return_value.'</div></div></div>';					
					}					 
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='textarea'){	 
						$return_value=$return_value.'<div class="form-group row">';
						$return_value=$return_value.'<label class="control-label col-md-4">'. esc_html($field_value).'</label><div class="col-md-8">';
						$return_value=$return_value.'<textarea  placeholder="'.esc_html__('Enter ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-textarea col-md-12"  rows="4"/ '.($require=='yes'?'data-validation="length" data-validation-length="2-100"':'').'></textarea></div></div>';
					}
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='datepicker'){	 
						$return_value=$return_value.'<div class="form-group row">';
						$return_value=$return_value.'<label class="control-label col-md-4">'. esc_html($field_value).'</label>';
						$return_value=$return_value.'<div class="col-md-8"><input type="text" placeholder="'.esc_html__('Select ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-date col-md-12 epinputdate " '.($require=='yes'?'data-validation="required" data-validation-error-msg="'. esc_html__('This field cannot be left blank','jobboard').'"':'').' /></div></div>';
					}
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='text'){	 
						$return_value=$return_value.'<div class="form-group row">';
						$return_value=$return_value.'<label class="control-label col-md-4">'. esc_html($field_value).'</label>';
						$return_value=$return_value.'<div class="col-md-8"><input type="text" placeholder="'.esc_html__('Enter ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-input col-md-12" '.($require=='yes'?'data-validation="length" data-validation-length="2-100"':'').' /></div></div>';
					}
					if(isset($field_type[$field_key_pass]) && $field_type[$field_key_pass]=='url'){	 
						$return_value=$return_value.'<div class="form-group row">';
						$return_value=$return_value.'<label class="control-label col-md-4">'. esc_html($field_value).'</label>';
						$return_value=$return_value.'<div class="col-md-8"><input type="text" placeholder="'.esc_html__('Enter ','jobboard').esc_attr($field_value).'" name="'.esc_html($field_key_pass).'" id="'. esc_attr($field_key_pass).'"  class="form-input col-md-12" '.($require=='yes'?'data-validation="length" data-validation-length="2-100"':'').' /></div></div>';
					}
				}
				return $return_value;
			}
			public function user_profile_image_upload($userid){
				$iv_membership_signup_profile_pic=get_option('jobboard_signup_profile_pic');
				if($iv_membership_signup_profile_pic=='' ){ $iv_membership_signup_profile_pic='yes';}	
				if($iv_membership_signup_profile_pic=='yes' ){ 
					if ( 0 < $_FILES['profilepicture']['error'] ) { 
					
											
					}
					else {  
						 $new_file_type = mime_content_type( $_FILES['profilepicture']['tmp_name'] );	
						
						if( in_array( $new_file_type, get_allowed_mime_types() ) ){   
							$upload_dir   = wp_upload_dir();
							$date = date('YmdHis');						
							$file_name = $date.sanitize_file_name($_FILES['profilepicture']['name']);
							$validate = wp_check_filetype( $file_name );
							if ( $validate['type'] == true ) {
								$return= move_uploaded_file($_FILES['profilepicture']['tmp_name'],  $upload_dir['basedir'].'/'.$file_name);
								if($return){  
									$image_url= $upload_dir['baseurl'].'/'.$file_name;
									update_user_meta($userid, 'iv_profile_pic_thum',sanitize_url($image_url));
								}
							}	
						}
					}
				}
			}
			
			public function jobboard_update_wp_post(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'addlisting' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				
				global $current_user;global $wpdb;	
				$allowed_html = wp_kses_allowed_html( 'post' );	
				$directory_url=get_option('epjbjobboard_url');					
				if($directory_url==""){$directory_url='job';}
				parse_str($_POST['form_data'], $form_data);
				$newpost_id= sanitize_text_field($form_data['user_post_id']);
				$my_post = array();
				$my_post['ID'] = $newpost_id;
				$my_post['post_title'] = $form_data['title'];
				$my_post['post_content'] =  wp_kses( $form_data['new_post_content'], $allowed_html);
				$my_post['post_type'] 	= $directory_url;					
				$user_can_publish=get_option('user_can_publish');	
				if($user_can_publish==""){$user_can_publish='yes';}	
				$my_post['post_status']=$form_data['post_status'];
				
				if($form_data['post_status']=='publish'){					
					$my_post['post_status']='pending';
					if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
						$my_post['post_status']='publish';
						}else{ 
						if($user_can_publish=="yes"){ 
							$my_post['post_status']='publish';
							}else{
							$my_post['post_status']='pending';
						}								
					}						
				}
				
				wp_update_post( $my_post );
				if(isset($form_data['feature_image_id'] ) AND $form_data['feature_image_id']!='' ){
					$attach_id =sanitize_text_field($form_data['feature_image_id']);
					set_post_thumbnail( sanitize_text_field($form_data['user_post_id']), $attach_id );
					}else{
					$attach_id='0';
					delete_post_thumbnail( sanitize_text_field($form_data['user_post_id']));
				}
				if(isset($form_data['postcats'] )){ 
					$category_ids = array($form_data['postcats']);
					wp_set_object_terms( $newpost_id, $category_ids, $directory_url.'-category');
				}
				// Check Feature*************	
				$post_author_id= $current_user->ID;
				$author_package_id=get_user_meta($post_author_id, 'jobboard_package_id', true);
				$have_package_feature= get_post_meta($author_package_id,'jobboard_package_feature',true);
				$exprie_date= strtotime (get_user_meta($post_author_id, 'jobboard_exprie_date', true));
				$current_date=time();						
				if($have_package_feature=='yes'){
					if($exprie_date >= $current_date){ 
						update_post_meta($newpost_id, 'jobboard_featured', 'featured' );	
					}	
					}else{
					update_post_meta($newpost_id, 'jobboard_featured', 'no' );	
				}
				// job detail *****	
				update_post_meta($newpost_id, 'job_education', wp_kses( $form_data['content_education'], $allowed_html));	
				update_post_meta($newpost_id, 'job_must_have', wp_kses( $form_data['content_must_have'], $allowed_html));
				// For Tag Save tag_arr
				$dir_tags=get_option('epjbdir_tags');
				if($dir_tags==""){$dir_tags='yes';}	
				if($dir_tags=='yes'){
					$tag_all='';
					if(isset($form_data['tag_arr'] )){
						$tag_name= $form_data['tag_arr'] ;							
						$i=0;$tag_all='';						
						wp_set_object_terms( $newpost_id, $tag_name, $directory_url.'_tag');							
					}
					$tag_all='';
					if(isset($form_data['new_tag'] )){						
						$tag_new= explode(",", $form_data['new_tag']); 			
						foreach($tag_new  as $one_tag){	
							wp_add_object_terms( $newpost_id, sanitize_text_field($one_tag), $directory_url.'_tag');											
							$i++;	
						}
					}	
					}else{
					$tag_all='';
					$tag_array= wp_get_post_tags( $newpost_id );
					foreach($tag_array as $one_tag){	
						wp_remove_object_terms( $newpost_id, $one_tag->name, 'post_tag' );							
					}
					if(isset($form_data['tag_arr'] )){
						$tag_name= $form_data['tag_arr'] ;							
						$i=0;$tag_all='';
						foreach($tag_name  as $one_tag){							
							$tag_all= $tag_all.",".sanitize_text_field($one_tag);												
							$i++;	
						}
						wp_set_post_tags($newpost_id, $tag_all, true); 	
					}
					if(isset($form_data['new_tag'] )){
						$tag_all=$tag_all.','.sanitize_text_field($form_data['new_tag']);
						wp_set_post_tags($newpost_id, $tag_all, true); 	
					}	
				}	
				update_post_meta($newpost_id, 'address', sanitize_text_field($form_data['address'])); 
				update_post_meta($newpost_id, 'latitude', sanitize_text_field($form_data['latitude'])); 
				update_post_meta($newpost_id, 'longitude', sanitize_text_field($form_data['longitude']));					
				update_post_meta($newpost_id, 'city', sanitize_text_field($form_data['city'])); 
				update_post_meta($newpost_id, 'state', sanitize_text_field($form_data['state'])); 
				update_post_meta($newpost_id, 'postcode', sanitize_text_field($form_data['postcode'])); 
				update_post_meta($newpost_id, 'country', sanitize_text_field($form_data['country'])); 
				update_post_meta($newpost_id, 'local-area', sanitize_text_field($form_data['local-area'])); 
				// Get latlng from address* START********
				$dir_lat=sanitize_text_field($form_data['latitude']);
				$dir_lng=sanitize_text_field($form_data['longitude']);
				$address = sanitize_text_field($form_data['address']);
				// Get latlng from address* ENDDDDDD********	
				// job detail*****
				update_post_meta($newpost_id, 'job_status', sanitize_text_field($form_data['job_type'])); 
				update_post_meta($newpost_id, 'educational_requirements', sanitize_text_field($form_data['educational_requirements'])); 
				update_post_meta($newpost_id, 'job_type', sanitize_text_field($form_data['job_type'])); 
				update_post_meta($newpost_id, 'job_level', sanitize_text_field($form_data['job_level'])); 
				update_post_meta($newpost_id, 'experience_range', sanitize_text_field($form_data['experience_range'])); 
				update_post_meta($newpost_id, 'age_range', sanitize_text_field($form_data['age_range'])); 
				update_post_meta($newpost_id, 'gender', sanitize_text_field($form_data['gender'])); 
				update_post_meta($newpost_id, 'vacancy', sanitize_text_field($form_data['vacancy'])); 
				update_post_meta($newpost_id, 'deadline', sanitize_text_field($form_data['deadline'])); 
				update_post_meta($newpost_id, 'workplace', sanitize_text_field($form_data['workplace']));
				update_post_meta($newpost_id, 'salary', sanitize_text_field($form_data['salary']));
				update_post_meta($newpost_id, 'other_benefits', sanitize_text_field($form_data['other_benefits']));
				if(isset($form_data['dirpro_email_button'])){						
					update_post_meta($newpost_id, 'dirpro_email_button', sanitize_text_field($form_data['dirpro_email_button'])); 
				}
				if(isset($form_data['dirpro_web_button'])){						
					update_post_meta($newpost_id, 'dirpro_web_button', sanitize_text_field($form_data['dirpro_web_button'])); 
				}
				update_post_meta($newpost_id, 'image_gallery_ids', sanitize_text_field($form_data['gallery_image_ids'])); 
				if(isset($form_data['feature_image_id'] )){
					$attach_id =sanitize_text_field($form_data['feature_image_id']);
					set_post_thumbnail( $newpost_id, $attach_id );					
				}	
				update_post_meta($newpost_id, 'listing_contact_source', sanitize_text_field($form_data['contact_source']));  
				update_post_meta($newpost_id, 'company_name', sanitize_text_field($form_data['company_name']));
				update_post_meta($newpost_id, 'phone', sanitize_text_field($form_data['phone'])); 
				update_post_meta($newpost_id, 'address', sanitize_text_field($form_data['address'])); 
				update_post_meta($newpost_id, 'contact-email', sanitize_text_field($form_data['contact-email'])); 
				update_post_meta($newpost_id, 'contact_web', sanitize_text_field($form_data['contact_web']));				
				update_post_meta($newpost_id, 'vimeo', sanitize_text_field($form_data['vimeo'])); 
				update_post_meta($newpost_id, 'youtube', sanitize_text_field($form_data['youtube'])); 
				delete_post_meta($newpost_id, 'jobboard-tags');
				delete_post_meta($newpost_id, 'jobboard-category');
				
				if($form_data['post_status']=='publish'){ 
					include( wp_jobboard_ABSPATH. 'inc/notification.php');
				}
				
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Updated Successfully', 'jobboard')));
				exit(0);				
			}
			public function jobboard_save_wp_post(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'addlisting' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				
				$allowed_html = wp_kses_allowed_html( 'post' );	
				global $current_user; global $wpdb;	
				parse_str($_POST['form_data'], $form_data);				
				$my_post = array();
				$directory_url=get_option('epjbjobboard_url');					
				if($directory_url==""){$directory_url='job';}
				$post_type = $directory_url;
				$user_can_publish=get_option('user_can_publish');	
				if($user_can_publish==""){$user_can_publish='yes';}	
				if($form_data['post_status']=='publish'){					
					$form_data['post_status']='pending';
					if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
						$form_data['post_status']='publish';
						}else{
						if($user_can_publish=="yes"){
							$form_data['post_status']='publish';
							}else{
							$form_data['post_status']='pending';
						}								
					}						
				}
				$my_post['post_title'] = $form_data['title'];
				$my_post['post_content'] = wp_kses( $form_data['new_post_content'], $allowed_html); 
				$my_post['post_type'] = $post_type;
				$my_post['post_status'] = sanitize_text_field($form_data['post_status']);										
				$newpost_id= wp_insert_post( $my_post );
				update_post_meta($newpost_id, 'job_status', sanitize_text_field($form_data['job_type'])); 
				// WPML Start******
				if ( function_exists('icl_object_id') ) {
					include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
					$_POST['icl_post_language'] = $language_code = ICL_LANGUAGE_CODE;
					$query =$wpdb->prepare( "UPDATE {$wpdb->prefix}icl_translations SET element_type='post_%s' WHERE element_id='%s' LIMIT 1",$post_type,$newpost_id );
					$wpdb->query($query);					
				}
				// End WPML**********	
				if(isset($form_data['postcats'] )){ 				
					$category_ids = array($form_data['postcats']);
					wp_set_object_terms( $newpost_id, $category_ids, $directory_url.'-category');
				}
				$default_fields = array();
				$field_set=get_option('jobboard_fields' );
				if($field_set!=""){ 
					$default_fields=get_option('jobboard_fields' );
					}else{															
					$default_fields['other_link']=esc_html__('Other Link','jobboard');
				}
				if(sizeof($default_fields )){			
					foreach( $default_fields as $field_key => $field_value ) { 
						update_post_meta($newpost_id, sanitize_text_field($field_key), sanitize_text_field($form_data[$field_key]) );							
					}					
				}
				// Check Feature*************	
				$post_author_id= $current_user->ID;
				$author_package_id=get_user_meta($post_author_id, 'jobboard_package_id', true);
				$have_package_feature= get_post_meta($author_package_id,'jobboard_package_feature',true);
				$exprie_date= strtotime (get_user_meta($post_author_id, 'jobboard_exprie_date', true));
				$current_date=time();						
				if($have_package_feature=='yes'){
					if($exprie_date >= $current_date){
						update_post_meta($newpost_id, 'jobboard_featured', 'featured' );	
					}	
					}else{
					update_post_meta($newpost_id, 'jobboard_featured', 'no' );	
				}
				update_post_meta($newpost_id, 'job_education', wp_kses( $form_data['content_education'], $allowed_html));	
				update_post_meta($newpost_id, 'job_must_have', wp_kses( $form_data['content_must_have'], $allowed_html));
				// For Tag Save tag_arr
				$dir_tags=get_option('epjbdir_tags');
				if($dir_tags==""){$dir_tags='yes';}	
				if($dir_tags=='yes'){
					$tag_all='';
					if(isset($form_data['tag_arr'] )){
						$tag_name= $form_data['tag_arr'] ;							
						$i=0;$tag_all='';						
						wp_set_object_terms( $newpost_id, $tag_name, $directory_url.'_tag');							
					}
					$tag_all='';
					if(isset($form_data['new_tag'] )){						
						$tag_new= explode(",", $form_data['new_tag']); 			
						foreach($tag_new  as $one_tag){	
							wp_add_object_terms( $newpost_id, sanitize_text_field($one_tag), $directory_url.'_tag');											
							$i++;	
						}
					}	
					}else{
					$tag_all='';
					if(isset($form_data['tag_arr'] )){
						$tag_name= $form_data['tag_arr'] ;							
						$i=0;$tag_all='';
						foreach($tag_name  as $one_tag){							
							$tag_all= $tag_all.",".sanitize_text_field($one_tag);												
							$i++;	
						}
						wp_set_post_tags($newpost_id, $tag_all, true); 	
					}
					if(isset($form_data['new_tag'] )){
						$tag_all=$tag_all.','.sanitize_text_field($form_data['new_tag']);
						wp_set_post_tags($newpost_id, $tag_all, true); 	
					}	
				}	
				update_post_meta($newpost_id, 'address', sanitize_text_field($form_data['address'])); 
				update_post_meta($newpost_id, 'latitude', sanitize_text_field($form_data['latitude'])); 
				update_post_meta($newpost_id, 'longitude', sanitize_text_field($form_data['longitude']));					
				update_post_meta($newpost_id, 'city', sanitize_text_field($form_data['city'])); 
				update_post_meta($newpost_id, 'state', sanitize_text_field($form_data['state'])); 
				update_post_meta($newpost_id, 'postcode', sanitize_text_field($form_data['postcode'])); 
				update_post_meta($newpost_id, 'country', sanitize_text_field($form_data['country'])); 
				update_post_meta($newpost_id, 'local-area', sanitize_text_field($form_data['local-area'])); 
				// Get latlng from address* START********
				$dir_lat=sanitize_text_field($form_data['latitude']);
				$dir_lng=sanitize_text_field($form_data['longitude']);
				$address = sanitize_text_field($form_data['address']);
				// Get latlng from address* ENDDDDDD********	
				// job detail*****
				update_post_meta($newpost_id, 'educational_requirements', sanitize_text_field($form_data['educational_requirements'])); 
				update_post_meta($newpost_id, 'job_type', sanitize_text_field($form_data['job_type'])); 
				update_post_meta($newpost_id, 'job_level', sanitize_text_field($form_data['job_level'])); 
				update_post_meta($newpost_id, 'experience_range', sanitize_text_field($form_data['experience_range'])); 
				update_post_meta($newpost_id, 'age_range', sanitize_text_field($form_data['age_range'])); 
				update_post_meta($newpost_id, 'gender', sanitize_text_field($form_data['gender'])); 
				update_post_meta($newpost_id, 'vacancy', sanitize_text_field($form_data['vacancy'])); 
				update_post_meta($newpost_id, 'deadline', sanitize_text_field($form_data['deadline'])); 
				update_post_meta($newpost_id, 'workplace', sanitize_text_field($form_data['workplace']));
				update_post_meta($newpost_id, 'salary', sanitize_text_field($form_data['salary']));
				update_post_meta($newpost_id, 'other_benefits', sanitize_text_field($form_data['other_benefits']));
				if(isset($form_data['dirpro_email_button'])){						
					update_post_meta($newpost_id, 'dirpro_email_button', sanitize_text_field($form_data['dirpro_email_button'])); 
				}
				if(isset($form_data['dirpro_web_button'])){						
					update_post_meta($newpost_id, 'dirpro_web_button', sanitize_text_field($form_data['dirpro_web_button'])); 
				}
				update_post_meta($newpost_id, 'image_gallery_ids', sanitize_text_field($form_data['gallery_image_ids'])); 
				update_post_meta($newpost_id, 'listing_contact_source', sanitize_text_field($form_data['contact_source']));  
				if(isset($form_data['feature_image_id'] )){
					$attach_id =sanitize_text_field($form_data['feature_image_id']);
					set_post_thumbnail( $newpost_id, $attach_id );					
				}	
				update_post_meta($newpost_id, 'company_name', sanitize_text_field($form_data['company_name']));
				update_post_meta($newpost_id, 'phone', sanitize_text_field($form_data['phone'])); 
				update_post_meta($newpost_id, 'address', sanitize_text_field($form_data['address'])); 
				update_post_meta($newpost_id, 'contact-email', sanitize_text_field($form_data['contact-email'])); 
				update_post_meta($newpost_id, 'contact_web', sanitize_text_field($form_data['contact_web']));
				update_post_meta($newpost_id, 'vimeo', sanitize_text_field($form_data['vimeo'])); 
				update_post_meta($newpost_id, 'youtube', sanitize_text_field($form_data['youtube'])); 
				if($form_data['post_status']=='publish'){ 
					include( wp_jobboard_ABSPATH. 'inc/notification.php');
				}
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Updated Successfully', 'jobboard')));
				exit(0);
			}
			public function eppro_upload_featured_image($thumb_url, $post_id ) { 
				require_once(ABSPATH . 'wp-admin/includes/file.php');
				require_once(ABSPATH . 'wp-admin/includes/media.php');
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				// Download file to temp location
				$i=0;$product_image_gallery='';									
				$tmp = download_url( $thumb_url );						
				// Set variables for storage
				// fix file name for query strings
				preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches);
				$file_array['name'] = basename($matches[0]);
				$file_array['tmp_name'] = $tmp;
				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink($file_array['tmp_name']);
					$file_array['tmp_name'] = '';						
				}
				//use media_handle_sideload to upload img:
				$thumbid = media_handle_sideload( $file_array, $post_id, 'gallery desc' );
				// If error storing permanently, unlink
				if ( is_wp_error($thumbid) ) {
					@unlink($file_array['tmp_name']);										
				}						
				set_post_thumbnail($post_id, $thumbid);	
			}
			public function finalerp_csv_product_upload(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'csv' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'Are you cheating:user Permission?' );
				}
				$csv_file_id=0;$maping='';
				if(isset($_POST['csv_file_id'])){
					$csv_file_id= sanitize_text_field($_POST['csv_file_id']);
				}
				require(wp_jobboard_DIR .'/admin/pages/importer/upload_main_big_csv.php');
				$total_files = get_option( 'finalerp-number-of-files');
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Updated Successfully', 'jobboard'), "maping"=>$maping));
				exit(0);
			}
			public function save_csv_file_to_database(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'csv' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'Are you cheating:user Permission?' );
				}
				parse_str($_POST['form_data'], $form_data);
				$csv_file_id=0;
				if(isset($_POST['csv_file_id'])){
					$csv_file_id= sanitize_text_field($_POST['csv_file_id']);
				}	
				$row_start=0;
				if(isset($_POST['row_start'])){
					$row_start= sanitize_text_field($_POST['row_start']);
				}
				require (wp_jobboard_DIR .'/admin/pages/importer/csv_save_database.php');
				echo json_encode(array("code" => $done_status,"msg"=>esc_html__( 'Updated Successfully', 'jobboard'), "row_done"=>$row_done ));
				exit(0);
			}
			public function eppro_get_import_status(){
				$eppro_total_row = floatval( get_option( 'eppro_total_row' ));	
				$eppro_current_row = floatval( get_option( 'eppro_current_row' ));		
				$progress =  ((int)$eppro_current_row / (int)$eppro_total_row)*100;
				if($eppro_total_row<=$eppro_current_row){$progress='100';}
				if($progress=='100'){
					echo json_encode(array("code" => "-1","progress"=>(int)$progress, "eppro_total_row"=>$eppro_total_row,"eppro_current_row"=>$eppro_current_row));	
					}else{
					echo json_encode(array("code" => "0","progress"=>(int)$progress, "eppro_total_row"=>$eppro_total_row ,"eppro_current_row"=>$eppro_current_row));
				}		  
				exit(0);
			}
			public function ep_jobboard_pdf_cv(){ 
				require( wp_jobboard_DIR . '/template/pdf/pdf_cv.php');
				require( wp_jobboard_DIR . '/template/pdf/pdf_post.php');
			}
			public function  jobboard_apply_submit_login(){
				global $current_user;
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'listing' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);
				$my_post = array();	
				$allowed_html = wp_kses_allowed_html( 'post' );	
				$directory_url='job_apply';
				$my_post['post_author'] =$current_user->ID;
				$my_post['post_title'] = $current_user->display_name;
				$my_post['post_name'] = $current_user->display_name;
				$my_post['post_content'] =wp_kses( $form_data['cover-content2'], $allowed_html) ;  
				$my_post['post_type'] 	= $directory_url;
				$my_post['post_status']='private';						
				$newpost_id= wp_insert_post( $my_post );
				update_post_meta($newpost_id, 'candidate_name', $current_user->display_name); 
				update_post_meta($newpost_id, 'apply_jod_id',  sanitize_text_field($form_data['dir_id']));				
				update_post_meta($newpost_id, 'email_address', $current_user->user_email); 
				update_post_meta($newpost_id, 'user_id', $current_user->ID); 					
				$old_apply=get_user_meta($current_user->ID,'job_apply_all', true);
				$new_apply=$old_apply.', '.sanitize_text_field($form_data['dir_id']);						
				update_user_meta($current_user->ID,'job_apply_all',$new_apply);
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Successfully Sent', 'jobboard')));
				// Send Email
				include( wp_jobboard_ABSPATH. 'inc/apply_submit_login.php');
				exit(0);
			}
			public function jobboard_apply_submit_nonlogin(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'listing' ) ) {
				}			
				// Save data
				parse_str($_POST['form_data'], $form_data);
				if ( 0 < $_FILES['file']['error'] ) {
					echo json_encode(array("code" => "Error","msg"=>esc_html__( 'File Error', 'jobboard')));						
				}
				else {									
					$allowed_html = wp_kses_allowed_html( 'post' );								
					if ( ! function_exists( 'wp_handle_upload' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
					}
					$uploadedfile = $_FILES['file']; 
					$upload_overrides = array(
						'test_form' => false
					);
					$file_url='';
					$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
					if ( $movefile && ! isset( $movefile['error'] ) ) {						
						$file_url = $movefile['url'] ;
					} else {
						/*
						 * Error generated by _wp_handle_upload()
						 * @see _wp_handle_upload() in wp-admin/includes/file.php
						 */
						echo esc_html($movefile['error']);
					}
					
					// Add post in apply_job section
					$my_post = array();	
					$directory_url='job_apply';
					$my_post['post_author'] = '0';
					$my_post['post_title'] = sanitize_title($form_data['canname']);
					$my_post['post_name'] = sanitize_text_field($form_data['canname']);
					$my_post['post_content'] =wp_kses( $form_data['cover-content'], $allowed_html) ;  
					$my_post['post_type'] 	= $directory_url;
					$my_post['post_status']='private';						
					$newpost_id= wp_insert_post( $my_post );
					update_post_meta($newpost_id, 'candidate_name', sanitize_text_field($form_data['canname'])); 
					update_post_meta($newpost_id, 'apply_jod_id',  sanitize_text_field($form_data['dir_id'])); 
					update_post_meta($newpost_id, 'file_name', $file_name); 
					update_post_meta($newpost_id, 'cv_file_url', $file_url);
					update_post_meta($newpost_id, 'email_address', sanitize_email($form_data['email_address'])); 
					update_post_meta($newpost_id, 'phone', sanitize_text_field($form_data['contact_phone'])); 
					echo json_encode(array("code" => "success","msg"=>esc_html__( 'Successfully Sent', 'jobboard')));
				}
				// Send Email
				include( wp_jobboard_ABSPATH. 'inc/apply_submit_nonlogin.php');
				exit(0);
			}
			public function jobboard_candidate_meeting_popup(){
				$candidate_post_id=sanitize_text_field($_REQUEST['user_id']);
				include( wp_jobboard_template. 'private-profile/candidate_meeting_popup.php');
				exit(0);
			}
			public function jobboard_candidate_email_popup(){
				include( wp_jobboard_template. 'private-profile/candidate_email_popup.php');
				exit(0);
			}
			public function jobboard_apply_popup(){
				include( wp_jobboard_template. 'listing/apply_popup.php');
				exit(0);
			}
			public function jobboard_cancel_paypal(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				global $wpdb;
				global $current_user;
				parse_str($_POST['form_data'], $form_data);
				if( ! class_exists('Paypal' ) ) {
					require_once(wp_jobboard_DIR . '/inc/class-paypal.php');
				}
				$post_name='jobboard_paypal_setting';						
				$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_name = '%s' ",$post_name));
				$paypal_id='0';
				if(isset($row->ID )){
					$paypal_id= $row->ID;
				}
				$paypal_api_currency=get_post_meta($paypal_id, 'jobboard_paypal_api_currency', true);
				$paypal_username=get_post_meta($paypal_id, 'jobboard_paypal_username',true);
				$paypal_api_password=get_post_meta($paypal_id, 'jobboard_paypal_api_password', true);
				$paypal_api_signature=get_post_meta($paypal_id, 'jobboard_paypal_api_signature', true);
				$credentials = array();
				$credentials['USER'] = (isset($paypal_username)) ? $paypal_username : '';
				$credentials['PWD'] = (isset($paypal_api_password)) ? $paypal_api_password : '';
				$credentials['SIGNATURE'] = (isset($paypal_api_signature)) ? $paypal_api_signature : '';
				$paypal_mode=get_post_meta($paypal_id, 'jobboard_paypal_mode', true);
				$currencyCode = $paypal_api_currency;
				$sandbox = ($paypal_mode == 'live') ? '' : 'sandbox.';
				$sandboxBool = (!empty($sandbox)) ? true : false;
				$paypal = new Paypal($credentials,$sandboxBool);
				$oldProfile = get_user_meta($current_user->ID,'iv_paypal_recurring_profile_id',true);
				if (!empty($oldProfile)) {
					$cancelParams = array(
					'PROFILEID' => $oldProfile,
					'ACTION' => 'Cancel'
					);
					$paypal -> request('ManageRecurringPaymentsProfileStatus',$cancelParams);
					update_user_meta($current_user->ID,'iv_paypal_recurring_profile_id','');
					update_user_meta($current_user->ID,'iv_cancel_reason', sanitize_text_field($form_data['cancel_text'])); 
					update_user_meta($current_user->ID,'jobboard_payment_status', 'cancel'); 
					echo json_encode(array("code" => "success","msg"=>"Cancel Successfully"));
					exit(0);							
					}else{
					echo json_encode(array("code" => "not","msg"=>esc_html__( 'Unable to Cancel', 'jobboard')));
					exit(0);	
				}
			}
			public function jobboard_woocommerce_form_submit(  ) {
				$iv_gateway = get_option('jobboard_payment_gateway');
				if($iv_gateway=='woocommerce'){ 
					require_once(wp_jobboard_ABSPATH . '/admin/pages/payment-inc/woo-submit.php');
				}	
			}
			public function  jobboard_profile_stripe_upgrade(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				require_once(wp_jobboard_DIR . '/admin/init.php');
				global $wpdb;
				global $current_user;
				parse_str($_POST['form_data'], $form_data);	
				$newpost_id='';
				$post_name='jobboard_stripe_setting';
				$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_name = '%s' ",$post_name ));
				if(isset($row->ID )){
					$newpost_id= $row->ID;
				}
				$stripe_mode=get_post_meta( $newpost_id,'jobboard_stripe_mode',true);	
				if($stripe_mode=='test'){
					$stripe_api =get_post_meta($newpost_id, 'jobboard_stripe_secret_test',true);	
					}else{
					$stripe_api =get_post_meta($newpost_id, 'jobboard_stripe_live_secret_key',true);	
				}
				\Stripe\Stripe::setApiKey($stripe_api);				
				// For  cancel ----
				$arb_status =	get_user_meta($current_user->ID, 'jobboard_payment_status', true);
				$cust_id = get_user_meta($current_user->ID,'jobboard_stripe_cust_id',true);
				$sub_id = get_user_meta($current_user->ID,'jobboard_stripe_subscrip_id',true);
				if($sub_id!=''){	
					try{
						$iv_cancel_stripe = Stripe_Customer::retrieve(sanitize_text_field($form_data['cust_id']));
						$iv_cancel_stripe->subscriptions->retrieve(sanitize_text_field($form_data['sub_id']))->cancel();
						} catch (Exception $e) {
					}
					update_user_meta($current_user->ID,'jobboard_payment_status', 'cancel'); 
					update_user_meta($current_user->ID,'jobboard_stripe_subscrip_id','');
				}			
				require_once(wp_jobboard_DIR . '/admin/pages/payment-inc/stripe-upgrade.php');
				echo json_encode(array("code" => "success","msg"=>$response));
				exit(0);
			}
			public function jobboard_contact_popup(){
				include( wp_jobboard_template. 'private-profile/contact_popup.php');
				exit(0);
			}
			public function jobboard_listing_contact_popup(){
				include( wp_jobboard_template. 'listing/contact_popup.php');
				exit(0);
			}
			public function jobboard_get_categories_caching($id, $post_type){				
				if(metadata_exists('post', $id, 'jobboard-category')) {
					$items = get_post_meta($id,'jobboard-category',true );										
					}else{									
					$items=wp_get_object_terms( $id, $post_type.'-category');
					update_post_meta($id, 'jobboard-category' , $items);
				}					
				return $items;
			}			
			public function jobboard_get_tags_caching($id, $post_type){				
				if(metadata_exists('post', $id, 'jobboard-tags')) {
					$items = get_post_meta($id,'jobboard-tags',true );										
					}else{										
					$items=wp_get_object_terms( $id, $post_type.'_tag');
					update_post_meta($id, 'jobboard-tags' , $items);
				}					
				return $items;
			}
			public function jobboard_cancel_stripe(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				require_once(wp_jobboard_DIR . '/admin/files/lib/Stripe.php');
				global $wpdb;
				global $current_user;
				parse_str($_POST['form_data'], $form_data);	
				$newpost_id='';
				$post_name='jobboard_stripe_setting';
				$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_name = '%s' ",$post_name ));
				if(isset($row->ID )){
					$newpost_id= $row->ID;
				}
				$stripe_mode=get_post_meta( $newpost_id,'jobboard_stripe_mode',true);	
				if($stripe_mode=='test'){
					$stripe_api =get_post_meta($newpost_id, 'jobboard_stripe_secret_test',true);	
					}else{
					$stripe_api =get_post_meta($newpost_id, 'jobboard_stripe_live_secret_key',true);	
				}
				Stripe::setApiKey($stripe_api);
				try{
					$iv_cancel_stripe = Stripe_Customer::retrieve(sanitize_text_field($form_data['cust_id']));
					$iv_cancel_stripe->subscriptions->retrieve(sanitize_text_field($form_data['sub_id']))->cancel();
					} catch (Exception $e) {
				}
				update_user_meta($current_user->ID,'iv_cancel_reason', sanitize_text_field($form_data['cancel_text'])); 
				update_user_meta($current_user->ID,'jobboard_payment_status', 'cancel'); 
				update_user_meta($current_user->ID,'jobboard_stripe_subscrip_id','');
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Cancel Successfully', 'jobboard')));
				exit(0);
			}
			
			public function jobboard_update_setting_fb(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);		
				if(array_key_exists('wp_capabilities',$form_data)){
					wp_die( 'Are you cheating:wp_capabilities?' );		
				}		
				global $current_user;
				update_user_meta($current_user->ID,'twitter', sanitize_text_field($form_data['twitter'])); 
				update_user_meta($current_user->ID,'facebook', sanitize_text_field($form_data['facebook'])); 
				update_user_meta($current_user->ID,'gplus', sanitize_text_field($form_data['gplus'])); 
				update_user_meta($current_user->ID,'linkedin', sanitize_text_field($form_data['linkedin'])); 
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Updated Successfully', 'jobboard')));
				exit(0);
			}
			public function jobboard_update_setting_password(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);		
				if(array_key_exists('wp_capabilities',$form_data)){
					wp_die( 'Are you cheating:wp_capabilities?' );		
				}
				global $current_user;										
				if ( wp_check_password( sanitize_text_field($form_data['c_pass']), $current_user->user_pass, $current_user->ID) ){
					if($form_data['r_pass']!=$form_data['n_pass']){
						echo json_encode(array("code" => "not", "msg"=>"New Password & Re Password are not same. "));
						exit(0);
						}else{
						wp_set_password( sanitize_text_field($form_data['n_pass']), $current_user->ID);
						echo json_encode(array("code" => "success","msg"=>"Updated Successfully"));
						exit(0);
					}
					}else{
					echo json_encode(array("code" => "not", "msg"=>esc_html__( 'Current password is wrong.', 'jobboard')));
					exit(0);
				}
			}
			public function jobboard_update_profile_setting(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);		
				if(array_key_exists('wp_capabilities',$form_data)){
					wp_die( 'Are you cheating:wp_capabilities?' );		
				}
				$directory_url=get_option('epjbjobboard_url');
				if($directory_url==""){$directory_url='job';}
				$allowed_html = wp_kses_allowed_html( 'post' );
				global $current_user;
				
				if(isset($form_data['company_type'])){
					update_user_meta($current_user->ID, 'company_type', sanitize_text_field($form_data['company_type'])); 
				}
				if(isset($form_data['coverletter'])){ 
					update_user_meta($current_user->ID, 'coverletter', $form_data['coverletter']); 
				}
				
				
				$field_type=array();
				$field_type_opt=  get_option( 'jobboard_field_type' );
				if($field_type_opt!=''){
					$field_type=get_option('jobboard_field_type' );
					}else{
					$field_type['first_name']='text';
					$field_type['last_name']='text';
					$field_type['phone']='text';								
					$field_type['address']='text';
					$field_type['city']='text';
					$field_type['zipcode']='text';
					$field_type['country']='text';
					$field_type['job_title']='text';
					$field_type['gender']='radio';
					$field_type['occupation']='text';
					$field_type['description']='textarea';
					$field_type['web_site']='url';					
				}		
				
				foreach ( $form_data as $field_key => $field_value ) { 
					if(strtolower(trim($field_key))!='wp_capabilities'){						
						if(is_array($field_value)){
							$field_value =implode(",",$field_value);
						}
						if($field_type[$field_key]=='url'){							
							update_user_meta($current_user->ID, sanitize_text_field($field_key), sanitize_url($field_value)); 
						}elseif($field_type[$field_key]=='textarea'){
							update_user_meta($current_user->ID, sanitize_text_field($field_key), sanitize_textarea_field($field_value));  
						}else{
							update_user_meta($current_user->ID, sanitize_text_field($field_key), sanitize_text_field($field_value)); 
						}
					}
				}
				
			
				// For education Save
				// Delete 1st
				$i=0;
				for($i=0;$i<20;$i++){
					delete_user_meta($current_user->ID, 'educationtitle'.$i);
					delete_user_meta($current_user->ID, 'edustartdate'.$i);
					delete_user_meta($current_user->ID, 'eduenddate'.$i);
					delete_user_meta($current_user->ID, 'institute'.$i);
					delete_user_meta($current_user->ID, 'edudescription'.$i);
				}
				// Delete End
				if(isset($form_data['educationtitle'] )){
					$educationtitle= $form_data['educationtitle']; //this is array data we sanitize later, when it save
					$edustartdate= $form_data['edustartdate']; //this is array data we sanitize later, when it save
					$eduenddate= $form_data['eduenddate']; //this is array data we sanitize later, when it save
					$institute= $form_data['institute'];
					$edudescription= $form_data['edudescription'];
					$i=0;
					for($i=0;$i<20;$i++){
						if(isset($educationtitle[$i]) AND $educationtitle[$i]!=''){
							update_user_meta($current_user->ID, 'educationtitle'.$i, sanitize_text_field($educationtitle[$i]));
							update_user_meta($current_user->ID, 'edustartdate'.$i, sanitize_text_field($edustartdate[$i]));
							update_user_meta($current_user->ID, 'eduenddate'.$i, sanitize_text_field($eduenddate[$i]));
							update_user_meta($current_user->ID, 'institute'.$i, sanitize_text_field($institute[$i]));
							update_user_meta($current_user->ID, 'edudescription'.$i, sanitize_textarea_field($edudescription[$i]));
						}
					}
				}
				// End education	
				// For Work Experience Save
				// Delete 1st
				$i=0;
				for($i=0;$i<20;$i++){
					delete_user_meta($current_user->ID, 'experience_title'.$i);
					delete_user_meta($current_user->ID, 'experience_start'.$i);
					delete_user_meta($current_user->ID, 'experience_end'.$i);
					delete_user_meta($current_user->ID, 'experience_company'.$i);
					delete_user_meta($current_user->ID, 'experience_description'.$i);
				}
				// Delete End
				if(isset($form_data['experience_title'] )){
					$experience_title= $form_data['experience_title']; //this is array data we sanitize later, when it save
					$experience_start= $form_data['experience_start']; //this is array data we sanitize later, when it save
					$experience_end= $form_data['experience_end']; //this is array data we sanitize later, when it save
					$experience_company= $form_data['experience_company'];
					$experience_description= $form_data['experience_description'];
					$i=0;
					for($i=0;$i<20;$i++){
						if(isset($experience_title[$i]) AND $experience_title[$i]!=''){
							update_user_meta($current_user->ID, 'experience_title'.$i, sanitize_text_field($experience_title[$i]));
							update_user_meta($current_user->ID, 'experience_start'.$i, sanitize_text_field($experience_start[$i]));
							update_user_meta($current_user->ID, 'experience_end'.$i, sanitize_text_field($experience_end[$i]));
							update_user_meta($current_user->ID, 'experience_company'.$i, sanitize_text_field($experience_company[$i]));
							update_user_meta($current_user->ID, 'experience_description'.$i, sanitize_textarea_field($experience_description[$i]));
						}
					}
				}
				// End Work Experience
				// For Award Save
				// Delete 1st
				$i=0;
				for($i=0;$i<20;$i++){
					delete_user_meta($current_user->ID, 'award_title'.$i);
					delete_user_meta($current_user->ID, 'award_year'.$i);						
					delete_user_meta($current_user->ID, 'award_description'.$i);
				}
				// Delete End
				if(isset($form_data['award_title'] )){
					$award_title= $form_data['award_title']; //this is array data we sanitize later, when it save
					$award_year= $form_data['award_year']; //this is array data we sanitize later, when it save
					$award_description= $form_data['award_description'];
					$i=0;
					for($i=0;$i<20;$i++){
						if(isset($award_title[$i]) AND $award_title[$i]!=''){
							update_user_meta($current_user->ID, 'award_title'.$i, sanitize_text_field($award_title[$i]));
							update_user_meta($current_user->ID, 'award_year'.$i, sanitize_text_field($award_year[$i]));
							update_user_meta($current_user->ID, 'award_description'.$i, sanitize_textarea_field($award_description[$i]));
						}
					}
				}
				// End Award
				// Languages
				for($i=0;$i<20;$i++){
					delete_user_meta($current_user->ID, 'language'.$i);
					delete_user_meta($current_user->ID, 'language_level'.$i);
				}
				$language= $form_data['language']; //this is array data we sanitize later, when it save
				$language_level= $form_data['language_level']; //this is array data we sanitize later, when it save
				for($i=0;$i<20;$i++){
					if(isset($language[$i]) AND $language[$i]!=''){							
						update_user_meta($current_user->ID, 'language'.$i, sanitize_text_field($language[$i]));
					}
					if(isset($language_level[$i]) AND $language_level[$i]!=''){			
						update_user_meta($current_user->ID, 'language_level'.$i, sanitize_text_field($language_level[$i]));
					}
				}	
				// professional_skills***
				$specialties='';
				if(isset($form_data['professional_skills'])){
					foreach ($form_data['professional_skills'] as $specialty){
						$specialties= $specialties.','. sanitize_text_field($specialty);
					}
				}
				// For new professional_skill
				$new_professional_skills=$form_data['new_professional_skills'];
				$new_professional_skills_arr= explode(",",$new_professional_skills);
				foreach ($new_professional_skills_arr as $specialty1){
					$specialty1= sanitize_text_field($specialty1);
					wp_create_term( $specialty1,$directory_url.'_tag');
					$specialties= $specialties.','. $specialty1;									
				}								
				update_user_meta($current_user->ID, 'professional_skills', $specialties); 
				echo json_encode(array("code" => "success","msg"=>esc_html__( 'Updated Successfully', 'jobboard')));
				exit(0);
			}
			public function jobboard_total_job_count($userid, $allusers='no' ){
				$directory_url=get_option('epjbjobboard_url');
				if($directory_url==""){$directory_url='job';}
				if($allusers=='yes' ){
					$args = array(
					'post_type' => $directory_url, // enter your custom post type
					'paged' => '1',					
					'post_status' => 'publish',	
					'posts_per_page'=>'99999',  // overrides posts per page in theme settings
					);
					}else{
					$args = array(
					'post_type' => $directory_url, // enter your custom post type
					'paged' => '1',
					'author'=>$userid ,
					'post_status' => 'publish',	
					'posts_per_page'=>'99999',  // overrides posts per page in theme settings
					);
				}
				$job_count = new WP_Query( $args );
				$count = $job_count->found_posts;
				return $count;
			}
			public function jobboard_total_applications_count($jobid ){ 
				$directory_url2='job_apply';		
				$args_apply ='';
				$args_apply = array(
				'post_type' => $directory_url2, 
				'paged' => '1',	
				'post_status'=>'Private',
				'posts_per_page'=>'99999', 
				'meta_query' => array(
				array(
				'key' => 'apply_jod_id',
				'value' => $jobid,
				'compare' => '='
				)
				)					
				);				
				$apply_count = new WP_Query( $args_apply );				
				$count = $apply_count->found_posts;
				return $count;
			}
			public function iv_restrict_media_library( $wp_query ) {
				if(!function_exists('wp_get_current_user')) { include(ABSPATH . "wp-includes/pluggable.php"); }
				global $current_user, $pagenow;
				if( is_admin() && !current_user_can('edit_others_posts') ) {
					$wp_query->set( 'author', $current_user->ID );
					add_filter('views_edit-post', 'fix_post_counts');
					add_filter('views_upload', 'fix_media_counts');
				}
			}
			public function check_expiry_date($user) {
				require_once(wp_jobboard_DIR . '/inc/check_expire_date.php');
			}
			public function jobboard_update_profile_pic(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				global $current_user;
				if(isset($_REQUEST['profile_pic_url_1'])){
					$iv_profile_pic_url=esc_url_raw($_REQUEST['profile_pic_url_1']);
					$attachment_thum=esc_url_raw($_REQUEST['attachment_thum']);
					}else{
					$iv_profile_pic_url='';
					$attachment_thum='';
				}
				update_user_meta($current_user->ID, 'iv_profile_pic_thum', $attachment_thum);					
				update_user_meta($current_user->ID, 'iv_profile_pic_url', $iv_profile_pic_url);
				echo json_encode('success');
				exit(0);
			}
			public function jobboard_paypal_form_submit(  ) {
				require_once(wp_jobboard_DIR . '/admin/pages/payment-inc/paypal-submit.php');
			}	
			
			public function plugin_mce_css_jobboard( $mce_css ) {
				if ( ! empty( $mce_css ) )
				$mce_css .= ',';
				$mce_css .= plugins_url( 'admin/files/css/iv-bootstrap.css', __FILE__ );
				return $mce_css;
			}
			/***********************************
				* Adds a meta box to the post editing screen
			*/
			public function prfx_custom_meta_jobboard() {
				$directory_url=get_option('epjbjobboard_url');
				if($directory_url==""){$directory_url='job';}
				add_meta_box('prfx_meta', esc_html__('Claim Approve ', 'jobboard'), array(&$this, 'jobboard_meta_callback'),$directory_url,'side');
				add_meta_box('prfx_meta2', esc_html__('Listing Data  ', 'jobboard'), array(&$this, 'jobboard_meta_callback_full_data'),$directory_url,'advanced');
			}
			public function jobboard_check_coupon(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'signup' ) ) {
					echo json_encode(array("msg"=>"Are you cheating:wpnonce?"));						
					exit(0);
				}
				global $wpdb;
				$coupon_code=sanitize_text_field($_REQUEST['coupon_code']);
				$package_id=sanitize_text_field($_REQUEST['package_id']);					
				$package_amount=get_post_meta($package_id, 'jobboard_package_cost',true);
				$api_currency =sanitize_text_field($_REQUEST['api_currency']);
				$post_cont = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = '%s' and  post_type='jobboard_coupon'",$coupon_code ));	
				if(sizeof($post_cont)>0 && $package_amount>0){
					$coupon_name = $post_cont->post_title;
					$current_date=$today = date("m/d/Y");
					$start_date=get_post_meta($post_cont->ID, 'jobboard_coupon_start_date', true);
					$end_date=get_post_meta($post_cont->ID, 'jobboard_coupon_end_date', true);
					$coupon_used=get_post_meta($post_cont->ID, 'jobboard_coupon_used', true);
					$coupon_limit=get_post_meta($post_cont->ID, 'jobboard_coupon_limit', true);
					$dis_amount=get_post_meta($post_cont->ID, 'jobboard_coupon_amount', true);							 
					$package_ids =get_post_meta($post_cont->ID, 'jobboard_coupon_pac_id', true);
					$all_pac_arr= explode(",",$package_ids);
					$today_time = strtotime($current_date);
					$start_time = strtotime($start_date);
					$expire_time = strtotime($end_date);
					if(in_array('0', $all_pac_arr)){
						$pac_found=1;
						}else{
						if(in_array($package_id, $all_pac_arr)){
							$pac_found=1;
							}else{
							$pac_found=0;
						}
					}
					$recurring = get_post_meta( $package_id,'jobboard_package_recurring',true); 
					if($today_time >= $start_time && $today_time<=$expire_time && $coupon_used<=$coupon_limit && $pac_found == '1' && $recurring!='on' ){
						$total = $package_amount -$dis_amount;
						$coupon_type= get_post_meta($post_cont->ID, 'jobboard_coupon_type', true);
						if($coupon_type=='percentage'){
							$dis_amount= $dis_amount * $package_amount/100;
							$total = $package_amount -$dis_amount ;
						}
						echo json_encode(array('code' => 'success',
						'dis_amount' => $dis_amount.' '.$api_currency,
						'gtotal' => $total.' '.$api_currency,
						'p_amount' => $package_amount.' '.$api_currency,
						));
						exit(0);
						}else{
						$dis_amount='';
						$total=$package_amount;
						echo json_encode(array('code' => 'not-success-2',
						'dis_amount' => '',
						'gtotal' => $total.' '.$api_currency,
						'p_amount' => $package_amount.' '.$api_currency,
						));
						exit(0);
					}
					}else{
					if($package_amount=="" or $package_amount=="0"){$package_amount='0';}
					$dis_amount='';
					$total=$package_amount;
					echo json_encode(array('code' => 'not-success-1',
					'dis_amount' => '',
					'gtotal' => $total.' '.$api_currency,
					'p_amount' => $package_amount.' '.$api_currency,
					));
					exit(0);
				}
			}
			public function jobboard_check_package_amount(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'signup' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				global $wpdb;
				$coupon_code=(isset($_REQUEST['coupon_code'])? sanitize_text_field($_REQUEST['coupon_code']):'');
				$package_id=sanitize_text_field($_REQUEST['package_id']);
				if( get_post_meta( $package_id,'jobboard_package_recurring',true) =='on'  ){
					$package_amount=get_post_meta($package_id, 'jobboard_package_recurring_cost_initial', true);			
					}else{					
					$package_amount=get_post_meta($package_id, 'jobboard_package_cost',true);
				}
				$api_currency =sanitize_text_field($_REQUEST['api_currency']);			
				$iv_gateway = get_option('jobboard_payment_gateway');
				if($iv_gateway=='woocommerce'){
					if ( class_exists( 'WooCommerce' ) ) {	
						$api_currency= get_option( 'woocommerce_currency' );
						$api_currency= get_woocommerce_currency_symbol( $api_currency );
					}
				}		
				$post_cont = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = '%s' and  post_type='jobboard_coupon'", $coupon_code));	
				if(isset($post_cont->ID)){
					$coupon_name = $post_cont->post_title;
					$current_date=$today = date("m/d/Y");
					$start_date=get_post_meta($post_cont->ID, 'jobboard_coupon_start_date', true);
					$end_date=get_post_meta($post_cont->ID, 'jobboard_coupon_end_date', true);
					$coupon_used=get_post_meta($post_cont->ID, 'jobboard_coupon_used', true);
					$coupon_limit=get_post_meta($post_cont->ID, 'jobboard_coupon_limit', true);
					$dis_amount=get_post_meta($post_cont->ID, 'jobboard_coupon_amount', true);							 
					$package_ids =get_post_meta($post_cont->ID, 'jobboard_coupon_pac_id', true);
					$all_pac_arr= explode(",",$package_ids);
					$today_time = strtotime($current_date);
					$start_time = strtotime($start_date);
					$expire_time = strtotime($end_date);
					$pac_found= in_array($package_id, $all_pac_arr);							
					if($today_time >= $start_time && $today_time<=$expire_time && $coupon_used<=$coupon_limit && $pac_found=="1"){
						$total = $package_amount -$dis_amount;
						echo json_encode(array('code' => 'success',
						'dis_amount' => $api_currency.' '.$dis_amount,
						'gtotal' => $api_currency.' '.$total,
						'p_amount' => $api_currency.' '.$package_amount,
						));
						exit(0);
						}else{
						$dis_amount='--';
						$total=$package_amount;
						echo json_encode(array('code' => 'not-success',
						'dis_amount' => $api_currency.' '.$dis_amount,
						'gtotal' => $api_currency.' '.$total,
						'p_amount' => $api_currency.' '.$package_amount,
						));
						exit(0);
					}
					}else{
					$dis_amount='--';
					$total=$package_amount;
					echo json_encode(array('code' => 'not-success',
					'dis_amount' => $api_currency.' '.$dis_amount,
					'gtotal' => $api_currency.' '.$total,
					'p_amount' => $api_currency.' '.$package_amount,
					));
					exit(0);
				}
			}
			/**
				* Outputs the content of the meta box
			*/
			public function jobboard_meta_callback($post) {
				wp_nonce_field(basename(__FILE__), 'prfx_nonce');
				require_once ('admin/pages/metabox.php');
			}
			public function jobboard_meta_callback_full_data(){
				require_once ('admin/pages/metabox_full_data.php');
			}
			public function jobboard_meta_save($post_id) {
				global $wpdb;
				$is_autosave = wp_is_post_autosave($post_id);
				if (isset($_REQUEST['jobboard_approve'])) {
					if($_REQUEST['jobboard_approve']=='yes'){ 
						update_post_meta($post_id, 'jobboard_approve', sanitize_text_field($_REQUEST['jobboard_approve']));
						// Set new user for post							
						$jobboard_author_id= sanitize_text_field($_REQUEST['jobboard_author_id']);
						$sql=$wpdb->prepare("UPDATE  $wpdb->posts SET post_author=%d  WHERE ID=$d",$jobboard_author_id,$post_id );		
						$wpdb->query($sql); 					
					}
				} 
				if (isset($_REQUEST['jobboard_featured'])) {							
					update_post_meta($post_id, 'jobboard_featured', sanitize_text_field($_REQUEST['jobboard_featured']));
				}
				if (isset($_REQUEST['listing_data_submit'])) { 
					$newpost_id=$post_id;
					update_post_meta($newpost_id, 'job_status', sanitize_text_field($_REQUEST['job_type'])); 
					$default_fields = array();
					$field_set=get_option('jobboard_fields' );
					if($field_set!=""){ 
						$default_fields=get_option('jobboard_fields' );
						}else{															
						$default_fields['other_link']=esc_html__('Other Link','jobboard');
					}
					if(sizeof($default_fields )){			
						foreach( $default_fields as $field_key => $field_value ) { 
							update_post_meta($newpost_id, $field_key, sanitize_text_field($_REQUEST[$field_key]) );							
						}					
					}
					update_post_meta($newpost_id, 'job_education', wp_kses( $_REQUEST['content_education'], $allowed_html));	
					update_post_meta($newpost_id, 'job_must_have', wp_kses( $_REQUEST['content_must_have'], $allowed_html));
					update_post_meta($newpost_id, 'address', sanitize_text_field($_REQUEST['address'])); 
					update_post_meta($newpost_id, 'latitude', sanitize_text_field($_REQUEST['latitude'])); 
					update_post_meta($newpost_id, 'longitude', sanitize_text_field($_REQUEST['longitude']));					
					update_post_meta($newpost_id, 'city', sanitize_text_field($_REQUEST['city'])); 
					update_post_meta($newpost_id, 'state', sanitize_text_field($_REQUEST['state'])); 
					update_post_meta($newpost_id, 'postcode', sanitize_text_field($_REQUEST['postcode'])); 
					update_post_meta($newpost_id, 'country', sanitize_text_field($_REQUEST['country'])); 
					update_post_meta($newpost_id, 'local-area', sanitize_text_field($_REQUEST['local-area'])); 
					// Get latlng from address* START********
					// Get latlng from address* ENDDDDDD********	
					// job detail*****
					update_post_meta($newpost_id, 'job_status', sanitize_text_field($_REQUEST['job_type'])); 
					update_post_meta($newpost_id, 'educational_requirements', sanitize_text_field($_REQUEST['educational_requirements'])); 
					update_post_meta($newpost_id, 'job_type', sanitize_text_field($_REQUEST['job_type'])); 
					update_post_meta($newpost_id, 'job_level', sanitize_text_field($_REQUEST['job_level'])); 
					update_post_meta($newpost_id, 'experience_range', sanitize_text_field($_REQUEST['experience_range'])); 
					update_post_meta($newpost_id, 'age_range', sanitize_text_field($_REQUEST['age_range'])); 
					update_post_meta($newpost_id, 'gender', sanitize_text_field($_REQUEST['gender'])); 
					update_post_meta($newpost_id, 'vacancy', sanitize_text_field($_REQUEST['vacancy'])); 
					update_post_meta($newpost_id, 'deadline', sanitize_text_field($_REQUEST['deadline'])); 
					update_post_meta($newpost_id, 'workplace', sanitize_text_field($_REQUEST['workplace']));
					update_post_meta($newpost_id, 'salary', sanitize_text_field($_REQUEST['salary']));
					update_post_meta($newpost_id, 'other_benefits', sanitize_text_field($_REQUEST['other_benefits']));
					if(isset($_REQUEST['dirpro_email_button'])){						
						update_post_meta($newpost_id, 'dirpro_email_button', sanitize_text_field($_REQUEST['dirpro_email_button'])); 
					}
					if(isset($_REQUEST['dirpro_web_button'])){						
						update_post_meta($newpost_id, 'dirpro_web_button', sanitize_text_field($_REQUEST['dirpro_web_button'])); 
					}
					update_post_meta($newpost_id, 'image_gallery_ids', sanitize_text_field($_REQUEST['gallery_image_ids'])); 
					if(isset($_REQUEST['feature_image_id'] )){
						$attach_id =sanitize_text_field($_REQUEST['feature_image_id']);
						set_post_thumbnail( $newpost_id, $attach_id );					
					}	
					update_post_meta($newpost_id, 'listing_contact_source', sanitize_text_field($_REQUEST['contact_source']));  
					update_post_meta($newpost_id, 'company_name', sanitize_text_field($_REQUEST['company_name']));
					update_post_meta($newpost_id, 'phone', sanitize_text_field($_REQUEST['phone'])); 
					update_post_meta($newpost_id, 'address', sanitize_text_field($_REQUEST['address'])); 
					update_post_meta($newpost_id, 'contact-email', sanitize_text_field($_REQUEST['contact-email'])); 
					update_post_meta($newpost_id, 'contact_web', sanitize_text_field($_REQUEST['contact_web']));
					update_post_meta($newpost_id, 'vimeo', sanitize_text_field($_REQUEST['vimeo'])); 
					update_post_meta($newpost_id, 'youtube', sanitize_text_field($_REQUEST['youtube'])); 
					delete_post_meta($newpost_id, 'jobboard-tags');
					delete_post_meta($newpost_id, 'jobboard-category');
										
				
					if($_POST['post_status']=='publish'){ 
						include( wp_jobboard_ABSPATH. 'inc/notification.php');
					}
						
				}
			}
			/**
				* Checks that the WordPress setup meets the plugin requirements
				* @global string $wp_version
				* @return boolean
			*/
			private function check_requirements() {
				global $wp_version;
				if (!version_compare($wp_version, $this->wp_version, '>=')) {
					add_action('admin_notices', 'wp_jobboard::display_req_notice');
					return false;
				}
				return true;
			}
			/**
				* Display the requirement notice
				* @static
			*/
			static function display_req_notice() {
				global $wp_jobboard;
				echo '<div id="message" class="error"><p><strong>';
				echo esc_html__('Sorry, BootstrapPress re requires WordPress ' . $wp_jobboard->wp_version . ' or higher.
				Please upgrade your WordPress setup', 'jobboard');
				echo '</strong></p></div>';
			}
			private function load_dependencies() {
				// Admin Panel
				if (is_admin()) {						
					require_once ('admin/notifications.php');					
				require_once ('admin/admin.php');					}
				// Front-End Site
				if (!is_admin()) {
				}
				// Global
			}
			/**
				* Called every time the plug-in is activated.
			*/
			
			public function activate() {
				require_once ('install/install.php');
			}
			/**
				* Called when the plug-in is deactivated.
			*/
			public function deactivate() {
				global $wpdb;
				if ( !is_plugin_active('jobboard/plugin.php') ) {
					$page_name='price-table';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."'";
					$wpdb->query($query);
					$page_name='registration';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."'";
					$wpdb->query($query);
					$page_name='my-account';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";
					$wpdb->query($query);
					$page_name='agent-public';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";
					$wpdb->query($query);
					$page_name='thank-you';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";
					$wpdb->query($query);
					$page_name='login';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."'";				
					$wpdb->query($query);
					$page_name='candidate-directory';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";
					$wpdb->query($query);
					$page_name='candidate-public';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";				
					$wpdb->query($query);
					$page_name='employer-directory';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";				
					$wpdb->query($query);
					$page_name='employer-public';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";				
					$wpdb->query($query);
					$page_name='iv-reminder-email-cron-job';						
					$query = "delete from {$wpdb->prefix}posts where  post_name='".$page_name."' ";
					$wpdb->query($query);
				}
			}
			/**
				* Called when the plug-in is uninstalled
			*/
			static function uninstall() {
			}
			/**
				* Register the widgets
			*/
			public function register_widget() {
			}
			/**
				* Internationalization
			*/
			public function i18n() {
				load_plugin_textdomain('jobboard', false, basename(dirname(__FILE__)) . '/languages/' );
			}
			/**
				* Starts the plug-in main functionality
			*/
			public function start() {
			}
			public function jobboard_price_table_func($atts = '', $content = '') {									
				ob_start();					  //include the specified file
				include( wp_jobboard_template. 'price-table/price-table-1.php');
				$content = ob_get_clean();	
				return $content;
			}
			public function jobboard_form_wizard_func($atts = '') {
				global $current_user;
				$template_path=wp_jobboard_template.'signup/';
				ob_start();	 //include the specified file
				if($current_user->ID==0){					
						include( $template_path. 'wizard-style-2.php');										
				}else{						  
						include( wp_jobboard_template. 'private-profile/profile-template-1.php');
				}
				$content = ob_get_clean();	
				return $content;
			}
			public function jobboard_profile_template_func($atts = '') {
				global $current_user;
				ob_start();
				if($current_user->ID==0){
					require_once(wp_jobboard_template. 'private-profile/profile-login.php');
					}else{					  
					include( wp_jobboard_template. 'private-profile/profile-template-1.php');
				}
				$content = ob_get_clean();	
				return $content;
			}
			public function jobboard_reminder_email_cron_func ($atts = ''){
				include( wp_jobboard_ABSPATH. 'inc/reminder-email-cron.php');
			}
			public function jobboard_cron_job(){
				include( wp_jobboard_ABSPATH. 'inc/all_cron_job.php');
				exit(0);
			}
			public function jobboard_categories_func($atts = ''){
				ob_start();	
				if(isset($atts['style']) and $atts['style']!="" ){
					$tempale=$atts['style']; 
					}else{
					$tempale=get_option('jobboard_categories'); 
				}
				if($tempale==''){
					$tempale='style-1';
				}						
				//include the specified file
				if($tempale=='style-1'){
					include( wp_jobboard_template. 'listing/jobboard_categories.php');
				}
				$content = ob_get_clean();
				return $content;	
			}
			public function jobboard_cities_func($atts = ''){
				ob_start();	
				include( wp_jobboard_template. 'listing/listing-cities.php');
				$content = ob_get_clean();
				return $content;
			}
			public function listing_carousel_func($atts = ''){
				ob_start();	
				include( wp_jobboard_template. 'listing/listing-carousel.php');
				$content = ob_get_clean();
				return $content;
			}	
			public function slider_search_func($atts = ''){
				ob_start();	
				include( wp_jobboard_template. 'listing/slider-search.php');
				$content = ob_get_clean();
				return $content;
			}
			public function jobboard_map_func($atts = ''){
				ob_start();	
				include( wp_jobboard_template. 'listing/job-map.php');
				$content = ob_get_clean();
				return $content;
			}				
			public function jobboard_featured_func($atts = ''){
				ob_start();	
				if(isset($atts['style']) and $atts['style']!="" ){
					$tempale=$atts['style']; 
					}else{
					$tempale=get_option('jobboard_featured'); 
				}
				if($tempale==''){
					$tempale='style-1';
				}						
				//include the specified file
				if($tempale=='style-1'){
					include( wp_jobboard_template. 'listing/jobboard_featured.php');
				}
				$content = ob_get_clean();
				return $content;	
			}		
			public function jobboard_all_jobs_grid_popup_func($atts=''){
				ob_start();	
				include( wp_jobboard_template. 'listing/archive-job-style-grid-popup.php');
				$content = ob_get_clean();
				return $content;
			}
			public function jobboard_all_jobs_grid_func($atts=''){
				ob_start();	
				include( wp_jobboard_template. 'listing/archive-job-style-grid.php');
				$content = ob_get_clean();
				return $content;
			}
			public function jobboard_all_jobs_func($atts=''){
				ob_start();	
				include( wp_jobboard_template. 'listing/archive-job-style-2.php');
				$content = ob_get_clean();
				return $content;
			}
			public function listing_filter_func($atts=''){
				ob_start();	
				include( wp_jobboard_template. 'listing/job-filter.php');
				$content = ob_get_clean();
				return $content;				
			}
			public function jobs_employer_directory_func($atts = ''){
				global $current_user;	
				ob_start(); //include the specified file					
				include( wp_jobboard_template. 'user-directory/employer-directory.php');
				$content = ob_get_clean();
				return $content;	
			}
			public function jobs_candidate_directory_func($atts = ''){
				global $current_user;	
				ob_start(); //include the specified file					
				include( wp_jobboard_template. 'user-directory/candidate-directory.php');
				$content = ob_get_clean();
				return $content;	
			}
			public function get_unique_location_values($post_type , $key = 'keyword' ){
				global $wpdb;
				$post_type=get_option('epjbjobboard_url');
				if($post_type==""){$post_type='job';}
				$all_data=array();
				// Area**
				$dir_facet_title=get_option('dir_facet_area_title');
				if($dir_facet_title==""){$dir_facet_title= esc_html__('Area','jobboard');}
				$res=array();
				$key = 'area';
				$res = $wpdb->get_col( $wpdb->prepare( "
				SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type='{$post_type}' AND  pm.meta_key = '%s'						
				", $key) );						
				foreach($res as $row1){							
					$row_data=array();
					if(!empty($row1)){
						$row_data['label']=$row1;
						$row_data['value']=$row1;
						$row_data['category']= $dir_facet_title;
						array_push( $all_data, $row_data );
					}
				}
				// City ***
				$dir_facet_title=get_option('dir_facet_location_title');
				if($dir_facet_title==""){$dir_facet_title= esc_html__('City','jobboard');}
				$res=array();
				$key = 'city';
				$res = $wpdb->get_col( $wpdb->prepare( "
				SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type='{$post_type}' AND  pm.meta_key = '%s'						
				", $key) );						
				foreach($res as $row1){							
					$row_data=array();
					if(!empty($row1)){
						$row_data['label']=$row1;
						$row_data['value']=$row1;
						$row_data['category']= $dir_facet_title;
						array_push( $all_data, $row_data );
					}	
				}
				// Zipcode ***
				$dir_facet_title=get_option('dir_facet_zipcode_title');
				if($dir_facet_title==""){$dir_facet_title= esc_html__('Zipcode','jobboard');}
				$res=array();
				$key = 'postcode';
				$res = $wpdb->get_col( $wpdb->prepare( "
				SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
				LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE p.post_type='{$post_type}' AND  pm.meta_key = '%s'						
				", $key) );						
				foreach($res as $row1){							
					$row_data=array();
					if(!empty($row1)){
						$row_data['label']=$row1;
						$row_data['value']=$row1;
						$row_data['category']= $dir_facet_title;
						array_push( $all_data, $row_data );
					}	
				}
				$all_data_json= json_encode($all_data);		
				return $all_data_json;
			}
			public function get_unique_search_values(){						
				global $wpdb;
				$post_type=get_option('epjbjobboard_url');
				if($post_type==""){$post_type='job';}
				$res=array();
				$all_data=array();						
				$partners = array();
				$partners_obj =  get_terms( $post_type.'-category', array('hide_empty' => true) );
				$dir_facet_title=get_option('dir_facet_cat_title');
				if($dir_facet_title==""){$dir_facet_title= esc_html__('Categories','jobboard');}
				foreach ($partners_obj as $partner) {
					$row_data=array();
					$row_data['label']=$partner->name.'['.$partner->count.']';
					$row_data['value']=$partner->name;
					$row_data['category']= $dir_facet_title;
					array_push( $all_data, $row_data );
				}
				// For tags
				$dir_facet_title=get_option('dir_facet_features_title');
				if($dir_facet_title==""){$dir_facet_title= esc_html__('Features','jobboard');}
				$dir_tags=get_option('epjbdir_tags');	
				if($dir_tags==""){$dir_tags='yes';}	
				if($dir_tags=="yes"){
					$partners = array();
					$partners_obj =  get_terms( $post_type.'_tag', array('hide_empty' => true) );
					foreach ($partners_obj as $partner) {
						$row_data=array();
						$row_data['label']=$partner->name.'['.$partner->count.']';
						$row_data['value']=$partner->name;
						$row_data['category']=$dir_facet_title;
						array_push( $all_data, $row_data );
					}
					}else{
					$args =array();
					$args['hide_empty']=true;
					$tags = get_tags($args );
					foreach ( $tags as $tag ) { 
						$row_data=array();
						$row_data['label']=$tag->name.'['.$tag->count.']';
						$row_data['value']=$tag->name;
						$row_data['category']=$dir_facet_title;
						array_push( $all_data, $row_data );
					}							
				}
				// End Tags	****					
				$args3 = array(
				'post_type' => $post_type, // enter your custom post type						
				'post_status' => 'publish',						
				'posts_per_page'=> -1,  // overrides posts per page in theme settings
				'orderby' => 'title',
				'order' => 'ASC',
				);
				$all_data_json=array();
				$query_auto = new WP_Query( $args3 );
				$posts_auto = $query_auto->posts;						
				foreach($posts_auto as $post_a) {
					$row_data=array();  
					$row_data['label']=$post_a->post_title;
					$row_data['value']=$post_a->post_title;
					$row_data['category']= esc_html__('Title','jobboard');
					array_push( $all_data, $row_data );
				}						
				$all_data_json= json_encode($all_data);	
				return $all_data_json;
			}
			public function jobboard_candidate_profile_public_func($atts = '') {	
				ob_start();						  //include the specified file
				include( wp_jobboard_template. 'profile-public/candidate-profile.php');							
				$content = ob_get_clean();	
				return $content;
			}
			public function jobboard_employer_profile_public_func($atts = '') {	
				ob_start();						  //include the specified file
				include( wp_jobboard_template. 'profile-public/employer-profile.php');							
				$content = ob_get_clean();	
				return $content;
			}
			public function ep_create_my_taxonomy_tags(){
				$directory_url=get_option('epjbjobboard_url');
				if($directory_url==""){$directory_url='job';}
				$dir_tags=get_option('epjbdir_tags');	
				if($dir_tags==""){$dir_tags='yes';}	
				if($dir_tags=='yes'){
					register_taxonomy(
					$directory_url.'_tag',
					$directory_url,
					array(
					'label' => esc_html__( 'Tags', 'jobboard'),
					'rewrite' => array( 'slug' => $directory_url.'_tag' ),
					'description'         => esc_html__( 'Tags', 'jobboard' ),
					'hierarchical' => true,
					'show_in_rest' =>	true,
					)
					);						
				}
			}		
			public function jobboard_save_favorite(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'_favorites',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites.', '.get_current_user_id();
				update_post_meta($dir_id,'_favorites',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'_dir_favorites', true);						
				$old_favorites2 = str_replace($dir_id ,' ',  $old_favorites2);
				$new_favorites2=$old_favorites2.', '.$dir_id;
				update_user_meta(get_current_user_id(),'_dir_favorites',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);	
			}
			public function jobboard_applied_delete(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'job_apply_all',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites;
				update_post_meta($dir_id,'job_apply_all',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'job_apply_all', true);						
				$old_favorites2 = str_replace($dir_id ,' ',  $old_favorites2);
				$new_favorites2=$old_favorites2;
				update_user_meta(get_current_user_id(),'job_apply_all',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);	
			}
			public function jobboard_save_un_favorite(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'_favorites',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites;
				update_post_meta($dir_id,'_favorites',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'_dir_favorites', true);						
				$old_favorites2 = str_replace($dir_id ,' ',  $old_favorites2);
				$new_favorites2=$old_favorites2;
				update_user_meta(get_current_user_id(),'_dir_favorites',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);	
			}
			public function jobboard_save_notification(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);	
				get_current_user_id();
				$notification_value=array();
				$notification= $form_data['notificationone']; //this is array data we sanitize later, when it save
				foreach($notification as $notification_one){
					if( $notification_one!=''){							
						$notification_value[]= sanitize_text_field($notification_one);
					}
				}	
				update_user_meta(get_current_user_id(),'job_notifications',$notification_value);
				echo json_encode(array("code" => "success","msg"=>"Updated Successfully"));
				exit(0);	
			}
			public function jobboard_candidate_schedule(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);	
				$dir_id=sanitize_text_field($form_data['dir_id']);	
				$already_meeting=get_post_meta($dir_id,'candidate_schedule',true);
				update_post_meta($dir_id,'candidate_schedule','yes');
				update_post_meta($dir_id,'candidate_schedule_time',sanitize_text_field($form_data['meeting_date']));
				update_post_meta($dir_id,'candidate_schedule_note',sanitize_text_field($form_data['message-content']));
				echo json_encode(array("msg" => 'success', 'already_meeting'=>$already_meeting ));
				exit(0);
			}
			public function jobboard_candidate_shortlisted(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}				
				parse_str($_POST['data'], $form_data);	
				$dir_id=sanitize_text_field($form_data['id']);	
				if(isset($form_data['shortlisted'])){
					update_post_meta($dir_id,'candidate_shortlisted','no');
					}else{
					update_post_meta($dir_id,'candidate_shortlisted','yes');
				}
				echo json_encode(array("msg" => 'success'));
				exit(0);	
			}
			public function jobboard_profile_bookmark(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'jobboard_profilebookmark',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites.', '.get_current_user_id();
				update_post_meta($dir_id,'jobboard_profilebookmark',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'jobboard_profilebookmark', true);						
				$old_favorites2 = str_replace($dir_id ,' ',  $old_favorites2);
				$new_favorites2=$old_favorites2.', '.$dir_id;
				update_user_meta(get_current_user_id(),'jobboard_profilebookmark',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);	
			}
			public function jobboard_profile_bookmark_delete(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'jobboard_profilebookmark',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites;
				update_post_meta($dir_id,'jobboard_profilebookmark',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'jobboard_profilebookmark', true);						
				$old_favorites2 = str_replace($dir_id ,'',  $old_favorites2);
				$new_favorites2=$old_favorites2;
				update_user_meta(get_current_user_id(),'jobboard_profilebookmark',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);		
			}
			public function jobboard_employer_bookmark(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'jobboard_employerbookmark',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites.', '.get_current_user_id();
				update_post_meta($dir_id,'jobboard_employerbookmark',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'jobboard_employerbookmark', true);						
				$old_favorites2 = str_replace($dir_id ,' ',  $old_favorites2);
				$new_favorites2=$old_favorites2.', '.$dir_id;
				update_user_meta(get_current_user_id(),'jobboard_employerbookmark',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);	
			}
			public function jobboard_message_delete(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);
				global $current_user;
				$message_id=sanitize_text_field($form_data['id']);
				$user_to=get_post_meta($message_id,'user_to',true);	
				if($user_to==$current_user->ID){				
					wp_delete_post($message_id);
					delete_post_meta($message_id,true);	
					echo json_encode(array("msg" => 'success'));
					}else{
					echo json_encode(array("msg" => 'Not success'));
				}
				exit(0);		
			}
			public function jobboard_employer_bookmark_delete(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);
				$old_favorites= get_post_meta($dir_id,'jobboard_employerbookmark',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites;
				update_post_meta($dir_id,'jobboard_employerbookmark',$new_favorites);
				$old_favorites2=get_user_meta(get_current_user_id(),'jobboard_employerbookmark', true);						
				$old_favorites2 = str_replace($dir_id ,'',  $old_favorites2);
				$new_favorites2=$old_favorites2;
				update_user_meta(get_current_user_id(),'jobboard_employerbookmark',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);		
			}
			public function jobboard_candidate_delete(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				global $current_user;
				parse_str($_POST['data'], $form_data);	
				$post_id=sanitize_text_field($form_data['id']);				
				$job_post_id= get_post_meta($post_id,'apply_jod_id',true);
				$post_edit = get_post($job_post_id);				
				$success='0';
				if($post_edit){
					if($post_edit->post_author==$current_user->ID){
						wp_delete_post($post_id);
						delete_post_meta($post_id,true);
						$success='1';
					}
					if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
						wp_delete_post($post_id);
						delete_post_meta($post_id,true);								
						$success='1';
					}	
				}
				if($success=='1'){
					echo json_encode(array("msg" => 'success'));
					}else{
					echo json_encode(array("msg" => 'not-success'));
				}				
				exit(0);
			}
			public function jobboard_candidate_reject(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				global $current_user;
				parse_str($_POST['data'], $form_data);							
				$post_id=sanitize_text_field($form_data['id']);				
				$job_post_id= get_post_meta($post_id,'apply_jod_id',true);
				$post_edit = get_post($job_post_id);				
				$success='0';
				if(isset($form_data['reject'])){
					if($post_edit->post_author==$current_user->ID){ 
						update_post_meta($post_id,'candidate_reject','no');		
						$success='1';
					}
					if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){ 
						update_post_meta($post_id,'candidate_reject','no');							
						$success='1';
					}	
					}else{
					if($post_edit){
						if($post_edit->post_author==$current_user->ID){ 
							update_post_meta($post_id,'candidate_reject','yes');		
							$success='1';
						}
						if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){ 
							update_post_meta($post_id,'candidate_reject','yes');							
							$success='1';
						}	
					}
				}
				if($success=='1'){
					echo json_encode(array("msg" => 'success'));
					}else{
					echo json_encode(array("msg" => 'not-success'));
				}		
				exit(0);
			}
			public function jobboard_delete_favorite(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'myaccount' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['data'], $form_data);					
				$dir_id=sanitize_text_field($form_data['id']);						
				$old_favorites= get_post_meta($dir_id,'_favorites',true);
				$old_favorites = str_replace(get_current_user_id(), '',  $old_favorites);
				$new_favorites=$old_favorites;
				update_post_meta($dir_id,'_favorites',$new_favorites);						
				$old_favorites2=get_user_meta(get_current_user_id(),'_dir_favorites', true);						
				$old_favorites2 = str_replace($dir_id ,' ',  $old_favorites2);						
				$new_favorites2=$old_favorites2;
				update_user_meta(get_current_user_id(),'_dir_favorites',$new_favorites2);
				echo json_encode(array("msg" => 'success'));
				exit(0);
			}
			public function jobboard_message_send(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);					
				// Create new message post
				$allowed_html = wp_kses_allowed_html( 'post' );					
				if(isset($form_data['dir_id'])){
					if($form_data['dir_id']>0){
						$dir_id=sanitize_text_field($form_data['dir_id']);
						$dir_detail= get_post($dir_id); 
						$dir_title= '<a href="'.get_permalink($dir_id).'">'.$dir_detail->post_title.'</a>';
						$user_id=$dir_detail->post_author;
						$user_info = get_userdata( $user_id);
						$client_email_address =$user_info->user_email;
						$userid_to=$user_id;
					}
				}
				if(isset($form_data['user_id'])){
					if($form_data['user_id']!=''){
						$dir_title= '';
						$user_info = get_userdata(sanitize_text_field($form_data['user_id']));
						$client_email_address =$user_info->user_email;
						$userid_to=sanitize_text_field($form_data['user_id']);
					}
				}
				$new_nessage= esc_html__( 'New Message', 'jobboard' );
				$my_post=array();
				$subject=$new_nessage;
				if(isset($form_data['subject'])){
					$subject=sanitize_text_field($form_data['subject']);
				} 
				$my_post['post_title'] =$subject;
				$my_post['post_content'] = wp_kses( $form_data['message-content'], $allowed_html); 
				$my_post['post_type'] = 'jobboard_message';
				$my_post['post_status']='private';												
				$newpost_id= wp_insert_post( $my_post );
				Update_post_meta($newpost_id,'user_to', $userid_to );
				Update_post_meta($newpost_id,'dir_url', $dir_title );				
				Update_post_meta($newpost_id,'from_email',sanitize_email($form_data['email_address']) );
				if(isset($form_data['name'])){
					Update_post_meta($newpost_id,'from_name', sanitize_text_field($form_data['name']) );
				}
				Update_post_meta($newpost_id,'from_phone', sanitize_text_field($form_data['visitorphone']) );
				include( wp_jobboard_ABSPATH. 'inc/message-mail.php');	
				echo json_encode(array("msg" => esc_html__( 'Message Sent', 'jobboard' )));
				exit(0);
			}
			public function jobboard_claim_send(){
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'contact' ) ) {
					wp_die( 'Are you cheating:wpnonce?' );
				}
				parse_str($_POST['form_data'], $form_data);					
				include( wp_jobboard_ABSPATH. 'inc/claim-mail.php');	
				echo json_encode(array("msg" => esc_html__( 'Message Sent', 'jobboard' )));
				exit(0);
			}
			public function check_listing_expire_date($listin_id, $owner_id,$directory_url){ 					
				$exp_date= get_post_meta($listin_id, 'deadline', true);
				if($exp_date!=''){					
					if(strtotime($exp_date) < time()){
						$dir_post = array();
						$dir_post['ID'] = $listin_id;
						$dir_post['post_status'] = 'draft';	
						$dir_post['post_type'] = $directory_url;	
						wp_update_post( $dir_post );
						update_post_meta($listin_id, 'jobboard_featured', 'no' );
					}						
				}
			}
			public function paging() {
				global $wp_query;
			} 
			public function check_write_access($arg=''){
				global $current_user;
				$userId=$current_user->ID;
				if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
					return true;
				}		
				$package_id=get_user_meta($userId,'jobboard_package_id',true);
				$access=get_post_meta($package_id, 'jobboard_package_'.$arg, true);
				if($access=='yes'){
					return true;
					}else{
					return false;
				}
			} 
			public function check_reading_access($arg='',$id=0){
				global $post;
				global $current_user;
				$userId=$current_user->ID;
				if($id>0){
					$post = get_post($id);
				}	
				if($post->post_author==$userId){
					return true;
				}
				$package_id=get_user_meta($userId,'jobboard_package_id',true);					 
				$access=get_post_meta($package_id, 'jobboard_package_'.$arg, true);
				$active_module=get_option('epjbjobboard_active_visibility'); 
				if($active_module=='yes' ){		
					if(isset($current_user->ID) AND $current_user->ID!=''){
						$user_role= $current_user->roles[0];
						if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
							return true;
						}																
						}else{							
						$user_role= 'visitor';
					}	
					$store_array=get_option('epjbiv_visibility_serialize_role');	
					if(isset($store_array[$user_role]))
					{	
						if(in_array($arg, $store_array[$user_role])){
							return true;
							}else{
							return false;
						}
						}else{ 
						return false;
					}
					}else{
					return true;
				}
			}
		}
	}
	/*
		* Creates a new instance of the BoilerPlate Class
	*/
	function jobboardBootstraplight() {
		return wp_jobboard::instance();
	}
jobboardBootstraplight(); ?>