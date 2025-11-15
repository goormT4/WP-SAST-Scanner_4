<?php

namespace Axilweb\AiJobListing\Rest; 
use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Data\Data; 
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API Attributes_Controller class.
 *
 * @since 0.1.0
 */
class Application_Fields_Data_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'application-fields';

    /**
     * Register REST API Routes.
     *
     * Registers the REST API routes for the resource using `register_rest_route()`. Defines 
     * a route for retrieving a collection of items with appropriate arguments, schema, and 
     * permission callbacks.
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
                    'args'                => $this->get_collection_params(),
                    'schema'              => [$this, 'get_item_schema'],
                    'permission_callback' => '__return_true', // Use this for public access
                ],
            ]
        );
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
     * Retrieve Items for REST API.
     *
     * Handles the REST API request to fetch items. Retrieves data using the `Data::getFieldsData()` method
     * and ensures the response is properly formatted for the REST API.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     *
     * @return WP_REST_Response|null The REST API response containing the retrieved data, or null if no data is returned.
     */ 
    public function get_items($request): ?WP_REST_Response
    {
        $data = Data::getFieldsData();
        return rest_ensure_response($data);
    }

    /**
     * Retrieve Collection Parameters for REST API.
     *
     * Extends the parent class's `get_collection_params()` method to define additional
     * parameters and their default values for the REST API. Adds a default value for
     * `limit` to specify the number of items per page and `s` for search queries.
     *
     * @since 1.0.0
     *
     * @return array An array of collection parameters with defaults for pagination and search.
     */ 
    public function get_collection_params(): array
    {
        $params                     = parent::get_collection_params(); 
        $params['limit']['default'] = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['s']['default']     = '';

        return $params;
    }
}
