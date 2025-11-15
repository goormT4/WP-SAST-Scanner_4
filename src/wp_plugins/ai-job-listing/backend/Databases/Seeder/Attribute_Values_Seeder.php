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
class Attribute_Values_Seeder extends Db_Seeder {

    /**
     * Run Attribute Values seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::JOB_ATTIBUTE_VALUE_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }

        // Generate some job_attributes_Values.
        $attribute_metas = [
            [
                'value'                     => 'Full Time',
                'slug'                      => 'full-time',   
                'attribute_id'              => 1,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'value'                     => 'Part Time',
                'slug'                      => 'part-time',   
                'attribute_id'              => 1,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],  
            [
                'value'                     => 'Internship',
                'slug'                      => 'internship',   
                'attribute_id'              => 1,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'value'                     => 'Contractual',
                'slug'                      => 'contractual',   
                'attribute_id'              => 1,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],  
              

            [
                'value'                     => 'Accounting',
                'slug'                      => 'accounting',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Business Development',
                'slug'                      => 'business-development',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Customer Service',
                'slug'                      => 'customer-service',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Finance',
                'slug'                      => 'finance',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Human Resources',
                'slug'                      => 'human-resources',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ), 
            ],
            [
                'value'                     => 'IT',
                'slug'                      => 'it',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ), 
            ],
            [
                'value'                     => 'Marketing',
                'slug'                      => 'marketing',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ), 
            ], 
            [
                'value'                     => 'Operations Management',
                'slug'                      => 'operations-management',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ), 
            ],
            [
                'value'                     => 'Production',
                'slug'                      => 'production',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ), 
            ],
            [
                'value'                     => 'Sales',
                'slug'                      => 'sales',   
                'attribute_id'              => 2,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ), 
            ],  
            [
                'value'                     => 'Day Shift',
                'slug'                      => 'day-shift',   
                'attribute_id'              => 3,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Evening Shift',
                'slug'                      => 'evening-shift',   
                'attribute_id'              => 3,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Night Shift',
                'slug'                      => 'night-shift',   
                'attribute_id'              => 3,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'value'                     => 'On-site',
                'slug'                      => 'on-site',   
                'attribute_id'              => 4,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Remote',
                'slug'                      => 'remote',   
                'attribute_id'              => 4,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],  
            [
                'value'                     => "Bachelor's",
                'slug'                      => "Bachelor's",   
                'attribute_id'              => 5,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'value'                     => "Master's",
                'slug'                      => "master's",   
                'attribute_id'              => 5,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],   
            [
                'value'                     => 'Weekly',
                'slug'                      => 'weekly',   
                'attribute_id'              => 6,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Monthly',
                'slug'                      => 'monthly',   
                'attribute_id'              => 6,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Annually',
                'slug'                      => 'annually',   
                'attribute_id'              => 6,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
 
            [
                'value'                     => 'Website',
                'slug'                      => 'website',   
                'attribute_id'              => 7,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Linkedin',
                'slug'                      => 'linkedin',   
                'attribute_id'              => 7,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => 'Google Search',
                'slug'                      => 'google-search',   
                'attribute_id'              => 7,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 

            [
                'value'                     => '0 year',
                'slug'                      => '0-year',   
                'attribute_id'              => 8,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'value'                     => '1 Year',
                'slug'                      => '1-year',   
                'attribute_id'              => 8,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => '2 Years',
                'slug'                      => '2-years',   
                'attribute_id'              => 8,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ], 
            [
                'value'                     => '3 Years',
                'slug'                      => '3-years',   
                'attribute_id'              => 8,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => '4 Years',
                'slug'                      => '4-years',   
                'attribute_id'              => 8,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => '5 Years',
                'slug'                      => '5-years',   
                'attribute_id'              => 8,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            
            
            [
                'value'                     => "In A Week's Notice",
                'slug'                      => 'in-a-week"s-notice',   
                'attribute_id'              => 9,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => "In 2 Week's Notice",
                'slug'                      => 'in-2-week"s-notice',   
                'attribute_id'              => 9,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => "In 3 Week's Notice",
                'slug'                      => 'in-3-week"s-notice',   
                'attribute_id'              => 9,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => "In 4 Week's Notice",
                'slug'                      => 'in-4-week"s-notice',   
                'attribute_id'              => 9,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'value'                     => "In 5 Week's Notice",
                'slug'                      => 'in-5-week"s-notice',   
                'attribute_id'              => 9,  
                'menu_orderby'              => 1, 
                'is_active'                 => 1,   
                'created_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                'created_by'                => get_current_user_id(), 
                'updated_at'                => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],



        ];
   
        // Create each of the job_attributes.
        $table_attribute_values = $wpdb->prefix .  'axilweb_ajl_attribute_values';
        foreach ( $attribute_metas as $attribute_meta ) { 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_attribute_values,
                $attribute_meta
            );
        }

        // Update that seeder already runs.
        update_option( Keys::JOB_ATTIBUTE_VALUE_SEEDER_RAN, true );
    }
}
