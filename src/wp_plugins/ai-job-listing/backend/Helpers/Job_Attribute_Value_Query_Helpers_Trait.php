<?php
namespace Axilweb\AiJobListing\Helpers; 
use Axilweb\AiJobListing\Models\Job_Attribute_Value;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Job_Attribute_Value_Query_Helpers_Trait
{

    /**
     * Adds job attribute values by their IDs to a specific job.
     *
     * This function takes an array of attribute value IDs and assigns them to a specific job by creating records 
     * in the `job_attribute_value` table. It returns an array of created job attribute value IDs.
     *
     * @param array $attribute_value_ids The array of attribute value IDs to associate with the job.
     * @param int $job_id The ID of the job to which the attribute values should be associated.
     *
     * @return array Returns an array of job attribute value IDs that were created.
     */ 
    public static function addJob_Attribute_ValueByIds($attribute_value_ids, $job_id)
    {
        // $attribute_value_ids = $request['attribute_value_ids']; 
        $job_attribute_value_ids = [];
        foreach ($attribute_value_ids as $attribute_value_id) {
            $job_attribute_value_data = [
                'job_id' => $job_id,
                'attribute_value_id' => $attribute_value_id,
            ];
            $job_attribute_value_ids[] = (new Job_Attribute_Value)->create(

                $job_attribute_value_data,
                [
                    '%d',
                    '%d',
                    '%d',
                ]
            );
        }
        return $job_attribute_value_ids;
    }
  
    /**
     * Removes job attribute values by job ID with cache invalidation.
     *
     * This function deletes all the entries in the `job_attribute_value` table for a given job ID
     * and handles cache invalidation to maintain data consistency.
     *
     * @param int $job_id The ID of the job whose attribute values should be removed.
     *
     * @return bool Returns `true` if the operation was successful, or `false` if an error occurred.
     */
    public static function removeJob_Attribute_ValueByJobId($job_id)
    {
        // Validate job_id
        $job_id = absint($job_id);
        if (!$job_id) {
            return false;
        }
        
        // Get attribute values for this job before deletion (for cache invalidation)
        $attribute_values = self::getAttributeAndAttribute_ValuesForSpecificJob($job_id);
        
        // Execute the database query
        $result = self::_query_remove_job_attribute_value_by_job_id($job_id);
        
        // If deletion was successful, invalidate related caches
        if ($result) {
            // Clear the cache for this job's attribute values
            $job_cache_key = 'axilweb_ajl_job_attr_values_' . $job_id;
            wp_cache_delete($job_cache_key, 'axilweb_ajl_job_attribute_values');
            
            // Clear the attribute value job counts cache
            wp_cache_delete('axilweb_ajl_attr_value_job_counts', 'axilweb_ajl_job_attribute_values');
            
            // If we have the attribute values that were deleted, clear their individual caches too
            if (!empty($attribute_values)) {
                foreach ($attribute_values as $form_key => $value_data) {
                    if (isset($value_data['id'])) {
                        $attr_value_cache_key = 'axilweb_ajl_attr_value_' . $value_data['id'];
                        wp_cache_delete($attr_value_cache_key, 'axilweb_ajl_attribute_values');
                    }
                }
            }
        }
        
        return $result;
    }
 
    /**
     * Remove job attribute values by job ID from the custom table.
     *
     * @param int $job_id The ID of the job to remove attribute values for
     * @return bool True on success, false on failure
     */
    private static function _query_remove_job_attribute_value_by_job_id($job_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Caching not applicable for delete operation
        return $wpdb->delete( $table_name, ['job_id' => $job_id], ['%d'] );
    }

    /**
     * Fetches the attributes and their corresponding values for a specific job with caching.
     *
     * This function retrieves the form key of each attribute and its associated value for the job specified by `$job_id`.
     * It returns an associative array where the keys are the attribute form keys, and the values are arrays containing 
     * the attribute value's ID and label. Results are cached for improved performance.
     *
     * @param int $job_id The ID of the job for which the attributes and values are to be fetched.
     *
     * @return array Returns an associative array where keys are the attribute form keys and values are arrays containing 'id' and 'label' of the attribute value.
     */
    public static function getAttributeAndAttribute_ValuesForSpecificJob($job_id)
    {
        // Validate job_id
        $job_id = absint($job_id);
        if (!$job_id) {
            return [];
        }
        
        // Create cache key
        $cache_key = 'axilweb_ajl_job_attr_values_' . $job_id;
        $cache_group = 'axilweb_ajl_job_attribute_values';
        
        // Try to get from cache first
        $output = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $output) {
            $results = self::_query_get_attribute_and_attribute_values_for_specific_job($job_id);
            
            // Format the results
            $output = [];
            foreach ($results as $each_result) {
                $output[$each_result['attribute_form_key']] = [
                    'id'    => $each_result['attribute_value_id'],
                    'label' => $each_result['attribute_value'],
                ];
            }
            
            // Cache the results
            if (!empty($output)) {
                wp_cache_set($cache_key, $output, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $output;
    }

    /**
     * Fetches the count of active jobs associated with each attribute value with caching.
     *
     * This function retrieves the count of active jobs for each attribute value across different job attributes.
     * The results are grouped by the attribute values and sorted by attribute name and value. Results are cached
     * for improved performance.
     *
     * @return array|WP_Error Returns an array of results containing the attribute slug, attribute value, job count, and job status if jobs are found. 
     *                       Returns a WP_Error if no active job data is found.
     */ 
    public static function getAttributeValueJobCounts() {
        // Create cache key and group
        $cache_key = 'axilweb_ajl_attr_value_job_counts';
        $cache_group = 'axilweb_ajl_job_attribute_values';
        
        // Try to get from cache first
        $results = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $results) {
            $results = self::_query_get_attribute_value_job_counts();
            
            // Cache the results if they're valid
            if (!is_wp_error($results) && !empty($results)) {
                wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $results;
    }

    /**
     * Get attributes and their values for a specific job.
     *
     * @param int $job_id The ID of the job to fetch attributes for
     * @return array Array of attribute data for the job
     */
    private static function _query_get_attribute_and_attribute_values_for_specific_job($job_id) {
        global $wpdb;
        
        // Table names
        $table_attributes = $wpdb->prefix . 'axilweb_ajl_attributes';
        $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $table_job_attribute_value = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';
        
        // Cache key for this specific job's attribute data
        $cache_key = 'axilweb_ajl_job_' . $job_id . '_attributes_values';
        $cache_group = 'axilweb_ajl';
        
        // Check if data is cached
        $result = wp_cache_get($cache_key, $cache_group);
        if (false !== $result) {
            return $result;
        }
        
        $job_attr_table = esc_sql($table_job_attribute_value);
        $attr_values_table = esc_sql($table_attribute_values);
        $attrs_table = esc_sql($table_attributes);
        
        // Query to get attributes and values for a specific job
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table join required for job attributes
        $query = "SELECT 
            attributes.form_key as attribute_form_key, 
            attribute_values.id as attribute_value_id, 
            attribute_values.value as attribute_value 
        FROM `$job_attr_table` as job_attribute_value 
        INNER JOIN `$attr_values_table` as attribute_values 
            ON job_attribute_value.attribute_value_id = attribute_values.id 
        INNER JOIN `$attrs_table` as attributes 
            ON attribute_values.attribute_id = attributes.id 
        WHERE job_attribute_value.job_id = %d";
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table names are sanitized with esc_sql(), caching handled by calling code if needed
        $result = $wpdb->get_results( $wpdb->prepare( $query, $job_id ), ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Query is prepared with $wpdb->prepare()
        
        // Cache the result for future requests
        if ($result) {
            wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
        }
    
        return $result;
    }

    private static function _query_get_attribute_value_job_counts() {
        global $wpdb;
        
        // Define table names using the correct WordPress conventions
        $table_attributes = $wpdb->prefix . 'axilweb_ajl_attributes';
        $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $table_job_attribute_values = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';
        $table_jobs = $wpdb->prefix . 'axilweb_ajl_jobs';  // Table for jobs
        
        // Create a cache key for job attribute counts
        $cache_key = 'axilweb_ajl_job_attr_counts';
        $cache_group = 'axilweb_ajl_job_attr_values';
        
        // Try to get from cache first
        $results = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $results) {
            // Escape table names separately using esc_sql
            $tables = array(
                'attributes' => esc_sql($table_attributes),
                'attribute_values' => esc_sql($table_attribute_values),
                'job_attribute_values' => esc_sql($table_job_attribute_values),
                'jobs' => esc_sql($table_jobs)
            );
            
            // FIX 3: Since there are no user inputs in this query, we can safely use a prepared statement
            // for the entire query with no placeholders
            $sql = "SELECT 
                attributes.slug AS attribute_slug,
                attribute_values.id AS attribute_value_id,
                attribute_values.value AS attribute_value,
                jobs.status AS status,
                jobs.id AS id,
                COUNT(DISTINCT job_attribute_values.job_id) AS job_count
            FROM `{$tables['attributes']}` AS attributes
            JOIN `{$tables['attribute_values']}` AS attribute_values 
                ON attributes.id = attribute_values.attribute_id
            JOIN `{$tables['job_attribute_values']}` AS job_attribute_values 
                ON attribute_values.id = job_attribute_values.attribute_value_id
            JOIN `{$tables['jobs']}` AS jobs 
                ON job_attribute_values.job_id = jobs.id AND jobs.status = 'active' 
            GROUP BY attribute_values.id, attributes.id
            ORDER BY attributes.name, attribute_values.value";
             // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary for custom table operations, table names sanitized with esc_sql(), caching handled by calling code if needed
            $results = $wpdb->get_results(
                $wpdb->prepare('%s', $sql),
                ARRAY_A
            );
            
            // Cache the result for future requests (1 hour cache)
            if ($results) {
                wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        // Return the results or handle the case when no results are found
        if (!$results) {
            return new WP_Error('axilweb_ajl_no_data', 'No active job attribute data found', array('status' => 404));
        }
        
        return $results;
    }
}
