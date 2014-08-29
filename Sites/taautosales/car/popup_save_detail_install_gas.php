<?php
include_once("../include/config.php");
include_once("../include/function.php");

$gasInvoiceNo = pg_escape_string($_GET["gasInvoiceNo"]);

$qry_list = pg_query("select distinct a.\"gasInvoiceNo\", a.\"wh_id\", (select max(c.\"gasInvoiceDate\") from \"CarMove\" c where c.\"gasInvoiceNo\" = a.\"gasInvoiceNo\") as \"gasInvoiceDateMax\"
					from \"CarMove\" a
					where a.\"gasInvoiceNo\" = '$gasInvoiceNo' ");
$res = pg_fetch_array($qry_list);
//$gasInvoiceNo = $res['gasInvoiceNo'];
$gasInvoiceDate = $res['gasInvoiceDateMax'];
$wh_id = $res['wh_id'];

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
    <title>บันทึกรายละเอียดการติดตั้งแก๊ส</title>
	
	<meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function(){
			$("#gasInvoiceDate").datepicker({
				showOn: 'button',
				buttonImage: '../images/calendar.gif',
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				//minDate: 0,
				dateFormat: 'yy-mm-dd'
			});
			
			$("#payDate").datepicker({
				showOn: 'button',
				buttonImage: '../images/calendar.gif',
				buttonImageOnly: true,
				changeMonth: true,
				changeYear: true,
				//minDate: 0,
				dateFormat: 'yy-mm-dd'
			});
		});
		
		function validate() 
		{
			var theMessage = "Please complete the following: \n-----------------------------------\n";
			var noErrors = theMessage
			
			var sum_install_amount = 0.00;
			var sum_oil_amount = 0.00;
			
			if (document.getElementById("gasInvoiceDate").value==""){
				theMessage = theMessage + "\n ->  กรุณาระบุ วันที่ใบแจ้งหนี้/ใบส่งของ ";
			}
			
			for(var i = 1; i <= counter; i++)
			{
				if (document.getElementById("gas_install_date"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ วันที่ติดตั้ง " + i;
				}
				
				if (document.getElementById("car_idno"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ เลขที่ทะเบียนรถในสต๊อก " + i;
				}
				
				if (document.getElementById("car_name"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ รุ่นรถ " + i;
				}
				
				if (document.getElementById("car_num"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ เลขที่ตัวถัง " + i;
				}
				
				if (document.getElementById("send_gas_date"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ วันที่ส่งรถติดตั้งแก๊ส " + i;
				}
				
				if (document.getElementById("res_id"+i).value!="" && document.getElementById("received_date"+i).value==""){ // ถ้ามีเลขที่ใบจอง แต่ไม่มีวันที่ลูกค้ารับรถ
					theMessage = theMessage + "\n ->  กรุณาระบุ วันที่ลูกค้ารับรถ " + i;
				}
				
				if (document.getElementById("gas_deatils"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ รายการติดตั้ง " + i;
				}
				
				if (document.getElementById("install_amount"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ ค่าติดตั้ง " + i;
				}else{
					sum_install_amount = parseFloat(sum_install_amount) + parseFloat(document.getElementById("install_amount"+i).value);
				}
				
				if (document.getElementById("oil_amount"+i).value==""){
					theMessage = theMessage + "\n ->  กรุณาระบุ ค่าน้ำมัน " + i;
				}else{
					sum_oil_amount = parseFloat(sum_oil_amount) + parseFloat(document.getElementById("oil_amount"+i).value);
				}
			}
			
			if((parseFloat(sum_install_amount) + parseFloat(sum_oil_amount)) != parseFloat(document.getElementById("sum_amount").value)){
				theMessage = theMessage + "\n ->  จำนวนเงินรวมไม่ถูกต้อง";
			}

			// If no errors, submit the form
			if (theMessage == noErrors) {
				return true;
			} 
			else
			{
				// If errors were found, show alert message
				alert(theMessage);
				return false;
			}
		}
		
		function cal_pay()
		{
			var cash_amount = document.getElementById("cash_amount").value;
			var cheque_amount = document.getElementById("cheque_amount").value;
			var discount = document.getElementById("discount").value;
			
			document.getElementById("sum_amount").value = parseFloat(parseFloat(cash_amount) + parseFloat(cheque_amount)).toFixed(2);
			document.getElementById("payTrue").value = parseFloat((parseFloat(cash_amount) + parseFloat(cheque_amount)) - parseFloat(discount)).toFixed(2);
		}
	</script>
</head>
<body>
<center>
<h1>บันทึกรายละเอียดการติดตั้งแก๊ส</h1>
<form name="frm1" method="post" action="process_save_detail_install_gas.php">
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
					$qry_installDetail = pg_query("select * from \"CarMove\" where \"gasInvoiceNo\" = '$gasInvoiceNo' ");
					while($res_installDetail = pg_fetch_array($qry_installDetail))
					{
						$i++;
						$auto_id_CarMove = $res_installDetail["auto_id"];
						$car_id = $res_installDetail["car_id"];
						
						// หาระบบแก๊ส
						$qry_GasType = pg_query("select \"GasTypeID\" from \"CarMove\"
											where \"auto_id\" = (select max(\"auto_id\") from \"CarMove\" where \"auto_id\" <> '$auto_id_CarMove' and \"car_id\" = '$car_id' and \"GasTypeID\" is not null) ");
						$GasTypeID = pg_fetch_result($qry_GasType,0);
						
						// หารายการติดตั้ง
						$qry_GasTypeName = pg_query("select \"GasTypeName\" from \"GasType\" where \"GasTypeID\" = '$GasTypeID' ");
						$GasTypeName = pg_fetch_result($qry_GasTypeName,0);
						
						// หาข้อมูลรถเบื่องต้น
						$qry_carDetail = pg_query("select \"car_idno\", \"car_name\", \"car_num\", \"res_id\" from \"Cars\" where \"car_id\" = '$car_id' ");
						$car_idno = pg_fetch_result($qry_carDetail,0); // เลขที่ทะเบียนรถในสต๊อก
						$car_name = pg_fetch_result($qry_carDetail,1); // รุ่นรถ
						$car_num = pg_fetch_result($qry_carDetail,2); // เลขที่ตัวถัง
						//$res_id = pg_fetch_result($qry_carDetail,3); // เลขที่ใบจอง
						
						// หาข้อมูลใบจอง (ไม่เอารายการที่ ยกเลิกการจอง และไม่เอา รายการที่ขายไปแล้ว)
						$qry_res_id = pg_query("select max(\"res_id\") from \"Reserves\" where \"car_id\" = '$car_id' and \"reserve_status\" not in('0', '1') ");
						$res_id = pg_fetch_result($qry_res_id,0); // เลขที่ทะเบียนรถในสต๊อก
						
						// หาข้อมูล วันที่ลูกค้ารับรถ
						$qry_receive_date = pg_query("select \"receive_date\" from \"Reserves\" where \"res_id\" = '$res_id' ");
						$receive_date = pg_fetch_result($qry_receive_date,0); // วันที่ลูกค้ารับรถ
					?>
						<input type="hidden" name="auto_id_CarMove<?php echo $i; ?>" id="auto_id_CarMove<?php echo $i; ?>" value="<?php echo $auto_id_CarMove; ?>">
						<tr bgcolor="#E8E8E8">
							<td align="center">&nbsp;&nbsp;&nbsp; <?php echo $i; ?> &nbsp;&nbsp;&nbsp;</td>
							<td><input type="text" name="gas_install_date<?php echo $i; ?>" id="gas_install_date<?php echo $i; ?>" size="10"></td>
							<td><input type="text" name="car_idno<?php echo $i; ?>" id="car_idno<?php echo $i; ?>" value="<?php echo $car_idno; ?>" readOnly style="background-color:#CCCCCC;"></td>
							<td><input type="text" name="car_name<?php echo $i; ?>" id="car_name<?php echo $i; ?>" value="<?php echo $car_name; ?>" readOnly style="background-color:#CCCCCC;"></td>
							<td><input type="text" name="car_num<?php echo $i; ?>" id="car_num<?php echo $i; ?>" value="<?php echo $car_num; ?>" readOnly style="background-color:#CCCCCC;"></td>
							<td><input type="text" name="eng_cert<?php echo $i; ?>" id="eng_cert<?php echo $i; ?>"></td>
							<td><input type="text" name="send_gas_date<?php echo $i; ?>" id="send_gas_date<?php echo $i; ?>" size="10"></td>
							<td><input type="text" name="received_date<?php echo $i; ?>" id="received_date<?php echo $i; ?>" value="<?php echo $receive_date; ?>" size="10"></td>
							<td><input type="text" name="res_id<?php echo $i; ?>" id="res_id<?php echo $i; ?>" value="<?php echo $res_id; ?>" readOnly style="background-color:#CCCCCC;"></td>
							<td><input type="text" name="gas_deatils<?php echo $i; ?>" id="gas_deatils<?php echo $i; ?>" value="<?php echo $GasTypeName; ?>"></td>
							<td><input type="text" name="install_amount<?php echo $i; ?>" id="install_amount<?php echo $i; ?>" size="10" style="text-align:right;"></td>
							<td><input type="text" name="oil_amount<?php echo $i; ?>" id="oil_amount<?php echo $i; ?>" size="10" style="text-align:right;"></td>
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
				<table width="auto" border="0" cellSpacing="1" cellPadding="3" bgcolor="#FFFFFF">
					<tr>
						<td align="right">วันที่จ่าย : </td>
						<td align="left"><input type="text" name="payDate" id="payDate" size="10"></td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">เงินสด : </td>
						<td align="left">จำนวน <input type="textbox" name="cash_amount" id="cash_amount" value="0.00" size="10" style="text-align:right;" onChange="cal_pay();" onKeyUp="cal_pay();"> บาท</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">เช็ค : </td>
						<td align="left">จำนวน <input type="textbox" name="cheque_amount" id="cheque_amount" value="0.00" size="10" style="text-align:right;" onChange="cal_pay();" onKeyUp="cal_pay();"> บาท</td>
						<td align="left">เลขที่เช็ค : <input type="textbox" name="chequeNO" id="chequeNO"></td>
						<td align="left">
							ธนาคาร :
							<select name="BankCode" id="BankCode">
								<option value="">เลือกธนาคาร</option>
								<?php
								$qry_bank = pg_query("select * from \"BankInThai\" order by \"BankName\" ");
								while($res_bank = pg_fetch_array($qry_bank))
								{
									$BankCode = $res_bank["BankCode"];
									$BankName = $res_bank["BankName"];
									
									echo "<option value=\"$BankCode\">$BankName</option>";
								}
								?>
							</select>
						</td>
						<td align="left">ผู้จ่ายเช็ค : <input type="textbox" name="payerCheque" id="payerCheque"></td>
					</tr>
					<tr>
						<td align="right">จำนวนเงินรวม : </td>
						<td align="left">จำนวน <input type="textbox" name="sum_amount" id="sum_amount" value="0.00" size="10" style="text-align:right; background-color:#CCCCCC;" readOnly> บาท</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">ส่วนลด : </td>
						<td align="left">จำนวน <input type="textbox" name="discount" id="discount" value="0.00" size="10" style="text-align:right;" onChange="cal_pay();" onKeyUp="cal_pay();"> บาท</td>
						<td colspan="3"></td>
					</tr>
					<tr>
						<td align="right">จำนวนเงินที่จ่ายจริง : </td>
						<td align="left">จำนวน <input type="textbox" name="payTrue" id="payTrue" value="0.00" size="10" style="text-align:right; background-color:#CCCCCC;" readOnly> บาท</td>
						<td colspan="3"></td>
					</tr>
				</table>
			</center>
			</fieldset>
			
			<br><br>
			<input type="submit" value="บันทึก" onclick="return validate();">
		</td>
	</tr>
</table>
</form>
</center>
</body>

<script type="text/javascript">
var counter = document.getElementById("rowDetail").value;

$(document).ready(function(){
	for(var j = 1; j <= counter; j++)
	{
		$("#gas_install_date"+j).datepicker({
			showOn: 'button',
			buttonImage: '../images/calendar.gif',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			//minDate: 0,
			dateFormat: 'yy-mm-dd'
		});
		
		$("#send_gas_date"+j).datepicker({
			showOn: 'button',
			buttonImage: '../images/calendar.gif',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			//minDate: 0,
			dateFormat: 'yy-mm-dd'
		});
		
		$("#received_date"+j).datepicker({
			showOn: 'button',
			buttonImage: '../images/calendar.gif',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			//minDate: 0,
			dateFormat: 'yy-mm-dd'
		});
	}
});
</script>

</html>