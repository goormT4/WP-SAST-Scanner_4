<?php
namespace Axilweb\AiJobListing\Setup;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class Installer.
 *
 * Install necessary database tables and options for the plugin.
 */
class Cron_Job
{
    public function doingCronJob()
    {
        // cron job for wordpress
        add_filter('cron_schedules', [$this, 'add_every_minute_schedule']);
        add_action('wp_loaded', [$this, 'schedule_expired_jobs_cron']);
        add_action('axilweb_ajl_update_expired_jobs', [$this, 'update_expired_jobs']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate_expired_jobs_cron']);
    }

    /**
     * Add a custom schedule for running tasks every minute.
     *
     * @param array $schedules Existing cron schedules.
     * @return array Modified array of cron schedules with the 'every_minute' schedule added.
     */
    public function add_every_minute_schedule($schedules) {
        $schedules['every_minute'] = [
            'interval' => 60,
            'display' => __('Every Minute',  'ai-job-listing')
        ];
        return $schedules;
    }

    /**
     * Schedule the cron job to update expired jobs.
     *
     * This function sets up a custom cron event that runs at a specified interval
     * to handle expired job listings. If the event is already scheduled, it does nothing.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function schedule_expired_jobs_cron() {
        if (!wp_next_scheduled('axilweb_ajl_update_expired_jobs')) {
            //wp_schedule_event(time(), 'every_minute', 'axilweb_ajl_update_expired_jobs');
            wp_schedule_event(time(), 'daily', 'axilweb_ajl_update_expired_jobs');
        }
    }
 
    /**
     * Update expired jobs in the database.
     *
     * This function updates the status of jobs in the custom table to 'expired'
     * if the `application_deadline` has passed and the status is not already set to 'expired'.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update_expired_jobs() {
        $now = new \DateTime('now');
        $today = $now->format('Y-m-d');
        
        // Cache key for storing the last update timestamp
        $cache_key = 'axilweb_ajl_expired_jobs_last_updated';
        $cache_group = 'axilweb_ajl_cron';
        
        // Check if we've already run this update today
        $last_updated = wp_cache_get($cache_key, $cache_group);
        
        // Only run the update if we haven't run it today or if cache is empty
        if ($last_updated !== $today) {
            // Use our private method to handle the direct database query
            $affected_rows = $this->_query_update_expired_jobs($today);
            
            // If jobs were updated, invalidate any job-related caches
            if ($affected_rows > 0) {
                // Clear any job list caches that might be affected
                wp_cache_delete('axilweb_ajl_job_list', 'axilweb_ajl');
                
                // Store record of this update in cache
                wp_cache_set($cache_key, $today, $cache_group, DAY_IN_SECONDS);
            }
        }
    }  
    
    /**
     * Deactivate the cron job for expired jobs.
     *
     * This function removes all scheduled cron events associated with the
     * `update_expired_jobs` action. It is typically called during plugin deactivation.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function deactivate_expired_jobs_cron() {
        $timestamp = wp_next_scheduled('axilweb_ajl_update_expired_jobs');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'axilweb_ajl_update_expired_jobs');
        }
    }
    
    /**
     * Internal method to update expired jobs in the database.
     *
     * This private method handles the direct database query to update job statuses to 'expired'
     * when they've passed their application deadline.
     *
     * @since 1.0.0
     * @param string $date_threshold The date to check against in Y-m-d format
     * @return int|false Number of rows affected or false on error
     */
    private function _query_update_expired_jobs($date_threshold) {
        global $wpdb;
        
        // Direct database query is necessary for batch update operations
        // Caching is handled by the parent method update_expired_jobs()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        return $wpdb->query($wpdb->prepare( 
            "UPDATE {$wpdb->prefix}axilweb_ajl_jobs 
            SET status = 'expired' 
            WHERE application_deadline < %s 
            AND status != 'expired'",
            $date_threshold
        ));
    }
}
