<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Jobattribute Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class Attributes_Seeder extends Db_Seeder {

    /**
     * Run Jobattribute seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::JOB_ATTIBUTE_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }

        // Generate some job_attributes.
        $job_attributes = [
            [
                'name'                      => 'Job Types',
                'slug'                      => 'job-types',
                'icon'                      => 'tio-briefcase_outlined',
                'form_key'                  => 'job_types', 
                'is_required'               => false,
                'label'                     => 'Job Types List',
                'type_id'                   => 1,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Departments',
                'slug'                      => 'job-departments',
                'icon'                      => 'tio-category_outlined',
                'form_key'                  => 'job_departments', 
                'is_required'               => false,
                'label'                     => 'Department List',
                'type_id'                   => 1,  
                'menu_orderby'              => 2, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Job Shifts',
                'slug'                      => 'job-shifts',
                'icon'                      => 'tio-timer_30_s',
                'form_key'                  => 'job_shifts', 
                'is_required'               => false,
                'label'                     => 'Job Shift List',
                'type_id'                   => 1,  
                'menu_orderby'              => 3, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Locations',
                'slug'                      => 'job-locations',
                'icon'                      => 'tio-my_location',
                'form_key'                  => 'job_locations', 
                'is_required'               => false,
                'label'                     => 'Locations List',
                'type_id'                   => 1,  
                'menu_orderby'              => 4, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Education Qualifications',
                'slug'                      => 'job-education-qualifications',
                'icon'                      => 'tio-education_outlined',
                'form_key'                  => 'job_education_qualifications', 
                'is_required'               => false,
                'label'                     => 'Education Qualification List',
                'type_id'                   => 1,  
                'menu_orderby'              => 5, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'name'                      => 'Salary Types',
                'slug'                      => 'job-salary-type',
                'icon'                      => 'tio-money',
                'form_key'                  => 'job_salary_type', 
                'is_required'               => false,
                'label'                     => 'Application Method List',
                'type_id'                   => 1,  
                'menu_orderby'              => 7, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Lead Source',
                'slug'                      => 'lead-source',
                'icon'                      => 'tio-company',
                'form_key'                  => 'lead_source', 
                'is_required'               => false,
                'label'                     => 'How Did You Hear About Us?',
                'type_id'                   => 1,  
                'menu_orderby'              => 8, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Work Experience',
                'slug'                      => 'work-experience',
                'icon'                      => 'tio-briefcase_outlined',
                'form_key'                  => 'work_experience', 
                'is_required'               => false,
                'label'                     => 'Work Experience (In Years)',
                'type_id'                   => 1,  
                'menu_orderby'              => 9, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'                      => 'Notice Period',
                'slug'                      => 'notice-period',
                'icon'                      => 'tio-group_add',
                'form_key'                  => 'notice_period', 
                'is_required'               => false,
                'label'                     => 'Notice Period',
                'type_id'                   => 1,  
                'menu_orderby'              => 10, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
        ];
   
        // Create each of the job_attributes.
        $table_attributes = $wpdb->prefix .  'axilweb_ajl_attributes';
        foreach ( $job_attributes as $job_attribute ) { 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_attributes,
                $job_attribute
            );
        }

        // Update that seeder already runs.
        update_option( Keys::JOB_ATTIBUTE_SEEDER_RAN, true );
    }
}
