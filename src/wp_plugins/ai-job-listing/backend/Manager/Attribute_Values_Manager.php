<?php

namespace Axilweb\AiJobListing\Manager;

use Axilweb\AiJobListing\Models\Attribute_Values;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}
class Attribute_Values_Manager
{

    /**
     * Attribute values class.
     *
     * @var attribute_values
     */
    public $attribute_values;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attribute_values = new Attribute_Values();
    }

    /**
     * Retrieves a list of attribute values based on specified query arguments.
     *
     * This method retrieves attribute values from the database with options for pagination, 
     * ordering, filtering by various parameters (like ID, slug, search term, attribute ID, and status).
     * It can return either the full list of results or just the count of matching records.
     *
     * @param array $args {
     *     Optional. Array of arguments to filter and sort the attribute values. 
     *
     *     @type int    $page          The page number to fetch. Default is AXILWEB_AJL_DEFAULT_PAGE.
     *     @type int    $per_page      The number of results per page. Default is AXILWEB_AJL_POSTS_PER_PAGE.
     *     @type string $orderby       The field to order results by. Default is AXILWEB_AJL_DEFAULT_ORDERBY.
     *     @type string $order         The direction to order the results ('ASC' or 'DESC'). Default is AXILWEB_AJL_DEFAULT_ORDER.
     *     @type string $search        The search term to filter by value. Default is an empty string.
     *     @type bool   $count         Whether to return only the count of matching records. Default is false.
     *     @type array  $where         An array of additional conditions to apply to the query. Default is an empty array.
     *     @type int    $id            The ID to filter the results by. Optional.
     *     @type string $slug          The slug to filter the results by. Optional.
     *     @type string $status        The status to filter by ('trash' for deleted records, or NULL for active records). Optional.
     *     @type int    $attribute_id  The attribute ID to filter the results by. Optional.
     * }
     *
     * @return array|int The list of attribute values if $args['count'] is false, 
     *                   or the count of attribute values if $args['count'] is true.
     */ 
    public function all(array $args = [])
    {
        global $wpdb;

        // Default arguments
        $defaults = [
            'page'     => AXILWEB_AJL_DEFAULT_PAGE,
            'per_page' => AXILWEB_AJL_POSTS_PER_PAGE,
            'orderby'  => AXILWEB_AJL_DEFAULT_ORDERBY,
            'order'    => AXILWEB_AJL_DEFAULT_ORDER,
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        // Parse arguments with defaults
        $args = wp_parse_args($args, $defaults);

        $where_clauses = [];

        // Add conditions dynamically for Queryable
        if (!empty($args['id'])) {
            $where_clauses['id'] = [
                'operator' => '=',
                'value'    => absint($args['id']),
            ];
        }

        if (!empty($args['slug'])) {
            $like = '%' . $wpdb->esc_like(sanitize_text_field(wp_unslash($args['slug']))) . '%';
            $where_clauses['slug'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
        }

        if (isset($args['status']) && $args['status'] === 'trash') {
            $args['trash'] = true;
        }

        if (!empty($args['search'])) {
            $args['search_columns'] = ['value'];
        }

        if (!empty($args['attribute_id'])) {
            $where_clauses['attribute_id'] = [
                'operator' => '=',
                'value'    => absint($args['attribute_id']),
            ];
        }

        // Assign the formatted where clauses back to args
        $args['where'] = $where_clauses;

        // Pass arguments to Queryable's all method
        $attribute_values = $this->attribute_values->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $attribute_values;
        }

        return $attribute_values;
    }


    /**
     * Get single job attribute by id|slug.
     *
     * @since 0.3.0
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

        return $this->attribute_values->get_by($args['key'], $args['value']);
    }

    /**
     * Create Attribute Values.
     *
     * Creates a new attribute value record in the database with the provided data. 
     * Fires a custom action hook after the attribute value is successfully created.
     *
     * @since 1.0.0
     *
     * @param array $data The data to insert for the attribute values.
     *                    Keys should match the database column names, and the order 
     *                    must match the placeholders in the query.
     *
     * @return int|WP_Error The ID of the newly created attribute values on success, or a WP_Error on failure.
     */
    public function create($data)
    {

        // Create job now.
        $attribute_values_id = $this->attribute_values->create(
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

        if (!$attribute_values_id) {
            return new \WP_Error('axilweb-ajl-attribute-values-create-failed', __('Failed to create job Attribute values.', 'ai-job-listing'));
        }

        /**
         * Fires after a Attribute values has been created.
         *
         * @since 1.0.0
         *
         * @param int   $attribute_values_id
         * @param array $attribute_values_data
         */
        do_action('axilweb_ajl_attribute_values_created', $attribute_values_id, $data);

        return $attribute_values_id;
    }

    /**
     * Update Attribute Values.
     *
     * Updates the specified attribute values in the database with the provided data. 
     * Fires a custom action hook after the attribute values are successfully updated.
     *
     * @since 1.0.0
     *
     * @param array $data               The data to update for the attribute values.
     *                                   Keys should match the database column names.
     * @param int   $attribute_values_id The ID of the attribute values to update.
     *
     * @return int|WP_Error The ID of the updated attribute values on success, or a WP_Error on failure.
     */
    public function update(array $data, int $attribute_values_id)
    {

        $updated = $this->attribute_values->update(
            $data,
            [
                'id' => $attribute_values_id,
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
                '%d'
            ]
        );

        if (!$updated) {
            return new \WP_Error('axilweb-ajl-attribute-values-update-failed', __('Failed to update Attribute values.', 'ai-job-listing'));
        }

        if ($updated >= 0) {
            /**
             * Fires after a Attribute values is being updated.
             *
             * @since 1.0.0
             *
             * @param int   $attribute_values_id
             * @param array $attribute_values_data
             */
            do_action('axilweb_ajl_attribute_values_updated', $attribute_values_id);

            return $attribute_values_id;
        }

        return new \WP_Error('axilweb-ajl-attribute-values-update-failed', __('Failed to update the Attribute values.', 'ai-job-listing'));
    }

    /**
     * Delete or restore attribute values in the custom table.
     *
     * @param array|int $attribute_values_ids The ID or array of IDs of attribute values to delete/restore.
     * @param string    $action              The action to perform. Default is "soft_delete". Accepted values: "soft_delete", "restore".
     * @return int|WP_Error The number of attribute values deleted/restored on success, or a WP_Error on failure.
     */
    public function delete($attribute_values_ids, $action = "soft_delete")
    {
        global $wpdb;

        // Ensure IDs are an array and sanitized
        if (is_array($attribute_values_ids)) {
            $attribute_values_ids = array_map('absint', $attribute_values_ids);
        } else {
            $attribute_values_ids = [absint($attribute_values_ids)];
        }

        try {
            // Start transaction
            $this->attribute_values->query_get_results('START TRANSACTION');

            // Sanitize the table name
            $table_attribute_values = esc_sql($wpdb->prefix . 'axilweb_ajl_attribute_values');
            $deleted_at = ($action === 'restore') ? null : current_datetime()->format('Y-m-d H:i:s');
            $deleted_by = get_current_user_id();

            // Create cached key for this operation
            $cache_key = 'axilweb_ajl_attribute_values_' . md5(serialize($attribute_values_ids) . $action);
        
            // Placeholder parameter no longer used in refactored methods but kept for method signature compatibility
            $placeholder = '';
        
            // Execute the appropriate query method based on action
            if ($action === "restore") {
                $total_affected = $this->_query_restore_attribute_values(
                    $deleted_by,
                    $attribute_values_ids,
                    $table_attribute_values,
                    $placeholder
                );
            } else {
                $total_affected = $this->_query_soft_delete_attribute_values(
                    $deleted_at,
                    $deleted_by,
                    $attribute_values_ids,
                    $table_attribute_values,
                    $placeholder
                );
            }

            if ($total_affected === false) {
                throw new \Exception('Database query failed during attribute values ' . $action);
            }

            // Invalidate any existing caches for these attribute values
            foreach ($attribute_values_ids as $id) {
                wp_cache_delete('axilweb_ajl_attribute_value_' . $id, 'axilweb_ajl');
            }
            wp_cache_delete($cache_key, 'axilweb_ajl');

            // Action hook after deletion
            do_action('axilweb_ajl_attribute_values_deleted', $attribute_values_ids);

            // Commit transaction
            $this->attribute_values->query_get_results('COMMIT');

            return $total_affected;
        } catch (\Exception $e) {
            // Rollback in case of error
            $this->attribute_values->query_get_results('ROLLBACK');
            return new \WP_Error('axilweb-ajl-attribute-values-delete-error', $e->getMessage());
        }
    }

    /**
     * Permanently Delete Attribute Values.
     *
     * Deletes one or more attribute values from the database permanently. 
     * Fires a custom action hook after the deletion process is complete.
     *
     * @since 1.0.0
     *
     * @param array|int $attribute_values_ids The ID or array of IDs of attribute values to permanently delete.
     *
     * @return int|WP_Error The number of attribute values deleted on success, or a WP_Error on failure.
     */
    public function permanent_delete($attribute_values_ids)
    {
        global $wpdb;

        // Ensure IDs are an array and sanitized
        if (is_array($attribute_values_ids)) {
            $attribute_values_ids = array_map('absint', $attribute_values_ids);
        } else {
            $attribute_values_ids = [absint($attribute_values_ids)];
        }

        try {
            // Start transaction
            $this->attribute_values->query_get_results('START TRANSACTION');

            // Prepare the table name
            $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';

            // Convert array of IDs to a format suitable for SQL IN clause
            $ids_placeholders = implode(',', array_fill(0, count($attribute_values_ids), '%d'));

 
            // Set up cache key for this operation
            $cache_key = 'axilweb_ajl_attribute_values_delete_' . md5(serialize($attribute_values_ids));
            $cache_group = 'axilweb_ajl_attribute_values_operations';
            
            // Check if operation result is cached
            $total_deleted = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, perform the database operation
            if (false === $total_deleted) {
                // Execute the delete query using a separate method
                $total_deleted = $this->_query_delete_attribute_values($attribute_values_ids, $table_attribute_values, $ids_placeholders);
                
                // Cache the operation result
                wp_cache_set($cache_key, $total_deleted, $cache_group, HOUR_IN_SECONDS);
            }
            
            // Invalidate related caches
            foreach ($attribute_values_ids as $id) {
                wp_cache_delete('axilweb_ajl_attribute_value_' . $id, 'axilweb_ajl');
            }

            // Action hook after deletion
            do_action('axilweb_ajl_attribute_values_deleted', $attribute_values_ids);

            // Commit transaction
            $this->attribute_values->query_get_results('COMMIT');

            return $total_deleted;
        } catch (\Exception $e) {
            // Rollback in case of error
            $this->attribute_values->query_get_results('ROLLBACK');
            return new \WP_Error('axilweb-ajl-attribute-values-delete-error', $e->getMessage());
        }
    }

    /**
     * Restore Attribute Values.
     *
     * Restores one or more attribute values by setting the `deleted_at` field to NULL 
     * and updating the `deleted_by` field in the database. Fires a custom action hook 
     * for each restored attribute value.
     *
     * @since 1.0.0
     *
     * @param array|int $attribute_values_ids The ID or array of IDs of attribute values to restore.
     *
     * @return int|WP_Error The number of attribute values restored on success, or a WP_Error on failure.
     */
    public function restore($attribute_values_ids)
    {
        global $wpdb;

        // Ensure IDs are an array and sanitized
        if (is_array($attribute_values_ids)) {
            $attribute_values_ids = array_map('absint', $attribute_values_ids);
        } else {
            $attribute_values_ids = [absint($attribute_values_ids)];
        }

        try {
            // Start transaction
            $this->attribute_values->query_get_results('START TRANSACTION');

            // Prepare table name
            $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';

            // Prepare the necessary fields for update
            $deleted_by = get_current_user_id();

            // Convert array of IDs to a format suitable for SQL IN clause
            $ids_placeholders = implode(',', array_fill(0, count($attribute_values_ids), '%d'));

 
            // Set up cache key for this operation
            $cache_key = 'axilweb_ajl_attribute_values_restore_' . md5(serialize($attribute_values_ids));
            $cache_group = 'axilweb_ajl_attribute_values_operations';
            
            // Check if operation result is cached
            $total_restored = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, perform the database operation
            if (false === $total_restored) {
                // Execute the restore query using a separate method
                $total_restored = $this->_query_restore_attribute_values($deleted_by, $attribute_values_ids, $table_attribute_values, $ids_placeholders);
                
                // Cache the operation result
                wp_cache_set($cache_key, $total_restored, $cache_group, HOUR_IN_SECONDS);
            }
            
            // Invalidate related caches
            foreach ($attribute_values_ids as $id) {
                wp_cache_delete('axilweb_ajl_attribute_value_' . $id, 'axilweb_ajl');
            }

            // Fire the action hook for each restored attribute value
            foreach ($attribute_values_ids as $attribute_values_id) {
                do_action('axilweb_ajl_attribute_values_restored', $attribute_values_id);
            }

            // Commit transaction
            $this->attribute_values->query_get_results('COMMIT');

            return $total_restored;
        } catch (\Exception $e) {
            // Rollback in case of error
            $this->attribute_values->query_get_results('ROLLBACK');
            return new \WP_Error('axilweb-ajl-attribute-values-restore-error', $e->getMessage());
        }
    }
    
    /**
     * Internal method to delete attribute values from the database.
     *
     * @since 1.0.0
     * @param array $attribute_values_ids Array of attribute value IDs to delete
     * @param string $table_attribute_values Table name for attribute values
     * @param string $ids_placeholders SQL placeholders for the ID list (not used in refactored version)
     * @return int|bool Number of rows affected or false on error
     */
    private function _query_delete_attribute_values($attribute_values_ids, $table_attribute_values, $ids_placeholders) {
        global $wpdb;
        
        $cache_key_prefix = 'axilweb_ajl_attr_value_';
        $cache_group = 'axilweb_ajl_attributes';
        
        // Invalidate caches for each attribute value
        foreach ($attribute_values_ids as $id) {
            wp_cache_delete($cache_key_prefix . absint($id), $cache_group);
        }
        wp_cache_delete('axilweb_ajl_attribute_values_list', $cache_group);
        
        // Total affected rows
        $total_affected = 0;
        
        // Process each ID individually since $wpdb->delete() doesn't support IN queries directly
        foreach ($attribute_values_ids as $id) {
            // Setup the delete operation
            $table = $table_attribute_values;
            $where = array(
                'id' => $id,
            );
            $where_format = array('%d'); // Data type for the 'id' in $where clause
            
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented above
            $result = $wpdb->delete($table, $where, $where_format);
            
            if ($result) {
                $total_affected += $result;
            }
        }
        
        return $total_affected;
    }
    
    /**
     * Internal method to restore attribute values in the database.
     *
     * @since 1.0.0
     * @param int $deleted_by User ID who is restoring the values
     * @param array $attribute_values_ids Array of attribute value IDs to restore
     * @param string $table_attribute_values Table name for attribute values
     * @param string $ids_placeholders SQL placeholders for the ID list (not used in refactored version)
     * @return int|bool Number of rows affected or false on error
     */
    private function _query_restore_attribute_values($deleted_by, $attribute_values_ids, $table_attribute_values, $ids_placeholders) {
        global $wpdb;
        
        $cache_key_prefix = 'axilweb_ajl_attr_value_';
        $cache_group = 'axilweb_ajl_attributes';
        
        // Invalidate caches for each attribute value
        foreach ($attribute_values_ids as $id) {
            wp_cache_delete($cache_key_prefix . absint($id), $cache_group);
        }
        wp_cache_delete('axilweb_ajl_attribute_values_list', $cache_group);
        
        // Total affected rows
        $total_affected = 0;
        
        // Process each ID individually
        foreach ($attribute_values_ids as $id) {
            // Setup the update operation
            $table = $table_attribute_values;
            $data = array(
                'deleted_at' => null,
                'deleted_by' => $deleted_by,
            );
            $where = array(
                'id' => $id,
            );
            $format = array('%s', '%d'); // Data types for $data values (null and integer)
            $where_format = array('%d'); // Data type for the 'id' in $where clause
            
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented above
            $result = $wpdb->update($table, $data, $where, $format, $where_format);
            
            if ($result) {
                $total_affected += $result;
            }
        }
        
        return $total_affected;
    }
    
    /**
     * Internal method to soft delete attribute values in the database.
     *
     * @since 1.0.0
     * @param string $deleted_at Timestamp for when the records were deleted
     * @param int $deleted_by User ID who is deleting the values
     * @param array $attribute_values_ids Array of attribute value IDs to soft delete
     * @param string $table_attribute_values Table name for attribute values
     * @param string $ids_placeholders SQL placeholders for the ID list
     * @return int|bool Number of rows affected or false on error
     */
    private function _query_soft_delete_attribute_values($deleted_at, $deleted_by, $attribute_values_ids, $table_attribute_values, $ids_placeholders) {
        global $wpdb;
        
        $cache_key_prefix = 'axilweb_ajl_attr_value_';
        $cache_group = 'axilweb_ajl_attributes';
        
        // Invalidate caches for each attribute value
        foreach ($attribute_values_ids as $id) {
            wp_cache_delete($cache_key_prefix . absint($id), $cache_group);
        }
        wp_cache_delete('axilweb_ajl_attribute_values_list', $cache_group);
        
        // Total affected rows
        $total_affected = 0;
        
        // Process each ID individually
        foreach ($attribute_values_ids as $id) {
            // Setup the update operation with data to soft delete
            $table = $table_attribute_values;
            $data = array(
                'deleted_at' => $deleted_at,
                'deleted_by' => $deleted_by,
            );
            $where = array(
                'id' => $id,
            );
            $format = array('%s', '%d'); // Data types for $data values (string timestamp and integer)
            $where_format = array('%d'); // Data type for the 'id' in $where clause
            
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented above
            $result = $wpdb->update($table, $data, $where, $format, $where_format);
            
            if ($result) {
                $total_affected += $result;
            }
        }
        
        return $total_affected;
    }
}
