<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$id = $_GET['id'];

if(empty($id)){
    echo "ไม่พบ Res ID";
    exit;
}

$qry_res = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$id' ");
if($res_res = pg_fetch_array($qry_res)){
    
    $cus_id = $res_res['cus_id'];
    $car_id = $res_res['car_id'];
    $car_id = $res_res['car_id'];
    $product_id = $res_res['product_id'];  
	$reserve_color = $res_res['reserve_color']; 
	$receive_date = $res_res['receive_date'];
	
    /*$remark = $res_res['remark'];
        if(empty($car_id)){
            $arr_remark = explode("\n",$remark);
            $arr_remark1 = explode("=",$arr_remark[0]);
            $arr_remark2 = explode("=",$arr_remark[1]);
        }*/
		
    $down_price = $res_res['down_price'];
    $car_price = $res_res['car_price'];
    $num_install = $res_res['num_install'];
    $installment = $res_res['installment'];
    $finance_price = $res_res['finance_price'];
    $finance_cus_id = $res_res['finance_cus_id'];
    
    $type_insure = $res_res['type_insure'];
    $use_radio = $res_res['use_radio'];
    $user_id = $res_res['user_id'];
	$remark = $res_res['remark'];
}
/*
$qry_resd = pg_query("SELECT SUM(amount) as sumamount FROM \"VAccPayment\" WHERE res_id='$id' AND o_receipt IS NOT NULL ");
if($res_resd = pg_fetch_array($qry_resd)){
    $appointment_amt = $car_price-$res_resd['sumamount'];
}else{
    $appointment_amt = $car_price;
}
*/
$user_name = GetUserName($user_id);
$cus_name = GetCusName($cus_id);
$finance_cus_name = GetCusName($finance_cus_id);

$appointment_amt = $down_price;
$qry = pg_query("SELECT telephone FROM \"Customers\" WHERE cus_id='$cus_id' ");
if($res = pg_fetch_array($qry)){
    $telephone=$res['telephone'];
}
?>

<div id = "div_display_data">
<div style="float:left">
<b>ผู้จอง : </b><?php echo $cus_name; ?>
<br>
<b>เบอร์โทรติดต่อ : </b><?php echo $telephone; ?>
<br>
<b>ผู้รับจอง : </b><?php echo $user_name; ?>
</div>
<div style="float:right">

<input style="font-size:11px" type="button" name="btnAddOtherMoney" id="btnAddOtherMoney" value="ตั้งค่าใช้จ่ายอื่นๆ">
<input style="font-size:11px" type="button" name="btnAddGiveAway" id="btnAddGiveAway" value="ของแถม">

<?//========================================= ตรวจสอบเงื่อนไขในการให้ของแถม  (ยกเลิกเงื่อนไขนี้ไป ไม่ต้องเช็ค) ==============================================?>
<?php// if(empty($car_id)){ ?>
<!--<input style="font-size:11px" type="button" name="btnAddGiveAway" id="btnAddGiveAway" value="ของแถม" disabled>
<?php// }else{ ?>
<input style="font-size:11px" type="button" name="btnAddGiveAway" id="btnAddGiveAway" value="ของแถม">
<?php //} ?>
<?php //-------------------------------------------------------------------------------------------?>

<!--
<?php// if(empty($car_id)){ ?>
    <input style="font-size:11px" type="button" name="btnSelectCars" id="btnSelectCars" value="เลือกรถ">
<?php //}else{ ?>
    <input style="font-size:11px" type="button" name="btnSelectCars" id="btnSelectCars" value="เลือกรถ" disabled>
<?php //} ?>
-->
<input style="font-size:11px" type="button" name="btnUpdateMoney1" id="btnUpdateMoney1" value="เปลี่ยนแปลงการจอง">

</div>
<div style="clear:both"></div>

<div class="linedotted"></div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td valign="top" width="50%">
	
