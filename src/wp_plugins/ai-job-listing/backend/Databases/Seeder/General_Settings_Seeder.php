<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * General Settings Seeder class.
 *
 * Seed some fresh General Settings for initial startup.
 */
class General_Settings_Seeder extends Db_Seeder {

    /**
     * Run General Settings seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run() {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option( Keys::GENERAL_SETTINGS_SEEDER_RAN, false );
        if ( $already_seeded ) {
            return;
        }
 
        $settings = [
            [
                'name'              => 'career_page_title',
                'label'             => 'Career Page Title',
                'value'             => 'Be a Part of our Growing Team.',   
                'type'              => 'general_setting',    
                'form_type'         => 'text',
                'options'           => null,
                'placeholder'       => 'Enter Page Title',
                'column_width'       => 'full',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'              => 'career_page_tagline',
                'label'             => 'Career Page Tagline',
                'value'             => 'Make an impact doing what you love.',   
                'type'              => 'general_setting',    
                'form_type'         => 'text',
                'options'           => null,
                'placeholder'       => 'Enter Page Tagline',
                'column_width'      => 'full',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'              => 'thank_you_title',
                'label'             => 'Thank You Page Title',
                'value'             => 'Thank you for applying!',   
                'type'              => 'general_setting',    
                'form_type'         => 'text',
                'options'           => null,
                'placeholder'       => 'Enter Thank you Title',
                'column_width'      => 'full',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'              => 'thank_you_tagline',
                'label'             => 'Thank You Page Tagline',
                'value'             => 'We received your application. we will review your application and notify you in your registered email address.',   
                'type'              => 'general_setting',    
                'form_type'         => 'text',
                'options'           => null,
                'placeholder'       => 'Enter Thank you Tagline',
                'column_width'      => 'full',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            
            [
                'name'              => 'primary_color',
                'label'             => 'Primary Color',
                'value'             => '#ff7f5c',   
                'type'              => 'general_setting',    
                'form_type'         => 'color',
                'options'           => null,
                'placeholder'       => 'Enter Primary Color', 
                'column_width'       => 'half',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'              => 'secondary_color',
                'label'             => 'Secondary Color',
                'value'             => '#323da5',   
                'type'              => 'general_setting',    
                'form_type'         => 'color',
                'options'           => null,
                'placeholder'       => 'Enter Secondary Color', 
                'column_width'       => 'half',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],
            [
                'name'              => 'currency_symbol',
                'label'             => 'Currency Code',
                'value'             => '$',   
                'type'              => 'general_setting',    
                'form_type'         => 'select',
                'options'           => null,
                'placeholder'       => 'Enter Currency Code',
                'column_width'       => 'half',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],  
            [
                'name'              => 'career_page',
                'label'             => 'Career Page Select',
                'value'             => '',   
                'type'              => 'general_setting',    
                'form_type'         => 'select',
                'options'           => null,
                'placeholder'       => 'Select Career Page',
                'column_width'       => 'half',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],   
            [
                'name'              => 'open_ai_api_key',
                'label'             => 'API Key',
                'value'             => '',   
                'type'              => 'ai_setting',    
                'form_type'         => 'text',
                'options'           => null,
                'placeholder'       => 'Enter API Key',
                'column_width'       => 'full',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],  
            [
                'name'              => 'sendgrid_api_key',
                'label'             => 'SendGrid API Key',
                'value'             => '',   
                'type'              => 'sendgrid_setting',    
                'form_type'         => 'text',
                'options'           => null,
                'placeholder'       => 'Enter SendGrid API Key',
                'column_width'       => 'full',
                'updated_at'        => current_datetime()->format( 'Y-m-d H:i:s' ),
                 
            ],    
  
        ];
   
        // Create each of the job_attributes.
        $table_general_settings = $wpdb->prefix .  'axilweb_ajl_general_settings';
        foreach ( $settings as $setting ) { 
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_general_settings,
                $setting
            );
        }

        // Update that seeder already runs.
        update_option( Keys::GENERAL_SETTINGS_SEEDER_RAN, true );
    }
}
