<?php
include_once("../include/config.php");
include_once("../include/function.php");
	$inv_no = pg_escape_string($_GET['inv_no']);
	
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
		
		// หาจำนวนเงิน
		$qry_money = pg_query("select sum(\"amount\") as \"sum_amount\", sum(\"vat\") as \"sum_vat\"
							from \"InvoiceDetails_account\" where \"inv_no\" = '$inv_no' ");
		$sum_amount = pg_fetch_result($qry_money,0); // มูลค่า ก่อน vat
		$sum_vat = pg_fetch_result($qry_money,1); // ยอดรวม vat
		$sum_all = $sum_amount + $sum_vat; // ยอดรวม
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
		<td  width="130"><b>เลขที่ใบแจ้งหนี้ :</b></td>
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
					
					$sum_all = $amount + $vat;
					
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
					echo "<td align=\"right\">".number_format($sum_all,2)."</td>";
					echo "<td align=\"right\">".number_format($amount,2)."</td>";
					echo "<td align=\"right\">".number_format($vat,2)."</td>";
					echo "</tr>";
				}
				?>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">วันที่ใบเสร็จ : <input type="textbox" name="txt_receipt_date" id="txt_receipt_date"></td>
	</tr>
	<tr>
		<td colspan="2">ชำระโดย : <input type="radio" name="payBy" id="payByCA" value="CA"> เงินสด <input type="radio" name="payBy" id="payBySA" value="SA"> เช็ค</td>
	</tr>
</table>
<br>
<div style="text-align:right">
	<input type="button" name="btn_save" id="btn_save" value="บันทึก"  >
</div>
</body>
</html>
<script>
$(document).ready(function(){
	$("#txt_receipt_date").datepicker({
		showOn: 'button',
		buttonImage: '../images/calendar.gif',
		buttonImageOnly: true,
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd'
	});
});

$('#btn_save').click(function(){
	
	if(document.getElementById("txt_receipt_date").value == '')
	{
		alert('กรุณาระบุ วันที่ใบเสร็จ');
		return false;
	}
	
	if(document.getElementById("payByCA").checked == true)
	{
		var payBy = 'CA';
	}
	else if(document.getElementById("payBySA").checked == true)
	{
		var payBy = 'SA';
	}
	else
	{
		alert('กรุณาระบุ ชำระโดย');
		return false;
	}
	
	$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกใบเสร็จ-ใบกำกับใช่หรือไม่ ?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
					$.post('save_receipt.php',{
						cmd: 'save',
						inv_no: '<?php echo $inv_no; ?>',
						receive_date: $('#txt_receipt_date').val(),
						payBy: payBy
					},
					function(data){
						if(data.success){
							alert(data.message);
							print_doc(data.receipt_no,data.invoice_no);
							print_logs(data.receipt_no);
							print_logs(data.invoice_no);
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

function print_logs(doc_no){
	var str_doc_no = doc_no
	    $.post('save_receipt_reprint_api.php',{
        cmd: 'save_tax_rec_reprint_reason',
		doc_no: str_doc_no,
		reason: ""
    },
    function(data){
        if(data.success){
            //alert(data.message);
			$('#div_dialog').remove();
			//print_doc_new(str_doc_no);
        }else{
            //alert(data.message);
        }
    },'json');
}

function print_doc(receipt_no,invoice_no){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบเสร็จรับเงิน\" onclick=\"javascript:window.open('../report/receipt_account_pdf.php?receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');\"> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบกำกับภาษี\" onclick=\"javascript:window.open('../report/tax_invoice_account_pdf.php?invoice_no="+invoice_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); \">  </div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
			location.reload();
        }
    });
}

function CloseDialogChq(){
    $('#div_prt').remove();
    location.reload();
}
</script>