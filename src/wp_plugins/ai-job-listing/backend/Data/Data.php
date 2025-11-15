<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Data class.  
 *
 * @since 0.1.0
 */
class Data {
  use Job_Attribute_Data;
  use Application_About_Info_Data;
  use Application_Work_Experience_Data;
  use Application_Form_Data;
  use Applications_Process_Data;
  use Setting_Data;
} 