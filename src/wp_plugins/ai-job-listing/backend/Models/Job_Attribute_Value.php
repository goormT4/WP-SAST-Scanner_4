<?php

namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Job_Attribute_Value class.
 *
 * @since 0.1.0
 */
class Job_Attribute_Value extends Base_Model {

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_job_attribute_value';


    /**
     * Converts a job attribute value object into an associative array.
     *
     * This function takes a `job_attribute_values` object and converts its properties into an associative array.
     * The properties are cast to appropriate types (e.g., integers for IDs), and other values are kept as they are.
     * This transformation is useful when working with the data in array format for database operations, API responses, or other data handling tasks.
     *
     * @param object $job_attribute_values The job attribute values object containing the data.
     *
     * @return array An associative array representing the job attribute values.
     */
    public static function to_array( object $job_attribute_values ): array {
        $database_data = [
            'id'                          => (int) $job_attribute_values->id,
            'job_id'                      => $job_attribute_values->job_id,
            'attribute_value_id'          => (int) $job_attribute_values->attribute_value_id,  
 
        ]; 
        return $database_data;
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
        'job_id'                    => '',
        'attribute_value_id'        => '',  
        ];

        $data = wp_parse_args( $data, $defaults );
        // Sanitize template data
        return [
            'job_id'                  => $this->sanitize( $data['job_id'],                  'number' ),
            'attribute_value_id'      => $this->sanitize( $data['attribute_value_id'],      'number' ),  
             
        ];
    } 

}
