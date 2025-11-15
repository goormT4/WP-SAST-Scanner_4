<?php
namespace Axilweb\AiJobListing\Manager; 
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Job_Attribute_Value;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Job_Attribute_Value_Manager 
{

    /**
     * jobs attribute value class.
     *
     * @var job_attribute_meta
     */
    public $job_attribute_value;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->job_attribute_value = new Job_Attribute_Value();
    }

    /**
     * Get all jobs attribute value by criteria.
     *
     * @since 1.0.0
     *
     * @param array $args
     * @return array|object|string|int
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
    
        // Add search condition
        if (!empty($args['search'])) {
            $like = '%' . sanitize_text_field(wp_unslash($args['search'])) . '%';
            $where_clauses['title'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
            $where_clauses['description'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
        }
    
        // Assign the structured `where` block to args
        $args['where'] = $where_clauses;
    
        // Call the Queryable `all` method
        $jobs = $this->job_attribute_value->all($args);
    
        // Return count if requested
        if ($args['count']) {
            return (int) $jobs;
        }
    
        return $jobs;
    }
    

    /**
     * Get single jobs attribute value by id|slug.
     *
     * @since 1.0.0
     *
     * @param array $args
     * @return array|object|null
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

        return $this->job_attribute_value->get_by($args['key'], $args['value']);
    }

    /**
     * Create a new jobs attribute value.
     *
     * @since 1.0.0
     *
     * @param array $data
     *
     * @return int | WP_Error $id
     */
    public function create($data)
    {
        // Prepare job data for database-insertion.
        $job_data = $this->job_attribute_value->prepare_for_database($data);

        // Create job now.
        $job_id = $this->job_attribute_value->create(
            $job_data,
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
            ]
        );

        if (!$job_id) {
            return new \WP_Error('axilweb-ajl-attribute-value-create-failed', __('Failed to create Job Attribute Value.', 'ai-job-listing'));
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
     * Update jobs attribute value.
     *
     * @since 1.0.0
     *
     * @param array $data
     * @param int   $job_id
     *
     * @return int | WP_Error $id
     */
    public function update(array $data, int $job_id)
    {
        // Prepare job data for database-insertion.
        $job_data = $this->job_attribute_value->prepare_for_database($data);

        // Update job.
        $updated = $this->job_attribute_value->update(
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
            return new \WP_Error('axilweb-ajl-job-attribute-value-update-failed', __('Failed to update Job Attribute Value.', 'ai-job-listing'));
        }

        if ($updated >= 0) {
            /**
             * Fires after a jobs attribute value is being updated.
             *
             * @since 1.0.0
             *
             * @param int   $job_id
             * @param array $job_data
             */
            do_action('axilweb_ajl_jobs_updated', $job_id, $job_data);

            return $job_id;
        }

        return new \WP_Error('axilweb-ajl-job-attribute-value-update-failed', __('Failed To Update The Job Attribute Value.', 'ai-job-listing'));
    }

   
    public function delete($job_ids, $action = "soft_delete")
   {
    if (!is_array($job_ids)) {
        $job_ids = [absint($job_ids)];
    } else {
        $job_ids = array_map('absint', $job_ids);
    }

    try {
        $this->job_attribute_value->query_get_results('START TRANSACTION');

        $total_deleted = 0;
        foreach ($job_ids as $job_id) {
            $deleted = $this->job_attribute_value->update(
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
             * Fires after a job attribute value has been deleted or restored.
             *
             * @since 1.0.0
             *
             * @param int $job_id
             */
            do_action('axilweb_ajl_job_attribute_value_deleted', $job_id);
        }

        $this->job_attribute_value->query_get_results('COMMIT');

        return $total_deleted;
    } catch (\Exception $e) {
        $this->job_attribute_value->query_get_results('ROLLBACK');
        return new \WP_Error('axilweb-ajl-job-attribute-value-delete-error', $e->getMessage());
    }
}


}
