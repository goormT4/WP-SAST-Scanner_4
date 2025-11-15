<?php

namespace Axilweb\AiJobListing\Rest; 
use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\App_Process_Comment;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API App_Process_Comment_Controller class.
 *
 * @since 0.1.0
 */
class App_Process_Comment_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'process-comment';

    /**
     * Register all routes related with carts.
     *
     * @return void
     */
    /**
     * Register all routes related with carts.
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
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                ],
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_collection_params(),
                    'schema'              => [$this, 'get_item_schema'],
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
    }
  
   /**
     * Retrieve Process Comments.
     *
     * Handles the REST API request to fetch process comments based on provided query parameters. 
     * Supports filtering by `app_id`, `user_id`, and `app_process_id`. Includes pagination support 
     * and returns the total count and total pages in response headers.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing query parameters.
     *
     * @return WP_REST_Response A REST API response containing the filtered process comments, 
     *                          total count, and total pages.
     */ 
    public function get_items( $request ){
        $args   = [];
        $data   = [];
        $params = $this->get_collection_params();

        foreach ( $params as $key => $value ) {
            if ( isset( $request[ $key ] ) ) {
                $args[ $key ] = $request[ $key ];
            }
        } 
        if (isset($request['app_id']) && !empty($request['app_id'])) {
            $app_id = absint($request['app_id']);  
            $args['where'] = "app_id = $app_id"; 
        }  
        if (isset($request['user_id']) && !empty($request['user_id'])) {
            $user_id = absint($request['user_id']);  
            $args['where'] = "user_id = $user_id"; 
        }  
        if (isset($request['app_process_id']) && !empty($request['app_process_id'])) {
            $app_process_id = absint($request['app_process_id']);  
            $args['where'] = "app_process_id = $app_process_id"; 
        }  

        $process_comments = axilweb_ajl_jobs()->app_process_comment_manager->all($args); 
        foreach ( $process_comments as $comments ) {
            $response = $this->prepare_item_for_response( $comments, $request );
            $data[]   = $this->prepare_response_for_collection( $response );
        } 
        $args['count'] = 1;
        $total         = axilweb_ajl_jobs()->app_process_comment_manager->all( $args );
        $max_pages     = ceil( $total / (int) $args['limit'] );
        $response      = rest_ensure_response( $data );

        $response->header( 'X-WP-Total', (int) $total );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );

        return $response;
    }

    /**
     * Create a New Process Comment.
     *
     * Handles the REST API request to create a new process comment. Validates the request, 
     * prepares data for the database, inserts the comment, and returns the created comment 
     * as a response. If the ID is already provided, the function prevents creation and 
     * returns an error response.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object containing data to create the process comment.
     *
     * @return WP_REST_Response|WP_Error A REST API response with the created comment or a WP_Error object on failure.
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
        $process_comment_data = App_Process_Comment::prepare_data_for_database($request);
        if (is_wp_error($request)) {
            return $request;
        }
        // Insert the application.
        $process_comment_id = axilweb_ajl_jobs()->app_process_comment_manager->create($process_comment_data, $request);

        if (is_wp_error($process_comment_id)) {
            return $process_comment_id;
        }
 
        $processComment = (new App_Process_Comment)->get_by('id', $process_comment_id);
        $response = $this->prepare_item_for_response($processComment, $request);
        $response = rest_ensure_response($response);
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $process_comment_id)));
        return $response;
 
    }
 
    /**
     * Prepare a Single Item for REST API Response.
     *
     * Converts an item into a REST API-compatible response structure. Handles the transformation of 
     * the item into an array, applies context-specific filtering, and adds relevant links for the 
     * REST API response.
     *
     * @since 1.0.0
     *
     * @param mixed           $item   The item (e.g., a process comment) to prepare for the response.
     * @param WP_REST_Request $request The REST API request object.
     * @param string          $type   Optional. The type of transformation to apply. Default is '*'.
     *
     * @return WP_REST_Response The prepared REST API response.
     */ 
    public function prepare_item_for_response($item, $request, $type = '*')
    {

        $data       = [];
        $data       = App_Process_Comment::to_array($item, $type);
        $data       = $this->prepare_response_for_collection($data);
        $context    = !empty($request['context']) ? $request['context'] : 'view';
        $data       = $this->filter_response_by_context($data, $context);
        $response   = rest_ensure_response($data);
        $response->add_links(Helpers::prepare_links($item, $this->namespace, $this->rest_base, $this->base));

        return $response;
    }
 
    /**
     * Retrieve Collection Parameters for REST API.
     *
     * Extends the parent class's `get_collection_params()` method to define additional
     * query parameters for the REST API. Adds default values for `limit` and `s` parameters.
     *
     * @since 1.0.0
     *
     * @return array An array of collection parameters, including defaults for pagination and search.
     */ 
    public function get_collection_params(): array
    {
        $params = parent::get_collection_params();

        $params['limit']['default'] = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['s']['default']     = '';

        return $params;
    }
}
