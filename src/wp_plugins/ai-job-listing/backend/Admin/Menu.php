<?php

namespace Axilweb\AiJobListing\Admin;

use Axilweb\AiJobListing\Helpers\Helpers;

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Admin Menu class.
 *
 * Responsible for managing admin menus.
 */
class Menu
{

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        // Add an action hook to initialize the menu when the admin menu is being constructed.
        add_action('admin_menu', [$this, 'init_menu']);
    }

    /**
     * Outputs custom inline CSS styles for the admin menu.
     *
     * This method generates and outputs custom inline styles specifically targeting the AI Job Listing plugin's admin menu item.
     * The styles modify the appearance of the plugin's menu icon by adjusting padding, opacity, and width of the icon image.
     * This ensures that the plugin's admin menu item appears with the desired styling.
     *
     * @return void
     *
     * @since 1.0.0
     */


    /**
     * Initialize the Admin Menu
     */
    public function init_menu()
    {
        // Your main menu config
        $slug          = AXILWEB_AJL_SLUG;
        $menu_position = 30;
        $capability    = 'manage_options';
        $logo_icon     = AXILWEB_AJL_ASSETS . '/images/joblisting.png';

        // Add the main menu page
        add_menu_page(
            esc_attr__('AI Job Listing', 'ai-job-listing'),
            esc_attr__('AI Job Listing', 'ai-job-listing'),
            $capability,
            $slug,
            [$this, 'plugin_page'],
            $logo_icon,
            $menu_position
        );
 
        
        // Add the submenu pages
        global $submenu;

        // Initialize the submenu array for the main menu
        $submenu[$slug][] = [
            esc_attr__('Welcome', 'ai-job-listing'),
            $capability,
            'admin.php?page=' . $slug,
        ];
        // Initialize the submenu array for the main menu
        $submenu[$slug][] = [
            esc_attr__('Job List', 'ai-job-listing'),
            $capability,
            'admin.php?page=' . $slug . '#/job-list',
        ];

        $submenu[$slug][] = [
            esc_attr__('Applicants', 'ai-job-listing'),
            $capability,
            'admin.php?page=' . $slug . '#/applicants',
        ];

        $submenu[$slug][] = [
            esc_attr__('Settings', 'ai-job-listing'),
            $capability,
            'admin.php?page=' . $slug . '#/settings',
        ];

        // Get the career page ID from options
        $career_page_id = get_option('axilweb_ajl_career_page');

        if ($career_page_id) {
            $submenu[$slug][] = [
                esc_attr__('Live Site', 'ai-job-listing'),
                $capability,
                esc_url(get_permalink($career_page_id))
            ];
        }
    }

    /**
     * Redirects the user to the job listing page in the admin dashboard.
     *
     * This method performs a redirect to the AI Job Listing's job listing page in the WordPress admin panel.
     * It uses `wp_redirect` to navigate the user to the specified URL and ensures the redirect happens immediately
     * by calling `exit` after the redirection.
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function redirect_to_job_list()
    {
        // Define the URL you want to redirect to
        $redirect_url = admin_url('admin.php?page=ai-job-list#/job/list');
        $redirect_url = wp_nonce_url($redirect_url, 'redirect_job_list_nonce');

        // Use wp_redirect to perform the redirection
        wp_redirect($redirect_url);

        // Ensure the redirection happens immediately
        exit;
    }

    /**
     * Render the plugin page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function plugin_page()
    {
        // Include the template file to render the plugin page.
        require_once AXILWEB_AJL_TEMPLATE_PATH . '/app.php';
    }
}
