<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Applications_Migration extends Db_Migrator {

    /**
     * Migrate the job listing applications table.
     *
     * This function creates the `axilweb_ajl_applications` table in the WordPress 
     * database if it does not already exist. The table stores job application information 
     * related to specific job listings, including the current and previous process stages 
     * of the application, and metadata about the application status (e.g., whether it has been read).
     * 
     * The table includes the following fields:
     * - `id`: A unique identifier for each application (primary key).
     * - `job_id`: The ID of the job listing associated with the application.
     * - `process_id`: The ID of the current application process (e.g., "phone interview", "final interview").
     * - `previous_process_id`: The ID of the previous application process.
     * - `is_read`: A flag to indicate whether the application has been read (1 for read, 0 for unread).
     * - `created_at`: The timestamp of when the application was created.
     * - `created_by`: The ID of the user who created the application.
     * - `updated_at`: The timestamp of when the application was last updated.
     * - `updated_by`: The ID of the user who last updated the application.
     * - `deleted_at`: The timestamp of when the application was deleted (if applicable).
     * - `deleted_by`: The ID of the user who deleted the application (if applicable).
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate(); 
        $table_applications = $wpdb->prefix .  'axilweb_ajl_applications';
        $schema_jobs = "CREATE TABLE IF NOT EXISTS `$table_applications` (
                
            `id`                      int            UNSIGNED  NOT NULL AUTO_INCREMENT,  
            `job_id`                  int            UNSIGNED  NOT NULL,
            `process_id`              int            UNSIGNED  NOT NULL,
            `previous_process_id`     int            UNSIGNED   NULL,
            `is_read`                 int            UNSIGNED  NOT NULL DEFAULT '0', 
            `created_at`              datetime                 NULL,
            `created_by`              int            UNSIGNED  NOT NULL,
            `updated_at`              datetime                 NULL,
            `updated_by`              int            UNSIGNED  NULL,  
            `deleted_at`              datetime                 NULL,
            `deleted_by`              int            UNSIGNED  NULL,
            
            PRIMARY KEY (`id`),  

            KEY                 `job_id`                  (`job_id`),
            KEY                 `process_id`              (`process_id`),
            KEY                 `created_by`              (`created_by`),
            KEY                 `updated_by`              (`updated_by`),
            KEY                 `deleted_by`              (`deleted_by`)
 
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_jobs );
    }
}
