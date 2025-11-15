<?php 
namespace Axilweb\AiJobListing\Helpers; 
use Axilweb\AiJobListing\Helpers\Helpers;
use Axilweb\AiJobListing\Models\Application;
use Axilweb\AiJobListing\Models\Email_Type;
use Axilweb\AiJobListing\Models\Job;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

trait Job_Application_Helpers_Trait {

    /**
     * Retrieves the job notification emails from a given job object.
     *
     * This function checks if the job object and its `job_notification_emails` property exist.
     * If the property is present, it decodes the JSON data into an array and returns it.
     * If any of the conditions fail, it returns `null`.
     *
     * @param object $job The job object containing the `job_notification_emails` property.
     * @return array|null An array of job notification emails if available, or `null` if not.
     */
	public static function getEmailsFromJob($job)
	{
		 if (!$job) {
            return null;
        }

        if (! $job->job_notification_emails) {
            return null;
        }
        $job_notification_emails = json_decode( $job->job_notification_emails, true );
        if (!$job_notification_emails) {
            return null;
        }
        return $job_notification_emails;
	}

    /**
     * Retrieves the email addresses along with their corresponding display names from a job object.
     *
     * This function checks if the job object and its `job_notification_emails` property exist.
     * It then decodes the JSON data into an array of email addresses and looks up each email
     * in the WordPress users table to get the associated display name. If no user is found for an email,
     * a default message 'No matching user' is added.
     *
     * @param object $job The job object containing the `job_notification_emails` property.
     * @return array An associative array with email addresses as keys and their corresponding display names as values.
     *               If no user is found for an email, the value will be 'No matching user'.
     */
    public static function getEmailsWithNames($job)
    {
        if (!$job || empty($job->job_notification_emails)) {
            return [];
        }
    
        // Decode the JSON email data from the job object
        $emailAddresses = json_decode($job->job_notification_emails, true);
        if (empty($emailAddresses)) {
            return [];
        }
    
        // Prepare an array to hold emails and corresponding display names
        $emailsAndNames = [];
    
        // Iterate through each email and retrieve the corresponding display name
        foreach ($emailAddresses as $email) {
            // Sanitize the email address before using it
            $safeEmail = sanitize_email($email);
            
            // Create a cache key for this email
            $cache_key = 'axilweb_ajl_user_display_name_' . md5($safeEmail);
            $cache_group = 'axilweb_ajl_user_display_names';
            
            // Try to get the display name from cache
            $displayName = wp_cache_get($cache_key, $cache_group);
            
            // If not in cache, fetch using WordPress core function
            if (false === $displayName) {
                // Instead of a direct query, use get_user_by()
                $user = get_user_by('email', $safeEmail);
                $displayName = $user ? $user->display_name : null;
                
                // Cache the result (even if null)
                wp_cache_set($cache_key, $displayName, $cache_group, HOUR_IN_SECONDS);
            }
    
            // If a display name exists, add it to the result array
            if ($displayName) {
                $emailsAndNames[$email] = $displayName;
            } else {
                // Optionally handle cases where no corresponding user is found
                $emailsAndNames[$email] = 'No matching user';
            }
        }
    
        return $emailsAndNames;
    }
     
    /**
     * Retrieves an email template by its type and receiver type.
     *
     * This function queries the database to fetch a specific email template based on the `type_id` 
     * and the `receiver_type` (either 'applicant' or other types). It performs the query using 
     * WordPress's `$wpdb` class to safely retrieve the email template.
     *
     * @param int    $type_id      The ID of the email template type.
     * @param string $receiver_type The type of receiver (default is 'applicant').
     * 
     * @return object|null Returns the email template object if found, or null if not found.
     */
	public static function getEmail_TemplatesByTypeReceiver($type_id, $receiver_type = 'applicant')
	{
		// Validate inputs
		$type_id = absint($type_id);
		$receiver_type = sanitize_text_field($receiver_type);
		
		// Create cache key
		$cache_key = 'axilweb_ajl_email_template_' . $type_id . '_' . $receiver_type;
		$cache_group = 'axilweb_ajl_email_templates';
		
		// Try to get from cache first
		$result = wp_cache_get($cache_key, $cache_group);
		
		// If not in cache, fetch from database
		if (false === $result) {
			// Query the database
			$result = self::_query_get_email_template($type_id, $receiver_type);
			
			// Cache the result (even if null)
			wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
		}
		
		return $result;
	}

    /**
     * Retrieves SMTP settings from the database based on the specified type.
     *
     * This function queries the database to fetch SMTP settings (or other settings) from the `axilweb_ajl_general_settings` table,
     * where the settings are categorized by their `type`. The function returns the settings as an associative array.
     *
     * @param string $type The type of settings to retrieve, default is 'smtp'.
     * 
     * @return array An associative array containing the name-value pairs of the SMTP settings. If no settings are found, an empty array is returned.
     */
    public static function getSmtpSettings($type = 'smtp') {
        // Sanitize input
        $type = sanitize_text_field($type);
        
        // Create cache key
        $cache_key = 'axilweb_ajl_settings_' . $type;
        $cache_group = 'axilweb_ajl_general_settings';
        
        // Try to get from cache first
        $smtp_settings = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $smtp_settings) {
            // Query the database
            $results = self::_query_get_smtp_settings($type);
            
            // Initialize an associative array to hold the name-value pairs
            $smtp_settings = [];
        
            // Populate the array with name as key and value as value
            if (!empty($results)) {
                foreach ($results as $row) {
                    $smtp_settings[$row['name']] = $row['value'];
                }
            }
            
            // Cache the results
            wp_cache_set($cache_key, $smtp_settings, $cache_group, HOUR_IN_SECONDS);
        }
    
