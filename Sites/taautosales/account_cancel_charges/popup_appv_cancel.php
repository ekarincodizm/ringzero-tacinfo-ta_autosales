<?php
include_once("../include/config.php");
include_once("../include/function.php");

	$cancelID = pg_escape_string($_GET['cancelID']);
	
	// หาเลขที่ใบแจ้งหนี้และเหตุผลที่ขอยกเลิก
	$qry_inv = pg_query("select \"inv_no\", \"doerNote\" from \"Invoices_account_cancel\" where \"cancelID\" = '$cancelID' ");
	$inv_no = pg_fetch_result($qry_inv,0);
	$doerNote = pg_fetch_result($qry_inv,1);
	
	$qry = pg_query("SELECT * FROM \"Invoices_account\" WHERE \"inv_no\" = '$inv_no' ");
	while($res = pg_fetch_array($qry))
	{
		$cus_id = $res['cus_id']; // รหัสลูกค้า
		$inv_date = $res['inv_date']; // วันที่ตั้งหนี้
		$maturity_date = $res['maturity_date']; // วันที่ครบกำหนดชำระ
		$user_id = $res['user_id']; // รหัสพนักงานที่ทำรายการ
		$doer_stamp = $res['doer_stamp']; // วันเวลาที่ทำรายการ
		$chargesType = $res['chargesType']; // ประเภท ค่าใช้จ่าย
		
		// หาชื่อสกุลลูกค้า
		$qry_nameCus = pg_query("select * from \"Customers\" where \"cus_id\" = '$cus_id' ");
		$res_nameCus = pg_fetch_array($qry_nameCus);
		$pre_name = $res_nameCus['pre_name']; // คำนำหน้า
		$cus_name = $res_nameCus['cus_name']; // ชื่อ
		$surname = $res_nameCus['surname']; // สกุล
		$fullnameCus = "$pre_name$cus_name $surname"; // ชื่อเต็ม ลูกค้า
		
		// หาชื่อพนักงาน
		$qry_nameUser = pg_query("select \"fullname\" from \"fuser\" where \"id_user\" = '$user_id' ");
		$fullnameUser = pg_fetch_result($qry_nameUser,0);
		
		// ประเภท ค่าใช้จ่าย
		if($chargesType == "P"){$chargesTypeName = "ค่าสินค้า";}
		elseif($chargesType == "S"){$chargesTypeName = "ค่าบริการ";}
		else{$chargesTypeName = "";}
	}
	
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script type="text/javascript" src="../images/jqueryui/js/jquery.numberformatter-1.2.4.min.js"></script>
	<script type="text/javascript" src="../images/jqueryui/js/jshashset-3.0.js"></script>
	
