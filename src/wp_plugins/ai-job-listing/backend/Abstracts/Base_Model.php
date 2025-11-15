<?php 
namespace Axilweb\AiJobListing\Abstracts;
use Axilweb\AiJobListing\Traits\Input_Sanitizer;
use Axilweb\AiJobListing\Traits\Queryable;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Base model class.
 *
 * @since 0.1.0
 */
abstract class Base_Model {

    use Queryable;
    use Input_Sanitizer;


    /**
     * @var $db
     */
    private $db;

    /**
     * Table name.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $table;

    /**
     * Primary key column of the table.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Created at column of the table.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $created_at_key = 'created_at';

    /**
     * Updated at column of the table.
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $updated_at_key = 'updated_at';

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . $this->table;
    }

    /**
     * Convert item dataset to array.
     *
     * @since 1.0.0
     *
     * @param object $item
     *
     * @return array
     */
    abstract public static function to_array( object $item ): array;
}
