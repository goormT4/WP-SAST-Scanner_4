<?php

namespace Axilweb\AiJobListing\Models;

use WP_REST_Request;
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * AttributeValues class.
 *
 * @since 0.1.0
 */
class Attribute_Values extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_attribute_values';


    /**
     * Converts an object representing attribute values into an associative array.
     *
     * This function takes an object that contains the data for a specific attribute value and
     * converts it into an array. The array includes relevant fields such as ID, value, slug,
     * associated attribute ID, and timestamps for creation, updates, and deletions. The function
     * ensures that the correct data types are applied (e.g., casting integers) and also includes
     * helper methods like `get_attribute_slug()` to fetch associated data.
     *
     * @param object $attribute_values The object containing the attribute values to be converted.
     *
     * @return array The converted associative array containing the attribute values.
     */
    public static function to_array(object $attribute_values): array
    {


        $database_data = [
            'id'                    => (int) $attribute_values->id,
            'value'                 => $attribute_values->value,
            'slug'                  => $attribute_values->slug,
            'attribute_id'          => (int) $attribute_values->attribute_id,
            'attribute_slug'        => static::get_attribute_slug($attribute_values),
            'menu_orderby'          => (int) $attribute_values->menu_orderby,
            'is_active'             => (int) $attribute_values->is_active,
            'created_at'            => $attribute_values->created_at,
            'created_by'            => (int) $attribute_values->created_by,
            'updated_at'            => $attribute_values->updated_at,
            'updated_by'            => $attribute_values->updated_by,
            'deleted_at'            => $attribute_values->deleted_at,
            'deleted_by'            => $attribute_values->deleted_by,

        ];
        return $database_data;
    }

    /**
     * Retrieves the name and slug of an attribute by its ID.
     *
     * This function fetches the `name` and `slug` columns for a specific attribute
     * based on the `attribute_id` present in the provided `attribute_values` object.
     * If the `attribute_id` is invalid or not provided, the function returns `null`.
     *
     * @since 1.0.0
     *
     * @param object|null $attribute_values The object containing the `attribute_id`.
     * @return object|null The attribute data (name and slug) as an object, or null if not found or invalid.
     */
    public static function get_attribute_slug(?object $attribute_values): ?object
    {
        $attributes = new Attributes();

        $columns = 'name, slug';
        return $attributes->get((int) $attribute_values->attribute_id, $columns);
    }

    /**
     * Prepares a single email template for create or update.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request Request object.
     *
     * @return object|WP_Error
     */
    public static function prepare_create_item_for_database($request)
    {
        $data = [];
        $data['value']              = $request['value'];
        $data['slug']               = self::generate_unique_slug($request);
        $data['attribute_id']       = $request['attribute_id'];
        $data['menu_orderby']       = empty($request['menu_orderby']) ? '1' : absint($request['menu_orderby']);
        $data['is_active']          = empty($request['is_active']) ? '1' : absint($request['is_active']);
        $data['created_at']         = current_datetime()->format('Y-m-d H:i:s');
        $data['created_by']         = empty($request['created_by']) ? get_current_user_id() : absint($request['created_by']);
        $data['updated_at']         = current_datetime()->format('Y-m-d H:i:s');
        $data['updated_by']         = empty($request['updated_by']) ? get_current_user_id() : absint($request['updated_by']);
        return $data;
    }

    /**
     * Generate unique slug if no slug is provided.
     *
     * @since 1.0.0
     *
     * @param WP_REST_Request $request
     *
     * @return string
     */
    public static function generate_unique_slug(WP_REST_Request $request)
    {
        $slug = $request['slug'];

        if (empty($slug)) {
            $slug = sanitize_title($request['value']);
            $slug = str_replace(' ', '-', $slug);

            // Auto-generate only for create page.
            if (empty($request['id'])) {
                $existing_attribute_values = axilweb_ajl_jobs()->attributes_values_manager->get(
                    [
                        'key' => 'slug',
                        'value' => $slug,
                    ]
                );
                // If error, means, there is no slug by this slug
                if (empty($existing_attribute_values)) {
                    return $slug;
                }

                return self::generate_beautiful_slug($slug);
            }
        }

        return $slug;
    }

    /**
     * Prepares data for updating an item in the database.
     *
     * This function constructs an associative array from the given request data, ensuring
     * proper sanitization and default values for missing fields. It includes fields
     * such as `value`, `slug`, `attribute_id`, `menu_orderby`, `is_active`, `updated_at`, and `updated_by`.
     *
     * @since 1.0.0
     *
     * @param array $request The incoming data for the update.
     * @return array The prepared data for database update.
     */
    public static function prepare_update_item_for_database($request)
    {

        $data = [];
        $data['value']              = $request['value'];
        $data['slug']               = self::generate_unique_slug($request);
        $data['attribute_id']       = $request['attribute_id'];
        $data['menu_orderby']       = (isset($request['menu_orderby'])) ? $request['menu_orderby'] : 1;
        $data['is_active']          = (isset($request['is_active'])) ? $request['is_active'] : 1;
        $data['updated_at']         = current_datetime()->format('Y-m-d H:i:s');
        $data['updated_by']         = empty($request['updated_by']) ? get_current_user_id() : absint($request['updated_by']);
        return $data;
    }

    /**
     * Generate beautiful slug.
     *
     * @since 1.0.0
     *
     * @param string $slug
     * @param integer $i
     *
     * @return string
     */
    public static function generate_beautiful_slug(string $slug = '', $i = 1): string
    {
        while (true) {
            $new_slug     = $slug . '-' . $i;
            $existing_attribute_values = axilweb_ajl_jobs()->attributes_values_manager->get(
                [
                    'key' => 'slug',
                    'value' => $new_slug,
                ]
            );

            if (empty($existing_attribute_values)) {
                return $new_slug;
            } else {
                self::generate_beautiful_slug($slug, $i + 1);
            }

            $i++;
        }
    }
}
