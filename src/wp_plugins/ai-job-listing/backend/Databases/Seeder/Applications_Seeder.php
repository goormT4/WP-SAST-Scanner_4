<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Applications Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class Applications_Seeder extends Db_Seeder {

    /**
     * Run Applications seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::APPLICATIONS_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }
        $job_applications = [
            [
                'job_id'                    => 1,
                'process_id'                => 1,
                'app_mk'                    => 'application-key',
                'app_mv'                    => 'application-value',
                'is_read'                   => 0,  
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ] 
        ];
   
        // Create each of the job_applications.
        $table_applications = $wpdb->prefix .  'axilweb_ajl_applications';
        foreach ( $job_applications as $job_application ) { 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_applications,
                $job_application
            );
        }

        // Update that seeder already runs.
        update_option( Keys::APPLICATIONS_SEEDER_RAN, true );
    }
}
