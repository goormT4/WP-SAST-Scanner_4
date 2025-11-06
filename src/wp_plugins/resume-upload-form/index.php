<?php 

/*
Plugin Name: Upload Resume
Description: Implemented to save and review the user information.
Version: 1.2.0
Author: mbbhatti
*/

$plugin_url = trailingslashit( WP_PLUGIN_URL.'/'.dirname( plugin_basename(__FILE__)) );
$helper_path = $plugin_url.'helpers/';
$css_path = $plugin_url.'public/css/';
$js_path = $plugin_url.'public/js/';

register_activation_hook(__FILE__,'resume_activate');
function resume_activate () 
{        
    global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table = $wpdb->prefix . "user_info";	
	array_push($wpdb->tables,$table); 		
     
    if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) { 
		
		$sql = "CREATE TABLE $table (
			      id int(11) NOT NULL AUTO_INCREMENT,			      
			      user_id  int(11),
			      name varchar(255),
			      email varchar(255),
			      phone varchar(100),
			      experience int(10),
			      resume_path varchar(255),
			      comments text,
			      UNIQUE KEY id (id)
			    );";        
 		
		dbDelta($sql);			
		
		if(get_option("db_table")){
        	update_option( "db_table_".$table, $table ); 
		}else{
		 	add_option("db_table_".$table, $table); 
		}
	}    
}

register_deactivation_hook( __FILE__, 'resume_deactivate');
function resume_deactivate()
{	
	global $wpdb;
	
	$table = $wpdb->prefix . "user_info";
	$wpdb->query("Drop table ".$table);
	array_pop($wpdb->tables);
	delete_option( "db_table_".$table);
}

include("main.php");
