<?php
namespace Axilweb\AiJobListing\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Security Utilities Class
 * 
 * Provides centralized security functions for sanitization, validation, and safe DB operations.
 * This class ensures consistent security practices throughout the plugin.
 *
 * @since 1.0.0
 */
class Security_Utilities {
    
    /**
     * Validates and sanitizes a table name for use in database queries.
     * 
     * @since 1.0.0
     * 
     * @param string $table_name The table name to validate
     * @return string Sanitized table name
     */
    public static function sanitize_table_name($table_name) {
        // Remove common prefixes for validation but keep original for return
        $clean_name = str_replace(array('wp_', 'axil_'), '', $table_name);
        
        // Only allow alphanumeric characters and underscores
        if (!preg_match('/^[\w_]+$/', $clean_name)) {
            return '';
        }
        
        // Return the validated table name with backticks for safe SQL use
        return '`' . $table_name . '`';
    }
    
    // public static function sanitize_table_name_old($table_name) {
    //     // Only allow alphanumeric characters, underscores and prefix
    //     if (!preg_match('/^[\w_]+$/', str_replace(array('wp_', 'axil_'), '', $table_name))) {
    //         return '';
    //     }
        
    //     return esc_sql($table_name);
    // }
    
    /**
     * Validates and sanitizes a column name for use in database queries.
     * 
     * @since 1.0.0
     * 
     * @param string $column_name The column name to validate
     * @return string Sanitized column name or empty string if invalid
     */
    public static function sanitize_column_name($column_name) {
        // For wildcards like '*'
        if ($column_name === '*') {
            return '*';
        }
        
        // For column lists like 'id, name, status'
        if (strpos($column_name, ',') !== false) {
            $columns = array_map('trim', explode(',', $column_name));
            $sanitized_columns = array_map([self::class, 'sanitize_column_name'], $columns);
            
            // Filter out any empty values (invalid columns)
            $sanitized_columns = array_filter($sanitized_columns);
            if (empty($sanitized_columns)) {
                return '';
            }
            
            return implode(', ', $sanitized_columns);
        }
        
        // For aliased columns like 'count(*) as total'
        if (stripos($column_name, ' as ') !== false) {
            list($column, $alias) = array_map('trim', explode(' as ', $column_name, 2));
            $sanitized_column = self::sanitize_column_name($column);
            
            // For alias, we need to validate it's a basic identifier without backticks
            $sanitized_alias = '';
            if (preg_match('/^[a-zA-Z0-9_]+$/', $alias)) {
                $sanitized_alias = '`' . $alias . '`';
            }
            
            if (!empty($sanitized_column) && !empty($sanitized_alias)) {
                return "$sanitized_column AS $sanitized_alias";
            }
            return '';
        }
        
        // For function calls like 'count(*)'
        if (preg_match('/^([a-zA-Z0-9_]+)\((.*)\)$/', $column_name, $matches)) {
            $function = $matches[1];
            $argument = $matches[2];
            
            // Only allow specific SQL functions
            $allowed_functions = ['count', 'sum', 'avg', 'max', 'min', 'date'];
            if (!in_array(strtolower($function), $allowed_functions)) {
                return '';
            }
            
            if ($argument === '*') {
                return "$function(*)";
            }
            
            $sanitized_argument = self::sanitize_column_name($argument);
            if (!empty($sanitized_argument)) {
                return "$function($sanitized_argument)";
            }
            return '';
        }
        
        // For standard column names - validate strictly
        if (preg_match('/^[a-zA-Z0-9_]+$/', $column_name)) {
            // Return properly backticked column name
            return '`' . $column_name . '`';
        }
        
        // Invalid column name
        return '';
    }

    // public static function sanitize_column_name($column_name) {
    //     // For wildcards like '*'
    //     if ($column_name === '*') {
    //         return '*';
    //     }
        
    //     // For column lists like 'id, name, status'
    //     if (strpos($column_name, ',') !== false) {
    //         $columns = array_map('trim', explode(',', $column_name));
    //         $sanitized_columns = array_map([self::class, 'sanitize_column_name'], $columns);
    //         return implode(', ', $sanitized_columns);
    //     }
        
