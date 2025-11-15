<?php

namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Application_Work_Experience_Data
{

  /**
   * Retrieve the work experience information based on the given ID.
   *
   * This function filters the work experience data by the provided `id` 
   * and returns the first matching entry. If no match is found, it returns `null`.
   * The function uses `array_filter` to search for the element with the specified ID 
   * in the work experience array and returns the first matching result.
   *
   * @since 1.0.0
   *
   * @param int $id The ID used to filter the work experience data.
   *                This value is used to match against the 'id' field in the data.
   *
   * @return mixed The first matching element from the work experience data, 
   *               or `null` if no matching element is found.
   */
  static function getgetWorkExperienceById($id)
  {
    $filteredArray = array_filter(self::getWorkExperience(), function ($element) use ($id) {
      return $element['id'] == $id;
    });
    return array_pop($filteredArray);
  }

  /**
   * Retrieve the list of work experience options.
   *
   * This function returns an array of work experience options, where each option contains
   * an `id`, a `value` representing the years of experience, and an `is_active` flag indicating 
   * whether the option is active (1 for active, 0 for inactive). This data can be used for 
   * populating a dropdown or selection menu in the application form or user interface.
   *
   * @since 1.0.0
   *
   * @return array An array of work experience options, where each element contains:
   *               - 'id': The unique identifier for the experience level.
   *               - 'value': The work experience description (e.g., "0 year", "1 year").
   *               - 'is_active': A flag indicating whether the option is active (1 for active, 0 for inactive).
   */
  static function getWorkExperience()
  {
    return [
      [
        "id" => 1,
        "value" => "0 year",
        "is_active" => 1,
      ],
      [
        "id" => 2,
        "value" => "1 year",
        "is_active" => 1,
      ],
      [
        "id" => 3,
        "value" => "2 years",
        "is_active" => 1,
      ],
      [
        "id" => 4,
        "value" => "3 years",
        "is_active" => 1,
      ],
      [
        "id" => 5,
        "value" => "4 years",
        "is_active" => 1,
      ],
      [
        "id" => 6,
        "value" => "5 years",
        "is_active" => 1,
      ],
      [
        "id" => 7,
        "value" => "5 ++ years",
        "is_active" => 1,
      ],
    ];
  }
}
