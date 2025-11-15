<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class General_Settings_Migration extends Db_Migrator {

   /**
     * Migrate the table for general settings.
     *
     * This function creates a table named 'axilweb_ajl_general_settings' in the WordPress database.
     * It's designed to store configurable settings for a job listing plugin or theme, where each setting
     * can be dynamically adjusted through admin interfaces. The settings include labels, types, default
     * values, and potential options for dropdowns or similar inputs.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate(); 
        $table_general_settings = $wpdb->prefix .  'axilweb_ajl_general_settings'; 
        $schema_settings = "CREATE TABLE IF NOT EXISTS `$table_general_settings` (
                
            `id`                int(20) NOT NULL AUTO_INCREMENT,  
            `label`             varchar(255),
            `name`              varchar(255),
            `value`             text NULL,
            `type`              varchar(255) NULL,  
            `form_type`         varchar(255) NULL,  
            `options`           text NULL,  
            `placeholder`       varchar(255) NULL,  
            `column_width`      varchar(255) NULL,   
            `updated_at`        datetime     NULL,
            `updated_by`        int          NULL,
            `deleted_at`        int          NULL,
            `deleted_by`        int(20)      UNSIGNED NULL,
           
            PRIMARY KEY   (`id`), 
                    KEY   `updated_by`       (`updated_by`),
                    KEY   `deleted_by`       (`deleted_by`)
                   
 
        ) $charset_collate";

        // Create the tables.
        dbDelta( $schema_settings );
    }
}
 