<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Table_Name {
  const ATTRIBUTES              = 'axilweb_ajl_attributes';
  const ATTRIBUTES_VALUES       = 'axilweb_ajl_attribute_values';
  const JOBS                    = 'axilweb_ajl_jobs';
  const JOB_ATTRIBUTES_VALUE    = 'axilweb_ajl_job_attribute_value';
  const JOB_APPLICATIONS        = 'axilweb_ajl_applications';
  const JOB_APPLICATION_META    = 'axilweb_ajl_application_meta';
  const JOB_APP_PROCESS         = 'axilweb_ajl_app_process';
  const JOB_APP_PROCESS_COMMENT = 'axilweb_ajl_app_process_comment';
  const JOB_APP_PROCESS_BY_JOB  = 'axilweb_ajl_app_process_by_job';
  const GENERAL_SETTINGS        = 'axilweb_ajl_general_settings';
}