<?php
namespace Axilweb\AiJobListing\Helpers; 
use Axilweb\AiJobListing\Data\Capabilities_Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Utility Helpers Trait.
 *
 * @since 0.1.0     
 * @package Ai Job Listing  
 * @subpackage Helpers
 * @category Helpers
 * @author Axilweb  
 */
trait Utility_Helpers_Trait
{
    public static function sanitize($value, string $type): string
    {
        $sanitized = '';

        switch ($type) {
            case 'text':
            case 'textarea':
                $sanitized = sanitize_text_field(wp_unslash($value));
                break;

            case 'number':
                $sanitized = absint(wp_unslash($value));
                break;

            case 'email':
                $sanitized = sanitize_email(wp_unslash($value));
                break;

            case 'switch':
            case 'boolean':
            case 'bool':
                $sanitized = (bool) wp_unslash($value);
                break;

            case 'block':
                // Sanitize gutenberg block data.
                // We're not sanitizing it, now, when we're showing it,
                // We'll use gutenberg's own way for rendering blocks.
                $sanitized = $value;
                break;

            default:
                $sanitized = $value;
                break;
        }

        return $sanitized;
    }
    /**
     * Generate a slug from a string.
     *
     * @since 1.0.0
     *
     * @param string $string The string to generate a slug from.
     * @return string The generated slug.
     */
    public static function generateSlug($string): string
    {
        // Convert the string to lowercase
        $slug = strtolower($string);

        // Replace spaces with dashes
        $slug = str_replace(' ', '-', $slug);

        // Remove special characters
        $slug = preg_replace('/[^a-zA-Z0-9\-]/', '', $slug);

        // Remove consecutive dashes
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim dashes from the beginning and end
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Prepares links for the request.
     *
     * @since 1.0.0
     *
     * @param WP_Post $post post object
     *
     * @return array links for the given data.
     */
    public static function prepare_links($item, $namespace, $rest_base, $base): array
    {
        $base = sprintf('%s/%s%s', $namespace, $rest_base, $base);

        // Add a null check and fallback for the id
        $id = null;
        if (is_object($item) && isset($item->id)) {
            $id = $item->id;
        } elseif (is_array($item) && isset($item['id'])) {
            $id = $item['id'];
        }

        // Only create links if we have a valid id
        $links = [];
        if ($id !== null) {
            $links = [
                'self' => [
                    'href' => rest_url(trailingslashit($base) . $id),
                ],
                'collection' => [
                    'href' => rest_url($base),
                ],
            ];
        }

        return $links;
    }


    /**
     *  Get Placeholders.
     *
     * @since 1.0.0 
     * 
     * @param int $count
     * @param string $placeholder
     * 
     * @return string   
     */
    public static function getPlaceHolders($count, string $placeholder = '%d')
    {
        $count = is_array($count) ? count($count) : $count;
        return implode(', ', array_fill(0, $count, $placeholder));
    }
    public static function isUserAdministrator()
    {
        $user = get_userdata(get_current_user_id());
        return in_array( 'administrator', $user->roles );
    }
    public static function current_user_can($capability)
    {
        return self::isUserAdministrator() || current_user_can($capability);
    }
 
   
    /**
     * Get job post limits.
     *
     * Prepares links for the request.
     *
     * @since 1.0.0
     *
     * @return mixed[] Links for the given data.
     */
    public static function getJobPostLimits()
    {
        $job_post = get_option('axilweb_ajl_job_post');
        // $website_usage = get_option('aijob_listing_website-usage');

        if (!$job_post) {
            return AXILWEB_AJL_JOB_LIMIT;
        }

      return $job_post;
    }
   
    /**
     * Get job post limits.
     *
     * Prepares links for the request.
     *
     * @since 1.0.0
     *
     * @return mixed[] Links for the given data.
     */
    public static function getNumberOfJobPost()
        {
            // Set up cache key and group
            $cache_key = 'axilweb_ajl_job_count_total';
            $cache_group = 'axilweb_ajl_job_counts';
            
            // Try to get from cache first
            $row_count = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, fetch from database
            if (false === $row_count) {
                // Query the database
                $row_count = self::_query_get_number_of_job_post();
                
                // Cache the results (even if 0)
                wp_cache_set($cache_key, $row_count, $cache_group, HOUR_IN_SECONDS);
            }
            
            return $row_count ? $row_count : 0;
        }

    /**
     * Check if the user has reached the job posting limit.
     *
     * Determines whether the user has reached the job posting limit based on their current job post count
     * and the job posting limit set in the system.
     *
     * @since 1.0.0
     *
     * @return bool True if the user has reached the job posting limit, false otherwise.
     */
    public static function hasJobLimit()
    {
        return Helpers::getJobPostLimits() > Helpers::getNumberOfJobPost();
    }
    
    /**
     * Find an item in an array by its name.
     *
     * Searches through an array of items and returns the first item that matches the specified name.
     * Each item in the array should be an associative array with a 'name' key.
     * If no matching item is found, it returns null.
     *
     * @since 1.0.0
     *
     * @param array $items The array of items to search, each item being an associative array with at least a 'name' key.
     * @param string $name The name to search for within the items array.
     * @return array|null The first matching item as an associative array, or null if no match is found.
     */
    static function findItemByName(array $items, string $name): ?array
    {
        $filteredItems = array_filter($items, function($item) use ($name) {
            return isset($item['name']) && $item['name'] === $name;
        });

        // Return the first matching item, or null if no match
        return !empty($filteredItems) ? array_shift($filteredItems) : null;
    }


    /**
     * Get attribute values by slug.
     *
     * @param string $attribute_slug The slug of the attribute to retrieve values for.
     * @return array Array of attribute values with label and value pairs.
     */
    public static function getAttributeValuesBySlug($attribute_slug) {
        global $wpdb;
        
        try {
            // Sanitize the input
            $attribute_slug = sanitize_text_field($attribute_slug);
            
            // Define table names
            $attribute_values_table = esc_sql($wpdb->prefix . 'axilweb_ajl_attribute_values');
            $attributes_table = esc_sql($wpdb->prefix . 'axilweb_ajl_attributes');
            
            // Build and execute the query
            // Set up cache key and group
            $cache_key = 'axilweb_ajl_attr_values_' . sanitize_key($attribute_slug);
            $cache_group = 'axilweb_ajl_attribute_values';
            
            // Try to get from cache first
            $results = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, fetch from database
            if (false === $results) {
                // Query the database
                $results = self::_query_get_attribute_values_by_slug($attribute_slug);
                
                // Cache the results (even if empty)
                wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
            }
            
            if (empty($results)) {
                return [];
            }
            
            return array_map(
                static function($item) {
                    return [
                        'label' => $item['value'],
                        'value' => $item['value'],
                    ];
                },
                $results
            );
            
        } catch (Exception $e) {
            // Using WordPress's proper logging mechanism instead of error_log
            if (defined('WP_DEBUG') && WP_DEBUG) {
                // Only log when debug is enabled
                $message = sprintf('Error in getAttributeValuesBySlug: %s', $e->getMessage());
                ajl_log($message, 'error', [
                    'method' => 'getAttributeValuesBySlug',
                    'attribute_slug' => $attribute_slug
                ]);
            }
            return [];
        }
    }
    
    /**
     * Retrieve the slug of the Career page.
     *
     * Fetches the slug for the Career page based on the page ID stored in the system options.
     * If the Career page is set and exists, it returns the page's slug; otherwise, returns null.
     *
     * @since 1.0.0
     *
     * @return string|null The slug of the Career page, or null if not set or the page does not exist.
     */
    public static function getCareerPageSlug()
    {
   
        $career_page_id = get_option('axilweb_ajl_career_page');
        $career_page = get_post($career_page_id);
        return $career_page ? $career_page->post_name : null;
    }
    /**
     * Retrieve the ID of the Career page.
     * Fetches the ID for the Career page based on the page ID stored in the system options.
     * If the Career page is set and exists, it returns the page's ID; otherwise, returns null.
     * @since 1.0.0
     * @return string|null The ID of the Career page, or null if not set or the page does not exist.
     */
    public static function getCareerPageId()
    {
   
        $career_page_id = get_option('axilweb_ajl_career_page'); 
        return $career_page_id ? $career_page_id : null;
    }


    /**
     * Check if the user is logged in using WordPress function.
     *
     * @since 1.0.0
     *
     * @return bool True if the user is logged in using WordPress function, false otherwise.
     */
    public static function isWpLogin()
    {
        // Check if the user is logged in using WordPress function
        if (is_user_logged_in()) {
            return true; // User is logged in
        } else {
            return false; // User is not logged in
        }
    }
    
    /**
     * Internal method to get the number of job posts.
     * Implementation method for getNumberOfJobPost().
     *
     * This is a specific implementation for querying the custom jobs table,
     * which requires a direct database query.
     *
     * @since 1.0.0
     * @return int The count of job posts
     */
    private static function _query_get_number_of_job_post() {
        global $wpdb;
        $table_jobs = $wpdb->prefix . 'axilweb_ajl_jobs';
        
        // Create a cache key for job count
        $cache_key = 'axilweb_ajl_job_count';
        $cache_group = 'axilweb_ajl_jobs_stats';
        
        // Try to get from cache first
        $count = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $count) {
            // Use %i placeholder for proper table name escaping
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for counting jobs. Proper caching is implemented with wp_cache_get/set above.
            $count = (int) $wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM %i", $table_jobs)
            );
            
            // Cache the result for future requests (30 minutes)
            wp_cache_set($cache_key, $count, $cache_group, 30 * MINUTE_IN_SECONDS);
        }
        
