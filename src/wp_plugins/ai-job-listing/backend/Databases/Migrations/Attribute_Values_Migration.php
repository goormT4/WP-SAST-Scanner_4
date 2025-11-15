<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Attribute_Values_Migration extends Db_Migrator {

    /**
     * Migrate the job listing attribute values table.
     *
     * This function creates the `axilweb_ajl_attribute_values` table in the WordPress 
     * database if it does not already exist. The table is used to store attribute values 
     * associated with job listings. These values can be various metadata like job categories, 
     * types, locations, etc. It also includes necessary indexes for efficient querying and 
     * optimization of operations related to job attributes.
     *
     * The table includes the following fields:
     * - `id`: A unique identifier for each attribute value.
     * - `value`: The value of the attribute (e.g., "Full-Time", "Remote").
     * - `slug`: A unique slug representing the attribute value.
     * - `attribute_id`: The ID of the attribute that this value is related to.
     * - `menu_orderby`: The order in which the attribute value should appear in the UI.
     * - `is_active`: A flag indicating whether the attribute value is active (1 for active, 0 for inactive).
     * - `created_at`: The timestamp when the record was created.
     * - `created_by`: The ID of the user who created the record.
     * - `updated_at`: The timestamp when the record was last updated.
     * - `updated_by`: The ID of the user who last updated the record.
     * - `deleted_at`: The timestamp when the record was deleted (if applicable).
     * - `deleted_by`: The ID of the user who deleted the record (if applicable).
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_attribute_values = $wpdb->prefix .  'axilweb_ajl_attribute_values';;
        $schema_attribute_values = "CREATE TABLE IF NOT EXISTS `{$table_attribute_values}` (
            `id`                 int                      NOT NULL AUTO_INCREMENT, 
            `value`              varchar(200)             NOT NULL,
            `slug`               varchar(200)             NOT NULL,
            `attribute_id`       tinyint(4)               NOT NULL,
            
            `menu_orderby`       int                      NULL,
            `is_active`          tinyint(4)      UNSIGNED NOT NULL, 
            `created_at`         datetime                 NULL,
            `created_by`         int             UNSIGNED NULL,
            `updated_at`         datetime                 NULL,
            `updated_by`         int             UNSIGNED NULL,  
            `deleted_at`         datetime                 NULL,
            `deleted_by`         int             UNSIGNED NULL,
            
            PRIMARY KEY         (`id`),
            UNIQUE KEY          `slug`              (`slug`),
            KEY                 `attribute_id`    (`attribute_id`),
            KEY                 `created_by`      (`created_by`),
            KEY                 `updated_by`      (`updated_by`),
            KEY                 `deleted_by`      (`deleted_by`)
        ) $charset_collate;";

        // Create the tables.
        dbDelta( $schema_attribute_values );
    }
}
