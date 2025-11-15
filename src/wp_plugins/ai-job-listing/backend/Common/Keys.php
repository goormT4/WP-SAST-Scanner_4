<?php
namespace Axilweb\AiJobListing\Common;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Manage all key strings.
 *
 * @since 0.1.0
 */
class Keys
{

    /**
     * ai_job_listing job installed option key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const AXILWEB_AJL_INSTALLED = 'axilweb_ajl_installed';

    /**
     * ai_job_listing job version key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const AXILWEB_AJL_VERSION = 'axilweb_ajl_version';

    /**
     * Job type seeder ran key.
     *
     * @var string
     *
     * @since 0.5.0
     */
    const JOB_TYPE_SEEDER_RAN = 'axilweb_ajl_job_type_seeder_ran';

    /**
     * Job seeder ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const JOB_SEEDER_RAN = 'axilweb_ajl_job_seeder_ran';

    /**
     * Job seeder ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const JOB_META_VALUE_SEEDER_RAN = 'axilweb_ajl_job_meta_value_seeder_ran';

    /**
     * Job Location ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const JOB_ATTIBUTE_SEEDER_RAN = 'axilweb_ajl_job_attribute_seeder_ran';

    /**

     * Job Location ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const JOB_ATTIBUTE_VALUE_SEEDER_RAN = 'axilweb_ajl_job_attribute_value_seeder_ran';

    /**
 
     * Job Applications ran key
     *
     * @var string
     *
     * @since 1.0.0
     */
    const APPLICATIONS_SEEDER_RAN = 'axilweb_ajl_applications';

    /**
     * Application Process ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const EMAIL_TYPE_SEEDER_RAN = 'axilweb_ajl_email_type_seeder_ran';
    const EMAIL_TEMPLATE_SEEDER_RAN = 'axilweb_ajl_email_template_seeder_ran';
    

    /**
     * Application Process ran key.
     *
     * @var string
     *
     * @since 1.0.0
     */
    const APPLICATION_PROCESS_SEEDER_RAN = 'axilweb_ajl_app_process_seeder_ran';
    
    /** 
     * Check whether Default page created or not.
     *
     * @var string
     *
     * @since 1.0.0
     */ 
    const DEFAULT_PAGE_ALREADY_CREATED = 'axilweb_ajl_default_page_already_created';
    const GENERAL_SETTINGS_SEEDER_RAN = 'axilweb_ajl_general_settings';
}
