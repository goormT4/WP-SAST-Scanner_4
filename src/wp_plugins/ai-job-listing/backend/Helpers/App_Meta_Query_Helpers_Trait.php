<?php
namespace Axilweb\AiJobListing\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait App_Meta_Query_Helpers_Trait
{

   /**
     * Insert application metadata into the database with caching.
     *
     * This function inserts a batch of metadata records associated with a specific application 
     * into the `axilweb_ajl_application_meta` table. It constructs the SQL query dynamically 
     * using placeholders and values provided in the `$data` array.
     *
     * @since 1.0.0
     *
     * @param array $data An array containing the placeholders and values for the SQL insert query.
     *                    - 'place_holders': An array of SQL placeholders for the insert statement.
     *                    - 'values': An array of values corresponding to the placeholders.
     *
     * @return bool True on successful insertion, false on failure.
     */
    public static function addApplication_Meta($data)
    {
        if (empty($data['place_holders']) || empty($data['values'])) {
            return false;
        }
        
        // Execute the database query
        $query_result = self::_query_add_application_meta($data);
        
        // If insertion was successful, invalidate any related caches
        if ($query_result) {
            // Find app_id from the values array (it should be the first value in each placeholder set)
            $app_id = false;
            foreach ($data['values'] as $key => $value) {
                if ($key % 5 === 0) { // Every 5th element is an app_id (based on column order)
                    $app_id = intval($value);
                    break;
                }
            }
            
            // If we found a valid app_id, invalidate cache
            if ($app_id) {
                $cache_key = 'axilweb_ajl_app_meta_' . $app_id;
                wp_cache_delete($cache_key, 'axilweb_ajl_application_meta');
            }
            
            return true;
        }
        
        return false;
    }
     
    /**
     * Internal method to insert application metadata into the database.
     * Implementation method for addApplication_Meta().
     *
     * This is a specific implementation for inserting into the custom application_meta table,
     * using WordPress's $wpdb->insert() method for safer database operations.
     *
     * @since 1.0.0
     * @param array $data An array containing placeholders and values for the SQL query
     * @return bool|int Result of the database operation
     */
    private static function _query_add_application_meta($data)
    {
        global $wpdb;

        // Validate input data
        if (empty($data['place_holders']) || empty($data['values']) || !is_array($data['place_holders']) || !is_array($data['values'])) {
            return false;
        }

        // Create a cache key for this operation
        $cache_key = 'axilweb_ajl_app_meta_insert_' . md5(serialize($data));
        $cache_group = 'axilweb_ajl_app_meta_operations';
        
        // Check if we have a cached result first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache or cache is invalidated, execute the query
        if (false === $result) {
            // Define table name (safe as it uses WordPress prefix)
            $table = $wpdb->prefix . 'axilweb_ajl_application_meta';
            
            // Track total number of successful inserts
            $total_inserts = 0;
            
            // Process each batch of values for insert
            // Assuming place_holders have values for batch inserts and values are grouped every 5 items
            $total_placeholders = count($data['place_holders']);
            
            for ($i = 0; $i < $total_placeholders; $i++) {
                // Ensure we have all required values for this insert
                $value_index = $i * 5; // Each insert uses 5 values
                if (!isset($data['values'][$value_index]) || 
                    !isset($data['values'][$value_index + 1]) || 
                    !isset($data['values'][$value_index + 2]) || 
                    !isset($data['values'][$value_index + 3]) || 
                    !isset($data['values'][$value_index + 4])) {
                    continue; // Skip if missing values
                }
                
                // Prepare data for insert
                $insert_data = array(
                    'app_id'     => $data['values'][$value_index],       // app_id
                    'app_mk'     => $data['values'][$value_index + 1],   // meta key  
                    'app_mv'     => $data['values'][$value_index + 2],   // meta value
                    'created_at' => $data['values'][$value_index + 3],   // created_at
                    'created_by' => $data['values'][$value_index + 4]    // created_by
                );
                
               // In the _query_add_application_meta function, before the $wpdb->insert call:

                // Define formats based on the expected data types
                $formats = array('%d', '%s', '%s', '%s', '%d');
                
                // Use $wpdb->insert for safer database operations
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Using caching to mitigate performance concerns
                $insert_result = $wpdb->insert($table, $insert_data, $formats);
                    
                if ($insert_result) {
                    $total_inserts++;
                }
            }
            
            // Set result to the number of rows inserted
            $result = $total_inserts;
            
            // Cache successful results
            if ($result > 0) {
                wp_cache_set($cache_key, $result, $cache_group, 60); // Cache for 60 seconds
            }
        }
        
        return $result;
    }

