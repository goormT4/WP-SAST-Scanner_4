<?php
if (!class_exists('ComposerAutoloaderInit4618f5c41cf5e27cc7908556f031e4d4')) {require_once EWD_FEUP_CD_PLUGIN_PATH . 'PHPSpreadsheet/vendor/autoload.php';}
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
function EWD_FEUP_Export_To_Excel() {
	global $wpdb;
	global $ewd_feup_user_table_name, $ewd_feup_user_fields_table_name, $ewd_feup_levels_table_name, $ewd_feup_fields_table_name;

	if ( ! current_user_can( 'administrator' ) ) { return __( 'You must be an admin to undertake the requested action.', 'front-end-only-users' ); }
		
	$Admin_Approval = get_option("EWD_FEUP_Admin_Approval");
	$Email_Confirmation = get_option("EWD_FEUP_Email_Confirmation");
	$Payment_Frequency = get_option("EWD_FEUP_Payment_Frequency");
		
	// Instantiate a new PHPExcel object 
	$objPHPExcel = new Spreadsheet();
	// Set the active Excel worksheet to sheet 0 
	$objPHPExcel->setActiveSheetIndex(0);  

	// Print out the regular order field labels
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "Username");
	$objPHPExcel->getActiveSheet()->setCellValue("B1", "Date Created");
	$objPHPExcel->getActiveSheet()->setCellValue("C1", "Last Login");
		
	$column = 'D';

	if ($Admin_Approval == "Yes") {
		$objPHPExcel->getActiveSheet()->setCellValue($column . "1", "Admin Approved");
		$column++;
	}
	if ($Email_Confirmation == "Yes"){
		$objPHPExcel->getActiveSheet()->setCellValue($column . "1", "Email Confirmed");
		$column++;
	}
	if ($Payment_Frequency != "None"){
		$objPHPExcel->getActiveSheet()->setCellValue($column . "1", "Membership Fees Paid");
		$column++;
	}

	//start of printing column names as names of custom fields  
	$Sql = "SELECT * FROM $ewd_feup_fields_table_name";
	$User_Fields = $wpdb->get_results($Sql);
	foreach ($User_Fields as $User_Field) {
     	$objPHPExcel->getActiveSheet()->setCellValue($column . "1", $User_Field->Field_Name);
    	$column++;
	}  

	//start while loop to get data  
	$rowCount = 2;  
	$Users = $wpdb->get_results("SELECT * FROM $ewd_feup_user_table_name");
	foreach ($Users as $User)  
	{  
    	$objPHPExcel->getActiveSheet()->setCellValue("A" . $rowCount, $User->Username);
		$objPHPExcel->getActiveSheet()->setCellValue("B" . $rowCount, $User->User_Date_Created);
		$objPHPExcel->getActiveSheet()->setCellValue("C" . $rowCount, $User->User_Last_Login);

		$column = 'D';

		if ($Admin_Approval == "Yes") {
			$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $User->User_Admin_Approved);
			$column++;
		}
		if ($Email_Confirmation == "Yes"){
			$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $User->User_Email_Confirmed);
			$column++;
		}
		if ($Payment_Frequency != "None"){
			$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $User->User_Membership_Fees_Paid);
			$column++;
		}

    	foreach ($User_Fields as $User_Field) {  
        	$MetaValue = $wpdb->get_var($wpdb->prepare("SELECT Field_Value FROM $ewd_feup_user_fields_table_name WHERE Field_ID=%d AND User_ID=%d", $User_Field->Field_ID, $User->User_ID));
        	$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $MetaValue);
        	$column++;
    	}
    	$rowCount++;
	} 


	// Redirect output to a client's web browser (Excel5) 
	header('Content-Type: application/vnd.ms-excel'); 
	header('Content-Disposition: attachment;filename="FEUP_Users_Export.csv"'); 
	header('Cache-Control: max-age=0'); 
	$objWriter = new Csv($objPHPExcel);
	$objWriter->save('php://output');
	die();
}
?>