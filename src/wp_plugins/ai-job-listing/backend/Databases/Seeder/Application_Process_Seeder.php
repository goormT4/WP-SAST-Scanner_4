<?php
namespace Axilweb\AiJobListing\Databases\Seeder;
use Axilweb\AiJobListing\Abstracts\Db_Seeder;
use Axilweb\AiJobListing\Common\Keys;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Application Process Seeder class.
 *
 * Seed some fresh emails for initial startup.
 */
class Application_Process_Seeder extends Db_Seeder
{

    /**
     * Run Jobs seeder.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function run()
    {
        global $wpdb;

        // Check if there is already a seeder runs for this plugin.
        $already_seeded = (bool) get_option(Keys::APPLICATION_PROCESS_SEEDER_RAN, false);
        if ($already_seeded) {
            return;
        }

        // Generate some app_process.
        $application_processes = [
            [
                'name'                      => 'Unlisted',
                'key'                       => 'unlisted',
                'icon'                      => 'tio-group_equal',
                'icon_color'                => '#323DA5',
                'order'                     => 1,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Shortlist',
                'key'                       => 'shortlist',
                'icon'                      => 'tio-format_bullets',
                'icon_color'                => '#FF7F5C',
                'order'                     => 2,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Phone',
                'key'                       => 'phone',
                'icon'                      => 'tio-call',
                'icon_color'                => '#3EB75E',
                'order'                     => 3,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Face',
                'key'                       => 'face_interview',
                'icon'                      => 'tio-user',
                'icon_color'                => '#8685EF',
                'order'                     => 4,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Test',
                'key'                       => 'test',
                'icon'                      => 'tio-book_outlined',
                'icon_color'                => '#1BA2DB',
                'order'                     => 5,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Final',
                'key'                       => 'final',
                'icon'                      => 'tio-group_equal',
                'icon_color'                => '#FF90AA',
                'order'                     => 6,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Hired',
                'key'                       => 'hired',
                'icon'                      => 'tio-done',
                'icon_color'                => '#FFC400',
                'order'                     => 7,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
        
            ],
            [
                'name'                      => 'Rejected',
                'key'                       => 'rejected',
                'icon'                      => 'tio-clear',
                'icon_color'                => '#FF585A',
                'order'                     => 8,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
            ],
            [
                'name'                      => 'Expired',
                'key'                       => 'expired',
                'icon'                      => 'tio-info_outined',
                'icon_color'                => '#FF585A',
                'order'                     => 8,
                'created_at'                => current_datetime()->format('Y-m-d H:i:s'),
                'created_by'                => get_current_user_id(),
                'updated_at'                => current_datetime()->format('Y-m-d H:i:s'),
            ]
             
        ];

        // Create each of the Application_Process.
        $table_app_process = $wpdb->prefix .  'axilweb_ajl_app_process';
        foreach ($application_processes as $application_process) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is acceptable in seeder files that only run during plugin setup/installation.
            $wpdb->insert(
                $table_app_process,
                $application_process
            );
        }

        // Update that seeder already runs.
        update_option(Keys::APPLICATION_PROCESS_SEEDER_RAN, true);
    }
}
