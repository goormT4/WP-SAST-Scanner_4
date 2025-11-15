<?php

namespace Axilweb\AiJobListing\Rest; 
use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Data\Data;
use WP_Error;
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
class Applications_Work_Experience_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'work-experience';

    /**
     * Register REST API Routes.
     *
     * Registers the REST API route for the resource. Defines the endpoint, HTTP methods,
     * callback functions, permissions, and schema for handling resource requests.
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
     * Retrieves a collection of job items.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items($request): ?WP_REST_Response
    {

        $data = Data::getWorkExperience();
        return rest_ensure_response($data);
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
        $params['limit']['default'] = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['s']['default']     = ''; 
        return $params;
    }
}
