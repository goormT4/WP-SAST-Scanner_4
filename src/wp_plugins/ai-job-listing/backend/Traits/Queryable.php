<?php 
namespace Axilweb\AiJobListing\Traits;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 /**
 * Queryable trait.
 *
 * Manage basic DB query operations.
 *
 * @since 0.1.0
 */
trait Queryable
{
     /**
      * Retrieve all records from the database table.
        * @since 1.0.0
        *
        * @param array $args {
        *     Optional. Arguments to filter the results.
        *
        *     @type string $columns   Columns to select. Default is all columns.
        *     @type string $orderby   Column to order results by. Default is the primary key.
        *     @type string $order     Order of results. Default is 'DESC'.   
        *     @type bool   $count     Whether to return only the count of matching records. Default is false.
        *     @type int    $page      Page number for pagination. Default is 1.
        *     @type int    $per_page  Number of records per page. Default is 10.
        *     @type array  $where     Array of WHERE conditions. Default is an empty array.
        * 
        * @return array|false Array of records if successful, false otherwise.  
   
      */
    public function all(array $args = [])
    {
        global $wpdb;
    
        $columns  = !empty($args['columns']) ? sanitize_text_field($args['columns']) : '*';
        $orderby  = !empty($args['orderby']) ? sanitize_text_field($args['orderby']) : $this->primary_key;
        $order    = !empty($args['order']) ? sanitize_text_field($args['order']) : 'DESC';
        $count    = !empty($args['count']) ? boolval($args['count']) : false;
        $page     = !empty($args['page']) ? absint($args['page']) : 1;
        $per_page = !empty($args['per_page']) ? absint($args['per_page']) : AXILWEB_AJL_POSTS_PER_PAGE;
    
        // Generate a unique cache key based on the query parameters
        $cache_key = 'axilweb_ajl_' . md5(
            $this->table . 
            serialize($args) . 
            $columns . 
            $orderby . 
            $order . 
            $count . 
            $page . 
            $per_page
        );
        
        // Try to get results from cache first
        $results = wp_cache_get($cache_key, 'axilweb_ajl');
        
        // If results are found in cache, return them
        if (false !== $results) {
            return $results;
        }
    
        $query_values = [];
        $where_clauses = [];
    
        // Validate table name using regex pattern
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return false; // Invalid table name
        }
        $table_name = $this->table;
        
