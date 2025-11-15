<?php

namespace Axilweb\AiJobListing\Rest;

use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Email_Type;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * API Jobs_Controller class.
 *
 * @since 0.1.0
 */
class Email_Types_Controller extends Rest_Controller
{

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'email-types';
    
    /**
     * Register all routes related to the API endpoints for retrieving items.
     *
     * This method registers a single REST API route that allows reading items. The route is associated 
     * with a callback function (`get_items`), performs a permission check (`check_permission`), and 
     * provides arguments and schema validation for the request.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => [ $this, 'check_permission' ],
                    'args'                => $this->get_collection_params(),
                    'schema'              => [ $this, 'get_item_schema' ],
                ] 
            ]
        );
        
    }

     /**
     * Retrieves a collection of email types.
     *
     * This function fetches email types from the database, processes each item into
     * a standardized REST API response format, and adds pagination metadata.
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|null The prepared REST API response or null on failure.
     */
    public function get_items( $request ) { 
          $args = [
              'orderby' => 'id', 
              'order'   => 'ASC'
          ];  
           $data   = [];
           $params = $this->get_collection_params();
          
          foreach ( $params as $key => $value ) {
              if ( isset( $request[ $key ] ) ) {
                  $args[ $key ] = $request[ $key ];
              }
          }
          
          $settings = axilweb_ajl_jobs()->email_types_manager->all( $args ); 
          foreach ( $settings as $setting ) {
              $response = $this->prepare_item_for_response( $setting, $request );
              $data[]   = $this->prepare_response_for_collection( $response );
          } 
          $args['count'] = 1;
          $total         = axilweb_ajl_jobs()->email_types_manager->all( $args );
          $max_pages     = ceil( $total / (int) $args['limit'] );
          $response      = rest_ensure_response( $data ); 
          $response->header( 'X-WP-Total', (int) $total );
          $response->header( 'X-WP-TotalPages', (int) $max_pages ); 
          return $response;
      }
      
    /**
     * Prepares a single email type for a REST API response.
     *
     * This function formats an individual email type as a REST API response,
     * applying context filtering and adding hypermedia links.
     *
     * @param mixed            $item    The email type to prepare (e.g., an object or array).
     * @param WP_REST_Request  $request The REST API request object.
     * @return WP_REST_Response|null The prepared response, or null on failure.
     */
      public function prepare_item_for_response($item, $request): ?WP_REST_Response
      {
   
          $data = [];
          $data       = Email_Type::to_array($item);
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
