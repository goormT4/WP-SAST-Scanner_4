<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Axilweb_Ajl_jobs_ShortCode {

    /**
     * Constructor to initialize hooks and actions.
     */
    public function __construct() {
        add_action('elementor/widgets/register', [$this, 'axilweb_ajl_register_widget']);
        add_shortcode('ai-job-listing', [$this, 'axilweb_ajl_shortcode_callback']);
    }

    /**
     * Registers the custom Elementor widget for job listings.
     *
     * @param \Elementor\Widgets_Manager $widgets_manager The widget manager instance for Elementor.
     * 
     * @return void
     */
    public function axilweb_ajl_register_widget($widgets_manager) {
        require_once(__DIR__ . '/widgets/job-listing.php');
        $widgets_manager->register(new \Axilweb_Ajl_Elementor_Ai_Job_Listing_Widget());
    }

    /**
     * Fetches job listings from an external API based on widget settings.
     *
     * @param array $settings Settings for the API request, including job limit and ordering.
     * 
     * @return array|WP_Error API response or error.
     */
    private function axilweb_ajl_get_jobs_from_api($settings) {
      $base_url = home_url() . '/wp-json/ai_job_listing/v1/jobs-frontend';
      
        $params = array(
            'per_page' => $settings['job_limit'],
            'orderby' => 'id',
            'order' => $settings['ordering'],
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
            error_log('AI Job Listing API Error: Code ' . $response_code . ', Body: ' . $response_body . ', URL: ' . $url);
            return new \WP_Error('api_error', 
                sprintf(__('Invalid API response (Code: %d). Please check if the plugin is properly activated and the database is set up correctly.', 'ai-job-listing'), $response_code), 
                ['status' => $response_code]
            );
        }

        return $response;
    }

    /**
     * Renders the HTML for a list of job listings.
     *
     * @param object $results The job listings fetched from the API.
     * 
     * @return string The HTML output for the job listings.
     */ 
    private function axilweb_ajl_render_jobs_html($results) {
        $html = '<div class="ai-job-listing-list"><div class="job-box-lists">';
        foreach ($results as $result) {
            $attribute_values = json_decode($result->attribute_values, true);
            $job_departments_values = [];
            foreach ($attribute_values as $attribute) {
                if ($attribute['form_key'] === 'job_departments') {
                    $job_departments_values[] = $attribute['value'];
                }
            }
            $job_departments = implode(', ', $job_departments_values); 
            $html .= '<div class="job-box">
                        <div class="job-box-inner">
                            <h4 class="job-title">
                                <a href="/career/' . $result->slug . '/">' . $result->title . '</a>
                            </h4>
                            <p class="job-excerpt">' . $result->description . '</p>
                            <div class="job-meta-list">
                                <div class="job-meta">
                                    <i class="tio-briefcase_outlined"></i>
                                    <span>' . $job_departments . '</span>
                                </div>
                                <div class="job-meta">
                                    <i class="tio-appointment"></i>
                                    <span>' . $result->application_deadline . '</span>
                                </div>
                                <div class="job-meta">
                                    <i class="tio-poi_outlined"></i>
                                    <span>On-site</span>
                                </div>
                            </div>
                        </div>
                    </div>';
        }
        $html .= '</div></div>';
        return $html;
    }

    /**
     * Shortcode callback to display job listings.
     *
     * @param array $atts Shortcode attributes, including `job_limit` and `ordering`.
     * 
     * @return string The HTML output of the job listings, or an error message if the API request fails.
     */
    public function axilweb_ajl_shortcode_callback($atts) {
        $defaults = array(
            'job_limit' => 2,
            'ordering' => 'ASC',
        );
        $atts = shortcode_atts($defaults, $atts, 'ajl-job-listing');
        $response = $this->axilweb_ajl_get_jobs_from_api($atts);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
 
            // Translators: %s is the specific error message returned by the API
            return esc_html(sprintf(__('Something went wrong: %s', 'ai-job-listing'), $error_message));
        }

        $results = json_decode(wp_remote_retrieve_body($response));
        return $this->axilweb_ajl_render_jobs_html($results);
    }

}

/**      
 * Initialize the class.
 */
new Axilweb_Ajl_jobs_ShortCode();
