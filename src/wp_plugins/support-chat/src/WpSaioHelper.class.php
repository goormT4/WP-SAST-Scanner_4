<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WpSaioHelper {

    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function sanitize_array($var) {
        if (is_array($var)) {
            return array_map('self::sanitize_array', $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }
    public static function format_content($app, $content, $append_prefix = false) {
        if( $app === 'viber') {
            if( strpos($content, 'vb.me') !== false || strpos($content, 'viber://pa') !== false ) {
                return $content;
            }
        }
        $app_prefix = array(
            'messenger' => 'https://m.me/',
            'facebook-messenger' => 'https://m.me/',
            'whatsapp' => 'https://wa.me/',
            'snapchat' => 'https://www.snapchat.com/add/',
            'line' => 'https://line.me/ti/p/',
            'viber' => 'viber://chat?number=',
            'phone' => 'tel:',
            'email' => 'mailto:',
            'telegram' => 'https://t.me/',
            'zalo' => 'https://zalo.me/',
            'kakaotalk' => 'kakaotalk://open/chat/',
            'wechat' => 'weixin://dl/chat?',
        );
        if( ! isset($app_prefix[$app]) ) {
            return $content;
        }
        if( $app === 'viber' && !wp_is_mobile() ) {
            $content = str_replace('viber://add?number=', '', $content);
            $app_prefix[$app] = 'http://chats.viber.com/';
        }
        $content = str_replace($app_prefix[$app], '', $content);
        return $append_prefix ? $app_prefix[$app] . $content : $content;
    }
    public static function get_pages() {
        return get_pages();
    }
}