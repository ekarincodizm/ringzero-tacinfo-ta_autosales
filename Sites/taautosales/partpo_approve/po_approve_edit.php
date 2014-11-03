<?php
include_once ("../include/config.php");
include_once ("../include/function.php");
/*
 if(!CheckAuth()){
 header("Refresh: 0; url=../index.php");`
 exit();
 }
 */
$page_title = "อนุมัติสั่งซื้ออะไหล่/อุปกรณ์";
$parts_pocode = pg_escape_string($_GET["parts_pocode"]);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="txt/html; charset=utf-8" />
    <title><?php echo $company_name; ?> - <?php echo $page_title; ?></title>
    <LINK href="../images/styles.css" type=text/css rel=stylesheet>

    <link type="text/css" href="../images/jqueryui/css/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../images/jqueryui/js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="../images/jqueryui/js/jquery-ui-1.8.16.custom.min.js"></script>

</head>
<body>

<div class="roundedcornr_box" style="width:600px">
	<div class="roundedcornr_content">

<?php
	// include_once ("../include/header_popup.php");
	
	function read_Parts_Unit($unitid){ //Return UnitName
		$parts_unit_strQuery = "
			SELECT 
				\"unitname\"
			FROM 
				\"parts_unit\"
			WHERE
				\"unitid\" = '".$unitid."'
		";
		$parts_unit_query = pg_query($parts_unit_strQuery);
		while($parts_unit_result = pg_fetch_array($parts_unit_query)){
			echo $parts_unit_result["unitname"];
		}
	}
?>
	<br/>
	<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
		<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
		    <td>รหัสสินค้า</td>
		    <td>ชื่อสินค้า</td>
		    <td>รายละเอียดสินค้า</td>
		    <td>จำนวน</td>
		    <td>หน่วย</td>
		    <td>ราคา/หน่วย</td>
		    <td>จำนวนเงิน</td>
		</tr>
		<?php
		
		$purchaseOrderPartsDetails_strQuery = "
			SELECT 
				* 
			FROM 
				\"PurchaseOrderPartsDetails\" 
			JOIN 
				\"parts\"
			ON
				\"parts\".code = \"PurchaseOrderPartsDetails\".parts_code
			WHERE 
				\"PurchaseOrderPartsDetails\".parts_pocode = '".$parts_pocode."'
			ORDER BY auto_id ASC 
		";
		$purchaseOrderPartsDetails_query = pg_query($purchaseOrderPartsDetails_strQuery);
		while($purchaseOrderPartsDetails_result = pg_fetch_array($purchaseOrderPartsDetails_query)){
		    $j++;
		    if($j%2==0){
		        echo "<tr class=\"odd\">";
		    }else{
		        echo "<tr class=\"even\">";
		    }
		?>
		    <td><?php echo $purchaseOrderPartsDetails_result['parts_code']; ?></td>
		    <td><?php echo $purchaseOrderPartsDetails_result['name']; ?></td>
		    <td><?php echo $purchaseOrderPartsDetails_result['details']; ?></td>
		    <td align="center"><?php echo $purchaseOrderPartsDetails_result['quantity']; ?></td>
		    <td align="center"><?php echo read_Parts_Unit($purchaseOrderPartsDetails_result['unit']); ?></td>
		    <td align="right"><?php echo number_format($purchaseOrderPartsDetails_result['costperunit'], 2); ?></td>
		    <td align="right"><?php echo number_format($purchaseOrderPartsDetails_result['total'], 2); ?></td>
		    
		</tr>
		<?php
		}
		?>
		</table>
		<br />
