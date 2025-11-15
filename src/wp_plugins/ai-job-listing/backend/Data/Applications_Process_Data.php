<?php
namespace Axilweb\AiJobListing\Data;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Applications_Process_Data { 
  
  /**
   * Retrieve the list of application processes.
   *
   * This function returns an array of application processes, where each process contains:
   * - `id`: The unique identifier for the process.
   * - `name`: The name of the process.
   * - `slug`: A URL-friendly identifier for the process.
   * - `class`: A CSS class associated with the process for styling.
   * - `icon`: The icon class for the process.
   * - `step_order`: The order in which the process step appears in the sequence.
   * - `menu_order`: The order in the menu, if applicable (can be NULL).
   *
   * This data can be used to represent the different stages of an application process, 
   * including steps like "Phone", "Face", "Test", "Final", etc.
   *
   * @since 1.0.0
   *
   * @return array An array of application process stages, where each element contains:
   *               - 'id': The unique identifier for the process.
   *               - 'name': The name of the process.
   *               - 'slug': A URL-friendly identifier for the process.
   *               - 'class': A CSS class associated with the process.
   *               - 'icon': The icon for the process.
   *               - 'step_order': The step order in the application process.
   *               - 'menu_order': The order for the menu display, or NULL.
   */
  static function getApplicationsProcess() {
     return [ 
      [
        "id"          => 1,
        "name"        => "Unlisted", 
        "slug"        => 'unlisted', 
        "class"       => 'process-unlisted', 
        "icon"        => 'fa-users', 
        "step_order"  => 1, 
        "menu_order"  => NULL, 
      ], 
      [
        "id"          => 2,
        "name"        => "Phone", 
        "slug"        => 'phone', 
        "class"       => 'process-phone', 
        "icon"        => 'fa-phone', 
        "step_order"  => 2, 
        "menu_order"  => NULL, 
      ], 
      [
        "id"          => 3,
        "name"        => "Face", 
        "slug"        => 'face', 
        "class"       => 'process-face', 
        "icon"        => 'fa-user', 
        "step_order"  => 3, 
        "menu_order"  => NULL, 
      ], 
      [
        "id"          => 4,
        "name"        => "Test", 
        "slug"        => 'test', 
        "class"       => 'process-test', 
        "icon"        => 'fa-keyboard', 
        "step_order"  => 4, 
        "menu_order"  => NULL, 
      ], 
      [
        "id"          => 5,
        "name"        => "Final", 
        "slug"        => 'final', 
        "class"       => 'process-final', 
        "icon"        => 'fa-user-check', 
        "step_order"  => 5, 
        "menu_order"  => NULL, 
      ], 
      [
        "id"          => 6,
        "name"        => "Hired", 
        "slug"        => 'hired', 
        "class"       => 'process-hired', 
        "icon"        => 'fa-check', 
        "step_order"  => 6, 
        "menu_order"  => NULL, 
      ], 
      [
        "id"          => 7,
        "name"        => "Rejected", 
        "slug"        => 'rejected', 
        "class"       => 'process-rejected', 
        "icon"        => 'fa-times', 
        "step_order"  => 7, 
        "menu_order"  => NULL, 
      ],
      [
        "id"          => 8,
        "name"        => "All", 
        "slug"        => 'all', 
        "class"       => 'process-all', 
        "icon"        => 'FaUserPlus', 
        "step_order"  => 8, 
        "menu_order"  => NULL, 
      ] 
    ];
  }
}