<?php //==================================== แสดงข้อมูล รายละเอียดการจอง =========================================?>
<div style="margin-top:10px">
	<?php
	if(empty($car_id)){ // กรณีการจองแบบ ไม่เจาะจงรถ 
		
		$qry_product = pg_query("SELECT product_id,name as product_name FROM \"Products\" WHERE product_id = '$product_id' ");
		if($res_product = pg_fetch_array($qry_product)){
	?>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="150"><b>รูปแบบการจอง :</b></td><td>ไม่เจาะจงรถ</td>
				</tr>
				<tr>
					<td><b>รูปแบบรถ :</b></td><td><?php echo $res_product['product_name']; ?></td>
				</tr>
				<tr>
					<td><b>สีรถแท็กซี่ :</b></td><td><?php echo $reserve_color; ?></td>
				</tr>
				<tr>
					<td><b>ประกันประเภท :</b></td><td><?php echo $type_insure; ?></td>
				</tr>
				<tr>
					<td><b>ติดตั้งเครื่องวิทยุสื่อสาร :</b></td><td><?php if($use_radio == 't') echo "ติดตั้ง"; else echo "ไม่ติดตั้ง"; ?></td>
				</tr>
			</table>
	<?php }
	}else{  //กรณีการจองแบบเจาะจงรถ
			$qry_cname = pg_query("SELECT * FROM \"Cars\" WHERE car_id='$car_id' AND cancel='FALSE' ");
			if($res_cname = pg_fetch_array($qry_cname)){
				$car_name = $res_cname['car_name'];
				$car_idno = $res_cname['car_idno'];
				$car_license_plate = $res_cname['license_plate'];
				$car_num = $res_cname['car_num'];
				$mar_num = $res_cname['mar_num'];
				$car_year = $res_cname['car_year'];
				$color = $res_cname['color'];
				$po_id = $res_cname['po_id'];
				$sub_po_id = substr($po_id, 0, 2);
	}
	?>
	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="150"><b>รูปแบบการจอง :</b></td><td>เจาะจงรถ</td>
	</tr>
	<tr>
		<td><b>รูปแบบรถ :</b></td><td><?php echo $car_name; ?></td>
	</tr>
	<tr>
		<td><b>ทะเบียนสต๊อก :</b></td><td><?php echo $car_idno; ?></td>
	</tr>
	<tr>
		<td><b>ทะเบียนรถ :</b></td><td><?php echo $car_license_plate; ?></td>
	</tr>
	<tr>
		<td><b>เลขเครื่อง :</b></td><td><?php echo $mar_num; ?></td>
	</tr>
	<tr>
		<td><b>เลขถัง :</b></td><td><?php echo $car_num; ?></td>
	</tr>
	<tr>
		<td><b>ปีรถ :</b></td><td><?php echo $car_year; ?></td>
	</tr>
	<tr>
		<td><b>สีรถแท็กซี่ :</b></td><td><?php echo $reserve_color; ?></td>
	</tr>
	<tr>
		<td><b>ประกันประเภท :</b></td><td><?php echo $type_insure; ?></td>
	</tr>
	<tr>
		<td><b>ติดตั้งเครื่องวิทยุสื่อสาร :</b></td><td><?php if($use_radio == 't') echo "ติดตั้ง"; else echo "ไม่ติดตั้ง"; ?></td>
	</tr>
	</table>
	<?php
	}
	?>
</div>
    </td>
    <td valign="top" width="50%">
<div style="margin-top:10px">
<?php
if($num_install == 0){
?>
	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100"><b>รูปแบบการซื้อ :</b></td><td>ซื้อสด</td>
	</tr>
	<tr>
		<td><b>ราคารถ :</b></td><td><?php echo $car_price; ?></td>
	</tr>
	</table>
<?php
}else{
?>
	<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100"><b>รูปแบบการซื้อ :</b></td><td>ซื้อผ่อน</td>
	</tr>
	<tr>
		<td><b>ราคารถ :</b></td><td><?php echo $car_price; ?></td>
	</tr>
	<tr>
		<td><b>ดาวน์ :</b></td><td><?php echo $down_price; ?></td>
	</tr>
	<tr>
		<td><b>ยอดจัดเช่าซื้อ :</b></td><td><?php echo $finance_price; ?></td>
	</tr>
	<tr>
		<td><b>จำนวนงวดผ่อน :</b></td><td><?php echo $num_install; ?></td>
	</tr>
	<tr>
		<td><b>ค่างวด :</b></td><td><?php echo $installment; ?> | ดอกเบี้ย = <?php echo interest_rate($car_price-$down_price,$installment,$num_install); ?></td>
	</tr>
	<tr>
		<td><b>บริษัท Finance :</b></td><td><?php echo $finance_cus_name; ?></td>
	</tr>
	</table>
<?php
}
?>
</div>
    </td>
