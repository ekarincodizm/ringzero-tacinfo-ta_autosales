<?php
include_once("../include/config.php");
include_once("../include/function.php");
if(!CheckAuth()){
    exit();
}
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "cash"){
    $date = pg_escape_string($_GET['date']);
    if(empty($date)){
        $date = $nowdate;
    }
	
?>

<div id="div_cash_show">

<div style="float:left"><span style="font-weight:bold; font-size:15px">รับเงินสดประจำวัน</span>
เลือกวันที่
<input type="text" name="txt_cash_select_date" id="txt_cash_select_date" value="<?php echo $date; ?>" style="width:80px; text-align:center">
<input type="button" name="btncashShow" id="btncashShow" value="แสดง">
</div>
<div style="float:right">
<span style="background-color:#FFD9D9; padding:3px; font-size:11px">รายการยกเลิก</span>&nbsp;|&nbsp;
<a href="reports_received_pdf.php?cmd=<?php echo $cmd; ?>&date=<?php echo $date; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a></div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบเสร็จ</td>
    <td>เลขที่ใบจอง</td>
    <td>เลขที่ใบแจ้งหนี้</td>
    <td>ชื่อ/สกุล</td>
    <td>รายการ</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$j = 0;
/*$qry = pg_query("SELECT A.*,B.* FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt 
WHERE A.cancel='FALSE' AND A.o_prndate='$date' AND B.status='CA' ORDER BY user_id ASC ");*/

$qry=pg_query("SELECT A.user_id,A.o_receipt,''::text AS tem_id,B.inv_no,B.service_id,B.amount,A.cancel FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt 
WHERE A.o_prndate='$date' AND B.status='CA' 
UNION ALL 
SELECT A.user_id,A.tem_rec_no,A.tem_id,C.cus_id,B.service_id,B.amount,A.cancel FROM \"TemporaryReceipt\" A inner join \"TemRecDetail\" B on A.tem_rec_no = B.tem_rec_no 
inner join \"TemporaryCustomers\" C on C.tem_id = A.tem_id 
WHERE A.prn_date='$date' AND A.status='CA' 
ORDER BY user_id ,  o_receipt ,  inv_no ASC 
");

while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['user_id'];
    $o_receipt = $res['o_receipt'];
    $inv_no = $res['inv_no'];
    $service_id = $res['service_id'];
        $service_name = GetServicesName($service_id);
    $amount = $res['amount'];
    $cancel = $res['cancel'];
    $tem_id = $res['tem_id'];
    $res_id = "";
    $IDNO = "";
    $cus_id = "";
    
    if(substr($inv_no, 0,3) != "CUS"){
        $qry_inv = @pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' ");
        if($res_inv = @pg_fetch_array($qry_inv)){
            $res_id = $res_inv['res_id'];
            $IDNO = $res_inv['IDNO'];
            $cus_id = $res_inv['cus_id'];
        }
    }else{
        $cus_id = $inv_no;
        $res_id = $tem_id;
        
        $qry_inv = @pg_query("SELECT license_plate FROM \"TemporaryCustomers\" WHERE tem_id='$res_id' ");
        if($res_inv = @pg_fetch_array($qry_inv)){
            $inv_no = $res_inv['license_plate'];
        }
    }
    
    $pre_name = "";
    $cus_name = "";
    $surname = "";
    $qry_cus = @pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = @pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }
    
	if($cancel == "f"){
		if(($user_id != $before_user_id) AND $j != 1){
			echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=3 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"2\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
			$sum = 0;
			$sum += $amount;
		}else{
			$sum += $amount;
		}
		
		$sum_all += $amount;	
	}

	

	if($cancel == "t"){
        echo "<tr class=\"oddcancel\">";
    }elseif($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
	    
?>
    <td><?php echo $o_receipt; ?></td>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></a></td>
    <td><?php echo $inv_no; ?></td>
    <td><?php echo "$pre_name $cus_name $surname"; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
</tr>
<?php
    $before_user_id = $user_id;
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=3 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"2\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#D5FFD5\"><td colspan=5 align=\"right\">รวมทั้งหมด</td><td colspan=1 align=\"right\">".number_format($sum_all,2)."</td></tr>";
}
?>
</table>

</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_cash_select_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $('#btncashShow').click(function(){
        var d = $('#txt_cash_select_date').val();
        $('#div_cash_show').empty();
        $('#div_cash_show').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        $('#div_cash_show').load('reports_received_api.php?cmd=cash&date='+d);
    });
});


