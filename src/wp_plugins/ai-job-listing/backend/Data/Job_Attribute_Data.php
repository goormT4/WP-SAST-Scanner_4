<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Job_Attribute_Data {
  
  /**
   * Retrieve job attribute information based on the provided attribute ID.
   *
   * This function filters the list of job attributes to find the entry that matches the provided 
   * `attribute_id`. It uses `array_filter` to search for the matching element and returns the 
   * first matching entry using `array_pop`. If no match is found, it returns `null`.
   *
   * @since 1.0.0
   *
   * @param int $attribute_id The ID used to filter the job attributes.
   *                          This ID is matched against the 'attribute_id' field of each entry.
   *
   * @return mixed The first matching job attribute from the job attributes array, 
   *               or `null` if no matching attribute is found.
   */
  static function getJobAtributeById($attribute_id)
  {
     $filteredArray = array_filter(self::getJobAtributes(), function($element) use ($attribute_id) {
      return $element['attribute_id'] == $attribute_id;
    });
    return array_pop( $filteredArray );
  }
  
  /**
   * Retrieve job attribute information based on the provided attribute slug.
   *
   * This function filters the list of job attributes to find the entry that matches the provided 
   * `attribute_slug`. It uses `array_filter` to search for the matching element and returns the 
   * first matching entry using `array_pop`. If no match is found, it returns `null`.
   *
   * @since 1.0.0
   *
   * @param string $attribute_slug The slug used to filter the job attributes.
   *                               This slug is matched against the 'attribute_slug' field of each entry.
   *
   * @return mixed The first matching job attribute from the job attributes array, 
   *               or `null` if no matching attribute is found.
   */
  static function getJobAtributeBySlug($attribute_slug)
  {
   
     $filteredArray = array_filter(self::getJobAtributes(), function($element) use ($attribute_slug) {
      return $element['attribute_slug'] == $attribute_slug;
    });
    return array_pop( $filteredArray );
  }
 
  /**
   * Retrieve the field names from the job attributes.
   *
   * This function extracts the field keys from the options of the job attributes. 
   * It retrieves all job attributes and collects the `field_key` from the first option 
   * of each attribute. The result is an array of field names (keys) that can be used 
   * for further processing or rendering the job attribute fields.
   *
   * @since 1.0.0
   *
   * @return array An array of field keys, each representing a field name from the job attributes' options.
   */
  static function getJobAtributesFieldName()
  {
    $job_attributes = self::getJobAtributes();
    $field_names = [];
    foreach ($job_attributes as $job_attribute) {
      $field_names[] = $job_attribute['options_field']['0']['field_key'];
    }
    return $field_names; 
  }

  /**
   * Retrieve a list of job attributes.
   *
   * This function returns an array of job attributes, where each attribute contains information 
   * such as the `attribute_id`, `attribute_name`, `attribute_slug`, and options for the attribute 
   * such as the `field_key`. These job attributes are used to define various job-related properties 
   * in the system, such as job types, departments, locations, and other related settings.
   *
   * @since 1.0.0
   *
   * @return array An array of job attributes, where each element contains:
   *               - 'attribute_id': The unique identifier for the attribute.
   *               - 'attribute_name': The name of the attribute.
   *               - 'attribute_slug': The slug representing the attribute (used for URL or identification).
   *               - 'menu_orderby': The order of the attribute in the menu.
   *               - 'is_active': A flag indicating whether the attribute is active (1 for active).
   *               - 'options_field': An array of fields associated with the attribute, including:
   *                 - 'label': The label of the field.
   *                 - 'field_type': The type of the field (e.g., text).
   *                 - 'field_name': The name of the field.
   *                 - 'field_key': The unique key of the field.
   */
  static function getJobAtributes() {
     return [ 
      [
        "attribute_id"=> 1,
        "attribute_name"=> "Job Types",
        "attribute_slug"=> "job-types",
        "menu_orderby"=> 1,
        "is_active"=> 1,
        "options_field"=> [
          [
            "label"       => "Name",
            "field_type"  => "text",
            "field_name"  => "value",
            "field_key"  => "job_types",
          ] 
        ]
      ],
      [
        'attribute_id'              => 2,
        'attribute_name'            => 'Departments',
        'attribute_slug'            => 'job-departments', 
        'menu_orderby'              => 2, 
        'is_active'                 => 1,    
        'options_field'             => [ 
          [
            "label" => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "job_departments"
          ] 
       ]  
      ],
      [
        'attribute_id'              => 3,
        'attribute_name'            => 'Job Shifts',
        'attribute_slug'            => 'job-shifts', 
        'menu_orderby'              => 3, 
        'is_active'                 => 1,  
        'options_field'             => [ 
          [
            "label" => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "job_shifts"
          ] 

       ]    
      ],
      [
        'attribute_id'              => 4,
        'attribute_name'            => 'Locations',
        'attribute_slug'            => 'job-locations', 
        'menu_orderby'              => 4, 
        'is_active'                 => 1, 
        'options_field'             => [ 
          [
            "label" => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "job_locations"
          ] 
       ]    
      ],
      [
        'attribute_id'              => 5,
        'attribute_name'            => 'Education Qualifications',
        'attribute_slug'            => 'job-education-qualifications', 
        'menu_orderby'              => 5, 
        'is_active'                 => 1,
        'options_field'             => [ 
          [
            "label" => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "job_education_qualifications"
          ] 
       ]        
      ],
 
      [
        'attribute_id'              => 6,
        'attribute_name'            => 'Salary Types',
        'attribute_slug'            => 'job-salary-type', 
        'menu_orderby'              => 6, 
        'is_active'                 => 1,  
        'options_field'             => [ 
          [
            "label" => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "job_salary_type"
          ] 
       ]       
      ], 
      [
        'attribute_id'              => 7,
        'attribute_name'            => 'Lead Source',
        'attribute_slug'            => 'lead-source', 
        'menu_orderby'              => 7, 
        'is_active'                 => 1,  
        'options_field'             => [ 
          [
            "label"      => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "lead_source"
          ] 
       ]       
      ],
      [
        'attribute_id'              => 8,
        'attribute_name'            => 'Years of Experience',
        'attribute_slug'            => 'work-experience', 
        'menu_orderby'              => 8, 
        'is_active'                 => 1,  
        'options_field'             => [ 
          [
            "label"      => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "work_experience"
          ] 
       ]       
      ],
      [
        'attribute_id'              => 9,
        'attribute_name'            => 'Notice Period',
        'attribute_slug'            => 'notice-period', 
        'menu_orderby'              => 9, 
        'is_active'                 => 1,  
        'options_field'             => [ 
          [
            "label"      => "Name",
            "field_type" => "text",
            "field_name" => "value",
            "field_key"  => "notice_period"
          ] 
       ]       
      ],  
    ];
  }
}