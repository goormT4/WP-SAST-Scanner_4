<?php

namespace Axilweb\AiJobListing\Rest;

use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Data\Data;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\General_Setting;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}
/**
 * API General_Setting class.
 *
 * @since 0.1.0
 */
class General_Settings_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'general-settings';
    
    /**
     * Get a setting by name with caching.
     *
     * Retrieves a setting from cache first, or from the database if not cached.
     * Handles caching the result for improved performance.
     *
     * @since 1.0.0
     * @param string $setting_name The name of the setting to retrieve
     * @return object|null The setting object or null if not found
     */
    protected function get_setting_by_name_cached($setting_name) {
        // Create cache keys
        $cache_key = 'axilweb_ajl_setting_' . md5($setting_name);
        $cache_group = 'axilweb_ajl_settings';
        
        // Try to get from cache first
        $setting = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $setting) {
            $setting = $this->_query_get_setting_by_name($setting_name);
            
            // Cache the result if found
            if ($setting) {
                wp_cache_set($cache_key, $setting, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $setting;
    }
    
    /**
     * Get a setting by ID with caching.
     *
     * Retrieves a setting from cache first, or from the database if not cached.
     * Handles caching the result for improved performance.
     *
     * @since 1.0.0
     * @param int $setting_id The ID of the setting to retrieve
     * @return object|null The setting object or null if not found
     */
    protected function get_setting_by_id_cached($setting_id) {
        // Create cache keys
        $cache_key = 'axilweb_ajl_setting_id_' . $setting_id;
        $cache_group = 'axilweb_ajl_settings';
        
        // Try to get from cache first
        $setting = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $setting) {
            $setting = $this->_query_get_setting_by_id($setting_id);
            
            // Cache the result if found
            if ($setting) {
                wp_cache_set($cache_key, $setting, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $setting;
    }
    
    /**
     * Query to get a setting by name.
     * Implementation method for get_setting_by_name_cached().
     *
     * This is a specific implementation for querying custom settings table,
     * which requires a direct database query to access the custom table structure.
     *
     * @since 1.0.0
     * @param string $setting_name The setting name to look up
     * @return object|null The setting object or null if not found
     */
    private function _query_get_setting_by_name($setting_name) {
        global $wpdb;
        
        // Direct query is necessary for accessing the custom settings table
        // Caching is handled by the parent method get_setting_by_name_cached()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}axilweb_ajl_general_settings WHERE name = %s", 
                $setting_name
            )
        );
    }
    
    /**
     * Query to get a setting by ID.
     * Implementation method for get_setting_by_id_cached().
     *
     * This is a specific implementation for querying custom settings table by ID,
     * which requires a direct database query to access the custom table structure.
     *
     * @since 1.0.0
     * @param int $setting_id The setting ID to look up
     * @return object|null The setting object or null if not found
     */
    private function _query_get_setting_by_id($setting_id) {
        global $wpdb;
        
        // Direct query is necessary for accessing the custom settings table
        // Caching is handled by the parent method get_setting_by_id_cached()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}axilweb_ajl_general_settings WHERE id = %d", 
                $setting_id
            )
        );
    }

    /**
     * Register all routes related to the API endpoints.
     *
     * This method registers various REST API routes for different resources like settings, roles, and users.
     * It uses the `register_rest_route` function to define each route, associate it with a callback, 
     * and define permission checks.
     *
     * @return void
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                    'schema'              => [$this, 'get_item_schema'],
                ]
            ]
        );

        // Route for updating settings
        register_rest_route($this->namespace, '/update-setting', [
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => [$this, 'updateSetting'],
            'permission_callback' => [$this, 'check_permission'],
            'args'                => $this->get_collection_params(),
            'schema'              => [$this, 'get_item_schema'],
        ]);

        // Route for retrieving general settings
        register_rest_route($this->namespace, 'general-setting', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_items_array'],
            'permission_callback' => [$this, 'check_permission'],
            'args'                => $this->get_collection_params(),
            'schema'              => [$this, 'get_item_schema'],
        ]);

        // Route for retrieving general settings
        register_rest_route($this->namespace, 'general-setting-frontend', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_items_array_frontend'],
            'permission_callback' => '__return_true',
            'args'                => $this->get_collection_params(),
            'schema'              => [$this, 'get_item_schema'],
        ]);

        // Route for searching pages
        register_rest_route(
            $this->namespace,
            '/search-pages',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'search_pages'], 
                    'permission_callback' => '__return_true', // fixed: permission_callback was missing 
                    'args'                => [
                        'search' => [
                            'required'          => false,
                            'type'             => 'string',
                            'description'       => __('Search term for pages', 'ai-job-listing'),
                            'validate_callback' => function ($param) {
                                return is_string($param);
                            }
                        ],
                        'per_page' => [
                            'required'          => false,
                            'type'             => 'integer',
                            'default'          => 30,
                            'description'       => __('Number of pages per page', 'ai-job-listing'),
                            'validate_callback' => function ($param) {
                                return is_numeric($param) && $param > 0;
                            }
                        ],
                        'page' => [
                            'required'          => false,
                            'type'             => 'integer',
                            'default'          => 1,
                            'description'       => __('Current page number', 'ai-job-listing'),
                            'validate_callback' => function ($param) {
                                return is_numeric($param) && $param > 0;
                            }
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Update or create a general setting in the database.
     *
     * This function updates an existing setting if it exists or creates a new setting if it doesn't.
     *
     * @param array $data The data array containing 'name' and 'value' of the setting.
     * @return object|null The updated or newly created record, or null on failure.
     */
    public function get_items_array_frontend($request): ?WP_REST_Response
    {
        $args = [
            'orderby' => 'id',
            'order'   => 'ASC'
        ];
        $data   = [];
        $params = $this->get_collection_params();

        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }

        $settings = axilweb_ajl_jobs()->general_settings_manager->all($args);

        $data = []; // Initialize the response data array

        // Process each setting and add to the data array under the setting's name
        foreach ($settings as $setting) {
            // Convert object to array if $setting is an object, or adjust accordingly
            $data[$setting->name] = $setting->value;
        }
        $data['page_slug'] = Helpers::getCareerPageSlug();
        $data['is_login'] = Helpers::isWpLogin();
        $data['career_page'] = Helpers::getCareerPageId();

        // Prepare data for response
        $prepared_data = $this->prepare_response_for_collection($data);
        // Create a response object
        $response = rest_ensure_response($prepared_data);
        return $response;
    }

    /**
     * Update or create a general setting in the database.
     *
     * This function updates an existing setting if it exists or creates a new setting if it doesn't.
     *
     * @param array $data The data array containing 'name' and 'value' of the setting.
     * @return object|null The updated or newly created record, or null on failure.
     */
     /**
      * Updates or creates a setting with proper caching
      *
      * @param array $data Setting data with name and value
      * @return object|null The updated or newly created record, or null on failure
      */
     public function updateOrCreateSetting($data)
    {
        global $wpdb;
        
        // Sanitize input data
        $setting_name = sanitize_text_field($data['name']);
        $setting_value = $data['value'];
        
        // Create cache keys
        $cache_key = 'axilweb_ajl_setting_' . md5($setting_name);
        $cache_group = 'axilweb_ajl_settings';
        
        // Use our cached method to check if the setting exists
        $existing = $this->get_setting_by_name_cached($setting_name);

        if ($existing) {
            // If the entry exists, update it with prepared statement
            // Direct database call is necessary for updating custom table
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $updated = $wpdb->update(
                $wpdb->prefix . 'axilweb_ajl_general_settings',
                ['value' => $setting_value], // Data to update
                ['name' => $setting_name],   // Where clause
                ['%s'], // Format for data values
                ['%s']  // Format for where values
            );
            
            // Clear cache after update
            if ($updated !== false) {
                wp_cache_delete($cache_key, $cache_group);
                
                // Use our cached method to get the updated record
                // First clear any existing cache for this setting
                wp_cache_delete($cache_key, $cache_group);
                
                // Then retrieve the fresh record with our helper method
                $updated_record = $this->get_setting_by_name_cached($setting_name);
                
                return $updated_record;
            }
            
            return null;
        } else {
            // If the entry doesn't exist, insert a new one
            $setting_state_data = Data::getListingPageSetting();
            $item = Helpers::findItemByName($setting_state_data, $setting_name);
            
            if ($item) {
                // Insert with proper format specifiers
                // Direct database call is necessary for inserting into custom table
                // All values are properly sanitized and parameterized with format specifiers
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $is_inserted = $wpdb->insert(
                    $wpdb->prefix . 'axilweb_ajl_general_settings',
                    [
                        'label' => $item['label'],
                        'name' => $item['name'],
                        'value' => $setting_value,
                        'placeholder' => $item['placeholder'],
                        'type' => $item['type'],
                        'form_type' => $item['form_type'],
                        'options' => $item['options'],
                        'column_width' => $item['column_width'],
                        'updated_at' => current_datetime()->format('Y-m-d H:i:s'),
                    ],
                    [ // Format specifiers for each column
                        '%s', // label
                        '%s', // name
                        '%s', // value
                        '%s', // placeholder
                        '%s', // type
                        '%s', // form_type
                        '%s', // options
                        '%s', // column_width
                        '%s'  // updated_at
                    ] 
                );
                
                if ($is_inserted) {
                    // Get the inserted record using our cached helper method
                    $inserted_id = $wpdb->insert_id;
                    $inserted_record = $this->get_setting_by_id_cached($inserted_id);
                    
                    // Also cache by name for lookups by name
                    if ($inserted_record) {
                        wp_cache_set($cache_key, $inserted_record, $cache_group, HOUR_IN_SECONDS);
                    }
                    
                    return $inserted_record;
                }
            }
            
            return null;
        }
    }

    /**
     * Updates or creates multiple settings via the REST API.
     *
     * This function processes an array of settings sent via a REST API request.
     * It updates existing settings or creates new ones if they do not exist.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|WP_Error The response containing updated settings or an error message.
     */
    public function updateSetting($request): ?WP_REST_Response
    {
        // Validate input data
        if (empty($request['settings']) || !is_array($request['settings'])) {
            return new WP_Error(
                'axilweb_ajl_settings_required',
                __('Settings array is required for update', 'ai-job-listing'),
                ['status' => 400]
            );
        }

        $settings_data = $request['settings'];
        $updated_setting_data = [];

        foreach ($settings_data as $single_setting_data) {
            // Check if the current setting is `career_page` and update the option
            if (isset($single_setting_data['name'], $single_setting_data['value']) && $single_setting_data['name'] === 'career_page') {
                $page_id = $single_setting_data['value']; 
                $this->updateOptionTableCarrerPage($page_id); 

            }
            // Update or create the setting
            $updated_setting_data[] = $this->updateOrCreateSetting($single_setting_data);
        }

        // Create and return the response
        $response = rest_ensure_response($updated_setting_data);
        $response->set_status(200);
        return $response;
    }

    /**
     * Update the career page option in the options table.
     *
     * @param int $updated_page_id The ID of the updated career page.
     * @return void
     */
    protected function updateOptionTableCarrerPage($updated_page_id)
    {
        $page_id = get_option('axilweb_ajl_career_page');
        wp_update_post([
            'ID'           => $page_id,
            'post_content' => null,
        ]);
        update_option('axilweb_ajl_career_page', $updated_page_id);
    }

    /**
     * Retrieves all settings with pagination and merges with default settings.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The response containing settings data.
     */
    /**
     * Retrieves all settings with pagination and merges with default settings.
     * Uses proper caching to reduce database queries.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The response containing settings data.
     */
    public function get_items($request)
    {
        $args = [
            'orderby' => 'id',
            'order'   => 'ASC'
        ];
        $data   = [];
        $params = $this->get_collection_params();

        // Process and sanitize parameters
        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }
        
        // Generate a unique cache key based on the query arguments
        $cache_key = 'axilweb_ajl_settings_' . md5(serialize($args));
        $cache_group = 'axilweb_ajl_settings_data';
        
        // Try to get settings from cache
        $cached_settings = wp_cache_get($cache_key, $cache_group);
        
        if (false === $cached_settings) {
            // If not in cache, fetch from database
            $settings = axilweb_ajl_jobs()->general_settings_manager->all($args);
            
            // Process settings data
            foreach ($settings as $setting) {
                $response = General_Setting::to_array($setting);
                $data[]   = $response;
            }
            
            // Cache the results
            wp_cache_set($cache_key, $data, $cache_group, HOUR_IN_SECONDS);
        } else {
            // Use cached data
            $data = $cached_settings;
        }
       
        // Merge with default settings that may not be in the database
        $data_name_columns = array_column($data, 'name');
        $listing_page_state_items = Data::getListingPageSetting();
        foreach ($listing_page_state_items as $state_item) {
            if (!in_array($state_item['name'], $data_name_columns)) {
                $data[] = $state_item;
            }
        }
        
        // Get total count for pagination - use cache for this too
        $count_args = $args;
        $count_args['count'] = 1;
        $count_cache_key = 'axilweb_ajl_settings_count_' . md5(serialize($count_args));
        
        $total = wp_cache_get($count_cache_key, $cache_group);
        if (false === $total) {
            $total = axilweb_ajl_jobs()->general_settings_manager->all($count_args);
            wp_cache_set($count_cache_key, $total, $cache_group, HOUR_IN_SECONDS);
        }
        
        // Setup pagination headers
        $max_pages = ceil($total / (int) $args['limit']);
        $response  = rest_ensure_response($data);
        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', (int) $max_pages);

        return $response;
    }

    /**
     * Retrieves settings as a key-value array with additional fields.
     *
     * This function fetches settings from the database, processes them into
     * a key-value format where the setting `name` is the key and `value` is the value,
     * and appends additional fields like `page_slug`.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The prepared REST API response.
     */
    /**
     * Retrieves settings as a key-value array with additional fields and proper caching.
     *
     * This function fetches settings from the database with caching, processes them into
     * a key-value format where the setting `name` is the key and `value` is the value,
     * and appends additional fields like `page_slug`.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response The prepared REST API response.
     */
    public function get_items_array($request): ?WP_REST_Response
    {
        $args = [
            'orderby' => 'id',
            'order'   => 'ASC'
        ];
        $params = $this->get_collection_params();

        // Process and sanitize request parameters
        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }
        
        // Generate a unique cache key based on the query args
        $cache_key = 'axilweb_ajl_settings_array_' . md5(serialize($args));
        $cache_group = 'axilweb_ajl_settings_array';
        
        // Try to get settings from cache first
        $cached_data = wp_cache_get($cache_key, $cache_group);
        
        if (false === $cached_data) {
            // If not in cache, fetch settings from the database
            $settings = axilweb_ajl_jobs()->general_settings_manager->all($args);
            
            $data = []; // Initialize the response data array
            
            // Process each setting and add to the data array under the setting's name
            foreach ($settings as $setting) {
                // Convert object to array if $setting is an object, or adjust accordingly
                $data[$setting->name] = $setting->value;
            }
            
            // Cache the processed data
            wp_cache_set($cache_key, $data, $cache_group, HOUR_IN_SECONDS);
        } else {
            // Use the cached data
            $data = $cached_data;
        }
        
        // Get dynamic values that should not be cached
        $page_slug_cache_key = 'axilweb_ajl_career_page_slug';
        $page_slug = wp_cache_get($page_slug_cache_key, $cache_group);
        
        if (false === $page_slug) {
            $page_slug = Helpers::getCareerPageSlug();
            wp_cache_set($page_slug_cache_key, $page_slug, $cache_group, HOUR_IN_SECONDS);
        }
        
        // Add dynamic values to the data array
        $data['page_slug'] = $page_slug;
        $data['is_login'] = Helpers::isWpLogin(); // This should be calculated on each request

        // Prepare data for response
        $prepared_data = $this->prepare_response_for_collection($data);
        // Create a response object
        $response = rest_ensure_response($prepared_data);
        return $response;
    }

    /**
     * Search WordPress pages with pagination and optional search term.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    /**
     * Search WordPress pages with pagination and optional search term.
     * Implements caching to improve performance and meet WordPress standards.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function search_pages($request)
    {
        // Sanitize and validate input parameters
        $search         = sanitize_text_field($request->get_param('search'));
        $per_page       = (int) $request->get_param('per_page');
        $page           = (int) $request->get_param('page');
        
        // Use wp_cache_get for option value instead of direct get_option call
        $career_page_cache_key = 'axilweb_ajl_career_page_id';
        $career_page_id = wp_cache_get($career_page_cache_key, 'axilweb_ajl_options');
        
        if (false === $career_page_id) {
            $career_page_id = (int) get_option(AXILWEB_AJL_PREFIX . 'career_page');
            wp_cache_set($career_page_cache_key, $career_page_id, 'axilweb_ajl_options', HOUR_IN_SECONDS);
        }

        // Build WP_Query arguments
        $args = array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => 'title',
            'order'          => 'ASC'
        );

        if (!empty($search)) {
            $args['s'] = $search;
        }
        
        // Create a cache key based on the query arguments
        $cache_key = 'AXILWEB_AJL_DEFAULT_PAGEs_search_' . md5(serialize($args));
        $cache_group = 'AXILWEB_AJL_DEFAULT_PAGEs';
        
        // Try to get results from cache
        $cached_results = wp_cache_get($cache_key, $cache_group);
        
        if (false === $cached_results) {
            // If not in cache, perform the query
            $query = new \WP_Query($args);
            
            // Cache the query results for future use
            wp_cache_set($cache_key, $query, $cache_group, HOUR_IN_SECONDS);
        } else {
            // Use cached results
            $query = $cached_results;
        }

        // Return empty response if no pages found and no career page set
        if (!$query->have_posts() && !$career_page_id) {
            return rest_ensure_response([
                'pages' => [],
                'total' => 0,
                'total_pages' => 0
            ]);
        }

        // Format page data
        $pages = array_map(function ($post) use ($career_page_id) {
            return [
                'id'            => $post->ID,
                'title'         => html_entity_decode($post->post_title),
                'url'           => get_permalink($post->ID),
                'is_career_page' => $post->ID == $career_page_id
            ];
        }, $query->posts);

        // If career page exists and not in search results, add it
        if ($career_page_id) {
            // Use cache for career page data
            $career_page_data_key = 'axilweb_ajl_career_page_data_' . $career_page_id;
            $career_page = wp_cache_get($career_page_data_key, 'AXILWEB_AJL_DEFAULT_PAGEs');
            
            if (false === $career_page) {
                $career_page = get_post($career_page_id);
                if ($career_page) {
                    wp_cache_set($career_page_data_key, $career_page, 'AXILWEB_AJL_DEFAULT_PAGEs', HOUR_IN_SECONDS);
                }
            }
            
            if ($career_page && $career_page->post_type === 'page') {
                // Check if career page already exists in results
                $career_page_exists = false;
                foreach ($pages as $page) {
                    if ($page['id'] === $career_page_id) {
                        $career_page_exists = true;
                        break;
                    }
                }

                // Add career page to results if not already included
                if (!$career_page_exists) {
                    array_unshift($pages, [
                        'id'            => $career_page_id,
                        'title'         => html_entity_decode($career_page->post_title),
                        'url'           => get_permalink($career_page_id),
                        'is_career_page' => true
                    ]);
                }
            }
        }

        return rest_ensure_response([
            'pages'       => $pages,
            'total'       => (int) $query->found_posts + (!empty($career_page_id) ? 1 : 0),
            'total_pages' => (int) ceil(($query->found_posts + (!empty($career_page_id) ? 1 : 0)) / $per_page)
        ]);
    }


    /**
     * Prepares a single item for a REST API response.
     *
     * This function formats an individual item as a REST API response,
     * applying context filtering and adding hypermedia links.
     *
     * @param mixed            $item    The item to prepare (e.g., a setting object or array).
     * @param WP_REST_Request  $request The REST API request object.
     * @return WP_REST_Response|null The prepared response, or null on failure.
     */
    public function prepare_item_for_response($item, $request): ?WP_REST_Response
    {

        $data = [];
        $data       = General_Setting::to_array($item);
        $data       = $this->prepare_response_for_collection($data);
        $context    = !empty($request['context']) ? $request['context'] : 'view';
        $data       = $this->filter_response_by_context($data, $context);
        $response   = rest_ensure_response($data);
        $response->add_links(Helpers::prepare_links($item, $this->namespace, $this->rest_base, $this->base));
        return $response;
    }

    /**
     * Retrieves the query params for collections.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_collection_params(): array
    {
        $params = parent::get_collection_params();

        $params['limit']['default'] = 100;
        $params['per_page']['default'] = 70;
        $params['s']['default']     = '';

        return $params;
    }
}
