<?php 
namespace Axilweb\AiJobListing\Manager; 
use Axilweb\AiJobListing\Models\General_Setting;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class General_Settings_Manager 
{

    /**
     * General_Settings class.
     *
     * @var General_Settings
     */
    public $general_settings;
    protected $per_page = 50;
    protected $page = 1;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->general_settings = new General_Setting();
    }

    /**
     * Retrieve General Settings.
     *
     * Fetches a list of general settings based on the provided query arguments. 
     * Supports pagination, sorting, searching, and filtering. If the `count` argument 
     * is set to true, it returns the total number of general settings instead of the list.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to query general settings. Default empty array.
     *
     *     @type int    $page     The current page number. Default is 1.
     *     @type int    $per_page The number of general settings per page. Default is 50.
     *     @type string $orderby  The column to sort by. Default is 'id'.
     *     @type string $order    The sort direction. Accepts 'ASC' or 'DESC'. Default is 'DESC'.
     *     @type string $search   Search term to filter general settings by `title` or `description`. Default is empty.
     *     @type bool   $count    Whether to return only the total count of general settings. Default is false.
     *     @type array  $where    Additional conditions to filter general settings. Default is empty.
     * }
     *
     * @return array|int List of general settings on success, or the total count if `count` is true.
     */ 
    public function all(array $args = [])
    {
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

        // Merge provided arguments with defaults
        $args = wp_parse_args($args, $defaults);

        // Prepare the `where` block as an array for Queryable
        $where_clauses = [];

        // Add search filter
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
        $general_settings = $this->general_settings->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $general_settings;
        }

        return $general_settings;
    }


    /**
     * Retrieve a Single General Setting.
     *
     * Fetches a specific general setting based on the provided key-value pair. 
     * The `key` specifies the database column to search, and the `value` is the value to match.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to retrieve a specific general setting. Default empty array.
     *
     *     @type string $key   The database column to search by. Default is 'id'.
     *     @type mixed  $value The value to match in the specified column. Default is an empty string.
     * }
     *
     * @return array|null The general setting data as an associative array on success, or null if not found.
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

        return $this->general_settings->get_by($args['key'], $args['value']);
    }

    /**
     * Create General Setting.
     *
     * Inserts a new general setting into the database with the provided data. 
     * Fires a custom action hook after the general setting is successfully created.
     *
     * @since 1.0.0
     *
     * @param array $data The data to insert for the general setting.
     *                    Keys should match the database column names, and the order 
     *                    must align with the provided placeholders.
     *
     * @return int|WP_Error The ID of the newly created general setting on success, or a WP_Error on failure.
     */ 
    public function create($data)
    {

        // Create General_Settings now.
        $general_setting_id = $this->general_settings->create(
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

        if (!$general_setting_id) {
            return new \WP_Error('axilweb-ajl-general-setting-create-failed', __('Failed to create General_Settings.', 'ai-job-listing'));
        }
        
        /**
         * Fires after a job has been created.
         *
         * @since 1.0.0
         *
         * @param int   $general_setting_id
         * @param array $General_Setting_data
         */
        do_action('axilweb_ajl_general_setting_created', $general_setting_id, $data);

        return $general_setting_id;
    }

   /**
     * Update General Setting.
     *
     * Updates a specific general setting in the database with the provided data. 
     * Fires a custom action hook after the general setting is successfully updated.
     *
     * @since 1.0.0
     *
     * @param array $data                The data to update for the general setting.
     *                                    Keys should match the database column names.
     * @param int   $general_setting_id  The ID of the general setting to update.
     *
     * @return int|WP_Error The ID of the updated general setting on success, or a WP_Error on failure.
     */ 
    public function update(array $data, int $general_setting_id)
    {

        $updated = $this->general_settings->update(
            $data,
            [
                'id' => $general_setting_id,
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
            return new \WP_Error('axilweb-ajl-general-setting-update-failed from Manager', __('Failed to update Settings.', 'ai-job-listing'));
        }

        if ($updated >= 0) {
            /**
             * Fires after a job is being updated.
             *
             * @since 1.0.0
             *
             * @param int   $general_setting_id
             * @param array $General_Setting_data
             */
            do_action('axilweb_ajl_general_setting_updated', $general_setting_id);

            return $general_setting_id;
        }

        return new \WP_Error('axilweb-ajl-general-setting-update-failed', __('Failed to update the Settings.', 'ai-job-listing'));
    }

    /**
     * Delete or Restore General Settings.
     *
     * Soft deletes or restores one or more general settings based on the specified action. 
     * Updates the `deleted_at` and `deleted_by` fields in the database. Fires a custom 
     * action hook after each general setting is processed.
     *
     * @since 1.0.0
     *
     * @param array|int $general_setting_ids The ID or array of IDs of general settings to delete or restore.
     * @param string    $action              The action to perform. Default is "soft_delete".
     *                                        Accepted values: "soft_delete", "restore".
     *
     * @return int|WP_Error The total number of general settings processed on success, or a WP_Error on failure.
     */ 
    public function delete($general_setting_ids, $action = "soft_delete")
    {

        if (is_array($general_setting_ids)) {
            $general_setting_ids = array_map('absint', $general_setting_ids);
        } else {
            $general_setting_ids = [absint($general_setting_ids)];
        }

        try {
            $this->general_settings->query_get_results('START TRANSACTION');

            $total_deleted = 0;
            foreach ($general_setting_ids as $general_setting_id) {
                $deleted = $this->general_settings->update(
                    [
                        'deleted_at' => ($action == 'restore') ? NULL : current_datetime()->format('Y-m-d H:i:s'),
                        'deleted_by' => get_current_user_id(),
                    ],
                    [
                        'id' => $general_setting_id,
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
                 * @param int $general_setting_id
                 */
                do_action('axilweb_ajl_general_setting_deleted', $general_setting_id);
            }

            $this->general_settings->query_get_results('COMMIT');

            return $total_deleted;
        } catch (\Exception $e) {
            $this->general_settings->query_get_results('ROLLBACK');

            return new \WP_Error('axilweb-ajl-general-setting-delete-error', $e->getMessage());
        }
    }
 

    /**
     * Permanently Delete General Settings.
     *
     * Deletes one or more general settings from the database permanently. 
     * Fires a custom action hook after each general setting is successfully deleted.
     *
     * @since 1.0.0
     *
     * @param array|int $general_setting_ids The ID or array of IDs of general settings to delete.
     *
     * @return int|WP_Error The total number of general settings deleted on success, or a WP_Error on failure.
     */ 
    public function permanent_delete($general_setting_ids)
    {

        if (is_array($general_setting_ids)) {
            $general_setting_ids = array_map('absint', $general_setting_ids);
        } else {
            $general_setting_ids = [absint($general_setting_ids)];
        }

        try {
            $this->general_settings->query_get_results('START TRANSACTION');

            $total_deleted = 0;
            foreach ($general_setting_ids as $general_setting_id) {
                $deleted = $this->general_settings->delete(
                    [
                        'id' => $general_setting_id,
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
                 * @param int $general_setting_id
                 */
                do_action('axilweb_ajl_general_setting_deleted', $general_setting_id);
            }

            $this->general_settings->query_get_results('COMMIT');

            return $total_deleted;
        } catch (\Exception $e) {
            $this->general_settings->query_get_results('ROLLBACK');

            return new \WP_Error('axilweb-ajl-general-setting-delete-error', $e->getMessage());
        }
    }
}
