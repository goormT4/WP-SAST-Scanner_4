<?php

namespace Axilweb\AiJobListing\Rest; 
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use Axilweb\AiJobListing\Data\Data;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Attribute_Values;
use Axilweb\AiJobListing\Abstracts\Rest_Controller;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API Attribute_Values_Controller class.
 *
 * @since 0.1.0
 */
class Attribute_Values_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'attribute-values';

    /**
     * Get attribute value by args with caching.
     *
     * Retrieves attribute values from cache first, or from the database if not cached.
     * Handles caching the result for improved performance.
     *
     * @since 1.0.0
     * @param array $args Arguments to search for
     * @return object|null The attribute value object or null if not found
     */
    public function getAttributeValueByArgs( $args = [] ) {
        if (empty($args)) {
            return null;
        }
        
        // Create cache key based on the arguments
        $cache_key = 'axilweb_ajl_attrval_' . md5(serialize($args));
        $cache_group = 'axilweb_ajl_attribute_values';
        
        // Try to get from cache first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $result) {
            $result = $this->_query_get_attribute_value_by_args($args);
            
            // Cache the result if found
            if ($result) {
                wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $result;
    }
    
    /**
     * Query to get attribute value by arguments.
     * Implementation method for getAttributeValueByArgs().
     *
     * This is a specific implementation for querying custom attribute values table,
     * which requires a direct database query to access the custom table structure and
     * dynamically build the query based on provided arguments.
     *
     * @since 1.0.0
     * @param array $args Arguments to search for
     * @return object|null The attribute value object or null if not found
     */
    private function _query_get_attribute_value_by_args( $args = [] ) {
        global $wpdb;
    
        // Define the table name with prefix
        $table_name = $wpdb->prefix . 'axilweb_ajl_attribute_values';
    
        // Set up the WHERE clause based on provided arguments
        $where_clause = [];
        $values = [];
    
        foreach ( $args as $key => $value ) {
            // Sanitize column name for security
            $safe_key = sanitize_key($key);
            // Add the field comparison with appropriate placeholder
            $where_clause[] = "`$safe_key` = %s";  // Placeholder for the value
            $values[] = $value;  // Add the actual value to be prepared
        }
    
        if (!empty($where_clause)) {
            // Create a unique cache key based on the query parameters
            $cache_key = 'axilweb_ajl_attr_value_' . md5($table_name . serialize($where_clause) . serialize($values));
            $cache_group = 'axilweb_ajl_attribute_values';
    
            // Try to get from cache first
            $result = wp_cache_get($cache_key, $cache_group);
    
            // If not in cache, fetch from database
            if (false === $result) {
                // Direct query is necessary for custom table operations
                // Define the base query with a placeholder for conditions
                $base_query = "SELECT * FROM `$table_name` WHERE %%WHERE%% LIMIT 1";
    
                // Replace the placeholder with actual conditions
                $final_query = str_replace('%%WHERE%%', implode(' AND ', $where_clause), $base_query);
    
                // Execute the prepared query with placeholders
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared -- Direct query is necessary for custom table operations with dynamic WHERE conditions
                $result = $wpdb->get_row( $wpdb->prepare($final_query, ...$values) );
    
                // Store in cache for future requests
                wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
            }
        } else {
            // No conditions - return the first row
            // Create a unique cache key for the simple query
            $cache_key = 'axilweb_ajl_attr_value_first_' . md5($table_name);
            $cache_group = 'axilweb_ajl_attribute_values';
    
            // Try to get from cache first
            $result = wp_cache_get($cache_key, $cache_group);
    
            // If not in cache, fetch from database
            if (false === $result) {
                // Simple query without conditions
                $esc_table = esc_sql($table_name);
                
                // Execute the query
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Direct query is necessary for querying custom table; table name is properly escaped with esc_sql()
                $result = $wpdb->get_row("SELECT * FROM `{$esc_table}` LIMIT 1");
    
                // Store in cache for future requests
                wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
            }
        }
    
        // Return the result or null if not found
        return $result ? $result : null;
    }
    
   /**
     * Register REST API Routes.
     *
     * Registers multiple REST API routes for handling CRUD operations and other custom endpoints
     * related to attributes and their values. Defines methods, callbacks, permissions, and arguments
     * for each route.
     *
     * @since 1.0.0
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
                    // 'schema'              => [$this, 'get_item_schema'],
                ],
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'delete_items'],
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
        register_rest_route(
            $this->namespace,
            '/attribute-values-by-attribute-slug/(?P<attribute_slug>[a-zA-Z0-9-]+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'attribute_values_by_attribute_slug'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                ],
            ]
        );
        register_rest_route(
            $this->namespace,
            '/attribute-values-by-attribute',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'attribute_values_by_attribute'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                ],
            ]
        );
        register_rest_route(
            $this->namespace,
            '/attribute-values-delete-permanently',
            [
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'permanently_delete_items'],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                ],
            ]
        );
        register_rest_route(
            $this->namespace,
            '/attribute-values-restore',
            [

                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'restore_deleted_items'],
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
        
         
        if (isset($request['per_page']) && !empty($request['per_page'])) {
            $args['per_page'] = $request['per_page'];
        }

        if (isset($request['orderby']) && !empty($request['orderby'])) {
            $args['orderby'] = $request['orderby'];
        }

        if (isset($request['order']) && !empty($request['order'])) {
            $args['order'] = $request['order'];
        }

        if (isset($request['id']) && !empty($request['id'])) {
            $args['id'] = $request['id'];
        }

        if (isset($request['slug']) && !empty($request['slug'])) {
            $args['slug'] = $request['slug'];
        }

        if (isset($request['status']) && $request['status'] == 'trash') {
            $args['status'] = 'trash';
        } else {
            $args['status'] = 'active';
        }

        if (isset($request['search']) && !empty($request['search'])) {
            $args['search'] = $request['search'];
        }

        $params = $this->get_collection_params();
        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }

        $data['job_attribute_payload'] = array();
        if (isset($request['attribute_slug']) && !empty($request['attribute_slug'])) {
            $attribute_slug = $request['attribute_slug'];
            $attribute_payload = Data::getJobAtributeBySlug($attribute_slug);
            
            $attribute_id = $attribute_payload['attribute_id'];
            if (empty($attribute_payload)) {

                return rest_ensure_response(
                    [
                        'message' => __('No records method.', 'ai-job-listing'),
                        "data" => array(
                            "status" => AXILWEB_AJL_NO_RECORDS_FOUND
                        )
                    ]
                );
            }
            $args['attribute_id'] = $attribute_id;
            $args['attribute_slug'] = $attribute_slug; 
            $jobAttribute = (array) Data::getJobAtributeById($attribute_id);
            $data['job_attribute_payload'] = (array) $jobAttribute;
        }
 
        $attribute_values = axilweb_ajl_jobs()->attributes_values_manager->all($args);
           
 
        $data   = [];
        $val    = [];
        foreach ($attribute_values as $attributes_values_single) {
            $response = $this->prepare_item_for_response($attributes_values_single, $request);
            $val[]  = $this->prepare_response_for_collection($response);
        }

        $data['data'] = $val;
        $args['count'] = 1;

       
        $args_with_count = array_merge($args, ['count' => true]); 
        $total           = axilweb_ajl_jobs()->attributes_values_manager->all($args_with_count); 
        $max_pages     = ceil($total / (int) $args['per_page']);
        $response      = rest_ensure_response($data);
        if (isset($data['job_attribute_payload']) && !empty($data['job_attribute_payload'])) {
            $response      = Helpers::rest_response($data);
        }
        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', (int) $max_pages);
        return $response;
    }

    // The getAttributeValueByArgs method has been moved to the top of the class
    // with proper caching implementation
    
 
    /**
     * Create new attribute value.
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
            return rest_ensure_response(
                [
                    'message' => __('Cannot create existing email template.', 'ai-job-listing'),
                    "status" => AXILWEB_AJL_ALREADY_EXISTS
                ]
            );
        }
        $slug = $request['value']; // value 
        $args = ['attribute_id' => $request['attribute_id'], 'value' => $slug];
        $existing_attribute_value = $this->getAttributeValueByArgs($args);

        if (!empty($existing_attribute_value)) {
            return rest_ensure_response(
                [
                    'message' => __('Cannot create existing Attribute', 'ai-job-listing'),
                    "status" => AXILWEB_AJL_ALREADY_EXISTS
                ]
            );
        }

        $prepared_data = Attribute_Values::prepare_create_item_for_database($request);


        if (is_wp_error($prepared_data)) {
            return $prepared_data;
        }
        // Insert the job.
        $job_attribute_id = axilweb_ajl_jobs()->attributes_values_manager->create($prepared_data);

        if (is_wp_error($job_attribute_id)) {
            return $job_attribute_id;
        }
        // Get job after insert to sending response.
        $response = rest_ensure_response($prepared_data);
        $response = Helpers::rest_response($response, 201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $job_attribute_id)));
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
            return new WP_Error('no_ids', __('No job attribute value ids found.', 'ai-job-listing'), ['status' => 400]);
        }

        $deleted = axilweb_ajl_jobs()->attributes_values_manager->delete($request['ids']);

        if ($deleted) {
            $message = __('Job attribute Trash successfully.', 'ai-job-listing');

            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $deleted,
                    "status" => 200
                     
                ]
            );
        }

        return new WP_Error('axilweb-ajl-no-attribute-values-deleted', __('No job deleted. Job attribute has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
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
            return rest_ensure_response(
                [
                    'message' => __('Invalid job attribute Value ID.', 'ai-job-listing'),
                    "data" => array(
                        "status" => AXILWEB_AJL_NO_RECORDS_FOUND
                    )
                ]
            );
        }

        $prepared_data = Attribute_Values::prepare_update_item_for_database($request);

        if (is_wp_error($prepared_data)) {
            return $prepared_data;
        }

        // Update the attributes_values.
        $attribute_values_id = absint($request['id']);
        $attribute_values_id = axilweb_ajl_jobs()->attributes_values_manager->update($prepared_data, $attribute_values_id);

        if (is_wp_error($attribute_values_id)) {
            return $attribute_values_id;
        }

        $response = rest_ensure_response($prepared_data); 
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $attribute_values_id)));

        return $response;
    }

    /**
     * Delete single or multiple attribute value.
     *
     * @since 1.0.0
     *
     * @param array $request
     *
     * @return WP_REST_Response|WP_Error
     */
    public function restore_deleted_items($request)
    {

        if (!isset($request['ids'])) {
            return new WP_Error('no_ids', __('No job attribute value ids found.', 'ai-job-listing'), ['status' => 400]);
        }
        $restore = axilweb_ajl_jobs()->attributes_values_manager->restore($request['ids']);
        if ($restore) {
            return rest_ensure_response(
                [
                    'message' => __('Job attribute value restore successfully.', 'ai-job-listing'),
                    'total' => $restore,
                    "status" => 200
                     
                ]
            );
        }


        return new WP_Error('axilweb-ajl-no-attribute-values-deleted', __('No job deleted. Job attribute has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
    }

    /**
     * Permanently deletes job attribute values by their IDs.
     *
     * This function deletes multiple job attribute values permanently based on the provided IDs.
     * If no IDs are provided or the deletion fails, appropriate error messages are returned.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|WP_Error The response indicating the result of the operation.
     */
    public function permanently_delete_items($request)
    {

        if (!isset($request['ids'])) {
            return new WP_Error('no_ids', __('No job attribute value ids found.', 'ai-job-listing'), ['status' => 400]);
        }
        $deleted = axilweb_ajl_jobs()->attributes_values_manager->permanent_delete($request['ids']);

        if ($deleted) {
            $message = __('Job attribute value deleted successfully.', 'ai-job-listing');
 
            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $deleted,
                    "status" => 200
                    
                ]
            );
 
        }

        return new WP_Error('axilweb-ajl-no-attribute-values-deleted', __('No job deleted. Job attribute has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
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
    public function prepare_item_for_response($item, $request): ?WP_REST_Response
    {
        $data = [];
        $data       = Attribute_Values::to_array($item);
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
        $params['per_page']['default'] = 10;
        $params['s']['default']     = ''; 
        
        return $params;
    }
}
