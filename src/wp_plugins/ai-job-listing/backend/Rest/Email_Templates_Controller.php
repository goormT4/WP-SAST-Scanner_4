<?php
namespace Axilweb\AiJobListing\Rest;
use Axilweb\AiJobListing\Abstracts\Rest_Controller;
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Email_Template;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API Email_Templates class.
 *
 * @since 0.1.0
 */
class Email_Templates_Controller extends Rest_Controller {

    /**
     * Route base.
     *
     * @var string
     */
    protected $base = 'email-templates';
    
    /**
     * Register all REST API routes related to the resource.
     *
     * This method registers two REST API routes for managing resources:
     * - The first route allows reading a collection of items.
     * - The second route allows reading a single item by ID and updating an item by ID.
     * 
     * Each route includes permission checks and provides necessary arguments and schema validation.
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
        register_rest_route(
            $this->namespace,
            '/' . $this->base . '/(?P<id>[a-zA-Z0-9-]+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_item'],
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
    }

    /**
     * Retrieves a collection of email templates.
     *
     * This function fetches email templates from the database, processes each item into
     * a standardized REST API response format, and adds pagination metadata.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|null The prepared REST API response or null on failure.
     */
    public function get_items( $request ): ?WP_REST_Response { 
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
          
          $settings = axilweb_ajl_jobs()->email_templates_manager->all( $args );
   
          foreach ( $settings as $setting ) {
              $response = $this->prepare_item_for_response( $setting, $request );
              $data[]   = $this->prepare_response_for_collection( $response );
          }
  
          $args['count'] = 1;
          $total         = axilweb_ajl_jobs()->email_templates_manager->all( $args );
          $max_pages     = ceil( $total / (int) $args['limit'] );
          $response      = rest_ensure_response( $data );
  
          $response->header( 'X-WP-Total', (int) $total );
          $response->header( 'X-WP-TotalPages', (int) $max_pages );
  
          return $response;
      }

    /**
     * Retrieves a single email template by ID or slug.
     *
     * This function fetches an email template from the database based on the provided
     * ID (numeric) or slug (string). If the email template is not found, it returns an error.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|WP_Error The prepared email template response or an error if not found.
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
        $email_template = axilweb_ajl_jobs()->email_templates_manager->get($args);
        if (!$email_template) {
            return new WP_Error('axilweb-ai-job-listing-job-rest-email-template-not-found', __('Email template not found. May be email template has been deleted or you don\'t have access to that.', 'ai-job-listing'), ['status' => AXILWEB_AJL_NO_RECORDS_FOUND]);
        }
        // Prepare response.
        $email_template = $this->prepare_item_for_response($email_template, $request); 
        return rest_ensure_response($email_template);
    }
    
    /**
     * Updates an email template by ID.
     *
     * This function updates the specified email template with the provided data.
     * It validates and sanitizes inputs, ensures the email template exists, and
     * triggers an action after a successful update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The REST API request object.
     * @return WP_REST_Response|WP_Error The updated email template or an error if the update fails.
     */
    public function update_item($request)
    {
       
        if (empty($request['id'])) {
            return new WP_Error(
                'Axilweb_Ajl_jobs_rest_email_template_exists',
                __('Invalid Email Template ID.', 'ai-job-listing'),
                array('status' => 2200)
            );
        }
        $template_data = [  
            "has_notification_status"       => Helpers::sanitize($request["has_notification_status"],   'number'),
            "subject"                       => Helpers::sanitize($request["subject"],  'text'),
            "message"                       => Helpers::sanitize($request["message"],  'text'),
            "updated_at"                    => current_datetime()->format('Y-m-d H:i:s'),
            "updated_by"                    => get_current_user_id(),
        ]; 

         $template_id = absint($request['id']);  
         $updated = (new Email_Template)->update(
            $template_data,
            ['id' => $template_id, ],
            ['%s', '%s', '%s', '%s', '%s',],
            ['%d', ]
        );

        if ( ! $updated ) {
            return new \WP_Error( 'axilweb-ai-Job-listing-email-template-update-failed', __( 'Failed to update email template.', 'ai-job-listing' ) );
        }

        do_action( 'axilweb_ai_job_listing_email_template_updated', $template_id, $template_data ); 
        $template = (new Email_Template)->get_by('id', $template_id);
        $response = $this->prepare_item_for_response($template, $request);
        $response = rest_ensure_response($response);
        $response->set_status(200);
        
        return $response;
    }

    /**
     * Prepares a single email type for a REST API response.
     *
     * This function formats an individual email type as a REST API response,
     * applying context filtering and adding hypermedia links.
     *
     * @since 1.0.0
     *
     * @param mixed            $item    The email type to prepare (e.g., an object or array).
     * @param WP_REST_Request  $request The REST API request object.
     * @return WP_REST_Response|null The prepared response, or null on failure.
     */
      public function prepare_item_for_response($item, $request): ?WP_REST_Response
      {
   
          $data = [];
          $data       = Email_Template::to_array($item);
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
