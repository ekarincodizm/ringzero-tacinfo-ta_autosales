<?php
include_once("../include/config.php");
include_once("../include/function.php");
?>

<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />

<?php
$iduser = $_SESSION["ss_iduser"];
$nowDateTime = nowDateTime();

$gasInvoiceNo = pg_escape_string($_POST['gasInvoiceNo']); // เลขที่ใบแจ้งหนี้/ใบส่งของ
$gasInvoiceDate = pg_escape_string($_POST['gasInvoiceDate']); // วันที่ใบแจ้งหนี้/ใบส่งของ
$wh_id = pg_escape_string($_POST['wh_id']); // รหัสร้านค้า
$payDate = pg_escape_string($_POST['payDate']); // วันที่จ่าย
$cash_amount = pg_escape_string($_POST['cash_amount']); // ยอดเงินสด
$cheque_amount = pg_escape_string($_POST['cheque_amount']); // ยอดเช็ค
$chequeNO = pg_escape_string($_POST['chequeNO']); // เลขที่เช็ค
$BankCode = pg_escape_string($_POST['BankCode']); // รหัสธนาคาร
$payerCheque = pg_escape_string($_POST['payerCheque']); // ผู้จ่ายเช็ค
$sum_amount = pg_escape_string($_POST['sum_amount']); // จำนวนเงินรวม
$discount = pg_escape_string($_POST['discount']); // ส่วนลด
$payTrue = pg_escape_string($_POST['payTrue']); // จำนวนเงินที่จ่ายจริง

$rowDetail = pg_escape_string($_POST['rowDetail']); // จำนวนรถ

pg_query("BEGIN WORK");
$status = 0;
$Error = "พบข้อผิดพลาด <br>";

// กำหนดชื่อบริษัท
if($wh_id == "18")
{
	$storeName = "'บริษัท เอ็น.จี.วี. พลัส จำกัด'";
}
elseif($wh_id == "19")
{
	$storeName = "'บริษัท สแกนอินเตอร์ จำกัด'";
}
else
{
	$storeName = "NULL";
}

$chequeNO = checknull($chequeNO);
$BankCode = checknull($BankCode);
$payerCheque = checknull($payerCheque);
$payDate = checknull($payDate);

$qry_select = pg_query("select * from \"installGas_edit\" where \"gasInvoiceNo\" = '$gasInvoiceNo' and \"appvStatus\" = '9' ");
$num_row = pg_num_rows($qry_select);
if($num_row>0){
	$status++;
	$Error .= "มีการขอแก้ไข $gasInvoiceNo ก่อนหน้านี้ที่ยังรอการอนุมัติ <br>";
}

// เพิ่มข้อมูลหลัก
$qry_installGas = "insert into \"installGas_edit\"(\"gasInvoiceNo\", \"gasInvoiceDate\", \"wh_id\", \"Vender\", \"payDate\", \"cash_amount\", \"cheque_amount\",
							\"chequeNO\", \"BankCode\", \"payerCheque\", \"sum_amount\", \"discount\", \"payTrue\", \"doerID\", \"doerStamp\", \"appvStatus\")
						values('$gasInvoiceNo', '$gasInvoiceDate', '$wh_id', $storeName, $payDate, '$cash_amount', '$cheque_amount',
							$chequeNO, $BankCode, $payerCheque, '$sum_amount', '$discount', '$payTrue', '$iduser', '$nowDateTime', '9')
					returning \"editID\" ";

if(!$res=@pg_query($qry_installGas)){
	$Error .= "บันทึก ไม่สำเร็จ  $qry_installGas <br>";
	$status++;
}else{
	$editID = pg_fetch_result($res,0);
}

// เพิ่มรายละเอียด	
for($i=1; $i<=$rowDetail; $i++)
{
	$auto_id_CarMove[$i] = pg_escape_string($_POST["auto_id_CarMove$i"]);
	$gas_install_date[$i] = pg_escape_string($_POST["gas_install_date$i"]);
	$car_idno[$i] = pg_escape_string($_POST["car_idno$i"]);
	$car_name[$i] = pg_escape_string($_POST["car_name$i"]);
	$car_num[$i] = pg_escape_string($_POST["car_num$i"]);
	$eng_cert[$i] = pg_escape_string($_POST["eng_cert$i"]);
	$send_gas_date[$i] = pg_escape_string($_POST["send_gas_date$i"]);
	$received_date[$i] = pg_escape_string($_POST["received_date$i"]);
	$res_id[$i] = pg_escape_string($_POST["res_id$i"]);
	$gas_deatils[$i] = pg_escape_string($_POST["gas_deatils$i"]);
	$install_amount[$i] = pg_escape_string($_POST["install_amount$i"]);
	$oil_amount[$i] = pg_escape_string($_POST["oil_amount$i"]);
	
	$res_id[$i] = checknull($res_id[$i]);
	$eng_cert[$i] = checknull($eng_cert[$i]);
	
	$qry_installGasDetail = "insert into \"installGasDetail_edit\"(\"editID\", \"gasInvoiceNo\", \"auto_id_CarMove\", \"gas_install_date\", \"car_idno\", \"car_name\", \"car_num\",
								\"eng_cert\", \"send_gas_date\", \"received_date\", \"res_id\", \"gas_deatils\", \"install_amount\", \"oil_amount\")
							values('$editID', '$gasInvoiceNo', '$auto_id_CarMove[$i]', '$gas_install_date[$i]', '$car_idno[$i]', '$car_name[$i]', '$car_num[$i]',
								$eng_cert[$i], '$send_gas_date[$i]', '$received_date[$i]', $res_id[$i], '$gas_deatils[$i]', '$install_amount[$i]', '$oil_amount[$i]')";
	
	if(!$res=@pg_query($qry_installGasDetail)){
		$Error .= "บันทึก ไม่สำเร็จ  $qry_installGasDetail <br>";
		$status++;
	}
}
	
if($status == 0){
	pg_query("COMMIT");
	echo "<center>";
	echo "<font color=\"#0000FF\">บันทึกสำเร็จ</font>";
	echo "</center>";
	echo "<meta http-equiv='refresh' content='2; URL=frm_edit_detail_install_gas.php'>";
	
	/*echo "<script>";
	echo "alert('บันทึกสำเร็จ')";
	echo "location.reload();";
	echo "</script>";*/
}else{
	pg_query("ROLLBACK");
	$data = "<font color=\"#FF0000\">ไม่สามารถบันทึกได้! $Error</font>";
	echo $data;
	echo "<meta http-equiv='refresh' content='5; URL=frm_edit_detail_install_gas.php'>";
	
	/*echo "<script>";
	echo "alert('บันทึกผิดพลาด !!')";
	echo "location.reload();";
	echo "</script>";*/
}
?>