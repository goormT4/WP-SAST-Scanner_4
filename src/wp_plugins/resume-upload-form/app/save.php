<?php

/**
* Save user data
*/
function saveResume() 
{   
    if ( isset( $_POST['cf-submitted'] ) ) {       
        
        global $wpdb;       
        
        $table = $wpdb->prefix . "user_info";       
                
        $where = (get_current_user_id() > 0) ? 
                    " WHERE user_id= '" . get_current_user_id() . "'" : 
                    " WHERE email= '" . sanitize_email( $_POST["email"] ) . "'";    
        
        $sql = "SELECT id, resume_path FROM " . $table . $where;
        $record = $wpdb->get_row($sql);                    
                
        // sanitize form values
        $fname      = sanitize_text_field( $_POST["fname"] );
        $lname      = sanitize_text_field( $_POST["lname"] );
        $email      = sanitize_email( $_POST["email"] );                
        $phone      = sanitize_text_field( $_POST["phone"] );                
        $comments   = sanitize_text_field( $_POST["comments"] );
        $exp        = sanitize_text_field( $_POST["exp"] );
        $send       = isset($_POST["cf-send-email"]) ? $_POST["cf-send-email"] : 'off'; 
        
        // Upload File
        if (!function_exists('wp_handle_upload')) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $uploadedfile = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );        

        if ( $movefile && !isset( $movefile['error'] ) ) {  
                  
            $data = array(
                "name" => $fname.' '.$lname, 
                "email" => $email, 
                "phone" => $phone, 
                "user_id" => get_current_user_id(), 
                'resume_path' => strstr($movefile['url'], 'wp-content'),
                'comments' => $comments,
                'experience' => $exp        
            );
         
            if (isset($record->id) > 0) {     

                // Deleting already saved resume of this user
                if(file_exists($record->resume_path)){
                    unlink($record->resume_path);
                }

                // Updating table
                $where = array( "user_id" => get_current_user_id() );
                if(get_current_user_id() == 0){
                    $where = array( "email" => $email);
                }
                
                $wpdb->update( $table, $data, $where, $format=null, $where_format=null);
            } else {
                $wpdb->insert($table, $data);
                $wpdb->insert_id;                
            }                   
                       
            if($send == 'on') {     

                $subject = "Application Received"; 
                
                $headers  = "From: ".get_bloginfo('admin_email')."\r\n";                
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                $headers .= "X-Priority: 1\r\n"; 

                $message = '<html><body>';
                $message .= '<p>Hello '.$fname.',</p>';
                $message .= '<p>Thank you for your application and interest to work in our company. we will contact you after reviewed your resume.</p>';
                $message .= '<p>&nbsp;</p><p>&nbsp;</p>';                
                $message .= '<p>Best Regards,</p>';        
                $message .= '</body></html>';
                
                $user = get_user_by( 'email', get_bloginfo('admin_email'));
                $message .= $user->first_name.' '.$user->last_name; 

                if(!@mail($email,$subject,$message,$headers)) {
                    return "<div class='error-style'>Email not sent!</div>";
                } 
            }
        } else {
            return "<span class='error-style'>".$movefile['error']."</span>";
        }  
        
        return "<span class='success-style'>Resume has been uploaded successfully!</span>";
    }
}