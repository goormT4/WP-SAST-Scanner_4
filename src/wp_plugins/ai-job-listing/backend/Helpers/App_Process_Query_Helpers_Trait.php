<?php
namespace Axilweb\AiJobListing\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait App_Process_Query_Helpers_Trait
{
    
    /**
     * Retrieves the process ID for a given order with caching.
     *
     * This function fetches the ID of the first process in the app process table
     * matching the specified order. If no process is found, it defaults to `1`.
     * Results are cached for improved performance.
     *
     * @since 1.0.0
     *
     * @return int The process ID for the specified order, or `1` as the default.
     */
    public static function getProcessByOrder()
    {
        // Use a fixed default order
        $order = 1;
        
        // Set up cache key and group
        $cache_key = 'axilweb_ajl_process_order_' . $order;
        $cache_group = 'axilweb_ajl_app_processes';
        
        // Try to get from cache first
        $process_id = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $process_id) {
            // Query the database
            $results = self::_query_get_process_by_order($order);
            
            // Process results
            $process_id = (isset($results[0]['id']) && !empty($results[0]['id'])) ? $results[0]['id'] : 1;
            
            // Cache the result
            wp_cache_set($cache_key, $process_id, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $process_id;
    }

    /**
     * Retrieves the next and previous steps for a given process ID with caching.
     *
     * This function fetches details of the next and previous steps for a given `process_id`
     * from the job listing application process table. It ensures proper handling of default
     * and reject process IDs when no next or previous steps are found. Results are cached
     * for improved performance.
     *
     * @since 1.0.0
     *
     * @param int|string $process_id The ID of the current process.
     * @return array|null An associative array containing the next and previous steps, or null if the `process_id` is invalid.
     */
    public static function getNextPreviousStep($process_id)
    {
        if ($process_id == "*") {
            return null;
        }
        
        // Validate process_id
        $process_id = absint($process_id);
        if (!$process_id) {
            return null;
        }
        
        // Set up cache key and group
        $cache_key = 'axilweb_ajl_process_steps_' . $process_id;
        $cache_group = 'axilweb_ajl_app_processes';
        
        // Try to get from cache first
        $next_previous_step = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database and construct the result
        if (false === $next_previous_step) {
            $next_previous_step = array();
            
            // Get previous step
            $get_prev_order_sql = self::_query_get_previous_step($process_id);
            
            // Get next step
            $get_next_order_sql = self::_query_get_next_step($process_id);
            
            // Process previous step results
            if (isset($get_prev_order_sql[0]) && !empty($get_prev_order_sql[0])) {
                $next_previous_step['previous_step'] = $get_prev_order_sql[0];
                $next_previous_step['previous_step']['reject_status_id'] = AXILWEB_AJL_REJECT_PROCESS_ID;
            } else {
                $next_previous_step['previous_step']['id'] = AXILWEB_AJL_DEFAULT_PROCESS_ID;
                $next_previous_step['previous_step']['key'] = AXILWEB_AJL_DEFAULT_PROCESS_SLUG;
                $next_previous_step['previous_step']['name'] = AXILWEB_AJL_DEFAULT_PROCESS_NAME;
                $next_previous_step['previous_step']['reject_status_id'] = AXILWEB_AJL_REJECT_PROCESS_ID;
            }
            
            // Process next step results
            if ($process_id < AXILWEB_AJL_REJECT_PROCESS_ID) {
                if (isset($get_next_order_sql[0]) && !empty($get_next_order_sql[0])) {
                    $next_previous_step['next_step'] = $get_next_order_sql[0];
                    $next_previous_step['next_step']['reject_status_id'] = AXILWEB_AJL_REJECT_PROCESS_ID;
                }
            } else {
                $next_previous_step['next_step']['id'] = AXILWEB_AJL_REJECT_PROCESS_ID;
                $next_previous_step['next_step']['key'] = AXILWEB_AJL_REJECT_PROCESS_SLUG;
                $next_previous_step['next_step']['name'] = AXILWEB_AJL_REJECT_PROCESS_NAME;
                $next_previous_step['next_step']['reject_status_id'] = AXILWEB_AJL_REJECT_PROCESS_ID;
            }
            
            // Cache the results
            wp_cache_set($cache_key, $next_previous_step, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $next_previous_step;
    }
    
    /**
     * Internal method to query a process by its order.
     * Implementation method for getProcessByOrder().
     *
     * This is a specific implementation for querying the custom app process table,
     * which requires a direct database query.
     *
     * @since 1.0.0
     * @param int $order The order of the process to retrieve
     * @return array Database result objects
     */
    private static function _query_get_process_by_order($order) {
        global $wpdb;
        // Table name
        $table_app_process = $wpdb->prefix . 'axilweb_ajl_app_process';
        
        // Create cache key for this specific query
        $cache_key = 'axilweb_ajl_process_order_' . absint($order);
        $cache_group = 'axilweb_ajl_app_process';
        
        // Try to get from cache first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $result) {
            // Use %i placeholder for proper table name escaping
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for custom table operations. Caching is implemented with the check above.
            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT `id` FROM %i WHERE `order` = %d", 
                    $table_app_process,
                    $order
                ), 
                ARRAY_A
            );
            
            // Cache the result for future requests
            if ($result) {
                wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $result;
    }

    /**
     * Get the previous step in the application process based on process ID.
     *
     * @param int $process_id The ID of the current process
     * @return array Array of previous step data (id, key, name)
     */
    private static function _query_get_previous_step($process_id) {
        global $wpdb;
        
        // Table name
        $table_app_process = $wpdb->prefix . 'axilweb_ajl_app_process';
        
        // Create cache key for this specific query
        $cache_key = 'axilweb_ajl_process_prev_' . sanitize_key($process_id);
        $cache_group = 'axilweb_ajl_app_process';
        
        // Try to get from cache first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $result) {
            // Properly escape table name
            $table_name = esc_sql($table_app_process);
            
            // First, execute the subquery to get the order value
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for custom table operations to retrieve process ordering information
            $order_value = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `order` FROM %s WHERE id = %d",
                    $table_name,
                    $process_id
                )
            );
            
            // If we have a valid order value, use it in the main query
            if ($order_value !== null) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for custom table operations to retrieve previous process in sequence
                $result = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT `id`, `key`, `name` FROM %s 
                        WHERE `id` < %d 
                        ORDER BY `order` DESC LIMIT 1",
                        $table_name,
                        $process_id
                    ),
                    ARRAY_A
                );
            } else {
                $result = array();
            }
            
            // Cache the result for future requests
            wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $result;
    }
    
    /**
     * Get the next step in the application process based on process ID.
     *
     * @param int $process_id The ID of the current process
     * @return array Array of next step data (id, key, name)
     */
    private static function _query_get_next_step($process_id) {
        global $wpdb;
        
        // Table name
        $table_app_process = $wpdb->prefix . 'axilweb_ajl_app_process';
        
        // Create cache key for this specific query
        $cache_key = 'axilweb_ajl_process_next_' . sanitize_key($process_id);
        $cache_group = 'axilweb_ajl_app_process';
        
        // Try to get from cache first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $result) {
            // Properly escape table name
            $table_name = esc_sql($table_app_process);
            
            // First, execute the subquery to get the order value
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for custom table operations to retrieve process ordering information
            $order_value = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `order` FROM %s WHERE id = %d",
                    $table_name,
                    $process_id
                )
            );
            
            // If we have a valid order value, use it in the main query
            if ($order_value !== null) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for custom table operations to retrieve next process in sequence
                $result = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT `id`, `key`, `name` FROM %s 
                        WHERE `id` > %d 
                        ORDER BY `order` ASC LIMIT 1",
                        $table_name,
                        $process_id
                    ),
                    ARRAY_A
                );
            } else {
                $result = array();
            }
            
            // Cache the result for future requests
            wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
        }
        
        return $result;
    }
}
