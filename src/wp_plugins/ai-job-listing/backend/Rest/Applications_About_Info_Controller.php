<?php

namespace Axilweb\AiJobListing\Rest;

use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Data\Data;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}
/**
 * API Applications_About_Info_Controller class.
 *
 * @since 0.1.0
 */
class Applications_About_Info_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'about-info';

    /**
     * Register REST API Routes.
     *
     * Registers a REST API route for the resource using `register_rest_route()`. 
     * Defines a route for retrieving a collection of items with appropriate 
     * callback, permissions, arguments, and schema.
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
                    'schema'              => [$this, 'get_item_schema'],
                ],
            ]
        );
    }


    /**
     * Retrieve Application About Information.
     *
     * Handles the REST API request to fetch application-related information. 
     * Retrieves data using the `Data::getApplicationAboutInfo()` method and 
     * ensures the response is properly formatted for the REST API.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     *
     * @return WP_REST_Response|null The REST API response containing the retrieved data, 
     *                               or null if no data is available.
     */
    public function get_items($request): ?WP_REST_Response
    {
        $data = Data::getApplicationAboutInfo();
        return rest_ensure_response($data);
    }

    /**
     * Retrieve Collection Parameters for REST API.
     *
     * Extends the parent class's `get_collection_params()` method to define additional
     * parameters or override default values for the REST API. Adds a default value for
     * `limit` to specify the number of items per page and `s` for search queries.
     *
     * @since 1.0.0
     *
     * @return array An array of collection parameters with defaults for pagination and search.
     */
    public function get_collection_params(): array
    {
        $params = parent::get_collection_params();

        $params['limit']['default'] = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['s']['default']     = '';

        return $params;
    }
}
