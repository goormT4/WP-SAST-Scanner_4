<?php

namespace Axilweb\AiJobListing\Rest; 
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Axilweb\AiJobListing\Abstracts\Rest_Controller;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API Attributes_Controller class.
 *
 * @since 0.1.0
 */
class Attributes_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'attributes';

    /**
     * Register the REST API routes for the base resource.
     *
     * This method registers a single REST API route for the resource:
     * - `/base`: A route to retrieve a list of items.
     * 
     * The route supports the following:
     * - **GET** request to fetch items (READABLE method).
     * - It includes the necessary permission checks to ensure the request is authorized.
     * - The request and response are validated using a schema.
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

        $attributes = axilweb_ajl_jobs()->attributes_manager->all($args); 
        $args['count'] = 1;

        $total         = count($attributes);
        $max_pages     = ceil($total / (int) $request->get_default_params()['limit']);
        $response      = rest_ensure_response($attributes);
        $response->header('X-WP-Total', (int) $total);
        $response->header('X-WP-TotalPages', (int) $max_pages);
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
        $params['limit']['default'] = AXILWEB_AJL_POSTS_PER_PAGE;
        $params['s']['default']     = '';

        return $params;
    }
}
