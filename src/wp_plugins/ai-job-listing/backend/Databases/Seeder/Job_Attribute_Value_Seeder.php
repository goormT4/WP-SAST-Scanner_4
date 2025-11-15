<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;

/**
 * Job Attribute Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class Job_Attribute_Value_Seeder extends Db_Seeder {

    /**
     * Run Job Attribute seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::JOB_META_VALUE_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        } 
        // Generate some Job Attribute.
        $jobs_meta_values = [
            [
                 
                'job_id'                        => 1,
                'attribute_value_id'            => 1,  
            ],
        ];

        // Create each of the jobs.
        $table_job_attribute_value = $wpdb->prefix .  'axilweb_ajl_job_attribute_value';
        foreach ( $jobs_meta_values as $jobs_meta_value ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_job_attribute_value,
                $jobs_meta_value
            );
        }

        // Update that seeder already runs.
        update_option( Keys::JOB_META_VALUE_SEEDER_RAN, true );
    }
}
