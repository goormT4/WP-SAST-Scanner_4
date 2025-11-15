<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Admin_Email_Email_Template_Migration extends Db_Migrator {

    /**
     * Migrate the table for the job listing admin email template.
     *
     * This function creates the `axil_job_listing_admin_email_email_template` table in the WordPress 
     * database, if it does not already exist. The table stores relationships between job listings 
     * and admin email templates, allowing for customized emails to be sent to administrators based 
     * on job-related events. It also includes relevant indexes for performance optimization.
     *
     * The table includes the following fields:
     * - `id`: A unique identifier for each record.
     * - `job_id`: The ID of the job listing associated with the email template.
     * - `admin_email_id`: The ID of the administrator email template.
     * - `updated_at`: The timestamp for when the record was last updated.
     * - `updated_by`: The ID of the user who last updated the record.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate    = $wpdb->get_charset_collate(); 
        $table_admin_email_email_template = $wpdb->prefix .  'axil_job_listing_admin_email_email_template'; 
        $schema_template = "CREATE TABLE IF NOT EXISTS `$table_admin_email_email_template` (
                
            `id`                    int(20) NOT NULL AUTO_INCREMENT, 
            `job_id`                int(11) NULL,  
            `admin_email_id`        int(11) NULL,   
            `updated_at`            datetime        NULL,
            `updated_by`            int             NULL,
           
            PRIMARY KEY   (`id`), 
                    KEY   `updated_by`       (`updated_by`),
                    KEY   `admin_email_id`          (`admin_email_id`),
                    KEY   `email_template_id`       (`email_template_id`)
                   
 
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_template );
    }
}
 