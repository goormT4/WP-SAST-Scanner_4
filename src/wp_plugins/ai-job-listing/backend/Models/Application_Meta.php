<?php 
namespace Axilweb\AiJobListing\Models; 
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Application_Meta class.
 *
 * @since 0.1.0
 */
class Application_Meta extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_application_meta';


    /**
     * Job application meta item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $applications
     *
     * @return array
     */
    public static function to_array(object $job_application_meta): array
    {
        $data = [];
        return $data;
    }

    /**
     * Prepares a job application meta for create or update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return object|WP_Error
     */
    public static function prepare_data_for_database($job_applications_id, $process_data)
    {
        $fields = [];
        $values = $place_holders = array();
        if (isset($process_data) && !empty($process_data)) {
            foreach ($process_data as $key => $val) {
              
                if ($val['app_mk'] != "applicant_password" && $val['app_mv'] != "applicant_password_confirm") {
                    $fields[$key]['app_id'] = $job_applications_id;
                    $fields[$key]['app_mk'] = $val['app_mk']; 
                    $fields[$key]['app_mv'] = $val['app_mv']; 
                    $fields[$key]['created_at'] = current_datetime()->format('Y-m-d H:i:s');
                    $fields[$key]['created_by'] = empty($val['created_by']) ? get_current_user_id() : absint($val['created_by']);
                }
            }
            if (!empty($fields)) {
                foreach ($fields as $key => $val) {
                    array_push($values, $val['app_id'], $val['app_mk'], $val['app_mv'], $val['created_at'], $val['created_by']);
                    $place_holders[] = "( %d, %s , %s, %s, %s )";
                }
            }
        } 
        return [
            "values" => $values,
            "place_holders" => $place_holders
        ];
    }
}
