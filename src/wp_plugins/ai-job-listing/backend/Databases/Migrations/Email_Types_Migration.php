<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Email_Types_Migration extends Db_Migrator {

   /**
     * Migrate the table for email types used in the job listing system.
     *
     * This function creates a table named 'axil_job_listing_email_types' in the WordPress database.
     * The table stores different types of email templates that can be used within the job listing system,
     * such as notification emails, automated response emails, etc. Each type has a name, a slug for identification,
     * an icon, and an activation flag to toggle its use.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate(); 
        $table_email_types = $wpdb->prefix .  'axil_job_listing_email_types'; 
        $schema_email_types = "CREATE TABLE IF NOT EXISTS `$table_email_types` (
                
            `id`           int(20) NOT NULL AUTO_INCREMENT,  
            `name`         varchar(255), 
            `slug`         varchar(255),  
            `icon`         varchar(200)    NOT NULL,
            `is_active`    tinyint(1) Null,     
            `deleted_at`   int             NULL,
            `deleted_by`   int(20)         UNSIGNED NULL,
            
            PRIMARY KEY   (`id`) 
                   
 
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_email_types );
    }
}
 