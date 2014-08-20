<?php
include_once("../include/config.php");
include_once("../include/function.php");

    $cmd = pg_escape_string($_POST['cmd']);
	
if($cmd == "save"){
	
    $receive_date = pg_escape_string($_POST['receive_date']);
    $inv_no = pg_escape_string($_POST['inv_no']);
    $payBy = pg_escape_string($_POST['payBy']);
	$id_user = $_SESSION["ss_iduser"];

	pg_query("BEGIN WORK");
	$status = 0;
	
	// ตรวจสอบก่อนว่า มีการขอยกเลิกหรือไม่
	$qry_chk_cancel = pg_query("select \"appvStatus\" from \"Invoices_account_cancel\" where \"inv_no\" = '$inv_no' and \"appvStatus\" <> '0' ");
	$chk_cancel = pg_fetch_result($qry_chk_cancel,0);
	if($chk_cancel == "9")
	{
		$txt_error .= " ใบแจ้งหนี้ $inv_no อยู่ระหว่างการขอยกเลิก ";
        $status++;
	}
	elseif($chk_cancel == "1")
	{
		$txt_error .= " ใบแจ้งหนี้ $inv_no ถูกยกเลิกแล้ว ";
        $status++;
	}
	else
	{ // ถ้าไม่มีการขอยกเลิก
		$qry_rec = pg_query("select insert_acc_receipt_new_account('$inv_no', '$receive_date', '$payBy', '$id_user') ");

		$res_rec = pg_fetch_result($qry_rec,0);
		if( empty($res_rec) OR $res_rec == "" ){
			$txt_error .= " insert_acc_receipt_new error ";
			$status++;
		}
		
		$str_replace_v = str_replace("R", "V", $res_rec);
	}

	if($status == 0){
		pg_query("COMMIT");
		$data['success'] = true;
		$data['receipt_no'] = $res_rec;
		$data['invoice_no'] = $str_replace_v;
		$data['message'] = "บันทึกเรียบร้อยแล้ว  เอกสารเลขที่ $res_rec ";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error";
    }
    echo json_encode($data);
}
?>