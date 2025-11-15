<?php
 /**
 * Plugin Name:       AI Job Listing
 * Description:       A simple job listing plugin to manage job postings, AI-generated descriptions, and candidate applications.
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Version:           1.1.1
 * Tested up to:      6.8
 * Author:            Axilweb
 * Author URI:        https://axilweb.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ai-job-listing
 */

use Axilweb\AiJobListing\Admin\Menu;
use Axilweb\AiJobListing\Assets\Manager as AssetsManager;
use Axilweb\AiJobListing\Blocks\Manager as BlocksManager;
use Axilweb\AiJobListing\Manager\App_Process_Comment_Manager;
use Axilweb\AiJobListing\Manager\App_Process_Manager;
use Axilweb\AiJobListing\Manager\Application_Manager;
use Axilweb\AiJobListing\Manager\Application_Meta_Manager;
use Axilweb\AiJobListing\Manager\Attribute_Values_Manager;
use Axilweb\AiJobListing\Manager\Attributes_Manager;
use Axilweb\AiJobListing\Manager\Email_Templates_Manager;
use Axilweb\AiJobListing\Manager\Email_Types_Manager;
use Axilweb\AiJobListing\Manager\General_Settings_Manager;
use Axilweb\AiJobListing\Manager\Job_Attribute_Value_Manager;
use Axilweb\AiJobListing\Manager\Job_Manager;
use Axilweb\AiJobListing\Rest\Api;
use Axilweb\AiJobListing\Setup\Cron_Job;

defined('ABSPATH') || exit;

/**
 * Axilweb_Ajl_Core class.
 *
 * @class Axilweb_Ajl_Core The class that holds the entire AI_Job_Listing plugin
 */
final class Axilweb_Ajl_Core
{
    /**
     * Plugin version.
     *
     * @var string
     */
    const VERSION = '1.1.1';

    /**
     * Plugin slug.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const SLUG = 'ai-job-listing';

    /**
     * Holds various class instances.
     *
     * @var array
     *
     * @since 1.0.0
     */
    private $container = [];