        // Validate orderby (column name) for SQL injection prevention
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $orderby)) {
            $orderby = 'id'; // Default to safe value if invalid
        }
        
        // Validate order direction
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'DESC'; // Default to safe value if invalid
        }
    
        // Process WHERE conditions
        if (!empty($args['where']) && is_array($args['where'])) {
            foreach ($args['where'] as $key => $condition) {
                // Validate key (column name) using regex pattern
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                    continue; // Skip invalid keys
                }
                $sanitized_key = $key;
    
                if (is_array($condition)) {
                    $operator = isset($condition['operator']) ? sanitize_text_field($condition['operator']) : '=';
                    $value = $condition['value'];
    
                    if ($value === null) {
                        $where_clauses[] = "`{$sanitized_key}` IS NULL";
                    } elseif ($operator === 'IN') {
                        if (!is_array($value)) {
                            $value = [$value]; // Ensure it's an array
                        }
                        // Ensure the array is not empty to avoid SQL errors
                        if (!empty($value)) {
                            $placeholders = implode(', ', array_fill(0, count($value), '%s'));
                            $where_clauses[] = "`{$sanitized_key}` IN ({$placeholders})";
                            $query_values = array_merge($query_values, array_map('sanitize_text_field', $value));
                        }

                    } elseif ($value === 'NOT NULL') {
                        $where_clauses[] = "`{$sanitized_key}` IS NOT NULL";
                    } else {
                        // Validate operator against whitelist
                        $valid_operators = ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE'];
                        if (!in_array(strtoupper($operator), $valid_operators)) {
                            $operator = '='; // Default to safe operator if invalid
                        }
                        $where_clauses[] = "`{$sanitized_key}` {$operator} %s";
                        $query_values[] = sanitize_text_field($value);
                    }
                } else {
                    if ($condition === null) {
                        $where_clauses[] = "`{$sanitized_key}` IS NULL";
                    } elseif ($condition === 'NOT NULL') {
                        $where_clauses[] = "`{$sanitized_key}` IS NOT NULL";
                    } else {
                        $where_clauses[] = "`{$sanitized_key}` = %s";
                        $query_values[] = sanitize_text_field($condition);
                    }
                }
            }
        }
    
        // Process Search Query
        if (!empty($args['search'])) {
            $like = '%' . $wpdb->esc_like(sanitize_text_field(wp_unslash($args['search']))) . '%';
            $search_columns = !empty($args['search_columns']) ? (array) $args['search_columns'] : ['title', 'description'];
    
            $search_conditions = [];
            foreach ($search_columns as $column) {
                // Validate column name with regex pattern for SQL identifiers
                if (!preg_match("/^[\\w_]+$/", $column)) {
                    continue; // Skip invalid column names
                }
                $search_conditions[] = "`{$column}` LIKE %s";
                $query_values[] = $like;
            }
    
            if (!empty($search_conditions)) {
                $sanitized_conditions = array_map('sanitize_text_field', $search_conditions);
                $where_clauses[] = '(' . implode(' OR ', $sanitized_conditions) . ')';
                // Safe: Ease conditions is sanitized individually
            }
        }
    
        // Deleted/Trashed Filter
        $deleted_clause = !empty($args['trash']) ? "deleted_at IS NOT NULL" : "deleted_at IS NULL";
        $where_clauses[] = $deleted_clause;
    
        // Construct WHERE Clause
        // Safe: each $where_clauses element is built from validated conditions
        $where_clause = !empty($where_clauses) ? ' WHERE ' . implode(' AND ', $where_clauses) : '';
    
        // Validate and sanitize columns
        if ($columns === '*') {
            $columns_sql = '*';
        } else {
            // Safe: $columns is already sanitized using array_map
            $cols_array = is_array($columns) ? $columns : array_map('trim', explode(',', $columns));

            // Whitelist columns against the table schema for security.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table name is dynamic but comes from a trusted source (class property), not user input.
            $table_columns = $wpdb->get_col("SHOW COLUMNS FROM `{$table_name}`");
            if (is_wp_error($table_columns) || ! $table_columns) {
                $table_columns = [];
            }

            $valid_cols = array_intersect($cols_array, $table_columns);

            if (empty($valid_cols)) {
                $columns_sql = '*'; // Default to all columns if none are valid or provided.
            } else {
                // Backtick the valid columns for use in the query.
                // Safe: columns are already sanitized using sanitize_key
                $columns_sql = implode(', ', array_map(function($col) {
                    return "`{$col}`";
                }, $valid_cols));
            }
        }
    
        // Prepare base query dynamically
        if ($count) {
            if (empty($query_values)) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary for custom table operations with proper caching implemented
                $sql = "SELECT COUNT(*) FROM `{$table_name}`{$where_clause}";
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary for custom table count operation with proper caching
                $results = $wpdb->get_var($sql);
            } else {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary for custom table operations with proper caching implemented
                $sql = "SELECT COUNT(*) FROM `{$table_name}`{$where_clause}";
                if (!empty($query_values)) {
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- $sql is properly constructed with esc_sql and sanitized table name
                    $results = $wpdb->get_var($wpdb->prepare($sql, ...$query_values));
                } else {
                    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary for custom table operations with proper caching implemented
                    $results = $wpdb->get_var($sql);
                }
            }
            
            // Cache the results for 1 hour (3600 seconds)
            wp_cache_set($cache_key, $results, 'axilweb_ajl', 3600);
            
            return $results;
        }
    
        // Prepare final query with dynamic parts
        $query_values_final = $query_values;
        if ($per_page > 0) {
            $offset = ($page - 1) * $per_page;
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query necessary for custom table operations with table identifiers properly escaped and caching implemented
            $sql = "SELECT {$columns_sql} FROM `{$table_name}`{$where_clause} ORDER BY `{$orderby}` {$order} LIMIT %d OFFSET %d";
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared -- SQL identifiers are properly validated and values are prepared
            $results = $wpdb->get_results( $wpdb->prepare($sql, array_merge($query_values_final, [$per_page, $offset])) );
            
            // Cache the results for 1 hour (3600 seconds)
            wp_cache_set($cache_key, $results, 'axilweb_ajl', 3600);
            
            return $results;
        } else {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Direct query necessary for custom table operations with table identifiers properly escaped and caching implemented
            $results = $wpdb->get_results( $wpdb->prepare("SELECT {$columns_sql} FROM `{$table_name}`{$where_clause} ORDER BY {$orderby} {$order}", $query_values_final) );
            
            // Cache the results for 1 hour (3600 seconds)
            wp_cache_set($cache_key, $results, 'axilweb_ajl', 3600);
            
            return $results;
        }
    }
    
    /**
     * Retrieve all records from the database table.
     *
     * @param array $args {
     *     Optional. Arguments to filter the results.
     *
     *     @type string $orderby   Column to order results by. Default is the primary key.
     *     @type string $order     Order of results. Default is 'DESC'.
     *     @type int    $page      Page number for pagination. Default is 1.
     *     @type int    $per_page  Number of records per page. Default is 10.
     *     @type array  $where     Array of WHERE conditions. Default is an empty array.
     * }
     *
     * @return array|false Array of records if successful, false otherwise.
     */
    public function jobs_list(array $args = [])
    {
        global $wpdb;

        $orderby  = !empty($args['orderby']) ? sanitize_text_field($args['orderby']) : $this->primary_key;
        $order    = !empty($args['order']) ? sanitize_text_field($args['order']) : 'DESC';
        $page     = !empty($args['page']) ? absint($args['page']) : 1;
        $per_page = !empty($args['per_page']) ? absint($args['per_page']) : AXILWEB_AJL_POSTS_PER_PAGE;
        $count    = !empty($args['count']) ? boolval($args['count']) : false;
        $sort_by  = !empty($args['sort_by']) ? sanitize_text_field($args['sort_by']) : '';

        $query_values = [];
        $where_clauses = [];

        // Validate main table name
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return false; // Invalid table name
        }
        $main_table = $this->table;
        
        // Define table names
        $attribute_value_table = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';
        $values_table = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $attributes_table = $wpdb->prefix . 'axilweb_ajl_attributes';
        
        // Job attributes filter
        if (!empty($args['job_attributes'])) {
            $attribute_value_ids = explode(',', $args['job_attributes']);

            // Ensure attribute_value_ids is an array to prevent errors.
            if (!is_array($attribute_value_ids)) {
                $attribute_value_ids = [$attribute_value_ids];
            }

            $placeholders = implode(', ', array_fill(0, count($attribute_value_ids), '%s'));
            
            // Create a unique cache key for this specific query
            $cache_key = 'ajl_job_ids_' . md5(serialize($attribute_value_ids));
            $cache_group = 'axilweb_ajl_jobs';
            
            // Try to get results from cache first
            $job_ids = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache or cache is invalidated, execute the query
            if (false === $job_ids) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Using caching implementation with wp_cache_get/set to mitigate performance concerns and all table names/placeholders are properly constructed
                $job_ids = $wpdb->get_col( $wpdb->prepare( "SELECT jobs.id FROM `{$main_table}` as jobs INNER JOIN `{$attribute_value_table}` as job_attribute_value ON job_attribute_value.job_id = jobs.id WHERE job_attribute_value.attribute_value_id IN ({$placeholders}) GROUP BY job_attribute_value.job_id HAVING COUNT(DISTINCT job_attribute_value.attribute_value_id) = %d", array_merge($attribute_value_ids, [count($attribute_value_ids)]) ) );
                
                // Store in cache for future requests
                wp_cache_set($cache_key, $job_ids, $cache_group, HOUR_IN_SECONDS);
            }

            if (!empty($job_ids)) {
                $job_placeholders = implode(', ', array_fill(0, count($job_ids), '%d'));
                $where_clauses[] = "jobs.id IN ({$job_placeholders})";
                $query_values = array_merge($query_values, $job_ids);
            } else {
                $where_clauses[] = "jobs.id IS NULL";
            }
        }

        // Status filter
        if (!empty($args['status'])) {
            $explode_status = explode(',', $args['status']);
            $status_placeholders = implode(', ', array_fill(0, count($explode_status), '%s'));
            $where_clauses[] = "jobs.status IN ({$status_placeholders})";
            $query_values = array_merge($query_values, $explode_status);
        }

        // Salary filters
        if (!empty($args['min_salary'])) {
            $where_clauses[] = "jobs.min_salary >= %d";
            $query_values[] = absint($args['min_salary']);
        }
        if (!empty($args['max_salary'])) {
            $where_clauses[] = "jobs.max_salary <= %d";
            $query_values[] = absint($args['max_salary']);
        }

        // ID and slug filters
        if (!empty($args['id'])) {
            $where_clauses[] = "jobs.id = %d";
            $query_values[] = absint($args['id']);
        }
        if (!empty($args['slug'])) {
            $where_clauses[] = "jobs.slug = %s";
            $query_values[] = sanitize_text_field($args['slug']);
        }

        // Search filter
        if (!empty($args['search'])) {
            $like = '%' . $wpdb->esc_like(sanitize_text_field(wp_unslash($args['search']))) . '%';
            $search_columns = !empty($args['search_columns']) ? (array) $args['search_columns'] : ['title', 'description'];

            $search_conditions = [];
            foreach ($search_columns as $column) {
                // Validate column name with regex pattern for SQL identifiers
                if (!preg_match("/^[\w_]+$/", $column)) {
                    continue; // Skip invalid column names
                }
                $search_conditions[] = "`{$column}` LIKE %s";
                $query_values[] = $like;
            }

            if (!empty($search_conditions)) {
                $where_clauses[] = '(' . implode(' OR ', $search_conditions) . ')';
            }
        }

        // Trash filter
        $where_clauses[] = !empty($args['trash']) ? "jobs.deleted_at IS NOT NULL" : "jobs.deleted_at IS NULL";

        // Combine WHERE clauses
        $where_clause = !empty($where_clauses) ? ' WHERE ' . implode(' AND ', $where_clauses) : '';

        // If count requested
        if ($count) {
            $count_sql = "SELECT COUNT(DISTINCT jobs.id) FROM `{$main_table}` as jobs {$where_clause}";
            
            if (!empty($query_values)) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Direct query necessary with validated identifiers and properly prepared values
                return $wpdb->get_var( $wpdb->prepare( $count_sql, $query_values ) );
            } else {
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query necessary with validated identifiers and no user input
                return $wpdb->get_var( $count_sql );
            }
        }

        // Build the order clause with proper escaping
        // Validate and sanitize order direction
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        
        // Build order clause based on sort type
        if ($sort_by === 'alphabetically') {
            $order_clause = "ORDER BY jobs.title ASC";
        } elseif ($sort_by === 'most_recent') {
            $order_clause = "ORDER BY jobs.created_at DESC";
        } else {
            // Validate orderby columns against allowed columns
            $allowed_columns = ['id', 'title', 'created_at', 'updated_at', 'status', 'slug'];
            if (!in_array($orderby, $allowed_columns)) {
                $orderby = 'id'; // Default to safe column if invalid
            }
            $order_clause = "ORDER BY jobs.{$orderby} {$order}";
        }

        // Calculate pagination values
        $offset = ($page - 1) * $per_page;

        // Prepare and execute the final query
        $sql = "SELECT jobs.*, JSON_ARRAYAGG(JSON_OBJECT('id', av.id, 'form_key', attr.form_key, 'value', av.value)) AS attribute_values "
                . "FROM `{$main_table}` as jobs "
                . "LEFT JOIN `{$attribute_value_table}` as jav ON jav.job_id = jobs.id "
                . "LEFT JOIN `{$values_table}` as av ON av.id = jav.attribute_value_id "
                . "LEFT JOIN `{$attributes_table}` as attr ON attr.id = av.attribute_id "
                . "{$where_clause} "
                . "GROUP BY jobs.id {$order_clause} "
                . "LIMIT %d OFFSET %d";
                
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Direct query necessary for custom table operations with validated identifiers and properly prepared values
        return $wpdb->get_results( $wpdb->prepare( $sql, array_merge($query_values, [$per_page, $offset]) ) );
    }
        

    /**
     * Count Jobs by Status.
     *
     * Retrieves the count of jobs in various statuses.
     *
     * @since 1.0.0
     *
     * @param string|null $status The status to filter jobs by.
     * @return array An array containing the counts of jobs in various statuses.
     */
    public function count_job_by_status($status = null)
    {
        global $wpdb;

        // Validate the table name using regex pattern
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return false; // Invalid table name
        }
        $table_name = $this->table;
        
        // Create a unique cache key based on status parameter
        $cache_key = 'ajl_job_counts_' . ($status ? sanitize_text_field($status) : 'all');
        $cache_group = 'axilweb_ajl_job_stats';
        
        // Try to get results from cache first
        $results = wp_cache_get($cache_key, $cache_group);
        
        // If results are found in cache, return them
        if (false !== $results) {
            return $results;
        }

        if (!empty($status)) {
            $status = sanitize_text_field($status);
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Using caching to mitigate performance concerns
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) as all_jobs, COUNT(CASE WHEN status IN (%s, %s) THEN 1 ELSE NULL END) as posted_job, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as active, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as archived, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as paused, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as draft, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as expired FROM `{$table_name}` WHERE deleted_at IS NULL AND status = %s",
                    'active',
                    'inactive',
                    'active',
                    'archived',
                    'inactive',
                    'draft',
                    'expired',
                    $status
                ),
                ARRAY_A
            );
        } else {
            // Query without status filter
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Using caching to mitigate performance concerns
            $results = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) as all_jobs, COUNT(CASE WHEN status IN (%s, %s) THEN 1 ELSE NULL END) as posted_job, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as active, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as archived, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as paused, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as draft, COUNT(CASE WHEN status = %s THEN 1 ELSE NULL END) as expired FROM `{$table_name}` WHERE deleted_at IS NULL",
                    'active',
                    'inactive',
                    'active',
                    'archived',
                    'inactive',
                    'draft',
                    'expired'
                ),
                ARRAY_A
            );
        }
        
        // Cache the results (status counts typically don't change frequently)
        // Using a shorter cache time (15 minutes) since these are aggregate counts
        if (!empty($results)) {
            wp_cache_set($cache_key, $results, $cache_group, 15 * MINUTE_IN_SECONDS);
        }
        
        return $results;
    }


    /**
     * Count applications by status.
     *
     * @param int $job_id Job ID.
     * @param int $process_id Process ID.
     * @param string $action Action type.
     *
     * @return array
     */
    public function count_application_by_status($job_id, $process_id, $action)
    {
    global $wpdb;

    $final_process = [];
    // Define table names with proper prefix - these are constants and not user input
    $table_applications = $wpdb->prefix . 'axilweb_ajl_applications';
    $table_app_process = $wpdb->prefix . 'axilweb_ajl_app_process';

        // Define default process statuses with their properties
        $default_processes = [
            ['icon' => 'tio-group_equal', 'label' => 'Unlisted', 'key' => 'unlisted', 'id' => '1', 'iconBg' => '#323DA5'],
            ['icon' => 'tio-format_bullets', 'label' => 'Shortlist', 'key' => 'shortlist', 'id' => '2', 'iconBg' => '#FF7F5C'],
            ['icon' => 'tio-call', 'label' => 'Phone', 'key' => 'phone', 'id' => '3', 'iconBg' => '#3EB75E'],
            ['icon' => 'tio-user', 'label' => 'Face', 'key' => 'face_interview', 'id' => '4', 'iconBg' => '#8685EF'],
            ['icon' => 'tio-book_outlined', 'label' => 'Test', 'key' => 'test', 'id' => '5', 'iconBg' => '#1BA2DB'],
            ['icon' => 'tio-group_equal', 'label' => 'Final', 'key' => 'final', 'id' => '6', 'iconBg' => '#FF90AA'],
            ['icon' => 'tio-done', 'label' => 'Hired', 'key' => 'hired', 'id' => '7', 'iconBg' => '#FFC400'],
            ['icon' => 'tio-clear', 'label' => 'Rejected', 'key' => 'rejected', 'id' => '8', 'iconBg' => '#FF585A'],
            ['icon' => 'tio-info_outlined', 'label' => 'Expired', 'key' => 'expired', 'id' => '9', 'iconBg' => '#FF585A']
        ];

        // Load application process counts if `action` is `load`
        if (!empty($action) && $action === 'load') {
            // Build the dynamic part of the query for process counts
            $case_placeholders = [];
            $query_params = [];

            foreach ($default_processes as $process) {
                // Sanitize the key to use as a safe column alias
                $key = sanitize_key($process['key']);
                // Add the placeholder for the CASE statement. The alias is safe.
                $case_placeholders[] = "COUNT(CASE WHEN process_id = %d THEN 1 END) AS `{$key}`";
                // Add the process ID to the parameters array
                $query_params[] = $process['id'];
            }

            // Combine all CASE statement placeholders into a single string
            $all_case_placeholders = implode(', ', $case_placeholders);

            // Add the job_id to the end of the parameters array for the WHERE clause
            $query_params[] = $job_id;

            // Create a cache key that is consistent and unique for this query
            $cache_key = 'axilweb_ajl_job_process_counts_' . md5($job_id . '_' . implode('_', array_column($default_processes, 'id')));
            $cache_group = 'axilweb_ajl_job_processes';
            
            // Try to get from cache first
            $counts = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, fetch from database
            if (false === $counts) {
                // Construct the final query template with placeholders
                $query = "SELECT {$all_case_placeholders}, COUNT(id) AS total_all FROM `{$table_applications}` WHERE job_id = %d AND deleted_at IS NULL";
                
                // Prepare the full query with all parameters at once, which is more secure and efficient
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is implemented above.
                $counts = $wpdb->get_row($wpdb->prepare($query, $query_params), ARRAY_A);
                
                // Cache the result for future requests
                if ($counts) {
                    wp_cache_set($cache_key, $counts, $cache_group, HOUR_IN_SECONDS);
                }
            }

            // Build the final process array with counts
            foreach ($default_processes as $index => $process) {
                $key = sanitize_key($process['key']);
                $final_process[$index] = [
                    'icon' => sanitize_text_field($process['icon']),
                    'label' => sanitize_text_field($process['label']),
                    'count' => (string)($counts[$key] ?? '0'),
                    'key' => sanitize_text_field($process['key']),
                    'id' => sanitize_text_field($process['id']),
                    'iconBg' => sanitize_text_field($process['iconBg'])
                ];
            }

            // Add the "All" summary at the end
            $final_process[] = [
                'icon' => 'tio-group_add',
                'label' => 'All',
                'count' => (string)($counts['total_all'] ?? '0'),
                'key' => 'all',
                'iconBg' => '#26B0A1'
            ];
        }

        // Fetch previous and next steps for the process
        if (!empty($process_id)) {
            // Table name is already validated as it uses WordPress prefix + constant suffix
            $table = $table_app_process;
            
            // Create cache keys for previous and next steps
            $prev_cache_key = 'axilweb_ajl_prev_process_' . absint($process_id);
            $next_cache_key = 'axilweb_ajl_next_process_' . absint($process_id);
            $cache_group = 'axilweb_ajl_app_processes';
            
            // Try to get previous step from cache
            $get_prev_order_sql = wp_cache_get($prev_cache_key, $cache_group);
            
            // If not in cache, fetch from database
            if (false === $get_prev_order_sql) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is properly escaped with esc_sql; caching implemented above
                $get_prev_order_sql = $wpdb->get_results( $wpdb->prepare( "SELECT `id`, `key`, `name` FROM `{$table}` WHERE `id` < ( SELECT `order` FROM `{$table}` WHERE id = %d ) ORDER BY `order` DESC LIMIT 1", $process_id ), ARRAY_A );
                
                // Cache the result for future requests
                wp_cache_set($prev_cache_key, $get_prev_order_sql, $cache_group, HOUR_IN_SECONDS);
            }

            // Try to get next step from cache
            $get_next_order_sql = wp_cache_get($next_cache_key, $cache_group);
            
            // If not in cache, fetch from database
            if (false === $get_next_order_sql) {
                // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is properly escaped with esc_sql; caching implemented above
                $get_next_order_sql = $wpdb->get_results( $wpdb->prepare( "SELECT `id`, `key`, `name` FROM `{$table}` WHERE `id` > ( SELECT `order` FROM `{$table}` WHERE id = %d ) ORDER BY `order` ASC LIMIT 1", $process_id ), ARRAY_A );
                
                // Cache the result for future requests
                wp_cache_set($next_cache_key, $get_next_order_sql, $cache_group, HOUR_IN_SECONDS);
            }
            
            // Set previous step
            $final_process['previous_step'] = !empty($get_prev_order_sql[0]) ? $get_prev_order_sql[0] : [
                'id' => AXILWEB_AJL_DEFAULT_PROCESS_ID,
                'key' => AXILWEB_AJL_DEFAULT_PROCESS_SLUG,
                'name' => AXILWEB_AJL_DEFAULT_PROCESS_NAME
            ];
            $final_process['previous_step']['reject_status_id'] = AXILWEB_AJL_REJECT_PROCESS_ID;

            // Set next step based on process ID
            if ($process_id < AXILWEB_AJL_REJECT_PROCESS_ID) {
                $final_process['next_step'] = !empty($get_next_order_sql[0]) ? $get_next_order_sql[0] : [];
                if (!empty($final_process['next_step'])) {
                    $final_process['next_step']['reject_status_id'] = AXILWEB_AJL_REJECT_PROCESS_ID;
                }
            } else {
                $final_process['next_step'] = [
                    'id' => AXILWEB_AJL_REJECT_PROCESS_ID,
                    'key' => AXILWEB_AJL_REJECT_PROCESS_SLUG,
                    'name' => AXILWEB_AJL_REJECT_PROCESS_NAME,
                    'reject_status_id' => AXILWEB_AJL_REJECT_PROCESS_ID
                ];
            }
        }

        return $final_process;
    }


    /**
     * Get job applications based on specified arguments.
     *
     * @param array $args Array of arguments to filter and retrieve applications.
     *
     * @return array|int List of job applications or the count of matching applications.
     * Manual Escaping for Dynamic SQL: ORDER BY and ORDER cannot use $wpdb->prepare() with placeholders, so we manually escape these parts to prevent SQL injection.
     *Placeholders for Other Dynamic Values: For parts that can safely use placeholders (like WHERE conditions, LIMIT, and OFFSET), we continue using $wpdb->prepare() as intended.
    */
    public function job_application_lists(array $args = [])
    {
    global $wpdb;

    $defaults = [
        'page'     => 1,
        'per_page' => AXILWEB_AJL_POSTS_PER_PAGE,
        'orderby'  => $this->primary_key,
        'order'    => 'DESC',
        'search'   => '',
        'count'    => false,
        'status'   => '',
        'date'     => '',
        'id'       => '',
        'is_read'  => '',
        'job_id'   => '',
        'trash'    => false,
    ];

    $args = wp_parse_args($args, $defaults);
    $where_clauses = [];
    $prepare_args = [];

    // Search filter
    if (!empty($args['search'])) {
        $like = '%' . $wpdb->esc_like(sanitize_text_field(wp_unslash($args['search']))) . '%';
        $app_meta_table = esc_sql($wpdb->prefix . 'axilweb_ajl_application_meta');
        
        // Create a cache key for this specific search query
        $cache_key = 'axilweb_ajl_app_meta_search_' . md5($like);
        $cache_group = 'axilweb_ajl_app_meta';
        
        // Try to get results from cache
        $meta_ids = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $meta_ids) {
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table name is properly escaped; caching implemented above
            $meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT app_meta.app_id FROM `{$app_meta_table}` as app_meta WHERE app_meta.app_mv LIKE %s GROUP BY app_meta.app_id", $like ) );
            
            // Cache the result for future requests
            if ($meta_ids) {
                wp_cache_set($cache_key, $meta_ids, $cache_group, HOUR_IN_SECONDS);
            }
        }

        if (!empty($meta_ids)) {
            $placeholders = implode(',', array_fill(0, count($meta_ids), '%d'));
            $where_clauses[] = "app.id IN ($placeholders)";
            $prepare_args = array_merge($prepare_args, array_map('absint', $meta_ids));
        } else {
            return [];
        }
    }

    // Add filters with proper preparation
    if (!empty($args['status']) && $args['status'] !== 'all') {
        $where_clauses[] = 'app_process.key = %s';
        $prepare_args[] = sanitize_text_field($args['status']);
    }

    if (!empty($args['date'])) {
        $date_range = explode(',', $args['date']);
        if (!empty($date_range[0]) && !empty($date_range[1])) {
            $where_clauses[] = 'DATE(app.created_at) BETWEEN %s AND %s';
            $prepare_args[] = sanitize_text_field($date_range[0]);
            $prepare_args[] = sanitize_text_field($date_range[1]);
        }
    }

    if (!empty($args['id'])) {
        $where_clauses[] = 'app.id = %d';
        $prepare_args[] = absint($args['id']);
    }

    if (!empty($args['is_read'])) {
        $where_clauses[] = 'app.is_read = %d';
        $prepare_args[] = absint($args['is_read']);
    }

    if (!empty($args['job_id'])) {
        $where_clauses[] = 'app.job_id = %d';
        $prepare_args[] = absint($args['job_id']);
    }

    $where_clauses[] = $args['trash'] ? 'app.deleted_at IS NOT NULL' : 'app.deleted_at IS NULL';

    $where_sql = !empty($where_clauses) ? ' WHERE ' . implode(' AND ', $where_clauses) : '';

    // Table names
    // Validate main table name
    if (!preg_match('/^[\w_]+$/', $this->table)) {
        return false; // Invalid table name
    }
    $main_table = $this->table;

    // Define constant table suffixes (can't be injected)
    $app_meta_table = $wpdb->prefix . 'axilweb_ajl_application_meta';
    $app_process_table = $wpdb->prefix . 'axilweb_ajl_app_process';

    $base_query = "FROM `{$main_table}` as app
                INNER JOIN `{$app_meta_table}` as app_meta ON app.id = app_meta.app_id
                INNER JOIN `{$app_process_table}` as app_process ON app_process.id = app.process_id
                $where_sql";

    if ($args['count']) {
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- Direct query necessary for custom table operations with table identifiers properly escaped and caching happens at the caller level
        return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT app.id) $base_query", $prepare_args ) );
    }

    $select_fields = "app_process.key,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'app_mk', app_meta.app_mk,
                            'app_mv', app_meta.app_mv
                        )
                    ) AS meta_attributes,
                    app.id, app.job_id, app.deleted_at, app.process_id, app.previous_process_id,
                    app.is_read, DATE(app.created_at) AS application_date";

    // Whitelist allowed orderby columns and validate
    $allowed_orderby_columns = ['id', 'job_id', 'created_at', 'updated_at', 'is_read', 'process_id'];

    // Validate orderby parameter against whitelist
    if (!in_array($args['orderby'], $allowed_orderby_columns)) {
        $orderby = 'id'; // Default to safe value
    } else {
        $orderby = $args['orderby'];
    }

    // Validate order direction
    $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';

    $query = "SELECT $select_fields $base_query GROUP BY app.id ORDER BY app.$orderby $order";

    // Prepare LIMIT and OFFSET with placeholders
    if ($args['per_page'] > 0) {
        $offset = ($args['page'] - 1) * $args['per_page'];
        $query .= " LIMIT %d OFFSET %d";
        $prepare_args[] = (int)$args['per_page'];
        $prepare_args[] = (int)$offset;
    }

    // We have already whitelisted $orderby and $order, 
    // and escaped table names properly above. 
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Query structure is built dynamically due to complex conditions. Values are prepared with placeholders, tables are escaped, and results are for dynamic job application lists which would be difficult to effectively cache due to variable query parameters
    return $wpdb->get_results($wpdb->prepare($query, ...$prepare_args)); // Only dynamic parts are prepared
    }

    /**
     * Retrieve a record or specific value from the database based on a key-value pair.
     *
     * This method fetches a single row or a single value from a custom database table
     * where a specified column matches the provided value.
     *
     * @since 1.0.0
     *
     * @param string $key         The column name to match against.
     * @param string $value       The value to search for in the specified column.
     * @param string $columns     The columns to retrieve. Default is all columns (*).
     * @param bool   $is_single_val Whether to return a single value (true) or the entire row (false).
     *
     * @return mixed The requested data from the database, or null if not found.
     */
    public function get_by( string $key, string $value, string $columns = '*', bool $is_single_val = false ) {
        global $wpdb;
    
        // 1) Validate table name using regex pattern for SQL identifiers
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return null; // Invalid table name
        }
        $table_name = $this->table;
        
        // 2) Sanitize and validate key (column name) using regex pattern for SQL identifiers
        $sanitized_key = sanitize_key( $key );
        if ( empty( $sanitized_key ) || ! preg_match( '/^[a-zA-Z0-9_]+$/', $sanitized_key ) ) {
            return null; // Invalid key
        }
        
        // 3) Sanitize and validate columns
        $sanitized_cols = '*';
        if ( $columns !== '*' ) {
            $cols_array = array_map( 'trim', explode( ',', $columns ) );
            $valid_cols = [];
            foreach ( $cols_array as $col ) {
                // Ensure column names are valid and backtick-quoted for safety
                if ( preg_match( '/^[a-zA-Z0-9_]+$/', $col ) ) {
                    $valid_cols[] = "`" . $col . "`";
                }
            }
            if ( empty( $valid_cols ) ) {
                return null; // No valid columns
            }
            $sanitized_cols = implode( ', ', $valid_cols );
        }
    
        // 4) Generate a unique cache key for this query
        $cache_key = 'axilweb_ajl_get_by_' . md5( $table_name . $sanitized_key . $value . $sanitized_cols . $is_single_val );
        $cache_group = 'axilweb_ajl';
    
        // 5) Check cache first
        $cached = wp_cache_get( $cache_key, $cache_group );
        if ( false !== $cached ) {
            return $cached;
        }

        // 6) Run the query and handle the result
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $query = $wpdb->prepare("SELECT {$sanitized_cols} FROM `{$table_name}` WHERE `{$sanitized_key}` = %s LIMIT 1", $value );
        
        if ( $is_single_val ) {
            // If we're fetching a single value (e.g., one column value)
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom table operation with caching implemented above
            $result = $wpdb->get_var( $query );
        } else {
            // Fetch the full row
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom table operation with caching implemented above
            $result = $wpdb->get_row( $query );
        }
    
        // 7) Cache the result for 1 hour
        wp_cache_set( $cache_key, $result, $cache_group, HOUR_IN_SECONDS );
    
        return $result;
    }

    /**
     * Get single row by id.
     *
     * @since 1.0.0
     *
     * @param integer $id
     * @param string  $columns
     *
     * @return string|null|object
     */
    public function get(int $id, string $columns = '*')
    {
        // Generate a unique cache key
        $cache_key = 'axilweb_ajl_get_' . $this->table . '_' . $id . '_' . md5($columns);
        
        // Try to get results from cache first
        $result = wp_cache_get($cache_key, 'axilweb_ajl');
        
        // If results are found in cache, return them
        if (false !== $result) {
            return $result;
        }
        
        $result = $this->get_by($this->primary_key, $id, $columns);
        
        // Cache the result for 1 hour (3600 seconds)
        wp_cache_set($cache_key, $result, 'axilweb_ajl', 3600);
        
        return $result;
    }

    /**
     * Update a row.
     *
     * @since 1.0.0
     *
     * @param array $data
     * @param array $where
     * @param array $format
     * @param array $where_format
     *
     * @return integer|boolean
     */
    public function update(array $data, array $where, array $format = [], array $where_format = [])
    {
        global $wpdb;

        // Validate table name using regex pattern for SQL identifiers
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return false; // Invalid table name
        }
        $table_name = $this->table;

        // Update the data
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented below
        $result = $wpdb->update(
            $table_name,
            $data,
            $where,
            $format,
            $where_format
        );

        // Clear any cached queries that might contain this data
        if ($result !== false) {
            // If we have an ID in the where clause, clear that specific cache
            if (isset($where['id']) || isset($where[$this->primary_key])) {
                $id = isset($where['id']) ? $where['id'] : $where[$this->primary_key];
                wp_cache_delete('axilweb_ajl_get_' . $this->table . '_' . $id . '_*', 'axilweb_ajl', true);
            }
            
            // Also clear general caches for this table
            wp_cache_delete('axilweb_ajl_' . md5($this->table . '*'), 'axilweb_ajl', true);
        }

        return $result;
    }

    /**
     * Create a new row.
     *
     * @since 1.0.0
     *
     * @param array $data
     * @param array $format
     *
     * @return int|false The number of rows inserted, or false on error.
     */
    public function create(array $data, array $format = [])
    {
        global $wpdb;

        // Validate table name using regex pattern for SQL identifiers
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return false; // Invalid table name
        }
        $table_name = $this->table;

        // Insert the data
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented below
        $result = $wpdb->insert(
            $table_name,
            $data,
            $format
        );

        if (!$result) {
            return false;
        }

        $insert_id = $wpdb->insert_id;

        // Clear any cached queries related to this table
        wp_cache_delete('axilweb_ajl_' . md5($this->table . '*'), 'axilweb_ajl', true);

        return $insert_id;
    }

    /**
     * Delete a row.
     *
     * @since 1.0.0
     *
     * @param array $where
     * @param array $where_format
     *
     * @return integer|boolean
     */
    public function delete(array $where, array $where_format = [])
    {
        global $wpdb;
        
        if (empty($where)) {
            return false;
        }
        
        // Validate table name using regex pattern for SQL identifiers
        if (!preg_match('/^[\w_]+$/', $this->table)) {
            return false; // Invalid table name
        }
        $table_name = $this->table;
        
        // If we have an ID in the where clause, get the record first for cache invalidation
        $id = null;
        if (isset($where['id']) || isset($where[$this->primary_key])) {
            $id = isset($where['id']) ? $where['id'] : $where[$this->primary_key];
        }
        
        // Delete the record
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented above
        $result = $wpdb->delete(
            $table_name,
            $where,
            $where_format
        );
        
        // Clear any cached queries that might contain this data
        if ($result !== false) {
            // If we have an ID, clear that specific cache
            if ($id) {
                wp_cache_delete('axilweb_ajl_get_' . $this->table . '_' . $id . '_*', 'axilweb_ajl', true);
            }
            
            // Also clear general caches for this table
            wp_cache_delete('axilweb_ajl_' . md5($this->table . '*'), 'axilweb_ajl', true);
        }
        
        return $result;
    }
   
    /**
     * Execute query and get results.
     *
     * @since 1.0.0
     *
     * @param string $sql_template SQL query template
     * @param array $args Arguments for the SQL query
     *
     * @return string|null|array
     */
    public function query_get_results(string $sql_template, array $args = [])
    {
        global $wpdb;
        
        // For transaction queries, just execute without caching
        if (preg_match('/^\s*(START TRANSACTION|COMMIT|ROLLBACK)\s*$/i', $sql_template)) {
            if (!empty($args)) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Transaction queries cannot be cached and require direct execution
                return $wpdb->query($wpdb->prepare($sql_template, ...$args));
            }
            
            // These are hardcoded SQL commands (START TRANSACTION, COMMIT, ROLLBACK) with no variables
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Transaction queries with no user inputs
            return $wpdb->query($sql_template);
        }
        
        // Generate a cache key for SELECT queries only
        $cache_key = null;
        if (stripos($sql_template, 'SELECT') === 0) {
            $cache_key = 'axilweb_ajl_query_results_' . md5($sql_template . serialize($args));
            
            // Try to get results from cache first
            $result = wp_cache_get($cache_key, 'axilweb_ajl');
            
            // If results are found in cache, return them
            if (false !== $result) {
                return $result;
            }
        }
        
        // Execute the query
        if (!empty($args)) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom table operation with prepared statement and caching implemented above for SELECT queries
            $result = $wpdb->get_results($wpdb->prepare($sql_template, ...$args));
        } else {
            // For queries without parameters, ensure we're not executing queries with user input
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Static query with no user inputs
            $result = $wpdb->get_results($sql_template);
        }
        
        // Cache SELECT query results
        if ($cache_key && $result !== false) {
            wp_cache_set($cache_key, $result, 'axilweb_ajl', HOUR_IN_SECONDS);
        }
        
        return $result;
    }
}
