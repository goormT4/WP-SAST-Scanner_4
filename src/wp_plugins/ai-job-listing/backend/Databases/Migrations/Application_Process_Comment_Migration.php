<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Application_Process_Comment_Migration extends Db_Migrator {

    /**
     * Migrate the job listing application process comments table.
     *
     * This function creates the `axilweb_ajl_app_process_comment` table in the WordPress 
     * database if it does not already exist. The table is used to store comments related to 
     * the application process for job listings. It links comments to the application ID, 
     * application process ID, and user ID (if applicable), allowing users to leave comments 
     * or notes on the application process.
     *
     * The table includes the following fields:
     * - `id`: A unique identifier for each comment (primary key).
     * - `user_id`: The ID of the user who created the comment (optional, can be `NULL`).
     * - `app_id`: The ID of the application to which the comment is related.
     * - `app_process_id`: The ID of the specific process stage the comment pertains to.
     * - `comment`: The comment text.
     * - `created_at`: The date the comment was created.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_app_process_comment = $wpdb->prefix .  'axilweb_ajl_app_process_comment';
        $schema_jobs = "CREATE TABLE IF NOT EXISTS `$table_app_process_comment` ( 

        `id`                        int    NOT NULL AUTO_INCREMENT,  
        `user_id`                   int UNSIGNED DEFAULT NULL,
        `app_id`                    int UNSIGNED NOT NULL,
        `app_process_id`            int UNSIGNED NOT NULL, 
        `comment`                   varchar(255) NULL, 
        `created_at`                date                NULL,
        `deleted_at`                datetime            NULL,
        PRIMARY KEY (`id`)
        ) $charset_collate";

        // Create the tables.
        dbDelta($schema_jobs);
    }
}