    //     // For aliased columns like 'count(*) as total'
    //     if (stripos($column_name, ' as ') !== false) {
    //         list($column, $alias) = array_map('trim', explode(' as ', $column_name, 2));
    //         $sanitized_column = self::sanitize_column_name($column);
    //         $sanitized_alias = self::sanitize_column_name($alias);
    //         if (!empty($sanitized_column) && !empty($sanitized_alias)) {
    //             return "$sanitized_column AS $sanitized_alias";
    //         }
    //         return '';
    //     }
        
    //     // For function calls like 'count(*)'
    //     if (preg_match('/^([a-zA-Z0-9_]+)\((.*)\)$/', $column_name, $matches)) {
    //         $function = $matches[1];
    //         $argument = $matches[2];
            
    //         // Only allow specific SQL functions
    //         $allowed_functions = ['count', 'sum', 'avg', 'max', 'min', 'date'];
    //         if (!in_array(strtolower($function), $allowed_functions)) {
    //             return '';
    //         }
            
    //         if ($argument === '*') {
    //             return "$function(*)";
    //         }
            
    //         $sanitized_argument = self::sanitize_column_name($argument);
    //         if (!empty($sanitized_argument)) {
    //             return "$function($sanitized_argument)";
    //         }
    //         return '';
    //     }
        
    //     // For standard column names
    //     if (!preg_match('/^[a-zA-Z0-9_]+$/', $column_name)) {
    //         return '';
    //     }
        
    //     return esc_sql($column_name);
    // }
    
    /**
     * Safely builds a WHERE clause for database queries
     * 
     * @since 1.0.0
     * 
     * @param array  $conditions Array of conditions
     * @param object $wpdb       WordPress database object
     * @return array Array containing the WHERE clause string and values array
     */
    public static function build_where_clause($conditions, $wpdb) {
        global $wpdb;
        
        $where_parts = [];
        $values = [];
        
        foreach ($conditions as $column => $condition) {
            $column = self::sanitize_column_name($column);
            
            if (empty($column)) {
                continue;
            }
            
            if (is_array($condition)) {
                // Handle complex conditions with operators
                $operator = isset($condition['operator']) ? strtoupper($condition['operator']) : '=';
                $value = isset($condition['value']) ? $condition['value'] : null;
                
                // Validate operator
                $allowed_operators = ['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN'];
                if (!in_array($operator, $allowed_operators)) {
                    continue;
                }
                
                if ($operator === 'IN' || $operator === 'NOT IN') {
                    if (!is_array($value) || empty($value)) {
                        continue;
                    }
                    
                    $placeholders = array_fill(0, count($value), '%s');
                    $where_parts[] = "`$column` $operator (" . implode(',', $placeholders) . ")";
                    $values = array_merge($values, $value);
                    
                } elseif ($operator === 'BETWEEN' || $operator === 'NOT BETWEEN') {
                    if (!is_array($value) || count($value) !== 2) {
                        continue;
                    }
                    
                    $where_parts[] = "`$column` $operator %s AND %s";
                    $values[] = $value[0];
                    $values[] = $value[1];
                    
                } else {
                    $where_parts[] = "`$column` $operator %s";
                    $values[] = $value;
                }
                
            } else {
                // Simple equality condition
                $where_parts[] = "`$column` = %s";
                $values[] = $condition;
            }
        }
        
        $where_clause = !empty($where_parts) ? ' WHERE ' . implode(' AND ', $where_parts) : '';
        
        return [
            'clause' => $where_clause,
            'values' => $values
        ];
    }
    
