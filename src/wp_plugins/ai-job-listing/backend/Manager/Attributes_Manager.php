<?php
namespace Axilweb\AiJobListing\Manager; 
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Attributes;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Attributes_Manager 
{

    /**
     * job_attribute class.
     *
     * @var attributes
     */
    public $attributes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = new Attributes();
    }

    /**
     * Get all attributes by Jobs.
     *
     * @since 1.0.0
     *
     * @param array $args
     * @return array|object|string|int
     */
    public function all(array $args = [])
    {
        // Default arguments
        $defaults = [
            'page'     => AXILWEB_AJL_DEFAULT_PAGE,
            'per_page' => AXILWEB_AJL_POSTS_PER_PAGE,
            'orderby'  => AXILWEB_AJL_DEFAULT_ORDERBY,
            'order'    => 'ASC',
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        // Merge provided arguments with defaults
        $args = wp_parse_args($args, $defaults);

        // Prepare the `where` block as an array for Queryable
        $where_clauses = [];

        // Add `id` condition
        if (!empty($args['id'])) {
            $where_clauses['id'] = [
                'operator' => '=',
                'value'    => absint($args['id']),
            ];
        }

        // Add `slug` condition
        if (!empty($args['slug'])) {
            $where_clauses['slug'] = [
                'operator' => 'LIKE',
                'value'    => '%' . sanitize_text_field(wp_unslash($args['slug'])) . '%',
            ];
        }

        // Add `status` condition
        if (isset($args['status']) && $args['status'] === 'trash') {
            $where_clauses['deleted_at'] = [
                'operator' => 'IS NOT',
                'value'    => null,
            ];
        } else {
            $where_clauses['deleted_at'] = [
                'operator' => 'IS',
                'value'    => null,
            ];
        }

        // Add `search` condition
        if (!empty($args['search'])) {
            $where_clauses['name'] = [
                'operator' => 'LIKE',
                'value'    => '%' . sanitize_text_field(wp_unslash($args['search'])) . '%',
            ];
        }

        // Assign the prepared `where` clauses to args
        $args['where'] = $where_clauses;

        // Call the Queryable `all` method
        $attributes = $this->attributes->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $attributes;
        }

        return $attributes;
    } 

}
