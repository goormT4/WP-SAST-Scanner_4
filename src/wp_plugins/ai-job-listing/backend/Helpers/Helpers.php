<?php
namespace Axilweb\AiJobListing\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Helpers {
  use Rest_Helpers_Trait;
  use Utility_Helpers_Trait;
  use Mail_Helpers_Trait;
  use Job_Attribute_Value_Query_Helpers_Trait;
  use Attribute_Query_Helpers_Trait;
  use Attribute_Value_Query_Helpers_Trait;
  use App_Process_Query_Helpers_Trait;
  use App_Meta_Query_Helpers_Trait;
  use General_Settings_Query_Helpers_Trait;
  use Job_Application_Helpers_Trait;
  
 
  /**
   * Fetches job listings from the API.
   *
   * This function makes a GET request to the job listings API endpoint with the specified parameters.
   *
   * @since 1.0.0
   *
   * @param array $attributes Attributes used for building the API query.
   * @return WP_Error|array The response from the API or a WP_Error object on failure.
   */
  static function get_jobs_from_api($attributes) { 
      $padding = $attributes['padding'];
      $base_url = home_url() . '/wp-json/ai_job_listing/v1/jobs-frontend';
      $params = array(
          'per_page' => $attributes['jobslimit'],
          'orderby' => 'id',
        // 'order' => $attributes['post_ordering'],
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
          error_log('AI Job Listing Gutenberg Block API Error: Code ' . $response_code . ', Body: ' . $response_body . ', URL: ' . $url);
          return new \WP_Error('api_error', 
              sprintf(__('Invalid API response (Code: %d). Please check if the plugin is properly activated and the database is set up correctly.', 'ai-job-listing'), $response_code), 
              ['status' => $response_code]
          );
      }

      return $response;
  }

 
  /**
   * Renders job listings for Gutenberg block with job departments and application deadlines.
   *
   * This function:
   * - Retrieves the padding settings from the block attributes.
   * - Fetches job data from the external API using `Helpers::get_jobs_from_api()`.
   * - If successful, decodes the job data and extracts attributes like job departments and deadlines.
   * - Iterates through the results and formats the job data into an HTML block that includes job title, description, departments, and application deadline.
   * - Displays the results in a grid layout, and includes a "Load More" button for pagination.
   *
   * @param array $attributes Block attributes containing padding and other settings.
   * 
   * @return string The HTML output for the job listings or error message.
   */
  static function gutenberg($attributes) {
    $allowed_tags = wp_kses_allowed_html( 'post' );
    $padding = $attributes['padding'];
    $padding_string = $padding['top'] . ' ' . $padding['right'] . ' ' . $padding['bottom'] . ' ' . $padding['left'];
  
      $results = Helpers::get_jobs_from_api($attributes); 
      if (is_wp_error($results)) {
        $error_message = $results->get_error_message();
        // Translators: %s is the specific error message returned by the API
        return esc_html(sprintf(__('Something went wrong: %s', 'ai-job-listing'), $error_message));
      }
      // Start building HTML output
      $html_output = '<div class="ai-job-listing-list"><div class="job-box-lists">';

      $results = json_decode(wp_remote_retrieve_body($results)); // null
      if (! $results) {
        return esc_html__('No jobs found', 'ai-job-listing');
      }
      
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

          // Implode the job_departments values array into a comma-separated string
          $job_departments = implode(', ', $job_departments_values); 
          
          // Build HTML with proper escaping
          $job_title = esc_html($result->title);
          $job_slug = esc_attr($result->slug);
          $job_description = wp_kses_post($result->description);
          $job_departments = esc_html($job_departments);
          $application_deadline = esc_html($result->application_deadline);
          
          // Format HTML output
          $html_job = sprintf(
              '<div class="job-box aijob-elementor-wrp">
                <div class="job-box-inner">
                <h4 class="job-title">
                  <a href="/career/%s">%s</a>
                </h4>
                %s
                <div class="job-meta-list">
                  <div class="job-meta">
                  <i class="tio-briefcase_outlined"></i>
                  <span>%s</span>
                  </div>
                  <div class="job-meta">
                  <i class="tio-appointment"></i>
                  <span>%s</span>
                  </div>
                  <div class="job-meta">
                  <i class="tio-poi_outlined"></i>
                  <span>%s</span>
                  </div>
                </div>
                </div>
              </div>',
              $job_slug,
              $job_title,
              $job_description,
              $job_departments,
              $application_deadline,
              esc_html__('On-site', 'ai-job-listing')
          );
          
          $html_output .= wp_kses($html_job, $allowed_tags);
        }
        
      // Output container end
      $html_output .= '</div></div>';
      
      // Career URL should be properly retrieved
      $career_page_id = get_option('axilweb_ajl_career_page');
      $career_page_url = $career_page_id ? esc_url(get_permalink($career_page_id)) : '/career/';
      
      $html_output .= sprintf(
          '<div class="aijob-elementor-btn text-center"><a class="aijob-elementor-btn text-center" href="%s">%s</a></div>',
          esc_url($career_page_url),
          esc_html__('Load More', 'ai-job-listing')
      );
      
      return $html_output;

  }
}