    /**
     * Safely executes a SELECT query with proper preparation.
     * 
     * @since 1.0.0
     * 
     * @param string $table   Table name
     * @param string $columns Columns to select
     * @param array  $where   Where conditions
     * @param string $orderby Column to order by
     * @param string $order   Order direction (ASC or DESC)
     * @param int    $limit   Number of rows to limit
     * @param int    $offset  Offset for pagination
     * @return array|object|null Query results
     */
    public static function secure_select($table, $columns = '*', $where = [], $orderby = null, $order = 'ASC', $limit = null, $offset = null) {
        global $wpdb;
        
        // Sanitize table name
        $table = self::sanitize_table_name($table);
        if (empty($table)) {
            return null;
        }
        
        // Sanitize columns
        $columns = self::sanitize_column_name($columns);
        if (empty($columns)) {
            return null;
        }
        
        // Build query parts
        $query = "SELECT $columns FROM `$table`";
        
        // Add WHERE clause if conditions exist
        $values = [];
        if (!empty($where)) {
            $where_parts = self::build_where_clause($where, $wpdb);
            $query .= $where_parts['clause'];
            $values = $where_parts['values'];
        }
        
        // Add ORDER BY if specified
        if (!empty($orderby)) {
            $sanitized_orderby = self::sanitize_column_name($orderby);
            $sanitized_order = in_array(strtoupper($order), ['ASC', 'DESC']) ? strtoupper($order) : 'ASC';
            
            if (!empty($sanitized_orderby)) {
                $query .= " ORDER BY `$sanitized_orderby` $sanitized_order";
            }
        }
        
        // Add LIMIT and OFFSET if specified
        if (!empty($limit) && is_numeric($limit)) {
            $query .= " LIMIT %d";
            $values[] = (int) $limit;
            
            if (!empty($offset) && is_numeric($offset)) {
                $query .= " OFFSET %d";
                $values[] = (int) $offset;
            }
        }
        
        // Prepare and execute the query
        if (!empty($values)) {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Query is dynamically built with sanitized input and properly prepared below
            $prepared_query = $wpdb->prepare($query, ...$values);
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- $prepared_query is the result of $wpdb->prepare() above. Caching is implemented at the application level by caller methods.
            return $wpdb->get_results($prepared_query);
        } else {
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Query is built with sanitized inputs from sanitize_table_name and sanitize_column_name methods. Caching is implemented at the application level by caller methods.
            return $wpdb->get_results($query);
        }
    }
    
    /**
     * Sanitizes and validates email addresses
     * 
     * @since 1.0.0
     * 
     * @param string|array $emails Email or array of emails to validate
     * @param bool $verify_exists Whether to verify if email belongs to a registered user
     * @return array Array of valid emails
     */
    public static function validate_emails($emails, $verify_exists = true) {
        $valid_emails = [];
        
        if (!is_array($emails)) {
            $emails = [$emails];
        }
        
        foreach ($emails as $email) {
            // Sanitize the email
            $email = sanitize_email($email);
            
            // Validate email format
            if (!is_email($email)) {
                continue;
            }
            
            // Optionally verify if email belongs to a registered user
            if ($verify_exists && !get_user_by('email', $email)) {
                continue;
            }
            
            $valid_emails[] = $email;
        }
        
        return $valid_emails;
    }
    
    /**
     * Get users by email addresses using WordPress core functions and proper caching
     * 
     * @since 1.0.0
     * 
     * @param array  $emails     Array of email addresses
     * @param string $cache_key  Optional cache key
     * @param int    $expiration Cache expiration in seconds (default 6 hours)
     * @return array Array of user data objects
     */
    public static function get_users_by_emails($emails, $cache_key = '', $expiration = 21600) {
        // Sanitize and validate emails
        $sanitized_emails = self::validate_emails($emails, false);
        
        if (empty($sanitized_emails)) {
            return [];
        }
        
        // Generate cache key if not provided
        if (empty($cache_key)) {
            $cache_key = 'ajl_users_by_emails_' . md5(implode(',', $sanitized_emails));
        }
        
        // Check cache first
        $users = wp_cache_get($cache_key, 'ajl_users');
        if (false !== $users) {
            return $users;
        }
        
        // Build query args for get_users() function
        $users = [];
        
        // Get users using WordPress core functions
        $wp_users = get_users([
            'fields' => ['ID', 'display_name', 'user_nicename', 'user_email'],
            'number' => -1
        ]);
        
        // Filter results to match our emails
        foreach ($wp_users as $user) {
            if (in_array($user->user_email, $sanitized_emails, true)) {
                $users[] = (object) [
                    'ID' => $user->ID,
                    'display_name' => $user->display_name,
                    'user_nicename' => $user->user_nicename,
                    'user_email' => $user->user_email
                ];
            }
        }
        
        // Cache the results
        if (!empty($users)) {
            wp_cache_set($cache_key, $users, 'ajl_users', $expiration);
        }
        
        return $users;
    }
}
