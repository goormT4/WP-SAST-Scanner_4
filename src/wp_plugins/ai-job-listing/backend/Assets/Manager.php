<?php

namespace Axilweb\AiJobListing\Assets;
// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Asset Manager class.
 *
 * Responsible for managing all of the assets (CSS, JS, Images, Locales).
 */
class Manager
{

    const PREFIX = 'axilweb_ajl_';

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('init',                     [$this, 'axilweb_ajl_register_all_scripts']);
        add_action('admin_enqueue_scripts',    [$this, 'axilweb_ajl_enqueue_admin_assets']);
        add_action('wp_enqueue_scripts',       [$this, 'axilweb_ajl_enqueue_job_list_assets'], 99);
        add_action('admin_enqueue_scripts',    [$this, 'axilweb_ajl_enqueue_media_scripts'], 99);
    }

    /**
     * Enqueue wp media script
     */
    public function axilweb_ajl_enqueue_media_scripts()
    {
        wp_enqueue_media();
    }

    /**
     * Register all scripts and styles.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function axilweb_ajl_register_all_scripts()
    {
        //admin
        $this->axilweb_ajl_register_styles($this->axilweb_ajl_get_styles());
        $this->axilweb_ajl_register_scripts($this->axilweb_ajl_get_scripts());
        // job list page
        $this->axilweb_ajl_register_styles($this->axilweb_ajl_get_job_list_styles());
        $this->axilweb_ajl_register_scripts($this->axilweb_ajl_get_job_list_scripts());
    }

    /**
     * Get all styles.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function axilweb_ajl_get_styles(): array
    {
        return [
            'ai_job_listing-css' => [
                'src'     => AXILWEB_AJL_BUILD . '/index.css',
                'version' => AXILWEB_AJL_VERSION,
                'deps'    => [],
            ],
            'ai_job_listing-admin-custom-css' => [
                'src'     => AXILWEB_AJL_ASSETS . '/css/admin-custom-style.css',
                'version' => AXILWEB_AJL_VERSION,
                'deps'    => [],
            ],
        ];
    }

    /**
     * Get all styles.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function axilweb_ajl_get_job_list_styles(): array
    {
        return [
            'ai_job_listing-list-css' => [
                'src'     => AXILWEB_AJL_BUILD . '/ai_job_listing.css',
                'version' => AXILWEB_AJL_VERSION,
                'deps'    => [],
            ],
        ];
    }

    /**
     * Get all scripts.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function axilweb_ajl_get_scripts(): array
    {
        $dependency = require_once AXILWEB_AJL_DIR . '/build/index.asset.php';

        return [
            'ai_job_listing-app' => [
                'src'       => AXILWEB_AJL_BUILD . '/index.js',
                'version'   => $dependency['version'],
                'deps'      => $dependency['dependencies'],
                'in_footer' => true,
                'strategy'  => 'defer', // Add defer attribute for better performance
            ],
        ];
    }

    /**
     * Get all scripts.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function axilweb_ajl_get_job_list_scripts(): array
    {
        $dependency = require_once AXILWEB_AJL_DIR . '/build/ai_job_listing.asset.php';

        return [
            'ai_job_listing-list-app' => [
                'src'       => AXILWEB_AJL_BUILD . '/ai_job_listing.js',
                'version'   => $dependency['version'],
                'deps'      => $dependency['dependencies'],
                'in_footer' => true,
                'strategy'  => 'defer', // Add defer attribute for better performance
            ],
        ];
    }
    /**
     * Register styles.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function axilweb_ajl_register_styles(array $styles)
    {
        foreach ($styles as $handle => $style) {
            wp_register_style($handle, $style['src'], $style['deps'], $style['version']);
        }
    }

    /**
     * Register scripts with proper attributes.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function axilweb_ajl_register_scripts(array $scripts)
    {
        foreach ($scripts as $handle => $script) {
            wp_register_script(
                $handle,
                $script['src'],
                $script['deps'],
                $script['version'],
                $script['in_footer']
            );

            // Add script loading strategy if specified (defer/async)
            if (!empty($script['strategy'])) {
                wp_script_add_data($handle, 'strategy', $script['strategy']);
            }

            // Localize script with any needed data
            if (!empty($script['localize'])) {
                foreach ($script['localize'] as $object_name => $data) {
                    wp_localize_script($handle, $object_name, $data);
                }
            }
        }
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since 1.0.0
     * @since 1.0.0 Loads the JS and CSS only on the ai_job_listing job admin page.
     *
     * @return void
     */
    public function axilweb_ajl_enqueue_admin_assets($hook)
    {
        // Check if we are on the admin page and page=ai_job_listingjob.
        if (! is_admin()) {
            return;
        }
        wp_enqueue_style('ai_job_listing-admin-custom-css');
        if (strpos($hook, 'ai-job-listing') === false) {
            return;
        }
        wp_enqueue_style('ai_job_listing-css'); 
        wp_enqueue_script('ai_job_listing-app');
    }

    /**
     * Enqueue admin styles and scripts.
     *
     * @since 1.0.0
     * @since 1.0.0 Loads the JS and CSS only on the ai_job_listing job admin page.
     *
     * @return void
     */
    public function axilweb_ajl_enqueue_job_list_assets()
    {

        if (is_admin()) {
            return;
        }
        $page_id = (int) get_option(self::PREFIX . 'career_page');

        if ("" !== $page_id) {
            wp_enqueue_style('ai_job_listing-list-css');
            wp_enqueue_script('ai_job_listing-list-app');
        }
    }
}
