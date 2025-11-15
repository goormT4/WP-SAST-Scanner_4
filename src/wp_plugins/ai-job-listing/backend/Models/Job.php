<?php

namespace Axilweb\AiJobListing\Models;
use Axilweb\AiJobListing\Abstracts\Base_Model;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Job class.
 *
 * @since 0.1.0
 */
class Job extends Base_Model
{

    /**
     * Table Name.
     *
     * @var string
     */
    protected $table = 'axilweb_ajl_jobs';

    /**
     * Prepare datasets for database operation.
     *
     * @since 1.0.0
     *
     * @param array $request
     * @return array
     */
    public function prepare_for_database(array $data): array
    {
        $defaults = [

            'title'                             => '',
            'slug'                              => '',
            'job_types_meta_id'                 => 1,
            'departments_meta_id'               => 1,
            'job_shifts_meta_id'                => 1,
            'locations_meta_id'                 => 1,
            'education_qualifications_meta_id'  => 1,
            'application_method_meta_id'        => 1,
            'salary_type_meta_id'               => 1,
            'no_of_vacancies'                   => 1,
            'is_required_cv_photo'              => 1,
            'description'                       => '',
            'responsibilities'                  => '',
            'min_salary'                        => '',
            'max_salary'                        => '',
            'benefits'                          => '',
            'requirements'                      => '',
            'experienced_year'                  => 3,
            'additional_requirements'           => '',
            'additional_notes'                  => '',
            'show_additional_note_field'        => 1,
            'status'                            => '',
            'total_views'                       => '',
            'seo_title'                         => '',
            'seo_description'                   => '',
            'feature_image'                     => '',
            'created_at'                        => current_datetime()->format('Y-m-d'),
            'created_by'                        => get_current_user_id(),
            'form_step_complete'                => 1,
            
 
        ];

        $data = wp_parse_args($data, $defaults);

        // Sanitize template data
        return [
            'title'                               => $this->sanitize($data['title'],                         'text'),
            'slug'                                => $this->sanitize($data['slug'],                          'text'),
            'no_of_vacancies'                     => $this->sanitize($data['no_of_vacancies'],               'text'),
            'is_required_cv_photo'                => $this->sanitize($data['is_required_cv_photo'],          'switch'),
            'description'                         => $this->sanitize($data['description'],                   'text'),
            'responsibilities'                    => $this->sanitize($data['responsibilities'],              'text'),
            'min_salary'                          => $this->sanitize($data['min_salary'],                    'text'),
            'max_salary'                          => $this->sanitize($data['max_salary'],                    'text'),
            'benefits'                            => $this->sanitize($data['benefits'],                      'text'),
            'requirements'                        => $this->sanitize($data['requirements'],                  'text'),
            'experienced_year'                    => $this->sanitize($data['experienced_year'],              'text'),
            'additional_requirements'             => $this->sanitize($data['additional_requirements'],       'text'),
            'additional_notes'                    => $this->sanitize($data['additional_notes'],              'text'),
            'status'                              => $this->sanitize($data['status'],                        'text'),
            'total_views'                         => $this->sanitize($data['total_views'],                   'text'),
            'seo_title'                           => $this->sanitize($data['seo_title'],                     'text'),
            'seo_description'                     => $this->sanitize($data['seo_description'],               'text'),
            'feature_image'                       => $this->sanitize($data['feature_image'],                 'text'),
            'created_at'                          => $this->sanitize($data['created_at'],                    'text'),
            'created_by'                          => $this->sanitize($data['created_by'],                    'number'),
            'form_step_complete'                  => $this->sanitize($data['form_step_complete'],            'number'),
           


        ];
    }


    /**
     * Retrieves the total number of applicants for a specific job.
     *
     * This function calculates the total number of applicants for a given job by
     * querying the database. It ensures that only non-deleted applicants are counted.
     *
     * @since 1.0.0
     *
     * @param int $job_id The ID of the job to retrieve applicants for.
     * @return int The total number of applicants.
     */
    public static function get_total_applicants($job_id)
    {

        global $wpdb;
        $table_applications = $wpdb->prefix .  'axilweb_ajl_applications';
        $table_jobs = $wpdb->prefix .  'axilweb_ajl_jobs';
        $query = $wpdb->prepare(
            "
            SELECT count(app.id) as total_applicants FROM %i as app
            INNER JOIN %i job ON job.id = app.job_id WHERE job_id = %d AND app.deleted_at IS NULL
             ",
            $table_applications,
            $table_jobs,
            $job_id
        );


        // Set up cache key and group
        $cache_key = 'axilweb_ajl_job_total_applicants_' . absint($job_id);
        $cache_group = 'axilweb_ajl_job_statistics';
        
        // Try to get from cache first
        $total_applicants = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $total_applicants) {
            // Query the database using a separate method
            $data = self::_query_get_total_applicants($job_id, $table_applications, $table_jobs);
            
            // Process the data
            $total_applicants = (isset($data[0]['total_applicants']) && !empty($data[0]['total_applicants'])) 
                ? $data[0]['total_applicants'] 
                : 0;
            
            // Cache the results (10 minutes is a reasonable time for job statistics)
            wp_cache_set($cache_key, $total_applicants, $cache_group, 10 * MINUTE_IN_SECONDS);
        }
        
