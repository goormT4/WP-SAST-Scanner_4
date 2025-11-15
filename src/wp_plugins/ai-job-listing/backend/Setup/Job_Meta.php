<?php
namespace Axilweb\AiJobListing\Setup;
use Axilweb\AiJobListing\Helpers\Helpers;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Job_Meta class.
 *
 * @since 0.1.0
 */
class Job_Meta
{
    // Define a constant for the prefix.
    const PREFIX = 'axilweb_ajl_';

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_filter('pre_get_document_title', [$this, 'axilweb_ajl_add_career_page_meta_title']);
        add_action('wp_head', [$this, 'axilweb_ajl_add_career_page_meta_tags'], 5); // Higher priority to run before preloaded data
    }
 
    /**
     * Adds custom meta tags to the job listing page based on job details.
     * 
     * This method extracts job information from the URL, fetches job details from the database
     * with caching mechanisms, and outputs appropriate meta tags for SEO optimization.
     *
     * @since 1.0.0
     * @return void
     */
    public function axilweb_ajl_add_career_page_meta_tags() {
        $current_url_path = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $career_page_slug = (string) Helpers::getCareerPageSlug();

        if (empty($career_page_slug)) {
            return;
        }

        if (strpos($current_url_path, $career_page_slug) !== false) {
            $after_career_page = substr($current_url_path, strpos($current_url_path, $career_page_slug) + strlen($career_page_slug));
            $parts = explode('/', trim($after_career_page, '/'));
            $last_part = end($parts);

            // If no slug, it's the main career page
            if (empty($last_part)) {
                return $this->outputMetaTagsForCareerPage();
            }

            // Generate a unique cache key for the job data
            $slug = sanitize_text_field($last_part);
            $cache_key = 'axilweb_ajl_job_meta_' . md5($slug);
            
            // Try to get from cache first
            $job = wp_cache_get($cache_key, 'axilweb_ajl_job_data');
            
            // If not in cache, fetch from database and cache the result
            if (false === $job) {
                global $wpdb;
                
                // This direct database query is necessary for fetching job data from our custom table
                // Caching is implemented below to optimize performance
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $job = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}axilweb_ajl_jobs WHERE slug = %s AND status = %s LIMIT 1",
                        $slug,
                        'active'
                    ),
                    ARRAY_A
                );
                
                // Cache the result for future use (1 hour)
                if ($job) {
                    wp_cache_set($cache_key, $job, 'axilweb_ajl_job_data', HOUR_IN_SECONDS);
                }
            }

            if ($job) {
                // Get job attributes
                $job_with_attributes = axilweb_ajl_jobs()->jobs_manager->get_job_attributes($job['id']);
                $job = array_merge($job, $job_with_attributes);
                
                $this->outputMetaTagsForSingleJob($job);
            } else {
                $this->outputMetaTagsForCareerPage();
            }
        }
    }

    /**
     * Outputs the meta tags for the career page.
     *
     * @return void
     */    
    private function outputMetaTagsForCareerPage()
    {
        // Get career page data
        $career_page_id = get_option('axilweb_ajl_career_page');
        $career_page = get_post($career_page_id);
        
        $seo_title = $career_page ? get_the_title($career_page) : __('Current Job Openings', 'ai-job-listing');
        $seo_description = $career_page ? get_the_excerpt($career_page) : __('Browse our current job openings and career opportunities.', 'ai-job-listing');

        // Define meta tags
        $meta_tags = [
            ['name' => 'description', 'content' => $seo_description],
            ['name' => 'keywords', 'content' => 'jobs, careers, employment, job listings, career opportunities'],
            ['property' => 'og:title', 'content' => $seo_title],
            ['property' => 'og:description', 'content' => $seo_description],
            ['property' => 'og:type', 'content' => 'website'],
            ['name' => 'twitter:card', 'content' => 'summary_large_image'],
            ['property' => 'twitter:description', 'content' => $seo_description],
            ['property' => 'twitter:title', 'content' => $seo_title]
        ];
        
        // Output meta tags using helper method
        $this->output_meta_tags($meta_tags);
    }

    /**
     * Outputs the meta tags for the job listing page.
     *
     * @param array $job Job details array.
     */
    private function outputMetaTagsForSingleJob($job) {
        // Generate title and description if not set
        $job_title = isset($job['title']) ? sanitize_text_field($job['title']) : '';
        $department = isset($job['attribute_values']['job_departments']) ? sanitize_text_field($job['attribute_values']['job_departments']) : '';
        $location = isset($job['attribute_values']['locations']) ? sanitize_text_field($job['attribute_values']['locations']) : '';
        
        $seo_title = isset($job['seo_title']) && !empty($job['seo_title']) 
            ? sanitize_text_field($job['seo_title'])
            : sprintf('%s - %s Position at %s', $job_title, $department, get_bloginfo('name'));
            
        $seo_description = isset($job['seo_description']) && !empty($job['seo_description'])
            ? sanitize_text_field($job['seo_description'])
            : sprintf('Apply for the position of %s in %s. Job location: %s', $job_title, $department, $location);

        $feature_image = isset($job['feature_image']) ? esc_url($job['feature_image']) : '';
        $slug = isset($job['slug']) ? sanitize_title($job['slug']) : '';
        
        // Generate permalink
        $career_page_id = get_option('axilweb_ajl_career_page');
        $career_page_url = $career_page_id ? get_permalink($career_page_id) : '';
        $job_url = $career_page_url && $slug ? $career_page_url . $slug : '';

        // Define meta tags
        $meta_tags = [
            ['name' => 'description', 'content' => $seo_description],
            ['name' => 'keywords', 'content' => "$job_title, $department, jobs, careers, employment"],
            ['property' => 'og:title', 'content' => $seo_title],
            ['property' => 'og:description', 'content' => $seo_description],
            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:url', 'content' => $job_url]
        ];
        
        // Add image meta tags if feature image exists
        if ($feature_image) {
            $meta_tags[] = ['property' => 'og:image', 'content' => $feature_image];
        }
        
        // Add Twitter card meta tags
        $meta_tags[] = ['name' => 'twitter:card', 'content' => 'summary_large_image'];
        $meta_tags[] = ['property' => 'twitter:description', 'content' => $seo_description];
        $meta_tags[] = ['property' => 'twitter:title', 'content' => $seo_title];
        
        if ($feature_image) {
            $meta_tags[] = ['property' => 'twitter:image', 'content' => $feature_image];
        }
        
        // Output meta tags using helper method
        $this->output_meta_tags($meta_tags);
    }
    
    /**
     * Helper method to safely output meta tags with proper escaping.
     *
     * @param array $meta_tags Array of meta tag configurations.
     */
    private function output_meta_tags($meta_tags) {
        foreach ($meta_tags as $tag) {
            $attr_type = isset($tag['property']) ? 'property' : 'name';
            $attr_value = isset($tag['property']) ? $tag['property'] : $tag['name'];
            
            printf(
                '<meta %s="%s" content="%s">',
                esc_attr($attr_type),
                esc_attr($attr_value),
                esc_attr($tag['content'])
            );
        }
    }

    /**
     * Adds custom meta title to the job listing page.
     * 
     * This method determines whether the current page is the career listing page or a specific job page,
     * and returns an appropriate title. For individual job pages, it fetches job details from the database
     * with proper caching to improve performance.
     *
     * @since 1.0.0
     * @return string|null Custom meta title or null if not found.
     */
    public function axilweb_ajl_add_career_page_meta_title() {
        $current_url_path = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $career_page_slug = (string) Helpers::getCareerPageSlug();
        
        if (strpos($current_url_path, $career_page_slug) !== false) {
            $after_career_page = substr($current_url_path, strpos($current_url_path, $career_page_slug) + strlen($career_page_slug));
            $parts = explode('/', trim($after_career_page, '/'));
            $last_part = end($parts);
            
            // Main jobs listing page
            if (empty($last_part)) {
                $career_page_id = get_option('axilweb_ajl_career_page');
                if ($career_page_id) {
                    return esc_html(get_the_title($career_page_id));
                }
                return esc_html__('Current Job Openings', 'ai-job-listing');
            }
            
            // Individual job page - get title from cached data if possible
            $slug = sanitize_text_field($last_part);
            $cache_key = 'axilweb_ajl_job_title_' . md5($slug);
            
            // Try to get from cache first
            $job = wp_cache_get($cache_key, 'axilweb_ajl_job_titles');
            
            // If not in cache, fetch from database and cache the result
            if (false === $job) {
                global $wpdb;
                
                // This direct database query is necessary for fetching job title data from our custom table
                // Caching is implemented below to optimize performance
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $job = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT title, seo_title FROM {$wpdb->prefix}axilweb_ajl_jobs WHERE slug = %s AND status = %s LIMIT 1",
                        $slug,
                        'active'
                    ),
                    ARRAY_A
                );
                
                // Cache the result for future use (1 hour)
                if ($job) {
                    wp_cache_set($cache_key, $job, 'axilweb_ajl_job_titles', HOUR_IN_SECONDS);
                }
            }

            if ($job) {
                remove_action('wp_head', 'rel_canonical');
                if (!empty($job['seo_title'])) {
                    return esc_html($job['seo_title']);
                }
                return esc_html($job['title']);
            }
        }
        
        return null;
    }

    /**
     * Fetches job details from an API based on a job slug.
     *
     * @param string $slug The slug of the job.
     * @return array|null Job details or null if not found or error occurs.
     */
    private function fetchJobDetails($slug) {
        $response = wp_remote_get(
            home_url('/wp-json/ai_job_listing/v1/jobs-frontend?slug=' . $slug),
            ['timeout' => 10]
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null; // Handle error gracefully
        }
        $jobs =  json_decode(wp_remote_retrieve_body($response), true);
        return $jobs[0];
    }
}
