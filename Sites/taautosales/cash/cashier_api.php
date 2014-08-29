<?php
include_once("../include/config.php");
include_once("../include/function.php");
$cmd = pg_escape_string($_REQUEST['cmd']);
if($cmd == "divshow"){
?>
<!--<div>
	<fieldset>
		<legend> ค้นหาข้อมูล </legend>
			<input type="radio" name="pay_type" id="pay_type" value="res" >รถจอง
			<input type="radio" name="pay_type" id="pay_type" value="rp" >รถฝากซ่อม
			<br><br>
			<label>ค้นหาตาม</label>
				<select name="ddl_condition" id="ddl_condition">
					<option value="not">กรุณาเลือก</option>
					<option value="res">ชื่อลูกค้า/เลขที่จอง</option>
					<option value="inv">เลขที่ใบแจ้งหนี้</option>
				</select>
				<input type="button" name ="btn_search" id="btn_search" value="ค้นหา">
	</fieldset>
</div>
<span id="span_inv" style="display:none">
	<input type="text" name = "txt_inv" id="txt_inv" />
</span>
<span id="span_res" style="display:none">
	<input type="text" name="txt_search" id="txt_search" size="60" onkeyup="javascript:CheckNaN()">
</span>
<br><br>-->
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<div style="float:left; margin-top:10px">
<font color="red" > ***เงื่อนไข การรับชำระเงิน ***</font>
<br>
<font color="red" > 1. ให้ออกใบเสร็จแยกใบ กรณีรับชำระ เงินจอง , เงินดาวน์พร้อมอุปกรณ์ , ค่ารถยนต์พร้อมอุปกรณ์ ,  ค่ามัดจำป้ายแดง  , ค่ามัดจำเล่มทะเบียน</font>
<br>
<font color="red" > 2. กรณีรับชำระค่าใช้จ่ายอื่นๆ  ให้ออกใบเสร็จ รวมเป็น ใบเดียวกันได้  (เลือกรายการตั้งหนี้ ที่เป็นค่าใช้จ่ายอื่นๆ หลายรายการต่อใบเสร็จ 1 ใบ)</font>
<br>
<font color="red" > 3. กรณีรับชำระ แล้วมีส่วนลด  พนง.ขายจะตั้งหนี้ เต็มจำนวนมา ให้ บันทึก จำนวนเงินที่ได้รับจริง (เช็ค / เงินสด) และ ส่วนลด ด้วย</font>
<br>
<font color="red" > 4. หากต้องการยกเลิกใบเสร็จ ต้องนำเลขที่ใบเสร็จ ไปยกเลิกที่เมนู ยกเลิกใบเสร็จ แล้วให้คุณพรหรือคุณมาลีอนุมัติ</font>
<br>
<font color="red" >5.  เช็ค 1 ใบ สามารถรับชำระได้หลายรายการ โดยระบุเลขที่เช็คเดียวกัน</font>
<br>
<font color="red" >6.  หากรับชำระแทน ใบเสร็จ ที่ยกเลิก ให้ระบุเลขที่เดิมด้วย</font>
<br>
<font color="red" > </font>
</div>
</table>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>เลือก</td>
    <td>เลขที่ใบแจ้งหนี้</td>
	<td>วันที่</td>
    <td>ชื่อลูกค้า</td>
    <td>เลขที่จอง</td>
    <td>รายละเอียดค่าใช้จ่าย</td>
    <td>ยอดเงิน</td>
    <!--<td>สถานที่เปิด</td>-->
</tr>

<?php
$j = 0;
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE status IS NULL AND cancel = 'FALSE' ORDER BY inv_no ASC ");
while( $res = pg_fetch_array($qry) ){
    $j++;
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    $branch_out = $res['branch_out'];
	$is_print = $res['is_print'];
	$inv_date = $res['inv_date'];
 
    $cus_name = GetCusName($cus_id);
    $branch_out = GetWarehousesName($branch_out);
    
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    $name = implode(",", $arr_name);
    
    $qry3 = pg_query("SELECT SUM(amount+vat) as amt FROM \"VInvDetail\" WHERE cancel='FALSE' AND inv_no='$inv_no' ");
    if( $res3 = pg_fetch_array($qry3) ){
        $amount = $res3['amt'];
    }

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><input type="checkbox" name="chk_box" id="chk_box<?php echo $j; ?>" value="<?php echo "$inv_no"; ?>" onchange="javascript:ListChkBox();"></td>
    <td><?php echo $inv_no; ?></td>
	<td><?php echo $inv_date; ?></td>
    <td><?php echo $cus_name; ?></td>
<?php
	if(substr($res_id,0,2)=='RS'){
?>		
	<td><a href="javascript:show_dialog('<?php echo $res_id; ?>')" title="รายละเอียดการจอง"><u><?php echo $res_id; ?></u></a></td>
<?php	
	}
	else
	{
?>		
	<td><a href="javascript:show_dialog_repair('<?php echo $res_id; ?>')" title="รายละเอียดรถซ่อม"><u><?php echo $res_id; ?></u></a></td>
<?php	
	}
?>	
	
	
    <td><?php echo $name; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
   <!-- <td><?php// echo $branch_out; ?></td>-->
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>

</table>

<div id="result_select_div"></div>

<script>
function ListChkBox(){
    var j = 0;
    var arr_chk_box = [];
    for (var i=1; i <= <?php echo $j; ?>; i++){
        if( $('input[id=chk_box'+ i +']:checked').val() ){
            j++;
            var inv = $('#chk_box'+i).val();
            arr_chk_box[j] = { inv:inv };
        }
    }
    
    $.get("cashier_api.php?cmd=show_select_div&arr_chk_box="+JSON.stringify(arr_chk_box),function(data){
        $("#result_select_div").empty();
        $("#result_select_div").html(data);
    },'html');
}

function show_dialog(res_id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../report/report_reserve_api.php?cmd=showdetail&id='+res_id);
		$('#divdetail').dialog({
			title: 'แสดงรายละเอียด',
			resizable: false,
			modal: true,  
			width: 800,
			height: 550,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}

function show_dialog_repair(res_id){
		$('body').append('<div id="divdetail"></div>');
		$('#divdetail').load('../car/rep_detail_api.php?cmd=showrepair&id='+res_id);
		$('#divdetail').dialog({
			title: 'แสดงรายละเอียด',
			resizable: false,
			modal: true,  
			width: 800,
			height: 550,
			close: function(ev, ui){
				$('#divdetail').remove();
			}
		});
}

</script>
<?php
}

elseif($cmd == "show_select_div"){
    $arr_chk_box = json_decode(stripcslashes($_GET["arr_chk_box"]));
    if( count($arr_chk_box) == 0 ){
        exit;
    }
?>
<div style="margin-top:10px; font-size:14px; font-weight:bold">รายการที่เลือกชำระ</div>
<div style="margin-top:5px; line-height:25px; border:1px dashed #C0C0C0; background-color:#FFFFE8">

<table cellpadding="1" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#FFFACD" style="font-weight:bold; text-align:center">
    <td>ลำดับ</td>
    <td>เลขที่ใบแจ้งหนี้</td>
	<td>วันที่</td>
    <td>ชื่อลูกค้า</td>
    <td>เลขที่จอง</td>
    <td>รายละเอียดค่าใช้จ่าย</td>
    <td>ยอดเงิน</td>
</tr>

<?php
    $nub=0;
    foreach($arr_chk_box as $key => $value){
        $inv_no = $value->inv;
        
        if(empty($inv_no)){ continue; }
        
        $arr_inv_list_print[] = $inv_no;
        
        $nub++;

        $qry = pg_query("SELECT * FROM \"Invoices\" WHERE inv_no='$inv_no' AND status IS NULL AND cancel = 'FALSE' ");
        if( $res = pg_fetch_array($qry) ){
            $cus_id = $res['cus_id'];
            $IDNO = $res['IDNO'];
            $res_id = $res['res_id'];
            $cus_name = GetCusName($cus_id);
			$is_print = $res['is_print'];
            $arr_name = array();
            $arr_service_id = array();
			$inv_date = $res['inv_date'];
            $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
            while( $res2 = pg_fetch_array($qry2) ){
                $service_id = $res2['service_id'];
                $service_name = GetServicesName($service_id);
                $arr_name[] = $service_name;
                $arr_service_id[] = $service_id;
            }
			
            $name = implode(",", $arr_name);
            $service_id_list = implode(",", $arr_service_id);

            $qry3 = pg_query("SELECT SUM(amount+vat) as amt FROM \"VInvDetail\" WHERE cancel='FALSE' AND inv_no='$inv_no' ");
            if( $res3 = pg_fetch_array($qry3) ){
                $amount = $res3['amt'];
            }
        }
        
        $sum_all+=$amount;
        
    if($nub%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td align="center"><?php echo $nub; ?></td>
    <td><?php echo $inv_no; ?></td>
	<td><?php echo $inv_date; ?></td>
    <td><?php echo $cus_name; ?></td>
    <td><?php echo $res_id; ?></td>
    <td><?php echo $name; ?></td>
    <td align="right" style="font-weight:bold"><?php echo number_format($amount,2); ?>
        <input type="hidden" name="txt_hide_inv<?php echo $nub; ?>" id="txt_hide_inv<?php echo $nub; ?>" value="<?php echo "$inv_no#$amount#$cus_id#$service_id_list#$res_id"; ?>">
		<input type="hidden" name="hdd_inv_no<?php echo $nub; ?>" id="hdd_inv_no<?php echo $nub; ?>" value="<?php echo "$inv_no"; ?>">
    </td>
</tr>
<?php
    }
?>
<tr bgcolor="#FFE4E1">
    <td align="left" colspan="2"><input type="button" name="btn_show_pay" id="btn_show_pay" value="เริ่มทำรายการชำระเงิน"></td>
    <td style="font-weight:bold" align="right" colspan="3">รวมเงิน</td>
    <td style="font-weight:bold" align="right"><?php echo number_format($sum_all,2); ?></td>
</tr>
</table>

</div>

<div id="show_pay_div"></div>

<?php $arr_inv_list_print = implode(",", $arr_inv_list_print); ?>

<script>
$('#btn_show_pay').click(function(){
    $('#show_pay_div').empty();
    $('#show_pay_div').load('cashier_api.php?cmd=show_pay_div&money=<?php echo $sum_all; ?>&nub=<?php echo $nub; ?>&prt=<?php echo $arr_inv_list_print; ?>&is_print=<?php echo $is_print; ?>');
    //$('#btn_show_pay').attr('disabled',true);
});
</script>
<?php
}

elseif($cmd == "show_pay_div"){
    $money = pg_escape_string($_GET['money']);
    $nub = pg_escape_string($_GET['nub']);
    $prt = pg_escape_string($_GET['prt']);
	$is_print = pg_escape_string($_GET['is_print']);
?>
<br><br>

<!-- ออกแทนใบเสร็จ -->

<span>
	วันที่รับชำระเงิน <input type="text" name="txt_date_rec" id="txt_date_rec" size="10" value="">
<span>
<br>
<span>
	<input type="checkbox" name="chk_receipt_replace" id="chk_receipt_replace">&nbsp;
	<label>ออกแทน ใบเสร็จรับเงินชั่วคราวที่ยกเลิกเลขที่</label>&nbsp;&nbsp;  
<span>
<span id="show_re_pay"><input type="text" name="txt_receipt_no" id="txt_receipt_no" size="30" /></span>

<div style="margin-top:10px">
    
<div style="float:left">
<b>หมายเหตุ</b><br />
<textarea name="area_remark_new" id="area_remark_new" rows="2" cols="100"></textarea>
</div>
<div style="float:right">
<!-- RIGHT -->
</div>
<div style="clear:both"></div>

</div>

<div style="margin-top:10px; font-size:14px; font-weight:bold">ชำระโดย</div>
<div style="margin-top:5px; line-height:25px; border:1px dashed #C0C0C0; background-color:#FFFFE8; padding:2px">

<div style="float:left; width:100px"><input type="checkbox" name="chk_buy_cash" id="chk_buy_cash"> เงินสด</div>
<div style="float:left; width:200px"><input type="text" name="txt_money_cash" id="txt_money_cash" size="20" value="<?php echo number_format($money,2); ?>" onkeypress="check_num(event);"> บาท</div>
<div style="clear:both"></div>

<div style="float:left; width:100px"><input type="checkbox" name="chk_buy_discount" id="chk_buy_discount"> ส่วนลด</div>
<div style="float:left; width:200px"><input type="text" name="txt_money_discount" id="txt_money_discount" size="20" value="<?php echo number_format($money,2); ?>" onkeypress="check_num(event);"> บาท</div>
<div style="clear:both"></div>

<div style="float:left; width:100px"><input type="checkbox" name="chk_buy_cheque" id="chk_buy_cheque"> เช็ค/เงินโอน</div>
<div style="float:left; width:200px"><input type="text" name="txt_money_chq" id="txt_money_chq" size="20" value="<?php echo number_format($money,2); ?>" onkeypress="check_num(event);"> บาท</div>
<div style="clear:both"></div>

<div id="div_chq_detail" style="display:none">

<div style="margin-top:10px; font-size:12px; font-weight:bold">รายละเอียดเช็ค</div>
    <div id="DivCheque">
    <div id="add_cheque_1" style="border: 1px dashed #D0D0D0; margin-top:1px; padding:3px; background-color:#F5F5F5">
<table cellpadding="0" cellspacing="0" border="0" width="100%">

<tr>
<div style="margin:5px 0px 5px 0px">
<input type="radio" name="radio_chq_type" id="radio_chq_type" value="0" checked > เช็ค
<input type="radio" name="radio_chq_type" id="radio_chq_type" value="1" > เงินโอน
</div>
</tr>

<tr>
    <td colspan="10">
<b>#1</b> | เช็คสั่งจ่ายบริษัท :
<select name="cb_accno1" id="cb_accno1">
<?php
$qry = pg_query("SELECT * FROM \"BankEnter\" ORDER BY \"accname\" ASC");
while( $res = pg_fetch_array($qry) ){
   $accno = $res['accno'];
	$bankno = $res['bankno'];
    $accname = $res['accname']; //ชื่อบัญชี
	$bank_name = $res['bankname'];//ชื่อธนาคาร
?>
    <option value="<?php echo "$accno"; ?>" <?php if($accno == '0576019909') echo "selected"; ?>><?php echo "$accno#$bank_name#$accname" ; ?></option>
<?php
}
?>
</select>
    </td>
</tr>
<tr>
	<td align="left">ธนาคาร&nbsp;</td><td>
		<select name="txt_cheque_bank1?>" id="txt_cheque_bank1">
			<option value="" select="selected">เลือกธนาคาร</option>
			<?php
			$qry = pg_query("SELECT * FROM \"BankInThai\" ORDER BY \"BankName\" ASC");
			while( $res = pg_fetch_array($qry) ){
				$bank_code = $res['BankCode'];
				$bank_name = $res['BankName'];
				echo "<option value=\"$bank_name\">$bank_name</option>";
			?>
				<!-- <option value="<?php echo "$bank_name"; ?>" <?php if($bank_code == 'KTB' ) echo "selected"; ?> ><?php echo "$bank_name"; ?></option>  ยกเลิก default-->
			<?php
			}
			?>
		</select>
	</td>
    <!--<td align="left">ธนาคาร&nbsp;</td><td><input type="text" name="txt_cheque_bank1" id="txt_cheque_bank1" style="width:80px"></td>-->
	
	
    <td align="right">สาขา&nbsp;</td><td><input type="text" name="txt_cheque_branch1" id="txt_cheque_branch1" style="width:80px"></td>
    <td align="right">เลขที่เช็ค&nbsp;</td><td><input type="text" name="txt_cheque_no1" id="txt_cheque_no1" style="width:60px" onkeypress="check_num(event);"></td>
    <td align="right">วันที่เช็ค/โอน&nbsp;</td><td><input type="text" name="txt_cheque_date1" id="txt_cheque_date1" style="width:80px; text-align:center" value="<?php echo $nowdate; ?>"></td>
    <td align="right">จำนวนเงิน&nbsp;</td><td><input type="text" name="txt_cheque_money1" id="txt_cheque_money1" style="width:80px; text-align:right" onblur="javascript:ChkChqAmt()" onkeypress="check_num(event);"></td>
</tr>
</table>
    </div>        
    </div>

    <div><input type="button" name="btnDel" id="btnDel" value="- ลบเช็ค" onclick="javascript:DelRowChq();"></div>
</div>

</div>

<!--<div id="show_print_doc" style="display:none;">
	<table>
		<tr>
			<td><input type="button" name="btnSave" id="btnSave" value="พิมพ์ใบจองรถยนต์"></td>
			<td><input type="button" name="btnSave" id="btnSave" value="พิมพ์ใบเสร็จรับเงิน"></td>
		</tr>
	</table>
</div>-->

<div style="text-align:right; margin-top:10px">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>


<script>

/* ใส่คอมม่าให้กับข้อมูลตัวเลข */
function addCommas(nStr)
{ // function สำหรับเพิ่มลูกน้ำ
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1))
	{
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
return x1 + x2;
}

var counter = 1;
    
$(document).ready(function(){

});
	
	$('#txt_receipt_no').attr('disabled',true);
    $('#txt_money_cash').attr('disabled',true);
    $('#txt_money_cash').attr('style','background:#EEE9E9');
	$('#txt_money_discount').attr('disabled',true);
    $('#txt_money_discount').attr('style','background:#EEE9E9');
    $('#txt_money_chq').attr('disabled',true);
    $('#txt_money_chq').attr('style','background:#EEE9E9');
	//แสดงปุ่ม วันที่ รับชำระเงิน
	$('#txt_date_rec').datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd',
        showOn: 'both'
    });
	
	
	//ให้แสดง textbox สำหรับกรอกเลขที่ใบเสร็จที่เคยยกเลิกไปแล้ว
	$("input[name='chk_receipt_replace']").change(function(){
	   	if( $('input[id=chk_receipt_replace]:checked').val() ){
			$('#txt_receipt_no').attr('disabled',false);
		}else{
			$('#txt_receipt_no').attr('disabled',true);
			$('#txt_receipt_no').val('');
		}
	});
	
    $("input[name='chk_buy_cash']").change(function(){
        if( $('input[id=chk_buy_cash]:checked').val() ){
            $('#txt_money_cash').attr('disabled',false);
                $('#txt_money_cash').attr('style','');
        }else{
            $('#txt_money_cash').attr('disabled',true);
                $('#txt_money_cash').attr('style','background:#EEE9E9');
        }
        
		if( $('input[id=chk_buy_cheque]:checked').val() ){
            var m1 = parseFloat( <?php echo $money; ?> );
            var m2 = parseFloat( $('#txt_money_chq').val() );	
			$('#txt_money_cash').val(m1-m2);
        }else if( $('input[id=chk_buy_discount]:checked').val() ){
            var m1 = parseFloat( <?php echo $money; ?> );
            var m2= parseFloat( $('#txt_money_discount').val() );
			$('#txt_money_cash').val(m1-m2 );
        }else{
            $('#txt_money_cash').val( <?php echo $money; ?> );
        }
		

    });
    
	$("input[name='chk_buy_discount']").change(function(){
        if( $('input[id=chk_buy_discount]:checked').val() ){
            $('#txt_money_discount').attr('disabled',false);
                $('#txt_money_discount').attr('style','');
        }else{
            $('#txt_money_discount').attr('disabled',true);
                $('#txt_money_discount').attr('style','background:#EEE9E9');
        }
		
        if( $('input[id=chk_buy_cheque]:checked').val()){
            var m1 = parseFloat( <?php echo $money; ?> );
            var m2 = parseFloat( $('#txt_money_chq').val() );
			$('#txt_money_discount').val(m1-m2 );
        }else if( $('input[id=chk_buy_cash]:checked').val() ){
		    var m1 = parseFloat( <?php echo $money; ?> );
            var m2 = parseFloat( $('#txt_money_cash').val() );
			$('#txt_money_discount').val(m1-m2 );
        }else{
            $('#txt_money_discount').val( <?php echo $money; ?> );
        }
		
    });
    
	
    $("input[name='chk_buy_cheque']").change(function(){
        if( $('input[id=chk_buy_cheque]:checked').val() ){
            $('#txt_money_chq').attr('disabled',false);
                $('#txt_money_chq').attr('style','');
            $('#div_chq_detail').show('fast');
        }else{
            $('#txt_money_chq').attr('disabled',true);
                $('#txt_money_chq').attr('style','background:#EEE9E9');
            $('#div_chq_detail').hide('fast');
        }
        
        if( $('input[id=chk_buy_cash]:checked').val() ){
            var m1 = parseFloat( <?php echo $money; ?> );
            var m2 = parseFloat( $('#txt_money_cash').val() );
			$('#txt_money_chq').val(m1-m2);
        }else if( $('input[id=chk_buy_discount]:checked').val() ){
			var m1 = parseFloat( <?php echo $money; ?> );
            var m2 = parseFloat( $('#txt_money_discount').val() );
            $('#txt_money_chq').val(m1-m2);
        }else{
            $('#txt_money_chq').val( <?php echo $money; ?> );
        }
	
    });
	
	
	$("input[name='radio_chq_type']").change(function(){
        if( $('input[id=radio_chq_type]:checked').val() == "0" ){ //เช็ค
			$("#txt_cheque_bank1").val('ธ.กรุงไทย');
			$("#txt_cheque_bank1").attr('disabled',false);
			$("#txt_cheque_no1").val('');
			$("#txt_cheque_no1").attr('disabled',false);
			$("#txt_cheque_branch1").val('');
			$("#txt_cheque_branch1").attr('disabled',false);
        }else{ //เงินโอน				
			$("#txt_cheque_bank1").attr('disabled',false);
			$("#txt_cheque_no1").val('000000');
			$("#txt_cheque_no1").attr('disabled',true);
			$("#txt_cheque_branch1").val('TransferMoney');
			$("#txt_cheque_branch1").attr('disabled',true);
		}
    });
	
	
	
    
    $("#txt_cheque_date1").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
	
	//autocomplete ของ receipt_no
	$("#txt_receipt_no").autocomplete({
        source: "autocomplete_cancel_receipt.php?cmd=autocomplete",
        minLength:1
       /* select: function(event, ui){
            if( ui.item.value != "" && ui.item.value != 'ไม่พบข้อมูล' ){
                var keyword = ui.item.value;
				var arr_keyword = keyword.split("#");
				//search_res(arr_keyword[0]);
            }
        }*/
    });
	
    $('#btnSave').click(function(){
        var sum_all_amt = 0;
        var sum_all_amt_input = 0;
        var sum_chq_amt = 0;
        var arr_cheque = [];
		var txt_is_print = '<?php echo $is_print; ?>';
		var txt_date_rec = '<?php echo $txt_date_rec; ?>';
		
		var txt_date_rec = $("#txt_date_rec").val();
		if( txt_date_rec == "" ){
            alert('โปรดตรวจสอบรายการ  และระบุวันที่รับชำระเงิน!!!');
            return false;
        } 
	
		if( $('input[id=chk_receipt_replace]:checked').val() == "on" ){
            if($('#txt_receipt_no').val() == "" ){
                alert('กรุณาระบุใบเสร็จเลขเดิม');
                return false;
            }
        }else{
		    	//alert(txt_is_print);
			if(txt_is_print == "1" ){
                alert('เคยมีการออกใบเสร็จแล้ว กรุณาเลือกและระบุ ออกแทน ใบเสร็จรับเงินชั่วคราวที่ยกเลิกเลขที่...');
                return false;
            }	
		}
		
		
		if( $('input[id=chk_buy_cash]:checked').val() == "on" ){
            //if($('#txt_money_cash').val() == "0" || $('#txt_money_cash').val() == ""){
			// รับชำระเงิน 0 บาทได้
			if($('#txt_money_cash').val() == ""){
                alert('ยอดเงินสดไม่ถูกต้อง');
                return false;
            }
            sum_all_amt += parseFloat($('#txt_money_cash').val());
            sum_all_amt_input += parseFloat($('#txt_money_cash').val());
        }
		
	    if( $('input[id=chk_buy_discount]:checked').val() == "on" ){
            if($('#txt_money_discount').val() == "0" || $('#txt_money_discount').val() == ""){
                alert('ยอดส่วนลดไม่ถูกต้อง');
                return false;
            }
            sum_all_amt += parseFloat($('#txt_money_discount').val());
            sum_all_amt_input += parseFloat($('#txt_money_discount').val());
        }	
        
        if( $('input[id=chk_buy_cheque]:checked').val() == "on" ){
            if($('#txt_money_chq').val() == "0" || $('#txt_money_chq').val() == ""){
			    alert('ยอดเงินเช็คไม่ถูกต้อง');
                return false;
            }
            
            sum_all_amt_input += parseFloat($('#txt_money_chq').val());
            
            for( i=1; i<=counter; i++ ){
                var txt_cheque_no = $('#txt_cheque_no'+ i).val();
                var txt_cheque_bank = $('#txt_cheque_bank'+ i).val();
                var txt_cheque_branch = $('#txt_cheque_branch'+ i).val();
                var txt_cheque_date = $('#txt_cheque_date'+ i).val();
                var txt_cheque_money = parseFloat($('#txt_cheque_money'+ i).val());
                var cb_accno = $('#cb_accno'+ i).val();

                if(txt_cheque_no == ""){
                    alert('กรุณากรอก เลขที่เช็ค (เช็ครายการที่ '+i+')');
                    return false;
                }
                if(txt_cheque_bank == ""){
                    alert('กรุณากรอก ธนาคาร (เช็ครายการที่ '+i+')');
                    return false;
                }
                if(txt_cheque_branch == ""){
                    alert('กรุณากรอก สาขา (เช็ครายการที่ '+i+')');
                    return false;
                }
                if(isNaN(txt_cheque_money) || txt_cheque_money == 0){
                    alert('กรุณากรอก จำนวนเงิน (เช็ครายการที่ '+i+')');
                    return false;
                }

                sum_chq_amt+=parseFloat(txt_cheque_money);

                arr_cheque[i] = { cheque_no:txt_cheque_no, cheque_bank:txt_cheque_bank, cheque_branch:txt_cheque_branch, cheque_date:txt_cheque_date, cheque_money:txt_cheque_money, cb_accno:cb_accno };
            }

            if(sum_chq_amt != $('#txt_money_chq').val()){
                alert('ยอดเงินรวมของรายละเอียดเช็ค ไม่ถูกต้อง!');
                return false;
            }
            sum_all_amt += parseFloat(sum_chq_amt);
        }
        
        if(sum_all_amt_input != "<?php echo $money; ?>"){
            alert('ยอดเงินรวมทั้งหมด ไม่เท่ากับรายการที่เลือกชำระ!');
            return false;
        }
        
        if(sum_all_amt != "<?php echo $money; ?>"){
            alert('ยอดเงินรวมทั้งหมด ไม่เท่ากับรายการที่เลือกชำระ!');
            return false;
        }
		
		

        var arr_select_inv = [];
		var arr_inv_no = [];
        for( i=1; i<=<?php echo $nub; ?>; i++ ){
            var txt_hide_inv = $('#txt_hide_inv'+ i).val();
			var tmp_arr_inv_no  =  $('#hdd_inv_no'+i).val();
            arr_select_inv[i] = { txt_hide_inv:txt_hide_inv }
			arr_inv_no[i] = {tmp_arr_inv_no:tmp_arr_inv_no}
        }

		$('body').append('<div id="divdialogconfirm"></div>');
		$("#divdialogconfirm").text('ต้องการบันทึกการชำระเงินใช่หรือไม่?');
		$("#divdialogconfirm").dialog({
			title: 'ยืนยัน',
			resizable: false,
			height:140,
			modal: true,
			buttons:{
				"ใช่": function(){
					/*var chk_buy_cash = $('input[id=chk_buy_cash]:checked').val();
					var txt_money_cash= $('#txt_money_cash').val();
					var chk_buy_discount= $('input[id=chk_buy_discount]:checked').val();
					var txt_money_discount= $('#txt_money_discount').val();
					var chk_buy_cheque= $('input[id=chk_buy_cheque]:checked').val();
					var txt_money_chq=  $('#txt_money_chq').val();
					alert(chk_buy_cash+txt_money_cash);
					alert(chk_buy_discount+txt_money_discount); */

				   $.post('cashier_api.php',{
					cmd: 'save',
					money: '<?php echo $money; ?>',
					chk_buy_cash: $('input[id=chk_buy_cash]:checked').val(),
					txt_money_cash: $('#txt_money_cash').val(),
					chk_buy_cheque: $('input[id=chk_buy_cheque]:checked').val(),
					txt_money_chq:  $('#txt_money_chq').val(),
					arr_cheque: JSON.stringify(arr_cheque),
					arr_select_inv: JSON.stringify(arr_select_inv), 
					service_id: '<?php echo $service_id_list; ?>',
					res_id: '<?php echo $res_id; ?>',
					arr_inv_no: JSON.stringify(arr_inv_no),
					chk_receipt_replace: $('input[id=chk_receipt_replace]:checked').val(),
					old_receipt_no: $('#txt_receipt_no').val(),
					txt_area_remark_new: $('#area_remark_new').val(),
					chk_buy_discount: $('input[id=chk_buy_discount]:checked').val(),
					txt_money_discount: $('#txt_money_discount').val(),
					is_print: '<?php echo $is_print; ?>',
					txt_date_rec: $('#txt_date_rec').val(),
					radio_chq_type: $('input[id=radio_chq_type]:checked').val()
					
				},
					function(data){					
						if(data.success){
							$("#divdialogconfirm").remove();
							var txtchkresid = data.res_id.substring(0,2);
							alert(data.message);							
							if(txtchkresid=='RP'){
								print_other_pay(data.res_id,data.receipt_no);
							}
							else
							{
								print_s002_3_4(data.res_id,data.receipt_no);								
							}						
						}else{
							alert(data.message);
						}
					},'json');
				},
				ไม่ใช่: function(){
					$( this ).dialog( "close" );
				}
			}
		});
		
		
      /*  $.post('cashier_api.php',{
            cmd: 'save',
            money: '<?php echo $money; ?>',
            chk_buy_cash: $('input[id=chk_buy_cash]:checked').val(),
            txt_money_cash: $('#txt_money_cash').val(),
            chk_buy_cheque: $('input[id=chk_buy_cheque]:checked').val(),
            txt_money_chq:  $('#txt_money_chq').val(),
            arr_cheque: JSON.stringify(arr_cheque),
            arr_select_inv: JSON.stringify(arr_select_inv),
			service_id: '<?php echo $service_id_list; ?>',
			res_id: '<?php echo $res_id; ?>',
			arr_inv_no: JSON.stringify(arr_inv_no),
			chk_receipt_replace: $('input[id=chk_receipt_replace]:checked').val(),
			old_receipt_no: $('#txt_receipt_no').val()
        },
        function(data){
            if(data.success){
                alert(data.message);
				print_s002_3_4(data.res_id,data.receipt_no);
            }else{
                alert(data.message);
            }
        },'json');*/

    });


//======================================== ของเดิม ===========================================//
/*function ShowPrint(){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/temporary_receipt.php?inv_id=<?php echo $prt; ?>','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq()\"></div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
        }
    });
}*/

//==================== พิมพ์ใบเสร็จรับเงินชั่วคราว ========================//
function print_s002_3_4(res_id,receipt_no){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จรับเงินชั่วคราว\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\">  </div>");
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




/*function print_doc(res_id,service_id,receipt_no,print_status){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/><input type=\"button\" "+print_status+"  name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบจอง\" onclick=\"javascript:window.open('../report/reserve_car_down_pdf_2.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600');\"> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จ\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"&service_id="+service_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); \">  </div>");
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
}*/

//==================== พิมพ์ใบเสร็จรับเงิน (ค่าใช้จ่ายอื่นๆ) ========================//
function print_other_pay(res_id,receipt_no){
    $('body').append('<div id="div_prt"></div>');
    //$('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จ\" onclick=\"javascript:window.open('../report/other_payment_receipt_pdf_1.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"&service_id="+service_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq(); \"> </div>");
	
	$('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จรับเงิน\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf2.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\">  </div>");
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

/*function print_doc(res_id,service_id,receipt_no,print_status){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br/><br/> <input type=\"button\" name=\"btn_print_receive\" id=\"btn_print_receive\" value=\"พิมพ์ใบเสร็จ\" onclick=\"javascript:window.open('../report/reserve_tmp_receipt_pdf.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"&service_id="+service_id+"&receipt_no="+receipt_no+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); \"> </div>");
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
}*/


//============================ พิมพ์ใบจองรถยนต์ =====================================//
/*function print_S002(res_id){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/reserve_car_down_pdf.php?inv_id=<?php echo $prt; ?>&res_id=RS1-1312055','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\"></div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
        }
    });
}*/

//================================= พิมพ์ใบเสร็จรับเงินชั่วคราว ==========================//
/*function print_tmp_receive_S002(res_id){
    $('body').append('<div id="div_prt"></div>');
    $('#div_prt').html("<div style=\"text-align:center\">บันทึกเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์เอกสาร\" onclick=\"javascript:window.open('../report/reserve_car_down_pdf.php?inv_id=<?php echo $prt; ?>&res_id="+res_id+"','receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600'); javascript:CloseDialogChq();\"></div>");
    $('#div_prt').dialog({
        title: 'พิมพ์เอกสาร  ใบสั่งจองรถยนต์ ',
        resizable: false,
        modal: true,  
        width: 300,
        height: 150,
        close: function(ev, ui){
            $('#div_prt').remove();
        }
    });
}*/

function CloseDialogChq(){
    $('#div_prt').remove();
    location.reload();
}

function ChkChqAmt(){
    var sum_chq_money = 0;
    for( i=1; i<=counter; i++ ){
        sum_chq_money += parseFloat($('#txt_cheque_money'+ i).val());
		bank_name = $('#txt_cheque_bank'+i).val();
		branch_name = $('#txt_cheque_branch'+i).val();
    }

    if( $('#txt_money_chq').val() != sum_chq_money ){
        
       $('body').append('<div id="divdialogconfirm"></div>');
        $("#divdialogconfirm").text('ยอดเงินไม่ครบ! ต้องการเพิ่มรายการเช็คหรือแก้ไขยอดเงิน ?');
        $("#divdialogconfirm").dialog({
            title: 'เพิ่มรายการ',
            resizable: false,
            height:140,
            modal: true,
            buttons:{
                "เพิ่มเช็ค": function(){
                    counter++;
                    var after_chq_money = parseFloat($('#txt_money_chq').val())-parseFloat(sum_chq_money);
                    $.get("cashier_api.php?cmd=addcheque&id="+counter+"&after_money="+after_chq_money+"&bank_name="+bank_name+"&branch_name="+branch_name,function(data){
                        $("#DivCheque").append(data);
                    },'html');
                    
                    $( this ).dialog( "close" );
                },
                "แก้ไขยอดเงิน": function(){
                    $( this ).dialog( "close" );
                    $('#txt_cheque_money'+ counter).focus();
                }
            }
        });
    
    }
}
function check_num(e)
{ // ให้พิมพ์ได้เฉพาะตัวเลขและจุด
    var key;
    if(window.event)
	{
        key = window.event.keyCode; // IE
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			window.event.returnValue = false;
		}
    }
	else
	{
        key = e.which; // Firefox       
		if(key <= 57 && key != 33 && key != 34 && key != 35 && key != 36 && key != 37 && key != 38 && key != 39 && key != 40 && key != 41 && key != 42
			&& key != 43 && key != 44 && key != 45 && key != 47)
		{
			// ถ้าเป็นตัวเลขหรือจุดสามารถพิมพ์ได้
		}
		else
		{
			key = e.preventDefault();
		}
	}
};

function DelRowChq(){
    if(counter==1){
        alert('ไม่สามารถลบได้! หากเลือกชำระด้วยเช็ค ต้องมีรายการเช็คอย่างน้อย 1 รายการ !');
        return false;
    }
    $('#add_cheque_'+counter).remove();
    counter--;
}
</script>

<?php
}

elseif($cmd == "save"){
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $money = pg_escape_string($_POST['money']);
    $chk_buy_cash = pg_escape_string($_POST['chk_buy_cash']);
    $txt_money_cash = pg_escape_string($_POST['txt_money_cash']);	
    $chk_buy_cheque = pg_escape_string($_POST['chk_buy_cheque']);
    $txt_money_chq = pg_escape_string($_POST['txt_money_chq']);
    $arr_cheque = json_decode(stripcslashes($_POST["arr_cheque"]));
    $arr_select_inv =  json_decode(stripcslashes($_POST["arr_select_inv"]));
    $service_id = pg_escape_string($_POST['service_id']);
	$res_id = pg_escape_string($_POST['res_id']);
	$tmp_arr_inv_no = json_decode(stripcslashes($_POST["arr_inv_no"]));
	$chk_receipt_replace = pg_escape_string($_POST['chk_receipt_replace']);
	$txt_date_rec = pg_escape_string($_POST['txt_date_rec']);
	$old_receipt_no = pg_escape_string($_POST['old_receipt_no']);
	$txt_area_remark_new = pg_escape_string($_POST['txt_area_remark_new']);
	$chk_buy_discount = pg_escape_string($_POST['chk_buy_discount']);
    $txt_money_discount = pg_escape_string($_POST['txt_money_discount']);
	$radio_chq_type = pg_escape_string($_POST['radio_chq_type']);
	$is_print = pg_escape_string($_POST['is_print']);
	
	$service_id_list = pg_escape_string($_POST['service_id_list']);

	$count_chq = count($arr_cheque)-1;
    $count_inv = count($arr_select_inv)-1;
	


    if($chk_buy_cash == "on" AND $chk_buy_cheque == "on" AND $count_inv > 1){ //ชำระ หลาย invoice ด้วย เงินสด และเช็ครวมกัน OCQ2
        $invoices_status = "OCQ2";
    }elseif($chk_buy_cash != "on" AND $chk_buy_cheque == "on" AND $count_chq > 1 AND $count_inv > 1){ //ชำระ หลาย invoice ด้วยเช็คหลายใบ OCQ2
        $invoices_status = "OCQ2";
    }elseif($chk_buy_cash == "on" AND $chk_buy_cheque == "on" AND $count_chq > 1 AND $count_inv == 1){ //ชำระเงินสด และ เช็คหลายใบ ใน 1 invoice OCAM
        $invoices_status = "OCAM";
    }elseif($chk_buy_cash != "on" AND $chk_buy_cheque == "on" AND $count_chq > 1 AND $count_inv == 1){ //ชำระเช็คอย่างเดียว แต่ใช้เช็คหลายๆ ใบ ใน  1 invoice OCQM
        $invoices_status = "OCQM";
    }elseif($chk_buy_cash == "on" AND $chk_buy_cheque == "on" AND $count_inv == 1){ //ชำระเงินสดและเช็ค ใน 1 invoice OCAQ
        $invoices_status = "OCAQ";
    }elseif($chk_buy_cash != "on" AND $chk_buy_cheque == "on" AND $chk_buy_discount != "on"){ //ชำระเช็คทั้งหมด OCCQ
        $invoices_status = "OCCQ";
    }elseif($chk_buy_cash == "on" AND $chk_buy_cheque != "on" AND $chk_buy_discount != "on"){ //ชำระเงินสดทั้งหมด OCCA
        $invoices_status = "OCCA";
	}elseif($chk_buy_discount == "on" AND $chk_buy_cash != "on" AND $chk_buy_cheque != "on" ){ // รับชำระเป็นส่วนลด ใน 1 invoice OCD1
        $invoices_status = "OCD1";		
	}elseif($chk_buy_discount == "on" AND $chk_buy_cash == "on" AND $chk_buy_cheque != "on" ){ // รับชำระเป็นส่วนลด และเงินสด ใน 1 invoice OCD2
        $invoices_status = "OCD2";		
	}elseif($chk_buy_discount == "on" AND $chk_buy_cash != "on" AND $chk_buy_cheque == "on" ){ // รับชำระเป็นส่วนลด และเช็คใน 1 invoice OCD3
        $invoices_status = "OCD3";				
	}else{
        $txt_error[] = "STATUS ไม่ถูกต้อง";
        $status++; 
    }

	 //$generate_id=@pg_query("select gen_rec_no('$nowdate',0)");
	 //แก้ไขใช้เป็นวันที่รับชำระเงิน
	 $generate_id=@pg_query("select gen_rec_no('$txt_date_rec',0)");
     $o_receipt_no=@pg_fetch_result($generate_id,0);
	 if(empty($o_receipt_no)){
        $txt_error[] = "สร้าง o_receipt ไม่สำเร็จ";
        $status++;
      }
	  
	//============================================== กรณีชำระเป็นเงินสด =======================================================//
    if($chk_buy_cash == "on" ){
		$money_cash_total = $txt_money_cash;
        $sub_status_1 = substr($invoices_status, 0, 2);
        $sub_status_2 = substr($invoices_status, 2, 2);
        
        $arr_other = array();
        foreach($arr_select_inv as $key => $value){
            $txt_hide_inv = $value->txt_hide_inv;
            $arr_txt_hide_inv = explode("#", $txt_hide_inv);//$inv_no#$amount#$cus_id#$service_id_list#$res_id
            $arr_other[$arr_txt_hide_inv[4]][] = $txt_hide_inv;
        }
        
		// ตรวจสอบก่อนว่า มีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
		$qry_chkRow = pg_query("select * from \"Invoices\" where inv_no='$arr_txt_hide_inv[0]' and \"status\" is null and \"cancel\" = 'FALSE' ");
		$row_chkRow = pg_num_rows($qry_chkRow);
		if($row_chkRow == 0){
			$txt_error[] = "มีการทำรายการไปก่อนหน้านี้แล้ว";
			$status++;
		}
		
        $arr_inv_insert = array();
        
        foreach($arr_other AS $other_key => $other_val){
            if(empty($other_key)){
               continue; 
            }
            
           

            $qry = "INSERT INTO \"Otherpays\" (o_receipt,o_date,money_way,money_type,o_prndate,cancel,o_memo,user_id) VALUES 
            ('$o_receipt_no','$txt_date_rec','$sub_status_1','CA','$nowdate','FALSE',DEFAULT,'$_SESSION[ss_iduser]')";
			
			
            if(!$res=@pg_query($qry)){
                $txt_error[] = "INSERT Otherpays ไม่สำเร็จ $qry";
                $status++;
            }
            
            foreach($other_val AS $other_v){
                $arr_txt_hide_inv = explode("#", $other_v);
                //if( $arr_txt_hide_inv[0] != "" AND $arr_txt_hide_inv[1] != "" AND $arr_txt_hide_inv[1] != "0" ){
				if( $arr_txt_hide_inv[0] != "" AND $arr_txt_hide_inv[1] != "" ){

                    if($arr_txt_hide_inv[4] != $other_key){
                        continue;
                    }
                    
					//.ให้ออกใบเสร็จ 0  บาทได้
                    /*if($money_cash_total == 0){
                        break;
                    }*/

                    $qry_service = pg_query("SELECT service_id,amount,vat FROM \"InvoiceDetails\" WHERE inv_no='$arr_txt_hide_inv[0]' AND cancel = 'FALSE' ORDER BY service_id ASC ");
                    while( $res_service = pg_fetch_array($qry_service) ){
                        $service_id = $res_service['service_id'];
                        $service_amount = ($res_service['amount']+$res_service['vat']);
						//.ให้ออกใบเสร็จ 0  บาทได้
                        /* if($money_cash_total == 0){
                            break;
                        } 
						*/
                        if($money_cash_total >= $service_amount){
                            $in_qry="INSERT INTO \"OtherpayDtl\" (o_receipt,inv_no,amount,service_id,status) values ('$o_receipt_no','$arr_txt_hide_inv[0]','$service_amount','$service_id','CA')";
                            if(!$res=@pg_query($in_qry)){
                                $txt_error[] = "INSERT OtherpayDtl ไม่สำเร็จ $in_qry";
                                $status++;
                            }
                            $money_cash_total-=$service_amount;
                            $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $service_amount;
                        }else{
                            $in_qry="INSERT INTO \"OtherpayDtl\" (o_receipt,inv_no,amount,service_id,status) values ('$o_receipt_no','$arr_txt_hide_inv[0]','$money_cash_total','$service_id','CA')";
                            if(!$res=@pg_query($in_qry)){
                                $txt_error[] = "INSERT OtherpayDtl ไม่สำเร็จ $in_qry";
                                $status++;
                            }
                            $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $money_cash_total;
							$money_cash_total = 0;
                            break;
                        }
						

                    }

                    $in_qry="UPDATE \"Invoices\" SET status='$invoices_status' ,receipt_memo = '$txt_area_remark_new' , is_print = '1' WHERE inv_no='$arr_txt_hide_inv[0]' ";
                    if(!$res=@pg_query($in_qry)){
                        $txt_error[] = "UPDATE Invoices ไม่สำเร็จ $in_qry";
                        $status++;
                    }
                }

                
            }//end foreach inv list
        }

    }
// กรณีจ่ายเป็นส่วนลด
if($chk_buy_discount == "on" ){
		$money_cash_total = $txt_money_discount;
        $sub_status_1 = substr($invoices_status, 0, 2);
        $sub_status_2 = substr($invoices_status, 2, 2);
        
        $arr_other = array();
        foreach($arr_select_inv as $key => $value){
            $txt_hide_inv = $value->txt_hide_inv;
            $arr_txt_hide_inv = explode("#", $txt_hide_inv);//$inv_no#$amount#$cus_id#$service_id_list#$res_id

            $arr_other[$arr_txt_hide_inv[4]][] = $txt_hide_inv;
        }
		
		if($chk_buy_cash == "on")
		{
			// ถ้ามีการตรวจสอบเรื่อง Concurrency กรณีชำระเป็นเงินสด แล้ว ไม่ต้องตรวจสอบอีก
		}
		else
		{
			// ตรวจสอบก่อนว่า มีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
			$qry_chkRow = pg_query("select * from \"Invoices\" where inv_no='$arr_txt_hide_inv[0]' and \"status\" is null and \"cancel\" = 'FALSE' ");
			$row_chkRow = pg_num_rows($qry_chkRow);
			if($row_chkRow == 0){
				$txt_error[] = "มีการทำรายการไปก่อนหน้านี้แล้ว";
				$status++;
			}
		}
        
        $arr_inv_insert = array();
        
		foreach($arr_other AS $other_key => $other_val){
            if(empty($other_key)){
               continue; 
            }
            

            $qry = "INSERT INTO \"Discountpays\" (o_receipt,o_date,money_way,money_type,o_prndate,cancel,o_memo,user_id) VALUES 
            ('$o_receipt_no','$txt_date_rec','$sub_status_1','D1','$nowdate','FALSE',DEFAULT,'$_SESSION[ss_iduser]')";
			
			
            if(!$res=@pg_query($qry)){
                $txt_error[] = "INSERT Discountpays ไม่สำเร็จ $qry";
                $status++;
            }
            
            foreach($other_val AS $other_v){
                $arr_txt_hide_inv = explode("#", $other_v);
                if( $arr_txt_hide_inv[0] != "" AND $arr_txt_hide_inv[1] != "" AND $arr_txt_hide_inv[1] != "0" ){

                    if($arr_txt_hide_inv[4] != $other_key){
                        continue;
                    }
                    
                    if($money_cash_total == 0){
                        break;
                    }

                    $qry_service = pg_query("SELECT service_id,amount,vat FROM \"InvoiceDetails\" WHERE inv_no='$arr_txt_hide_inv[0]' AND cancel = 'FALSE' ORDER BY service_id ASC ");
                    while( $res_service = pg_fetch_array($qry_service) ){
                        $service_id = $res_service['service_id'];
                        $service_amount = ($res_service['amount']+$res_service['vat']);

                        if($money_cash_total == 0){
                            break;
                        }

                        if($money_cash_total >= $service_amount){
                            $in_qry="INSERT INTO \"DiscountpayDtl\" (o_receipt,inv_no,amount,service_id,status) values ('$o_receipt_no','$arr_txt_hide_inv[0]','$service_amount','$service_id','D1')";
                            if(!$res=@pg_query($in_qry)){
                                $txt_error[] = "INSERT DiscountpayDtl ไม่สำเร็จ $in_qry";
                                $status++;
                            }
                            $money_cash_total-=$service_amount;
                            $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $service_amount;
                        }else{
                            $in_qry="INSERT INTO \"DiscountpayDtl\" (o_receipt,inv_no,amount,service_id,status) values ('$o_receipt_no','$arr_txt_hide_inv[0]','$money_cash_total','$service_id','D1')";
                            if(!$res=@pg_query($in_qry)){
                                $txt_error[] = "INSERT DiscountpayDtl ไม่สำเร็จ $in_qry";
                                $status++;
                            }
                            $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $money_cash_total;
                            $money_cash_total = 0;
                            break;
                        }
                    }

                    $in_qry="UPDATE \"Invoices\" SET status='$invoices_status' ,receipt_memo = '$txt_area_remark_new' , is_print = '1' WHERE inv_no='$arr_txt_hide_inv[0]' ";
                    if(!$res=@pg_query($in_qry)){
                        $txt_error[] = "UPDATE Invoices ไม่สำเร็จ $in_qry";
                        $status++;
                    } 
                }

                
            }//end foreach inv list
        } 

    }


	//================================================= กรณีจ่ายด้วยเช็ค ===================================================================//
	
	
    if($chk_buy_cheque == "on"){
        $arr_chq_sumamt = array();

		
        foreach($arr_cheque as $k => $v){
            $cheque_no = $v->cheque_no;
            $cheque_bank = $v->cheque_bank;
            $cheque_branch = $v->cheque_branch;
            $cheque_date = $v->cheque_date;
            $cheque_money = $v->cheque_money;
            $cb_accno = $v->cb_accno;

            
            if($cheque_no == "" OR $cheque_bank == "" OR $cheque_branch == "" OR $cheque_date == "" OR $cheque_money == "" OR $cb_accno == ""){
                continue;
            }
            	
            //insert chq gen เลขที่ running_no 
			$generate_id=@pg_query("select generate_id('$txt_date_rec',$_SESSION[ss_office_id],5)");
			$running_no=@pg_fetch_result($generate_id,0);	

			$qry = "INSERT INTO \"Cheques\" (running_no,cheque_no,bank_name,bank_branch,amt_on_cheque,receive_date,date_on_cheque,out_bangkok,acc_bank_enter,accept,accept_by_user,is_transfer) VALUES 
            ('$running_no','$cheque_no','$cheque_bank','$cheque_branch','$cheque_money','$txt_date_rec','$cheque_date',DEFAULT,'$cb_accno','TRUE','$_SESSION[ss_iduser]','$radio_chq_type')";

			
			
            if(!$res=@pg_query($qry)){
                $txt_error[] = "\n\nชื่อธนาคารอาจยาวเกินไป โปรดติดต่อเจ้าหน้าที่ไอที";
                $status++;
            }

            $money_chq_total = $cheque_money;
            
            foreach($arr_select_inv as $key => $value){
                $txt_hide_inv = $value->txt_hide_inv;
                $arr_txt_hide_inv = explode("#", $txt_hide_inv);
				
				if( $arr_txt_hide_inv[0] != "" AND $arr_txt_hide_inv[1] != "" AND $arr_txt_hide_inv[1] != "0" AND $arr_txt_hide_inv[2] != "" AND $arr_txt_hide_inv[4] != "" ){

					if($money_chq_total == 0){
                        break;
                    }
					
					// ถ้ายังไม่เคยตรวจสอบ Concurrency ในเช็ค
					if($check_Concurrency != "y")
					{
						if($chk_buy_cash == "on" || $chk_buy_discount == "on")
						{
							// ถ้ามีการตรวจสอบเรื่อง Concurrency กรณีชำระเป็นเงินสด หรือ กรณีจ่ายเป็นส่วนลด แล้ว ไม่ต้องตรวจสอบอีก
						}
						else
						{
							// ตรวจสอบก่อนว่า มีการทำรายการไปก่อนหน้านี้แล้วหรือยัง
							$qry_chkRow = pg_query("select * from \"Invoices\" where inv_no='$arr_txt_hide_inv[0]' and \"status\" is null and \"cancel\" = 'FALSE' ");
							$row_chkRow = pg_num_rows($qry_chkRow);
							if($row_chkRow == 0){
								$txt_error[] = "มีการทำรายการไปก่อนหน้านี้แล้ว";
								$status++;
							}
						}
						$check_Concurrency = "y"; // บอกว่ามีการตรวจสอบแล้ว
					}
                    
                    $qry_service = pg_query("SELECT service_id,amount,vat FROM \"InvoiceDetails\" WHERE inv_no='$arr_txt_hide_inv[0]' AND cancel = 'FALSE' ORDER BY service_id ASC ");
                    while( $res_service = pg_fetch_array($qry_service) ){
                        $service_id = $res_service['service_id'];
                        $service_amount = ($res_service['amount']+$res_service['vat']);
                        
                        if($money_chq_total == 0){
                            break;
                        }

                        if( $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] == 0 OR $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] == "" ){//หากเป็นรายการที่ยังไม่เคยจ่าย ให้จ่ายด้วยเช็คจำนวนเงินเต็ม
                            if($money_chq_total >= $service_amount){//ตรวจสอบยอดเงินของเช็คใบนั้นๆ ว่าพอจ่าย service นี้ หรือไม่
                                $qry = "INSERT INTO \"ChequeDetails\" (running_no,cheque_no,cus_id,res_id,inv_no,service_id,cus_amount,prn_date,receipt_no) VALUES 
                                ('$running_no','$cheque_no','$arr_txt_hide_inv[2]','$arr_txt_hide_inv[4]','$arr_txt_hide_inv[0]','$service_id','$service_amount',DEFAULT,'$o_receipt_no')";
                                if(!$res=@pg_query($qry)){
                                    $txt_error[] = "INSERT ChequeDetails ไม่สำเร็จ $qry";
                                    $status++;
                                }
                                
                                $arr_chq_sumamt[$arr_txt_hide_inv[0]][] = $service_amount;
                                
                                $money_chq_total-=$service_amount;
                                $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $service_amount;
                            }else{//หากไม่พอจ่าย ให้จ่ายเฉพาะที่เหลือของเช็คนั้นๆ
                                $qry = "INSERT INTO \"ChequeDetails\" (running_no,cheque_no,cus_id,res_id,inv_no,service_id,cus_amount,prn_date,receipt_no) VALUES 
                                ('$running_no','$cheque_no','$arr_txt_hide_inv[2]','$arr_txt_hide_inv[4]','$arr_txt_hide_inv[0]','$service_id','$money_chq_total',DEFAULT,'$o_receipt_no')";
                                if(!$res=@pg_query($qry)){
                                    $txt_error[] = "INSERT ChequeDetails ไม่สำเร็จ $qry";
                                    $status++;
                                }
                                
                                $arr_chq_sumamt[$arr_txt_hide_inv[0]][] = $money_chq_total;

                                $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $money_chq_total;
                                $money_chq_total = 0;
                                break;
                            }
                        }else{
                            if( $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] == $service_amount ){//หากจ่ายด้วยเงินสดไปแล้ว ให้ข้ามไป
                                continue;
                            }

                            $lob_by_cash = $service_amount-$arr_inv_insert[$arr_txt_hide_inv[0]][$service_id];

                            if($money_chq_total >= $lob_by_cash){
                                $qry = "INSERT INTO \"ChequeDetails\" (running_no,cheque_no,cus_id,res_id,inv_no,service_id,cus_amount,prn_date,receipt_no) VALUES 
                                ('$running_no','$cheque_no','$arr_txt_hide_inv[2]','$arr_txt_hide_inv[4]','$arr_txt_hide_inv[0]','$service_id','$lob_by_cash',DEFAULT,'$o_receipt_no')";
                                if(!$res=@pg_query($qry)){
                                    $txt_error[] = "INSERT ChequeDetails ไม่สำเร็จ $qry";
                                    $status++;
                                }
                                
                                $arr_chq_sumamt[$arr_txt_hide_inv[0]][] = $lob_by_cash;

                                $money_chq_total-=$lob_by_cash;
                                $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $service_amount;
                            }else{
                                $qry = "INSERT INTO \"ChequeDetails\" (running_no,cheque_no,cus_id,res_id,inv_no,service_id,cus_amount,prn_date,receipt_no) VALUES 
                                ('$running_no','$cheque_no','$arr_txt_hide_inv[2]','$arr_txt_hide_inv[4]','$arr_txt_hide_inv[0]','$service_id','$money_chq_total',DEFAULT,'$o_receipt_no')";
                                if(!$res=@pg_query($qry)){
                                    $txt_error[] = "INSERT ChequeDetails ไม่สำเร็จ $qry";
                                    $status++;
                                }
                                
                                $arr_chq_sumamt[$arr_txt_hide_inv[0]][] = $money_chq_total;
                                
                                $arr_inv_insert[$arr_txt_hide_inv[0]][$service_id] = $money_chq_total;
                                $money_chq_total = 0;
                                break;
                            }
                        }
                    }

                    $in_qry="UPDATE \"Invoices\" SET status='$invoices_status',receipt_memo = '$txt_area_remark_new' , is_print = '1' WHERE inv_no='$arr_txt_hide_inv[0]' ";
                    if(!$res=@pg_query($in_qry)){
                        $txt_error[] = "UPDATE Invoices ไม่สำเร็จ $in_qry";
                        $status++;
                    }

                }
            }
        }
    }
    
    
    
    
    foreach($arr_select_inv as $key => $value){
        $txt_hide_inv = $value->txt_hide_inv;
        $arr_txt_hide_inv = explode("#", $txt_hide_inv);//$inv_no#$amount#$cus_id#$service_id_list#$res_id
        if( !empty($arr_txt_hide_inv[3]) ){
            $arr_service_update = explode(",", $arr_txt_hide_inv[3]);
            foreach ($arr_service_update as $v) {
                if($v == "S001"){ //มัดจำป้ายแดง
                    $qry = pg_query("SELECT car_id FROM \"Reserves\" WHERE res_id='$arr_txt_hide_inv[4]' ");
                    if( $res = pg_fetch_array($qry) ){
                        $car_id = $res['car_id'];
                        
                       $qry = pg_query("SELECT license_plate FROM \"Cars\" WHERE car_id='$car_id' ");
                        if( $res = pg_fetch_array($qry) ){
                            $license_plate = $res['license_plate'];
                        
                            $qry = pg_query("SELECT new_plate FROM \"P_NewCarPlate\" WHERE license_plate='$license_plate' AND for_sale='TRUE' AND date_return IS NULL ");
                            if( $res = pg_fetch_array($qry) ){
                                $new_plate = $res['new_plate'];
                                
                                $update_product=@pg_query("select update_product('$license_plate','P014','$o_receipt_no','$new_plate')");
                                $res_product=@pg_fetch_result($update_product,0);
                                if(!$res_product){
                                    $txt_error[] = "update_product P014 ไม่สำเร็จ : $license_plate,P014,$o_receipt_no,$new_plate";
                                    $status++;
                                }
                            }//end select new_plate from P_NewCarPlate
                        }//end select license_plate from Cars
                    }//end select car_id from Reserves
                }//end check P014
				
				if($v == "S003" OR $V == "S004"){
					$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$arr_txt_hide_inv[4]'  AND cancel='FALSE' ");
					if($res = pg_fetch_array($qry)){
						$car_id = $res['car_id'];
						$down_price = $res['down_price'];
					}
			
					if(empty($car_id)){
					   $txt_error[] = "เกิดข้อผิดผลาด พบรายการยังไม่เลือกรถ [เลขจอง $arr_txt_hide_inv[4]  ใบแจ้งหนี้ $arr_txt_hide_inv[0]]";
					  // $txt_error[] = "เกิดข้อผิดผลาด พบรายการยังไม่เลือกรถ!!!";
						$status++;
						break;
					}
				}
				
            }//end foreach service
        }//end check empty data
    }//end foreach inv list

	
