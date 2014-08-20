<?php
include_once("../include/config.php");
include_once("../include/function.php");

$id_user = $_SESSION["ss_iduser"];
$nowDateTime = nowDateTime();

$cmd = pg_escape_string($_POST['cmd']);
	
if($cmd == "request") // ถ้าเป็นการขอยกเลิก
{
    $inv_no = pg_escape_string($_POST['inv_no']);
	$cancel_note = pg_escape_string($_POST['cancel_note']);

	pg_query("BEGIN WORK");
	$status = 0;
	
	// ตรวจสอบว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
	$qry_cancel = pg_query("select * from \"Invoices_account_cancel\" where \"inv_no\" = '$inv_no' and \"appvStatus\" in('9','1') ");
	$row_cancel = pg_num_rows($qry_cancel);
	if($row_cancel > 0)
	{
		$txt_error .= "เนื่องจาก มีการขอยกเลิกใบแจ้งหนี้ $inv_no ไปก่อนหน้านี้แล้ว";
        $status++;
	}
	
	// ตรวจสอบว่ามีการออกใบเสร็จไปก่อนหน้านี้แล้วหรือยัง
	$qry_is_print = pg_query("select \"is_print\" from \"Invoices_account\" where \"inv_no\" = '$inv_no' ");
	$is_print = pg_fetch_result($qry_is_print,0);
	if($is_print == "1")
	{
		$txt_error .= "เนื่องจาก ใบแจ้งหนี้ $inv_no ถูกออกใบเสร็จไปแล้ว";
        $status++;
	}
	
	$sql_request = "insert into \"Invoices_account_cancel\"(\"inv_no\", \"doerID\", \"doerStamp\", \"doerNote\", \"appvStatus\")
					values('$inv_no', '$id_user', '$nowDateTime', '$cancel_note', '9')";
	$qry_request = pg_query($sql_request);
	if(!$qry_request)
	{
		$txt_error .= $sql_request;
		$status++;
	}

	if($status == 0){
		pg_query("COMMIT");
		$data['success'] = true;
		$data['message'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถทำรายการได้! $txt_error";
    }
    echo json_encode($data);
}
elseif($cmd == "appv") // ถ้าเป็นการทำรายการอนุมัติ
{
	$appvStatus = pg_escape_string($_POST['appvStatus']);
    $cancelID = pg_escape_string($_POST['cancelID']);
	$appv_note = pg_escape_string($_POST['appv_note']);

	pg_query("BEGIN WORK");
	$status = 0;
	
	// หาเลขที่ใบแจ้งหนี้และเหตุผลที่ขอยกเลิก
	$qry_inv = pg_query("select \"inv_no\" from \"Invoices_account_cancel\" where \"cancelID\" = '$cancelID' ");
	$inv_no = pg_fetch_result($qry_inv,0);
	
	// ตรวจสอบว่ามีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
	$qry_appvStatus= pg_query("select \"appvStatus\" from \"Invoices_account_cancel\" where \"cancelID\" = '$cancelID' ");
	$appvStatusChk = pg_fetch_result($qry_appvStatus,0);
	if($appvStatusChk == "1")
	{
		$txt_error .= "เนื่องจาก อนุมัติ ยกเลิก $inv_no ไปก่อนหน้านี้แล้ว ";
		$status++;
	}
	elseif($appvStatusChk == "0")
	{
		$txt_error .= "เนื่องจาก ไม่อนุมัติ ยกเลิก $inv_no ไปก่อนหน้านี้แล้ว ";
		$status++;
	}
	
	if($appvStatus == "1") // ถ้าอนุมัติ
	{
		// ตรวจสอบว่ามีการออกใบเสร็จไปก่อนหน้านี้แล้วหรือยัง
		$qry_is_print = pg_query("select \"is_print\" from \"Invoices_account\" where \"inv_no\" = '$inv_no' ");
		$is_print = pg_fetch_result($qry_is_print,0);
		if($is_print == "1")
		{
			$txt_error .= "เนื่องจาก ใบแจ้งหนี้ $inv_no ถูกออกใบเสร็จไปแล้ว ";
			$status++;
		}
	}
	
	// บันทึกการทำรายการ
	$sql_appv = "update \"Invoices_account_cancel\" set \"appvStatus\" = '$appvStatus', \"appvID\" = '$id_user', \"appvStamp\" = '$nowDateTime', \"appvNote\" = '$appv_note'
				where \"cancelID\" = '$cancelID' and \"appvStatus\" = '9' ";
	$qry_appv = pg_query($sql_appv);
	if(!$qry_appv)
	{
		$txt_error .= "$sql_appv ";
		$status++;
	}
	
	if($appvStatus == "1") // ถ้าอนุมัติ
	{
		// update สถานะใบแจ้งหนี้ เป็น ยกเลิก
		$sql_cancel = "update \"Invoices_account\" set \"cancel\" = true where \"inv_no\" = '$inv_no' and \"cancel\" = false ";
		$qry_cancel = pg_query($sql_cancel);
		if(!$qry_cancel)
		{
			$txt_error .= "$sql_cancel ";
			$status++;
		}
	}

	if($status == 0){
		pg_query("COMMIT");
		$data['success'] = true;
		$data['message'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถทำรายการได้! $txt_error";
    }
    echo json_encode($data);
}
?>