<?php
		$PartsApproved_strQuery = "
			SELECT 
				* 
			FROM 
				\"PurchaseOrderPart\" 
			WHERE 
				parts_pocode = '".$parts_pocode."'
			ORDER BY parts_pocode ASC 
		";
		$PartsApproved_query = pg_query($PartsApproved_strQuery);
		while($PartsApproved_result = pg_fetch_array($PartsApproved_query)){
?>
		
			<div style="width: 55%; float: left; margin-left: 45%; height: 30px;">
				<!-- PO type -->
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>เงินรวมก่อนหักส่วนลด : </b>
				</div>
				<div style="width: 28%; float: left; margin-top: 1.2%; text-align: right;">
					<span id="dsubtotal"><?php echo number_format(($PartsApproved_result["subtotal"]), 2); ?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 45%; float: left; height: 30px; ">
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>%ส่วนลด : </b>
				</div>
				<div style="width: 23%; float: left; margin-top: 1.2%; text-align: right;">
					<span name="pcdiscount" id="pcdiscount"><?php echo number_format(($PartsApproved_result["pcdiscount"]*100.0), 2); ?></span>
				</div>
				<div style="width: 5%; float: left; margin-top: 1.2%;">
				</div>
				<div style="clear: both;"></div>
			</div>
			
			<div style="width: 55%; float: left; height: 30px; ">
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนเงินส่วนลด :</b>
				</div>
				<div style="width: 28%; float: left; margin-top: 1.2%; text-align: right;">
					<span name="discount" id="discount"><?php echo number_format(($PartsApproved_result["discount"]), 2); ?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 55%; float: left; margin-left: 45%; height: 30px; ">
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนเงินรวมก่อนภาษีมูลค่าเพิ่ม :</b>
				</div>
				<div style="width: 28%; float: left; margin-top: 1.2%; text-align: right;">
					<span id="vsubtotal"><?php echo number_format(($PartsApproved_result["bfv_total"]), 2); ?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
			
			
			<div style="width: 45%; float: left; height: 30px; ">
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>%ภาษีมูลค่าเพิ่ม :</b>
				</div>
				<div style="width: 23%; float: left; margin-top: 1.2%; text-align: right; ">
					<span name="pcvat" id="pcvat" ><?php echo number_format(($PartsApproved_result["pcvat"]*100.0), 2); ?></span>
				</div>
				<div style="width: 5%; float: left; margin-top: 1.2%;">
				</div>
				<div style="clear: both;"></div>
			</div>
			
			<div style="width: 55%; float: left; height: 30px; ">
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนภาษี :</b>
				</div>
				<div style="width: 28%; float: left; margin-top: 1.2%; text-align: right;">
					<span id="vat"><?php echo number_format(($PartsApproved_result["vat"]), 2); ?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
		
		
			<div style="width: 55%; float: left; margin-left: 45%; height: 30px; ">
				<div style="width: 70%; float: left; text-align: right; margin-right: 2%; margin-top: 1.2%;">
					<b>จำนวนรวมสุทธิ :</b>
				</div>
				<div style="width: 28%; float: left; margin-top: 1.2%; text-align: right;">
					<span id="nettotal"><?php echo number_format(($PartsApproved_result["nettotal"]), 2); ?></span>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
<?php
		}
		$PartsApproved_strQuery = "
			SELECT 
				* 
			FROM 
				\"PartsApproved\" 
			WHERE 
				code = '".$parts_pocode."'
			ORDER BY code ASC 
		";
		$PartsApproved_query = pg_query($PartsApproved_strQuery);
		while($PartsApproved_result = pg_fetch_array($PartsApproved_query)){
?>
			<div style="width: 100%; float: left; height: 30px; ">
				<div>
					<td align="left"><b>หมายเหตุ  : <?php echo $PartsApproved_result["user_note"]; ?></b></td>
				</div>
			</div>
<?php
		}
?>
			<!-- <div style="margin-top:10px">
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr style="font-weight:bold">
			    <td colspan="4" width="50%" align="right">รวม</td>
			    <td align="right" width="15%"><span id="span_sum_all_price">0</span></td>
			    <td align="right" width="10%"><span id="span_sum_all_vat">0</span></td>
			    <td align="right" width="20%"><span id="span_sum_all_all">0</span></td>
			</tr>
			</table>
			</div> -->
			
		<div style="clear:both"></div>
		<div class="linedotted"></div>
			
		
		<div style="margin-top:10px" align="center"><b>บันทึกการอนุมัติ:</b><br /><textarea name="appr_note" id="appr_note" cols="70" rows="3"></textarea></div>
		<div style="width:300px; margin: 0 auto; padding: 15px 0 0 0">
		    <div style="float:left">
		<input type="button" name="btnApprove" id="btnApprove" value="อนุมัติ" alt="อนุมัติ" title="อนุมัติ" />
		    </div>
		    <div style="float:right">
		<input type="button" name="btnApproveCancel" id="btnApproveCancel" value="ไม่อนุมัติ" alt="ไม่อนุมัติ" title="ไม่อนุมัติ" />
		    </div>
		</div>
	
	
	</div>
</div>

<script type="text/javascript">
	var parts_unit = new Array();
<?php
	$sqlStr_parts_unit = "
		SELECT unitname
		FROM parts_unit;
	";
	$query_part_unit = pg_query($sqlStr_parts_unit);
	while($res_parts_unit = pg_fetch_array($query_part_unit)){ //Query Unit Name For checking that There are already Added the Unit Name or Not
?>
		parts_unit.push("<?php echo $res_parts_unit["unitname"];?>");
<?php
	}
?>
</script>
<script type="text/javascript">
	
	var ApproveStatus = ""; 
	
	$('#btnApproveCancel').click(function() {
		ApproveStatus = 0;
		validate();
	});
	
	$('#btnApprove').click(function() {
		ApproveStatus = 2;
		validate();
	}); 
	
	function validate(){
		var chk = 0;
		var msg = "Error! \n";
		
		if ($('#appr_note').val() == "") {
			msg += "กรุณาระบุ รายละเอียด บันทึกการอนุมัติ \n";
			chk++;
		}

		if (chk > 0) {
			alert(msg);
			return false;
		} else {
			
			//Make Confirm Alert
			if(ApproveStatus == 0){
				if(!confirm('คุณต้องการที่จะยืนยันทำรายการ ไม่อนุมัติ หรือไม่')){
					return false;
				} 
			}
			else if(ApproveStatus == 2){
				if(!confirm('คุณต้องการที่จะยืนยันการอนุมัติหรือไม่')){
					return false;
				} 
			}
			
			
			//Send AJAX Request: HTTP POST: For Record PartsApprove 's Products
			$.post('po_approve_save.php', {
				parts_pocode : "<?php echo $parts_pocode; ?>",
				approve_status : ApproveStatus,
				appr_note : $('#appr_note').val() //This is 2nd Parameter -- Send Post Variables
			}, function(data) {
				if (data.success) { //If Success, Will be recorded
					alert(data.message);
					console.log(data.message);
					// window.opener.document.getElementById("updatelist_p_unitid").click();
					// window.close();
					location.reload();
				} else { //If Failed, Will not be recorded
					alert(data.message);
					console.log(data.message);
				}
			}, 'json');
		}
	}
	
</script>

</body>
</html>