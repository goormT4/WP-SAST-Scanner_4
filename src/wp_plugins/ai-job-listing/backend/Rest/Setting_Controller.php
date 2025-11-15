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
 * API RolesPermissionsController class.
 *
 * @since 0.1.0
 */
class Setting_Controller extends Rest_Controller {

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'setting';

    /**
     * Registers REST API routes for the custom namespace and endpoints.
     *
     * This method defines the available REST API routes for the plugin, including their HTTP methods, 
     * callback functions, permission checks, and additional arguments or schema definitions.
     *
     * @since 1.0.0
     */ 
    public function register_routes() {
        register_rest_route(
            $this->namespace, '/' . $this->base . '/',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => [$this, 'check_permission'],
                    'args'                => $this->get_collection_params(),
                    'schema'              => [ $this, 'get_item_schema' ],
                ], 
            ]
        );
        register_rest_route($this->namespace, '/current-user-data', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'getCurrentUserData' ],
                'permission_callback' => [$this, 'check_permission'],
            ]
        ]); 
          
    }
    
    /**
     * Retrieve data for the currently logged-in user.
     *
     * @return array Array containing user data.
     */
    public function getCurrentUserData()
    {
        // Retrieve the current user object
        $user = wp_get_current_user();
        
        // Construct an array containing user data
        $user_data = [
            'id'           => $user->ID,              
            'name'         => $user->display_name,   
            'email'        => $user->user_email,     
            'capabilities' => $user->allcaps,        
            'roles'        => $user->roles,         
            'avatar_url'   => get_avatar_url( $user->ID ),  
        ];
        
        // Return the user data array
        return $user_data;
    }
    
    /**
     * Retrieves a collection of job items.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */ 
    public function get_items( $request ): ?WP_REST_Response {
        $data = Data::getSettingInfo();
        return rest_ensure_response( $data ); 
    }    
}