</tr>
</table>

<div class="linedotted"></div>

<div>
<div style="margin-top:5px"><b>รายละเอียดหนี้ที่ค้างชำระ</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#ffcccc" style="font-weight:bold; text-align:center">
    <td>เลขที่ใบแจ้งหนี้</td>
    <td>รายละเอียดค่าใช้จ่าย</td>
    <td>ยอดเงิน</td>
  <!--  <td>สถานที่เปิด</td> -->
	<td></td>
</tr>

<?php
$j = 0;
$total_amount = 0;
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE res_id='$id' AND status IS NULL AND cancel = 'FALSE' ORDER BY inv_no ASC ");
while( $res = pg_fetch_array($qry) ){
    $j++;
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    $branch_out = $res['branch_out'];
    
    $cus_name = GetCusName($cus_id);
    $branch_out = GetWarehousesName($branch_out);
    
	
	//========================================== แสดงรายละเอียดค่าใช้จ่าย =====================================================//
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
	
    $name = implode(",", $arr_name);
	//------------------------------------------------------------------------------------------------//
	
    
    $qry3 = pg_query("SELECT SUM(amount+vat) as amt FROM \"VInvDetail\" WHERE cancel='FALSE' AND inv_no='$inv_no' ");
	
    while( $res3 = pg_fetch_array($qry3) ){
        $amount = $res3['amt'];
		if( $service_id  == 'S002' or $service_id  == 'S003' or $service_id  == 'S004'){
			$total_amount +=  $amount;
		}
	
    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <tr>
	<td><?php echo $inv_no; ?></td>
    <td><?php echo $name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
  <!--  <td><?php //echo $branch_out; ?></td>-->
	<td><input type="button" name="btn_delete" id="btn_delete" value="ยกเลิก" onclick="javascript:cancel_invoice('<?php echo $inv_no; ?>')"></td>
</tr>
<?php
	}
}

