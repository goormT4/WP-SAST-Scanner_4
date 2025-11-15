<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WpSaioAjax
{
    private static $_instance = null;

    public function __construct()
    {
        add_action('wp_ajax_wpsaio_choose_apps_settings', array($this, 'set_choose_apps_settings'));
        add_action('wp_ajax_wpsaio_design_settings', array($this, 'set_design_settings'));
        add_action('wp_ajax_wpsaio_display_settings', array($this, 'set_display_settings'));
        add_action('wp_ajax_wpsaio_review_tracked', array($this, 'track_review'));
    }

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function set_choose_apps_settings()
    {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( ! wp_verify_nonce( $nonce, 'wpsaio_nonce' ) ) {
            die('Permission Denied.');
        }
        //check manage_options capability
        if (!current_user_can('manage_options')) {
            die('Permission Denied.');
        }
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via WpSaioHelper::sanitize_array()
        $form_data = isset( $_POST['data']['formDataArray'] ) ? WpSaioHelper::sanitize_array( wp_unslash( $_POST['data']['formDataArray'] ) ) : [];
        $data = [];
        
        foreach ($form_data as $app) {
            $data[$app['name']]['params'] = [
                $app['key'] => $app['value'],
                'state' => $app['state'],
                'custom-app-title' => $app['customAppTitle'],
                'url-icon' => $app['urlIcon'],
                'color-icon' => $app['colorIcon']
            ];
        }
        $default_apps = WpSaio::getMessagingApps();
        $custom_apps = [];
        foreach ($data as $key => $value) {
            if ( !in_array($key, array_keys($default_apps))) {
                $replace_key = str_replace('-', '_', $key);
                $custom_app = [];
                $custom_app[$key]['icon'] = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M344.476 105.328L1.004 448.799 64.205 512l343.472-343.471-63.201-63.201zm-53.882 96.464l53.882-53.882 20.619 20.619-53.882 53.882-20.619-20.619zM410.885 78.818l37.657-37.656 21.29 21.29-37.656 37.657zM405.99 274.144l21.29-21.29 38.367 38.366-21.29 21.29zM198.501 66.642l21.29-21.29 38.13 38.127-21.292 21.291zM510.735 163.868h-54.289v30.111H510.996v-30.111zM317.017.018v54.289h30.111V0z"/></svg>';
                $custom_app[$key]['title'] =  isset($data[$key]['params']['custom-app-title']) ? $data[$key]['params']['custom-app-title'] : $key;
                $custom_app[$key]['shortcode'] =  "wpsaio_$replace_key";
                $custom_app[$key]['params']['url'] =  $default_apps['custom-app']['params']['url'];
                $custom_apps[$key] = $custom_app[$key];
            }
        }

        //add check here remove those elements in $default_apps which do not exist in $custom_apps
        foreach (array_keys($default_apps) as $default_app_key) {
            if (str_contains($default_app_key, 'custom-app-') && !array_key_exists($default_app_key, $data)) {
                unset($default_apps[$default_app_key]);
            }
        }
        // print_r(array_merge($default_apps, $custom_apps));

        update_option('njt_wp_saio_default_apps', array_merge($default_apps, $custom_apps));
        update_option('njt_wp_saio', $data);
        return true;
    }

    public function set_design_settings()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if (!wp_verify_nonce( $nonce, 'wpsaio_nonce')) {
            die('Permission Denied.');
        }
        //check manage_options capability
        if (!current_user_can('manage_options')) {
            die('Permission Denied.');
        }

        $enable_plugin = isset( $_POST['data']['enablePlugin'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['enablePlugin'] ) ) : '';
        $style = isset( $_POST['data']['style'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['style'] ) ) : '';
        $tooltip = isset( $_POST['data']['tooltip'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['tooltip'] ) ) : '';
        $widget_position = isset( $_POST['data']['widgetPosition'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['widgetPosition'] ) ) : '';
        $bottom_distance = isset( $_POST['data']['bottomDistance'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['bottomDistance'] ) ) : '';
        $button_icon = isset( $_POST['data']['buttonIcon'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['buttonIcon'] ) ) : '';
        $button_image = isset( $_POST['data']['buttonImage'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['buttonImage'] ) ) : '';
        $button_color = isset( $_POST['data']['buttonColor'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['buttonColor'] ) ) : '';

        update_option('wpsaio_enable_plugin', $enable_plugin);
        update_option('wpsaio_style', $style);
        update_option('wpsaio_tooltip', $tooltip);
        update_option('wpsaio_widget_position', $widget_position);
        update_option('wpsaio_bottom_distance', $bottom_distance);
        update_option('wpsaio_button_icon', $button_icon);
        update_option('wpsaio_button_image', $button_image);
        update_option('wpsaio_button_color', $button_color);

        return true;
    }

    public function set_display_settings()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if (!wp_verify_nonce( $nonce, 'wpsaio_nonce')) {
            die('Permission Denied.');
        }
        //check manage_options capability
        if (!current_user_can('manage_options')) {
            die('Permission Denied.');
        }

        $show_on_desktop = isset( $_POST['data']['showOnDesktop'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['showOnDesktop'] ) ) : '';
        $show_on_mobile = isset( $_POST['data']['showOnMobile'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['showOnMobile'] ) ) : '';
        $display_condition = isset( $_POST['data']['displayCondition'] ) ? sanitize_text_field( wp_unslash( $_POST['data']['displayCondition'] ) ) : '';
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via WpSaioHelper::sanitize_array()
        $includes_pages = isset( $_POST['data']['includePages'] ) ? WpSaioHelper::sanitize_array( wp_unslash( $_POST['data']['includePages'] ) ) : [];
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized via WpSaioHelper::sanitize_array()
        $excludes_pages = isset( $_POST['data']['excludePages'] ) ? WpSaioHelper::sanitize_array( wp_unslash( $_POST['data']['excludePages'] ) ) : [];

        if( ! is_array($includes_pages) ) {
            $includes_pages = explode(',', $includes_pages);
        }
        if( ! is_array($excludes_pages) ) {
            $excludes_pages = explode(',', $excludes_pages);
        }

        update_option('wpsaio_show_on_desktop', $show_on_desktop);
        update_option('wpsaio_show_on_mobile', $show_on_mobile);
        update_option('wpsaio_display_condition', $display_condition);
        update_option('wpsaio_includes_pages', $includes_pages);
        update_option('wpsaio_excludes_pages', $excludes_pages);

        return true;
    }
    public function track_review()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if (!wp_verify_nonce( $nonce, 'wpsaio_nonce')) {
            die('Permission Denied.');
        }
        update_option('wpsaio_review_tracked', '1');
        return true;
    }
}
