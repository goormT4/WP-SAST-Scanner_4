<?php 
namespace Axilweb\AiJobListing\Models; 
use Axilweb\AiJobListing\Abstracts\Base_Model;
use Axilweb\AiJobListing\Helpers\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Job Application class.
 *
 * @since 0.1.0
 */
class Application extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_applications';
 
    /**
     * Job application item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $applications
     *
     * @return array
     */
    // public static function to_array(object $job_application): array
    public static function to_array(?object $job_application, $type = '*', $status = AXILWEB_AJL_REJECT_PROCESS_SLUG): array
    {
 
        if (isset($type) && $type != "*") {
            return [
                "id"            => $job_application['id'],
                "{$type}"       => $job_application['' . $type . ''],
            ];
        } else {
            $profile_image = '';
            $resume = '';
            foreach (json_decode($job_application->meta_attributes, true) as $val) {
                if (isset($val['app_mk']) && $val['app_mk'] == "profile_image") {
                    if (isset($val['app_mv']) && !empty($val['app_mv'])) {
                        $profile_image = wp_get_attachment_image_url($val['app_mv'], 'applicant-profile-image-lg');
                    }
                }
                if (isset($val['app_mk']) && $val['app_mk'] == "resume") {
                    if (isset($val['app_mv']) && !empty($val['app_mv'])) {
                        $resume = wp_get_attachment_url($val['app_mv']);
                    }
                }
            }
            $data = [
                "id"                        => $job_application->id,
                "job_id"                    => $job_application->job_id,
                "job_title"                 => self::get_job_application($job_application),
                "process_id"                => $job_application->process_id,
                "app_process"                => self::get_app_process($job_application), 
                "previous_process_id"       => $job_application->previous_process_id ?? null,
                "status"                    => $job_application->key,
                "is_read"                   => $job_application->is_read,
                "date"                      => $job_application->application_date,
                "profile_image_url"         => $profile_image,
                "resume"                    => $resume,
                "meta_attributes"           => $job_application->meta_attributes
            ];
            if ($status == "all") {
                $next_previous_step = Helpers::getNextPreviousStep($job_application->process_id);
                $data = array_merge($data, $next_previous_step);
            }
           
            return $data;
        }
    }
  
    /**
     * Retrieves the title of a job associated with a job application.
     *
     * This function fetches the `title` column for a specific job identified by the
     * `job_id` property in the provided `job_application` object. If the `job_application`
     * is invalid or the `job_id` is missing, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $job_application The job application object containing the `job_id`.
     * @return object|null The job title as an object, or null if not found or invalid.
     */  
    public static function get_job_application(?object $job_application): ?object
    {
        $job = new Job();

        $columns = 'title';
        return $job->get((int) $job_application->job_id, $columns);
    }

    /**
     * Retrieves the process details for a given job application.
     *
     * This function fetches details such as `id`, `name`, `icon`, and `icon_color`
     * for the process associated with a job application based on its `process_id`.
     * If the `process_id` is invalid or missing, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $job_application The job application object containing the `process_id`.
     * @return object|null The process details as an object, or null if not found or invalid.
     */
    public static function get_app_process(?object $job_application): ?object
    {
        if (! $job_application->process_id) return null;
        $app_process = new App_Process();
        $columns = '`id`, `name`, `icon`, `icon_color`';
        return $app_process->get((int) $job_application->process_id, $columns);
    }

    /**
     * Retrieves the process name for a given job application.
     *
     * This function fetches the `name` column for the process associated with a job application
     * based on its `process_id`. If the `process_id` is invalid or missing, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $job_application The job application object containing the `process_id`.
     * @return object|null The process name as an object, or null if not found or invalid.
     */
    public static function get_process_name(?object $job_application): ?object
    {
        $App_Process = new App_Process();

        $columns = 'name';
        return $App_Process->get((int) $job_application->process_id, $columns);
    } 

    /**
     * Prepares a job applications for create or update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return object|WP_Error
     */
    public static function prepare_data_for_database($request)
    {
        $data = [];
        $data['job_id']             = $request['job_id'];
        $data['is_read']            = $data['is_read'] = $request['is_read'] ?? 0;
        $data['process_id']         = Helpers::getProcessByOrder();
        $data['previous_process_id']= null;
        $data['created_at']         = current_datetime()->format('Y-m-d H:i:s');
        $data['created_by']         = empty($request['created_by']) ? get_current_user_id() : absint($request['created_by']);

        return $data;
    }
}
