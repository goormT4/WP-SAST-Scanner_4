<?php 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Gutenberg_Data_Helpers_Trait {
 
	/**
	 * Retrieves a list of all pages in the site.
	 *
	 * This function fetches all pages as an associative array with page IDs as keys
	 * and page titles as values, ordered by date in descending order.
	 *
	 * @since 0.1.0
	 *
	 * @return array An associative array of pages with page IDs as keys and titles as values.
	 */
	static function ai_job_listing_get_pages() { 
		$page_list = get_posts(array(
			'post_type' 		=> 'page',
			'orderby' 			=> 'date',
			'order' 			=> 'DESC',
			'posts_per_page' 	=> -1,
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
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 0.1.0
	 * @access protected
	 */
	protected function get_jobs_from_api($settings) {
		$base_url = home_url() . '/wp-json/ai_job_listing/v1/jobs-frontend';
		$params = array(
		    'per_page' 		=> $settings['job_limit'],
		    'orderby' 		=> 'id',
		    'order' 		=> $settings['post_ordering'],
		);
		$url = $base_url . '?' . http_build_query($params);

		$arguments = array(
			'method'  => 'GET',
			'timeout' => 10,
		);

		$response = wp_remote_get($url, $arguments);

		if (is_wp_error($response)) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code($response);
		if ($response_code !== 200) {
			$response_body = wp_remote_retrieve_body($response);
			error_log('AI Job Listing Gutenberg Trait API Error: Code ' . $response_code . ', Body: ' . $response_body . ', URL: ' . $url);
			return new \WP_Error('api_error', 
				sprintf(__('Invalid API response (Code: %d). Please check if the plugin is properly activated and the database is set up correctly.', 'ai-job-listing'), $response_code), 
				['status' => $response_code]
			);
		}

		return $response;
	}
	
	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$results = $this->get_jobs_from_api($settings); 

		 // Check for API errors.
		if (is_wp_error($results)) {
			$error_message = $results->get_error_message();
			echo esc_html("Something went wrong: " . $error_message);
			return;
		}

  		// Decode the API response.
		$results = json_decode(wp_remote_retrieve_body($results)); 
		foreach ($results as $result) { 
		    // Decode the attribute_values JSON string
		    $attribute_values = json_decode($result->attribute_values, true);

		    // Initialize an empty array to store job_departments values
		    $job_departments_values = [];

		    // Iterate through attribute_values array to find job_departments value
		    foreach ($attribute_values as $attribute) {
		        if ($attribute['form_key'] === 'job_departments') {
		            $job_departments_values[] = $attribute['value'];
		        }
		    }
			$job_departments 		= implode(', ', $job_departments_values);
			$job_title 				= esc_html($result['title']);
			$job_description 		= esc_html($result['description']);
			$job_slug 				= esc_url(home_url('/career/' . $result['slug']));
			$application_deadline 	= esc_html($result['application_deadline']);
			// Render the job listing.
			echo '<div class="grid gap-5 mb-6">
				<a class="block" href="' . esc_url($job_slug) . '">
					<div class="p-8 border border-shademid rounded">
						<h4 class="mb-2">' . esc_html($result['id']) . ' - ' . esc_html($job_title) . '</h4>
						<p class="text-[16px] text-headline mb-4">' . esc_html($job_description) . '</p>
						<div class="flex items-center gap-6">
							<div class="flex items-center gap-2 text-[14px] text-grey">
								<svg>...</svg>
								<span>' . esc_html($job_departments) . '</span>
							</div>
							<div class="flex items-center gap-2 text-[14px] text-grey">
								<svg>...</svg>
								<span>' . esc_html($application_deadline) . '</span>
							</div>
						</div>
					</div>
				</a>
			</div>';
		}
		 // Render the link to the additional jobs page.
		$link_page_url = esc_url(get_permalink($settings['title_page_link']));
		$link_text = esc_html($settings['title_link_text']);
	
		echo '<a class="inline-flex items-center gap-1 transition decoration-transparent px-4 py-2 rounded-md font-medium text-sm text-white bg-secondary hover:bg-opacity-80 hover:text-white focus:outline-none focus:shadow-none" href="' . esc_url($link_page_url) . '">' . esc_html($link_text) . '</a>';
	}

} 
