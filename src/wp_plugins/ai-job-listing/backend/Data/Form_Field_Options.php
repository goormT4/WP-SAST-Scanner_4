<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Form_Field_Options {

  /**
   * Retrieve application about information based on the provided ID.
   *
   * This function filters the list of application about information to find the entry 
   * that matches the provided `id`. It uses `array_filter` to search for the matching 
   * element and then returns the first matching element using `array_pop`. If no match 
   * is found, it returns `null`.
   *
   * @since 1.0.0
   *
   * @param int $id The ID used to filter the application about information.
   *                This ID is matched against the 'id' field of each entry.
   *
   * @return mixed The first matching element from the application about information array, 
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
   * Retrieve the list of application about information sources.
   *
   * This function returns an array of sources through which users have heard about the application. 
   * Each entry in the array contains an `id`, a `name` representing the source name, and an `is_active` 
   * flag that indicates whether the source is active or not. The array can be used to populate 
   * dropdowns, selection options, or display the sources in a user interface.
   *
   * @since 1.0.0
   *
   * @return array An array of application about information sources, where each element contains:
   *               - 'id': The unique identifier for the source.
   *               - 'name': The name of the source (e.g., "LinkedIn", "Facebook").
   *               - 'is_active': A flag indicating whether the source is active (1 for active, 0 for inactive).
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
