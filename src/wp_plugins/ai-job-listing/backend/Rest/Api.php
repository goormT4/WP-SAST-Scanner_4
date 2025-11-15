<?php 
namespace Axilweb\AiJobListing\Rest;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * API Manager class.
 *
 * All API classes would be registered here.
 *
 * @since 0.1.0
 */
class Api
{
    /**
     * Class dir and class name mapping.
     *
     * @var array
     *
     * @since 1.0.0
     */
    protected $class_map;

   /**
     * Constructor for initializing REST API controllers and registering routes.
     *
     * This constructor performs the following tasks:
     * - Checks if the `WP_REST_Server` class exists, ensuring the REST API is available.
     * - Applies a filter to modify the `class_map` array, which contains a list of controller classes that handle various aspects of the REST API for the plugin.
     * - Registers the action to initialize REST API routes by hooking into the `rest_api_init` action.
     *
     * The constructor initializes a class map for all the REST controllers required for the plugin and then registers the routes.
     *
     * @return void
     */
    public function __construct()
    {
        if (!class_exists('WP_REST_Server')) {
            return;
        } 
        $this->class_map = apply_filters(
            'axilweb_ajl_rest_api_class_map',
            [
                \Axilweb\AiJobListing\Rest\Jobs_Controller::class,
                \Axilweb\AiJobListing\Rest\Attributes_Controller::class,
                \Axilweb\AiJobListing\Rest\Attribute_Values_Controller::class,
                \Axilweb\AiJobListing\Rest\Applications_About_Info_Controller::class,
                \Axilweb\AiJobListing\Rest\Applications_Work_Experience_Controller::class, 
                \Axilweb\AiJobListing\Rest\Application_Fields_Data_Controller::class,
                \Axilweb\AiJobListing\Rest\Applications_Controller::class,
                \Axilweb\AiJobListing\Rest\App_Process_Comment_Controller::class,
                \Axilweb\AiJobListing\Rest\Setting_Controller::class,
                \Axilweb\AiJobListing\Rest\General_Settings_Controller::class,
                \Axilweb\AiJobListing\Rest\Email_Types_Controller::class,
                \Axilweb\AiJobListing\Rest\Email_Templates_Controller::class,

            ]
        );

        // Init REST API routes.
        add_action('rest_api_init', array($this, 'register_rest_routes'), 10);
    }

   /**
     * Register REST API Routes.
     *
     * Iterates through the `$class_map` property and registers REST API routes 
     * for each controller class that implements a `register_routes` method. 
     * This function ensures that all mapped controllers have their REST routes 
     * properly initialized.
     *
     * @since 1.0.0
     *
     * @return void
     */ 
    public function register_rest_routes(): void
    {
        foreach ($this->class_map as $controller) {
            $controller_instance = new $controller();
            // Register routes if the method exists
            if (method_exists($controller_instance, 'register_routes')) {
                $controller_instance->register_routes();
            }
        }
    }
}
