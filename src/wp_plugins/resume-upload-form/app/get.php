<?php

/**
* Get list of users
*/
function getResumeList() 
{  
    global $wpdb;    

    $table = $wpdb->prefix."user_info";
    $sql = "SELECT * FROM ".$wpdb->prefix."user_info";
    $results = $wpdb->get_results($sql);    

    if(count($results) > 0) { 
        $output = '';
        $output .= '<div class="container">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Experience</th>
                                        <th>Resume</th>
                                    </tr>
                                </thead>
                                <tbody>';                                
        
        foreach ($results as $row ) {               
            $output .= '<tr>';
            $output .= '<td>'.$row->name.'</td>';
            $output .= '<td>'.$row->email.'</td>';
            $output .= '<td>'.$row->phone.'</td>';            
            $output .= ($row->experience < 1) ? '<td> No </td>' : 
                            '<td>'.$row->experience.' Year(s)</td>';
            $output .= '<td>
                            <a href="'.$row->resume_path.'" target="_blank">'
                                .basename($row->resume_path).
                            '</a>
                        </td>';
            $output .= '</tr>';
        }
                                    
        $output .= '</tbody>
                </table>
            </div>
        </div>';

        return $output;
    }
}