if($j == 0){
    echo "<tr><td colspan=4 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
</div>

<div>
<div style="margin-top:5px"><b>รายละเอียดการจอง/มัดจำ</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>บริการ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
$j = 0;
$qry_resdt = pg_query("SELECT A.*,B.* FROM \"VOtherpay2\" A LEFT JOIN \"Services\" B on A.service_id = B.service_id 
WHERE A.res_id='$id' AND A.o_receipt IS NOT NULL AND B.constant_var IS NOT NULL ORDER BY inv_no ASC ");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    $service_id = $res_resdt['service_id'];
    $service_name = $res_resdt['name'];
    $status = $res_resdt['status'];
    $o_date = $res_resdt['o_date'];
    
    $appointment_amt -= $amount;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $o_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$status"; ?></td>
</tr>
<?php
}
if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>

<div style="margin-top:5px"><b>รายละเอียดส่วนลด</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>บริการ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
$j = 0;
$qry_resdt = pg_query("SELECT A.*,B.* FROM \"VDiscountpay\" A LEFT JOIN \"Services\" B on A.service_id = B.service_id 
WHERE A.res_id='$id' AND A.o_receipt IS NOT NULL AND B.constant_var IS NOT NULL ORDER BY inv_no ASC ");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    $service_id = $res_resdt['service_id'];
    $service_name = $res_resdt['name'];
    $status = $res_resdt['status'];
    $o_date = $res_resdt['o_date'];
    
    $appointment_amt -= $amount;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $o_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td><?php echo $service_name.'-ส่วนลด'; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$status"; ?></td>
</tr>
<?php
}
if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>





<?php
$j = 0;
$qry_resdt = pg_query("SELECT A.*,B.* FROM \"VOtherpay2\" A LEFT JOIN \"Services\" B on A.service_id = B.service_id 
WHERE A.res_id='$id' AND A.o_receipt IS NOT NULL AND B.constant_var IS NULL ORDER BY inv_no ASC ");
$qry_resdt_num = pg_num_rows($qry_resdt);
if($qry_resdt_num > 0){
?>
<div style="margin-top:5px"><b>รายละเอียดค่าใช้จ่ายอื่นๆ</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#F5DEB3" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>บริการ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    $service_id = $res_resdt['service_id'];
    $service_name = $res_resdt['name'];
    $status = $res_resdt['status'];
    $o_date = $res_resdt['o_date'];
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $o_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$status"; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=5 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>
<?php
}
?>

<?php
$qry_vidt_num = 0;
$qry_vidt = pg_query("SELECT * FROM v_chq_detail WHERE res_id ='$id' ORDER BY inv_no ASC ");

$qry_vidt_num = pg_num_rows($qry_vidt);
if($qry_vidt_num > 0){
?>
<div style="margin-top:5px"><b>รายละเอียดการจ่ายเช็ค</b></div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#C9C9E4" style="font-weight:bold; text-align:center">
    <td>วันที่รับ</td>
    <td>ธนาคาร</td>
    <td>เลขที่เช็ค</td>
    <td>วันที่บนเช็ค</td>
    <td>ค่าใช้จ่าย</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$chq_amount = 0;
while($res_vidt = pg_fetch_array($qry_vidt)){
    $receive_date = $res_vidt['receive_date'];
    $bank_name = $res_vidt['bank_name'];
    $cheque_no = $res_vidt['cheque_no'];
    $date_on_cheque = $res_vidt['date_on_cheque'];
    $cus_amount = $res_vidt['cus_amount'];
    $service_id = $res_vidt['service_id'];
    $service_name = GetServicesName($service_id);
    
	if( $service_id  == 'S002' or $service_id  == 'S003' or $service_id  == 'S004'){
			$chq_amount +=  $cus_amount;
	}
    //$appointment_amt -= $cus_amount;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $receive_date; ?></td>
    <td align="left"><?php echo $bank_name; ?></td>
    <td align="left"><?php echo $cheque_no; ?></td>
    <td align="center"><?php echo $date_on_cheque; ?></td>
    <td><?php echo $service_name; ?></td>
    <td align="right"><?php echo number_format($cus_amount,2); ?></td>
</tr>
<?php
}
?>
</table>

<?php
}
?>

<div class="linedotted"></div>
</div>
	<div style="margin-top:10px">
	
	<?php 
		
		
	?>
	ยอดเงินจอง <input type="text" name="txt_moneyreserv" id="txt_moneyreserv"  size="10" onkeyup="javascript:SummaryDialog()" value="<?php if($appointment_amt == 0){ echo "0"; } ?>">
	&nbsp;&nbsp;&nbsp;&nbsp;วันที่ต้องการออกรถ <input type="text" name="txt_dateoutcar" id="txt_dateoutcar" size="10" value="<?php echo $receive_date; ?>"><br><br>
	เงินดาวน์คงค้าง &nbsp;&nbsp;&nbsp;&nbsp;<b><span id="span_appointment_amt"><?php echo number_format($appointment_amt,2); ?></span></b> บาท.
	&nbsp;&nbsp;&nbsp;&nbsp;ค้างตั้งหนี้เงินดาวน์ &nbsp;&nbsp;&nbsp;&nbsp;<b><span id="span_balance_amt"><?php echo number_format(($appointment_amt - ($total_amount+$chq_amount) ),2) ; ?></span></b> บาท.
	
	</div>

<div class="linedotted"></div>
<div style="margin-top:10px">
	<b>หมายเหตุ</b> <br> <?php echo $remark;?>
</div>
<div class="linedotted"></div>
		<div style="margin-top:10px; text-align:right">
				<?php
				if($num_install == 0){
					echo "<input type=\"hidden\" name=\"txt_hid_dialog_type\" id=\"txt_hid_dialog_type\" value=\"1\">";
				}else{
					echo "<input type=\"hidden\" name=\"txt_hid_dialog_type\" id=\"txt_hid_dialog_type\" value=\"2\">";
				}
				?>
			<div>
				<?php //======================================= บันทึก =============================================================//?>		
						<input type="button" name="btnDialogSave" id="btnDialogSave" value="บันทึก" onclick="javascript:SaveData()">
				<?php //-------------------------------------------------------------------------------------------//?>
			</div>
		</div>
</div>
<script>

$(document).ready(function(){
	if( parseFloat( $('#span_balance_amt').text() ) == 0){
		$('#txt_moneyreserv').prop('disabled',true);
	}
});	


$(function(){
    $("#txt_dateoutcar, #txt_dialog_cheque_date").datepicker({
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        showOn: 'both'
    });
    
    $("input[name='chk_dialog_buy_cash']").change(function(){
        if( $('input[id=chk_dialog_buy_cheque]:checked').val() ){
            var m1 = parseFloat( $('#txt_moneyreserv').val() );
            var m2 = parseFloat( $('#txt_dialog_cheque_monny').val() );
            $('#txt_dialog_cash').val( m1-m2 );
        }else{
            $('#txt_dialog_cash').val( $('#txt_moneyreserv').val() );
        }
        
        if( $('input[id=chk_dialog_buy_cash]:checked').val() ){
            $('#divdialog_cash').show('fast');
        }else{
            $('#txt_dialog_cash').val('');
            $('#divdialog_cash').hide('fast');
        }
    });
    
    $("input[name='chk_dialog_buy_cheque']").change(function(){
        if( $('input[id=chk_dialog_buy_cash]:checked').val() ){
            var m1 = parseFloat( $('#txt_moneyreserv').val() );
            var m2 = parseFloat( $('#txt_dialog_cash').val() );
            $('#txt_dialog_cheque_monny').val( m1-m2 );
        }else{
            $('#txt_dialog_cheque_monny').val( $('#txt_moneyreserv').val() );
        }
        
        if( $('input[id=chk_dialog_buy_cheque]:checked').val() ){
            $('#divdialog_cheque').show('fast');
        }else{
            $('#txt_dialog_cheque_monny').val('');
            $('#divdialog_cheque').hide('fast');
        }
    });
    
//================================================== เปลี่ยนแปลงการจอง ================================================//
    $('#btnUpdateMoney1').click(function(){
       // $("#div_dialog").remove();
        $('body').append('<div id="divdialogedit"></div>');
        $('#divdialogedit').load('reserv_car_edit_api.php?cmd=edit_resv_all&id=<?php echo $id; ?>');
        $('#divdialogedit').dialog({
            title: 'เปลี่ยนแปลงการจอง : <?php echo $id; ?>',
            resizable: false,
            modal: true,  
            width: 800,
            height: 600,
            close: function(ev, ui){
                $('#divdialogedit').remove();
            }
        });
    });

	
  /*  $('#btnSelectCars').click(function(){
        $('body').append('<div id="divdialogeditcar"></div>');
        $('#divdialogeditcar').load('reserv_car_edit_api.php?cmd=selectcars&id=<?php echo $id; ?>&pid=<?php echo $product_id; ?>');
        $('#divdialogeditcar').dialog({
            title: 'เลือกรถ : <?php echo $id; ?>',
            resizable: false,
            modal: true,  
            width: 400,
            height: 150,
            close: function(ev, ui){
                $('#divdialogeditcar').remove();
            }
        });
    });*/

//========================================================= ตั้งค่าใช้จ่ายอื่น ๆ ==========================================================//
     $('#btnAddOtherMoney').click(function(){
       // $("#div_dialog").remove();
        window.open('../invoice/print_invoice_other.php?resid=<?php echo $id; ?>','sdf7fd8s7fs789','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=950,height=600');
    });

//================================================== ของแถม ======================================================================//
     $('#btnAddGiveAway').click(function(){
         window.open('../invoice/print_invoice_other_give.php?resid=<?php echo $id; ?>','dfsd342ss7fs789','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=950,height=600');
	});
});

function SummaryDialog(){
    var s1 = 0;
    var a1 = parseFloat($('#txt_moneyreserv').val());
    var a2 = parseFloat('<?php echo $appointment_amt; ?>');
	var tmp_a3 = parseFloat('<?php echo $total_amount; ?>');
	var chq_amount = parseFloat('<?php echo $chq_amount; ?>');
	var a3 = parseFloat( $('#span_balance_amt').val() );

	
	//$appointment_amt - $total_amount
    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
	if(isNaN(a3) || a3 == ""){
		a3 = a2-tmp_a3;
	}
	
	if(isNaN(chq_amount) || chq_amount == ""){
		chq_amount = 0;
	}
    
    s1 = a2-a1;
	s2 = a3-(a1+chq_amount);
	
    $('#span_appointment_amt').text(s1);
	$('#span_balance_amt').text(s2);
}

function chkSumDialog(){
    var s1 = 0;
    var a1 = parseFloat($('#txt_moneyreserv').val());
    var a2 = parseFloat($('#txt_dialog_cash').val());
    var a3 = parseFloat($('#txt_dialog_cheque_monny').val());

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=chk_dialog_buy_cash]:checked').val() ){
        s1+=a2;
    }
    if( $('input[id=chk_dialog_buy_cheque]:checked').val() ){
        s1+=a3;
    }
    
    if(a1 != s1){
        return false;
    }else{
        return true;
    }
}

