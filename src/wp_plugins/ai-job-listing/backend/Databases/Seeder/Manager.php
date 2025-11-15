<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Database Seeder class.
 *
 * It'll seed all of the seeders.
 */
class Manager {

    /**
     * Run the database seeders.
     *
     * @since 1.0.0
     *
     * @return void
     * @throws Exception
     */ 
    public function run() {
        // Array of database seeder class names.
        $seeder_classes = [
      
            \Axilweb\AiJobListing\Databases\Seeder\Attributes_Seeder::class,
            \Axilweb\AiJobListing\Databases\Seeder\Attribute_Values_Seeder::class,
            \Axilweb\AiJobListing\Databases\Seeder\Application_Process_Seeder::class,
            \Axilweb\AiJobListing\Databases\Seeder\General_Settings_Seeder::class,
            \Axilweb\AiJobListing\Databases\Seeder\Email_Type_Seeder::class,
            \Axilweb\AiJobListing\Databases\Seeder\Email_Template_Seeder::class,
            
        ];

        // Iterate over each seeder class.
        foreach ( $seeder_classes as $seeder_class ) {
            // Instantiate the seeder class.
            $seeder = new $seeder_class();
            // Run the seeder.
            $seeder->run();
        }
    }
}

