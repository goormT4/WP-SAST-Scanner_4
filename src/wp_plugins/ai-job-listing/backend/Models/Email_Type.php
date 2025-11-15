<?php
namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
 // Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Email_Type class.
 *
 * @since 0.1.0
 */
class Email_Type extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axil_job_listing_email_types';

    /**
     * Email_Types item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $axil_job_listing_email_types
     *
     * @return array
     */
    
    public static function to_array(object $emailTypes): array
    {
        $database_data = [
            'id'            => (int) $emailTypes->id, 
            'name'          => $emailTypes->name,
            'slug'          => $emailTypes->slug, 
            'icon'          => $emailTypes->icon, 
            'is_active'     => $emailTypes->is_active,

        ];
        return $database_data;
    } 
    
}
