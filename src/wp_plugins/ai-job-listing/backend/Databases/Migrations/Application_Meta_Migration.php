<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Application_Meta_Migration extends Db_Migrator {

    /**
     * Migrate the job listing application meta table.
     *
     * This function creates the `axilweb_ajl_application_meta` table in the WordPress 
     * database if it does not already exist. The table stores metadata for job applications, 
     * such as application ID, metadata key-value pairs, timestamps for creation and updates, 
     * and information about who created, updated, or deleted the record.
     * The function uses the `dbDelta()` function to ensure the table is created and updated correctly.
     *
     * The table includes the following fields:
     * - `id`: A unique identifier for each record.
     * - `app_id`: The ID of the associated job application.
     * - `app_mk`: The key of the metadata.
     * - `app_mv`: The value of the metadata.
     * - `created_at`: The timestamp of when the record was created.
     * - `created_by`: The ID of the user who created the record.
     * - `updated_at`: The timestamp of when the record was last updated.
     * - `updated_by`: The ID of the user who last updated the record.
     * - `deleted_at`: The timestamp of when the record was deleted.
     * - `deleted_by`: The ID of the user who deleted the record.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_application_meta = $wpdb->prefix .  'axilweb_ajl_application_meta';
        $schema_jobs = "CREATE TABLE IF NOT EXISTS `$table_application_meta` (
                
            `id`                            int              UNSIGNED              NOT NULL AUTO_INCREMENT, 
            `app_id`                        int              UNSIGNED              NOT NULL,
            `app_mk`                      varchar(255)                           NULL,
            `app_mv`                        longtext                               NULL, 
            `created_at`                    datetime                               NULL,
            `created_by`                    int              UNSIGNED              NOT NULL,
            `updated_at`                    datetime                               NULL,
            `updated_by`                    int              UNSIGNED              NULL,  
            `deleted_at`                    datetime                               NULL,
            `deleted_by`                    int              UNSIGNED              NULL,
            
            PRIMARY KEY (`id`),   
            KEY                             `app_id`                      (`app_id`),
            KEY                             `created_by`                  (`created_by`),
            KEY                             `updated_by`                  (`updated_by`),
            KEY                             `deleted_by`                  (`deleted_by`)
 
        ) $charset_collate";

        // Create the tables.
        dbDelta($schema_jobs);
    }
}
