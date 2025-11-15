<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Setting_Data { 

  static function getListingPageSetting()
  {
    return [
      [
        'label'       => 'Career Page Title',
        'name'        => 'career_page_title',
        'value'       => 'Be a Part of our Growing Team.',
        'placeholder' => 'Enter Page Title',
        'type'        => 'general_setting',
        'form_type'   => 'text',
        'options'       => null,
        'column_width'  => null,
        'updated_at'    => null,
      ],
      [
        'label'         => 'Career Page Tagline',
        'name'          => 'career_page_tagline',
        'value'         => 'Make an impact by doing what you love.',
        'placeholder'   => 'Enter Page Tagline',
        'type'          => 'general_setting',
        'form_type'     => 'text',
        'options'       => null,
        'column_width'  => null,
        'updated_at'    => null,
      ],
      [
        'label' => 'Primary Color',
        'name' => 'primary_color',
        'value' => '#323da5',
        'placeholder' => 'Enter company address',
        'type' => 'general_setting',
        'form_type' => 'color',
        'options' => null,
        'column_width' => null,
        'updated_at' => null,
      ],
      [
        'label' => 'Secondary Color',
        'name' => 'secondary_color',
        'value' => '#ff7f5c',
        'placeholder' => 'Enter Secondary Color',
        'type' => 'general_setting',
        'form_type' => 'color',
        'options' => null,
        'column_width' => null,
        'updated_at' => null,
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
        'updated_at'        => null, 
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
        'updated_at'        => null,
        
      ],   
      [
        'label' => 'Currency Symbol',
        'name' => 'currency_symbol',
        'value' => '$',
        'placeholder' => 'Enter Temperature',
        'type' => 'general_setting',
        'form_type' => 'text',
        'options' => null,
        'column_width' =>  null,
        'updated_at' => null,
      ], 
      [
        'label' => 'Career Page Select',
        'name' => 'career_page',
        'value' => '',
        'placeholder' => 'Select Career Page',
        'type' => 'general_setting',
        'form_type' => 'select',
        'options' => null,
        'column_width' => 'half',
        'updated_at' => null,
      ], 

    // AI 
    [
      'label' => 'API KEY',
      'name' => 'open_ai_api_key',
      'value' => '',
      'placeholder' => 'Enter API KEY',
      'type' => 'ai_setting',
      'form_type' => 'password',
      'options' => null,
      'column_width' => 'full',
      'updated_at' => null,
    ],
      
    ];
  }
  
  static function getSettingInfo() {
   return [
    'per_page' => [
      "id"                => 1,
      'name'              => 'per_page',
      'label'             => 'Post Per Page',
      'value'             => '10',   
      'type'              => 'global_setting',   
    ], 
    'jobs_limit' => [
      "id"                => 2,
      'name'              => 'jobs_limit',
      'label'             => 'Jobs Post Limit',
      'value'             => '10',   
      'type'              => 'jobs_setting',    
    ],  
    'primary_color' => [
      "id"                => 3,
      'name'              => 'primary_color',
      'label'             => 'Primary Color',
      'value'             => '#333333',   
      'type'              => 'jobs_setting',    
    ],
    'secondary_color' => [
      "id"                => 4,
      'name'              => 'secondary_color',
      'label'             => 'Secondary Color',
      'value'             => '#111111',   
      'type'              => 'applications_setting',    
    ],
    'tertiary_color' => [
      "id"                => 5,
      'name'              => 'tertiary_color',
      'label'             => 'Tertiary Color',
      'value'             => '#222222',   
      'type'              => 'applications_setting',    
    ],
    'font_Family' => [
      "id"                => 6,
      'name'              => 'font_Family',
      'label'             => 'Font Family',
      'value'             => 'Roboto',   
      'type'              => 'applications_setting',    
    ] 
  ];
}
}
