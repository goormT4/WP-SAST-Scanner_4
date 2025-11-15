<?php
namespace Axilweb\AiJobListing\Databases\Migrations;
use Axilweb\AiJobListing\Abstracts\Db_Migrator;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Attributes_Migration extends Db_Migrator {

    /**
     * Migrate the attributes table.
     *
     * This function creates the 'axilweb_ajl_attributes' table in the WordPress database.
     * It is used to store attribute definitions for job listings, including properties such as
     * name, icons, whether the attribute is required, and other metadata. The table supports
     * CRUD operations with fields for tracking creation, updates, and deletions.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function migrate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_attributes = $wpdb->prefix .  'axilweb_ajl_attributes'; 
        $schema_attributes = "CREATE TABLE IF NOT EXISTS `$table_attributes` (
            `id`                            int             NOT NULL AUTO_INCREMENT,
            `name`                          varchar(200)    NOT NULL,
            `icon`                          varchar(200)    NOT NULL,
            `is_required`                   TINYINT(1)      NULL,
            `form_key`                      varchar(200)    NOT NULL,
            `slug`                          varchar(200)    NOT NULL,
            `label`                         varchar(200)    NULL,
            `type_id`                       int             NULL,
            `menu_orderby`                  int             NULL,
            `is_active`                     tinyint(1)      UNSIGNED NOT NULL,
            `created_at`                    datetime        NULL,
            `created_by`                    int             UNSIGNED NULL,
            `updated_at`                    datetime        NULL,
            `updated_by`                    int(20)         UNSIGNED NULL,  
            `deleted_at`                    int             NULL,
            `deleted_by`                    int(20)         UNSIGNED NULL,
            
            PRIMARY KEY         (`id`),
            UNIQUE KEY          `slug`         (`slug`),
            KEY                 `created_by`   (`created_by`),
            KEY                 `updated_by`   (`updated_by`),
            KEY                 `deleted_by`   (`deleted_by`)
        ) $charset_collate;";

        // Create the tables.
        dbDelta( $schema_attributes );
    }
}