        return $count;
    }
    
    /**
     * Internal method to get attribute values by attribute slug.
     * Implementation method for getAttributeValuesBySlug().
     *
     * This is a specific implementation for querying across multiple custom attribute tables,
     * which requires a direct database query.
     *
     * @since 1.0.0
     * @param string $attribute_slug The attribute slug to look up values for
     * @return array The attribute values matching the slug
     */
    private static function _query_get_attribute_values_by_slug($attribute_slug) {
        global $wpdb;
        
        // Table names
        $attribute_values_table = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $attributes_table = $wpdb->prefix . 'axilweb_ajl_attributes';
        
        // Create a cache key for attribute values by slug
        $cache_key = 'axilweb_ajl_attr_values_' . sanitize_key($attribute_slug);
        $cache_group = 'axilweb_ajl_attributes';
        
        // Try to get from cache first
        $results = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $results) {
            // Use %i placeholders for table names and %s for string values
             // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is necessary for custom email template table. Proper caching is implemented with wp_cache_get/set above.
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT 
                        av.id,
                        av.slug,
                        av.value
                    FROM %i av
                    JOIN %i a ON av.attribute_id = a.id
                    WHERE a.slug = %s
                        AND av.is_active = %d
                        AND av.deleted_at IS NULL",
                    $attribute_values_table,
                    $attributes_table,
                    $attribute_slug,
                    1
                ),
                ARRAY_A
            );
            
            // Cache the results for future requests (1 hour)
            wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $results;
    }
}
