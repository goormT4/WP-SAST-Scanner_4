<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Email_Templates_Migration extends Db_Migrator {

    /**
     * Migrate the table for email templates.
     *
     * This function creates the 'axil_job_listing_email_templates' table in the WordPress database.
     * It stores email templates related to different job notification types, including various flags
     * to control notification behaviors and recipients. The table structure includes indexing for
     * quick lookups on frequently accessed columns like 'updated_by' and 'type_id'.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate    = $wpdb->get_charset_collate(); 
        $table_email_templates = $wpdb->prefix .  'axil_job_listing_email_templates'; 
        $schema_templates = "CREATE TABLE IF NOT EXISTS `$table_email_templates` (
                
            `id`                        int(20) NOT NULL AUTO_INCREMENT,  
            `type_id`                   int(11),  
            `receiver_type`             varchar(255) NULL, 
            `has_notification_status`   tinyint(1) Null,    
            `should_email_sent_to_all`  tinyint(1) Null default 1,    
            `subject`                   text NULL, 
            `message`                   text NULL, 
           
            `updated_at`            datetime        NULL,
            `updated_by`            int             NULL,
            `deleted_at`                    int             NULL,
            `deleted_by`                    int(20)         UNSIGNED NULL,

           
            PRIMARY KEY   (`id`), 
                    KEY   `updated_by`       (`updated_by`),
                    KEY   `type_id`          (`type_id`)
                   
 
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_templates );
    }
}
 