function ClosePopup(){
    $('#div_dialog').remove();
}

function ShowCarPlateDetails(id){
    $('body').append('<div id="DivCarPlate" style="margin:0; font-size:12px"></div>');
    $('#DivCarPlate').load('../drawn/withdrawal_carplate_api.php?cmd=divshow&license_plate='+id);
    $('#DivCarPlate').dialog({
        title: 'เบิกป้ายเหล็ก',
        resizable: false,
        modal: true,  
        width: 800,
        height: 350,
        close: function(ev, ui){
            $('#DivCarPlate').remove();
        }
    });
}

//============================================ Save Action =========================================//
function SaveData(){

    if( $('#txt_moneyreserv').val() == "" ){
        alert('กรุณากรอก ยอดเงินจอง');
        return false;
    }
    
    var dkl = parseFloat( $('#span_appointment_amt').text() );
    
    if( dkl < 0 ){
        alert('กรุณาตรวจสอบ ยอดเงินจอง');
        return false;
    }
    
    SaveData2();
}
    
function SaveData2(){
    $.post('reserv_car_edit_api.php',{
        cmd: 'plus', //ชำระเงินจองเพิ่มเติม
        resid: '<?php echo $id; ?>',
        cusid: '<?php echo $cus_id; ?>',
        product_id: '<?php echo $product_id; ?>',
        txt_moneyreserv: $('#txt_moneyreserv').val(),
        txt_dateoutcar: $('#txt_dateoutcar').val(), //วันที่ต้องการออกรถ
        span_appointment_amt: $('#span_appointment_amt').text(), // เงินดาวน์คงเหลือ
		span_balance_amt: $('#span_balance_amt').text(),
        txt_hid_dialog_type: $('#txt_hid_dialog_type').val(), //จำนวนงวด
        area_remark: $('#area_remark').val(), // หมายเหตุุ
        car_id: '<?php echo $car_id; ?>'
    },
    function(data){
        if(data.success){
			alert(data.message);
           //$('#dev_edit').empty();
           //$('#dev_edit').load('reserv_car_edit.php');
           $("#div_dialog").remove();
        }else{
            alert(data.message);
        }
    },'json');
}