    /**
     * Prepare application metadata for insertion into the database.
     *
     * This function processes an array of application metadata, categorizing specific 
     * keys such as 'resume' and 'profile_image' separately, while preparing the rest 
     * for insertion into the database. It returns an array that separates these specific 
     * metadata keys from the others.
     *
     * @since 1.0.0
     *
     * @param array $data The array of metadata to be processed. Each item in the array should be an associative array
     *                    with 'app_mk' and 'app_mv' keys.
     *
     * @return array An associative array containing:
     *               - 'process_meta': An array of metadata prepared for database insertion, excluding 'resume' and 'profile_image'.
     *               - 'profile_image_key': The key associated with the 'profile_image' metadata, if found.
     *               - 'resume_key': The key associated with the 'resume' metadata, if found.
     */
    public static function prepare_app_meta_for_insert($data): array {
        $process_meta       = array();
        $resume_key         = '';
        $profile_image_key  = '';

        // Check if data is set and not empty
        if (isset($data) && !empty($data)) {
            // Loop through each metadata item and use a more efficient approach
            foreach ($data as $key => $val) {
                $app_mk = sanitize_key($val['app_mk']); // Sanitize the key for better indexing
                
                // Use a more efficient array-based approach instead of switch
                if ($app_mk === 'resume') {
                    $resume_key = $key;
                } elseif ($app_mk === 'profile_image') {
                    $profile_image_key = $key;
                } else {
                    // Store meta with sanitized values and prepare for batch insertion
                    $process_meta[$key] = [
                        'app_mk' => $app_mk, 
                        'app_mv' => maybe_serialize($val['app_mv'])
                    ];
                }
            }
        }

        // Return the processed metadata and specific keys
        return [
            'process_meta'      => $process_meta,
            'profile_image_key' => $profile_image_key,
            'resume_key'        => $resume_key
        ];
    }
 /**
     * Prepare an attachment array for upload.
     *
     * This function takes an array representing file uploads (typically from $_FILES) 
     * and extracts the relevant details for a specific file, returning them in a structured format.
     * This is useful for processing individual file uploads in WordPress or other PHP environments.
     *
     * @since 1.0.0
     *
     * @param array $attachment The array containing file upload data, usually from $_FILES.
     * @param int $key The index of the specific file within the $attachment array to be processed.
     *
     * @return array An associative array containing the following keys:
     *               - 'name'     : The original name of the uploaded file.
     *               - 'type'     : The MIME type of the uploaded file.
     *               - 'tmp_name' : The temporary filename of the file on the server.
     *               - 'error'    : Any error code associated with the file upload.
     *               - 'size'     : The size of the uploaded file in bytes.
     */
    public static function prepare_attachment_for_upload($attachment, $key): array {
        // Validate input
        if (!is_array($attachment) || (!isset($attachment['name']) && !isset($attachment['app_meta']))) {
            return [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_NO_FILE,
                'size'     => 0
            ];
        }
       
        // Determine if this is a nested or flat file upload structure
        $is_nested = isset($attachment['name'][0]['app_mv']);

        // Handle the specific nested structure with app_mv
        if ($is_nested && isset($attachment['name'][$key]['app_mv'])) {
            return [
                'name'     => $attachment['name'][$key]['app_mv'],
                'type'     => $attachment['type'][$key]['app_mv'],
                'tmp_name' => $attachment['tmp_name'][$key]['app_mv'],
                'error'    => $attachment['error'][$key]['app_mv'],
                'size'     => $attachment['size'][$key]['app_mv']
            ];
        }

        // Handle $_FILES-like structure with nested keys and app_mv
        if (isset($attachment['app_meta']['name'][$key]['app_mv'])) {
            return [
                'name'     => $attachment['app_meta']['name'][$key]['app_mv'],
                'type'     => $attachment['app_meta']['type'][$key]['app_mv'],
                'tmp_name' => $attachment['app_meta']['tmp_name'][$key]['app_mv'],
                'error'    => $attachment['app_meta']['error'][$key]['app_mv'],
                'size'     => $attachment['app_meta']['size'][$key]['app_mv']
            ];
        }

        // Handle flat file upload structure
        if (!$is_nested && is_string($attachment['name'])) {
            return [
                'name'     => $attachment['name'],
                'type'     => $attachment['type'],
                'tmp_name' => $attachment['tmp_name'],
                'error'    => $attachment['error'],
                'size'     => $attachment['size']
            ];
        }

        // If no matching structure is found, return a default empty file array
        return [
            'name'     => '',
            'type'     => '',
            'tmp_name' => '',
            'error'    => UPLOAD_ERR_NO_FILE,
            'size'     => 0
        ];
    }

}
