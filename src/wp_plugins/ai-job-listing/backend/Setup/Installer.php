<?php

namespace Axilweb\AiJobListing\Setup;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class Installer.
 *
 * Install necessary database tables and options for the plugin.
 */
class Installer
{

    /**
     * Execute Plugin Installation or Update.
     *
     * This method performs essential setup tasks during plugin activation or update:
     * - Updates the installed version.
     * - Registers and creates necessary database tables.
     * - Runs database seeders for initial data population.
     *
     * @since 1.0.0
     *
     * @return void
     */ 
    public function run(): void
    {
        // Update the installed version.
        $this->add_version();  
        // Register and create tables.
        $this->create_tables();  
        // Run the database seeders.
        $seeder = new \Axilweb\AiJobListing\Databases\Seeder\Manager();
        $seeder->run();
    }
 
    /**
     * Update Plugin Installation and Version Information.
     *
     * Checks if the plugin has been installed before. If not, it records the current timestamp
     * as the installation time. Additionally, updates the plugin's version in the WordPress
     * options table to ensure version consistency across updates.
     *
     * @since 1.0.0
     *
     * @return void
     */ 
    public function add_version(): void
    {
        $installed = get_option(Keys::AXILWEB_AJL_INSTALLED);

        if (!$installed) {
            update_option(Keys::AXILWEB_AJL_INSTALLED, time());
        }

        update_option(Keys::AXILWEB_AJL_VERSION, AXILWEB_AJL_VERSION);
    }
 
    /**
     * Create Database Tables for the Plugin.
     *
     * Ensures the `dbDelta` function is available and runs database migrations for all required
     * tables used by the plugin. Each migration class handles the creation or update of its
     * respective table schema.
     *
     * @since 1.0.0
     *
     * @return void
     */ 
    public function create_tables()
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // Run the database table migrations. 
        \Axilweb\AiJobListing\Databases\Migrations\Jobs_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Attributes_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Attribute_Values_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Job_Attribute_Value_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Applications_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Application_Meta_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Application_Process_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\Application_Process_Comment_Migration::migrate();
        \Axilweb\AiJobListing\Databases\Migrations\General_Settings_Migration::migrate(); 
        \Axilweb\AiJobListing\Databases\Migrations\Email_Types_Migration::migrate(); 
        \Axilweb\AiJobListing\Databases\Migrations\Email_Templates_Migration::migrate();
         
    }
}
