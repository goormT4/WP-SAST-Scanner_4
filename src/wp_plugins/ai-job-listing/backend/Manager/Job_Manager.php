<?php
namespace Axilweb\AiJobListing\Manager;
use Axilweb\AiJobListing\Models\Job;
use Axilweb\AiJobListing\Helpers\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Job_Manager
{

    /**
     * Job class.
     *
     * @var Job
     */
    public $job;
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->job = new Job();
    }

    /**
     * Retrieve Jobs.
     *
     * Fetches a list of jobs based on the provided query arguments. Supports pagination, sorting, 
     * filtering by job attributes, and salary ranges. If the `count` argument is set to true, 
     * it returns the total number of jobs instead of the list.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to query jobs. Default empty array.
     *
     *     @type int    $page           The current page number. Default is the constant `AXILWEB_AJL_DEFAULT_PAGE`.
     *     @type int    $per_page       The number of jobs per page. Default is the constant `AXILWEB_AJL_POSTS_PER_PAGE`.
     *     @type string $orderby        The column to sort by. Default is the constant `AXILWEB_AJL_DEFAULT_ORDERBY`.
     *     @type string $order          The sort direction. Accepts 'ASC' or 'DESC'. Default is the constant `AXILWEB_AJL_DEFAULT_ORDER`.
     *     @type string $search         Search term to filter jobs. Default is an empty string.
     *     @type string $job_attributes Filter jobs by specific attributes. Default is an empty string.
     *     @type int    $min_salary     Minimum salary to filter jobs. Default is null.
     *     @type int    $max_salary     Maximum salary to filter jobs. Default is null.
     *     @type bool   $count          Whether to return only the total count of jobs. Default is false.
     *     @type array  $where          Additional conditions to filter jobs. Default is empty.
     * }
     *
     * @return array|int List of jobs on success, or the total count if `count` is true.
     */  
    public function all(array $args = [])
        {
            // Default arguments
            $defaults = [
                'page'           => AXILWEB_AJL_DEFAULT_PAGE,
                'per_page'       => AXILWEB_AJL_POSTS_PER_PAGE,
                'orderby'        => AXILWEB_AJL_DEFAULT_ORDERBY,
                'order'          => AXILWEB_AJL_DEFAULT_ORDER,
                'search'         => '',
                'job_attributes' => '',
                'min_salary'     => null,
                'max_salary'     => null,
                'count'          => false,
                'where'          => [],
            ];

            // Merge provided arguments with defaults
            $args = wp_parse_args($args, $defaults);

            // Prepare the `where` block as an array
            $where_clauses = [];

            // Handle job attributes filter
            if (!empty($args['job_attributes'])) {
                $where_clauses['job_attributes'] = [
                    'operator' => '=',
                    'value'    => sanitize_text_field($args['job_attributes']),
                ];
            }

            // Handle salary range filters
            if (!is_null($args['min_salary'])) {
                $where_clauses['min_salary'] = [
                    'operator' => '>=',
                    'value'    => absint($args['min_salary']),
                ];
            }

            if (!is_null($args['max_salary'])) {
                $where_clauses['max_salary'] = [
                    'operator' => '<=',
                    'value'    => absint($args['max_salary']),
                ];
            }

            // Handle search filter
            if (!empty($args['search'])) {
                $where_clauses['title'] = [
                    'operator' => 'LIKE',
                    'value'    => '%' . sanitize_text_field($args['search']) . '%',
                ];
                $where_clauses['description'] = [
                    'operator' => 'LIKE',
                    'value'    => '%' . sanitize_text_field($args['search']) . '%',
                ];
            }

            // Assign `where` clauses back to the args
            $args['where'] = $where_clauses;

            // Call the `jobs_list` method
            $jobs = $this->job->jobs_list($args);

            // Return count if requested
            if ($args['count']) {
                return (int) $jobs;
            }

            return $jobs;
        }

    /**
     * Retrieve Job Count or List.
     *
     * Fetches the total count of jobs or a list of jobs based on the provided query arguments. 
     * Supports filtering, sorting, and pagination. If the `count` argument is set to true, 
     * it returns the total number of jobs instead of the list.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to query jobs. Default empty array.
     *
     *     @type int    $page           The current page number. Default is the constant `AXILWEB_AJL_DEFAULT_PAGE`.
     *     @type int    $per_page       The number of jobs per page. Default is the constant `AXILWEB_AJL_POSTS_PER_PAGE`.
     *     @type string $orderby        The column to sort by. Default is the constant `AXILWEB_AJL_DEFAULT_ORDERBY`.
     *     @type string $order          The sort direction. Accepts 'ASC' or 'DESC'. Default is the constant `AXILWEB_AJL_DEFAULT_ORDER`.
     *     @type string $search         Search term to filter jobs. Default is an empty string.
     *     @type string $job_attributes Filter jobs by specific attributes. Default is an empty string.
     *     @type int    $min_salary     Minimum salary to filter jobs. Default is null.
     *     @type int    $max_salary     Maximum salary to filter jobs. Default is null.
     *     @type bool   $count          Whether to return only the total count of jobs. Default is false.
     *     @type array  $where          Additional conditions to filter jobs. Default is empty.
     * }
     *
     * @return array|int List of jobs on success, or the total count if `count` is true.
     */ 
    public function allcount(array $args = [])
    {
        
        $defaults = [
            'page'              => AXILWEB_AJL_DEFAULT_PAGE,
            'per_page'          => (isset($args['per_page']) && !empty($args['per_page'])) ? $args['per_page'] : AXILWEB_AJL_POSTS_PER_PAGE,
            'orderby'           => (isset($args['orderby']) && !empty($args['orderby'])) ? $args['orderby'] : AXILWEB_AJL_DEFAULT_ORDERBY,
            'order'             => (isset($args['order']) && !empty($args['order'])) ? $args['order'] : AXILWEB_AJL_DEFAULT_ORDER,
            'search'            => '',
            'job_attributes'    => '',
            'min_salary'        => null,
            'max_salary'        => null,
            'count'             => false,
            'where'             => [],
        ];

        $args = wp_parse_args($args, $defaults); 
        $jobs = $this->job->jobs_list($args);
        if ($args['count']) {
            return (int) $jobs;
         
        }
       
        return $jobs;
    }
    
    /**
     * Count Jobs by Status.
     *
     * Retrieves the total count of jobs grouped by their status. 
     * This function delegates the actual data retrieval to the `job` object.
     *
     * @since 1.0.0
     *
     * @return array An associative array containing job counts by status.
     */ 
    public function count_job_by_status()
    {
        $jobs_count = $this->job->count_job_by_status();
        return $jobs_count;
    }

    /**
     * Retrieve a Single Job.
     *
     * Fetches a specific job based on the provided key-value pair. 
     * The `key` specifies the database column to search, and the `value` is the value to match.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to retrieve a specific job. Default empty array.
     *
     *     @type string $key   The database column to search by. Default is 'id'.
     *     @type mixed  $value The value to match in the specified column. Default is an empty string.
     * }
     *
     * @return array|null The job data as an associative array on success, or null if no matching job is found.
     */ 
    public function get(array $args = [])
    {
        $defaults = [
            'key' => 'id',
            'value' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        if (empty($args['value'])) {
            return null;
        }

        return $this->job->get_by($args['key'], $args['value']);
    }

    /**
     * Create a Job.
     *
     * Inserts a new job record into the database with the provided data. 
     * Fires a custom action hook after the job is successfully created.
     *
     * @since 1.0.0
     *
     * @param array $data The data to insert for the job. Keys should match the database column names.
     *
     * @return int|WP_Error The ID of the newly created job on success, or a WP_Error on failure.
     */ 
    public function create($data)
    {
        // Prepare job data for database-insertion.
        $job_data = $this->job->prepare_for_database($data);

        $job_id = (new Job)->create(
            $job_data,
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
            ]
        );

        if (!$job_id) {
            return new \WP_Error('axilweb-ajl-job-create-failed', __('Failed to create job.', 'ai-job-listing'));
        }

        /**
         * Fires after a job has been created.
         *
         * @since 1.0.0
         *
         * @param int   $job_id
         * @param array $job_data
         */
        do_action('axilweb_ajl_jobs_created', $job_id, $job_data);

        return $job_id;
    }

    /**
     * Update a Job.
     *
     * Updates an existing job record in the database with the provided data. 
     * Fires a custom action hook after the job is successfully updated.
     *
     * @since 1.0.0
     *
     * @param array $data   The data to update for the job. Keys should match the database column names.
     * @param int   $job_id The ID of the job to update.
     *
     * @return int|WP_Error The ID of the updated job on success, or a WP_Error on failure.
     */ 
    public function update(array $data, int $job_id)
    {
        // Prepare job data for database-insertion.
        $job_data = $this->job->prepare_for_database($data);

        // Update job.
        $updated = $this->job->update(
            $job_data,
            [
                'id' => $job_id,
            ],
            [
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );

        if (!$updated) {
            return new \WP_Error('axilweb-ajl-job-update-failed', __('Failed to update job.', 'ai-job-listing'));
        }

        if ($updated >= 0) {
            /**
             * Fires after a job is being updated.
             *
             * @since 1.0.0
             *
             * @param int   $job_id
             * @param array $job_data
             */
            do_action('axilweb_ajl_jobs_updated', $job_id, $job_data);

            return $job_id;
        }

        return new \WP_Error('axilweb-ajl-job-update-failed', __('Failed to update the job.', 'ai-job-listing'));
    }

     /**
      * Delete or Restore Jobs.
      *
      * Deletes or restores one or more jobs from the database. 
      * This operation cannot be undone. Fires a custom action hook after the jobs are deleted or restored.
      *
      * @since 1.0.0
      *
      * @param array|int $job_ids The ID or array of IDs of jobs to delete or restore.
      * @param string    $action  The action to perform. Can be "soft_delete" or "restore".
      *
      * @return int|WP_Error The total number of jobs deleted or restored on success, or a WP_Error on failure. 
      */
    public function delete($job_ids, $action = "soft_delete")
    {
        if (!is_array($job_ids)) {
            $job_ids = [absint($job_ids)];
        } else {
            $job_ids = array_map('absint', $job_ids);
        }

        try {
            $this->job->query_get_results('START TRANSACTION');

            $total_deleted = 0;
            foreach ($job_ids as $job_id) {
                $deleted = $this->job->update(
                    [
                        'deleted_at' => ($action === 'restore') ? null : current_datetime()->format('Y-m-d H:i:s'),
                        'deleted_by' => get_current_user_id(),
                    ],
                    [
                        'id' => $job_id,
                    ],
                    [
                        '%s',
                        '%d',
                    ],
                    [
                        '%d',
                    ]
                );

                if ($deleted) {
                    $total_deleted += intval($deleted);
                }

                /**
                 * Fires after a job has been deleted or restored.
                 *
                 * @since 1.0.0
                 *
                 * @param int $job_id
                 */
                do_action('axilweb_ajl_job_deleted', $job_id);
            }

            $this->job->query_get_results('COMMIT');

            return $total_deleted;
        } catch (\Exception $e) {
            $this->job->query_get_results('ROLLBACK');

            return new \WP_Error('axilweb-ajl-job-delete-error', $e->getMessage());
        }
    }


    /**
     * Permanently Delete Jobs.
     *
     * Deletes one or more jobs from the database permanently. 
     * This operation cannot be undone. Fires a custom action hook after the jobs are deleted.
     *
     * @since 1.0.0
     *
     * @param array|int $job_ids The ID or array of IDs of jobs to delete permanently.
     *
     * @return int|WP_Error The total number of jobs deleted on success, or a WP_Error on failure.
     */  
    public function permanent_delete($job_ids)
    {
        if (is_array($job_ids)) {
            $job_ids = array_map('absint', $job_ids);
        } else {
            $job_ids = [absint($job_ids)];
        }

        try {
            $this->job->query_get_results('START TRANSACTION');

            $total_deleted = 0;
            foreach ($job_ids as $job_id) {
                $deleted = $this->job->delete(
                    [
                        'id' => $job_id,
                    ],
                    [
                        '%d',
                    ]
                );

                if ($deleted) {
                    $total_deleted += intval($deleted);
                }

                /**
                 * Fires after a job has been deleted.
                 *
                 * @since 1.0.0
                 *
                 * @param int $job_id
                 */
                do_action('axilweb_ajl_job_deleted', $job_id);
            }

            $this->job->query_get_results('COMMIT');

            return $total_deleted;
        } catch (\Exception $e) {
            $this->job->query_get_results('ROLLBACK');

            return new \WP_Error('axilweb-ajl-job-delete-error', $e->getMessage());
        }
    }

    /**
     * Retrieve Job Attributes.
     *
     * Fetches the attributes and their values for a specific job by joining the 
     * attributes, attribute values, and job-attribute value mapping tables.
     *
     * @since 1.0.0
     *
     * @param int $job_id The ID of the job to retrieve attributes for.
     *
     * @return array An associative array where the keys are attribute names and the values are their corresponding values.
     */ 
    public function get_job_attributes( $job_id ) 
    {
      global $wpdb;
      // Prepare the SQL query to join the three tables
      $table_attributes = $wpdb->prefix .  'axilweb_ajl_attributes';
      $table_attribute_values = $wpdb->prefix .  'axilweb_ajl_attribute_values';
      $table_job_attribute_value = $wpdb->prefix .  'axilweb_ajl_job_attribute_value';
    
 
      // Set up cache key and group
      $cache_key = 'axilweb_ajl_job_attributes_' . absint($job_id);
      $cache_group = 'axilweb_ajl_job_data';
      
      // Try to get from cache first - proper caching implementation for this direct database query
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
      $results = wp_cache_get($cache_key, $cache_group);
      
      // If not in cache, fetch from database
      if (false === $results) {
          // Query the database using a private method
          $results = $this->_query_get_job_attributes($job_id, $table_attributes, $table_attribute_values, $table_job_attribute_value);
          
          // Cache the results (even if empty)
          wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
      }

      // Initialize an empty array to store the attributes and values
      $attributes = array();

      // Loop through the results and populate the attributes array
      foreach ( $results as $row ) {
          $attributes[ $row['name'] ] = $row['value'];
      }

      return $attributes;
  }
  
  /**
   * Internal method to get job attributes by job ID.
   *
   * This is a specific implementation for querying across multiple custom job tables,
   * which requires a direct database query.
   *
   * @since 1.0.0
   * @param int $job_id The job ID to get attributes for
   * @param string $table_attributes The attributes table name
   * @param string $table_attribute_values The attribute values table name
   * @param string $table_job_attribute_value The job attribute value table name
   * @return array The job attributes data
   */
  private function _query_get_job_attributes($job_id, $table_attributes, $table_attribute_values, $table_job_attribute_value) {
      global $wpdb;
      
      // Create a cache key for job attributes
      $cache_key = 'axilweb_ajl_job_attributes_' . absint($job_id);
      $cache_group = 'axilweb_ajl_jobs';
      
      // Try to get from cache first
      $results = wp_cache_get($cache_key, $cache_group);
      
      // If not in cache, fetch from database
      if (false === $results) {
          // Use %i placeholders for table names and %d for integer values
          // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is necessary for custom table JOIN operations. Proper caching is implemented with wp_cache_get/set above.
          $results = $wpdb->get_results(
              $wpdb->prepare(
                  "SELECT attributes.name, attribute_values.value
                  FROM %i attributes
                  JOIN %i attribute_values ON attributes.id = attribute_values.attribute_id
                  JOIN %i job_attribute_value ON attribute_values.id = job_attribute_value.attribute_value_id
                  WHERE job_attribute_value.job_id = %d",
                  $table_attributes,
                  $table_attribute_values,
                  $table_job_attribute_value,
                  $job_id
              ),
              ARRAY_A
          );
          
          // Cache the results for future requests (1 hour)
          if ($results) {
              wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
          }
      }
      
      return $results;
  }
}