function ShowDetail(id){
    $('body').append('<div id="divdetail"></div>');
    $('#divdetail').load('report_reserve_api.php?cmd=showdetail&id='+id);
    $('#divdetail').dialog({
        title: 'รายละเอียดการจอง : '+id,
        resizable: false,
        modal: true,  
        width: 800,
        height: 450,
        close: function(ev, ui){
            $('#divdetail').remove();
        }
    });
}
</script>

<?php
}

elseif($cmd == "cheque2"){
    $date = pg_escape_string($_GET['date']);
    if(empty($date)){
        $date = $nowdate;
    }
?>

<div id="div_cheque_show">

<div style="float:left"><span style="font-weight:bold; font-size:15px">รับเช็คประจำวัน</span>
เลือกวันที่
<input type="text" name="txt_cheque_select_date" id="txt_cheque_select_date" value="<?php echo $date; ?>" style="width:80px; text-align:center">
<input type="button" name="btnchequeShow" id="btnchequeShow" value="แสดง">
</div>
<div style="float:right"><a href="reports_received_pdf.php?cmd=<?php echo $cmd; ?>&date=<?php echo $date; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a></div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่เช็ค</td>
    <td>ธนาคาร/สาขา</td>
    <td>วันที่บนเช็ค</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$j = 0;
$sum = 0;
$qry = pg_query("SELECT * FROM \"Cheques\" WHERE accept='TRUE' AND is_return='FALSE' AND receive_date='$date' ORDER BY accept_by_user ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['accept_by_user'];
    $cheque_no = $res['cheque_no'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_on_cheque = $res['date_on_cheque'];
    $amt_on_cheque = $res['amt_on_cheque'];
	$cancel= $res['cancel'];
	
	if($cancel == "f"){

    if(($user_id != $before_user_id) AND $j != 1){
        echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=2 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td>รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
        $sum = 0;
        $sum += $amt_on_cheque;
    }else{
        $sum += $amt_on_cheque;
    }
    }
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $cheque_no; ?></td>
    <td><?php echo "$bank_name/$bank_branch"; ?></td>
    <td><?php echo $date_on_cheque; ?></td>
    <td align="right"><?php echo number_format($amt_on_cheque,2); ?></td>
</tr>
<?php
    $before_user_id = $user_id;
}

if($j == 0){
    echo "<tr><td colspan=6 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=2 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td>รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
    echo "<tr bgcolor=\"#D5FFD5\" style=\"font-weight:bold\"><td colspan=4>รวมทั้งหมดจำนวน $j ใบ</td></tr>";
}
?>
</table>

</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_cheque_select_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $('#btnchequeShow').click(function(){
        var d = $('#txt_cheque_select_date').val();
        $('#div_cheque_show').empty();
        $('#div_cheque_show').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        $('#div_cheque_show').load('reports_received_api.php?cmd=cheque&date='+d);
    });
});
</script>

<?php
}

