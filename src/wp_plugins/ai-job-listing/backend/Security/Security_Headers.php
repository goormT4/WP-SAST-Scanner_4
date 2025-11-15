<?php
namespace Axilweb\AiJobListing\Security;
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Security Headers Manager
 */
class Security_Headers {
    /**
     * Initialize security headers
     */
    public function init() {
        add_action('send_headers', [$this, 'add_security_headers']);
        add_filter('rest_pre_serve_request', [$this, 'add_rest_security_headers'], 10, 4);
    }

    /**
     * Generate a cryptographically secure nonce
     *
     * @return string
     */
    private function generate_nonce() {
        return bin2hex(random_bytes(16));
    }

    /**
     * Add security headers to all responses
     */
    public function add_security_headers() {
        // Prevent Clickjacking and XSS
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Enhanced Referrer Policy for privacy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // More comprehensive Content Security Policy
        $csp = [
            "default-src 'self'",
            "script-src 'self'",  // Removed 'unsafe-inline' and 'unsafe-eval' for better security
            "style-src 'self' 'unsafe-inline'",  // Kept 'unsafe-inline' as it's often needed for WordPress
            "img-src 'self' data:",  // Removed https: to prevent mixed content
            "font-src 'self' data:",
            "connect-src 'self'",  // Removed https: to prevent mixed content
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'"
        ];

        // Use Report-Only mode initially to monitor without breaking functionality
        header("Content-Security-Policy-Report-Only: " . implode('; ', $csp));
        
        // Add actual Content-Security-Policy header
        header("Content-Security-Policy: " . implode('; ', $csp));

        // Strict Transport Security (HSTS) - only for HTTPS sites
        if (is_ssl()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }

    /**
     * Add security headers to REST API responses
     *
     * @param bool $served Whether the request has already been served
     * @param WP_HTTP_Response $result Result to send to the client
     * @param WP_REST_Request $request Request used to generate the response
     * @param WP_REST_Server $server Server instance
     * @return bool Whether the request has been served
     */
    public function add_rest_security_headers($served, $result, $request, $server) {
        $this->add_security_headers();
        
        // Add CORS headers for REST API
        $origin = get_http_origin();
        $allowed_origins = apply_filters('ai_job_listing_allowed_origins', [
            home_url(), 
            site_url(),
            // Add any additional allowed origins here
        ]);

        if ($origin) {
            // Allow configured origins
            if (in_array($origin, $allowed_origins, true)) {
                header('Access-Control-Allow-Origin: ' . esc_url_raw($origin));
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
                
                // Get and sanitize request method following WP core standards
                $request_method = '';
                if (isset($_SERVER['REQUEST_METHOD'])) {
                    $request_method = strtoupper(sanitize_key(wp_unslash($_SERVER['REQUEST_METHOD'])));
                }
                
                if ('OPTIONS' === $request_method) {
                    status_header(200);
                    exit();
                }
            }
        }
        
        return $served;
    }
}
