<?php 
namespace Axilweb\AiJobListing\Helpers;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

trait Attribute_Query_Helpers_Trait
{

    /**
     * Fetches all attributes from the attributes table with caching.
     *
     * This function retrieves all rows from the `axilweb_ajl_attributes` table
     * and implements caching to improve performance.
     *
     * @since 1.0.0
     *
     * @return array The list of attributes as an array of objects.
     */
    public static function fetchAllAttributes()
    {
      // Create cache key
      $cache_key = 'axilweb_ajl_all_attributes';
      $cache_group = 'axilweb_ajl_attributes';
      
      // Try to get from cache first
      $results = wp_cache_get($cache_key, $cache_group);
      
      // If not in cache, fetch from database
      if (false === $results) {
        $results = self::_query_fetch_all_attributes();
        
        // Cache the results
        if ($results) {
          wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
        }
      }
      
      // Returning the results
      return $results;
    }
    
  /**
   * Fetch all attributes from the custom attributes table.
   *
   * @return array Array of attribute objects
   */
  private static function _query_fetch_all_attributes() {
    global $wpdb;
    
    // Table name with WordPress prefix
    $table_name = $wpdb->prefix . 'axilweb_ajl_attributes';
    
    // Build cache key
    $cache_key = 'axilweb_ajl_all_attributes';
    $cache_group = 'axilweb_ajl';
    
    // Check cache first
    $results = wp_cache_get($cache_key, $cache_group);
    if (false !== $results) {
        return $results;
    }
    
    // If not in cache, perform the query
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom table requires direct query, using prepare for safety
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM %s",
            $table_name
        )
    );
    
    // Store in cache for 1 hour
    wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
    
    return $results;
  }
  
  /**
   * Fetches a specific column from the attributes table with caching.
   *
   * This function retrieves the specified column's values from the
   * `axilweb_ajl_attributes` table and implements caching for improved performance.
   *
   * @since 1.0.0
   *
   * @param string $column_name The name of the column to retrieve.
   * @return array|null An array of values from the specified column, or null if the query fails.
   */
  public static function fetchColumnFromAttributes($column_name) {
      // Validate column name - only allow alphanumeric and underscore characters
      if (!preg_match('/^[a-zA-Z0-9_]+$/', $column_name)) {
          return null; // Invalid column name
      }
      
      // Create cache key
      $cache_key = 'axilweb_ajl_attr_column_' . md5($column_name);
      $cache_group = 'axilweb_ajl_attributes';
      
      // Try to get from cache first
      $results = wp_cache_get($cache_key, $cache_group);
      
      // If not in cache, fetch from database
      if (false === $results) {
          $results = self::get_distinct_column_values($column_name);
          
          // Cache the results
          if ($results) {
              wp_cache_set($cache_key, $results, $cache_group, HOUR_IN_SECONDS);
          }
      }
      
      // Returning the results
      return $results;
  } 
      
    public static function get_distinct_column_values( string $column, array $args = [] ): array {
        global $wpdb;

        // 1. Validate column name with stricter regex pattern
        if ( ! preg_match( '/^[a-zA-Z0-9_]+$/', $column ) ) {
            return []; // Invalid column name, return empty array
        }
        
        // 2. Properly construct table name
        $table = $wpdb->prefix . 'axilweb_ajl_attributes';

        // 3. Initialize query values array - we'll collect all values for prepare() here
        $query_values = [];

        // 4. Start building the parameterized query
        $sql = "SELECT DISTINCT `{$column}` FROM `{$table}` WHERE 1=1";

        // 5. Add conditions if provided in $args
        if ( ! empty( $args['conditions'] ) && is_array( $args['conditions'] ) ) {
            foreach ( $args['conditions'] as $condition ) {
                if ( ! isset( $condition['column'], $condition['value'], $condition['operator'] ) ) {
                    continue;
                }

                // Validate condition column name
                $cond_column = $condition['column'];
                if ( ! preg_match( '/^[a-zA-Z0-9_]+$/', $cond_column ) ) {
                    continue; // Skip invalid column names
                }

                // Sanitize and validate operator
                $operator = self::sanitize_operator( $condition['operator'] );
                
                // Handle different value types
                if ( is_array( $condition['value'] ) ) {
                    // For IN clauses
                    if ( $operator === 'IN' || $operator === 'NOT IN' ) {
                        $placeholders = implode( ', ', array_fill( 0, count( $condition['value'] ), '%s' ) );
                        $sql .= " AND `{$cond_column}` {$operator} ({$placeholders})";
                        
                        // Add each array value as an individual parameter
                        foreach ( $condition['value'] as $val ) {
                            $query_values[] = $val;
                        }
                    }
                } else {
                    // For standard comparisons
                    $sql .= " AND `{$cond_column}` {$operator} %s";
                    $query_values[] = $condition['value'];
                }
            }
        }

        // 6. Prepare and execute the query
        $results = [];
        if ( ! empty( $query_values ) ) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Using $wpdb->prepare properly with validated identifiers
            $prepared_query = $wpdb->prepare( $sql, $query_values );
            
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom table operations with proper validation
            $results = $wpdb->get_col( $prepared_query );
        } else {
            // No parameters to prepare, query is safe as-is
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Custom table query with proper validation
            $results = $wpdb->get_col( $sql );
        }

        return is_array( $results ) ? $results : [];
    }
 
}
