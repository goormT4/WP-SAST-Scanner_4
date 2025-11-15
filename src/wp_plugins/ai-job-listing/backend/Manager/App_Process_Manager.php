<?php
namespace Axilweb\AiJobListing\Manager;
use Axilweb\AiJobListing\Models\App_Process;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class App_Process_Manager
{

    /**
     * app_process value class.
     *
     * @var app_process
     */
    public $app_process;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->app_process = new App_Process();
    }

    /**
     * Retrieves a list of processes based on the specified arguments.
     *
     * This method fetches processes from the database, supporting pagination, ordering, 
     * and optional filtering through search and custom conditions. It returns a list of processes 
     * or the total count, depending on the `count` argument.
     *
     * @param array $args Optional. An array of arguments to customize the query.
     *                    Supported keys:
     *                    - 'page' (int): The page number for pagination (default: 1).
     *                    - 'per_page' (int): The number of items per page (default: 10).
     *                    - 'orderby' (string): The column by which to order the results (default: 'id').
     *                    - 'order' (string): The direction of the order (default: 'DESC').
     *                    - 'search' (string): A search term to filter the title or description.
     *                    - 'count' (bool): Whether to return the total count of records instead of the actual data.
     *                    - 'where' (array): Custom SQL conditions to be applied to the query.
     *
     * @return array|int Returns an array of process records when `count` is false, or an integer count of records
     *                   if `count` is true. If no records are found, an empty array is returned.
     */ 
    public function all(array $args = [])
    {
        // Default arguments
        $defaults = [
            'page'     => 1,
            'per_page' => 10,
            'orderby'  => 'id',
            'order'    => 'DESC',
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        // Merge defaults with provided arguments
        $args = wp_parse_args($args, $defaults);

        // Prepare the `where` block as an array for Queryable
        $where_clauses = [];

        // Add search filter
        if (!empty($args['search'])) {
            $like = '%' . sanitize_text_field(wp_unslash($args['search'])) . '%';
            $where_clauses['title'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
            $where_clauses['description'] = [
                'operator' => 'LIKE',
                'value'    => $like,
            ];
        }

        // Assign the structured `where` block to args
        $args['where'] = $where_clauses;

        // Call the Queryable `all` method
        $app_process = $this->app_process->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $app_process;
        }

        return $app_process;
    }

 
    /**
     * Retrieves a single process based on the specified key-value pair.
     *
     * This method fetches a single process record from the database by matching the specified
     * key (e.g., 'id', 'slug') with the provided value. If the value is empty, it returns `null`.
     *
     * @param array $args {
     *     Optional. An array of arguments to filter the query.
     * 
     *     @type string $key   The field to match against (e.g., 'id', 'slug'). Default is 'id'.
     *     @type string $value The value to search for in the specified key field.
     * }
     * 
     * @return mixed Returns the process record if found, or `null` if no record is found or the value is empty.
     */ 
    public function get(array $args = [])
    {
        $defaults = [
            'key' => 'id',
            'value' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        if (empty($args['value'])) {
            return null;
        }

        return $this->app_process->get_by($args['key'], $args['value']);
    }
 
}
