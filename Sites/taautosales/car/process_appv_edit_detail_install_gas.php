<?php
include_once("../include/config.php");
include_once("../include/function.php");

$iduser = $_SESSION["ss_iduser"];
$nowDateTime = nowDateTime();

$cmd = pg_escape_string($_POST['cmd']);
$editID = pg_escape_string($_POST['editID']);
$remark = pg_escape_string($_POST['remark']);

pg_query("BEGIN WORK");
$status = 0;
$Error = "พบข้อผิดพลาด \n";

$qry_select = pg_query("select * from \"installGas_edit\" where \"editID\" = '$editID' and \"appvStatus\" = '9'");
$num_row = pg_num_rows($qry_select);
if($num_row>0)
{
	if($cmd == "appv")
	{
		//update อนุมัติ
		$qry_up = "update \"installGas_edit\" set \"appvID\" = '$iduser', \"appvStamp\" = '$nowDateTime', \"appvStatus\" = '1', \"appvNote\" = '$remark'
					where \"editID\" = '$editID' and \"appvStatus\" = '9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		
		// หาข้อมูลหลัก
		$qry_main = pg_query("select * from \"installGas_edit\" where \"editID\" = '$editID' ");
		$res_main = pg_fetch_array($qry_main);
		$gasInvoiceNo = $res_main["gasInvoiceNo"]; // เลขที่ที่ใบแจ้งหนี้/ใบส่งของ
		$gasInvoiceDate = $res_main["gasInvoiceDate"]; // วันที่ใบแจ้งหนี้/ใบส่งของ
		$wh_id = $res_main["wh_id"]; // รหัสร้านค้า
		$Vender = $res_main["Vender"]; // ชื่อร้านค้า
		$payDate = $res_main["payDate"]; // วันที่จ่าย
		$cash_amount = $res_main["cash_amount"]; // ยอดเงินสด
		$cheque_amount = $res_main["cheque_amount"]; // ยอดเช็ค
		$chequeNO = $res_main["chequeNO"]; // เลขที่เช็ค
		$BankCode = $res_main["BankCode"]; // รหัสธนาคาร
		$payerCheque = $res_main["payerCheque"]; // ผู้จ่ายเช็ค
		$sum_amount = $res_main["sum_amount"]; // จำนวนเงินรวม
		$discount = $res_main["discount"]; // ส่วนลด
		$payTrue = $res_main["payTrue"]; // จำนวนเงินที่จ่ายจริง
		
		// ตรวจสอบค่าว่าง
		$gasInvoiceDate = checknull($gasInvoiceDate); // วันที่ใบแจ้งหนี้/ใบส่งของ
		$wh_id = checknull($wh_id); // รหัสร้านค้า
		$Vender = checknull($Vender); // ชื่อร้านค้า
		$payDate = checknull($payDate); // วันที่จ่าย
		$cash_amount = checknull($cash_amount); // ยอดเงินสด
		$cheque_amount = checknull($cheque_amount); // ยอดเช็ค
		$chequeNO = checknull($chequeNO); // เลขที่เช็ค
		$BankCode = checknull($BankCode); // รหัสธนาคาร
		$payerCheque = checknull($payerCheque); // ผู้จ่ายเช็ค
		$sum_amount = checknull($sum_amount); // จำนวนเงินรวม
		$discount = checknull($discount); // ส่วนลด
		$payTrue = checknull($payTrue); // จำนวนเงินที่จ่ายจริง
		
		// update ข้อมูลหลัก
		$qry_up = "update \"installGas\" set \"gasInvoiceDate\" = $gasInvoiceDate, \"wh_id\" = $wh_id, \"Vender\" = $Vender, \"payDate\" = $payDate,
					\"cash_amount\" = $cash_amount, \"cheque_amount\" = $cheque_amount, \"chequeNO\" = $chequeNO, \"BankCode\" = $BankCode ,
					\"payerCheque\" = $payerCheque, \"sum_amount\" = $sum_amount, \"discount\" = $discount, \"payTrue\" = $payTrue
					where \"gasInvoiceNo\" = '$gasInvoiceNo'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		
		// ข้อมูลรายละเอียด
		$qry_installDetail = pg_query("select * from \"installGasDetail_edit\" where \"editID\" = '$editID' order by \"editDetailID\" ");
		while($res_installDetail = pg_fetch_array($qry_installDetail))
		{
			$auto_id_CarMove = $res_installDetail["auto_id_CarMove"]; // รหัสรายการใน CarMove
			$gas_install_date = $res_installDetail["gas_install_date"]; // วันที่ติดตั้ง
			$car_idno = $res_installDetail["car_idno"]; // เลขทะเบียนรถในสต๊อก
			$car_name = $res_installDetail["car_name"]; // รุ่นรถ
			$car_num = $res_installDetail["car_num"]; // เลขที่ตัวถัง
			$eng_cert = $res_installDetail["eng_cert"]; // เลขที่ใบวิศวะ
			$send_gas_date = $res_installDetail["send_gas_date"]; // วันที่ส่งรถติดตั้งแก๊ส
			$received_date = $res_installDetail["received_date"]; // วันที่ลูกค้ารับรถ
			$res_id = $res_installDetail["res_id"]; // เลขที่ใบจอง
			$gas_deatils = $res_installDetail["gas_deatils"]; // รายการติดตั้ง
			$install_amount = $res_installDetail["install_amount"]; // ค่าติดตั้งแก๊ส
			$oil_amount = $res_installDetail["oil_amount"]; // ค่าน้ำมัน
			
			// ตรวจสอบค่าว่าง
			$gas_install_date = checknull($gas_install_date); // วันที่ติดตั้ง
			$car_idno = checknull($car_idno); // เลขทะเบียนรถในสต๊อก
			$car_name = checknull($car_name); // รุ่นรถ
			$car_num = checknull($car_num); // เลขที่ตัวถัง
			$eng_cert = checknull($eng_cert); // เลขที่ใบวิศวะ
			$send_gas_date = checknull($send_gas_date); // วันที่ส่งรถติดตั้งแก๊ส
			$received_date = checknull($received_date); // วันที่ลูกค้ารับรถ
			$res_id = checknull($res_id); // เลขที่ใบจอง
			$gas_deatils = checknull($gas_deatils); // รายการติดตั้ง
			$install_amount = checknull($install_amount); // ค่าติดตั้งแก๊ส
			$oil_amount = checknull($oil_amount); // ค่าน้ำมัน
			
			// หาว่ามีข้อมูลเก่าอยู่หรือไม่
			$qry_oldDetail = pg_query("select * from \"installGasDetail\" where \"gasInvoiceNo\" = '$gasInvoiceNo' and \"auto_id_CarMove\" = '$auto_id_CarMove' ");
			$row_oldDetail = pg_num_rows($qry_oldDetail);
			if($row_oldDetail > 0) // ถ้ามีข้อมูลแล้ว  ให้ทำการ update
			{
				// update ข้อมูลรายละเอียด
				$qry_up = "update \"installGasDetail\" set \"gas_install_date\" = $gas_install_date, \"car_idno\" = $car_idno, \"car_name\" = $car_name, \"car_num\" = $car_num,
							\"eng_cert\" = $eng_cert, \"send_gas_date\" = $send_gas_date, \"received_date\" = $received_date, \"res_id\" = $res_id ,
							\"gas_deatils\" = $gas_deatils, \"install_amount\" = $install_amount, \"oil_amount\" = $oil_amount
							where \"gasInvoiceNo\" = '$gasInvoiceNo' and \"auto_id_CarMove\" = '$auto_id_CarMove' ";
				if(pg_query($qry_up)){
				}else{
					$status++;
					$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
				}
			}
			else // ถ้ามีข้อมูลแล้ว  ให้ทำการ insert
			{
				// insert ข้อมูลรายละเอียด
				$qry_in = "insert into \"installGasDetail\"(\"gasInvoiceNo\", \"auto_id_CarMove\", \"gas_install_date\", \"car_idno\", \"car_name\", \"car_num\", \"eng_cert\",
								\"send_gas_date\", \"received_date\", \"res_id\", \"gas_deatils\", \"install_amount\", \"oil_amount\")
							values('$gasInvoiceNo', '$auto_id_CarMove', $gas_install_date, $car_idno, $car_name, $car_num, $eng_cert,
								$send_gas_date, $received_date, $res_id, $gas_deatils, $install_amount, $oil_amount)";
				if(pg_query($qry_in)){
				}else{
					$status++;
					$Error .= "Update ข้อมูลล้มเหลว  $qry_in \n";
				}
			}
		}
	}
	else if($cmd == "notappv")
	{
		//update ไม่อนุมัติ
		$qry_up = "update \"installGas_edit\" set \"appvID\" = '$iduser', \"appvStamp\" = '$nowDateTime', \"appvStatus\" = '0', \"appvNote\" = '$remark'
					where \"editID\" = '$editID' and \"appvStatus\" = '9'";
		if(pg_query($qry_up)){
		}else{
			$status++;
			$Error .= "Update ข้อมูลล้มเหลว  $qry_up \n";
		}
		
	}
}
else
{
	$status++;
	$Error .= "มีการทำรายการอนุมัติไปก่อนหน้านี้แล้ว \n";
}
	
if($status == 0){
        pg_query("COMMIT");
        $data = 1;
    }else{
        pg_query("ROLLBACK");
        $data = "ไม่สามารถบันทึกได้! $Error";
    }
echo $data;
?>