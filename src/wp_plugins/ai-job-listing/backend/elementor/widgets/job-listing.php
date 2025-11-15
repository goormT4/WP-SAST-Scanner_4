<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 0.1.0
 */
class Axilweb_Ajl_Elementor_Ai_Job_Listing_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'ai-job-listing';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Ai Job Listing', 'ai-job-listing' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-code';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url() {
		// Using site URL instead of external URL to comply with WordPress guidelines
		return admin_url('admin.php?page=ai-job-listing-help');
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the oEmbed widget belongs to.
	 *
	 * @since 0.1.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'Joblisting', 'url', 'link' ];
	}

	public  function axilweb_ai_job_listing_get_pages()
	{

		$page_list = get_posts(array(
			'post_type' => 'page',
			'orderby' => 'date',
			'order' => 'DESC',
			'posts_per_page' => -1,
		));

		$pages = array();

		if (!empty($page_list) && !is_wp_error($page_list)) {
			foreach ($page_list as $page) {
				$pages[$page->ID] = $page->post_title;
			}
		}

		return $pages;
	} 

	/**
	 * Registers the widget controls for the "Content" section in Elementor.
	 *
	 * This function defines and registers the various controls (fields) available for the user
	 * in the Elementor panel under the "Content" section. These controls allow the user to
	 * configure settings for the job listings widget such as job ordering, job limit, link text,
	 * and link page selection.
	 *
	 * The controls added here will be reflected in the Elementor editor, allowing the user
	 * to interact with these settings while customizing the widget.
	 *
	 * @return void
	 */ 
	protected function register_controls() { 
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'ai-job-listing' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'post_ordering',
            array(
                'label'   => __( 'Jobs Ordering', 'ai-job-listing' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => array(
                    'DESC' => esc_html__( 'Desecending', 'ai-job-listing' ),
                    'ASC'  => esc_html__( 'Ascending', 'ai-job-listing' ),
                ),
                'default' => 'DESC',
            )
        );

		$this->add_control(
			'job_limit',
			[
				'label' => esc_html__('Jobs Limit','ai-job-listing'),
				'type' =>  \Elementor\Controls_Manager::TEXT,
				'default' => '6',
				'title' => esc_html__('Enter Job Limits','ai-job-listing'),
			]
		);

		$this->add_control(
			'title_link_text',
			[
				'label' => esc_html__('Link Text','ai-job-listing'),
				'type' =>  \Elementor\Controls_Manager::TEXT,
				'default' => 'Load More',
				'title' => esc_html__('Enter button text','ai-job-listing'),
			]
		);

		$this->add_control(
			'title_page_link',
			[
				'label' => esc_html__('Select Link Page','ai-job-listing'),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'options' =>  $this-> axilweb_ai_job_listing_get_pages(),

			]
		);
		$this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function axilweb_ai_job_listing_get_jobs_from_api($settings) {
		$base_url = home_url() . '/wp-json/ai_job_listing/v1/jobs-frontend';
		$params = array(
		    'per_page' => $settings['job_limit'],
		    'orderby' => 'id',
		    'order' => $settings['post_ordering'],
		);
		$url = $base_url . '?' . http_build_query($params);

		$arguments = array(
			'method' => 'GET',
			'timeout' => 10,
		);

		$response = wp_remote_get($url, $arguments);

		if (is_wp_error($response)) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code($response);
		if ($response_code !== 200) {
			$response_body = wp_remote_retrieve_body($response);
			error_log('AI Job Listing Elementor Widget API Error: Code ' . $response_code . ', Body: ' . $response_body . ', URL: ' . $url);
			return new \WP_Error('api_error', 
				sprintf(__('Invalid API response (Code: %d). Please check if the plugin is properly activated and the database is set up correctly.', 'ai-job-listing'), $response_code), 
				['status' => $response_code]
			);
		}

		return $response;
	}
	
	/**
	 * Renders the job listings HTML on the front end.
	 *
	 * This method fetches the job listings from the API using the specified settings (e.g., job limit, ordering).
	 * It processes the results, extracting relevant data such as job title, description, departments, and application deadline.
	 * The job listings are displayed in a grid format with each job linking to a detailed job page. 
	 * After rendering the job listings, a "Load More" button is added to the page.
	 *
	 * @return void
	 */ 
	protected function render() {
		$allowed_tags = wp_kses_allowed_html( 'post' );
		$settings = $this->get_settings_for_display();
		$results = $this->axilweb_ai_job_listing_get_jobs_from_api($settings);
		
		if (is_wp_error($results)) {
			$error_message = $results->get_error_message();
			
			// Translators: %s is the error message returned from the API call
			$error_text = sprintf(__('Something went wrong: %s', 'ai-job-listing'), $error_message);
			
			printf(
				'<div class="aijob-error">%s</div>',
				esc_html($error_text)
			);
			return;
		}
		
		// Output container start with proper escaping
		echo wp_kses_post('<div class="ai-job-listing-list"><div class="job-box-lists">');
		
		$results = json_decode(wp_remote_retrieve_body($results), true);
		if (is_array($results) && !empty($results)) {
			foreach ($results as $result) {
				$attribute_values = json_decode($result['attribute_values'], true);
				$job_departments_values = [];
	
				foreach ($attribute_values as $attribute) {
					if ($attribute['form_key'] === 'job_departments') {
						$job_departments_values[] = sanitize_text_field($attribute['value']);
					}
				}
	
				// Build data with proper escaping
				$job_departments = esc_html(implode(', ', $job_departments_values));
				$job_title = esc_html($result['title']);
				$job_description = wp_kses($result['description'], $allowed_tags);
				$job_slug = esc_url(home_url('/career/' . $result['slug']));
				$application_deadline = esc_html($result['application_deadline']);
				
				// Create job box template with proper escaping using standard strings
				$job_box_template = '<div class="job-box aijob-elementor-wrp">
'
				    . '	<div class="job-box-inner">
'
				    . '		<h4 class="job-title">
'
				    . '			<a href="%1$s">%2$s</a>
'
				    . '		</h4>
'
				    . '		%3$s
'
				    . '		<div class="job-meta-list">
'
				    . '			<div class="job-meta">
'
				    . '				<i class="tio-briefcase_outlined"></i>
'
				    . '				<span>%4$s</span>
'
				    . '			</div>
'
				    . '			<div class="job-meta">
'
				    . '				<i class="tio-appointment"></i>
'
				    . '				<span>%5$s</span>
'
				    . '			</div>
'
				    . '			<div class="job-meta">
'
				    . '				<i class="tio-poi_outlined"></i>
'
				    . '				<span>%6$s</span>
'
				    . '			</div>
'
				    . '		</div>
'
				    . '	</div>
'
				    . '</div>';
				
				// Output job box with all data properly escaped
				echo wp_kses_post(sprintf(
					$job_box_template,
					$job_slug,
					$job_title,
					$job_description,
					$job_departments,
					$application_deadline,
					esc_html__('On-site', 'ai-job-listing')
				));
			}
		} else {
			echo wp_kses_post('<div class="aijob-no-jobs">' . esc_html__('No jobs found', 'ai-job-listing') . '</div>');
		}
		
		// Close container divs
		echo wp_kses_post('</div></div>');
		
		// Get link data with proper escaping
		$link_page_url = !empty($settings['title_page_link']) ? esc_url(get_permalink($settings['title_page_link'])) : '';
		$link_text = !empty($settings['title_link_text']) ? esc_html($settings['title_link_text']) : esc_html__('Load More', 'ai-job-listing');
		
		// Output link button with proper escaping
		if (!empty($link_page_url)) {
			printf(
				'<div class="aijob-elementor-btn text-center"><a class="axil-btn" href="%s">%s</a></div>',
				esc_url($link_page_url),
				esc_html($link_text)
			);
		}
	}

} 
