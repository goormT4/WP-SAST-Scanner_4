<?php
	$csv = file_get_contents( get_attached_file($csv_file_id) );
	
	$csv_rows = explode( "\n", $csv );
	$total_rows = count( $csv_rows );
	$title_row = $csv_rows[ 0 ];
	update_option( 'eppro_total_row',$total_rows-1);	
	update_option( 'eppro_current_row','1');	
	$title_row_array= explode(",",$title_row);
	$main_fields =array('id','post_title','post_content','category','tag','featured-image','image_gallery_urls','job_type', 'vacancy','salary','gender','job_level','educational_requirements','job_must_have','deadline','job_education','other_benefits','listing_contact_source','company_name', 'address','local-area','latitude','longitude','city','postcode','state','country','phone','contact-email','contact_web','youtube');
	$maping='';
	$default_fields = array();
	$field_set=get_option('jobboard_fields' );
	if($field_set!=""){ 
		$default_fields=get_option('jobboard_fields' );
		}else{															
		$default_fields['other_link']=esc_html__('Other Link','jobboard');
	}
	if(sizeof($default_fields )){			
		foreach( $default_fields as $field_key => $field_value ) { 
			array_push($main_fields, $field_key);
		}					
	}
	$i=0;
	$maping=$maping.'<form id="csv_maping" name="csv_maping" ><table class="table  table-striped">
	<thead>
    <tr>    
	<th>'.esc_html__('Post Field/Map to field', 'jobboard' ).'</th>
	<th>'.esc_html__('CSV Column Title/Name', 'jobboard' ).'</th>      
    </tr>
	</thead>';
	foreach($title_row_array as $one_col){
		$sel_name= str_replace (' ','-', $one_col);
		$maping=$maping.'<tr><td><select name="'.trim($sel_name).'">';
		$maping=$maping.'<option value="">'.esc_html__('Email', 'jobboard' ).'</option>';
		$ii=0;
		foreach($main_fields as $main_one){		
			$maping=$maping.'<option value="'.esc_attr($main_one).'" '.($i==$ii?' selected':"").'>'.esc_html($main_one).'</option>';		
			$ii++;
		}	
		$maping=$maping.'</select></td>';
		$maping=$maping.'<td>'.$one_col.'<input type="hidden" name="column'.$i.'" value="'.esc_attr($one_col).'"></td>';
		$maping=$maping.'</tr>';	
		$i++;	
	}
	$maping=$maping.'</table></form>';
?>