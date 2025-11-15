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
class Jobs_Seeder extends Db_Seeder {

    /**
     * Run Jobs seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::JOB_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }
        // Generate some jobs.
        $jobs = [
            [
                'title'                            => 'First Job Post',
                'slug'                             => 'first-job-post',  
                'no_of_vacancies'                   => 1,
                'is_required_cv_photo'              => 1,  
                'description'                       => 'This is a simple job description.', 
                'application_deadline'              => current_datetime()->format( 'Y-m-d' ),  
                'responsibilities'                  => 'This is a simple job Responsibilities.',  
                'min_salary'                        => 100.1,
                'max_salary'                        => 101.1,   
                'benefits'                          => 'This is a simple job benefits.',  
                'requirements'                      => 'This is a simple job Requirements.',  
                'experienced_year'                  => 3,
                'additional_requirements'           => 'This is a simple job Additional Requirements.', 
                'additional_notes'                  => 'This is a simple job additional notes.',   
                'status'                            => 'active',
                'total_views'                       => '',  
                'seo_title'                         => 'This is a simple job seo title.',  
                'seo_description'                   => 'This is a simple job seo description.',  
                'feature_image'                     => '',  
                'job_notification_emails'            => wp_json_encode(['support@aijob.com']),  
                'deleted_by'                        => get_current_user_id(),
                'created_at'                        => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                        => get_current_user_id(),
                'updated_by'                        => get_current_user_id(),
                'updated_at'                        => null,
                'deleted_at'                        => null,
                'form_step_complete'                => 1,
                'total_view_count'                  => null,
                 
            ],
        ];

        // Create each of the jobs.
        $table_jobs = $wpdb->prefix .  'axilweb_ajl_jobs';
        foreach ( $jobs as $job ) { 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_jobs,
                $job
            );
        }

        // Update that seeder already runs.
        update_option( Keys::JOB_SEEDER_RAN, true );
    }
}