/*case1
ไม่เจาะจงรถ
1. รับชำระเงินจอง s002 ให้ชำระได้
2.s003,s004 รับชำระไม่ได้ เพราะยังไม่ได้เลือกรถ
case2 
1. กรณีเจาะจงหรือไม่เจาะจง cashier จะไม่ gen idno*/

   /* foreach($arr_select_inv as $key => $value){//UPDATE IDNO กรณี inv ที่ออกรถเท่านั้น
        $txt_hide_inv = $value->txt_hide_inv;
        $arr_txt_hide_inv = explode("#", $txt_hide_inv);//$inv_no#$amount#$cus_id#$service_id_list#$res_id
        $inv_no = $arr_txt_hide_inv[0];
        $res_id = $arr_txt_hide_inv[4];
        
        $qry = pg_query("SELECT service_id FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel='FALSE' LIMIT 1 ");
        if($res = pg_fetch_array($qry)){
            $service_id = $res['service_id'];
            $GetConstantVar = GetConstantVar($service_id);
        }
		
		//กรณีไม่เจาะจงรถ ถ้าเป็น S003,S004 จะไม่สามารถออกรถได้
		if( $service_id == 'S003' OR $service_id == 'S004' ){
			$qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' AND \"IDNO\" IS NULL AND cancel='FALSE' ");
            if($res = pg_fetch_array($qry)){
                $car_id = $res['car_id'];
                $down_price = $res['down_price'];
			}
			
			if(empty($car_id)){
               // $txt_error[] = "เกิดข้อผิดผลาด พบรายการยังไม่เลือกรถ [เลขจอง $res_id ใบแจ้งหนี้ $inv_no]";
			   $txt_error[] = "เกิดข้อผิดผลาด พบรายการยังไม่เลือกรถ!!!";
                $status++;
                break;
            }
		}
		
		
        /*if( substr($GetConstantVar, 0, 3) == "CAR" ){//ตรวจสอบหากเป็น service ไม่ต้องทำ

            $qry = pg_query("SELECT * FROM \"Reserves\" WHERE res_id='$res_id' AND \"IDNO\" IS NULL AND cancel='FALSE' ");
            if($res = pg_fetch_array($qry)){
                $car_id = $res['car_id'];
                $down_price = $res['down_price'];
                
                $sum_amount = 0;
                $qry_amt = pg_query("SELECT SUM(amount) AS amount FROM \"VAccPayment\" WHERE res_id='$res_id' AND constant_var LIKE 'CAR%' ");
                if($res_amt = pg_fetch_array($qry_amt)){
                    $sum_amount = $res_amt['amount'];
                }

                $sum_arr_chq_sumamt = 0;
                $sum_arr_chq_sumamt = @array_sum($arr_chq_sumamt[$inv_no]);
                $sum_lob_amount_chq = $sum_amount-$sum_arr_chq_sumamt;

                if($down_price == $sum_lob_amount_chq){
				
				
                    if(empty($car_id)){
                        $txt_error[] = "เกิดข้อผิดผลาด พบรายการยังไม่เลือกรถ [เลขจอง $res_id ใบแจ้งหนี้ $inv_no]";
                        $status++;
                        break;
                    }
                    
                    $qry_stkcar = pg_query("SELECT po_id FROM \"VStockCars\" WHERE car_id='$car_id' ");
                    if( $res_stkcar= pg_fetch_array($qry_stkcar) ){
                        $stkcar_po_id = $res_stkcar['po_id'];
                    }

                    if(substr($stkcar_po_id, 0, 2) == "PO"){
                        $generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],2)");
                        $genidno=@pg_fetch_result($generate_id,0);
                    }else{
                        $genidno=$stkcar_po_id;
                    }
                    
                    if(empty($genidno)){
                        $txt_error[] = "เกิดข้อผิดผลาด ไม่สามารถสร้าง เลขที่สัญญา (IDNO) ได้ [เลขจอง $res_id ใบแจ้งหนี้ $inv_no]";
                        $status++;
                        break;
                    }

                    $in_qry="UPDATE \"Reserves\" SET \"IDNO\"='$genidno',receive_date='$nowdate' WHERE res_id='$res_id' ";
                    if(!$res=@pg_query($in_qry)){
                        $txt_error[] = "UPDATE Reserves ไม่สำเร็จ $in_qry";
                        $status++;
                    }
                }
            }
           
        }
 
    }//end foreach*/

	$res_id = $arr_txt_hide_inv[4];
	// ชำระเงิน ตัวใหม่
	if($chk_buy_cash == 'on'){$cash_amount = $txt_money_cash;}else{$cash_amount = '0';}
	if($chk_buy_cheque == 'on'){$chq_amount = $txt_money_chq;}else{$chq_amount = '0';}
	if($chk_buy_discount == 'on'){$discount_amount = $txt_money_discount;}else{$discount_amount = '0';}
	
	$exe_cashier = exec_cashier($o_receipt_no,$chq_amount,$cash_amount,$res_id,$_SESSION["ss_iduser"],$discount_amount,$txt_date_rec);
	
	$print_count = print_count($o_receipt_no,'2');
	$_SESSION['ss_print_count'] = $print_count;
	
	//บันทึกประวัติการพิมพ์
	$in_doc_print_logs = "INSERT INTO doc_print_logs (doc_no,doc_type,print_count,id_user,print_date)
											  VALUES ('$o_receipt_no','2','$print_count','$_SESSION[ss_iduser]','$nowDateTime')";
	if(!$res=@pg_query($in_doc_print_logs)){
        $txt_error[] = "INSERT doc_print_logs ไม่สำเร็จ $in_doc_print_logs";
        $status++;
    }
	
	//บันทึกประวัติการออกใบเสร็จใหม่แทนใบเสร็จเดิม
	if($chk_receipt_replace == 'on'){//มีการออกใบเสร็จใหม่แทนใบเสร็จเดิม
		$in_receipt_renew_logs = " INSERT INTO receipt_renew_logs (old_receipt_no,new_receipt_no,id_user,action_date)
														   VALUES ('$old_receipt_no','$o_receipt_no','$_SESSION[ss_iduser]','$nowDateTime') ";
		if(!$res=pg_query($in_receipt_renew_logs)){
			$txt_error[] = "INSERT receipt_renew_logs ไม่สำเร็จ $in_receipt_renew_logs";
			$status++;
		}
	}else{
		if($is_print=='1'){
			$txt_error[] = "กรุณาเลือกออกใบเสร็จแทนใบเสร็จเดิมที่มีการยกเลิก";
			$status++;
		}
	}
	

    if($status == 0){
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว"; 
		$data['res_id'] = $res_id;
		$data['receipt_no'] = $o_receipt_no;
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้!!! \n$txt_error[0]";
    }
    echo json_encode($data);
}

