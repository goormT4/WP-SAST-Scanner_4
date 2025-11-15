<?php 
namespace Axilweb\AiJobListing\Manager; 
use Axilweb\AiJobListing\Models\Email_Type;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Email_Types_Manager
{

    /**
     * Email_Typess class.
     *
     * @var class email_type

    */
    public $email_type;
    protected $per_page = 50;
    protected $page = 1;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->email_type = new Email_Type();
    }

    /**
     * Retrieve Email Types.
     *
     * Fetches a list of email types based on the provided query arguments.
     * Supports pagination, sorting, and filtering. If the `count` argument 
     * is set to true, it returns the total number of email types instead of the list.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to query email types. Default empty array.
     *
     *     @type int    $page     The current page number. Default is 1.
     *     @type int    $per_page The number of email types per page. Default is 50.
     *     @type string $orderby  The column to sort by. Default is 'id'.
     *     @type string $order    The sort direction. Accepts 'ASC' or 'DESC'. Default is 'DESC'.
     *     @type string $search   Search term to filter email types. Default is empty.
     *     @type bool   $count    Whether to return only the total count of email types. Default is false.
     *     @type array  $where    Additional conditions to filter email types. Default is empty.
     * }
     *
     * @return array|int List of email types on success, or the total count if `count` is true.
     */ 
    public function all(array $args = [])
    {
        // Default arguments
        $defaults = [
            'page'     => 1,
            'per_page' => 50,
            'orderby'  => 'id',
            'order'    => 'DESC',
            'search'   => '',
            'count'    => false,
            'where'    => [],
        ];

        // Parse arguments with defaults
        $args = wp_parse_args($args, $defaults);

        $where_clauses = [];

        // Dynamically prepare `where` block
        if (!empty($args['search'])) {
            $where_clauses['title'] = [
                'operator' => 'LIKE',
                'value'    => '%' . sanitize_text_field($args['search']) . '%',
            ];
        }

        // Assign the formatted `where` clauses back to args
        $args['where'] = $where_clauses;

        // Call Queryable's `all` method
        $email_type = $this->email_type->all($args);

        // Return count if requested
        if ($args['count']) {
            return (int) $email_type;
        }

        return $email_type;
    }


    /**
     * Retrieve a Single Email Type.
     *
     * Fetches a specific email type based on the provided key-value pair. 
     * The `key` specifies the database column to search, and the `value` is the value to match.
     *
     * @since 1.0.0
     *
     * @param array $args {
     *     Optional. Arguments to retrieve a specific email type. Default empty array.
     *
     *     @type string $key   The database column to search by. Default is 'id'.
     *     @type mixed  $value The value to match in the specified column. Default is an empty string.
     * }
     *
     * @return array|null The email type data as an associative array on success, or null if no matching record is found.
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

        return $this->Email_Types->get_by($args['key'], $args['value']);
    } 
  
}
