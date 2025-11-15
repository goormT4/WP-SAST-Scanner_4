<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Application_About_Info_Data {
 
  /**
   * Retrieve the about information based on the given ID.
   *
   * This function filters the application about information by the provided `id` 
   * and returns the first matching entry. If no match is found, it returns `null`.
   * The function uses `array_filter` to search for the element with the specified ID 
   * within the application about information array and returns the first matching result.
   *
   * @since 1.0.0
   *
   * @param int $id The ID used to filter the about information.
   *                This value is used to match against the 'id' field in the data.
   *
   * @return mixed The first matching element from the application about information, 
   *               or `null` if no matching element is found.
   */
  static function getAboutInfoById($id)
  {
     $filteredArray = array_filter(self::getApplicationAboutInfo(), function($element) use ($id) {
      return $element['id'] == $id;
    });
    return array_pop( $filteredArray );
  }
  
  /**
   * Retrieve a list of application about information.
   *
   * This function returns an array of application about information items. Each item 
   * in the array contains details such as the `id`, `name`, and whether the entry is 
   * active (`is_active`). The returned array can be used to populate selection options 
   * or provide a list of sources that users can choose from.
   *
   * @since 1.0.0
   *
   * @return array An array of application about information, where each element contains:
   *               - 'id': The unique identifier for the entry.
   *               - 'name': The name/label associated with the information source.
   *               - 'is_active': A flag indicating whether the entry is active (1 for active, 0 for inactive).
   */
  static function getApplicationAboutInfo() {
     return [ 
      [
        "id"=> 1,
        "name"=> "Axilweb website", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 2,
        "name"=> "LinkedIn", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 3,
        "name"=> "Google search", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 4,
        "name"=> "Facebook", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 5,
        "name"=> "Twitter", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 6,
        "name"=> "Instagram", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 7,
        "name"=> "bdjobs", 
        "is_active"=> 1, 
      ], 
      [
        "id"=> 8,
        "name"=> "Personal Reference", 
        "is_active"=> 1, 
      ], 
    ];
  }
}
