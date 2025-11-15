<?php

namespace Axilweb\AiJobListing\Manager;

use Axilweb\AiJobListing\Models\Application;
use Axilweb\AiJobListing\Models\Application_Meta;
use Axilweb\AiJobListing\Helpers\Helpers;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

class Application_Manager
{

    /**
     * Job Application class.
     *
     * @var Application Meta
     */
    public $job_applications;
    public $job_application_meta;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->job_applications = new Application();
        $this->job_application_meta = new Application_Meta();
    }

    /**
     * Retrieves a list of job applications with optional filters and pagination.
     *
     * This method retrieves job application records based on various parameters such as page number,
     * number of records per page, ordering, search terms, and more. It also handles counting the records
     * if requested, returning the total count of matching job applications.
     *
     * @param array $args {
     *     Optional. An array of arguments to filter and customize the query.
     *
     *     @type int    $page           The page number for pagination. Default is AXILWEB_AJL_DEFAULT_PAGE.
     *     @type int    $per_page       The number of job applications per page. Default is AXILWEB_AJL_POSTS_PER_PAGE.
     *     @type string $orderby        The field to order by. Default is AXILWEB_AJL_DEFAULT_ORDERBY.
     *     @type string $order          The order direction ('ASC' or 'DESC'). Default is AXILWEB_AJL_DEFAULT_ORDER.
     *     @type string $search         A search term to filter job applications by title or description.
     *     @type string $job_attributes The job attributes to filter by.
     *     @type bool   $count          Whether to return the total count of matching job applications. Default is false.
     *     @type array  $where          Custom SQL WHERE conditions to apply.
     *     @type bool   $is_read        Whether to filter job applications by read status.
     * }
     *
     * @return mixed Returns a list of job applications if `$args['count']` is false, or the total count of job applications (int) if `$args['count']` is true.
     */
    public function all(array $args = [])
    {
        $defaults = [
            'page'              => AXILWEB_AJL_DEFAULT_PAGE,
            'per_page'          => (isset($args['per_page']) && !empty($args['per_page'])) ? $args['per_page'] : AXILWEB_AJL_POSTS_PER_PAGE,
            'orderby'           => (isset($args['orderby']) && !empty($args['orderby'])) ? $args['orderby'] : AXILWEB_AJL_DEFAULT_ORDERBY,
            'order'             => (isset($args['order']) && !empty($args['order'])) ? $args['order'] : AXILWEB_AJL_DEFAULT_ORDER,
            'search'            => '',
            'job_attributes'    => '',
            'count'             => false,
            'where'             => [],
            'is_read'           => null,
        ];

        $args = wp_parse_args($args, $defaults);
        $job_applications = $this->job_applications->job_application_lists($args);

        if ($args['count']) {
            return (int) $job_applications;
        }

        return $job_applications;
    }

    /**
     * Counts the number of job applications based on job ID, process ID, and action status.
     *
     * This method retrieves the count of job applications filtered by the specified job ID, process ID,
     * and action status. The result is useful for determining the number of applications in a specific
     * state or process for a given job.
     *
     * @param int    $job_id      The ID of the job.
     * @param int    $process_id  The ID of the process related to the job application.
     * @param string $action      The action/status to filter applications by (e.g., 'pending', 'approved', etc.).
     *
     * @return int   Returns the count of job applications matching the provided criteria.
     */
    public function count_application_by_status($job_id, $process_id, $action)
    {
        $jobs_count = $this->job_applications->count_application_by_status($job_id, $process_id, $action);
        return $jobs_count;
    }

    /**
     * Creates a new job application and its associated metadata.
     *
     * This method creates a new job application entry in the database and handles the upload of associated files,
     * such as a profile image and resume. Additionally, it stores metadata related to the job application.
     * The function ensures that the entire process is wrapped in a transaction to maintain data integrity.
     *
     * @param array $data      The data for the job application (e.g., job ID, applicant details).
     * @param array $request   The request object containing application metadata and files.
     * 
     * @return int|\WP_Error   Returns the job application ID if successful, or a WP_Error object on failure.
     */
    public function create($data, $request)
    {
        try {
            $this->job_applications->query_get_results('START TRANSACTION');

            // Create job application now.
            $job_applications_id = $this->job_applications->create(
                $data,
                [
                    '%s',
                    '%s',
                ]
            );
            /**
             * Fires after a job application has been created.
             *
             * @since 1.0.0
             *
             * @param int   $job_applications_id
             * @param array $data
             */
            do_action('axilweb_ajl_job_applications_created', $job_applications_id, $data);
            $process_meta = Helpers::prepare_app_meta_for_insert($request['app_meta']);

            // Create job application meta
            axilweb_ajl_jobs()->job_application_meta_manager->create($job_applications_id, $process_meta['process_meta']);

            // Get the files from the request
            $files = $request->get_file_params();

            // Upload profile image
            if (isset($files['app_meta']['name'][$process_meta['profile_image_key']]) && !empty($files['app_meta']['name'][$process_meta['profile_image_key']])) {
                $profile_image = Helpers::prepare_attachment_for_upload($files['app_meta'], $process_meta['profile_image_key']);
                // Create attachment for job application meta
                $profile_image_id = axilweb_ajl_jobs()->job_application_meta_manager->upload_attachment($job_applications_id, $profile_image, 'profile_image');

                $profile_image_meta[] = array(
                    'app_mk' => 'profile_image', 
                    'app_mv' => $profile_image_id
                );
                axilweb_ajl_jobs()->job_application_meta_manager->create($job_applications_id, $profile_image_meta);
            }

            // Upload resume pdf
            if (isset($files['app_meta']['name'][$process_meta['resume_key']]) && !empty($files['app_meta']['name'][$process_meta['resume_key']])) {
                $resume = Helpers::prepare_attachment_for_upload($files['app_meta'], $process_meta['resume_key']);
                // Create attachment for job application meta
                $resume_id = axilweb_ajl_jobs()->job_application_meta_manager->upload_attachment($job_applications_id, $resume, 'resume');
                $resume_meta[] = array(
                    'app_mk' => 'resume', 
                    'app_mv' => $resume_id
                );
                axilweb_ajl_jobs()->job_application_meta_manager->create($job_applications_id, $resume_meta);
            }

            $this->job_applications->query_get_results('COMMIT');
            return $job_applications_id;
        } catch (\Exception $e) {
            $this->job_applications->query_get_results('ROLLBACK');

            return new \WP_Error('axilweb-ajl-job-application-create-failed', $e->getMessage());
        }
    }

    /**
     * Delete Job Applications.
     *
     * Deletes one or more job applications by marking them as deleted 
     * (soft delete) or restores them based on the specified action.
     *
     * @since 1.0.0
     *
     * @param array|int $job_applications_ids The ID or array of IDs of job applications to delete.
     * @param string    $action              The action to perform. Default is "soft_delete".
     *                                        Accepted values: "soft_delete", "restore".
     *
     * @return int|WP_Error The number of job applications deleted/restored on success, or a WP_Error on failure.
     */
    public function delete($job_applications_ids, $action = "soft_delete")
    {
        global $wpdb;

        try {
            $this->job_applications->query_get_results('START TRANSACTION');

            $job_applications_ids = is_array($job_applications_ids)
                ? array_map('absint', $job_applications_ids)
                : [absint($job_applications_ids)];

            $applications_id_placeholders = implode(', ', array_fill(0, count($job_applications_ids), '%d'));
            $deleted_by = get_current_user_id();
            $deleted_at = ($action == 'restore') ? 'NULL' : "'" . current_datetime()->format('Y-m-d H:i:s') . "'";

            $table_applications = $wpdb->prefix .  'axilweb_ajl_applications'; 
            $prepare_values = array_merge([$deleted_by], $job_applications_ids); 
            // Set up cache key for this operation
            $cache_key = 'axilweb_ajl_applications_' . $action . '_' . md5(serialize($job_applications_ids));
            $cache_group = 'axilweb_ajl_applications_operations';
            
            // Try to get from cache first
            $total_deleted = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, perform the database operation
            if (false === $total_deleted) {
                // Execute the database operation using a private method
                $total_deleted = $this->_query_delete_or_restore_application(
                    $table_applications,
                    $deleted_at,
                    $prepare_values,
                    $applications_id_placeholders
                );
                
                // Cache the result
                wp_cache_set($cache_key, $total_deleted, $cache_group, HOUR_IN_SECONDS);
            }
            
            // Invalidate related caches
            foreach ($job_applications_ids as $id) {
                wp_cache_delete('axilweb_ajl_application_' . $id, 'axilweb_ajl');
            }

            $this->job_applications->query_get_results('COMMIT');
            return $total_deleted;
        } catch (\Exception $e) {
            $this->job_applications->query_get_results('ROLLBACK');
            return new \WP_Error('axilweb_ajl_application_delete_error', $e->getMessage());
        }
    }
    
    /**
     * Internal method to delete or restore applications in the database.
     *
     * This is a specific implementation for bulk deletion/restoration of applications,
     * which requires a direct database query.
     *
     * @since 1.0.0
     * @param string $table_applications The applications table name
     * @param string $deleted_at The deleted_at value (either 'NULL' or a datetime string)
     * @param array $prepare_values Values for the prepared statement
     * @return int|bool Number of rows affected or false on error
     */
    private function _query_delete_or_restore_application($table_applications, $deleted_at, $prepare_values) {
        global $wpdb;
    
        // Create cache key for invalidation when applications are modified
        $cache_key_prefix = 'axilweb_ajl_application_';
        $cache_group = 'axilweb_ajl_applications';
    
        // Before performing the update, clear any related caches
        if (!empty($prepare_values) && is_array($prepare_values)) {
            foreach ($prepare_values as $app_id) {
                if (is_numeric($app_id)) {
                    wp_cache_delete($cache_key_prefix . absint($app_id), $cache_group);
                }
            }
            wp_cache_delete('axilweb_ajl_applications_list', $cache_group);
        }
    
        // Safely escape the table name
        $table = esc_sql($table_applications);
    
        // Total affected rows
        $total_affected = 0;
    
        // Process each ID individually (instead of IN clause, we'll loop through each ID)
        foreach ($prepare_values as $app_id) {
            // Prepare data and WHERE clause
            $data = array(
                'deleted_by' => get_current_user_id(),
            );
    
            // If the `deleted_at` value is 'NULL' (restoring), set it as NULL in the data array
            if ($deleted_at === 'NULL') {
                $data['deleted_at'] = null;
            } else {
                // If `deleted_at` is a timestamp, use it
                $data['deleted_at'] = $deleted_at;
            }
    
            // Where condition
            $where = array(
                'id' => absint($app_id),  // Sanitize ID
            );
    
            // Format for `deleted_at` and `deleted_by` data
            $format = array('%s', '%d'); // %s for `deleted_at`, %d for `deleted_by`
            $where_format = array('%d'); // %d for `id`
    
            // Use $wpdb->update() to update each application
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table operation with cache invalidation implemented above
            $result = $wpdb->update(
                $table,  // Table name
                $data,   // Data to update
                $where,  // Where condition (id)
                $format, // Format for the $data values
                $where_format // Format for the $where values
            );
    
            // Check if the row was updated successfully
            if ($result !== false) {
                $total_affected += $result;
            }
        }
    
        return $total_affected;
    }
    

}
