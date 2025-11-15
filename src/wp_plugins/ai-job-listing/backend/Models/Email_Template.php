<?php
namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
use Axilweb\AiJobListing\Models\Email_Type;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Email_Template class.
 *
 * @since 0.1.0
 */
class Email_Template extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axil_job_listing_email_templates';

    /**
     * Email_Template item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $axil_job_listing_Email_Templates
     *
     * @return array
     */
    public static function to_array(object $emailTemplate): array
    {
        $data = [
            'id'                        => (int) $emailTemplate->id,
            'type_id'                   => $emailTemplate->type_id,
            'type'                      => self::getEmail_Type($emailTemplate->type_id),
            'receiver_type'             => $emailTemplate->receiver_type,
            'has_notification_status'   => $emailTemplate->has_notification_status,
            'should_email_sent_to_all'  => $emailTemplate->should_email_sent_to_all,
            'message'                   => $emailTemplate->message,
            'subject'                   => $emailTemplate->subject,
            'updated_by'                => $emailTemplate->updated_by,
            'updated_at'                => $emailTemplate->updated_at,
        ];
        return $data;
    } 
    
    /**
     * Retrieves the name of an email type by its ID.
     *
     * This function fetches the `name` column for a specific email type
     * identified by its `type_id`. If the `type_id` is invalid or not provided,
     * the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param int|null $type_id The ID of the email type to retrieve.
     * @return string|null The name of the email type, or null if not found or invalid.
     */
    public static function getEmail_Type($type_id)
    {
        if (!$type_id) {
            return null;
        }
        $email_type = new Email_Type();
        $columns = '`name`';
        return $email_type->get((int) $type_id, $columns);
    }
     
}
