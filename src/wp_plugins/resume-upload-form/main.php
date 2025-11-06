<?php

/**
* Adding style
*/
add_action('wp_enqueue_scripts', 'style_setting');
function style_setting() 
{    
    global $css_path;
   
    wp_enqueue_style( 'custom-style', $css_path.'custom-style.css');
    wp_enqueue_style( 'bootstrap', $css_path.'bootstrap.min.css' );
}

/**
* Adding script
*/
add_action('wp_enqueue_scripts', 'script_setting');
function script_setting() {
    
    global $js_path, $helper_path;

    wp_enqueue_script('my_jquery', $js_path.'jquery.min.js');
    wp_enqueue_script('bootstrap', $js_path.'bootstrap.min.js');
    wp_enqueue_script('my_jvalidate', $js_path.'jquery.validate.min.js');
    wp_enqueue_script('my_additionalvalidate', $js_path.'additional-methods.js');

    /* Code to be optimized */
    wp_register_script( 'my-script', '');
    wp_enqueue_script( 'my-script' );

    $translation = [
        'processUrl' => $helper_path.'captcha/process.php', 
        'captchaUrl' =>  $helper_path.'captcha/captcha.php'
    ];    
    wp_localize_script( 'my-script', 'script_name', $translation );
    wp_enqueue_script('my_jcustom', $js_path.'custom.js');
}

/**
* Loading data views and save it
*/
Require_once plugin_dir_path( __FILE__ ). "views/resume.php";
Require_once plugin_dir_path( __FILE__ ). "app/save.php";
Require_once plugin_dir_path( __FILE__ ). "app/get.php";

/**
* Shortcode form
*/
function cf_shortcode() 
{    
    ob_start();    
    echo saveResume();
    formResume();    
    return ob_get_clean();
}
add_shortcode( 'resume_upload_form', 'cf_shortcode' );

/**
* Shortcode table
*/
function list_shortcode() 
{    
    ob_start();
    echo getResumeList();
    return ob_get_clean();
}
add_shortcode( 'resume_upload_form_list', 'list_shortcode' );