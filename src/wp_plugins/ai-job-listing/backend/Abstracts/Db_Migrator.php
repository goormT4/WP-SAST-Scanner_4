<?php
namespace Axilweb\AiJobListing\Abstracts;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Database migration class.
 *
 * Abstract class to handle database migration classes.
 */
abstract class Db_Migrator {

	/**
	 * Migrate the database table.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 * */
	abstract public static function migrate();
}
