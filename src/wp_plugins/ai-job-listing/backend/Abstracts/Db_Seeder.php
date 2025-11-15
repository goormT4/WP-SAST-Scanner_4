<?php
namespace Axilweb\AiJobListing\Abstracts;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstract class to handle the seeder classes.
 *
 * @since 0.1.0
 */
abstract class Db_Seeder {

    /**
     * Run the seeders of the database.
     *
     * @since 1.0.0
     *
     * @return void
     */
    abstract public function run();
}
