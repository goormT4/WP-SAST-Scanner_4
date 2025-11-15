<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Application_Process_By_Job_Migration extends Db_Migrator {

   /**
     * Migrate the job listing process table.
     *
     * This function creates the `axilweb_ajl_app_process_by_job` table in the WordPress 
     * database if it does not already exist. The table stores the relationship between job listings 
     * and their associated application processes, allowing for multiple processes to be linked to a job. 
     * It includes indexing and a primary key for efficient queries.
     *
     * The table includes the following fields:
     * - `id`: A unique identifier for each record (primary key).
     * - `job_id`: The job listing ID to which the application process is associated.
     * - `process_id`: The ID of the specific process related to the job.
     * - `order`: The order in which the process should appear or be executed in the sequence.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_app_process_by_job = $wpdb->prefix .  'axilweb_ajl_app_process_by_job';
        $schema_jobs = "CREATE TABLE IF NOT EXISTS `$table_app_process_by_job` ( 

        `id`                          int             NOT NULL AUTO_INCREMENT, 
        `job_id`                      varchar(255)    NOT NULL,
        `process_id`                  varchar(255)    NOT NULL,
        `order`                       tinyint(4)      NOT NULL,
        PRIMARY KEY (`id`)
        ) $charset_collate";

        // Create the tables.
        dbDelta($schema_jobs);
    }
}
