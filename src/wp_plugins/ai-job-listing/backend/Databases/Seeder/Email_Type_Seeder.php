<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Jobs Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class Email_Type_Seeder extends Db_Seeder {

    /**
     * Run Email Type seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::EMAIL_TYPE_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        } 

        // Generate Em,ail Type.
        $jobs = [
            [
               
                'name'           => 'Appointment',  
                'slug'           => 'appointment',  
                'is_active'      => 1, 
                'icon'           => 'tio-email_outlined', 
                  
            ],
            [
               
                'name'           => 'Status Update',  
                'slug'           => 'status-update',  
                'is_active'      => 1,  
                'icon'           => 'tio-update',  
                    
            ]
             
        ];

        // Create each of the Email_Type.
        $table_jobs = $wpdb->prefix .  'axil_job_listing_email_types';
        foreach ( $jobs as $job ) { 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_jobs,
                $job
            );
        }

        // Update that seeder already runs.
        update_option( Keys::EMAIL_TYPE_SEEDER_RAN, true );
    }
}