elseif($cmd == "addcheque"){
    $id = pg_escape_string($_GET['id']);
    $after_money = pg_escape_string($_GET['after_money']);
	$get_bank_name = pg_escape_string($_GET['bank_name']);
	$branch_name = pg_escape_string($_GET['branch_name']);
?>
<div id="add_cheque_<?php echo $id; ?>" style="border: 1px dashed #D0D0D0; margin-top:1px; padding:3px; background-color:#F5F5F5">

<div style="margin-top:5px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td colspan="10">
<b>#<?php echo $id; ?></b> | เช็คสั่งจ่ายบริษัท :
<select name="cb_accno<?php echo $id; ?>" id="cb_accno<?php echo $id; ?>">
<?php
$qry = pg_query("SELECT * FROM \"BankEnter\" ORDER BY \"accname\" ASC");
while( $res = pg_fetch_array($qry) ){
    $accno = $res['accno'];
	$bankno = $res['bankno'];
	$accname = $res['accname'];
	$bank_name = $res['bankname'];
?>
    <option value="<?php echo "$accno";?>" <?php if($accno == '0576019909') echo "selected"; ?>><?php echo "$accno#$bank_name#$accname"; ?></option>
<?php
}
?>
</select>
    </td>
</tr>
<tr>
    <td align="left">ธนาคาร&nbsp;</td>
	<td>
		<select name="txt_cheque_bank<?php echo $id; ?>" id="txt_cheque_bank<?php echo $id; ?>">
			<option value="">เลือกธนาคาร</option>
			<?php
			$qry = pg_query("SELECT * FROM \"BankInThai\" ORDER BY \"BankName\" ASC");
			while( $res = pg_fetch_array($qry) ){
				$bank_code = $res['BankCode'];
				$bank_name = $res['BankName'];
			?>
				<option value="<?php echo "$bank_name"; ?>" <?php if($bank_name == $get_bank_name) echo "selected"; ?>><?php echo "$bank_name"; ?></option>
			<?php
			}
			?>
		</select>
		
	<!--<input type="text" name="txt_cheque_bank<?php// echo $id; ?>" id="txt_cheque_bank<?php// echo $id; ?>" style="width:80px">-->
	</td>
    <td align="right">สาขา&nbsp;</td><td><input type="text" name="txt_cheque_branch<?php echo $id; ?>" id="txt_cheque_branch<?php echo $id; ?>" style="width:80px" value="<?php echo $branch_name; ?>"></td>
    <td align="right">เลขที่เช็ค&nbsp;</td><td><input type="text" name="txt_cheque_no<?php echo $id; ?>" id="txt_cheque_no<?php echo $id; ?>" style="width:60px" onkeypress="check_num(event);"></td>
    <td align="right">วันที่บนเช็ค&nbsp;</td><td><input type="text" name="txt_cheque_date<?php echo $id; ?>" id="txt_cheque_date<?php echo $id; ?>" style="width:80px; text-align:center" value="<?php echo $nowdate; ?>"></td>
    <td align="right">จำนวนเงิน&nbsp;</td><td><input type="text" name="txt_cheque_money<?php echo $id; ?>" id="txt_cheque_money<?php echo $id; ?>" style="width:80px; text-align:right" value="<?php echo $after_money; ?>" onblur="javascript:ChkChqAmt()" onkeypress="check_num(event);"></td>
</tr>
</table>
</div>
    
</div>

<script>
$("#txt_cheque_date<?php echo $id; ?>").datepicker({
    showOn: 'button',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});
</script>
<?php
}
?>