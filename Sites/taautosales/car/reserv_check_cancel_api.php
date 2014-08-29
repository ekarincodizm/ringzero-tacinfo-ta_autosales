<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = $_REQUEST['cmd'];
$res_id = pg_escape_string($_REQUEST['res_id']);
	
if($cmd == 'check_payment'){
	$status = 0;
	$txt_error = array();
	
	pg_query("BEGIN WORK");
	
	
	$qry_check_payment = pg_query("SELECT res_id FROM receipt_tmp WHERE status = '1' AND res_id = '$res_id' ");
	$num_rows = pg_num_rows($qry_check_payment);
	
	if($num_rows != 0){
		$status++;
        $txt_error[] = "ไม่สามารถยกเลิกรายการนี้ได้ ต้องทำการยกเลิกใบเสร็จรับเงินชั่วคราวก่อน!!!";
	}
	
	if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = $txt_error[0];
    }
    echo json_encode($data);
}
?>