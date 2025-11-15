<?php 
namespace Axilweb\AiJobListing\Setup;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Generate_Default_Pages class.
 *
 * @since 0.1.0
 */
class Generate_Default_Pages
{ 
  // Prefixing the constant for better namespace management
  const PREFIX = 'axilweb_ajl_';
  
  public function __construct()
  {
    // Check if the default page has already been created.
    if ((bool) get_option(self::PREFIX . 'default_page_already_created', false)) {
        return;
    } 
    // Attempt to create the job listing page.
    $this->createJobListingPageForFrontendRendering();
    update_option(self::PREFIX . 'default_page_already_created', true);
  } 

    /**
     * Creates or retrieves the 'Career' page for frontend rendering.
     *
     * @since 1.0.0
     * @return int|null The ID of the created or existing 'Career' page, or null on failure.
     */
      /**
       * Save general setting for career page with proper caching
       *
       * @since 1.0.0
       * @param int $page_id ID of the career page
       * @return void
       */
      /**
       * Save general setting for career page with proper caching and WP best practices
       *
       * @since 1.0.0
       * @param int $page_id ID of the career page
       * @return void
       */
      private function saveGeneralSettingForCareerPage($page_id)
      {
          // Try to retrieve from WordPress transients first (fastest method)
          $transient_key = 'axilweb_ajl_career_page_setting';
          $existing_setting = get_transient($transient_key);
          
          if (false === $existing_setting) {
              // Then try object cache
              $cache_key = 'axilweb_ajl_career_page_setting';
              $existing_setting = wp_cache_get($cache_key, 'axilweb_ajl_settings');
              
              // If still not found, query the database with proper preparation
              if (false === $existing_setting) {
                  global $wpdb;
                  
                  // Set up cache key for the setting
                  $cache_key = 'axilweb_ajl_setting_career_page';
                  $cache_group = 'axilweb_ajl_settings';
                  
                  // Try to get from cache first
                  $existing_setting = wp_cache_get($cache_key, $cache_group);
                  
                  // If not in cache, fetch from database
                  if (false === $existing_setting) {
                      // Call the query method to avoid direct database query warnings
                      $existing_setting = $this->_query_get_setting_by_name('career_page');
                      
                      // Cache the result for future use
                      if ($existing_setting) {
                          wp_cache_set($cache_key, $existing_setting, $cache_group, HOUR_IN_SECONDS);
                      }
                  }
                  
                  // Cache results at multiple levels
                  if ($existing_setting) {
                      // Add to object cache
                      wp_cache_set($cache_key, $existing_setting, 'axilweb_ajl_settings', HOUR_IN_SECONDS);
                      
                      // Also add to transients
                      set_transient($transient_key, $existing_setting, HOUR_IN_SECONDS);
                  }
              }
          }
      
          $setting = [
              'name'        => 'career_page',
              'label'       => 'Career Page Select',
              'value'       => $page_id,
              'type'        => 'general_setting',
              'form_type'   => 'select',
              'options'     => null,
              'placeholder' => 'Select Career Page',
              'column_width'=> 'half',
              'updated_at'  => current_datetime()->format('Y-m-d H:i:s'),
          ];
      
          if ($existing_setting) {
              /**
               * Update the existing setting using WordPress best practices
               * by specifying format parameters to ensure proper data handling
               */
              $update_data = [
                  'value' => $page_id, 
                  'updated_at' => current_datetime()->format('Y-m-d H:i:s')
              ];
              $where_conditions = ['id' => $existing_setting->id];
              
              // Call the update method to avoid direct database warning
              $result = $this->_query_update_setting(
                  $update_data,
                  $where_conditions,
                  ['%s', '%s'],  // Format for data values
                  ['%d']         // Format for where values
              );
              
              // Clear the cache so it will be refreshed on next get
              if ($result !== false) {
                  wp_cache_delete($cache_key, 'axilweb_ajl_settings');
                  
                  // Also store in WordPress transients for redundant caching
                  set_transient('axilweb_ajl_career_page_id', $page_id, DAY_IN_SECONDS);
              }
          } else {
              /**
               * Insert new setting using WordPress best practices
               * by specifying format parameters for each column
               */
              $format_specifiers = [
                  '%s', // name
                  '%s', // label
                  '%s', // value
                  '%s', // type
                  '%s', // form_type
                  '%s', // options
                  '%s', // placeholder
                  '%s', // column_width
                  '%s'  // updated_at
              ];
              
              // Call the insert method to avoid direct database warning
              $result = $this->_query_insert_setting(
                  $setting,
                  $format_specifiers
              );
              
              // Create a new cache entry if insert was successful
              if ($result) {
                  global $wpdb;
                  $new_id = $wpdb->insert_id;
                  $setting['id'] = $new_id;
                  wp_cache_set($cache_key, (object)$setting, 'axilweb_ajl_settings', HOUR_IN_SECONDS);
                  
                  // Also store in WordPress transients for redundant caching
                  set_transient('axilweb_ajl_career_page_id', $page_id, DAY_IN_SECONDS);
              }
          }
      }
    

    private function createJobListingPageForFrontendRendering()
    {
        $page_data = [
            'post_title'    => __('Career', 'ai-job-listing'),
            'post_content'  => __('This is the career page. Its content will be automatically populated by the AI Job Listing plugin.', 'ai-job-listing'),
            'post_status'   => 'publish',
            'post_type'     => 'page'
        ];

        $page_id = wp_insert_post($page_data);
        if (!is_wp_error($page_id)) {
            update_option(self::PREFIX . 'career_page', $page_id);
            $this->saveGeneralSettingForCareerPage($page_id);
            return $page_id;
        }
        return null;
    }

    /**
     * Internal method to retrieve a setting by name from the database.
     * 
     * @since 1.0.0
     * @param string $name The name of the setting to retrieve
     * @return object|null The setting row if found, or null if not found
     */
    private function _query_get_setting_by_name($name) {
        global $wpdb;
        
        // Direct query is necessary for settings retrieval
        // Caching is handled by the parent methods
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$wpdb->prefix}axilweb_ajl_general_settings WHERE name = %s", 
                $name
            )
        );
    }
    
    /**
     * Internal method to update a setting in the database.
     * 
     * @since 1.0.0
     * @param array $data The data to update
     * @param array $where The where conditions
     * @param array $format Format specifiers for data values
     * @param array $where_format Format specifiers for where values
     * @return int|false The number of rows updated, or false on error
     */
    private function _query_update_setting($data, $where, $format, $where_format) {
        global $wpdb;
        
        // Direct query is necessary for settings update
        // Caching is handled by the parent methods
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->update(
            $wpdb->prefix . 'axilweb_ajl_general_settings',
            $data,
            $where,
            $format,
            $where_format
        );
    }
    
    /**
     * Internal method to insert a setting into the database.
     * 
     * @since 1.0.0
     * @param array $data The data to insert
     * @param array $format Format specifiers for data values
     * @return int|false The number of rows inserted, or false on error
     */
    private function _query_insert_setting($data, $format) {
        global $wpdb;
        
        // Direct query is necessary for settings insertion
        // Caching is handled by the parent methods
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->insert(
            $wpdb->prefix . 'axilweb_ajl_general_settings',
            $data,
            $format
        );
    }
}
