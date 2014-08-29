<?php
include_once("../include/config.php");
include_once("../include/function.php");

$editID = pg_escape_string($_GET["editID"]);

$qry_list = pg_query("select \"gasInvoiceNo\", \"gasInvoiceDate\", \"wh_id\" from \"installGas_edit\" where \"editID\" = '$editID' ");
$gasInvoiceNo = pg_fetch_result($qry_list,0);
$gasInvoiceDate = pg_fetch_result($qry_list,1);
$wh_id = pg_fetch_result($qry_list,2);

// กำหนดชื่อบริษัท
if($wh_id == "18")
{
	$storeName = "บริษัท เอ็น.จี.วี. พลัส จำกัด";
}
elseif($wh_id == "19")
{
	$storeName = "บริษัท สแกนอินเตอร์ จำกัด";
}
else
{
	$storeName = "";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
    <title>อนุมัติการแก้ไข รายละเอียดติดตั้งแก๊ส</title>
	
	<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
</head>
<body>
<center>
<h1>อนุมัติการแก้ไข รายละเอียดติดตั้งแก๊ส</h1>
<form name="frm1" method="post" action="process_edit_detail_install_gas.php">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
    <tr>
        <td align="center">
			<fieldset><legend><B>ข้อมูลหลัก</B></legend>
			<center>
				<table width="auto" border="0" cellSpacing="1" cellPadding="3" bgcolor="#FFFFFF">
					<tr>
						<td align="right"><b>เลขที่ใบแจ้งหนี้/ใบส่งของ : </b></td>
						<td align="left"><?php echo $gasInvoiceNo; ?></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right"><b>วันที่ใบแจ้งหนี้/ใบส่งของ : </b></td>
						<td align="left"><input type="text" name="gasInvoiceDate" id="gasInvoiceDate" value="<?php echo "$gasInvoiceDate"; ?>" size="10"></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right"><b>ชื่อร้านค้า : </b></td>
						<td align="left"><?php echo $storeName; ?></td>
					</tr>
				</table>
			</center>
			</fieldset>
			
			<fieldset><legend><B>รายละเอียด</B></legend>
			<center>
				<table id="tableDetail" align="center" width="auto" border="0" cellspacing="1" cellpadding="1" bgcolor="#BBBBEE">
					<tr align="center" bgcolor="#79BCFF">
						<th>NO.</th>
						<th>วันที่ติดตั้ง</th>
						<th>เลขที่ทะเบียนรถในสต๊อก</th>
						<th>รุ่นรถ</th>
						<th>เลขที่ตัวถัง</th>
						<th>เลขที่ใบวิศวะ</th>
						<th>วันที่ส่งรถติดตั้งแก๊ส</th>
						<th>วันที่ลูกค้ารับรถ</th>
						<th>เลขที่ใบจอง</th>
						<th>รายการติดตั้ง</th>
						<th>ค่าติดตั้ง</th>
						<th>ค่าน้ำมัน</th>
					</tr>
					
					<?php
					$i = 0;
					$qry_installDetail = pg_query("select * from \"installGasDetail_edit\" where \"editID\" = '$editID' order by \"editDetailID\" ");
					while($res_installDetail = pg_fetch_array($qry_installDetail))
					{
						$i++;
						$auto_id_CarMove = $res_installDetail["auto_id_CarMove"];
						
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
						
						// หาว่ามีข้อมูลเก่าอยู่หรือไม่
						$qry_oldDetail = pg_query("select * from \"installGasDetail\" where \"gasInvoiceNo\" = '$gasInvoiceNo' and \"auto_id_CarMove\" = '$auto_id_CarMove' ");
						$row_oldDetail = pg_num_rows($qry_oldDetail);
						
						if($row_oldDetail > 0) // ถ้ามีข้อมูลแล้ว
						{
							$res_oldDetail = pg_fetch_array($qry_oldDetail);
							
							$old_gas_install_date = $res_oldDetail["gas_install_date"]; // วันที่ติดตั้ง
							$old_car_idno = $res_oldDetail["car_idno"]; // เลขทะเบียนรถในสต๊อก
							$old_car_name = $res_oldDetail["car_name"]; // รุ่นรถ
							$old_car_num = $res_oldDetail["car_num"]; // เลขที่ตัวถัง
							$old_eng_cert = $res_oldDetail["eng_cert"]; // เลขที่ใบวิศวะ
							$old_send_gas_date = $res_oldDetail["send_gas_date"]; // วันที่ส่งรถติดตั้งแก๊ส
							$old_received_date = $res_oldDetail["received_date"]; // วันที่ลูกค้ารับรถ
							$old_res_id = $res_oldDetail["res_id"]; // เลขที่ใบจอง
							$old_gas_deatils = $res_oldDetail["gas_deatils"]; // รายการติดตั้ง
							$old_install_amount = $res_oldDetail["install_amount"]; // ค่าติดตั้งแก๊ส
							$old_oil_amount = $res_oldDetail["oil_amount"]; // ค่าน้ำมัน
							
							//-------------------------------------- เปรียบเทียบข้อมูล
								// วันที่ติดตั้ง
								if($gas_install_date != $old_gas_install_date)
								{
									$gas_install_date_style = "style=\"background-color:#FFBBBB;\" title=\"$old_gas_install_date\" ";
								}
								else
								{
									$gas_install_date_style = "";
								}
								
								// เลขทะเบียนรถในสต๊อก
								if($car_idno != $old_car_idno)
								{
									$car_idno_style = "style=\"background-color:#FFBBBB;\" title=\"$old_car_idno\" ";
								}
								else
								{
									$car_idno_style = "";
								}
								
								// รุ่นรถ
								if($car_name != $old_car_name)
								{
									$car_name_style = "style=\"background-color:#FFBBBB;\" title=\"$old_car_name\" ";
								}
								else
								{
									$car_name_style = "";
								}
								
								// เลขที่ตัวถัง
								if($car_num != $old_car_num)
								{
									$car_num_style = "style=\"background-color:#FFBBBB;\" title=\"$old_car_num\" ";
								}
								else
								{
									$car_num_style = "";
								}
								
								// เลขที่ใบวิศวะ
								if($eng_cert != $old_eng_cert)
								{
									$eng_cert_style = "style=\"background-color:#FFBBBB;\" title=\"$old_eng_cert\" ";
								}
								else
								{
									$eng_cert_style = "";
								}
								
								// วันที่ส่งรถติดตั้งแก๊ส
								if($send_gas_date != $old_send_gas_date)
								{
									$send_gas_date_style = "style=\"background-color:#FFBBBB;\" title=\"$old_send_gas_date\" ";
								}
								else
								{
									$send_gas_date_style = "";
								}
								
								// วันที่ลูกค้ารับรถ
								if($received_date != $old_received_date)
								{
									$received_date_style = "style=\"background-color:#FFBBBB;\" title=\"$old_received_date\" ";
								}
								else
								{
									$received_date_style = "";
								}
								
								// เลขที่ใบจอง
								if($res_id != $old_res_id)
								{
									$res_id_style = "style=\"background-color:#FFBBBB;\" title=\"$old_res_id\" ";
								}
								else
								{
									$res_id_style = "";
								}
								
								// รายการติดตั้ง
								if($gas_deatils != $old_gas_deatils)
								{
									$gas_deatils_style = "style=\"background-color:#FFBBBB;\" title=\"$old_gas_deatils\" ";
								}
								else
								{
									$gas_deatils_style = "";
								}
								
								// ค่าติดตั้งแก๊ส
								if($install_amount != $old_install_amount)
								{
									$install_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_install_amount\" ";
								}
								else
								{
									$install_amount_style = "style=\"text-align:right;\"";
								}
								
								// ค่าน้ำมัน
								if($oil_amount != $old_oil_amount)
								{
									$oil_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_oil_amount\" ";
								}
								else
								{
									$oil_amount_style = "style=\"text-align:right;\" ";
								}
							//-------------------------------------- จบการ เปรียบเทียบข้อมูล
						}
						else // ถ้าเป็นรายการใหม่
						{
							$gas_install_date_style = "style=\"background-color:#FFBBBB;\" "; // วันที่ติดตั้ง
							$car_idno_style = "style=\"background-color:#FFBBBB;\" "; // เลขที่ทะเบียนรถในสต๊อก
							$car_name_style = "style=\"background-color:#FFBBBB;\" "; // รุ่นรถ
							$car_num_style = "style=\"background-color:#FFBBBB;\" "; // เลขที่ตัวถัง
							$eng_cert_style = "style=\"background-color:#FFBBBB;\" "; // เลขที่ใบวิศวะ
							$send_gas_date_style = "style=\"background-color:#FFBBBB;\" "; // วันที่ส่งรถติดตั้งแก๊ส
							$received_date_style = "style=\"background-color:#FFBBBB;\" "; // วันที่ลูกค้ารับรถ
							$res_id_style = "style=\"background-color:#FFBBBB;\" "; // เลขที่ใบจอง
							$gas_deatils_style = "style=\"background-color:#FFBBBB;\" "; // รายการติดตั้ง
							$install_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" "; // ค่าติดตั้ง
							$oil_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" "; // ค่าน้ำมัน
						}
					?>
						<input type="hidden" name="auto_id_CarMove<?php echo $i; ?>" id="auto_id_CarMove<?php echo $i; ?>" value="<?php echo $auto_id_CarMove; ?>">
						<tr bgcolor="#E8E8E8">
							<td align="center">&nbsp;&nbsp;&nbsp; <?php echo $i; ?> &nbsp;&nbsp;&nbsp;</td>
							<td><input type="text" name="gas_install_date<?php echo $i; ?>" id="gas_install_date<?php echo $i; ?>" value="<?php echo $gas_install_date; ?>" size="10" <?php echo $gas_install_date_style; ?> readOnly></td>
							<td><input type="text" name="car_idno<?php echo $i; ?>" id="car_idno<?php echo $i; ?>" value="<?php echo $car_idno; ?>" <?php echo $car_idno_style; ?> readOnly></td>
							<td><input type="text" name="car_name<?php echo $i; ?>" id="car_name<?php echo $i; ?>" value="<?php echo $car_name; ?>" <?php echo $car_name_style; ?> readOnly></td>
							<td><input type="text" name="car_num<?php echo $i; ?>" id="car_num<?php echo $i; ?>" value="<?php echo $car_num; ?>" <?php echo $car_num_style; ?> readOnly></td>
							<td><input type="text" name="eng_cert<?php echo $i; ?>" id="eng_cert<?php echo $i; ?>" value="<?php echo $eng_cert; ?>" <?php echo $eng_cert_style; ?> readOnly></td>
							<td><input type="text" name="send_gas_date<?php echo $i; ?>" id="send_gas_date<?php echo $i; ?>" value="<?php echo $send_gas_date; ?>" size="10" <?php echo $send_gas_date_style; ?> readOnly></td>
							<td><input type="text" name="received_date<?php echo $i; ?>" id="received_date<?php echo $i; ?>" value="<?php echo $received_date; ?>" size="10" <?php echo $received_date_style; ?> readOnly></td>
							<td><input type="text" name="res_id<?php echo $i; ?>" id="res_id<?php echo $i; ?>" value="<?php echo $res_id; ?>" <?php echo $res_id_style; ?> readOnly></td>
							<td><input type="text" name="gas_deatils<?php echo $i; ?>" id="gas_deatils<?php echo $i; ?>" value="<?php echo $gas_deatils; ?>" <?php echo $gas_deatils_style; ?> readOnly></td>
							<td><input type="text" name="install_amount<?php echo $i; ?>" id="install_amount<?php echo $i; ?>" value="<?php echo $install_amount; ?>" size="10" <?php echo $install_amount_style; ?> readOnly></td>
							<td><input type="text" name="oil_amount<?php echo $i; ?>" id="oil_amount<?php echo $i; ?>" size="10" value="<?php echo $oil_amount; ?>" <?php echo $oil_amount_style; ?> readOnly></td>
						</tr>
					<?php
					}
					?>
				</table>
				<div id="TextBoxesGroup1">
				<div id='TextBoxDiv1'>
				</div>
				</div>
				<input type="hidden" name="rowDetail" id="rowDetail" value="<?php echo $i; ?>">
				<input type="hidden" name="gasInvoiceNo" id="gasInvoiceNo" value="<?php echo $gasInvoiceNo; ?>">
				<input type="hidden" name="wh_id" id="wh_id" value="<?php echo $wh_id; ?>">
			</center>
			</fieldset>
			
			<fieldset><legend><B>ชำระโดย</B></legend>
			<center>
				<?php
				// หาข้อมูลการชำระ
				$qry_payment = pg_query("select * from \"installGas_edit\" where \"editID\" = '$editID' ");
				$res_payment = pg_fetch_array($qry_payment);
				$payDate = $res_payment["payDate"]; // วันที่จ่าย
				$cash_amount = $res_payment["cash_amount"]; // ยอดเงินสด
				$cheque_amount = $res_payment["cheque_amount"]; // ยอดเช็ค
				$chequeNO = $res_payment["chequeNO"]; // เลขที่เช็ค
				$BankCodeOLD = $res_payment["BankCode"]; // รหัสธนาคาร
				$payerCheque = $res_payment["payerCheque"]; // ผู้จ่ายเช็ค
				$sum_amount = $res_payment["sum_amount"]; // จำนวนเงินรวม
				$discount = $res_payment["discount"]; // ส่วนลด
				$payTrue = $res_payment["payTrue"]; // จำนวนเงินที่จ่ายจริง
				
				// หาข้อมูลเดิม
				$qry_payment_old = pg_query("select * from \"installGas\" where \"gasInvoiceNo\" = '$gasInvoiceNo' ");
				$old_res_payment = pg_fetch_array($qry_payment_old);
				$old_payDate = $old_res_payment["payDate"]; // วันที่จ่าย
				$old_cash_amount = $old_res_payment["cash_amount"]; // ยอดเงินสด
				$old_cheque_amount = $old_res_payment["cheque_amount"]; // ยอดเช็ค
				$old_chequeNO = $old_res_payment["chequeNO"]; // เลขที่เช็ค
				$old_BankCodeOLD = $old_res_payment["BankCode"]; // รหัสธนาคาร
				$old_payerCheque = $old_res_payment["payerCheque"]; // ผู้จ่ายเช็ค
				$old_sum_amount = $old_res_payment["sum_amount"]; // จำนวนเงินรวม
				$old_discount = $old_res_payment["discount"]; // ส่วนลด
				$old_payTrue = $old_res_payment["payTrue"]; // จำนวนเงินที่จ่ายจริง
				
				//------------------------ เปรียบเทียบข้อมูล
					// วันที่จ่าย
					if($payDate != $old_payDate)
					{
						$payDate_style = "style=\"background-color:#FFBBBB;\" title=\"$old_payDate\" ";
					}
					else
					{
						$payDate_style = "";
					}
					
					// เงินสด
					if($cash_amount != $old_cash_amount)
					{
						$cash_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_cash_amount\" ";
					}
					else
					{
						$cash_amount_style = "style=\"text-align:right;\" ";
					}
					
					// เช็ค
					if($cheque_amount != $old_cheque_amount)
					{
						$cheque_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_cheque_amount\" ";
					}
					else
					{
						$cheque_amount_style = "style=\"text-align:right;\" ";
					}
					
					// เลขที่เช็ค
					if($chequeNO != $old_chequeNO)
					{
						$chequeNO_style = "style=\"background-color:#FFBBBB;\" title=\"$old_chequeNO\" ";
					}
					else
					{
						$chequeNO_style = "";
					}
					
					// ธนาคาร
					if($BankCodeOLD != $old_BankCodeOLD)
					{
						// หาชื่อธนาคารเดิม
						$qry_bank_old = pg_query("select \"BankName\" from \"BankInThai\" where \"BankCode\" = '$old_BankCodeOLD' ");
						$old_BankName = pg_fetch_result($qry_bank_old,0);
						
						$BankCodeOLD_style = "style=\"background-color:#FFBBBB;\" title=\"$old_BankName\" ";
					}
					else
					{
						$BankCodeOLD_style = "";
					}
					
					// ผู้จ่ายเช็ค
					if($payerCheque != $old_payerCheque)
					{
						$payerCheque_style = "style=\"background-color:#FFBBBB;\" title=\"$old_payerCheque\" ";
					}
					else
					{
						$payerCheque_style = "";
					}
					
					// จำนวนเงินรวม
					if($sum_amount != $old_sum_amount)
					{
						$sum_amount_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_sum_amount\" ";
					}
					else
					{
						$sum_amount_style = "style=\"text-align:right; ";
					}
					
					// ส่วนลด
					if($discount != $old_discount)
					{
						$discount_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_discount\" ";
					}
					else
					{
						$discount_style = "style=\"text-align:right; ";
					}
					
					// จำนวนเงินที่จ่ายจริง
					if($payTrue != $old_payTrue)
					{
						$payTrue_style = "style=\"text-align:right; background-color:#FFBBBB;\" title=\"$old_payTrue\" ";
					}
					else
					{
						$payTrue_style = "style=\"text-align:right; ";
					}
				//------------------------ จบการ เปรียบเทียบข้อมูล
				?>
				
				<table width="auto" border="0" cellSpacing="1" cellPadding="3" bgcolor="#FFFFFF">
					<tr>
						<td align="right">วันที่จ่าย : </td>
						<td align="left"><input type="text" name="payDate" id="payDate" value="<?php echo $payDate; ?>" size="10" <?php echo $payDate_style; ?> readOnly></td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">เงินสด : </td>
						<td align="left">จำนวน <input type="textbox" name="cash_amount" id="cash_amount" value="<?php echo $cash_amount; ?>" size="10" <?php echo $cash_amount_style; ?> readOnly> บาท</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">เช็ค : </td>
						<td align="left">จำนวน <input type="textbox" name="cheque_amount" id="cheque_amount" value="<?php echo $cheque_amount; ?>" size="10" <?php echo $cheque_amount_style; ?> readOnly> บาท</td>
						<td align="left">เลขที่เช็ค : <input type="textbox" name="chequeNO" id="chequeNO" value="<?php echo $chequeNO; ?>" <?php echo $chequeNO_style; ?> readOnly></td>
						<td align="left">
							ธนาคาร :
							<select name="BankCode" id="BankCode" <?php echo $BankCodeOLD_style; ?> disabled>
								<option value="">เลือกธนาคาร</option>
								<?php
								$qry_bank = pg_query("select * from \"BankInThai\" order by \"BankName\" ");
								while($res_bank = pg_fetch_array($qry_bank))
								{
									$BankCode = $res_bank["BankCode"];
									$BankName = $res_bank["BankName"];
									
									if($BankCode == $BankCodeOLD)
									{
										echo "<option value=\"$BankCode\" selected>$BankName</option>";
									}
									else
									{
										echo "<option value=\"$BankCode\">$BankName</option>";
									}
								}
								?>
							</select>
						</td>
						<td align="left">ผู้จ่ายเช็ค : <input type="textbox" name="payerCheque" id="payerCheque" value="<?php echo $payerCheque; ?>" <?php echo $payerCheque_style; ?> readOnly></td>
					</tr>
					<tr>
						<td align="right">จำนวนเงินรวม : </td>
						<td align="left">จำนวน <input type="textbox" name="sum_amount" id="sum_amount" value="<?php echo $sum_amount; ?>" size="10" <?php echo $sum_amount_style; ?> readOnly> บาท</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">ส่วนลด : </td>
						<td align="left">จำนวน <input type="textbox" name="discount" id="discount" value="<?php echo $discount; ?>" size="10" <?php echo $discount_style; ?> readOnly> บาท</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">จำนวนเงินที่จ่ายจริง : </td>
						<td align="left">จำนวน <input type="textbox" name="payTrue" id="payTrue" value="<?php echo $payTrue; ?>" size="10" <?php echo $payTrue_style; ?> readOnly> บาท</td>
						<td colspan="3"></td>
					</tr>
				</table>
			</center>
			</fieldset>
			
			<br><br>
			<table cellpadding="1" cellspacing="10" align="center">
				<tr align="center">
					<td><b>หมายเหตุการอนุมัติ :</b></td>
				</tr>
				<tr align="center">
					<td>
						<textarea id="remark" name="remark" cols="40" rows="5"><?php echo $remark; ?></textarea>
					</td>
				</tr>
				<tr align="center">
					<td><input type="button" name="appv" id="appv" value="อนุมัติ"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="button" name="notappv" id="notappv" value="ไม่อนุมัติ"></td>
				</tr>
			</table>
			<br><br>
		</td>
	</tr>
</table>
</form>
</center>
</body>

<script>
$('#appv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_edit_detail_install_gas.php',{
		cmd : 'appv',
		remark : $('#remark').val(),
		editID : '<?php echo $editID;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
$('#notappv').click(function(){
	if($('#remark').val() == ""){
		alert('กรุณาระบุหมายเหตุด้วย');
		return false;
	}
	$.post('process_appv_edit_detail_install_gas.php',{
		cmd : 'notappv',
		remark : $('#remark').val(),
		editID : '<?php echo $editID;?>'
	},function(data){
		if(data == 1){
			alert('บันทึกข้อมูลเรียบร้อยแล้ว');
			location.reload();
		}else{
			alert(data);
			location.reload();
		}
	});
});
</script>

</html>