    /**
     * Constructor for the ai_job_listing job class.
     *
     * Sets up all the appropriate hooks and actions within our plugin.
     *
     * @since 1.0.0
     */
    private function __construct()
    {
        // Require the Composer autoloader file to autoload classes.
        require_once __DIR__ . '/vendor/autoload.php';

        require_once __DIR__ . '/backend/elementor/elementor-widget.php';

        // Define constants specific to the plugin.
        $this->define_constants();
        $this->register_table_names();

        // Register activation and deactivation hooks.
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Add an action to flush rewrite rules on WordPress loaded.
        add_action('wp_loaded', [$this, 'flush_rewrite_rules']);

        (new Cron_Job)->doingCronJob();

        // Initialize the plugin.
        $this->init_plugin();
        // Create page automatically


    }
    /**
     * Register table names.
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function register_table_names(): void
    {
        // Access the global $wpdb variable.
        global $wpdb;

        // Register the table names by appending the table prefix to the table names. 
        $wpdb->axilweb_ajl_jobs                    = $wpdb->prefix .  'axilweb_ajl_jobs';
        $wpdb->axilweb_ajl_attributes              = $wpdb->prefix .  'axilweb_ajl_attributes';
        $wpdb->axilweb_ajl_attribute_values        = $wpdb->prefix .  'axilweb_ajl_attribute_values';
        $wpdb->axilweb_ajl_job_attribute_value     = $wpdb->prefix .  'axilweb_ajl_job_attribute_value';
        $wpdb->axilweb_ajl_applications            = $wpdb->prefix .  'axilweb_ajl_applications';
        $wpdb->axilweb_ajl_application_meta        = $wpdb->prefix .  'axilweb_ajl_application_meta';
        $wpdb->axilweb_ajl_app_process             = $wpdb->prefix .  'axilweb_ajl_app_process';
        $wpdb->axilweb_ajl_app_process_by_job      = $wpdb->prefix .  'axilweb_ajl_app_process_by_job';
        $wpdb->axilweb_ajl_app_process_comment     = $wpdb->prefix .  'axilweb_ajl_app_process_comment';
        $wpdb->axilweb_ajl_general_settings        = $wpdb->prefix .  'axilweb_ajl_general_settings';
    }

    /**
     * Initializes the Axilweb_Ajl_Core() class.
     *
     * Checks for an existing Axilweb_Ajl_Core instance
     * and if it doesn't find one, creates it.
     *
     * @since 1.0.0
     *
     * @return Axilweb_Ajl_Core|bool
     */
    public static function init()
    {
        static $instance = null;

        if (!$instance) {
            $instance = new Axilweb_Ajl_Core();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param string $prop The name of the property being accessed.
     *
     * @return mixed The value of the property.
     */
    public function __get($prop)
    {
        // Check if the property exists in the container.
        if (array_key_exists($prop, $this->container)) {
            // If it exists in the container, return its value.
            return $this->container[$prop];
        }

        // If the property doesn't exist in the container, retrieve it directly from the object.
        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @since 1.0.0
     *
     * @param mixed $prop The property being checked.
     *
     * @return bool True if the property exists, false otherwise.
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }
    /**
     * Define the constants.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define_constants()
    {
 
        // Plugin Information
        define('AXILWEB_AJL_VERSION', self::VERSION);
        define('AXILWEB_AJL_SLUG', self::SLUG);
        define('AXILWEB_AJL_PREFIX', 'axilweb_ajl_');

         // File Paths 
        define('AXILWEB_AJL_FILE', __FILE__);
        define('AXILWEB_AJL_DIR', __DIR__);
        define('AXILWEB_AJL_PATH', plugin_dir_path(__FILE__));
        define('AXILWEB_AJL_INCLUDES', AXILWEB_AJL_PATH . '/backend');
        define('AXILWEB_AJL_TEMPLATE_PATH', AXILWEB_AJL_PATH . '/templates');
        define('AXILWEB_AJL_LANGUAGES_DIR', AXILWEB_AJL_PATH . 'languages/');
         
         // URLs
         define('AXILWEB_AJL_URL', plugins_url('', __FILE__));
         define('AXILWEB_AJL_BUILD', AXILWEB_AJL_URL . '/build'); 
         define('AXILWEB_AJL_ASSETS', AXILWEB_AJL_URL . '/assets');
         define('AXILWEB_AJL_IMAGES_URL', AXILWEB_AJL_ASSETS . 'images/');
         
        // Database/Query Constants
        define('AXILWEB_AJL_POSTS_PER_PAGE', 10);
        define('AXILWEB_AJL_DEFAULT_PAGE', 1);
        define('AXILWEB_AJL_JOB_LIMIT', 10);
        define('AXILWEB_AJL_DEFAULT_ORDERBY', 'id');
        define('AXILWEB_AJL_DEFAULT_ORDER', 'DESC');
  
        // Status Codes / Process Statuses
        define('AXILWEB_AJL_NO_RECORDS_FOUND', 404); 
        define('AXILWEB_AJL_ALREADY_EXISTS', 409);  
        define('AXILWEB_AJL_DEFAULT_PROCESS_ID', 1); 
        define('AXILWEB_AJL_DEFAULT_PROCESS_SLUG', 'unlisted');
        define('AXILWEB_AJL_DEFAULT_PROCESS_NAME', 'Unlisted');
        define('AXILWEB_AJL_REJECT_PROCESS_ID', 8); 
        define('AXILWEB_AJL_REJECT_PROCESS_SLUG', 'rejected');
        define('AXILWEB_AJL_REJECT_PROCESS_NAME', 'Rejected');
        define('AXILWEB_AJL_IN_PROGRESS_IDS', '3,4,5,6');
        define('AXILWEB_AJL_HIRED_PROCESS_ID', 7);
        define('AXILWEB_AJL_ROOT_API_ENDPOINT', 'https://alttext.axilweb.com/backendapp/api/api-key/generate-job-description');
         
    }


    /**
     * Load the plugin after all plugins are loaded.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_plugin()
    {
        // Include required files.
        $this->includes();

        // Initialize hooks.
        $this->init_hooks();

        new \Axilweb\AiJobListing\Setup\Rewrite_Rules();
        new \Axilweb\AiJobListing\Setup\Job_Meta();

        /**
         * Fires after the plugin is loaded.
         *
         * @since 1.0.0
         */
        do_action('ai_job_listing_job_loaded');
    }


    /**
     * Activating the plugin.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function activate()
    {
        $this->install();
        new \Axilweb\AiJobListing\Setup\Generate_Default_Pages();
        $this->flush_rewrite_rules();   
    }

    /**
     * Placeholder for deactivation function.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function deactivate()
    {
        $this->flush_rewrite_rules();
    }

    /**
     * Flush rewrite rules after plugin is activated.
     *
     * Nothing being added here yet.
     *
     * @since 1.0.0
     */
    public function flush_rewrite_rules()
    {
        flush_rewrite_rules();
    }
    /**
     * Include the required files.
     *
     * @since 1.0.0
     *
     * @return void
     */

    /**
     *  Store all classes that need to this plugin as an array
     */
    private static function getServices(): array
    {
        return [
            "rest_api"                      => Api::class,
            "assets_manager"                => AssetsManager::class,
            "blocks_manager"                => BlocksManager::class,
            "jobs_manager"                  => Job_Manager::class,
            "attributes_manager"            => Attributes_Manager::class,
            "attributes_values_manager"     => Attribute_Values_Manager::class,
            "job_attributes_value_manager"  => Job_Attribute_Value_Manager::class,
            "job_applications_manager"      => Application_Manager::class,
            "job_application_meta_manager"  => Application_Meta_Manager::class,
            "app_process_comment_manager"   => App_Process_Comment_Manager::class,
            "app_process_manager"           => App_Process_Manager::class,
            "general_settings_manager"      => General_Settings_Manager::class,
            "email_types_manager"           => Email_Types_Manager::class,
            "email_templates_manager"       => Email_Templates_Manager::class,
        ];
    }
    /**
     * Instantiate class
     */
    private static function instantiate($class)
    {
        return new $class();
    }

    public function includes()
    {
        // Check if the current request is for the WordPress admin area.
        if ($this->is_request('admin')) {
            // If it is, initialize the admin menu component.
            $this->container['admin_menu'] = self::instantiate(Menu::class);
        }

        foreach (self::getServices() as $key => $class) {
            $this->container[$key] = self::instantiate($class);
        }
    }

    /**
     * Initialize the hooks.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_hooks()
    {

        // Localize our plugin
        add_action('init', [$this, 'localization_setup']);

        // Add the plugin page links
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_action_links']);

        add_action('init', [$this, 'ai_job_listingJobImageSizes']);
    }

    public  function ai_job_listingJobImageSizes()
    {
        add_image_size('applicant-profile-image', 32, 32, true); // width, height, crop (true or false)
        add_image_size('applicant-profile-image-lg', 120, 120, true); // width, height, crop (true or false)
    }


    /**
     * Initialize plugin for localization.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function localization_setup()
    {
        // WordPress 4.6+ automatically loads translations from /languages directory
        // No need to call load_plugin_textdomain() explicitly

        // Load the React-pages translations.
        if (is_admin()) {
            // Load wp-script translation for ai_job_listing-app
            wp_set_script_translations('ai_job_listing-app', 'ai-job-listing');
        }
    }

    /**
     * What type of request is this.
     *
     * @since 1.0.0
     *
     * @param string $type The type of request being checked (admin, ajax, rest, cron, or frontend).
     *
     * @return bool True if the current request matches the specified type, false otherwise.
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                // Check if the current request is targeting the WordPress admin area.
                return is_admin();

            case 'ajax':
                // Check if the current request is an AJAX request.
                return defined('DOING_AJAX');

            case 'rest':
                // Check if the current request is a REST API request.
                return defined('REST_REQUEST');

            case 'cron':
                // Check if the current request is a cron request.
                return defined('DOING_CRON');

            case 'frontend':
                // Check if the current request is not targeting the admin area and is not a cron request.
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }

    /**
     * Run the installer to create necessary migrations and seeders.
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function install()
    {
        // Instantiate the installer object.
        $installer = new \Axilweb\AiJobListing\Setup\Installer();

        // Run the installer.
        $installer->run();
    }

    /**
     * Plugin action links
     *
     * @param array $links
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function plugin_action_links($links)
    {
        $links[] = '<a href="' . admin_url('admin.php?page=ai-job-listing#/settings') . '">' . __('Settings', 'ai-job-listing') . '</a>';

        return $links;
    }
}

/**
 * Enqueue wp media script
 */

function axilweb_ajl_enqueue_media_scripts()
{
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'axilweb_ajl_enqueue_media_scripts');

/**
 * Initialize the main plugin.
 *
 * @since 1.0.0
 *
 * @return \Axilweb_Ajl_Core|bool Returns an instance of \Axilweb_Ajl_Core or false if initialization fails.
 */
function axilweb_ajl_jobs()
{
    return Axilweb_Ajl_Core::init();
}
/**
 * Deactivate the AI_Job_Listing plugin.
 *
 * @since 1.0.0
 */
axilweb_ajl_jobs();
