<?php 
namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * App_Process class.
 *
 * @since 0.1.0
 */
class App_Process extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_app_process';
 
    /**
     * process item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $app_process
     *
     * @return array
     */ 
    public static function to_array(?object $app_process): array
    { 
        return [
            "id"            => $app_process->id,
            "name"          => $app_process->app_id,
            "key"           => $app_process->app_process_id,
            "icon"          => $app_process->comment,
            "icon_color"    => $app_process->comment,
            
        ]; 
    }  
}
