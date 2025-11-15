<?php
namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * App_Process_Comment class.
 *
 * @since 0.1.0
 */
class App_Process_Comment extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_app_process_comment';
 
    /**
     * process comment item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $app_process_comment
     *
     * @return array
     */ 
    public static function to_array(?object $app_process_comment): array
    {
 
            return [
                "id"                    => $app_process_comment->id,
                "user_obj"               => self::get_user_info($app_process_comment),
                "app_id"                => $app_process_comment->app_id,
                "app_process_obj"        => self::get_app_process($app_process_comment),
                "comment"               => $app_process_comment->comment,
                "created_at"            => $app_process_comment->created_at,
                
            ];
    
    }
   
    /**
     * Retrieves the process details for a given app process comment.
     *
     * This function fetches the `id`, `name`, `icon`, and `icon_color` columns
     * for the process associated with a specific app process comment based on its `app_process_id`.
     * If the `app_process_id` is invalid or missing, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $app_process_comment The app process comment object containing the `app_process_id`.
     * @return object|null The app process details as an object, or null if not found or invalid.
     */
    public static function get_app_process( ?object $app_process_comment ): ?object {
        $appProcess = new App_Process(); 
        $columns = 'id, name, icon, icon_color';
        return $appProcess->get( (int) $app_process_comment->app_process_id, $columns );
    }

    /**
     * Retrieves the application details for a given app process comment.
     *
     * This function fetches the `id`, `process_id`, and `job_id` columns for an application
     * associated with a specific app process comment based on its `app_id`.
     * If the `app_id` is invalid or missing, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $app_process_comment The app process comment object containing the `app_id`.
     * @return object|null The application details as an object, or null if not found or invalid.
     */
    public static function get_app_id( ?object $app_process_comment ): ?object {
        $application = new Application(); 
        $columns = 'id, process_id, job_id';
        return $application->get( (int) $app_process_comment->app_id, $columns );
    }

    /**
     * Retrieves user information based on the user ID in the app process comment.
     *
     * This function fetches user details such as `id`, `name`, `display_name`, `user_email`,
     * and `avatar_url` for the user associated with the provided `app_process_comment` object.
     * If the `user_id` is invalid or the user does not exist, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $app_process_comment The app process comment object containing the `user_id`.
     * @return array|null The user information as an associative array, or null if not found or invalid.
     */
    public static function get_user_info( ?object $app_process_comment ): ?array {
        if ( empty( $app_process_comment->user_id ) ) {
            return null;
        } 
        $user = get_user_by( 'id', $app_process_comment->user_id );

        if ( empty( $user ) ) {
            return null;
        } 
        return [
            'id'                 => $app_process_comment->user_id,
            'name'               => $user->display_name,
            'display_name'       => $user->display_name,
            'user_email'         => $user->user_email ,
            'avatar_url'         => get_avatar_url( $user->ID ),
        ];
    }


   /**
     * Prepares a process comment for create or update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return array|WP_Error
     */
    public static function prepare_data_for_database($request)
    {
        $data = [];
        $data['user_id']           = $request['user_id'];
        $data['app_id']            = $request['app_id'];
        $data['app_process_id']    = $request['app_process_id'];
        $data['comment']           = $request['comment'];
        $data['created_at']        = current_datetime()->format( 'Y-m-d H:i:s' );
        
        return $data;
    }
}
