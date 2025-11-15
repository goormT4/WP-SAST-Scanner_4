<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Application_Process_Migration extends Db_Migrator {

    /**
     * Migrate the job listing application process table.
     *
     * This function creates the `axilweb_ajl_app_process` table in the WordPress 
     * database if it does not already exist. The table stores information about different 
     * stages or processes in the job application workflow. These processes could include stages 
     * like "Application Received", "Phone Interview", "Final Interview", etc.
     *
     * The table includes the following fields:
     * - `id`: A unique identifier for each process.
     * - `name`: The name of the application process (e.g., "Phone Interview").
     * - `key`: A unique key for the application process (e.g., "phone_interview").
     * - `icon`: An optional field to store an icon associated with the process.
     * - `icon_color`: An optional field to define the color of the icon.
     * - `order`: The order in which the process should appear or be executed.
     * - `created_at`: The timestamp when the process was created.
     * - `created_by`: The ID of the user who created the process.
     * - `updated_at`: The timestamp when the process was last updated.
     * - `updated_by`: The ID of the user who last updated the process.
     * - `deleted_at`: The timestamp when the process was deleted (if applicable).
     * - `deleted_by`: The ID of the user who deleted the process (if applicable).
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_app_process = $wpdb->prefix .  'axilweb_ajl_app_process';
        $schema_jobs = "CREATE TABLE IF NOT EXISTS `$table_app_process` ( 

        `id`                    int               NOT NULL AUTO_INCREMENT, 
        `name`                  varchar(100)      NOT NULL,
        `key`                   varchar(100)      NOT NULL,
        `icon`                  varchar(100)       NULL,
        `icon_color`            varchar(10)       NULL,
        `order`                 tinyint(4)        NOT NULL,
        `created_at`            datetime          NULL,
        `created_by`            int      UNSIGNED NOT NULL,
        `updated_at`            datetime          NULL,
        `deleted_at`            datetime          NULL,
        `deleted_by`            int      UNSIGNED NULL,
        `updated_by`            int      UNSIGNED NULL,  
        
        PRIMARY KEY (`id`), 
        KEY                 `created_by`    (`created_by`),
        KEY                 `updated_by`    (`updated_by`),
        KEY                 `deleted_by`    (`deleted_by`)
        
        ) $charset_collate";

        // Create the tables.
        dbDelta($schema_jobs);
    }
}
