<?php
namespace Axilweb\AiJobListing\Helpers;
use WP_Error;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Permissions_Check_Helpers_Trait
{
    /**
     * Determine the proper HTTP status code for authorization.
     *
     * This method sets up the appropriate HTTP status code based on the user's authentication status.
     * If a user is logged in, a status code of 403 (Forbidden) is returned, indicating that the user
     * does not have permission to access the resource. Otherwise, a status code of 401 (Unauthorized)
     * is returned, indicating that authentication is required to access the resource.
     *
     * @since 1.0.0
     *
     * @return int The HTTP status code for authorization (401 for unauthorized, 403 for forbidden).
     */
    public static function authorization_status_code()
    {
        $status = 401;

        // Check if a user is logged in
        if (is_user_logged_in()) {
            // User is logged in, set status code to 403 (Forbidden)
            $status = 403;
        }

        return $status;
    }
    
    /**
     * Check permissions for the delete operation.
     *
     * Determines whether the current user has the capability to delete attribute resources.
     * If the user has the required capability (defined in Capabilities_Data::DELETE_SETTINGS), permission is granted.
     * Otherwise, a WP_Error instance is returned indicating forbidden access.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request The request object.
     *
     * @return bool|WP_Error True if the user has permission, WP_Error if permission is denied.
     */
    public static function permissions_check($capabilities = "manage_options", $message = "You cannot view the Job resource.")
    {
        // Check if the current user has the capability to delete settings
        if (!current_user_can($capabilities)) {
            // If the user does not have the capability, return a WP_Error indicating forbidden access
            return new WP_Error(
                'rest_forbidden',
                $message,
                array('status' => self::authorization_status_code())
            );
        }

        // If the user has the capability, return true to indicate permission is granted
        return true;
    }
}