elseif($cmd == "receipt" ){
    $date1 = pg_escape_string($_GET['date1']);
	$date2 = pg_escape_string($_GET['date2']);
	$cashier_id = pg_escape_string($_GET['cashier_id']);
	$s_showDate = pg_escape_string($_GET['s_showDate']); // ประเภทวันที่
	$s_showDetail = pg_escape_string($_GET['s_showDetail']); // เงื่อนไขการแสดง
	
	if(empty($date1)){
        $date1 = $nowdate;
    }
	if(empty($date2)){
        $date2 = $nowdate;
    }
	
	//--- กำหนดค่า colspan
	// เงินสด (OC,CA)
	$colspan_1_1 = "7";
	$colspan_1_2 = "4";
	$colspan_1_3 = "6";
	
	// เช็คธนาคาร (OC,CQ)
	$colspan_2_1 = "8";
	$colspan_2_2 = "4";
	$colspan_2_3 = "7";
	
	// รายการส่วนลด (OC,D1)
	$colspan_3_1 = "7";
	$colspan_3_2 = "4";
	$colspan_3_3 = "6";
	
	//echo "$s_showDetail";
	
	if($s_showDetail == "1") // ถ้า แสดงรายการทั้งหมด
	{
		$disabledDate = "disabled";
		$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
		$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
	}
	elseif($s_showDetail == "2") // ถ้า แสดงรายการปกติ
	{
		$disabledDate = "disabled";
		$qwhere = "AND \"cancel\" = false";
		$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
		$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
	}
	elseif($s_showDetail == "3") // ถ้า แสดงรายการที่ยกเลิก
	{
		
	
		$disabledDate = "";
		$qwhere = "AND \"cancel\" = true";
		
		if($s_showDate == "2") // ถ้าแสดงตามวันที่ยกเลิก
		{
			$join = "left join \"CancelReceipt\" C on A.\"o_receipt\" = C.\"c_receipt\"";
			$cdc = ",C.\"c_date\"";
			$sDate = "C.\"c_date\" >= '$date1' AND C.\"c_date\" <= '$date2'";
			
			$joinChq = "LEFT JOIN \"CancelReceipt\" C ON chq_detail.\"receipt_no\" = C.\"c_receipt\"";
			$sDateChq = "C.\"c_date\" >= '$date1' AND C.\"c_date\" <= '$date2'";
		}
		else
		{
			$join = "left join \"CancelReceipt\" C on A.\"o_receipt\" = C.\"c_receipt\"";
			$cdc = ",C.\"c_date\"";
			$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
			
			$joinChq = "LEFT JOIN \"CancelReceipt\" C ON chq_detail.\"receipt_no\" = C.\"c_receipt\"";
			$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
		}
		
		
		//--- กำหนดค่า colspan
		// เงินสด (OC,CA)
		$colspan_1_1 = "8";
		$colspan_1_2 = "5";
		$colspan_1_3 = "7";
		
		// เช็คธนาคาร (OC,CQ)
		$colspan_2_1 = "9";
		$colspan_2_2 = "5";
		$colspan_2_3 = "8";
		
		// รายการส่วนลด (OC,D1)
		$colspan_3_1 = "8";
		$colspan_3_2 = "5";
		$colspan_3_3 = "7";
	}
	else
	{
		$s_showDetail = "1";
		$s_showDate = "1";
		$disabledDate = "disabled";
		$sDate = "A.\"o_date\" >= '$date1' AND A.\"o_date\" <= '$date2'";
		$sDateChq = "chq.\"receive_date\" >= '$date1' AND chq.\"receive_date\" <= '$date2'";
	}
?>

<script>
function check_s_showDetail()
{
	if(document.getElementById("s_showDetail_1").checked == true)
	{
		document.getElementById("s_showDate").value = '1';
		document.getElementById("s_showDate").disabled = true;
	}
	else if(document.getElementById("s_showDetail_2").checked == true)
	{
		document.getElementById("s_showDate").value = '1';
		document.getElementById("s_showDate").disabled = true;
	}
	else if(document.getElementById("s_showDetail_3").checked == true)
	{
		document.getElementById("s_showDate").value = '2';
		document.getElementById("s_showDate").disabled = false;
	}
}
</script>

<div id="div_receipt_show">

<div style="float:left">
	<span style="font-weight:bold; font-size:15px">การออกใบเสร็จประจำวัน</span>
	<select name="s_showDate" id="s_showDate" <?php echo $disabledDate; ?>>
		<option value="1" <?php if($s_showDate == "1"){echo "selected";} ?>>วันที่รับเงิน</option>
		<option value="2" <?php if($s_showDate == "2"){echo "selected";} ?>>วันที่ยกเลิก</option>
	</select>
	<input type="text" name="txt_receipt_select_date1" id="txt_receipt_select_date1" value="<?php echo $date1; ?>" style="width:80px; text-align:center">
	ถึงวันที่
	<input type="text" name="txt_receipt_select_date2" id="txt_receipt_select_date2" value="<?php echo $date2; ?>" style="width:80px; text-align:center">
	<select select name="cashier_type" id="cashier_type" >
								<option value="%">กรุณาเลือกพนักงานเก็บเงิน</option>
								<?php
									$qru_finance = pg_query("select * from \"fuser\" where user_group = '4' order by id_user");
									while($res = pg_fetch_array($qru_finance)){
									
										$id_user = $res['id_user'];
										$fullname = $res['fullname'];
										if ($id_user == $cashier_id){
											echo " <option value=\"$id_user\" selected> $fullname </option>";
										}else{
											echo " <option value=\"$id_user\"> $fullname </option>";
										}
									
									}
								?>
	</select>
	<input type="button" name="btnreceiptShow" id="btnreceiptShow" value="แสดง">
</div>

<div style="float:left">
	<input type="radio" name="s_showDetail" id="s_showDetail_1" value="1" <?php if($s_showDetail == "1"){echo "checked";} ?> onChange="check_s_showDetail();"> แสดงรายการทั้งหมด
	&nbsp;&nbsp;&nbsp;
	<input type="radio" name="s_showDetail" id="s_showDetail_2" value="2" <?php if($s_showDetail == "2"){echo "checked";} ?> onChange="check_s_showDetail();"> แสดงรายการปกติ
	&nbsp;&nbsp;&nbsp;
	<input type="radio" name="s_showDetail" id="s_showDetail_3" value="3" <?php if($s_showDetail == "3"){echo "checked";} ?> onChange="check_s_showDetail();"> แสดงรายการที่ยกเลิก
</div>

<div style="float:right">
<span style="background-color:#FFD9D9; padding:3px; font-size:11px">รายการยกเลิก</span>&nbsp;|&nbsp;
<a href="reports_received_pdf.php?cmd=<?php echo $cmd; ?>&date1=<?php echo $date1; ?>&date2=<?php echo $date2; ?>&cashier_id=<?php echo $cashier_id; ?>&s_showDate=<?php echo $s_showDate; ?>&s_showDetail=<?php echo $s_showDetail; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a></div>
<div style="clear:both"></div>

<div style="font-weight:bold; margin-top:10px">เงินสด (OC,CA)</div>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบเสร็จ</td>
    <td>วันที่รับเงิน</td>
	<?php if($s_showDetail == "3"){echo "<td>วันที่ยกเลิก</td>";} ?>
    <td>เลขที่ใบจอง</td>
    <td>เลขที่สัญญา</td>
    <td>ชื่อ/สกุล</td>
    <td>รายการ</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$j = 0;

$qry = pg_query(" SELECT A.*,B.* $cdc FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt $join
WHERE $sDate AND B.status='CA' AND A.user_id like '$cashier_id' $qwhere ORDER BY A.o_receipt ASC ");

$sum = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['user_id'];
    $o_receipt = $res['o_receipt'];
    $o_date = $res['o_date'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $cancel = $res['cancel'];
	$c_date = $res['c_date'];
    
    $qry_inv = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no'  ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $res_id = $res_inv['res_id'];
       // $IDNO = $res_inv['IDNO'];
        $cus_id = $res_inv['cus_id'];
    }
    
	$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
    if($res_inv = pg_fetch_array($qry_idno)){
        $idno = $res_inv['IDNO'];
    }
	
    $qry_cus = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }

		
	if($cancel == "f"){
		if(($user_id != $before_user_id) AND $j != 1){
			echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=4 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"2\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
			$sum = 0;
			$sum += $amount;
		}else{
			$sum += $amount;
		}
		
		$sum_all += $amount;
	}
    
    if($cancel == "t"){
        echo "<tr class=\"oddcancel\">";
    }elseif($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $o_receipt; ?></td>
    <td><?php echo $o_date; ?></td>
	<?php if($s_showDetail == "3"){echo "<td>$c_date</td>";} ?>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></a></td>
    <td><?php echo $idno; ?></td>
    <td><?php echo "$pre_name $cus_name $surname"; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
</tr>
<?php
    $before_user_id = $user_id;
}

if($j == 0){
    echo "<tr><td colspan=$colspan_1_1 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
	echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=$colspan_1_2 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"2\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#D5FFD5\"><td colspan=$colspan_1_3 align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr>";
}
?>
</table>

<div style="font-weight:bold; margin-top:10px">เช็คธนาคาร (OC,CQ)</div>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบเสร็จ</td>
	<td>เลขที่เช็ค</td>
    <td>วันที่รับเงิน</td>
	<?php if($s_showDetail == "3"){echo "<td>วันที่ยกเลิก</td>";} ?>
    <td>เลขที่จอง</td>
    <td>เลขที่สัญญา</td>
    <td>ชื่อ/สกุล</td>
    <td>รายการ</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$j = 0;
$sum_all = 0;
/*$qry = pg_query(" SELECT A.*,B.* FROM \"Otherpays\" A inner join \"OtherpayDtl\" B on A.o_receipt = B.o_receipt 
WHERE A.cancel='FALSE' AND A.o_prndate='$date' AND B.status='CQ' ORDER BY A.o_receipt ASC ");*/
$qry = pg_query(" SELECT 
					  chq_detail.receipt_no, 
					  chq_detail.prn_date, 
					  chq_detail.inv_no, 
					  chq_detail.cus_amount AS amount,
					  chq_detail.service_id,
					  chq.cancel,
					  chq.receive_date, 
					  chq_detail.res_id,
					  chq.accept_by_user,
					  chq.cheque_no
					  $cdc
					FROM  \"ChequeDetails\" chq_detail
					LEFT JOIN \"Cheques\" chq ON chq_detail.running_no = chq.running_no
					$joinChq
					WHERE $sDateChq
					AND chq.accept_by_user like '$cashier_id' $qwhere
					GROUP BY chq_detail.receipt_no, 
					  chq_detail.prn_date, 
					  chq_detail.inv_no, 
					  amount,
					  chq_detail.service_id,
					  chq.cancel,
					  chq.receive_date, 
					  chq_detail.res_id,
					  chq.accept_by_user,
					  chq.cheque_no
					  $cdc
					ORDER BY chq_detail.receipt_no ASC ");  
					
/*$qry = pg_query("SELECT cheque_no,bank_name,bank_branch,date_on_cheque,SUM(amt_on_cheque) AS amt_on_cheque ,accept_by_user FROM \"Cheques\" WHERE accept='TRUE' AND is_return='FALSE' AND receive_date='$date' 
GROUP BY cheque_no,bank_name,bank_branch,date_on_cheque,accept_by_user ORDER BY accept_by_user,cheque_no ASC ");		*/			

			
$sum = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['accept_by_user'];
    $o_receipt = $res['receipt_no'];
    $o_date = $res['receive_date'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $cancel = $res['cancel'];
	$cheque_no = $res['cheque_no'];
	$c_date = $res['c_date'];
    
	//หาชื่อลูกค้า และ เลขที่ใบจอง ทั้งหมด
    $qry_inv = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no'  ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $res_id = $res_inv['res_id'];
       // $IDNO = $res_inv['IDNO'];
        $cus_id = $res_inv['cus_id'];
    }
	
	$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
    if($res_inv = pg_fetch_array($qry_idno)){
        $idno = $res_inv['IDNO'];
    }
    
    $qry_cus = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }
	

	if($cancel == "f"){
		if(($user_id != $before_user_id) AND $j != 1){
			echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=4 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
			$sum = 0;
			$sum += $amount;
		}else{
			$sum += $amount;
		}
		$sum_all += $amount;
	}
	
    if($cancel == "t"){
        echo "<tr class=\"oddcancel\">";
    }elseif($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $o_receipt; ?></td>
	<td><?php echo $cheque_no; ?></td>
    <td><?php echo $o_date; ?></td>
	<?php if($s_showDetail == "3"){echo "<td>$c_date</td>";} ?>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></a></td> 
    <td><?php echo $idno; ?></td>
    <td><?php echo "$pre_name $cus_name $surname"; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
</tr>
<?php
    $before_user_id = $user_id;
}

if($j == 0){
    echo "<tr><td colspan=$colspan_2_1 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=$colspan_2_2 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#D5FFD5\"><td colspan=$colspan_2_3 align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr>";
}
?>
</table>

<div style="font-weight:bold; margin-top:10px">รายการส่วนลด (OC,D1)</div>
<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบเสร็จ</td>
    <td>วันที่รับเงิน</td>
	<?php if($s_showDetail == "3"){echo "<td>วันที่ยกเลิก</td>";} ?>
    <td>เลขที่ใบจอง</td>
    <td>เลขที่สัญญา</td>
    <td>ชื่อ/สกุล</td>
    <td>รายการ</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$j = 0;
$sum_all = 0 ;
$qry = pg_query(" SELECT A.*,B.* $cdc FROM \"Discountpays\" A inner join \"DiscountpayDtl\" B on A.o_receipt = B.o_receipt $join
WHERE $sDate AND A.user_id like '$cashier_id' $qwhere ORDER BY A.o_receipt ASC ");

$sum = 0;
while($res = pg_fetch_array($qry)){
    $j++;
    $user_id = $res['user_id'];
    $o_receipt = $res['o_receipt'];
    $o_date = $res['o_date'];
    $inv_no = $res['inv_no'];
    $amount = $res['amount'];
    $service_id = $res['service_id'];
    $service_name = GetServicesName($service_id);
    $cancel = $res['cancel'];
	$c_date = $res['c_date'];
    
    $qry_inv = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' ");
    if($res_inv = pg_fetch_array($qry_inv)){
        $res_id = $res_inv['res_id'];
       // $IDNO = $res_inv['IDNO'];
        $cus_id = $res_inv['cus_id'];
    }
    
	$qry_idno = pg_query("SELECT * FROM \"Reserves\" WHERE res_id = '$res_id' AND reserve_status <>'0' ");
    if($res_inv = pg_fetch_array($qry_idno)){
        $idno = $res_inv['IDNO'];
    }
	
    $qry_cus = pg_query("SELECT * FROM \"Customers\" WHERE cus_id='$cus_id' ");
    if($res_cus = pg_fetch_array($qry_cus)){
        $pre_name = $res_cus['pre_name'];
        $cus_name = $res_cus['cus_name'];
        $surname = $res_cus['surname'];
    }

       //echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=3 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
		
	if($cancel == "f"){
		if(($user_id != $before_user_id) AND $j != 1){
			echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=4 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"2\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
			$sum = 0;
			$sum += $amount;
		}else{
			$sum += $amount;
		}
		
		$sum_all += $amount;
	}
    
    if($cancel == "t"){
        echo "<tr class=\"oddcancel\">";
    }elseif($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $o_receipt; ?></td>
    <td><?php echo $o_date; ?></td>
	<?php if($s_showDetail == "3"){echo "<td>$c_date</td>";} ?>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></a></td>
    <td><?php echo $idno; ?></td>
    <td><?php echo "$pre_name $cus_name $surname"; ?></td>
    <!-- <td><?php echo $service_name; ?></td>  -->
	<td><?php echo "ส่วนลดที่ให้ลูกค้า"; ?> (<?php echo $service_name; ?>)</td>  
	
    <td align="right"><?php echo number_format($amount,2); ?></td>
</tr>
<?php
    $before_user_id = $user_id;
}

if($j == 0){
    echo "<tr><td colspan=$colspan_3_1 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
	echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=$colspan_3_2 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"2\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#D5FFD5\"><td colspan=$colspan_3_3 align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr>";
}
?>
</table>

</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_receipt_select_date1").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
	$("#txt_receipt_select_date2").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $('#btnreceiptShow').click(function(){
        var d1 = $('#txt_receipt_select_date1').val();
		var d2 = $('#txt_receipt_select_date2').val();
		var cash_id = $('#cashier_type').val();
		var s_showDate = $('#s_showDate').val(); // ประเภทวันที่
		var s_showDetail; // เงื่อนไขการแสดง
		
		if(document.getElementById("s_showDetail_1").checked == true)
		{
			s_showDetail = '1';
		}
		else if(document.getElementById("s_showDetail_2").checked == true)
		{
			s_showDetail = '2';
		}
		else if(document.getElementById("s_showDetail_3").checked == true)
		{
			s_showDetail = '3';
		}
		
		if (d1>d2){
			alert ("กรุณาเลือกช่วงวันที่ให้ถูกต้อง วันที่แรกต้องน้อยกว่าหรือเท่ากับวันที่สุดท้าย");
			return false;
		}
		$('#div_receipt_show').empty();
        $('#div_receipt_show').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        //$('#div_receipt_show').load('reports_received_api.php?cmd=receipt&date='+d);
		$('#div_receipt_show').load('reports_received_api.php?cmd=receipt&cashier_id='+cash_id+'&date1='+d1+'&date2='+d2+'&s_showDate='+s_showDate+'&s_showDetail='+s_showDetail);
		
		
    });
});
</script>
<?php
}

