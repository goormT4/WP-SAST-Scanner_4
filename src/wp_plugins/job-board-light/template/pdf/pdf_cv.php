<?php
	if(isset($_REQUEST['jobboardpdfcv'])){ 
		global $html_pdf;
		global $current_user;
		$pdfpost_id='';$footer_html='';$header='';
		$current_lang="en";
		$lang=get_bloginfo("language");
		$language_array= explode("-",$lang);
		if(isset($language_array[0])){
			$current_lang=$language_array[0];
		}
		ob_clean();
		require ( wp_jobboard_ABSPATH. 'inc/vendor/autoload.php');
		$user_id=1;
		if(isset($_REQUEST['jobboardpdfcv'])){
			$author_name= sanitize_text_field($_REQUEST['jobboardpdfcv']);
			$user = get_user_by( 'id', $author_name );
			if(isset($user->ID)){
				$user_id=$user->ID;
				$display_name=$user->display_name;
				$email=$user->user_email;
			}
		}	
	    $epfit_margin_left = '15';
		$epfit_margin_right ='15';
		$epfit_margin_top = '10';
		$epfit_margin_bottom = '30';
		$epfit_margin_header = '15';
		$mpdf_config = apply_filters('epfit_mpdf_config',[              
		'format'            => 'A4',
		'margin_left'       => $epfit_margin_left,
		'margin_right'      => $epfit_margin_right,
		'margin_top'        => $epfit_margin_top,
		'margin_bottom'     => $epfit_margin_bottom,
		'margin_header'     => $epfit_margin_header,  
			'fontdata' => [
				'frutiger' => [
					'R' => 'Roboto-Light.ttf',
					'I' => 'Roboto-LightItalic.ttf',
					'B' => 'Roboto-Bold.ttf',
					'BI' => 'Roboto-BoldItalic.ttf',
				]
			],
			'default_font' => 'Roboto'
		]);
		$mpdf = new \Mpdf\Mpdf( $mpdf_config );
		$footer_html='';
		if(isset($_REQUEST['jobboardpdfcv'])){ $pdfpost_id=sanitize_text_field($_REQUEST['jobboardpdfcv']); $postid=$pdfpost_id;}
		$postid=$pdfpost_id;	
		// Check access****************
		$has_access='yes';
		$current_userID= get_current_user_id();
		$post_author_id = get_post_field( 'post_author', $postid );
		$user_for=get_post_meta($postid,'report_for_user',true);
		if($current_userID ==$user_for || $current_userID==$post_author_id){
			$has_access='yes';
		}
		if(isset($current_user->roles[0]) and $current_user->roles[0]=='administrator'){
			$has_access='yes';					
		}
		
		
		//***********End Access Check****************
		$con_user=get_post_meta($postid,'report_for_user',true);
		$client_user = get_userdata((int)$con_user);
		$display_name='';
		if(isset($client_user->user_nicename)){$display_name=$client_user->user_nicename;}
		$name_display=get_user_meta($con_user,'first_name',true).' '.get_user_meta($con_user,'last_name',true);
		$footer_html=''.get_bloginfo();
		$iv_profile_pic_url=get_user_meta($postid, 'iv_profile_pic_thum',true);
		if($iv_profile_pic_url!=''){
				$iv_profile_pic_url= esc_url($iv_profile_pic_url);		
		}else{
				$iv_profile_pic_url= wp_jobboard_URLPATH.'assets/images/default-user.png';
		}
		$header = '	';
		$default_fields = array();
		$i=1;
		$html_pdf=$html_pdf.'<body style="font-family: Helvetica; font-size: 11pt;"><table  class="tableContainer" style="border-collapse: collapse;width:100%;"   ><tr>		
			<td scope="row" style="text-align: left;width:50%"><h2>'. get_user_meta($user_id,'full_name',true) .'</h2> <br/> '.$email.'<br/> Addresss<br/> '. esc_html__('Phone','jobboard').':'.get_user_meta($user_id,'phone',true).' </td>	
			<td scope="row" style="text-align: right;width:50%"><img height="150px" src="'.$iv_profile_pic_url.'"></td>	
			</tr></table>';
		$coverletter=get_user_meta($postid,'coverletter',true);
		$html_pdf=$html_pdf.'<table  class="tableContainer" style="border-collapse: collapse;width:100%;"   ><tr>		
			<td scope="row" style="text-align: left;width:100%"><h4>'. esc_html__('Objective','jobboard').'</h4><hr>'.$coverletter.'</td>
		</tr></table>';
		$html_pdf=$html_pdf.'<table  class="tableContainer" style="border-collapse: collapse;width:100%;margin-top:20px"   ><tr>		
			<td scope="row" style="text-align: left;width:100%"><h4>'. esc_html__('Work & Experince','jobboard').'</h4><hr></td>
		</tr>';
		 for($i=0;$i<30;$i++){
		  if(get_user_meta($postid,'experience_title'.$i,true)!=''){
			$html_pdf=$html_pdf.'	
			<tr>
			<td scope="row" style="text-align: left;width:100%"><p><b>'.get_user_meta($postid,'experience_title'.$i,true).'</b></p><p>
			'.get_user_meta($postid,'experience_company'.$i,true).', '.get_user_meta($postid,'experience_start'.$i,true).' - '.get_user_meta($postid,'experience_end'.$i,true) .'</p><p>'.get_user_meta($postid,'experience_description'.$i,true).'
			</p><br/></tr>';
		  }
		}
		$html_pdf=$html_pdf.'</table><table  class="tableContainer" style="border-collapse: collapse;width:100%;margin-top:20px"   ><tr>		
			<td scope="row" style="text-align: left;width:100%"><h4>'. esc_html__('Education','jobboard').'</h4><hr></td>
		</tr>';
		 for($i=0;$i<30;$i++){
		  if(get_user_meta($postid,'educationtitle'.$i,true)!=''){
			$html_pdf=$html_pdf.'	
			<tr>
			<td scope="row" style="text-align: left;width:100%"><p><b>'.get_user_meta($postid,'educationtitle'.$i,true).'</b></p><p>
			'.get_user_meta($postid,'institute'.$i,true).', '.get_user_meta($postid,'edustartdate'.$i,true).' - '.get_user_meta($postid,'eduenddate'.$i,true) .'</p><p>'.get_user_meta($postid,'edudescription'.$i,true).'
			</p><br/></tr>';
		  }
		}
		// Award**
		$html_pdf=$html_pdf.'</table><table  class="tableContainer" style="border-collapse: collapse;width:100%;margin-top:20px"   ><tr>		
			<td scope="row" style="text-align: left;width:100%"><h4>'. esc_html__('Honors & Awards','jobboard').'</h4><hr></td>
		</tr>';
		 for($i=0;$i<30;$i++){
		  if(get_user_meta($postid,'award_title'.$i,true)!=''){
			$html_pdf=$html_pdf.'	
			<tr>
			<td scope="row" style="text-align: left;width:100%"><p><b>'.get_user_meta($postid,'award_title'.$i,true).'</b></p><p>
			'.esc_html__('Year : ','jobboard').''.get_user_meta($postid,'award_year'.$i,true).' </p><p>'.get_user_meta($postid,'award_description'.$i,true).'
			</p><br/></tr>';
		  }
		}
		// Award**
		$html_pdf=$html_pdf.'</table><table  class="tableContainer" style="border-collapse: collapse;width:100%;margin-top:20px"   ><tr>		
			<td scope="row" style="text-align: left;width:100%"><h4>'. esc_html__('Languages','jobboard').'</h4><hr></td>
		</tr>';
		 for($i=0;$i<5;$i++){
		  if(get_user_meta($postid,'language'.$i,true)!=''){
			$html_pdf=$html_pdf.'	
			<tr>
			<td scope="row" style="text-align: left;width:100%"><p>'.get_user_meta($postid,'language'.$i,true).' : 
			'.get_user_meta($postid,'language_level'.$i,true).' </p><br/></tr>';
		  }
		}
		// Personal info
		$html_pdf=$html_pdf.'</table><table  class="tableContainer" style="border-collapse: collapse;width:100%;margin-top:20px"   ><tr>		
		<td scope="row" style="text-align: left;width:100%"><h4>'. esc_html__('Personal info','jobboard').'</h4><hr></td>
	</tr>';
		$default_fields = array();
			$field_set=get_option('jobboard_profile_fields' );
			$all_empty='no';
			if($field_set!=""){
				$default_fields=get_option('jobboard_profile_fields' );
				}else{
				$default_fields['full_name']='Full Name';	
				$default_fields['phone']='Phone Number';
				$default_fields['mobile']='Mobile Number';
				$default_fields['address']='Address';
				$default_fields['city']='City';
				$default_fields['zipcode']='Zipcode';
				$default_fields['state']='State';
				$default_fields['country']='Country';										
				$default_fields['job_title']='Job title';									
				$default_fields['hourly_rate']='Hourly Rate';
				$default_fields['experience']='Experience';
				$default_fields['age']='Age';
				$default_fields['qualification']='Qualification';								
				$default_fields['gender']='Gender';	
				$default_fields['website']='Website Url';
				$default_fields['description']='About';
				$all_empty='yes';
			}
			$field_type_roles=  	get_option( 'jobboard_field_type_roles' );			
			$myaccount_fields_array=  get_option( 'jobboard_myaccount_fields' );
			
			$not_show= array('description');			
			$user = new WP_User( $user_id);
			$i=1;
			foreach ( $default_fields as $field_key => $field_value ) { 
				$role_access='no';
				if(isset($myaccount_fields_array[$field_key])){ 
					if($myaccount_fields_array[$field_key]=='yes'){
						
						if(in_array('all',$field_type_roles[$field_key] )){
							$role_access='yes';
						}
						if(in_array('administrator',$field_type_roles[$field_key] )){
							$role_access='yes';
						}
						if(in_array('employer',$field_type_roles[$field_key] )){
							$role_access='yes';
						}
						if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
							foreach ( $user->roles as $role ){
								if(in_array($role,$field_type_roles[$field_key] )){
								$role_access='yes';
								}
								if('administrator'==$role){
									$role_access='yes';
								}
							}
						}	
						
					}
				}										
				if($role_access=='yes' OR $all_empty=='yes' ){
					if(!in_array($field_key,$not_show)){
						if(get_user_meta($user_id,$field_key,true)!=''){
							$html_pdf=$html_pdf.'	
							<tr>
							<td scope="row" style="text-align: left;width:100%"><p>'.$field_value.' : 
							'.esc_html( get_user_meta($user_id,$field_key,true)).' </p><br/></tr>';
							
																			
						}										
					}
				}
			}	
	
		 		
		
		$html_pdf=$html_pdf.'</table></body>';
		$stylesheet = file_get_contents(wp_jobboard_URLPATH . 'admin/files/css/pdf.css');		
		$mpdf->setFooter(''.$footer_html.', Page # {PAGENO}');	
		$mpdf->WriteHTML($html_pdf);
		$mpdf->Output();
		exit;
	}
?>