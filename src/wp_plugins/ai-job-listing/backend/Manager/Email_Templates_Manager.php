<?php 
namespace Axilweb\AiJobListing\Manager; 
use Axilweb\AiJobListing\Models\Email_Template;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Email_Templates_Manager 
{

    /**
     * Email_Templatess class.
     *
     * @var Email_Templates
     */
    public $email_templates;
    protected $per_page = 50;
    protected $page = 1;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->email_templates = new Email_Template();
    }

    /**
     * Retrieve Email Templates.
     *
     * Fetches a list of email templates based on the specified query arguments.
     * Supports pagination, sorting, searching, and filtering. If the `count` 
     * argument is set to true, it returns the total number of email templates instead of the list.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to query email templates. Default empty array.
     *
     *     @type int    $page     The current page number. Default is 1.
     *     @type int    $per_page The number of email templates per page. Default is 50.
     *     @type string $orderby  The column to sort by. Default is 'id'.
     *     @type string $order    The sort direction. Accepts 'ASC' or 'DESC'. Default is 'DESC'.
     *     @type string $search   Search term to filter email templates by `title` or `description`. Default is empty.
     *     @type bool   $count    Whether to return only the total count of email templates. Default is false.
     *     @type array  $where    Additional conditions to filter email templates. Default is empty.
     * }
     *
     * @return array|int List of email templates on success, or the total count if `count` is true.
     */ 
    public function all(array $args = [])
    {
        global $wpdb;

        // Default arguments
        $defaults = [
            'page'     => 1,
            'per_page' => 50,
            'orderby'  => 'id',
            'order'    => 'DESC',
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        // Merge with defaults
        $args = wp_parse_args($args, $defaults);

        // Prepare the `where` array for Queryable
        $where_clauses = [];

        // Handle search filter
        if (!empty($args['search'])) {
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

        // Assign prepared `where` clauses back to args
        $args['where'] = $where_clauses;

        // Call Queryable's `all` method
        $email_templates = $this->email_templates->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $email_templates;
        }

        return $email_templates;
    }


    /**
     * Retrieve a Single Email Template.
     *
     * Fetches a specific email template based on the provided key-value pair. 
     * The `key` specifies the database column to search, and the `value` is the value to match.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to retrieve a specific email template. Default empty array.
     *
     *     @type string $key   The database column to search by. Default is 'id'.
     *     @type mixed  $value The value to match in the specified column. Default is an empty string.
     * }
     *
     * @return array|null The email template data as an associative array on success, or null if not found.
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

        return $this->email_templates->get_by($args['key'], $args['value']);
    }

   /**
     * Create Email Template.
     *
     * Inserts a new email template into the database with the provided data. 
     * Fires a custom action hook after the email template is successfully created.
     *
     * @since 1.0.0
     *
     * @param array $data The data to insert for the email template.
     *                    Keys should match the database column names, and the order 
     *                    must align with the placeholders provided.
     *
     * @return int|WP_Error The ID of the newly created email template on success, or a WP_Error on failure.
     */ 
    public function create($data)
    {

        // Create Email_Templatess now.
        $email_templates_id = $this->email_templates->create(
            $data,
            [
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%s',
                '%d',
                '%s',
            ]
        );

        if (!$email_templates_id) {
            return new \WP_Error('axilweb-ajl-email-templates-create-failed', __('Failed to create Email_Templatess.', 'ai-job-listing'));
        }
        
        /**
         * Fires after a job has been created.
         *
         * @since 1.0.0
         *
         * @param int   $email_templates_id
         * @param array $Email_Templates_data
         */
        do_action('axilweb_ajl_email_templates_created', $email_templates_id, $data);

        return $email_templates_id;
    }

    /**
     * Update Email Templates.
     *
     * Updates the specified email template in the database with the provided data. 
     * Fires a custom action hook after the email template is successfully updated.
     *
     * @since 1.0.0
     *
     * @param array $data               The data to update for the email template.
     *                                   Keys should match the database column names.
     * @param int   $email_templates_id The ID of the email template to update.
     *
     * @return int|WP_Error The ID of the updated email template on success, or a WP_Error on failure.
     */ 
    public function update(array $data, int $email_templates_id)
    {

        $updated = $this->email_templates->update(
            $data,
            [
                'id' => $email_templates_id,
            ],

            [
                '%s', # value
                '%s', # slug
                '%d', # attribute_id
                '%s', # menu_orderby
                '%s', # is_active
                '%s', # updated_at
                '%d', # updated_by

            ],
            [
                '%d',
            ]
        );

        if (!$updated) {
            return new \WP_Error('axilweb-ajl-email-templates-update-failed from Manager', __('Failed to update the Email_Templates.', 'ai-job-listing'));
        }

        if ($updated >= 0) {
            /**
             * Fires after a job is being updated.
             *
             * @since 1.0.0
             *
             * @param int   $email_templates_id
             * @param array $Email_Templates_data
             */
            do_action('axilweb_ajl_email_templates_updated', $email_templates_id);

            return $email_templates_id;
        }

        return new \WP_Error('axilweb-ajl-email-templates-update-failed', __('Failed to update the Email_Templatess.', 'ai-job-listing'));
    }

    /**
     * Delete Email Templates.
     *
     * Deletes (soft delete) or restores one or more email templates based on the specified action.
     * Updates the `deleted_at` and `deleted_by` fields in the database. Fires a custom action hook 
     * after each email template is processed.
     *
     * @since 1.0.0
     *
     * @param array|int $email_templates_ids The ID or array of IDs of email templates to delete or restore.
     * @param string    $action              The action to perform. Default is "soft_delete".
     *                                        Accepted values: "soft_delete", "restore".
     *
     * @return int|WP_Error The total number of email templates processed on success, or a WP_Error on failure.
     */ 
    public function delete($email_templates_ids, $action = "soft_delete")
    {

        if (is_array($email_templates_ids)) {
            $email_templates_ids = array_map('absint', $email_templates_ids);
        } else {
            $email_templates_ids = [absint($email_templates_ids)];
        }

        try {
            $this->email_templates->query_get_results('START TRANSACTION');

            $total_deleted = 0;
            foreach ($email_templates_ids as $email_templates_id) {
                $deleted = $this->email_templates->update(
                    [
                        'deleted_at' => ($action == 'restore') ? NULL : current_datetime()->format('Y-m-d H:i:s'),
                        'deleted_by' => get_current_user_id(),
                    ],
                    [
                        'id' => $email_templates_id,
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
                 * Fires after a job has been deleted.
                 *
                 * @since 1.0.0
                 *
                 * @param int $email_templates_id
                 */
                do_action('ai_job_listing_email_templates_deleted', $email_templates_id);
            }

            $this->email_templates->query_get_results('COMMIT');

            return $total_deleted;
        } catch (\Exception $e) {
            $this->email_templates->query_get_results('ROLLBACK');

            return new \WP_Error('ai-job-listing-general-setting-delete-error', $e->getMessage());
        }
    }
 

    /**
     * Permanently Delete Email Templates.
     *
     * Deletes one or more email templates from the database permanently. 
     * Fires a custom action hook after each email template is successfully deleted.
     *
     * @since 1.0.0
     *
     * @param array|int $email_templates_ids The ID or array of IDs of email templates to delete.
     *
     * @return int|WP_Error The total number of email templates deleted on success, or a WP_Error on failure.
     */ 
    public function permanent_delete($email_templates_ids)
    {

        if (is_array($email_templates_ids)) {
            $email_templates_ids = array_map('absint', $email_templates_ids);
        } else {
            $email_templates_ids = [absint($email_templates_ids)];
        }

        try {
            $this->email_templates->query_get_results('START TRANSACTION');

            $total_deleted = 0;
            foreach ($email_templates_ids as $email_templates_id) {
                $deleted = $this->email_templates->delete(
                    [
                        'id' => $email_templates_id,
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
                 * @param int $email_templates_id
                 */
                do_action('ai_job_listing_email_templates_deleted', $email_templates_id);
            }

        $this->email_templates->query_get_results('COMMIT'); 
        return $total_deleted;
        } catch (\Exception $e) {
            $this->email_templates->query_get_results('ROLLBACK'); 
            return new \WP_Error('ai-job-listing-general-setting-delete-error', $e->getMessage());
        }
    }
}