//================ action ยกเลิกใบแจ้งหนี้ =================//
function cancel_invoice(inv_no){
		if(confirm("คุณต้องการที่จะยกเลิกเลขที่ใบแจ้งหนี้   "+inv_no+"    ใช่หรือไม่? ") == true){
		var pram_inv_no = inv_no;
		   $.post('reserv_car_edit_api.php',{
				cmd: 'cancel_invoice', //ยกเลิกรายการข้อมูล invoice
				param_inv_no: pram_inv_no
			},
			function(data){
				if(data.success){
					
						/*$('#dev_edit').empty();
						$('#dev_edit').load('reserv_car_edit.php');
						$("#div_dialog").remove();*/
						$('#div_dialog').empty();
						$('#div_dialog').load('reserv_car_edit_dialog.php');
						$("#div_dialog").remove();
						alert(data.message);
						 //$("#div_dialog").load('reserv_car_edit_dialog.php?id='+id);
				}else{
					alert(data.message);
				}
			},'json');
		}else{
		
		}
	
}
function ShowZeroPrint(id){
    $('body').append('<div id="div_print"></div>');
    $('#div_print').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบเสร็จรับเงิน\" onclick=\"javascript:window.open('../report/temporary_receipt_down_zero.php?res_id="+ id +"','receipt_down_zero_345435','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
    $('#div_print').dialog({
        title: 'พิมพ์เอกสาร : '+id,
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_print').remove();
        }
    });
}
</script>