elseif($cmd == "cheque"){
    $date = pg_escape_string($_GET['date']);
    if(empty($date)){
        $date = $nowdate;
    }
?>

<div id="div_cheque_show">

<div style="float:left"><span style="font-weight:bold; font-size:15px">รับเช็คประจำวัน</span>
เลือกวันที่
<input type="text" name="txt_cheque_select_date" id="txt_cheque_select_date" value="<?php echo $date; ?>" style="width:80px; text-align:center">
<input type="button" name="btnchequeShow" id="btnchequeShow" value="แสดง">
</div>
<div style="float:right"><a href="reports_received_pdf.php?cmd=<?php echo $cmd; ?>&date=<?php echo $date; ?>" target="_blank"><span style="font-weight:bold; font-size:14px; color:#006AD5"><img src="../images/print.png" border="0" width="16" height="16"> Print PDF</span></a></div>
<div style="clear:both"></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลขที่เช็ค</td>
    <td>ธนาคาร</td>
	<td>สาขา</td>
    <td>วันที่บนเช็ค</td>
    <td>เลขที่จอง</td>
    <td>ชื่อ/สกุล</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$j = 0;
 $sum = 0;
$qry = pg_query("SELECT cheque_no,bank_name,bank_branch,date_on_cheque,SUM(amt_on_cheque) AS amt_on_cheque ,accept_by_user,cancel FROM \"Cheques\" WHERE accept='TRUE' AND is_return='FALSE' AND receive_date='$date' 
GROUP BY cheque_no,bank_name,bank_branch,date_on_cheque,accept_by_user,cancel ORDER BY accept_by_user,cheque_no ASC ");

while($res = pg_fetch_array($qry)){
    $j++;
    $cheque_no = $res['cheque_no'];
	$user_id = $res['accept_by_user'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];
    $date_on_cheque = $res['date_on_cheque'];
    $amt_on_cheque = $res['amt_on_cheque'];
    $cancel = $res['cancel'];
    $qry_view = pg_query("SELECT * FROM \"VChequeDetail\" WHERE cheque_no='$cheque_no'  AND receive_date='$date' ");
    if($res_view = pg_fetch_array($qry_view)){
        $full_name = $res_view['full_name'];
        $res_id = $res_view['res_id'];
    }
    
	if($cancel == "f"){
		if(($user_id != $before_user_id) AND $j != 1){
			echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=3 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
			$sum = 0;
			$sum += $amt_on_cheque;
		}else{
			$sum += $amt_on_cheque;
		}
		$sum_all += $amt_on_cheque;
	}
		
	if($cancel == "t"){
        echo "<tr class=\"oddcancel\">";
	}elseif($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $cheque_no; ?></td>
    <td><?php echo "$bank_name"; ?></td>
	<td><?php echo "$bank_branch"; ?></td>
    <td><?php echo $date_on_cheque; ?></td>
    <td><a href="javascript:ShowDetail('<?php echo $res_id; ?>')"><u><?php echo $res_id; ?></a> </td>  
    <td><?php echo "$full_name"; ?></td>
    <td align="right"><?php echo number_format($amt_on_cheque,2); ?></td>
</tr>

<?php
    $before_user_id = $user_id;
}

if($j == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#FFFFDF\"><td colspan=3 align=left>User ชื่อ : ".GetUserName($before_user_id)." | ".GetOfficeName($before_user_id)."</td><td colspan=\"3\">รวมยอดเงิน</td><td align:right>".number_format($sum,2)."</td></tr>";
    echo "<tr style=\"text-align:right; font-weight:bold; background-color:#D5FFD5\"><td colspan=6 align=\"right\">รวมทั้งหมด</td><td align=\"right\">".number_format($sum_all,2)."</td></tr>";
}
?>

</table>

</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#txt_cheque_select_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $('#btnchequeShow').click(function(){
        var d = $('#txt_cheque_select_date').val();
        $('#div_cheque_show').empty();
        $('#div_cheque_show').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="Please Wait">');
        $('#div_cheque_show').load('reports_received_api.php?cmd=cheque&date='+d);
    });
});
</script>

<?php
}
?>