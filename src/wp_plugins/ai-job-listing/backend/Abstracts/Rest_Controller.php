<?php

namespace Axilweb\AiJobListing\Abstracts;

use Axilweb\AiJobListing\Security\Rate_Limiter;
use Axilweb\AiJobListing\Security\Security_Headers;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Rest Controller base class.
 *
 * @since 0.1.0
 */
abstract class Rest_Controller extends WP_REST_Controller
{
    public function __construct()
    {
        (new Security_Headers)->init();
    }

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'ai_job_listing/v1';



    /**
     * Validate request nonce
     *
     * @param WP_REST_Request $request Request object
     * @return true|WP_Error True if valid, WP_Error if invalid
     */
    protected function validate_nonce($request)
    {
        // Skip nonce validation for GET requests
        if ($request->get_method() === 'GET') {
            return true;
        }

        $nonce = $request->get_header('X-WP-Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_Error(
                'rest_invalid_nonce',
                __('Invalid nonce', 'ai-job-listing'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Check permissions for the request
     *
     * @param WP_REST_Request $request Request object
     * @return true|WP_Error True if has permission, WP_Error if not
     */
    public function check_permission($request)
    {
        // Validate nonce
        $nonce_check = $this->validate_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
    
        // Check if user has required capability
        if (!current_user_can('manage_options')) {
            /* translators: %s: HTTP method action such as "create posts" or "update settings" */
           
            $error_message = sprintf(__('Sorry, you are not allowed to %s.', 'ai-job-listing'), $this->get_permission_message($request->get_method()));
            
            return new WP_Error(
                'rest_forbidden',
                $error_message,
                ['status' => rest_authorization_required_code()]
            );
        }
    
        return true;
    }
    
    /**
     * Get permission error message based on method
     *
     * @param string $method HTTP method
     * @return string Error message
     */
    protected function get_permission_message($method)
    {
        $messages = [
            'GET' => __('view this content', 'ai-job-listing'),
            'POST' => __('create content', 'ai-job-listing'),
            'PUT' => __('update content', 'ai-job-listing'),
            'PATCH' => __('update content', 'ai-job-listing'),
            'DELETE' => __('delete content', 'ai-job-listing')
        ];

        return $messages[$method] ?? __('perform this action', 'ai-job-listing');
    }

    /**
     * Format collection response with pagination
     *
     * @param WP_REST_Response $response Response object
     * @param WP_REST_Request $request Request object
     * @param int $total_items Total number of items
     * @return WP_REST_Response Modified response
     */
    public function format_collection_response($response, $request, $total_items)
    {
        if ($total_items === 0) {
            return $response;
        }

        // Pagination values for headers
        $per_page = (int) (!empty($request['per_page']) ? $request['per_page'] : 20);
        $page = (int) (!empty($request['page']) ? $request['page'] : 1);

        $response->header('X-WP-Total', (int) $total_items);

        $max_pages = ceil($total_items / $per_page);

        $response->header('X-WP-TotalPages', (int) $max_pages);
        $base = add_query_arg($request->get_query_params(), rest_url(sprintf('/%s/%s', $this->namespace, $this->base)));

        if ($page > 1) {
            $prev_page = $page - 1;
            if ($prev_page > $max_pages) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg('page', $prev_page, $base);
            $response->link_header('prev', $prev_link);
        }

        if ($max_pages > $page) {
            $next_page = $page + 1;
            $next_link = add_query_arg('page', $next_page, $base);
            $response->link_header('next', $next_link);
        }

        return $response;
    }
}