        return $total_applicants;
    }
     
    /**
     * Retrieves the total number of in-progress applications for a specific job.
     *
     * @since 1.0.0
     *
     * @param int $job_id The ID of the job to retrieve in-progress applications for.
     * @return int The total number of in-progress applications.
     */
    public static function get_total_in_progress($job_id)
    {
        global $wpdb;

        // Early return if job_id is empty
        if (empty($job_id)) {
            return 0;
        }

        // Ensure AXILWEB_AJL_IN_PROGRESS_IDS is an array or properly formatted string
        $in_progress_ids = AXILWEB_AJL_IN_PROGRESS_IDS;
        
        if (!is_array($in_progress_ids)) {
            $in_progress_ids = explode(',', $in_progress_ids);
        }

        // Validate numeric IDs
        $in_progress_ids = array_map('intval', $in_progress_ids);

        if (empty($in_progress_ids)) {
            return 0; // No valid in-progress IDs
        }

        // The simple approach - using individual count queries and summing them
        // Set up cache key and group
        $cache_key = 'axilweb_ajl_job_in_progress_' . absint($job_id) . '_' . md5(serialize($in_progress_ids));
        $cache_group = 'axilweb_ajl_job_statistics';
        
        // Try to get from cache first
        $count = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $count) {
            $count = 0;
            
            // Query the database for each process ID
            foreach ($in_progress_ids as $process_id) {
                // Execute the query through a separate method
                $count += (int) self::_query_get_applications_count_by_process($job_id, $process_id);
            }
            
            // Cache the results (10 minutes is reasonable for job statistics)
            wp_cache_set($cache_key, $count, $cache_group, 10 * MINUTE_IN_SECONDS);
        }

        // Return result (ensure it's always an integer)
        return (int) $count;
    }


    /**
     * Retrieves the total number of rejected applicants for a specific job.
     *
     * This function calculates the total number of applications for a given job
     * that have a process ID indicating the rejected state.
     *
     * @since 1.0.0
     *
     * @param int $job_id The ID of the job to retrieve rejected applicants for.
     * @return int The total number of rejected applicants.
     */
    public static function get_total_rejected($job_id)
    {
        global $wpdb;
        $table_applications = $wpdb->prefix . "axilweb_ajl_applications"; 
        // Set up cache key and group
        $cache_key = 'axilweb_ajl_job_total_rejected_' . absint($job_id);
        $cache_group = 'axilweb_ajl_job_statistics';
        
        // Try to get from cache first
        $total_rejected = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $total_rejected) {
            // Query the database using a separate method
            $data = self::_query_get_total_rejected($job_id, $table_applications);
            
            // Process the data
            $total_rejected = (isset($data[0]['total_rejected']) && !empty($data[0]['total_rejected'])) 
                ? $data[0]['total_rejected'] 
                : 0;
            
            // Cache the results (10 minutes is reasonable for job statistics)
            wp_cache_set($cache_key, $total_rejected, $cache_group, 10 * MINUTE_IN_SECONDS);
        }
        
        return $total_rejected;
    }

    /**
     * Retrieves the total number of hired applicants for a specific job.
     *
     * This function calculates the total number of applications for a given job
     * that have a process ID indicating the hired state.
     *
     * @since 1.0.0
     *
     * @param int $job_id The ID of the job to retrieve hired applicants for.
     * @return int The total number of hired applicants.
     */
    public static function get_total_hired($job_id)
    {
        global $wpdb;
        $table_applications = $wpdb->prefix . "axilweb_ajl_applications";
     
        // Set up cache key and group
        $cache_key = 'axilweb_ajl_job_total_hired_' . absint($job_id);
        $cache_group = 'axilweb_ajl_job_statistics';
        
        // Try to get from cache first
        $total_hired = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $total_hired) {
            // Query the database using a separate method
            $data = self::_query_get_total_hired($job_id, $table_applications);
            
            // Process the data
            $total_hired = (isset($data[0]['total_hired']) && !empty($data[0]['total_hired'])) 
                ? $data[0]['total_hired'] 
                : 0;
            
            // Cache the results (10 minutes is reasonable for job statistics)
            wp_cache_set($cache_key, $total_hired, $cache_group, 10 * MINUTE_IN_SECONDS);
        }
        
        return $total_hired;
    }

    /**
     * Retrieves the total number of unread applications for a specific job.
     *
     * This function calculates the total number of applications for a given job
     * where the `is_read` column indicates they are unread.
     *
     * @since 1.0.0
     *
     * @param int $job_id The ID of the job to retrieve unread applications for.
     * @return int The total number of unread applications.
     */
    public static function get_total_unread($job_id)
	{
		global $wpdb;
		
		// Set up cache key and group
        $cache_key = 'axilweb_ajl_job_total_unread_' . absint($job_id);
        $cache_group = 'axilweb_ajl_job_statistics';
        
        // Try to get from cache first
        $total_unread = wp_cache_get($cache_key, $cache_group);
        
        // If not in cache, fetch from database
        if (false === $total_unread) {
            // Query the database using a separate method
            $data = self::_query_get_total_unread($job_id);
			
			// Process the data
            $total_unread = (isset($data[0]['total_unread']) && !empty($data[0]['total_unread'])) 
                ? $data[0]['total_unread'] 
                : 0;
            
            // Cache the results (10 minutes is reasonable for job statistics)
            wp_cache_set($cache_key, $total_unread, $cache_group, 10 * MINUTE_IN_SECONDS);
        }
        
        return $total_unread;
	}


    /**
     * Converts a job object to an array.
     *
     * @since 1.0.0
     *
     * @param object $job The job object to convert.
     * @return array The converted job data as an array.
     */
    public static function to_array(?object $job, $type = '*', $display_count = false): array
    {
    // Ensure $job is valid
    if (!$job) {
       
        return [];
    }

        // Safely handle the case when $job->id is NULL
        if (!isset($job->id) || $job->id === null) {
            if ($type == 'single') {
                return [
                    "id"     => $job['id'] ?? null,
                    "status" => $job['status'] ?? 'unknown',
                ];
            } else {
                return [
                    "id"                        => $job['id'] ?? null,
                    "title"                     => $job['title'] ?? '',
                    "slug"                      => $job['slug'] ?? '',
                    "application_deadline"      => $job['application_deadline'] ?? null,
                    "no_of_vacancies"           => $job['no_of_vacancies'] ?? 0,
                    "is_required_cv_photo"      => $job['is_required_cv_photo'] ?? false,
                    "description"               => $job['description'] ?? '',
                    "responsibilities"          => $job['responsibilities'] ?? '',
                    "min_salary"                => $job['min_salary'] ?? 0,
                    "max_salary"                => $job['max_salary'] ?? 0,
                    "benefits"                  => $job['benefits'] ?? '',
                    "requirements"              => $job['requirements'] ?? '',
                    "experienced_year"          => $job['experienced_year'] ?? 0,
                    "additional_requirements"   => $job['additional_requirements'] ?? '',
                    "additional_notes"          => $job['additional_notes'] ?? '',
                    "status"                    => $job['status'] ?? 'unknown',
                    "seo_title"                 => $job['seo_title'] ?? '',
                    "seo_description"           => $job['seo_description'] ?? '',
                    "feature_image"             => $job['feature_image'] ?? '',
                    "form_step_complete"        => $job['form_step_complete'] ?? false,
                    "deleted_at"                => $job['deleted_at'] ?? null,
                    "attribute_values"          => $job['attribute_values'] ?? [], // Safely handle attribute_values
                    "created_at"                => $job['created_at'] ?? null,
                ];
            }
        }

        // Handle the default case when $job->id is set
        $data = [
            "id"                        => $job->id,
            "title"                     => $job->title ?? '',
            "slug"                      => $job->slug ?? '',
            "application_deadline"      => $job->application_deadline ?? null,
            "no_of_vacancies"           => $job->no_of_vacancies ?? 0,
            "is_required_cv_photo"      => $job->is_required_cv_photo ?? false,
            "description"               => $job->description ?? '',
            "responsibilities"          => $job->responsibilities ?? '',
            "min_salary"                => $job->min_salary ?? 0,
            "max_salary"                => $job->max_salary ?? 0,
            "benefits"                  => $job->benefits ?? '',
            "requirements"              => $job->requirements ?? '',
            "experienced_year"          => self::get_job_types_meta($job->experienced_year ?? 0),
            "additional_requirements"   => $job->additional_requirements ?? '',
            "additional_notes"          => $job->additional_notes ?? '',
            "status"                    => $job->status ?? 'unknown',
            "seo_title"                 => $job->seo_title ?? '',
            "seo_description"           => $job->seo_description ?? '',
            "feature_image"             => $job->feature_image ?? '',
            "form_step_complete"        => $job->form_step_complete ?? false,
            "deleted_at"                => $job->deleted_at ?? null,
            "created_at"                => $job->created_at ?? null,
            "attribute_values"          => property_exists($job, 'attribute_values') ? $job->attribute_values : [], // Safely handle attribute_values
        ];

        // Add display_count-related data if requested
        if ($display_count) {
            $data = array_merge($data, [
                'total_unread'      => self::get_total_unread($job->id),
                'total_applicants'  => self::get_total_applicants($job->id),
                'total_in_progress' => self::get_total_in_progress($job->id),
                'total_rejected'    => self::get_total_rejected($job->id),
                'total_hired'       => self::get_total_hired($job->id),
            ]);
        }

        return $data;
    }

    /**
     * Get Job Attribute_Values.
     *
     * @since 1.0.0
     *
     * @param object $job
     *
     * @return object|null
     */
    public static function get_job_types_meta($id)
    {
        $job_types_meta = new Attribute_Values();

        $columns = 'id, value';
        return $job_types_meta->get((int) $id, $columns);
    }
 
    /**
     * Internal method to get total applicants from database.
     *
     * @since 1.0.0
     * @param int $job_id The job ID
     * @param string $table_applications The applications table name
     * @param string $table_jobs The jobs table name
     * @return array Results from the database query
     */
    private static function _query_get_total_applicants($job_id, $table_applications, $table_jobs) {
        global $wpdb;
        
        // Direct query is necessary for counting applicants
        // Caching is handled by the parent method get_total_applicants()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for counting applicants. Caching is handled by the parent method get_total_applicants().
        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT count(app.id) as total_applicants FROM %i as app
                INNER JOIN %i job ON job.id = app.job_id WHERE job_id = %d AND app.deleted_at IS NULL
                 ",
                $table_applications,
                $table_jobs,
                $job_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Internal method to get application count by process ID.
     *
     * @since 1.0.0
     * @param int $job_id The job ID
     * @param int $process_id The process ID
     * @return int Count of applications with this process ID
     */
    private static function _query_get_applications_count_by_process($job_id, $process_id) {
        global $wpdb;
        
        // Direct query is necessary for counting applications by process
        // Caching is handled by the parent method get_total_in_progress()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for counting applications by process. Caching is handled by the parent method get_total_in_progress().
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(id) FROM {$wpdb->prefix}axilweb_ajl_applications 
                WHERE process_id = %d AND job_id = %d",
                $process_id,
                $job_id
            )
        );
    }
    
    /**
     * Internal method to get total rejected applications from database.
     *
     * @since 1.0.0
     * @param int $job_id The job ID
     * @param string $table_applications The applications table name
     * @return array Results from the database query
     */
    private static function _query_get_total_rejected($job_id, $table_applications) {
        global $wpdb;
        
        // Direct query is necessary for counting rejected applications
        // Caching is handled by the parent method get_total_rejected()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for counting rejected applications. Caching is handled by the parent method get_total_rejected().
        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT count(app.id) as total_rejected FROM %i as app
                WHERE app.process_id = %d AND job_id = %d",
                $table_applications,
                AXILWEB_AJL_REJECT_PROCESS_ID,
                $job_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Internal method to get total hired applications from database.
     *
     * @since 1.0.0
     * @param int $job_id The job ID
     * @param string $table_applications The applications table name
     * @return array Results from the database query
     */
    private static function _query_get_total_hired($job_id, $table_applications) {
        global $wpdb;
        
        // Direct query is necessary for counting hired applications
        // Caching is handled by the parent method get_total_hired()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for counting hired applications. Caching is handled by the parent method get_total_hired().
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT count(app.id) as total_hired FROM %i as app
                WHERE app.process_id = %d AND job_id = %d",
                $table_applications,
                AXILWEB_AJL_HIRED_PROCESS_ID,
                $job_id
            ),
            ARRAY_A
        );
    }
    
    /**
     * Internal method to get total unread applications from database.
     *
     * @since 1.0.0
     * @param int $job_id The job ID
     * @return array Results from the database query
     */
    private static function _query_get_total_unread($job_id) {
        global $wpdb;
        
        // Direct query is necessary for counting unread applications
        // Caching is handled by the parent method get_total_unread()
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Direct query is necessary for counting unread applications. Caching is handled by the parent method get_total_unread().
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT COUNT(id) as total_unread 
                 FROM `{$wpdb->prefix}axilweb_ajl_applications` 
                 WHERE is_read != 1 
                 AND job_id = %d 
                 AND deleted_at IS NULL",
                $job_id
            ),
            ARRAY_A
        );
    }
}
