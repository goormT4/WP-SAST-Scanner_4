<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Jobs_Migration extends Db_Migrator {

    /**
     * Migrate the jobs table.
     *
     * This function sets up the 'axilweb_ajl_jobs' table in the WordPress database.
     * It stores detailed information about job listings, including descriptions, salary information,
     * SEO metadata, and tracking data for record management. The table structure ensures
     * optimization for queries with indexed keys for creators, updaters, and deleters.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_jobs = $wpdb->prefix .  'axilweb_ajl_jobs';
        $schema_jobs = "CREATE TABLE IF NOT EXISTS `$table_jobs` (
            `id`                            int        UNSIGNED NOT NULL AUTO_INCREMENT,
            `title`                         varchar(255)        NULL,
            `slug`                          varchar(255)        NULL,  
            `no_of_vacancies`               VARCHAR(10)         NULL, 
            `application_deadline`          date                NULL,  
            `is_required_cv_photo`          tinyint(4)          NULL, 
            `description`                   text,
            `responsibilities`              text, 
            `min_salary`                    decimal(15,2),  
            `max_salary`                    decimal(15,2),   
            `benefits`                      text,  
            `requirements`                  text,  
            `experienced_year`              tinyint(4)          NULL,
            `additional_requirements`       text,  
            `additional_notes`              text,   
            `status`                        varchar(255)        NULL, 
            `total_views`                   int                 NULL,
            `seo_title`                     text Null,
            `seo_description`               text Null,
            `feature_image`                 varchar(191)        NULL,
            `job_notification_emails`        text               NULL,
            `deleted_by`                    int                 NULL,
            `created_at`                    datetime            NULL,
            `created_by`                    int                 NULL,
            `updated_by`                    int                 NULL,  
            `updated_at`                    datetime            NULL,
            `deleted_at`                    datetime            null,
            `form_step_complete`            tinyint(4)          NULL,
            `total_view_count`              varchar(255)        NULL, 
             
            PRIMARY KEY         (`id`), 
            UNIQUE KEY          `slug`                         (`slug`),  
            KEY                 `created_by`                   (`created_by`),
            KEY                 `updated_by`                   (`updated_by`),
            KEY                 `deleted_by`                   (`deleted_by`)
             

        ) $charset_collate";

        // Create the tables.
        dbDelta($schema_jobs);
    }
}
