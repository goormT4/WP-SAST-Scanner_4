<?php
namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * General_Setting class.
 *
 * @since 0.1.0
 */
class General_Setting extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_general_settings';

    /**
     * General_Setting item to a formatted array.
     *
     * @since 1.0.0
     *
     * @param object $axilweb_ajl_general_settings
     *
     * @return array
     */
    public static function to_array(object $generalSettings): array
    {
        $database_data = [
            'id'                => (int) $generalSettings->id,
            'label'             => $generalSettings->label,
            'name'              => $generalSettings->name,
            'value'             => $generalSettings->value,
            'placeholder'       => $generalSettings->placeholder,
            'type'              => $generalSettings->type,
            'form_type'         => $generalSettings->form_type,
            'options'           => $generalSettings->options,
            'options'           => $generalSettings->options,
            'column_width'      => $generalSettings->column_width,
            'updated_at'        => $generalSettings->updated_at,

        ];
        return $database_data;
    } 
     
}
