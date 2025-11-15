<?php

namespace Axilweb\AiJobListing\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Attribute_Value_Query_Helpers_Trait
{

  /**
   * Removes a job attribute value by its ID with cache invalidation.
   *
   * Deletes a record from the `axilweb_ajl_attribute_values` table based on the provided ID
   * and handles cache invalidation to ensure data consistency.
   *
   * @since 1.0.0
   *
   * @param int $id The ID of the attribute value to remove.
   * @return bool|int Returns the number of rows affected on success, or false on failure.
   */
  public static function removeJob_Attribute_ValueById($id)
  {
    // Validate input
    $id = absint($id);
    if (!$id) {
      return false;
    }
    
    // Get attribute value before deletion to invalidate specific caches
    $attribute_value = self::getAttributeValueById($id);
    
    // Execute the database query
    $result = self::_query_remove_job_attribute_value_by_id($id);
    
    // If deletion was successful, invalidate related caches
    if ($result) {
      // Invalidate individual attribute cache
      $cache_key = 'axilweb_ajl_attr_value_' . $id;
      wp_cache_delete($cache_key, 'axilweb_ajl_attribute_values');
      
      // If we have attribute details, invalidate potential related caches
      if ($attribute_value) {
        // Invalidate related attribute value caches
        if (!empty($attribute_value->attribute_id)) {
          $attribute_id_cache = 'axilweb_ajl_attr_values_by_attr_id_' . $attribute_value->attribute_id;
          wp_cache_delete($attribute_id_cache, 'axilweb_ajl_attribute_values');
        }
        
        if (!empty($attribute_value->job_id)) {
          $job_id_cache = 'axilweb_ajl_attr_values_by_job_id_' . $attribute_value->job_id;
          wp_cache_delete($job_id_cache, 'axilweb_ajl_attribute_values');
        }
      }
      
      // Always invalidate all attributes cache
      wp_cache_delete('axilweb_ajl_all_attr_values', 'axilweb_ajl_attribute_values');
    }
    
    return $result;
  }
  
  /**
   * Get attribute value by ID with caching.
   *
   * Retrieves a single attribute value from the database with caching support.
   *
   * @since 1.0.0
   * @param int $id The ID of the attribute value to retrieve
   * @return object|null The attribute value object or null if not found
   */
  private static function getAttributeValueById($id) {
    // Create cache key
    $cache_key = 'axilweb_ajl_attr_value_' . $id;
    $cache_group = 'axilweb_ajl_attribute_values';
    
    // Try to get from cache first
    $result = wp_cache_get($cache_key, $cache_group);
    
    // If not in cache, fetch from database
    if (false === $result) {
      $result = self::_query_get_attribute_value_by_id($id);
      
      // Cache the result
      if ($result) {
        wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
      }
    }
    
    return $result;
  }

  
  
  /**
   * Internal method to query an attribute value by its ID.
   * Implementation method for getAttributeValueById().
   *
   * @since 1.0.0
   * @param int $id The ID of the attribute value to retrieve
   * @return object|null The attribute value object or null if not found
   */

   private static function _query_get_attribute_value_by_id($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'axilweb_ajl_attribute_values';
    
    // Create a cache key for this specific attribute value
    $cache_key = 'axilweb_ajl_attribute_value_' . absint($id);
    $cache_group = 'axilweb_ajl_attribute_values';
    
    // Try to get from cache first
    $result = wp_cache_get($cache_key, $cache_group);
    
    // If not in cache, fetch from database
    if (false === $result) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table operation with cache invalidation implemented above
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM %i WHERE id = %d",
                $table_name,
                $id
            )
        );
        
        // Cache the result for future requests
        if ($result) {
            wp_cache_set($cache_key, $result, $cache_group, HOUR_IN_SECONDS);
        }
    }
    
    return $result;
  }
  
    /**
     * Internal method to remove a job attribute value by its ID.
     * Implementation method for removeJob_Attribute_ValueById().
     *
     * This is a specific implementation for removing data from the custom attribute values table,
     * which requires a direct database query.
     *
     * @since 1.0.0
     * @param int $id The ID of the attribute value to remove
     * @return bool|int Result of the database operation
     */
    private static function _query_remove_job_attribute_value_by_id($id) {
      global $wpdb; 
      // Table name
      $table_attribute_values = $wpdb->prefix . 'axilweb_ajl_attribute_values'; 
      
      // Invalidate the cache for this attribute value before deletion
      $cache_key = 'axilweb_ajl_attribute_value_' . absint($id);
      $cache_group = 'axilweb_ajl_attribute_values';
      wp_cache_delete($cache_key, $cache_group);
      
      // Create a transaction log cache key
      $transaction_key = 'axilweb_ajl_attribute_delete_' . absint($id);
      $transaction_group = 'axilweb_ajl_transactions';
      
      // Properly escape table name for the query
      $table = $table_attribute_values;
      
      // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom table operation with cache invalidation implemented above
      return $wpdb->delete(
          $table,
          array( 'id' => $id ),
          array( '%d' )
      );
    }
}