<?php

namespace Axilweb\AiJobListing\Rest;

use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Application;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}
/**
 * API Attributes_Controller class.
 *
 * @since 0.1.0
 */
class Applications_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'apply-job';
    
    /**
     * Get application reports by job ID with caching.
     *
     * Retrieves application reports from cache first, or from the database if not cached.
     * Handles caching the result for improved performance.
     *
     * @since 1.0.0
     * @param int $job_id The job ID to fetch reports for
     * @return array Application reports data
     */
    private function get_application_reports_cached($job_id) {
        // Create cache key
        $cache_key = 'axilweb_ajl_app_reports_' . $job_id;
        $cache_group = 'axilweb_ajl_application_reports';
        
        // Try to get from cache first
        $results = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $results) {
            $results = $this->_query_get_application_reports($job_id);
            
            // Cache the results
            if ($results) {
                wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $results;
    }
    
    /**
     * Query to get application reports by job ID.
     * Implementation method for get_application_reports_cached().
     *
     * This is a specific implementation for querying the custom applications table,
     * which requires a direct database query.
     *
     * @since 1.0.0
     * @param int $job_id The job ID to fetch reports for
     * @return array Application reports data
     */
    private function _query_get_application_reports($job_id) {
        global $wpdb;
        $table_name = $wpdb->axilweb_ajl_applications;
        
        // Direct query is necessary for accessing the custom applications table
        // Caching is handled by the parent method get_application_reports_cached()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->get_results( $wpdb->prepare( "SELECT id, DATE(created_at) as 'date' FROM %i WHERE job_id = %d", $table_name, $job_id ), ARRAY_A );
    }

    /**
     * Register REST API Routes.
     *
     * Registers multiple REST API routes for handling various operations related to applications,
     * such as creation, deletion, restoration, retrieval, and status counting. Each route is
     * defined with its HTTP method, callback function, permission checks, and request arguments.
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
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'create_item'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                    'permission_callback' => '__return_true',
                    'schema'              => [$this, 'get_item_schema'],
                    
                ],
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [$this, 'delete_items'],
                    'permission_callback' => [$this, 'check_permission'],
                    'schema'              => [$this, 'get_item_schema'],
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
            '/applications-restore',
            [

                [
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => [$this, 'restoreDeletedItems'],
                    'permission_callback' => [$this, 'check_permission'],
                    'schema'              => [$this, 'get_item_schema'],
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

        register_rest_route($this->namespace, '/application-lists', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_items'],
                'permission_callback' => [$this, 'check_permission'],
                'args'                => $this->get_collection_params(),
                'schema'              => [$this, 'get_item_schema'],
            ]
        ]);

        register_rest_route($this->namespace, '/application-report', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'getItemsReports'],
                'permission_callback' => [$this, 'check_permission'],
                'args'                => $this->get_collection_params(),
                'schema'              => [$this, 'get_item_schema'],
                 
            ]
        ]);
        register_rest_route($this->namespace, '/application-item-update', [
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [$this, 'isSingleUpdateItem'],
                'permission_callback' => [$this, 'check_permission'],
                'args'                => $this->get_collection_params(),
                'schema'              => [$this, 'get_item_schema'],
            ]
        ]);

        register_rest_route($this->namespace, '/count-application-by-status', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'count_application_by_status'],
                'permission_callback' => [$this, 'check_permission'],        
                'args'                => $this->get_collection_params(),
                'schema'              => [$this, 'get_item_schema'],
            ]
        ]);

         // Frontend route without permission check
         register_rest_route(
            $this->namespace,
            '/' . $this->base . '-frontend/',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items_frontend'],
                    'args'                => $this->get_collection_params(),
                    'schema'              => [$this, 'get_item_schema'],
                    'permission_callback' => '__return_true', // Use this for public access
                    
                ],
            ]
        );
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
     * Updates the process ID for a specific application.
     *
     * This function updates the `process_id` and `previous_process_id` for a given application.
     * It handles special logic for rejected applications and ensures proper sanitization of inputs.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|WP_Error The response indicating the result of the update operation.
     */
    public function updateApplicationProcessId($request)
    {
        if (empty($request['id'])) {
            return new WP_Error(
                'axilweb_ajl_rest_application_id_exists',
                __('Invalid application ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }

        $application_id = absint($request['id']);
        $application = (new Application)->get($application_id);

        $application_current_process_id  = $application->process_id;
        $previous_process_id = $application_current_process_id;
        $request_process_id = Helpers::sanitize($request['process_id'], 'number');
        $process_id = $request_process_id;

        $rejected_process_id = AXILWEB_AJL_REJECT_PROCESS_ID;
        $is_previous_process_id_was_rejected = $application_current_process_id && $application_current_process_id == $rejected_process_id;

        # following logical expression for handling reject job to previous job
        if ($is_previous_process_id_was_rejected) {
            $process_id = $application->previous_process_id ?? $request_process_id;
            $previous_process_id = $rejected_process_id;
        }

        $data = [
            'process_id' => $process_id,
            'previous_process_id' => $previous_process_id,
        ];
        $updated = (new Application)->update(
            $data,
            [
                'id' => $application_id,
            ],
            [
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );

        if (!$updated) {
            return new \WP_Error('axilweb-ajl-application-update-failed', __('Failed to update application.', 'ai-job-listing'));
        }

        do_action('axilweb_ajl_single_application_updated', $application_id, $data);
        $type = 'process_id';
        $response = $this->prepare_item_for_response($request, $request, $type);
        $response = rest_ensure_response($response);
        $response->set_status(200);
        return $response;
    }

    /**
     * Update a Single Application Item.
     *
     * Handles the REST API request to update a single application item based on the provided ID.
     * Supports updating specific fields such as `process_id` or `is_read`. Returns the updated 
     * application data as a REST API response.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing the data to update.
     *
     * @return WP_REST_Response|WP_Error A REST API response with the updated application data or
     *                                    a WP_Error object on failure.
     */
    public function isSingleUpdateItem($request)
    {
        if (empty($request['id'])) {
            return new WP_Error(
                'axilweb-ajl-rest-application-id-exists',
                __('Invalid application ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }

        if (isset($request['process_id']) && !empty($request['process_id'])) {
            return $this->updateApplicationProcessId($request);
        }

        if (isset($request['is_read']) && !empty($request['is_read'])) {
            $type =  'is_read';
            $application_data = [
                "is_read"      => Helpers::sanitize($request["is_read"], 'number'),
            ];
        }


        if (is_wp_error($application_data)) {
            return $application_data;
        }

        $application_id = absint($request['id']);
        $updated = (new Application)->update(
            $application_data,
            [
                'id' => $application_id,
            ],
            [
                '%d',

            ],
            [
                '%d',
            ]
        );

        if (!$updated) {
            return new \WP_Error('axilweb-ajl-application-update-failed', __('Failed to update application.', 'ai-job-listing'));
        }

        do_action('axilweb_ajl_single_application_updated', $application_id, $application_data);
        $response = $this->prepare_item_for_response($request, $request, $type);
        $response = rest_ensure_response($response);
        $response->set_status(200);
        return $response;
    }

    /**
     * Delete Multiple Applications.
     *
     * Handles the REST API request to delete multiple applications based on their IDs.
     * Deletes the specified application IDs and returns a success response if the operation
     * is successful. Returns an error if no IDs are provided or if no applications were deleted.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing the IDs of applications to delete.
     *
     * @return WP_REST_Response|WP_Error A REST API response with the number of deleted applications or
     *                                    a WP_Error object if the operation fails.
     */
    public function delete_items($request)
    {


        if (!isset($request['ids'])) {
            return new WP_Error('axilweb-ajl-no-ids', __('No Application ids found.', 'ai-job-listing'), ['status' => 400]);
        }

        $deleted = axilweb_ajl_jobs()->job_applications_manager->delete($request['ids']);

        if ($deleted) {
            $message = __('Application deleted successfully.', 'ai-job-listing');
            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $deleted,
                ]
            );
        }

        return new WP_Error('axilweb-ajl-no-application-deleted', __('No application deleted. Application has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
    }

    /**
     * Restore Deleted Applications.
     *
     * Handles the REST API request to restore deleted applications based on their IDs.
     * Restores the specified application IDs by reversing the deletion process and 
     * returns a success response if the operation is successful. Returns an error if 
     * no IDs are provided or if the restore operation fails.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing the IDs of applications to restore.
     *
     * @return WP_REST_Response|WP_Error A REST API response with the number of restored applications or
     *                                    a WP_Error object if the operation fails.
     */
    public function restoreDeletedItems($request)
    {

        if (!isset($request['ids'])) {
            return new WP_Error('no_ids', __('No job ids found.', 'ai-job-listing'), ['status' => 400]);
        }

        $restore = axilweb_ajl_jobs()->job_applications_manager->delete($request['ids'], 'restore');

        if ($restore) {
            $message = __('Applications restore successfully.', 'ai-job-listing');

            return rest_ensure_response(
                [
                    'message' => $message,
                    'total' => $restore,
                ]
            );
        }

        return new WP_Error('axilweb-ajl-no-application-meta-deleted', __('No application deleted. Application has already been deleted. Please try again.', 'ai-job-listing'), ['status' => 400]);
    }

    /**
     * Count Applications by Status.
     *
     * Handles the REST API request to count applications based on their job ID, process ID, and action type.
     * Returns the count of applications that match the given criteria. If no records are found, it returns
     * an appropriate error response.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing the job ID, process ID, and action type.
     *
     * @return WP_REST_Response|WP_Error A REST API response with the count of applications or
     *                                    a WP_Error object if no records are found.
     */
    public function count_application_by_status($request)
    {
        $job_id = (isset($request['job_id']) && !empty($request['job_id'])) ? $request['job_id'] : "";
        $process_id = (isset($request['process_id']) && !empty($request['process_id'])) ? $request['process_id'] : AXILWEB_AJL_DEFAULT_PROCESS_ID;
        $action = (isset($request['action']) && $request['action'] == 'click') ? 'click' : "load";
        $count_application_by_status = axilweb_ajl_jobs()->job_applications_manager->count_application_by_status($job_id, $process_id, $action);
        if (!$count_application_by_status) {
            return new WP_Error('axilweb-ajl-job-rest-application-no_records_found', __('No records found!', 'ai-job-listing'), ['status' => AXILWEB_AJL_NO_RECORDS_FOUND]);
        }
        return rest_ensure_response($count_application_by_status);
    }

 

    /**
     * Create a New Application.
     *
     * Handles the REST API request to create a new application. Checks if an application ID 
     * already exists and prevents duplicate entries. Prepares the application data, inserts it 
     * into the database, sends an email notification, and optionally creates a user.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing application data.
     *
     * @return WP_REST_Response|WP_Error A REST API response with the created application data or 
     *                                    a WP_Error object if the operation fails.
     */
    public function create_item($request)
    {
        if (!empty($request['id'])) {
            return rest_ensure_response(
                [
                    'message' => __('Cannot create data.', 'ai-job-listing'),
                    "data" => array(
                        "status" => AXILWEB_AJL_ALREADY_EXISTS
                    )
                ]
            );
        }

        $prepared_data = Application::prepare_data_for_database($request);
        if (is_wp_error($request)) {
            return $request;
        }

        // Insert the application.
        $application_id = axilweb_ajl_jobs()->job_applications_manager->create($prepared_data, $request);
        $job_id = $request['job_id'];
        $email_type_slug = 'appointment';
        Helpers::sendEmailForApplication($job_id, $application_id, $email_type_slug);
        

        if (is_wp_error($application_id)) {
            return $application_id;
        }
      
        $response = rest_ensure_response($prepared_data);
        $response = Helpers::rest_response($response, 201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $application_id)));
        return $response;
    }

    /**
     * Retrieve a List of Job Applications.
     *
     * Handles the REST API request to fetch job applications based on the provided filters and parameters.
     * Supports pagination, filtering by job ID, search term, status, date, report type, and read status.
     * Returns the applications as a paginated REST API response.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing the filtering and pagination parameters.
     *
     * @return WP_REST_Response|null The REST API response with a list of job applications or null if no data is found.
     */
    public function get_items($request): ?WP_REST_Response
    {
        $args   = [];
        if (isset($request['id']) && !empty($request['id'])) {
            $args['id'] = $request['id'];
        }
        if (isset($request['job_id']) && !empty($request['job_id'])) {
            $args['job_id'] = $request['job_id'];
        }
        if (isset($request['search']) && !empty($request['search'])) {
            $args['search'] = $request['search'];
        }
        if (isset($request['status']) && !empty($request['status'])) {
            $args['status'] = $request['status'];
        }
        if (isset($request['date']) && !empty($request['date'])) {
            $args['date'] = $request['date'];
        }
        if (isset($request['report']) && !empty($request['report'])) {
            $args['report'] = $request['report'];
        }
        if (isset($request['is_read']) && !empty($request['is_read'])) {
            $args['is_read'] =  Helpers::sanitize($request["is_read"], 'number');
        }

        $data   = [];

        $params = $this->get_collection_params();
        foreach ($params as $key => $value) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }

        $jobs = axilweb_ajl_jobs()->job_applications_manager->all($args);

        foreach ($jobs as $job) {
            $response = $this->prepare_item_for_response($job, $request);
            $data[]   = $this->prepare_response_for_collection($response);
        }

        $args_with_count = array_merge($args, ['count' => true]);
        $total = axilweb_ajl_jobs()->job_applications_manager->all($args_with_count);

        $max_pages     = ceil($total / (int) $args['limit']);
        $response      = rest_ensure_response($data);
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
    public function getItemsReports($request)
    {
        if (empty($request['job_id'])) {
            return new WP_Error(
                'axilweb-ajl-rest-job-id-exists',
                __('Invalid job ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }
        
        $job_id = absint($request['job_id']);
        
        // Use our cached helper method to get application reports
        $results = $this->get_application_reports_cached($job_id);
        
        return rest_ensure_response($results);
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

        $data       = [];
        $data       = Application::to_array($item, $type, $request['status']);
        $data       = $this->prepare_response_for_collection($data);
        $context    = !empty($request['context']) ? $request['context'] : 'view';
        $data       = $this->filter_response_by_context($data, $context);
        $response   = rest_ensure_response($data);
        $response->add_links(Helpers::prepare_links($item, $this->namespace, $this->rest_base, $this->base));

        return $response;
    }

    /**
     * Define Collection Parameters for REST API.
     *
     * Extends the parent method to customize and define collection parameters used for 
     * fetching multiple resources in the REST API. Adds default values for `limit` and `s`.
     *
     * @since 1.0.0
     *
     * @return array An array of collection parameters with their configurations.
     */
    public function get_collection_params(): array
    {
        $params = parent::get_collection_params();
        $params['limit']['default'] = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['s']['default']     = '';

        return $params;
    }
}