</head>
<body>
<table cellpadding="3" cellspacing="0" border="0" width="100%">
	<tr>
		<td  width="135"><b>เลขที่ใบแจ้งหนี้ :</b></td>
		<td><b><?php echo $inv_no; ?></b></td>
	</tr>
	<tr>
		<td>ประเภท ค่าใช้จ่าย :</td>
		<td ><?php echo $chargesTypeName; ?></td>
	</tr>
	<tr>
		<td>วันที่ตั้งหนี้ :</td>
		<td ><?php echo $inv_date; ?></td>
	</tr>
	<tr>
		<td>วันที่ครบกำหนดชำระ :</td>
		<td><?php echo $maturity_date; ?></td>
	</tr>
	<tr>
		<td>ชื่อ-สกุลลูกค้า :</td>
		<td><?php echo $fullnameCus ?></td>
	</tr>
	<tr>
		<td colspan="2"><div style="margin-top:5px; line-height:25px; border:1px dashed #C0C0C0; background-color:#FFFFE8"></td>
	</tr>
	<tr>
		<td colspan="2">รายละเอียด</td>
	</tr>
	<tr>
		<td colspan="2">
			<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
				<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
					<th>no.</th>
					<th>รายการ</th>
					<th>ราคา/หน่วย</th>
					<th>จำนวน</th>
					<th>ยอดรวม</th>
					<th>มูลค่า</th>
					<th>vat</th>
				</tr>
				<?php
				$j = 0;
				$qry = pg_query("SELECT * FROM \"InvoiceDetails_account\" WHERE \"inv_no\" = '$inv_no' AND \"cancel\" = false ");
				while($res = pg_fetch_array($qry))
				{
					$j++;
					$auto_id = $res["auto_id"];
					$product_id = $res["product_id"];
					$service_id = $res["service_id"];
					$unitPriceIncludeVat = $res["unitPriceIncludeVat"];
					$unit = $res["unit"];
					$amount = $res["amount"];
					$vat = $res["vat"];
					
					$sum_amount_vat = $amount + $vat; // ยอดรวม
					
					$sum_amount += $amount; // มูลค่ารวมทั้งหมด
					$sum_vat += $vat; // vat รวมทั้งหมด
					$sum_all += $sum_amount_vat; // มูลค่ารวม vat ทั้งหมด
					
					// หาชื่อรายการ
					if($chargesType == "P"){$qry_listName = pg_query("select \"name\" from \"Products\" where \"product_id\" = '$product_id' ");}
					elseif($chargesType == "S"){$qry_listName = pg_query("select \"name\" from \"Services\" where \"service_id\" = '$service_id' ");}
					else{$qry_listName = "";}
					$listName = pg_fetch_result($qry_listName,0);
					
					if($j%2==0){
						echo "<tr class=\"odd\">";
					}else{
						echo "<tr class=\"even\">";
					}
	
					echo "<td align=\"center\">$j</td>";
					echo "<td align=\"left\">$listName</td>";
					echo "<td align=\"right\">".number_format($unitPriceIncludeVat,2)."</td>";
					echo "<td align=\"center\">$unit</td>";
					echo "<td align=\"right\">".number_format($sum_amount_vat,2)."</td>";
					echo "<td align=\"right\">".number_format($amount,2)."</td>";
					echo "<td align=\"right\">".number_format($vat,2)."</td>";
					echo "</tr>";
				}
				?>
				<tr class="odd" style="background-color:#FFCCCC;">
					<td align="right" colspan="4"><b>รวม</b></td>
					<td align="right"><b><?php echo number_format($sum_all,2); ?></b></td>
					<td align="right"><b><?php echo number_format($sum_amount,2); ?></b></td>
					<td align="right"><b><?php echo number_format($sum_vat,2); ?></b></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>หมายเหตุการขอยกเลิก : </td><td><textarea name="cancel_note" id="cancel_note" cols="60" rows="4" readOnly style="background-color:#CCCCCC;"><?php echo $doerNote; ?></textarea></td>
	</tr>
	<tr>
		<td>หมายเหตุการอนุมัติ : </td><td><textarea name="appv_note" id="appv_note" cols="60" rows="4"></textarea></td>
	</tr>
</table>
<br>
<div style="text-align:right">
	<input type="button" name="btn_appv" id="btn_appv" value="อนุมัติ">
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" name="btn_ditappv" id="btn_ditappv" value="ไม่อนุมัติ">
</div>
</body>
</html>
<script>
// อนุมัติ
$('#btn_appv').click(function(){

	if(document.getElementById("appv_note").value == '')
	{
		alert('กรุณาระบุ หมายเหตุการอนุมัติ');
		return false;
	}
	
	$('body').append('<div id="divdialogconfirm"></div>');
	$("#divdialogconfirm").text('ต้องการอนุมัติยกเลิกใช่หรือไม่ ?');
	$("#divdialogconfirm").dialog({
		title: 'ยืนยัน อนุมัติ',
		resizable: false,
		height:140,
		modal: true,
		buttons:{
			"ใช่": function(){
				$.post('process_cancel_charges.php',{
					cmd: 'appv',
					appvStatus: '1',
					cancelID: '<?php echo $cancelID; ?>',
					appv_note: $('#appv_note').val()
				},
				function(data){
					if(data.success){
						alert(data.message);
						$('#divdialogconfirm').remove();
						location.reload();
					}else{
						alert(data.message);
					}
				},'json');
			},
			"ไม่ใช่": function(){
				$( this ).dialog( "close" );
			}
		}
	});
});

// ไม่อนุมัติ
$('#btn_ditappv').click(function(){

	if(document.getElementById("appv_note").value == '')
	{
		alert('กรุณาระบุ หมายเหตุการอนุมัติ');
		return false;
	}
	
	$('body').append('<div id="divdialogconfirm"></div>');
	$("#divdialogconfirm").text('ต้องการไม่อนุมัติใช่หรือไม่ ?');
	$("#divdialogconfirm").dialog({
		title: 'ยืนยัน ไม่อนุมัติ',
		resizable: false,
		height:140,
		modal: true,
		buttons:{
			"ใช่": function(){
				$.post('process_cancel_charges.php',{
					cmd: 'appv',
					appvStatus: '0',
					cancelID: '<?php echo $cancelID; ?>',
					appv_note: $('#appv_note').val()
				},
				function(data){
					if(data.success){
						alert(data.message);
						$('#divdialogconfirm').remove();
						location.reload();
					}else{
						alert(data.message);
					}
				},'json');
			},
			"ไม่ใช่": function(){
				$( this ).dialog( "close" );
			}
		}
	});
});
</script>