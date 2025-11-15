<?php
namespace Axilweb\AiJobListing\Databases\Seeder; 
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Email_Template Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class Email_Template_Seeder extends Db_Seeder {

    /**
     * Run Email Template seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::EMAIL_TEMPLATE_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }
  
        $jobs = [
            [
                'type_id'                      => 1,
                'receiver_type'                => 'applicant',  
                'has_notification_status'      => 1, 
                'should_email_sent_to_all'     => 1,   
                'message'                      => 'Dear [applicant_name], <br> You have successfully [job_title] scheduled appointment',  
                'subject'                      => 'Application Successfully Submit',   
                'updated_by'                   => get_current_user_id(),
                'updated_at'                   => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'type_id'                      => 1,
                'receiver_type'                => 'admin',  
                'has_notification_status'      => 1, 
                'should_email_sent_to_all'     => 1,   
                'message'                      => 'Dear [notification_name], <br> You have successfully scheduled appointment',  
                'subject'                      => 'Admin Approved',  
                'updated_by'                   => get_current_user_id(),
                'updated_at'                   => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'type_id'                      => 2,
                'receiver_type'                => 'applicant',  
                'has_notification_status'      => 1, 
                'should_email_sent_to_all'     => 1,   
                'message'                      => 'Dear [applicant_name] ,<br>You have successfully scheduled appointment.<br>…	',  
                'subject'                      => 'Appointment Approved',  
                'updated_by'                   => get_current_user_id(),
                'updated_at'                   => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'type_id'                      => 2,
                'receiver_type'                => 'admin',  
                'has_notification_status'      => 1, 
                'should_email_sent_to_all'     => 1,   
                'message'                      => 'Dear [applicant_name] <br>You have successfully scheduled appointment.<br>…	',  
                'subject'                      => 'Admin Approved',   
                'updated_by'                   => get_current_user_id(),
                'updated_at'                   => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ]
             
        ];

        // Create each of the jobs.
        $table_jobs = $wpdb->prefix .  'axil_job_listing_email_templates';
        foreach ( $jobs as $job ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_jobs,
                $job
            );
        } 
        // Update that seeder already runs.
        update_option( Keys::EMAIL_TEMPLATE_SEEDER_RAN, true );
    }
}