        return $smtp_settings;
    }
    
    /**
     * Internal method to get an email template by type and receiver.
     *
     * @since 1.0.0
     * @param int $type_id The type ID for the email template
     * @param string $receiver_type The receiver type for the email template
     * @return object|null The email template object or null if not found
     */
    private static function _query_get_email_template($type_id, $receiver_type) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'axil_job_listing_email_templates';
        
        // Create a cache key based on the query parameters
        $cache_key = 'axilweb_ajl_email_template_' . absint($type_id) . '_' . sanitize_key($receiver_type);
        $cache_group = 'axilweb_ajl_email_templates';
        
        // Try to get from cache first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $result) {
            // Use %i placeholder for proper table name escaping
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is necessary for custom email template table. Proper caching is implemented with wp_cache_get/set above.
            $result = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM %i WHERE type_id = %d AND receiver_type = %s LIMIT 1",
                    $table_name,
                    $type_id,
                    $receiver_type
                )
            );
            
            // Cache the result for future requests
            if ($result) {
                wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $result;
    }
    
    /**
     * Internal method to get SMTP settings by type.
     *
     * @since 1.0.0
     * @param string $type The settings type (e.g., 'smtp')
     * @return array The settings from the database
     */
    private static function _query_get_smtp_settings($type) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'axilweb_ajl_general_settings';
        
        // Create a cache key based on the type parameter
        $cache_key = 'axilweb_ajl_smtp_settings_' . sanitize_key($type);
        $cache_group = 'axilweb_ajl_smtp_settings';
        
        // Try to get from cache first
        $result = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $result) {
            // Use %i placeholder for proper table name escaping
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Direct query is necessary for custom settings table. Proper caching is implemented with wp_cache_get/set above.
            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT name, value FROM %i WHERE type = %s",
                    $table_name,
                    $type
                ),
                ARRAY_A
            );
            
            // Cache the result for future requests
            if ($result) {
                wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
            }
        }
        
        return $result;
    }
    
    
    /**
     * Sends email notifications for a job application to both the applicant and the admin.
     *
     * This function handles the process of sending email notifications upon a job application. It fetches relevant data for the 
     * applicant and job, retrieves the appropriate email templates for both the applicant and the admin, and populates the templates 
     * with dynamic values (like applicant name). The emails are then sent using the defined helper function.
     *
     * @param int $job_id The ID of the job to which the application was submitted.
     * @param int $application_id The ID of the job application.
     * @param string $email_type_slug The email type slug to determine the type of email template to use (default is 'appointment').
     *
     * @return bool Returns `true` if the emails were successfully sent, otherwise `null`.
     */
	public static function sendEmailForApplication($job_id, $application_id, $email_type_slug='appointment')
	{
         

        $args = ['id' => $application_id];
        $application = axilweb_ajl_jobs()->job_applications_manager->all($args);
        $application_data = null;
        $application_meta_attributes = null;
        $application_meta_email = null;
        $applicant_meta_name = null;
        $applicant_email_template = null;
        $admin_email_template = null;
        $admin_email_smtp = null;
        $template  = null;
        $applicant_populated_template  = null;

   
        if ($application) {
            $application_data = Application::to_array($application[0]);
        }
        if ($application_data) {
            $application_meta_attributes = json_decode($application_data['meta_attributes'], true);
            foreach ($application_meta_attributes as $attribute_item) {
                 if ($attribute_item['app_mk'] == 'email') {
                    $application_meta_email = $attribute_item['app_mv'];
                }
                if ($attribute_item['app_mk'] == 'full_name') {
                    $applicant_meta_name = $attribute_item['app_mv'];
                }
            }
        }
  
        $job = (new Job)->get_by('id', $job_id);
        // Extract job details for template placeholders
        $job_title = $job ? ($job->title ?? '') : '';
        // Retrieve admin notification emails
        $job_notification_emails = Helpers::getEmailsFromJob($job);
        // Convert emails array to comma-separated string for placeholder replacement
        $job_notification_emails_string = is_array($job_notification_emails) ? implode(', ', $job_notification_emails) : '';
        $email_type = (new Email_Type)->get_by('slug', $email_type_slug);
        if (! $email_type) {
            return null;
        }
        $admin_email_template = Helpers::getEmail_TemplatesByTypeReceiver($email_type->id, 'admin'); 
        $applicant_email_template = Helpers::getEmail_TemplatesByTypeReceiver($email_type->id, 'applicant');
        $job_notification_names_with_emails = Helpers::getEmailsWithNames($job);
 
        // Email template
        if ($application_meta_email && $applicant_email_template) {
            $template = $applicant_email_template->message;
        }
        // Replace the placeholders with customer data
        $applicant_populated_template = str_replace(
            ['[applicant_name]', '[applicant_mail]', '[job_title]'],
            [$applicant_meta_name, $application_meta_email, $job_title],
            $template
        );
  
        if ($application_meta_email && $applicant_email_template) {
             
            Helpers::emailSend($application_meta_email, $applicant_email_template->subject, $applicant_populated_template);   
             
        }

        $admin_populated_templates = [];
        $admin_populated_template = null;
        if ($admin_email_template && is_array($job_notification_names_with_emails)) {
            foreach ($job_notification_names_with_emails as $email => $name) {
                $admin_populated_templates[] = $admin_populated_template = str_replace(
                    ['[admin_mail_notification]', '[job_title]'],
                    [$job_notification_emails_string, $job_title],
                    $admin_email_template->message
                );
               Helpers::emailSend($email, $admin_email_template->subject, $admin_populated_template);   
            }
        }
 

        return false;
	}

} 
