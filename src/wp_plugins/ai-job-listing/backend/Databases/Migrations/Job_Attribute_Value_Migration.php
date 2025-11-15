<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Job_Attribute_Value_Migration extends Db_Migrator {

    /**
     * Migrate the job_attribute_value table.
     *
     * This function sets up the 'axilweb_ajl_job_attribute_value' table in the WordPress database,
     * linking job listings to various attributes for enhanced categorization and filtering.
     * The function creates the table with necessary fields and adds foreign key constraints to maintain
     * data integrity between it and the primary jobs table.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_job_attribute_value = $wpdb->prefix .  'axilweb_ajl_job_attribute_value';
        $table_jobs = $wpdb->prefix .  'axilweb_ajl_jobs';
        $schema_job_meta_value = "CREATE TABLE IF NOT EXISTS `$table_job_attribute_value` (
            `id`                    int         UNSIGNED NOT NULL AUTO_INCREMENT, 
            `job_id`                int         UNSIGNED NULL, 
            `attribute_value_id`    int         UNSIGNED NULL,   
             
            PRIMARY KEY         (`id`),  
            KEY                 `job_id`                (`job_id`),
            KEY                 `attribute_value_id`    (`attribute_value_id`),
            FOREIGN KEY (job_id) 
            REFERENCES {$table_jobs}(id)
            ON UPDATE CASCADE ON DELETE CASCADE
            
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_job_meta_value );
    }
}


