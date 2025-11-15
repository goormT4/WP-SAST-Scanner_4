<?php 
namespace Axilweb\AiJobListing\Manager;
use Axilweb\AiJobListing\Models\App_Process_Comment;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class App_Process_Comment_Manager
{

    /**
     * app_process_comment value class.
     *
     * @var app_process_comment
     */
    public $app_process_comment;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->app_process_comment = new App_Process_Comment();
    }

    /**
     * Retrieves a list of items with the ability to filter, paginate, and sort the results.
     *
     * This method fetches items from the database based on the provided parameters. It supports pagination,
     * searching, ordering, and filtering. The results can either be a list of items or the count of items,
     * depending on the `count` argument.
     *
     * @param array $args Optional arguments to filter and paginate the results:
     * - 'page'      (int)   The page number (default is 1).
     * - 'per_page'  (int)   The number of items per page (default is 10).
     * - 'orderby'   (string) The field to order by (default is 'id').
     * - 'order'     (string) The sorting direction (default is 'DESC').
     * - 'search'    (string) The search term to filter results by (default is empty).
     * - 'count'     (bool)   Whether to return the count of items instead of the results (default is false).
     * - 'where'     (array)  Custom SQL conditions to apply (default is an empty array).
     *
     * @return mixed Returns either the list of items (array) or the count (integer) depending on the 'count' parameter.
     *               If 'count' is true, it returns the total number of matching items.
     *               Otherwise, it returns an array of processed items.
     */ 
    public function all(array $args = [])
    {
        // Default arguments
        $defaults = [
            'page'     => 1,
            'per_page' => 10,
            'orderby'  => 'id',
            'order'    => 'DESC',
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        // Merge provided arguments with defaults
        $args = wp_parse_args($args, $defaults);

        // Prepare the `where` block as an array for Queryable
        $where_clauses = [];

        // Handle search filtering
        if (!empty($args['search'])) {
            global $wpdb;
            $like = '%' . $wpdb->esc_like(sanitize_text_field(wp_unslash($args['search']))) . '%';
            $where_clauses['title'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
            $where_clauses['description'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
        }

        // Assign the `where` clauses to args
        $args['where'] = $where_clauses;

        // Call the Queryable `all` method
        $process_comment = $this->app_process_comment->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $process_comment;
        }

        return $process_comment;
    }


    /**
     * Retrieves a single item based on a key-value pair.
     *
     * This method fetches a single item from the database by using a specified key and value.
     * It utilizes the `app_process_comment->get_by()` method to query the database.
     * If no value is provided, the function returns `null`.
     *
     * @param array $args Optional arguments to filter the item by:
     * - 'key'   (string) The column/key to search by (default is 'id').
     * - 'value' (mixed)  The value to search for (default is empty).
     *
     * @return mixed Returns the item if found, or `null` if no value is provided or no matching item is found.
     *               The return value is determined by the `get_by()` method from `app_process_comment`.
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

        return $this->app_process_comment->get_by($args['key'], $args['value']);
    }

    /**
     * Creates a new process comment and inserts it into the database.
     *
     * This method prepares the data for a new process comment and then inserts it into the database
     * using the `App_Process_Comment` class's `create` method. If the creation is successful, the
     * newly created process comment's ID is returned. Otherwise, it returns a `WP_Error` indicating
     * the failure.
     *
     * @param array $data The data to be used for creating the process comment. The data will be 
     *                    processed and sanitized before being inserted into the database.
     *
     * @return int|\WP_Error Returns the ID of the newly created process comment on success,
     *                       or a `WP_Error` if the creation failed.
     */ 
    public function create($data)
    {
        // Prepare job data for database-insertion.
        $process_comment_data = $this->app_process_comment->prepare_data_for_database($data);

        $process_comment_id = (new App_Process_Comment)->create(
            $process_comment_data,
            [
                '%d',
                '%d',
                '%d',
                '%s',
                '%s',
                 
            ]
        );

        if (!$process_comment_id) {
            return new \WP_Error('axilweb-ajl-process-comment-create-failed', __('Failed to create process comment.', 'ai-job-listing'));
        }

        /**
         * Fires after a job has been created.
         *
         * @since 1.0.0
         *
         * @param int   $job_id
         * @param array $process_comment_data
         */
        do_action('axilweb-ajl-job_listing_jobs_created', $process_comment_id, $process_comment_data);

        return $process_comment_id;
    }

    /**
     * Updates an existing process comment in the database.
     *
     * This method takes the provided data and updates the existing process comment with the given ID.
     * If the update is successful, the ID of the updated process comment is returned. If the update fails,
     * a `WP_Error` is returned with an error message indicating the failure reason.
     *
     * @param array $data The data to be used for updating the process comment. The data will be processed
     *                    and sanitized before being applied to the database.
     * @param int $process_comment_id The ID of the process comment to be updated.
     *
     * @return int|\WP_Error Returns the ID of the updated process comment on success, or a `WP_Error` if the
     *                       update failed.
     */ 
    public function update(array $data, int $process_comment_id)
    {
        // Prepare job data for database-insertion.
        $process_comment_data = $this->app_process_comment->prepare_for_database($data);

        // Update job.
        $updated = $this->app_process_comment->update(
            $process_comment_data,
            [
                'id' => $process_comment_id,
            ],
            [
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
            return new \WP_Error('axilweb-ajl-process-comment-update-failed', __('Failed to update process comment.', 'ai-job-listing'));
        }

        if ($updated >= 0) {
            /**
             * Fires after a job is being updated.
             *
             * @since 1.0.0
             *
             * @param int   $job_id
             * @param array $process_comment_data
             */
            do_action('axilweb_ajl_job_listing_jobs_updated', $process_comment_id, $process_comment_data);

            return $process_comment_id;
        }

        return new \WP_Error('axilweb-ajl-process-comment-update-failed', __('Failed to update the process comment.', 'ai-job-listing'));
    }
}
