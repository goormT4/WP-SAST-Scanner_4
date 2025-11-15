<?php 
namespace Axilweb\AiJobListing\Manager; 
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Application_Meta;

// Include WordPress media handling functions
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Application_Meta_Manager 
{

    /**
     * Job Application class.
     *
     * @var application_meta
     */
    public $job_application_meta;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->job_application_meta = new Application_Meta();
    }

    /**
     * Creates a new job application meta entry.
     *
     * This method processes the job application metadata and stores it in the database.
     * It also fires an action hook after successfully creating the meta entry.
     *
     * @param int   $job_applications_id The ID of the job application to associate the meta with.
     * @param array $request             The request object containing the meta data to be stored.
     * 
     * @return int|\WP_Error             Returns the job application meta ID if successful, or a WP_Error object on failure.
     */ 
    public function create($job_applications_id, $request)
    {
        try {
            $prepare_data = Application_Meta::prepare_data_for_database($job_applications_id, $request);
            // Create job application meta.

            $job_application_meta_id = Helpers::addApplication_Meta($prepare_data);
            
            /**
             * Fires after a job application meta has been created.
             *
             * @since 1.0.0
             *
             * @param int   $job_application_meta_id
             * @param array $data
             */
            do_action('axilweb_ajl_job_application_meta_created', $job_application_meta_id, $prepare_data);

            return $job_application_meta_id;
        } catch (\Exception $e) {
            return new \WP_Error('axilweb_ajl_job_application_meta_created', $e->getMessage());
        }
    }
 
    /**
     * Uploads an attachment (e.g., profile image or resume) for a job application.
     *
     * This method handles the upload of an attachment file for a job application,
     * creating an attachment in WordPress and storing its ID in the database.
     *
     * @since 1.0.0
     * @param int   $job_applications_id The ID of the job application to associate the attachment with.
     * @param array $attachment          The attachment data array containing file information.
     * @param string $meta_key           The meta key for the attachment type (e.g., 'profile_image' or 'resume').
     * @return int|false The attachment ID if successful, or false on failure.
     */
    public function upload_attachment($job_applications_id, $attachment, $meta_key = 'profile_image') 
    {
        // Detailed logging  
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        // Determine if this is a nested or flat file upload structure
        $is_nested = isset($attachment['name'][0]['app_mv']);

        // Find the keys for file uploads
        $file_keys = $is_nested 
            ? array_keys($attachment['name']) 
            : [0];  // If not nested, use a single key

        $upload_keys = array_filter($file_keys, function($key) {
            return is_numeric($key);
        });

        $attachment_ids = [];

        foreach ($upload_keys as $key) {
            try {
                // Prepare the file for upload
                $file = Helpers::prepare_attachment_for_upload($attachment, $key);

                // Validate file
                if (empty($file['name']) || !file_exists($file['tmp_name'])) { 
                    continue;
                }

                // Prepare the file array that WordPress expects
                $_FILES[$meta_key] = $file;

                // Make sure WordPress media functions are available
                if (!function_exists('media_handle_upload')) {
                    require_once(ABSPATH . 'wp-admin/includes/media.php');
                }
                
                // Handle file upload using WordPress media functions
                $attachment_id = \media_handle_upload($meta_key, $job_applications_id);

                if (is_wp_error($attachment_id)) { 
                    continue;
                }

                $attachment_ids[] = $attachment_id; 

            } catch (\Exception $e) { 
                continue;
            }
        }

        // Return the first attachment ID or false if no uploads were successful
        return !empty($attachment_ids) ? $attachment_ids[0] : false;
    }
        
}
