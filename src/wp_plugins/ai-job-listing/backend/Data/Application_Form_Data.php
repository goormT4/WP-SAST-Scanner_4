<?php
namespace Axilweb\AiJobListing\Data;
use Axilweb\AiJobListing\Helpers\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Application_Form_Data
{

  /**
   * Retrieve a field set associated with a specific block ID.
   *
   * This function filters the fields data to find the first matching entry based on the 
   * provided block ID. It uses the `array_filter` function to iterate through the 
   * fields data and returns the first matching field set for the given block ID.
   *
   * @since 1.0.0
   *
   * @param int $block_id The ID of the block for which fields are to be retrieved.
   *                      This value is used to filter the field data.
   *
   * @return mixed The first matching field set associated with the block ID,
   *               or null if no matching field set is found.
   */
    static function getFieldsByblockId($block_id)
    {
      $filteredArray = array_filter(self::getFieldsData(), function ($element) use ($block_id) {
        return $element['block_id'] == $block_id;
      });
      return array_pop($filteredArray);
    }
    
  /**
   * Retrieve the first field set from the fields data.
   *
   * This function filters the fields data and returns the first entry found after filtering. 
   * It uses the `array_filter` function to filter through the data and then extracts 
   * the first matching result using `array_pop`. 
   *
   * @since 1.0.0
   *
   * @return mixed The first field set from the fields data after filtering, 
   *               or null if no matching fields are found.
   */
  static function getFields()
  {
    $filteredArray = array_filter(self::getFieldsData());
    return array_pop($filteredArray);
  }

  /**
   * Retrieve the list of sub-blocks associated with different steps.
   *
   * This function returns a static array of sub-block data, where each sub-block is 
   * associated with a specific step, identified by the `block_id`. Each sub-block 
   * contains an ID, name, and order information for its display sequence.
   *
   * @since 1.0.0
   *
   * @return array An array of sub-blocks, each containing:
   *               - 'id': The unique identifier for the sub-block.
   *               - 'block_id': The identifier of the block (step) the sub-block belongs to.
   *               - 'name': The name/label of the sub-block.
   *               - 'order': The order in which the sub-block should appear within its block.
   */
  static function getSubBlocksData()
  {
    return [
      [
        "id"                => 1,
        "block_id"          => 1, // For Step 1
        "name"              => "Required Information",
        "order"             => 1,
      ],
      [
        "id"                => 2,
        "block_id"          => 2, // For Step 2
        "name"              => "Preliminary Questions",
        "order"             => 1, // For Step 2 order 1
      ],
      [
        "id"                => 3,
        "block_id"          => 2, // For Step 2
        "name"              => "Applicant Information",
        "order"             => 2, // For Step 2 order 2
      ],
      [
        "id"                => 4,
        "block_id"          => 3, // For Step 3
        "name"              => "Upload Your Resume",
        "order"             => 1, // For Step 3 order 1
      ]
    ];
  }

  /**
   * Retrieve the fields data for the user application form.
   *
   * This function gathers and returns the fields for different blocks in the user application form, 
   * including personal information, questions, applicant details, and resume upload options. 
   * The data is dynamically populated for the logged-in user based on their profile information, 
   * including email, phone number, and full name.
   *
   * @since 1.0.0
   *
   * @return array An array of blocks, each containing sub-blocks with associated field options, 
   *               including form elements like text fields, select dropdowns, radio buttons, 
   *               and file upload options. Each field includes attributes such as `label`, 
   *               `name`, `type`, `order`, `required`, and `value`.
   */
  static function getFieldsData()
  {
    
    $current_user = wp_get_current_user();
    // Check if the user is logged in
    if ( $current_user->ID ) {

        $email_address = isset($current_user->user_email) ? $current_user->user_email : null;
        $user_nicename = isset($current_user->user_nicename) ? $current_user->user_nicename : null;
      
        // Attempt to retrieve the phone number
        $phone_number = get_user_meta( $current_user->ID, 'phone_number', true );  
        $full_name = get_user_meta( $current_user->ID, 'full_name', true );  
        // Return the phone number or a default value if empty
        $phone_number = !empty( $phone_number ) ? $phone_number : '';
        $phone_number =  esc_html( $phone_number );
      } else {
        $phone_number =  '';
      }
      
    return [
      [
        "block_id"              => 1,
        "block_name"            => 'Info',
        "order"                 => 1,
        "sub_blocks"            => [
          [
            "sub_block_id"          => 1,
            "sub_block_name"        => 'Required Information',
            "field_options"         => [
              [
                'label'            => false,
                'name'             => 'profile_image',
                'type'             => 'file',
                'placeholder'      => false,
                'order'            => 1,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => false,
                'doc_attachment'   => false,
                'is_show'          => true,

              ],
              [
                'label'            => 'Full Name',
                'name'             => 'full_name',
                'type'             => 'text',
                'placeholder'      => 'Full Name',
                'order'            => 2,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            => $full_name,

              ],
              [
                'label'            => 'How Did You Hear About Us?',
                'name'             => 'about_us',
                'type'             => 'select',
                'placeholder'      => false,
                'order'            => 3,
                'required'         => false,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            =>  Helpers::getAttributeValuesBySlug('lead-source'),
              ],
              [
                'label'            => 'Email',
                'name'             => 'email',
                'type'             => 'email',
                'placeholder'      => 'Your Email Address',
                'order'            => 4,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            => $email_address, 
              ],
              [
                'label'            => 'Phone Number',
                'name'             => 'phone_number',
                'type'             => 'tel',
                'placeholder'      => 'Your Phone Number',
                'order'            => 5,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            => $phone_number, 
              ]
            ],
          ]
        ]
      ],
      [
        "block_id"              => 2,
        "block_name"            => 'Questions',
        "sub_blocks"            => [
          [
            "sub_block_id"        => 2,
            "sub_block_name"        => 'Preliminary Questions',
            "order"               => 1,
            "field_options"          => [
              [
                'label'            => 'Were you referred to this position by an employee of this company?',
                'name'             => 'referred_to',
                'type'             => 'radio',
                'placeholder'      => false,
                'order'            => 1,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => false,
                'is_show'          => true,
                'value'            => [
                  'no' => 'No',
                  'yes' => 'Yes'
                ],
                'conditional_field' => [
                  'label'            => 'Referred Person',
                  'name'             => 'referred_person',
                  'type'             => 'textarea',
                  'placeholder'      => 'Please list the names of the employees.',
                ]

              ],
              [
                'label'            => 'Do you have any relation to an existing employee at this company?',
                'name'             => 'any_relation',
                'type'             => 'radio',
                'placeholder'      => false,
                'order'            => 2,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => false,
                'is_show'          => true,
                'value'            => [
                  'no' => 'No',
                  'yes' => 'Yes'
                ],
                'conditional_field' => [
                  'label'             => 'Relative Person',
                  'name'              => 'relative_person',
                  'type'              => 'textarea',
                  'placeholder'       => 'Please list the names of the employees.',
                ]

              ],
            ]
          ],
          [
            "sub_block_id"          => 3,
            "sub_block_name"        => 'Applicant Information',
            "order"                 => 2,
            "field_options"         => [
              [
                'label'            => 'Years of Experience',
                'name'             => 'work_experience',
                'type'             => 'select',
                'placeholder'      => false,
                'order'            => 1,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            => Helpers::getAttributeValuesBySlug('work-experience'),
              ],
              [
                'label'            => 'When Can You Join?',
                'name'             => 'when_join',
                'type'             => 'select',
                'placeholder'      => false,
                'order'            => 2,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            => Helpers::getAttributeValuesBySlug('notice-period'),
              ],
              [
                'label'            => 'Current Salary',
                'name'             => 'current_salary',
                'type'             => 'number',
                'placeholder'      => false,
                'order'            => 3,
                'required'         => true,
                'default_value'    => false,
                'half_width'        => true,
                'is_show'           => true,
                'field_note' => "BDT"
              ],
              [
                'label'            => 'Expected Salary',
                'name'             => 'expected_salary',
                'type'             => 'number',
                'placeholder'      => false,
                'order'            => 3,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'field_note'       => "BDT"
              ],
              [
                'label'            => 'Education',
                'name'             => 'education',
                'type'             => 'select',
                'placeholder'      => false,
                'order'            => 4,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
                'value'            => Helpers::getAttributeValuesBySlug('job-education-qualifications'),
              ],
              [
                'label'            => 'Education Institute',
                'name'             => 'education_institute',
                'type'             => 'text',
                'placeholder'      => false,
                'order'            => 5,
                'required'         => true,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
              ],
              [
                'label'            => 'Current Company Name',
                'name'             => 'current_company_name',
                'type'             => 'text',
                'placeholder'      => false,
                'order'            => 6,
                'required'         => false,
                'default_value'    => false,
                'half_width'       => true,
                'is_show'          => true,
              ],
              [
                'label'            => 'Briefly Explain Your Work Experience:',
                'name'             => 'explain_work_experience',
                'type'             => 'textarea',
                'placeholder'      => false,
                'order'            => 7,
                'required'         => false,
                'default_value'    => false,
                'half_width'       => false,
                'is_show'          => true,
              ],
              [
                'label'            => 'Portfolio Link:',
                'name'             => 'protfolio_link',
                'type'             => 'url',
                'placeholder'      => 'Add your domain portfolio link e.g., Dribble, Behance, GitHub, Personal website',
                'order'            => 8,
                'required'         => false,
                'default_value'    => false,
                'half_width'       => false,
                'is_show'          => true,
              ],
              [
                'label'            => 'Additional Comments:',
                'name'             => 'additional_comments',
                'type'             => 'textarea',
                'placeholder'      => "Write Comments",
                'order'            => 9,
                'required'         => false,
                'default_value'    => false,
                'half_width'       => false,
                'is_show'          => true,
              ]
            ]
          ]
        ]
      ],
      [
        "block_id"              => 3,
        "block_name"            => 'Resume',
        "order"                 => 1,
        "sub_blocks"            => [
          [
            "sub_block_id"          => 4,
            "sub_block_name"        => 'Upload Your Resume',
            "field_options"          => [
              [
                'label'            => false,
                'name'             => 'resume',
                'type'             => 'file',
                'placeholder'      => false,
                'order'            => 1,
                'required'         => false,
                'default_value'    => false,
                'half_width'       => false,
                'is_show'          => true,
                'doc_attachment'   => true,
              ]
            ],
          ]
        ]
      ],
    ];
  }
}
