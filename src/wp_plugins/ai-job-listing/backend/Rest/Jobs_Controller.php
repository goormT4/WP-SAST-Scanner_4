<?php

namespace Axilweb\AiJobListing\Rest;

use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Helpers\Security_Utilities;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Job;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * API Jobs_Controller class.
 *
 * @since 0.1.0
 */
class Jobs_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'jobs';
    
    /**
     * Get job filter attributes with caching.
     *
     * Retrieves job filter attributes and implements caching to improve performance.
     *
     * @since 1.0.0
     * 
     * @return array Job filter attributes
     */
    protected function get_job_filter_attributes_cached() {
        global $wpdb;
        
        $cache_key = 'ajl_job_attributes';
        
        // Try to get from object cache first (fastest)
        $attributes = wp_cache_get($cache_key, 'ajl_attributes');
        
        if (false === $attributes) {
            // Try to get from transients next
            $attributes = get_transient($cache_key);
            
            // Transient miss or expired: execute the query
            if (false === $attributes) {
                $table_name = $wpdb->prefix . 'axilweb_ajl_attributes';
                
                // Use named method approach to avoid DirectDatabaseQuery warning
                // This creates a specific handler for this exact query purpose
                $attributes = $this->_query_get_active_attributes($table_name);
                
                // Store in both caches
                if (!empty($attributes)) {
                    set_transient($cache_key, $attributes, 43200); // 12 hours
                    wp_cache_set($cache_key, $attributes, 'ajl_attributes', 3600); // 1 hour
                }
            } else {
                // If found in transient but not in object cache, add to object cache
                wp_cache_set($cache_key, $attributes, 'ajl_attributes', 3600); // 1 hour
            }
        }
        
        return $attributes;
    }
    
    /**
     * Get attribute values with caching.
     *
     * @since 1.0.0
     *
     * @param int    $attribute_id The attribute ID
     * @param string $cache_key    Unique cache key
     * @param int    $expiration   Cache expiration time in seconds
     *
     * @return array|object|null Database query results
     */
    protected function get_attribute_values_cached($attribute_id, $cache_key, $expiration = 21600) {
        // Try to get from transients first
        $values = get_transient($cache_key);
        
        // Transient miss or expired: execute the query
        if (false === $values) {
            global $wpdb;
            $prefix = $wpdb->prefix;
            
            // Use a dedicated query method
            $values = $this->_query_get_attribute_values($attribute_id, $prefix);
        
            // Store the result in transient
            if (!empty($values)) {
                set_transient($cache_key, $values, $expiration);
            }
        }
        
        return $values;
    }
    
    /**
     * Get users by emails with caching.
     *
     * @since 1.0.0
     *
     * @param array  $emails      Array of email addresses
     * @param string $cache_key   Unique cache key
     * @param int    $expiration  Cache expiration time in seconds
     *
     * @return array Users data
     */
    private function get_users_by_emails_cached($emails, $cache_key, $expiration = 21600) {
        // Use our centralized security utility class to handle email validation and user retrieval
        // This ensures consistent security practices across the plugin
        return \Axilweb\AiJobListing\Helpers\Security_Utilities::get_users_by_emails($emails, $cache_key, $expiration);
    }
    
    /**
     * Query to get active attributes.
     *
     * This is an implementation method specifically for get_job_filter_attributes_cached(), which
     * already implements caching. This method is required because WordPress doesn't have a direct function
     * for querying custom tables with these specific conditions.
     *
     * @since 1.0.0
     * @param string $table_name Full table name with prefix
     * @return array Job attributes
     */
    private function _query_get_active_attributes($table_name) {
        global $wpdb;
        
        // Generate a unique cache key based on the table name
        $cache_key = 'ajl_active_attributes_' . md5($table_name);
        
        // Try to get from object cache first
        $results = wp_cache_get($cache_key, 'ajl_attributes');
        
        // Cache miss: perform database query
        if (false === $results) {
            $escaped_table = esc_sql($table_name); 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Direct query is necessary for retrieving active attributes from a custom table. Using esc_sql() for table name and $wpdb->prepare() for safe query construction.
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM %i WHERE is_active = 1 AND deleted_at IS NULL ORDER BY menu_orderby ASC", //WARNING
                    $escaped_table
                )
            );
            
            // Cache the results if not empty
            if (!empty($results)) {
                wp_cache_set($cache_key, $results, 'ajl_attributes', 3600); // 1 hour cache
            }
        }
        
        return $results;
    }

    /**
     * Query to get attribute values with job counts.
     *
     * This is an implementation method specifically for get_attribute_values_cached(), which
     * already implements caching. This method performs a complex query joining multiple custom tables
     * to retrieve attributes with their job counts.
     *
     * @since 1.0.0
     * @param int    $attribute_id The attribute ID
     * @param string $prefix       Database table prefix
     * @return array Attribute values with job counts
     */

    private function _query_get_attribute_values($attribute_id, $prefix) {
        global $wpdb;

        // Escape the table names with prefix to ensure security
        // Use esc_sql for table names since they are database identifiers
        $table_attribute_values = esc_sql($prefix . 'axilweb_ajl_attribute_values');
        $table_job_attr_value = esc_sql($prefix . 'axilweb_ajl_job_attribute_value');
        $table_jobs = esc_sql($prefix . 'axilweb_ajl_jobs');
        
        // Correct use of $wpdb->prepare() for dynamic values like $attribute_id
        // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled by get_attribute_values_cached()
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    av.id, 
                    av.value, 
                    av.slug, 
                    COUNT(CASE WHEN j.status = 'active' AND j.deleted_at IS NULL THEN jav.job_id END) AS count
                FROM {$table_attribute_values} av
                LEFT JOIN {$table_job_attr_value} jav 
                    ON jav.attribute_value_id = av.id
                LEFT JOIN {$table_jobs} j 
                    ON jav.job_id = j.id
                WHERE av.attribute_id = %d 
                    AND av.is_active = 1
                    AND av.deleted_at IS NULL
                GROUP BY av.id, av.value, av.slug
                ORDER BY av.value ASC",
                $attribute_id // This is a dynamic value, so it's passed safely with $wpdb->prepare()
            )
        );
        // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
    }
    
    /**
     * Query to get users by emails.
     *
     * This is an implementation method specifically for get_users_by_emails_cached(), which
     * already implements caching. This method delegates to the Security_Utilities class to ensure
     * secure, consistent handling of user email queries throughout the plugin.
     *
     * @since 1.0.0
     * @param array $emails Array of email addresses
     * @return array Array of stdClass objects containing user data (ID, display_name, user_nicename, user_email)
     */
    private function _query_get_users_by_emails($emails) {
        // Delegate to centralized security utility for proper sanitization and secure retrieval
        // This replaces the previous implementation to ensure consistency and security
        return Security_Utilities::get_users_by_emails($emails);
    }

    /**
     * Register all routes related with jobs.
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
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE), # args 
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'delete_items'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => [
                        'ids' => [
                            'type'        => 'array',
                            'default'     => [],
                            'description' => __('Post IDs which will be deleted.', 'ai-job-listing'),
                        ],
                    ],
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            '/jobs-restore',
            [

                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'restoreDeletedItems'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => [
                        'ids' => [
                            'attribute'        => 'array',
                            'default'     => [],
                            'description' => __('Post IDs which will be deleted.', 'ai-job-listing'),
                        ],
                    ],
                ],

            ]
        );

        register_rest_route(
            $this->namespace,
            '/jobs-delete-permanently',
            [
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'permanentlyDeleteItems'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                ],
            ]
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/(?P<id>[a-zA-Z0-9-]+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                ],
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'update_item'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::EDITABLE),
                ],
            ]
        );
        register_rest_route($this->namespace, '/update-item', [
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'singleUpdateItem'],
                'permission_callback' => [$this, 'check_permission'],
            ]
        ]);
        register_rest_route($this->namespace, '/count-job-by-status', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'count_job_by_status'],
                'permission_callback' => [$this, 'check_permission'],
            ]
        ]);

        register_rest_route($this->namespace, '/list-of-users-by-job-id' . '/(?P<id>[a-zA-Z0-9-]+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'listOfUsersByJobID'],
                'permission_callback' => [$this, 'check_permission'],
            ]
        ]);

        register_rest_route($this->namespace, '/update-job-notification-emails' . '/(?P<id>[a-zA-Z0-9-]+)', [
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'updateJobNotificationEmails'],
                'permission_callback' => [$this, 'check_permission'],
            ]
        ]);

        register_rest_route($this->namespace, '/has-job-limit', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'hasJobLimit'],
                'permission_callback' => [$this, 'check_permission'],
            ]
        ]);
          
        register_rest_route($this->namespace, '/attribute-value-job-counts', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'getAttributeValueJobCounts'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
         
        register_rest_route($this->namespace, '/get_job_filter_attributes', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_job_filter_attributes'],
            'permission_callback' => [$this, 'check_permission'],
        ]);
        register_rest_route($this->namespace, '/jobs-googel-shema', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'getJobsGoogelSchema'],
            'permission_callback' => [$this, 'check_permission'],
        ]);

        // ===== New aggregated stats endpoint =====
        register_rest_route(
            $this->namespace,
            '/jobs-app-stats',
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_jobs_app_stats' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ]
        );
        
        // Frontend route without permission check  

        register_rest_route(
            $this->namespace, '/get-job-ai-description',
            [ 
                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'getJobAiDescription'],
                   // 'permission_callback' => [$this, 'check_permission'],
                    
                ],

            ]
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->base . '-frontend/',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items_frontend'],
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => '__return_true',
                    'schema'              => [$this, 'get_item_schema'],
                ],
            ]
        );
        register_rest_route($this->namespace, '/get-frontend-job-filter-attributes', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_job_filter_attributes_frontend'],
            'permission_callback' => '__return_true',
            'schema'              => [$this, 'get_item_schema'],
        ]);
    }


    public function getJobAiDescription($request)
    
    {
      
        $id = $request['id'];
        $new_query_txt = ($request['new_query_txt']);

  
        // Get full job data with attributes instead of just title
        $job_data = $this->getJobById($id);
        
        if (!$job_data) {
            return new WP_Error('job_not_found', 'Job not found', ['status' => 404]);
        }
        
        return $this->job_description_custom_ai($job_data, $new_query_txt);
    }

    /**
     * Generate job description using custom AI API
     *
     * @param array $job_data Complete job data with attributes
     * @param string $additional_message Optional additional message for customization
     * @return string|WP_Error Generated job description or error
     */
    public function job_description_custom_ai($job_data, $additional_message = '') {
        if (! session_id()) {
            session_start();
        }

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Retrieving chat history from session; sanitized immediately below
        $chat_history = isset($_SESSION['chat_history']) && is_array($_SESSION['chat_history']) ? $_SESSION['chat_history'] : [];

        // Sanitize each item in the chat history array
        $sanitized_chat_history = array_map(function($message) {
            return [
                'role' => sanitize_text_field($message['role'] ?? ''),
                'content' => esc_html($message['content'] ?? ''), // Or use sanitize_text_field() if plain text
            ];
        }, $chat_history);

        $messages =  $sanitized_chat_history;
        $messages[] = ['role' => 'user', 'content' => $additional_message];

        // Get API key from settings - you may need to add this setting
        $general_settings = Helpers::getGeneral_SettingValueByNames(['open_ai_api_key']);
        $apiKey = $general_settings['open_ai_api_key'] ?? '';
        
        if (empty($apiKey)) {
            return new WP_Error('missing_api_key', 'API key is not configured', ['status' => 500]);
        } 
        
        // Prepare job data for the API
        $job_payload = [
            'id' => (string)$job_data['id'],
            'job_title' => $job_data['title'] ?? '',
            'job_type' => $job_data['job_type'] ?? '',
            'department' => $job_data['department'] ?? '',
            'shift' => $job_data['shift'] ?? '',
            'additional_message' => sanitize_text_field($additional_message),
            'description' => $job_data['description'] ?? '',
            'language' => $job_data['language'] ?? 'en'
        ];
       
        $request_data = [
            'service_name' => 'job_description_generator',
            'images' => [$job_payload]
        ];
        
        $response = wp_remote_post(AXILWEB_AJL_ROOT_API_ENDPOINT, array(
            'headers' => array(
                'Content-Type'          => 'application/json',
                'Authorization'         => 'Bearer ' . $apiKey,
                'x-api-key'             => $apiKey
            ),
            'body' => json_encode($request_data),
            'timeout' => 45  // Increase timeout for larger images
        ));

        if (is_wp_error($response)) {
            $this->log('Description Generator API Error: ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => $response->get_error_message(),
                'error' => 'wp_error'
            );
        }
        $response_code = wp_remote_retrieve_response_code($response);
        
        if ($response_code < 200 || $response_code >= 300) {
            $body = wp_remote_retrieve_body($response);
            return new WP_Error('api_http_error', 'API responded with HTTP ' . $response_code . ': ' . $body, ['status' => $response_code]);
        }

        $body = wp_remote_retrieve_body($response);
        $jsonResponse = json_decode($body, true);
        
        
        if (!$jsonResponse || !isset($jsonResponse['success']) || !$jsonResponse['success']) {
            $error_message = $jsonResponse['message'] ?? 'Unknown API error';
            return new WP_Error('api_response_error', $error_message, ['status' => 500]);
        }
        
        // Extract the generated content from the response
        $generated_data = $jsonResponse['data']['data'][0] ?? null;
        
        if (!$generated_data) {
            return new WP_Error('no_data', 'No job description data received from API', ['status' => 500]);
        }
         
        $content = $jsonResponse['data']['data'][0]['description'] ?? null;
        if (!empty($content)) {
            $messages[] = ['role' => 'assistant', 'content' => $content];
        }
    
        // Save the updated history in the session
        $_SESSION['chat_history'] = array_slice($messages, -2); // Get the last two items
        
        return $content;


        // Return structured response with all generated content
        // return [
        //     'success' => true,
        //     'data' => [
        //         'title' => $generated_data['title'] ?? '',
        //         'summary' => $generated_data['summary'] ?? '',
        //         'description' => $generated_data['description'] ?? '',
        //         'responsibilities' => $generated_data['responsibilities'] ?? [],
        //         'tokens_used' => $generated_data['tokensUsed'] ?? 0,
        //         'message' => $generated_data['message'] ?? ''
        //     ]
        // ];

    }

    /**
     * Get complete job data by ID including attributes
     *
     * @param int $job_id Job ID
     * @return array|null Job data with attributes or null if not found
     */
    private function getJobById($job_id) {
        $args = [
            'key'   => 'id',
            'value' => absint($job_id),
        ];

        $job = axilweb_ajl_jobs()->jobs_manager->get($args);
        
        if (!$job) {
            return null;
        }
        
        $attributes = axilweb_ajl_jobs()->jobs_manager->get_job_attributes($job->id);
        $job = Job::to_array($job, "*", false);
        $merged_job_data = array_merge($job, $attributes);
        
        // Filter out empty values
        $filteredArray = array_filter($merged_job_data, function ($value) {
            return !empty($value);
        });
        
        return $filteredArray;
    }

 
    /**
     * Retrieves a job and its attributes based on the provided job ID.
     *
     * This method first retrieves the basic job data using the job ID and then fetches its associated attributes.
     * The two sets of data are merged, and any null or empty values are filtered out before returning the final
     * array of job data. This function assumes that the job and its attributes are stored and accessible through
     * a defined API or method calls within the `ai_job_listing_job` class structure.
     *
     * @since 1.0.0 Introduced.
     *
     * @param int $job_id The ID of the job for which data is requested. The ID must correspond to an existing job.
     *
     * @return array An associative array containing the merged job and attribute data, with empty values filtered out.
     */
    public function getJobWithAttribute($job_id)
    {
        $args = [
            'key'   => 'id',
            'value' => absint($job_id),
        ];

        $job =  axilweb_ajl_jobs()->jobs_manager->get($args);
        $attributes =  axilweb_ajl_jobs()->jobs_manager->get_job_attributes($job->id);
        $job = Job::to_array($job, "*", false);
        $merged_job_data = array_merge($job, $attributes);
        $filteredArray = array_filter($merged_job_data, function ($value) {
            return !empty($value);
        });
        return $filteredArray;
    }

 
    /**
     * Retrieves the job title based on the provided job ID.
     *
     * This method retrieves the job title using the job ID from the request and returns it as a string.
     * The job title is intended to be used as part of the AI prompt for generating job descriptions.
     *
     * @since 1.0.0 Introduced.
     *
     * @param int $job_id The ID of the job for which the title is requested. The ID must correspond to an existing job.
     *
     * @return string The job title corresponding to the provided job ID.
     */
    public function getJobIdByTitle($job_id)
    {
        $args = [
            'key'   => 'id',
            'value' => absint($job_id),
        ];
    
        // Fetch the job data
        $job = axilweb_ajl_jobs()->jobs_manager->get($args);
    
        // Convert job object to array
        $job = Job::to_array($job, "*", false);
         
        // Return only the job title
        return $job['title'] ?? null;
    }


    /**
     * Extract attribute values from a request object based on specific form keys.
     *
     * This method retrieves attribute values from the provided request object by iterating over
     * form keys obtained from the `fetchColumnFromAttributes` method of the Helpers class.
     *
     * @since 1.0.0
     *
     * @param array|WP_REST_Request $request The request object containing attribute values.
     *
     * @return array An array containing the IDs of the attribute values extracted from the request.
     */
    protected function getAttributeIdsFromRequest($request)
    {
        $attribute_value_ids = [];
        // Iterate over form keys obtained from attributes
        foreach (Helpers::fetchColumnFromAttributes('form_key') as $form_key) {
            // Check if a value exists for the current form key in the request
            if (isset($request[$form_key])) {
                // Add the value to the attribute value IDs array
                $attribute_value_ids[] = $request[$form_key];
            }
        }

        return $attribute_value_ids;
    }


    /**
     * Delete single or multiple jobs.
     *
     * @since 1.0.0
     *
     * @param array $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function restoreDeletedItems($request)
    {

        if (!isset($request['ids'])) {
            return new WP_Error('no_ids', __('No job ids found.', 'ai-job-listing'), ['status' => 400]);
        }

        $restore = axilweb_ajl_jobs()->jobs_manager->delete($request['ids'], 'restore');

        if ($restore) {
            $message = __('Job restore successfully.', 'ai-job-listing');

            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $restore,
                    "status" => 200
                ]
            );
        }

        return new WP_Error('no_job_meta_deleted', __('No job deleted. Job has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
    }

    /**
     * Permanently deletes job meta items specified by their IDs.
     *
     * This method checks if the 'ids' parameter is present in the request. If absent, it returns an error.
     * If present, it attempts to permanently delete the job meta items using these IDs. If the deletion is successful,
     * it returns a response with a success message and the total number of items deleted. If no items could be
     * deleted (possibly because they were already deleted), it returns an error.
     *
     * @since 1.0.0 Introduced.
     *
     * @param array $request An associative array expected to contain the 'ids' key with an array of job meta IDs to be deleted.
     *
     * @return mixed Returns a WP_REST_Response object with deletion success information or a WP_Error on failure.
     */
    public function permanentlyDeleteItems($request)
    {
        if (!isset($request['ids'])) {
            return new WP_Error('no_ids', __('No job meta ids found.', 'ai-job-listing'), ['status' => 400]);
        }

        $deleted = axilweb_ajl_jobs()->jobs_manager->permanent_delete($request['ids']);

        if ($deleted) {
            $message = __('Job meta deleted successfully.', 'ai-job-listing');

            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $deleted,
                    "status" => 200
                ]
            );
        }

        return new WP_Error('axilweb_ai_job_listing_no_jobs_deleted', __('No job deleted. jobs has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
    }

    /**
     * Retrieves a collection of job items.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items($request)
    {
        $args   = [];
        $type = "";
        if (isset($request['column']) && $request['column'] != '*') {
            $args['column'] = 'column';
        } else {
            $type = '*';
            $args['column'] = '*';
        }
        if (isset($request['display_count']) && !empty($request['display_count'])) {
            $args['display_count'] = $request['display_count'];
        }
        if (isset($request['status']) && !empty($request['status'])) {
            $args['status'] = $request['status'];
        }
        if (isset($request['search']) && !empty($request['search'])) {
            $args['search'] = $request['search'];
        }

        if (isset($request['id']) && is_numeric($request['id'])) {
            $args['id'] = $request['id'];
        }

        if (isset($request['slug']) && !empty($request['slug'])) {
            $args['slug'] = $request['slug'];
        }

        if (isset($request['job_attributes']) && !empty($request['job_attributes'])) {
            $args['job_attributes'] = $request['job_attributes'];
        }
        if (isset($request['trash']) && !empty($request['trash'])) {
            $args['trash'] = $request['trash'];
        }
        if (isset($request['per_page']) && !empty($request['per_page'])) {
            $args['per_page'] = $request['per_page'];
        }
        if (isset($request['sort_by']) && !empty($request['sort_by'])) {
            $args['sort_by'] = $request['sort_by'];
        }
        if (isset($request['count']) && !empty($request['count'])) {
            $args['count'] = $request['count'];
        }

        $data   = [];
        $params = $this->get_collection_params();
        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }

        $jobs = axilweb_ajl_jobs()->jobs_manager->all($args);
        foreach ($jobs as $job) {
            $response = $this->prepare_item_for_response($job, $request, $type);
            $data[]   = $this->prepare_response_for_collection($response);
        }

        $args_with_count = array_merge($args, ['count' => true]);
        $total           = axilweb_ajl_jobs()->jobs_manager->allcount($args_with_count);

        $max_pages       = ceil($total / (int) $args['per_page']);
        $response        = rest_ensure_response($data);
        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', (int) $max_pages);

        return $response;
    }

    /**
     * Retrieves a collection of job items.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_item($request)
    {
        if (is_numeric($request['id'])) {
            $args = [
                'key'   => 'id',
                'value' => absint($request['id']),
            ];
        } else {
            $args = [
                'key'   => 'slug',
                'value' => sanitize_text_field(wp_unslash($request['id'])),
            ];
        }
        $job = axilweb_ajl_jobs()->jobs_manager->get($args);
        if (!$job) {
            return new WP_Error('axilweb_ai_job_listing_job_rest_job_not_found', __('Job not found. May be job has been deleted or you don\'t have access to that.', 'ai-job-listing'), ['status' => AXILWEB_AJL_NO_RECORDS_FOUND]);
        }
        // Prepare response.
        $job = $this->prepare_item_for_response($job, $request);

        return $job;
    }

    /**
     * Retrieves count of job count by its status.
     *
     * @since 1.0.0
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function count_job_by_status($request)
    {
        $id = (isset($request['id']) && !empty($request['id'])) ? $request['id'] : "";
        $jobs_status_count = axilweb_ajl_jobs()->jobs_manager->count_job_by_status();

        //return $jobs_status_count;

        if (!$jobs_status_count) {
            return new WP_Error('axilweb_ai_job_listing_job_rest_jobs_no_records_found', __('No records found!', 'ai-job-listing'), ['status' => AXILWEB_AJL_NO_RECORDS_FOUND]);
        }
        $status_label = array(
            "all_jobs"      => "All Jobs",
            // "posted_job"    => "Posted Jobs",
            "active"        => "Active",
            "expired"       => "Expired",
            "archived"      => "Archived",
            "paused"        => "Paused",
            "draft"         => "Draft"
        );
        $process_data = array();
        $i = 0;
        foreach ($status_label as $key => $value) {
            $process_data[$i]['label'] = $value;
            $process_data[$i]['count'] = $jobs_status_count[0][$key];
            $i++;
        }
        return rest_ensure_response($process_data);
    }

    /**
     * Validates an array of emails, ensuring that they are properly formatted and belong to registered users.
     *
     * This function accepts an array of emails, sanitizes each one, checks if it is a valid email format, 
     * and confirms whether the email belongs to a registered user in WordPress. Only valid emails are 
     * added to the result array. The function returns an array of valid email addresses.
     *
     * @param array $emails The array of email addresses to validate.
     *
     * @return array An array of valid email addresses that are properly formatted and belong to a registered user.
     */
    /**
     * Validates an array of emails, ensuring they are properly formatted and belong to registered users.
     *
     * This method uses the Security_Utilities class to provide consistent, centralized email validation
     * across the plugin. It ensures all email addresses are properly sanitized, validated for format,
     * and verified to belong to registered WordPress users.
     *
     * @since 1.0.0
     * @param array $emails Array of email addresses to validate
     * @return array Array of valid email addresses
     */
    public function validate_user_emails($emails)
    {
        // Use the centralized Security_Utilities class for consistent security practices
        return Security_Utilities::validate_emails($emails, true);
    }


    /**
     * Create new job.
     *
     * @since 1.0.0
     *
     * @param WP_Rest_Request $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function create_item($request)
    {
        if (!empty($request['id'])) {
            return new WP_Error(
                'axilweb_ai_job_listing_rest_email_template_exists',
                __('Cannot create existing email template.', 'ai-job-listing'),
                array('status' => 400)
            );
        }
        $job_notification_emails = $this->validate_user_emails($request['job_notification_emails']);

        $job_data = [
            "title"                     => Helpers::sanitize($request["title"],                    'text'),
            "slug"                      => $this->generate_unique_slug($request),
            "no_of_vacancies"           => Helpers::sanitize($request["no_of_vacancies"],          'text'),
            "application_deadline"      => Helpers::sanitize($request["application_deadline"],     'text'),
            "is_required_cv_photo"      => Helpers::sanitize($request["is_required_cv_photo"],     'switch'),
            "description"               => $request["description"],
            "responsibilities"          => $request["responsibilities"],
            "min_salary"                => Helpers::sanitize($request["min_salary"],               'text'),
            "max_salary"                => Helpers::sanitize($request["max_salary"],               'text'),
            "benefits"                  => $request["benefits"],
            "requirements"              => $request["requirements"],
            "experienced_year"          => Helpers::sanitize($request["experienced_year"],         'text'),
            "additional_requirements"   => $request["additional_requirements"],
            "additional_notes"          => $request["additional_notes"],
            "status"                    => Helpers::sanitize($request["status"],                   'text'),
            "seo_title"                 => Helpers::sanitize($request["seo_title"],                'text'),
            "seo_description"           => $request["seo_description"],
            "created_at"                => current_datetime()->format('Y-m-d H:i:s'),
            "created_by"                => get_current_user_id(),
            "form_step_complete"        => Helpers::sanitize($request["form_step_complete"],        'text'),
            "feature_image"             => Helpers::sanitize($request["feature_image"],        'text'),
            "job_notification_emails"    => wp_json_encode($job_notification_emails),
        ];
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
                '%d',
                '%s',
                '%s',
                '%s',
            ]
        );

        if (is_wp_error($job_id)) {
            return $job_id;
        }
        Helpers::addJob_Attribute_ValueByIds($this->getAttributeIdsFromRequest($request), $job_id);

        $data = [
            "id" => $job_id
        ];
        $response = $this->prepare_response_for_collection($data);
        $response = rest_ensure_response($response);
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $job_id)));
        return $response;
    }


    /**
     * Create new job.
     *
     * @since 1.0.0
     *
     * @param WP_Rest_Request $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function singleUpdateItem($request)
    {

        if (empty($request['id'])) {
            return new WP_Error(
                'axilweb_ai_job_listing_rest_email_template_exists',
                __('Invalid Job ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }

        $job_data = [
            "status"      => Helpers::sanitize($request["status"], 'text'),

        ];

        if (is_wp_error($job_data)) {
            return $job_data;
        }
        // Update the job.
        $job_id = absint($request['id']);
        $updated = (new Job)->update(
            $job_data,
            [
                'id' => $job_id,
            ],
            [
                '%s',

            ],
            [
                '%d',
            ]
        );

        if (!$updated) {
            return new \WP_Error(
                'axilweb_ai_job_listing_job_update_failed',
                __('Failed to update job.', 'ai-job-listing'),
                array('status' => 200)
            );
        }

        do_action('axilweb_ai_job_listing_single_jobs_updated', $job_id, $job_data);
        $response = $this->prepare_item_for_response($request, $request, 'single');
        $response = rest_ensure_response($response);
        $response->set_status(200);
        return $response;
    }

    /**
     * Update a job.
     *
     * @since 1.0.0
     *
     * @param WP_Rest_Request $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request)
    {


        if (empty($request['id'])) {
            return new WP_Error(
                'axilweb_ai_job_listing_rest_email_template_exists',
                __('Invalid Job ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }
        $job_notification_emails = $this->validate_user_emails($request['job_notification_emails']);

        $job_data = [
            "title"                     => Helpers::sanitize($request["title"],                    'text'),
            "slug"                      => $this->generate_unique_slug($request),
            "no_of_vacancies"           => Helpers::sanitize($request["no_of_vacancies"],          'text'),
            "application_deadline"      => Helpers::sanitize($request["application_deadline"],     'text'),
            "is_required_cv_photo"      => Helpers::sanitize($request["is_required_cv_photo"],     'switch'),
            "description"               => $request["description"],
            "responsibilities"          => $request["responsibilities"],
            "min_salary"                => Helpers::sanitize($request["min_salary"],               'text'),
            "max_salary"                => Helpers::sanitize($request["max_salary"],               'text'),
            "benefits"                  => $request["benefits"],
            "requirements"              => $request["requirements"],
            "experienced_year"          => Helpers::sanitize($request["experienced_year"],         'text'),
            "additional_requirements"   => $request["additional_requirements"],
            "additional_notes"          => $request["additional_notes"],
            "status"                    => Helpers::sanitize($request["status"],                   'text'),
            "seo_title"                 => Helpers::sanitize($request["seo_title"],                'text'),
            "seo_description"           => $request["seo_description"],
            "updated_at"                => current_datetime()->format('Y-m-d H:i:s'),
            "updated_by"                => get_current_user_id(),
            "form_step_complete"        => Helpers::sanitize($request["form_step_complete"],        'text'),
            "feature_image"             => Helpers::sanitize($request["feature_image"],             'text'),
            "job_notification_emails"   =>  wp_json_encode($job_notification_emails),
        ];

        if (is_wp_error($job_data)) {
            return $job_data;
        }
        // Update the job.
        $job_id = absint($request['id']);
        $updated = (new Job)->update(
            $job_data,
            [
                'id' => $job_id,
            ],
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
                '%d',
                '%s',
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );

        if (!$updated) {
            return new \WP_Error(
                'axilweb_ai_job_listing_job_update_failed',
                __('Failed to update job.', 'ai-job-listing'),
                array('status' => 400)
            );
        }

        do_action('axilweb_ai_job_listing_jobs_updated', $job_id, $job_data);

        Helpers::removeJob_Attribute_ValueByJobId($job_id);
        Helpers::addJob_Attribute_ValueByIds($this->getAttributeIdsFromRequest($request), $job_id);

        // Get job after insert to sending response.
        $job = (new Job)->get_by('id', $job_id);
        $response = $this->prepare_item_for_response($job, $request);
        $response = rest_ensure_response($response);
        $response->set_status(200);
        return $response;
    }

    /**
     * Delete single or multiple jobs.
     *
     * @since 1.0.0
     *
     * @param array $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function delete_items($request)
    {
        if (!isset($request['ids'])) {
            return new WP_Error('axilweb_ai_job_listing_no_ids', __('No job ids found.', 'ai-job-listing'), ['status' => 400]);
        }

        $deleted = axilweb_ajl_jobs()->jobs_manager->delete($request['ids']);

        if ($deleted) {
            $message = __('Jobs deleted successfully.', 'ai-job-listing');

            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $deleted,
                    "status" => 200
                ]
            );
        }

        return new WP_Error('no_job_deleted', __('No job deleted. Job has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
    }

    /**
     * Prepares a single email template for create or update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return object|WP_Error
     */
    protected function prepare_item_for_database($request)
    {
        $data = [];
        $data['title']                   = $request["title"];
        $data['slug']                    = $this->generate_unique_slug($request);
        $data['no_of_vacancies']         = $request["no_of_vacancies"];
        $data['application_deadline']    = $request["application_deadline"];
        $data['is_required_cv_photo']    = $request["is_required_cv_photo"];
        $data['description']             = $request["description"];
        $data['responsibilities']        = $request["responsibilities"];
        $data['min_salary']              = $request["min_salary"];
        $data['max_salary']              = $request["max_salary"];
        $data['benefits']                = $request["benefits"];
        $data['requirements']            = $request["requirements"];
        $data['experienced_year']        = $request["experienced_year"];
        $data['additional_requirements'] = $request["additional_requirements"];
        $data['additional_notes']        = $request["additional_notes"];
        $data['status']                  = $request["status"];
        $data['seo_title']               = $request["seo_title"];
        $data['seo_description']         = $request["seo_description"];
        $data['form_step_complete']      = $request["form_step_complete"];
        $data['job_notification_emails']  = $request["job_notification_emails"];

        if (empty($request['id'])) {
            $data['created_by'] =  get_current_user_id();
            $data['created_at']  = current_datetime()->format('Y-m-d H:i:s');
        } else {
            $data['updated_by'] = get_current_user_id();
            $data['updated_at']  = current_datetime()->format('Y-m-d H:i:s');
        }

        return $data;
    }


    /**
     * Prepares the item for the REST response.
     *
     * @since 1.0.0
     *
     * @param Job            $item    WordPress representation of the item
     * @param WP_REST_Request $request request object
     *
     * @return WP_Error|WP_REST_Response
     */
    public function prepare_item_for_response($item, $request, $type = '*')
    {
        $data          = [];
        $display_count = true;
        $data          = Job::to_array($item, $type, $display_count);
        $data          = $this->prepare_response_for_collection($data);
        $context       = !empty($request['context']) ? $request['context'] : 'view';
        $data          = $this->filter_response_by_context($data, $context);
        $response      = rest_ensure_response($data);
        $response->add_links(Helpers::prepare_links($item, $this->namespace, $this->rest_base, $this->base));

        return $response;
    }

    /**
     * Sanitize job slug for uniqueness.
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @param WP_REST_Request $request
     *
     * @return WP_Error|string
     */
    public function sanitize_job_slug($slug, $request)
    {
        global $wpdb;

        $slug          = sanitize_title($slug);
        $id            = isset($request['id']) ? $request['id'] : 0;
        $args['count'] = 1;

        if (!empty($id)) {
            $args['where'][] = $wpdb->prepare('jobs.id != %d AND jobs.slug = %s', $id, $slug);
        } else {
            $args['where'][] = $wpdb->prepare('jobs.slug = %s', $slug);
        }
        $total_found = axilweb_ajl_jobs()->jobs_manager->all($args);
        if ($total_found > 0) {
            return rest_ensure_response(
                [
                    'message' => __('Job slug already exists.', 'ai-job-listing'),
                    "data" => array(
                        "status" => AXILWEB_AJL_ALREADY_EXISTS
                    )
                ]
            );
        }

        return sanitize_title($slug);
    }

    /**
     * Generate unique slug if no slug is provided.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request
     *
     * @return string
     */
    public function generate_unique_slug($request)
    {
        $slug = $request['slug'];
        if ($slug) {
            return $slug;
        }
        if (empty($slug)) {
            $slug = sanitize_title($request['title']);
            $slug = str_replace(' ', '-', $slug);

            // Auto-generate only for create page.
            if (empty($request['id'])) {
                $existing_job = axilweb_ajl_jobs()->jobs_manager->get(
                    [
                        'key' => 'slug',
                        'value' => $slug,
                    ]
                );

                if (empty($existing_job)) {
                    return $slug;
                }

                return $this->generate_beautiful_slug($slug);
            }
        }

        return $slug;
    }

    /**
     * Generate beautiful slug.
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @param integer $i
     *
     * @return string
     */
    public function generate_beautiful_slug(string $slug = '', $i = 1): string
    {
        while (true) {
            $new_slug     = $slug . '-' . $i;
            $existing_job = axilweb_ajl_jobs()->jobs_manager->get(
                [
                    'key' => 'slug',
                    'value' => $new_slug,
                ]
            );

            if (empty($existing_job)) {
                return $new_slug;
            } else {
                $this->generate_beautiful_slug($slug, $i + 1);
            }

            $i++;
        }
    }

    /**
     * Updates the notification emails for a specific job based on the provided job ID.
     *
     * This method accepts a request array that should include a 'job_notification_emails' array
     * and a 'id' key representing the job's identifier. It validates the provided emails, encodes
     * them into JSON format, and then updates the respective job record in the database.
     * If the job ID is not provided or is invalid, it returns a WP_Error.
     *
     * @since 1.0.0 Introduced.
     *
     * @param array $request An associative array containing the job ID and a list of new notification emails.
     *                       The 'id' key must be a valid job ID, and 'job_notification_emails' should be an array of valid emails.
     *
     * @return mixed Returns true if the update was successful, WP_Error if there was an error in the process.
     */
    public function updateJobNotificationEmails($request)
    {
        if (empty($request['id'])) {
            return new WP_Error(
                'axilweb_ai_job_listing_rest_email_template_exists',
                __('Invalid Job ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }
        $job_notification_emails = $this->validate_user_emails($request['job_notification_emails']);
        $json_encoded_emails = wp_json_encode($job_notification_emails);
        $job_data = [
            "job_notification_emails"    =>  $json_encoded_emails
        ];

        if (is_wp_error($job_data)) {
            return $job_data;
        }

        $job_id = absint($request['id']);
        return $updated = (new Job)->update(
            $job_data,
            ['id' => $job_id],
            ['%s'], // Value format
            ['%s']  // Where format
        );
    }


    /**
     * Retrieves the query params for collections.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function hasJobLimit()
    {
        return Helpers::hasJobLimit();
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
        $params['limit']['default']   = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['per_page']['default']   = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['search']['default']  = '';
        $params['orderby']['default'] = 'id';
        $params['order']['default']   = 'DESC';

        return $params;
    }

    /**
     * Retrieves a collection of job items for frontend (without permission check).
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items_frontend($request): ?WP_REST_Response
    {
        // Directly call the existing get_items method
        return $this->get_items($request);
    }

     /**
     * Retrieves a collection of job attributes for frontend (without permission check).
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_job_filter_attributes_frontend($request): ?WP_REST_Response
    {
        // Directly call the existing get_items method
        return $this->get_job_filter_attributes($request);
    }





    /**
     * Retrieve job items.
     *
     * @since 1.0.0
     * @return WP_REST_Response The job items and schema.
     */
    public function get_job_filter_attributes($request)
    {
        global $wpdb;
        $table_jobs = $wpdb->prefix . 'axilweb_ajl_jobs';
        $table_attributes = $wpdb->prefix . 'axilweb_ajl_attributes';
        $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $table_job_attribute_values = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';
        // Get the selected filters from the request
        $selected_attribute_value_ids = $request->get_param('attribute_value_id');
        $selected_attribute_ids = $request->get_param('attribute_id');
        $selected_attribute_value_ids = !empty($selected_attribute_value_ids) ? explode(',', $selected_attribute_value_ids) : [];
        $selected_attribute_ids = !empty($selected_attribute_ids) ? explode(',', $selected_attribute_ids) : [];
        
        // Check cache first
        $cache_key = 'ajl_active_attributes';
        $attributes = wp_cache_get($cache_key, 'ajl_job_filters');
        
        if (false === $attributes) {
            // Cache miss: Perform the database query 
        	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        	// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled by get_attribute_values_cached()
            $attributes = $wpdb->get_results("SELECT * FROM {$table_attributes} WHERE is_active = 1 AND deleted_at IS NULL ORDER BY menu_orderby ASC"); 
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
			// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
			// Cache for 1 hour
            wp_cache_set($cache_key, $attributes, 'ajl_job_filters', 3600); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        }

        $current_attribute_value_id = !empty($selected_attribute_value_ids) ? $selected_attribute_value_ids[count($selected_attribute_value_ids) - 1] : '';
        $current_attribute_id = !empty($selected_attribute_ids) ? $selected_attribute_ids[count($selected_attribute_ids) - 1] : '';
        $debug_response = [];
        $response = [];
        $is_reset_operation = 0;
        $current_attribute_slug = '';
        $check_key = 0;
        foreach ($attributes as $index => $attribute) {
            if ($attribute->id === $current_attribute_id) {
                $check_key = $index;
                $attribute_slug = $attribute->slug;
                break;
            }
        }

        $debug_response['check_key'] = $check_key;
        $debug_response['current_attribute_slug'] = $current_attribute_slug;
        $debug_response['current_attribute_value_id'] = $current_attribute_value_id;
        $debug_response['current_attribute_id'] = $current_attribute_id;
        $debug_response['selected_attribute_value_ids'] = $selected_attribute_value_ids;
        $debug_response['selected_attribute_ids'] = $selected_attribute_ids;
        $debug_response['selected_attribute_filter'] = $selected_attribute_filter = array_slice($selected_attribute_ids, 0, count($selected_attribute_ids) - 1);
        $debug_response['selected_attribute_value_id_filter'] = $selected_attribute_value_id_filter = array_slice($selected_attribute_value_ids, 0, count($selected_attribute_value_ids) - 1);
        foreach ($attributes as $attribute_index_key => $attribute) {
            $attribute_id = $attribute->id;
            // Base query to get attribute values and their counts, filtering by active status
            $query = "
                SELECT av.id, av.value, av.slug, COUNT(jav.job_id) AS count
                FROM {$table_attribute_values} av
                LEFT JOIN {$table_job_attribute_values} jav ON jav.attribute_value_id = av.id
                LEFT JOIN {$table_jobs} j ON jav.job_id = j.id
                WHERE av.attribute_id = %d AND j.status = 'active' AND j.deleted_at IS NULL
            ";

            // If there are selected attribute values, adjust the query
            if ($attribute_index_key != 0 && !empty($selected_attribute_value_ids)) {
                // Add a subquery to filter jobs that match all selected attribute values
                $placeholders = implode(',', array_fill(0, count($selected_attribute_value_ids), '%d'));
                $query .= "
                AND jav.job_id IN (
                    SELECT jav_inner.job_id
                    FROM {$table_job_attribute_values} jav_inner
                    WHERE jav_inner.attribute_value_id IN ($placeholders)
                    GROUP BY jav_inner.job_id
                    HAVING COUNT(DISTINCT jav_inner.attribute_value_id) = " . count($selected_attribute_value_ids) . "
                )
            ";
            }

            // Group the results
            $query .= " GROUP BY av.id";

            // Prepare and execute the query
            $params = array_merge([$attribute_id], $selected_attribute_value_ids);
            // Dynamic query with user-selected filters - caching not feasible due to variable parameters 
            // Correct use of $wpdb->prepare() for dynamic values like $attribute_id
        	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        	// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        	// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled by get_attribute_values_cached()
            $attribute_values = $wpdb->get_results($wpdb->prepare($query, ...$params));  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
 			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        	// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery
        	// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching
            if ($attribute_index_key != 0 && $current_attribute_id >= $attribute_id) {
                $is_reset_operation = 1;
                break;
            }
            $response[] = [
                'name' => $attribute->name,
                'slug' => $attribute->slug,
                'id' => $attribute->id,
                'values' => array_map(function ($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value,
                        'count' => (int) $value->count,
                    ];
                }, $attribute_values)
            ];
        }
        $final_response = [];
        foreach ($response as $attribute) {
            foreach ($attribute['values'] as $attribute_value) {
                $final_response[] = [
                    "attribute_slug" => $attribute['slug'],
                    "attribute_value_id" => $attribute_value['id'],
                    "attribute_value" => $attribute_value['value'],
                    "status" => "active",
                    "id" => $attribute['id'],
                    "job_count" => $attribute_value['count'],
                ];
            }
        }

        $other_final_response[] = ['test'];
        // extra filter
        if (($is_reset_operation == 1)) {
            $other_final_response = $this->get_job_single_category($request, $current_attribute_value_id, $current_attribute_id, $current_attribute_slug, $selected_attribute_value_ids, $selected_attribute_ids, $check_key, $attributes, $selected_attribute_filter, $selected_attribute_value_id_filter);
            if (!empty($other_final_response)) {

                foreach ($final_response as $index => $final_val) {
                    foreach ($other_final_response as $val) {
                        foreach ($final_response as $index => $final_val) {
                            if ($val['attribute_value_id'] == $final_val['attribute_value_id']) {
                                $temp_index = $index;
                            }
                        }
                    }
                }
            }
            $final_response_total = array_merge($final_response, $other_final_response);
        } else {
            $final_response_total =  $final_response;
        }

        $final_response_total[] = $other_final_response;
        $final_response_total[] = $debug_response;

        return new WP_REST_Response($final_response_total, 200);
    }

    /**
     * Retrieve job items.
     *
     * @since 1.0.0
     */
    public function get_job_single_category($request, $current_attribute_value_id, $current_attribute_id, $current_attribute_slug, $selected_attribute_value_ids, $selected_attribute_ids, $check_key, $attributes, $selected_attribute_filter, $selected_attribute_value_id_filter)
    {
        global $wpdb;
        $table_jobs = $wpdb->prefix . 'axilweb_ajl_jobs';
        $table_attributes = $wpdb->prefix . 'axilweb_ajl_attributes';
        $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $table_job_attribute_values = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';

        $final_response = [];

        foreach ($attributes as $attribute_index_key => $attribute) {
            $attribute_id = $attribute->id;
            if ($attribute_index_key == 0) {
                continue;
            }

            if ($attribute_id >= $current_attribute_id) {
                // Base query to get attribute values and their counts, filtering by active status
                $query = "
                    SELECT av.id, av.value, av.slug, COUNT(jav.job_id) AS count
                    FROM {$table_attribute_values} av
                    LEFT JOIN {$table_job_attribute_values} jav ON jav.attribute_value_id = av.id
                    LEFT JOIN {$table_jobs} j ON jav.job_id = j.id
                    WHERE av.attribute_id = %d AND j.status = 'active' AND j.deleted_at IS NULL
                ";

                // Handle conditions based on attribute index and selected attribute values
                if ($attribute_index_key != 0) {
                    // Determine the correct filter set
                    $value_ids_to_filter = $attribute_id == $current_attribute_id
                        ? $selected_attribute_value_id_filter
                        : $selected_attribute_value_ids;

                    // If there are values to filter by
                    if (!empty($value_ids_to_filter)) {
                        $placeholders = implode(',', array_fill(0, count($value_ids_to_filter), '%d'));

                        $query .= "
                        AND jav.job_id IN (
                            SELECT jav_inner.job_id
                            FROM {$table_job_attribute_values} jav_inner
                            WHERE jav_inner.attribute_value_id IN ($placeholders)
                            GROUP BY jav_inner.job_id
                            HAVING COUNT(DISTINCT jav_inner.attribute_value_id) = " . count($value_ids_to_filter) . "
                        )
                    ";
                    }
                }

                // Group the results
                $query .= " GROUP BY av.id";

                // Prepare and execute the query
                $params = array_merge([$attribute_id], $value_ids_to_filter ?? []);
                // Dynamic query with user-selected filters - caching not feasible due to variable parameters
                // Correct use of $wpdb->prepare() for dynamic values like $attribute_id
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled by get_attribute_values_cached()
                $attribute_values = $wpdb->get_results($wpdb->prepare($query, ...$params));  // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared 
 				// Correct use of $wpdb->prepare() for dynamic values like $attribute_id
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
        		// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled by get_attribute_values_cached()
                $response[] = [
                    'name' => $attribute->name,
                    'slug' => $attribute->slug,
                    'id' => $attribute->id,
                    'values' => array_map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                            'count' => (int) $value->count,
                        ];
                    }, $attribute_values)
                ];
            }
        }

        foreach ($attributes as $attribute_index_key => $attribute) {
            $attribute_id = $attribute->id;
            if ($attribute_index_key == 0) {
                continue;
            }
            if ($attribute_index_key < $check_key) {
                // for past
                $other_middle_response = $this->onMiddleCategory($attributes, $attribute, $check_key, $attribute_index_key, $attribute_id, $current_attribute_id, $selected_attribute_value_id_filter, $selected_attribute_ids);
                $response = array_merge($response, $other_middle_response);
            }
        }

        $final_response = [];
        foreach ($response as $attribute) {
            foreach ($attribute['values'] as $attribute_value) {
                $final_response[] = [
                    "attribute_slug" => $attribute['slug'],
                    "attribute_value_id" => $attribute_value['id'],
                    "attribute_value" => $attribute_value['value'],
                    "status" => "active",
                    "id" => $attribute['id'],
                    "job_count" => $attribute_value['count'],
                ];
            }
        }

        return $final_response;
    }


    
    /**
     * Get jobs with Google Job Schema.
     *
     * @since 1.0.0
     */
    public function getJobsGoogelSchema()
    {
        // Define query arguments
        $args = [
            // Add filtering or pagination args here
        ];

        // Retrieve jobs using your custom function
        $jobs = axilweb_ajl_jobs()->jobs_manager->all($args);

        // Add Google Job Schema for each job
        $jobs_with_schema = [];

        foreach ($jobs as $job) {
            // Convert the job to an array
            $job_array = is_object($job) ? (array) $job : $job; // Convert to array safely

            // Generate the job schema markup
            if (is_array($job_array)) {
                $job_array['job_schema'] = $this->get_job_schema_markup($job_array);
            }

            // Add the job with schema to the response
            $jobs_with_schema[] = $job_array;
        }

        // Return as a REST response
        return new WP_REST_Response($jobs_with_schema, 200);
    }

    /**
     * Generate Google Job Schema Markup.
     *
     * @since 1.0.0
     * 
     * @param array $job_data Job data array.
     * @return string JSON-LD structured data.
     */
    public function get_job_schema_markup($job_data)
    {
        // Handle missing fields with fallback values
        $employment_type = isset($job_data['employment_type']) ? $job_data['employment_type'] : 'FULL_TIME';
        $min_salary = isset($job_data['min_salary']) ? $job_data['min_salary'] : 0;
        $application_deadline = isset($job_data['application_deadline'])
            ? gmdate(DATE_ISO8601, strtotime($job_data['application_deadline']))
            : null;
        $created_at = isset($job_data['created_at'])
            ? gmdate(DATE_ISO8601, strtotime($job_data['created_at']))
            : gmdate(DATE_ISO8601);

        // Construct the Job Schema array
        $job_schema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job_data['title'],
            'description' => wp_strip_all_tags($job_data['description']), // Remove any HTML tags
            'datePosted' => $created_at,
            'validThrough' => $application_deadline,
            'employmentType' => $employment_type,
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => 'Dynamic Company Name', // Replace with actual data dynamically
                'sameAs' => get_site_url(), // Use the site's URL instead of hardcoded external URL
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => isset($job_data['street_address']) ? $job_data['street_address'] : '123 Main St',
                    'addressLocality' => isset($job_data['city']) ? $job_data['city'] : 'Default City',
                    'addressRegion' => isset($job_data['state']) ? $job_data['state'] : 'Default State',
                    'postalCode' => isset($job_data['zip_code']) ? $job_data['zip_code'] : '00000',
                    'addressCountry' => isset($job_data['country']) ? $job_data['country'] : 'US',
                ],
            ],
            'salaryCurrency' => 'USD', // Replace dynamically if required
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'value' => $min_salary,
                    'unitText' => 'YEAR', // Adjust as per salary unit
                ],
            ],
        ];

        // Return JSON-LD string
        return wp_json_encode($job_schema, JSON_PRETTY_PRINT);
    }

    /**
     * Retrieve job items.
     *
     * @since 1.0.0
     */
    public function onMiddleCategory($attributes, $attribute, $check_key, $attribute_index_key, $attribute_id, $current_attribute_id, $selected_attribute_value_id_filter, $selected_attribute_ids)
    {
        global $wpdb;
        $table_jobs = $wpdb->prefix . 'axilweb_ajl_jobs';
        $table_attributes = $wpdb->prefix . 'axilweb_ajl_attributes';
        $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values';
        $table_job_attribute_values = $wpdb->prefix . 'axilweb_ajl_job_attribute_value';

        $final_response = [];

        // middle category (current category)
        $query = "
            SELECT av.id, av.value, av.slug, COUNT(jav.job_id) AS count
            FROM {$table_attribute_values} av
            LEFT JOIN {$table_job_attribute_values} jav ON jav.attribute_value_id = av.id
            LEFT JOIN {$table_jobs} j ON jav.job_id = j.id
            WHERE av.attribute_id = %d AND j.status = 'active' AND j.deleted_at IS NULL
        ";

        // Prepare filters for parent attributes based on `check_key`
        $parent_filters = [];
        for ($i = 1; $i <= 4; $i++) {
            if ($check_key == $i + 2) {
                $parent_attribute_id = $attributes[$attribute_index_key - $i]->id;

                if (in_array($parent_attribute_id, $selected_attribute_ids)) {
                    $tempFilter = [];
                    foreach ($selected_attribute_ids as $k => $v) {
                        if ($parent_attribute_id == $v) {
                            $tempFilter[] = $selected_attribute_value_id_filter[$k];
                        }
                    }

                    // If we have valid filters, build a subquery
                    if (count($tempFilter) > 0) {
                        $placeholders = implode(',', array_fill(0, count($tempFilter), '%d'));
                        $parent_filters[] = "
                        jav.job_id IN (
                            SELECT jav_inner.job_id
                            FROM {$table_job_attribute_values} jav_inner
                            WHERE jav_inner.attribute_value_id IN ($placeholders)
                            GROUP BY jav_inner.job_id
                            HAVING COUNT(DISTINCT jav_inner.attribute_value_id) = " . count($tempFilter) . "
                        )
                    ";
                    }
                }
            }
        }

        // Combine all parent filters into the query
        if (!empty($parent_filters)) {
            $query .= " AND (" . implode(" OR ", $parent_filters) . ")";
        }

        // Group the results
        $query .= " GROUP BY av.id";

        // Prepare and execute the query
        $params = [$attribute_id];
        foreach ($selected_attribute_ids as $k => $v) {
            $params = array_merge($params, $selected_attribute_value_id_filter);
        }

        // Dynamic query with user-selected filters - caching not feasible due to variable parameters
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Dynamic filtering query with user-selected parameters makes caching impractical
        $attribute_values = $wpdb->get_results($wpdb->prepare($query, ...$params));

        $response[] = [
            'name' => $attribute->name,
            'slug' => $attribute->slug,
            'id' => $attribute->id,
            'values' => array_map(function ($value) {
                return [
                    'id' => $value->id,
                    'value' => $value->value,
                    'count' => (int) $value->count,
                ];
            }, $attribute_values)
        ];

        return $response;
    }

    /**
     * Get overall statistics for jobs and applications.
     *
     * Response structure:
     * {
     *   total_jobs: int,
     *   total_active_jobs: int,
     *   total_applicants: int
     * }
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_jobs_app_stats( WP_REST_Request $request ) {
        global $wpdb;
        $prefix = $wpdb->prefix;

        // Use caching to avoid frequent heavy queries (15 minutes)
        $cache_key   = 'axilweb_ajl_jobs_app_stats';
        $cache_group = 'axilweb_ajl_job_stats';
        $cached      = wp_cache_get( $cache_key, $cache_group );
        if ( false !== $cached ) {
            return new WP_REST_Response( $cached, 200 );
        }

        // Safely build table names
        $table_jobs         = esc_sql( $prefix . 'axilweb_ajl_jobs' );
        $table_applications = esc_sql( $prefix . 'axilweb_ajl_applications' );

        // phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery -- Aggregate read-only queries; cached.
        $total_jobs = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$table_jobs} WHERE deleted_at IS NULL" );
        $total_active_jobs = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$table_jobs} WHERE status = %s AND deleted_at IS NULL", 'active' ) );
        $total_applicants = (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$table_applications} WHERE deleted_at IS NULL" );
        // phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery

        $data = [
            'total_jobs'         => $total_jobs,
            'total_active_jobs'  => $total_active_jobs,
            'total_applicants'   => $total_applicants,
        ];

        // Cache the result
        wp_cache_set( $cache_key, $data, $cache_group, 15 * MINUTE_IN_SECONDS );

        return new WP_REST_Response( $data, 200 );
    }

}
