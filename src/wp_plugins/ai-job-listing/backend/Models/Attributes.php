<?php 
namespace Axilweb\AiJobListing\Models; 
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Attributes class.
 *
 * @since 0.1.0
 */
class Attributes extends Base_Model {

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_attributes';

    /**
     * Converts a job attribute object into an associative array.
     *
     * This function takes a `job_attribute` object and converts its properties into an associative array.
     * It casts certain properties to appropriate types (e.g., integers for IDs and status), while keeping others as they are (e.g., strings or datetime).
     * This transformation is useful when working with the data in array format for database operations, API responses, or other data handling tasks.
     *
     * @param object $job_attribute The job attribute object that contains the attribute data.
     *
     * @return array An associative array representing the job attribute data.
     */
    public static function to_array( object $job_attribute ): array {
        return [
            'id'            => (int) $job_attribute->id,
            'name'          => $job_attribute->name,
            'slug'          => $job_attribute->slug,
            'type_id'       => $job_attribute->type_id, 
            'menu_orderby'  => $job_attribute->menu_orderby, 
            'is_active'     => $job_attribute->is_active,  
            'created_at'    => $job_attribute->created_at, 
            'created_by'    => $job_attribute->created_by,
            'updated_at'    => $job_attribute->updated_at,
            'updated_by'    => $job_attribute->updated_by, 
            'deleted_at'    => $job_attribute->deleted_at,
            'deleted_by'    => $job_attribute->deleted_by,
 
        ];
    }

    /**
     * Prepare datasets for database operation.
     *
     * @since 1.0.0
     *
     * @param array $request
     * @return array
     */
    public function prepare_for_database( array $data ): array {
        $defaults = [
            'name'                => '',
            'slug'                => '',
            'label'               => '',
            'type_id'             => '',
            'menu_orderby'        => '', 
            'is_active'           => '', 
            'created_at'          => current_datetime()->format( 'Y-m-d H:i:s' ),
            'created_by'          => get_current_user_id(),
            
        ];

        $data = wp_parse_args( $data, $defaults );

        // Sanitize template data
        return [
            'name'          => $this->sanitize( $data['name'],          'text' ),
            'slug'          => $this->sanitize( $data['slug'],          'text' ),
            'label'         => $this->sanitize( $data['label'],         'text' ),   
            'menu_orderby'  => $this->sanitize( $data['menu_orderby'],  'number' ), 
            'type_id'       => $this->sanitize( $data['type_id'],       'number' ), 
            'is_active'     => $this->sanitize( $data['is_active'],     'number' ), 
            'created_at'    => $this->sanitize( $data['created_at'],    'text' ),
            'created_by'    => $this->sanitize( $data['created_by'],    'number' ), 
             
        ];
    } 

}
