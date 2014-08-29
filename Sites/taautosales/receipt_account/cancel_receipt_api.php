<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];
$nowDateTime = nowDateTime();

if($cmd == "save_cancel_receipt")
{
	$receipt_no = trim(pg_escape_string($_POST['receipt_no']));
	$area_memo = pg_escape_string($_POST['area_memo']);
    $cb_type = pg_escape_string($_POST['cb_type']);

	pg_query("BEGIN WORK");
    $status = 0;
    $stat_ok = 0;

    $sub_id = substr($receipt_no, 2, 1);
	
	if($cb_type == 'NRF'){
		$cancel_status = '0'; // ไม่คืนเงิน
	}else if($cb_type == 'CUS'){
		$cancel_status = '2'; //คืนเงิน
	}
	
	// ตรวจสอบว่าเคยทำรายการไปแล้วหรือยัง
	$qry_cancel_receipt = pg_query("SELECT * FROM \"CancelReceipt_account\" WHERE \"r_receipt\" = '$receipt_no' AND \"appvStatus\" in('9','1') ");
	$num_rows = pg_num_rows($qry_cancel_receipt);
	if($num_rows > 0)
	{
		$status++;
		$txt_error .= "เลขที่ใบเสร็จ $receipt_no เคยมีการขอยกเลิกไปแล้ว!!! ";
	}
	
	// หาเลขที่ใบเสร็จในระบบ (บัญชี)
	$qry_rowChk = pg_query("SELECT \"receipt_no\" FROM \"ReceiptDtl\" WHERE receipt_no = '$receipt_no'
					AND \"invoice_no\" in(select \"tax_no\" from \"Invoices_account\" where \"cancel\" = false) ");
	$rowChk = pg_num_rows($qry_rowChk);
	if($rowChk > 0)
	{ // ถ้ามีข้อมูล			
		$stat_ok++;
	}
	
	if($stat_ok == 0)
	{
        $status++;
		$txt_error .= "ไม่พบ เลขที่ใบเสร็จ $receipt_no ในระบบ (บัญชี) !!! ";
    }
	else
	{
		// หาเลขที่ใบกำหับ
		$qry_invoice_no = pg_query("SELECT \"invoice_no\" FROM \"ReceiptDtl\" WHERE receipt_no = '$receipt_no'");
        $invoice_no = pg_fetch_result($qry_invoice_no,0);
		
		// หาเลขที่ใบแจ้งหนี้
		$qry_inv_no = pg_query("SELECT \"inv_no\" FROM \"Invoices_account\" WHERE \"tax_no\" = '$invoice_no'");
        $inv_no = pg_fetch_result($qry_inv_no,0);
		
		// หาจำนวนเงิน
		$qry_money = pg_query("select sum(\"amount\") as \"sum_amount\", sum(\"vat\") as \"sum_vat\" from \"InvoiceDetails_account\" where \"inv_no\" = '$inv_no' ");
		$amount = pg_fetch_result($qry_money,0); // มูลค่า
		$vat = pg_fetch_result($qry_money,1); // vat
		$money = $amount + $vat; // ยอดรวม
		
		// บันทึกข้อมูล
		$in_qry="INSERT INTO \"CancelReceipt_account\"(\"r_receipt\",\"inv_no\",\"amount\",\"vat\",\"money\",\"return_to\",\"doerID\",\"doerStamp\",\"doerNote\",\"appvStatus\") 
				VALUES('$receipt_no','$inv_no','$amount','$vat','$money','$cb_type','$_SESSION[ss_iduser]','$nowDateTime','$area_memo','9')";
		if(!$res=@pg_query($in_qry))
		{
			$txt_error .= "บันทึกไม่สำเร็จ $in_qry ";
			$status++;
		}
    }
	
	if($status == 0){
        pg_query("COMMIT");
		//pg_query("ROLLBACK");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = $txt_error;
    }
    echo json_encode($data);
}
?>