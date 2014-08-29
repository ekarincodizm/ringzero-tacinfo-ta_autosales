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
    $product_id = $res_res['product_id'];    
    $remark = $res_res['remark'];
        if(empty($car_id)){
            $arr_remark = explode("\n",$remark);
            $arr_remark1 = explode("=",$arr_remark[0]);
            $arr_remark2 = explode("=",$arr_remark[1]);
        }
    $down_price = $res_res['down_price'];
    $car_price = $res_res['car_price'];
    $num_install = $res_res['num_install'];
    $installment = $res_res['installment'];
    $finance_price = $res_res['finance_price'];
    $finance_cus_id = $res_res['finance_cus_id'];
    
    $type_insure = $res_res['type_insure'];
    $use_radio = $res_res['use_radio'];
    $user_id = $res_res['user_id'];
}

$qry_resd = pg_query("SELECT SUM(amount) as sumamount FROM \"VAccPayment\" WHERE res_id='$id' AND o_receipt IS NOT NULL ");
if($res_resd = pg_fetch_array($qry_resd)){
    $appointment_amt = $car_price-$res_resd['sumamount'];
}else{
    $appointment_amt = $car_price;
}

$user_name = GetUserName($user_id);
$cus_name = GetCusName($cus_id);
$finance_cus_name = GetCusName($finance_cus_id);

$money_buy_pay = $car_price-$finance_price;
?>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">ออกรถ</a></li>
        <li><a href="#tabs-2">จองเพิ่ม</a></li>
    </ul>
    <div id="tabs-1" style="padding:10px 5px 10px 5px;">

<div style="float:left">
<b>ผู้จอง : </b><?php echo $cus_name; ?>
<br>
<b>ผู้รับจอง : <?php echo $user_name; ?></b>
</div>
<div style="float:right">

<?php if(empty($car_id)){ ?>
    <input style="font-size:11px" type="button" name="btnSelectCars" id="btnSelectCars" value="เลือกรถ">
<?php }else{ ?>
    <input style="font-size:11px" type="button" name="btnSelectCars" id="btnSelectCars" value="เลือกรถ" disabled>
<?php } ?>

<input style="font-size:11px" type="button" name="btnUpdateMoney1" id="btnUpdateMoney1" value="เปลี่ยนแปลงการจอง">

</div>
<div style="clear:both"></div>

<div class="linedotted"></div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td valign="top" width="50%">
<div style="margin-top:10px">
<?php
if(empty($car_id)){
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150"><b>รูปแบบการจอง :</b></td><td>ไม่เจาะจงรถ</td>
</tr>
<tr>
    <td><b>รูปแบบรถ :</b></td><td><?php echo $arr_remark1[1]; ?></td>
</tr>
<tr>
    <td><b>สีรถ :</b></td><td><?php echo $arr_remark2[1]; ?></td>
</tr>
<tr>
    <td><b>ประกันประเภท :</b></td><td><?php echo $type_insure; ?></td>
</tr>
<tr>
    <td><b>ติดตั้งเครื่องวิทยุสื่อสาร :</b></td><td><?php if($use_radio == 't') echo "ติดตั้ง"; else echo "ไม่ติดตั้ง"; ?></td>
</tr>
</table>
<?php
}else{
    $qry_cname = pg_query("SELECT * FROM \"Cars\" WHERE car_id='$car_id' AND cancel='FALSE' ");
    if($res_cname = pg_fetch_array($qry_cname)){
        $car_license_plate = $res_cname['license_plate'];
        $car_num = $res_cname['car_num'];
        $mar_num = $res_cname['mar_num'];
        $car_year = $res_cname['car_year'];
        $color = $res_cname['color'];
    }
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150"><b>รูปแบบการจอง :</b></td><td>เจาะจงรถ</td>
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
    <td><b>สีรถ :</b></td><td><?php echo $color; ?></td>
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
    <td><b>ค่างวด :</b></td><td><?php echo $installment; ?></td>
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
<b>รายละเอียดการจอง/มัดจำ ที่ได้ชำระไว้</b><br />
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
$j = 0;
$qry_resdt = pg_query("SELECT * FROM \"VAccPayment\" WHERE res_id='$id' AND o_receipt IS NOT NULL ORDER BY inv_no ASC");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    
    $money_buy_pay -= $amount;
    
    $qry_other = pg_query("SELECT money_way,money_type FROM \"Otherpays\" WHERE o_receipt='$o_receipt' ");
    if($res_other = pg_fetch_array($qry_other)){
        $money_way = $res_other['money_way'];
        $money_type = $res_other['money_type'];
    }
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $inv_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$money_type/$money_way"; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=4 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>

<?php
$qry_vidt_num = 0;
$qry_vidt = pg_query("SELECT b.receive_date,b.bank_name,b.cheque_no,b.date_on_cheque,b.cus_amount FROM \"VInvDetail\" a LEFT JOIN \"VChequeDetail\" b ON a.inv_no=b.inv_no 
WHERE a.res_id ='$id' AND a.status='OCCQ' AND a.cancel = 'FALSE' AND b.accept = 'TRUE' AND b.is_pass = 'FALSE' 
ORDER BY a.inv_no ASC ");
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
    <td>ยอดเงิน</td>
</tr>
<?php
while($res_vidt = pg_fetch_array($qry_vidt)){
    $receive_date = $res_vidt['receive_date'];
    $bank_name = $res_vidt['bank_name'];
    $cheque_no = $res_vidt['cheque_no'];
    $date_on_cheque = $res_vidt['date_on_cheque'];
    $cus_amount = $res_vidt['cus_amount'];
    
    $money_buy_pay -= $cus_amount;
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $receive_date; ?></td>
    <td align="left"><?php echo $bank_name; ?></td>
    <td align="left"><?php echo $cheque_no; ?></td>
    <td align="center"><?php echo $date_on_cheque; ?></td>
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

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="50%" valign="top">

<div style="margin-top:10px">
<b>ยอดเงินที่เหลือ : </b>
<span id="span_money_lost"><?php echo $money_buy_pay; ?></span> บาท<br />
</div>

<div style="margin-top:10px">
<b>ชำระโดย</b><br />
<input type="checkbox" name="chk_dialog2_buy_cash" id="chk_dialog2_buy_cash"> เงินสด
<span id="divdialog2_cash" style="display:none">&nbsp;ยอดเงินสด <input type="text" name="txt_dialog2_cash" id="txt_dialog2_cash" size="10"></span>
<br />
<input type="checkbox" name="chk_dialog2_buy_cheque" id="chk_dialog2_buy_cheque"> เช็ค
<div id="divdialog2_cheque" style="margin-top:10px; margin-left:25px; display:none">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ธนาคาร</td><td><input type="text" name="txt_dialog2_cheque_bank" id="txt_dialog2_cheque_bank" size="10"></td>
</tr>
<tr>
    <td>สาขา</td><td><input type="text" name="txt_dialog2_cheque_banch" id="txt_dialog2_cheque_banch" size="10"></td>
</tr>
<tr>
    <td>เลขที่เช็ค</td><td><input type="text" name="txt_dialog2_cheque_no" id="txt_dialog2_cheque_no"size="10"></td>
</tr>
<tr>
    <td>วันที่บนเช็ค</td><td><input type="text" name="txt_dialog2_cheque_date" id="txt_dialog2_cheque_date" size="10" value="<?php echo $nowdate; ?>"></td>
</tr>
<tr>
    <td>ยอดเงินบนเช็ค</td><td><input type="text" name="txt_dialog2_cheque_monny" id="txt_dialog2_cheque_monny" size="10"></td>
</tr>
<tr>
    <td>จังหวัดเช็ค</td><td>
<input type="radio" name="radio_dialog2_cheque_prov" id="radio_dialog2_cheque_prov" value="1" checked> กรุงเทพ <input type="radio" name="radio_dialog2_cheque_prov" id="radio_dialog2_cheque_prov" value="2"> ต่างจังหวัด
    </td>
</tr>
</table>
</div>
</div>
    </td>
    <td width="50%" valign="top">
<div style="margin-top:10px">
<b>หมายเหตุ</b><br />
<textarea name="area_remark" id="area_remark" rows="5" cols="50"></textarea>
</div>
    </td>
</tr>
</table>

<div class="linedotted"></div>

<div style="margin-top:10px; text-align:right">
<?php
if($num_install == 0){
    echo "<input type=\"hidden\" name=\"txt_hid_dialog2_type\" id=\"txt_hid_dialog2_type\" value=\"1\">";
}else{
    echo "<input type=\"hidden\" name=\"txt_hid_dialog2_type\" id=\"txt_hid_dialog2_type\" value=\"2\">";
}
?>
<input type="button" name="btnDialog2Save" id="btnDialog2Save" value="บันทึก">
</div>

    </div>
    <div id="tabs-2" style="padding:10px 5px 10px 5px;">

<div style="float:left">
<b>ผู้จอง : </b><?php echo $cus_name; ?>
<br>
<b>ผู้รับจอง : <?php echo $user_name; ?></b>
</div>
<div style="float:right">

<?php if(empty($car_id)){ ?>
    <input style="font-size:11px" type="button" name="btnSelectCars2" id="btnSelectCars2" value="เลือกรถ">
<?php }else{ ?>
    <input style="font-size:11px" type="button" name="btnSelectCars2" id="btnSelectCars2" value="เลือกรถ" disabled>
<?php } ?>

<input style="font-size:11px" type="button" name="btnUpdateMoney2" id="btnUpdateMoney2" value="เปลี่ยนแปลงการจอง">

</div>
<div style="clear:both"></div>

<div class="linedotted"></div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td valign="top" width="50%">
<div style="margin-top:10px">
<?php
if(empty($car_id)){
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150"><b>รูปแบบการจอง :</b></td><td>ไม่เจาะจงรถ</td>
</tr>
<tr>
    <td><b>รูปแบบรถ :</b></td><td><?php echo $arr_remark1[1]; ?></td>
</tr>
<tr>
    <td><b>สีรถ :</b></td><td><?php echo $arr_remark2[1]; ?></td>
</tr>
<tr>
    <td><b>ประกันประเภท :</b></td><td><?php echo $type_insure; ?></td>
</tr>
<tr>
    <td><b>ติดตั้งเครื่องวิทยุสื่อสาร :</b></td><td><?php if($use_radio == 't') echo "ติดตั้ง"; else echo "ไม่ติดตั้ง"; ?></td>
</tr>
</table>
<?php
}else{
?>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="150"><b>รูปแบบการจอง :</b></td><td>เจาะจงรถ</td>
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
    <td><b>สีรถ :</b></td><td><?php echo $color; ?></td>
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
    <td><b>ค่างวด :</b></td><td><?php echo $installment; ?></td>
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
<b>รายละเอียดการจอง/มัดจำ ที่ได้ชำระไว้</b><br />
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#DEDEBE" style="font-weight:bold; text-align:center">
    <td>วันที่</td>
    <td>เลขที่ใบเสร็จ</td>
    <td>ยอดเงิน</td>
    <td>สถานะ</td>
</tr>
<?php
$j = 0;
$qry_resdt = pg_query("SELECT * FROM \"VAccPayment\" WHERE res_id='$id' AND o_receipt IS NOT NULL ORDER BY inv_no ASC");
while($res_resdt = pg_fetch_array($qry_resdt)){
    $j++;
    $inv_date = $res_resdt['inv_date'];
    $inv_no = $res_resdt['inv_no'];
    $amount = $res_resdt['amount'];
    $o_receipt = $res_resdt['o_receipt'];
    
    $qry_other = pg_query("SELECT money_way,money_type FROM \"Otherpays\" WHERE o_receipt='$o_receipt' ");
    if($res_other = pg_fetch_array($qry_other)){
        $money_way = $res_other['money_way'];
        $money_type = $res_other['money_type'];
    }
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $inv_date; ?></td>
    <td align="center"><?php echo $o_receipt; ?></td>
    <td align="right"><?php echo number_format($amount,2); ?></td>
    <td align="center"><?php echo "$money_type/$money_way"; ?></td>
</tr>
<?php
}

if($j == 0){
    echo "<tr><td colspan=4 align=center>- ไม่พบรายการ -</td></tr>";
}
?>
</table>

<?php
$qry_vidt_num = 0;
$qry_vidt = pg_query("SELECT b.receive_date,b.bank_name,b.cheque_no,b.date_on_cheque,b.cus_amount FROM \"VInvDetail\" a LEFT JOIN \"VChequeDetail\" b ON a.inv_no=b.inv_no 
WHERE a.res_id ='$id' AND a.status='OCCQ' AND a.cancel = 'FALSE' AND b.accept = 'TRUE' AND b.is_pass = 'FALSE' 
ORDER BY a.inv_no ASC ");
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
    <td>ยอดเงิน</td>
</tr>
<?php
while($res_vidt = pg_fetch_array($qry_vidt)){
    $receive_date = $res_vidt['receive_date'];
    $bank_name = $res_vidt['bank_name'];
    $cheque_no = $res_vidt['cheque_no'];
    $date_on_cheque = $res_vidt['date_on_cheque'];
    $cus_amount = $res_vidt['cus_amount'];
?>
<tr bgcolor="#FFFFFF">
    <td align="center"><?php echo $receive_date; ?></td>
    <td align="left"><?php echo $bank_name; ?></td>
    <td align="left"><?php echo $cheque_no; ?></td>
    <td align="center"><?php echo $date_on_cheque; ?></td>
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
ยอดเงินจอง <input type="text" name="txt_moneyreserv" id="txt_moneyreserv" size="10" onkeyup="javascript:SummaryDialog()">&nbsp;&nbsp;&nbsp;&nbsp;วันที่ต้องการออกรถ <input type="text" name="txt_dateoutcar" id="txt_dateoutcar" size="10" value="<?php echo $nowdate; ?>">&nbsp;&nbsp;&nbsp;&nbsp;ยอดคงเหลือ <b><span id="span_appointment_amt"><?php echo $appointment_amt; ?></span></b> บาท.
</div>

<div style="margin-top:10px">
<b>ชำระโดย</b><br />
<input type="checkbox" name="chk_dialog_buy_cash" id="chk_dialog_buy_cash"> เงินสด
<span id="divdialog_cash" style="display:none">&nbsp;ยอดเงินสด <input type="text" name="txt_dialog_cash" id="txt_dialog_cash" size="10"></span>
<br />
<input type="checkbox" name="chk_dialog_buy_cheque" id="chk_dialog_buy_cheque"> เช็ค
<div id="divdialog_cheque" style="margin-top:10px; margin-left:25px; display:none">
<table cellpadding="2" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ธนาคาร</td><td><input type="text" name="txt_dialog_cheque_bank" id="txt_dialog_cheque_bank" size="10"></td>
</tr>
<tr>
    <td>สาขา</td><td><input type="text" name="txt_dialog_cheque_banch" id="txt_dialog_cheque_banch" size="10"></td>
</tr>
<tr>
    <td>เลขที่เช็ค</td><td><input type="text" name="txt_dialog_cheque_no" id="txt_dialog_cheque_no"size="10"></td>
</tr>
<tr>
    <td>วันที่บนเช็ค</td><td><input type="text" name="txt_dialog_cheque_date" id="txt_dialog_cheque_date" size="10" value="<?php echo $nowdate; ?>"></td>
</tr>
<tr>
    <td>ยอดเงินบนเช็ค</td><td><input type="text" name="txt_dialog_cheque_monny" id="txt_dialog_cheque_monny" size="10"></td>
</tr>
<tr>
    <td>จังหวัดเช็ค</td><td>
<input type="radio" name="radio_dialog_cheque_prov" id="radio_dialog_cheque_prov" value="1" checked> กรุงเทพ <input type="radio" name="radio_dialog_cheque_prov" id="radio_dialog_cheque_prov" value="2"> ต่างจังหวัด
    </td>
</tr>
</table>
</div>
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
<input type="button" name="btnDialogSave" id="btnDialogSave" value="บันทึก">
</div>


    </div>
</div>

<script>
$(function(){
    $("#tabs").tabs();

    $("#txt_dateoutcar, #txt_dialog_cheque_date, #txt_dialog2_cheque_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
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
    
    $('#btnDialogSave').click(function(){
        
        if( $('#txt_moneyreserv').val() == "" ){
            alert('กรุณากรอก ยอดเงินจอง');
            return false;
        }
        
        if( $('input[id=chk_dialog_buy_cash]:checked').val() != "on" && $('input[id=chk_dialog_buy_cheque]:checked').val() != "on" ){
            alert('กรุณาเลือกชำระด้วยเงินสดหรือเช็ค');
            return false;
        }
        
        if( !chkSumDialog() ){
            alert('ยอดเงินสด/เช็ค ไม่ถูกต้อง ยอดรวมต้องเท่ากับ ยอดเงินจอง');
            return false;
        }
        
        $.post('reserv_car_edit_api.php',{
            cmd: 'plus',
            resid: '<?php echo $id; ?>',
            cusid: '<?php echo $cus_id; ?>',
            product_id: '<?php echo $product_id; ?>',
            txt_moneyreserv: $('#txt_moneyreserv').val(),
            txt_dateoutcar: $('#txt_dateoutcar').val(),
            chk_dialog_buy_cash: $('input[id=chk_dialog_buy_cash]:checked').val(),
            txt_dialog_cash: $('#txt_dialog_cash').val(),
            chk_dialog_buy_cheque: $('input[id=chk_dialog_buy_cheque]:checked').val(),
            txt_dialog_cheque_bank: $('#txt_dialog_cheque_bank').val(),
            txt_dialog_cheque_banch: $('#txt_dialog_cheque_banch').val(),
            txt_dialog_cheque_no: $('#txt_dialog_cheque_no').val(),
            txt_dialog_cheque_date: $('#txt_dialog_cheque_date').val(),
            txt_dialog_cheque_monny: $('#txt_dialog_cheque_monny').val(),
            span_appointment_amt: $('#span_appointment_amt').text(),
            txt_hid_dialog_type: $('#txt_hid_dialog_type').val(),
            radio_dialog_cheque_prov: $('input[id=radio_dialog_cheque_prov]:checked').val()
        },
        function(data){
            if(data.success){
                $('#dev_edit').empty();
                $('#dev_edit').load('reserv_car_edit.php');
                $("#div_dialog").remove();
                alert(data.message);
            }else{
                alert(data.message);
            }
        },'json');
    });
    
    /*========================*/
    
    $("input[name='chk_dialog2_buy_cash']").change(function(){
        if( $('input[id=chk_dialog2_buy_cheque]:checked').val() ){
            var m1 = parseFloat( $('#span_money_lost').text() );
            var m2 = parseFloat( $('#txt_dialog2_cheque_monny').val() );
            $('#txt_dialog2_cash').val( m1-m2 );
        }else{
            $('#txt_dialog2_cash').val( $('#span_money_lost').text() );
        }
        
        if( $('input[id=chk_dialog2_buy_cash]:checked').val() ){
            $('#divdialog2_cash').show('fast');
        }else{
            $('#txt_dialog2_cash').val('');
            $('#divdialog2_cash').hide('fast');
        }
    });
    
    $("input[name='chk_dialog2_buy_cheque']").change(function(){
        if( $('input[id=chk_dialog2_buy_cash]:checked').val() ){
            var m1 = parseFloat( $('#span_money_lost').text() );
            var m2 = parseFloat( $('#txt_dialog2_cash').val() );
            $('#txt_dialog2_cheque_monny').val( m1-m2 );
        }else{
            $('#txt_dialog2_cheque_monny').val( $('#span_money_lost').text() );
        }
        
        if( $('input[id=chk_dialog2_buy_cheque]:checked').val() ){
            $('#divdialog2_cheque').show('fast');
        }else{
            $('#txt_dialog2_cheque_monny').val('');
            $('#divdialog2_cheque').hide('fast');
        }
    });
    
    $('#btnDialog2Save').click(function(){
        
        <?php
        if(empty($car_id)){
        ?>
            alert('กรุณาเลือกรถก่อนค่ะ !');
            return false;
        <?php
        }
        ?>
        
        if( $('input[id=chk_dialog2_buy_cash]:checked').val() != "on" && $('input[id=chk_dialog2_buy_cheque]:checked').val() != "on" ){
            alert('กรุณาเลือกชำระด้วยเงินสดหรือเช็ค');
            return false;
        }
        
        if( !chkSumDialog2() ){
            alert('ยอดเงินสด/เช็ค ไม่ถูกต้อง ยอดรวมต้องเท่ากับ ยอดเงินที่เหลือ');
            return false;
        }
        
        if( $('#area_remark').val() == "" ){
            alert('กรุณากรอก หมายเหตุ');
            return false;
        }
        
        $.post('reserv_car_edit_api.php',{
            cmd: 'outcar',
            resid: '<?php echo $id; ?>',
            cusid: '<?php echo $cus_id; ?>',
            product_id: '<?php echo $product_id; ?>',
            txt_money_lost: $('#span_money_lost').text(),
            chk_dialog2_buy_cash: $('input[id=chk_dialog2_buy_cash]:checked').val(),
            txt_dialog2_cash: $('#txt_dialog2_cash').val(),
            chk_dialog2_buy_cheque: $('input[id=chk_dialog2_buy_cheque]:checked').val(),
            txt_dialog2_cheque_bank: $('#txt_dialog2_cheque_bank').val(),
            txt_dialog2_cheque_banch: $('#txt_dialog2_cheque_banch').val(),
            txt_dialog2_cheque_no: $('#txt_dialog2_cheque_no').val(),
            txt_dialog2_cheque_date: $('#txt_dialog2_cheque_date').val(),
            txt_dialog2_cheque_monny: $('#txt_dialog2_cheque_monny').val(),
            area_remark: $('#area_remark').val(),
            txt_hid_dialog2_type: $('#txt_hid_dialog2_type').val(),
            radio_dialog2_cheque_prov: $('input[id=radio_dialog2_cheque_prov]:checked').val(),
            car_id: '<?php echo $car_id; ?>'
        },
        function(data){
            if(data.success){
                $('#dev_edit').empty();
                $('#dev_edit').load('reserv_car_edit.php');
                $('#div_dialog').remove();
                alert(data.message);
                //$('#div_dialog').html("<div style=\"text-align:center\">"+data.message+"<br /><br /><input type=\"button\" name=\"btnPrint\" id=\"btnPrint\" value=\"พิมพ์ใบรับรถยนต์\" onclick=\"javascript:ClosePopup(); javascript:window.open('../report/car_receipt.php?id=<?php echo $id; ?>','car_receipt78457845','toolbar=no,menubar=no,resizable=yes,scrollbars=yes,status=no,location=no,width=800,height=600')\"></div>");
            }else{
                alert(data.message);
            }
        },'json');
    });
    
    $('#btnUpdateMoney1, #btnUpdateMoney2').click(function(){
        $("#div_dialog").remove();
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

    $('#btnSelectCars, #btnSelectCars2').click(function(){
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
    });
    
});

function SummaryDialog(){
    var s1 = 0;
    var a1 = parseFloat($('#txt_moneyreserv').val());
    var a2 = parseFloat('<?php echo $appointment_amt; ?>');

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    
    s1 = a2-a1;

    $('#span_appointment_amt').text(s1);
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

function chkSumDialog2(){
    var s1 = 0;
    var a1 = parseFloat($('#span_money_lost').text());
    var a2 = parseFloat($('#txt_dialog2_cash').val());
    var a3 = parseFloat($('#txt_dialog2_cheque_monny').val());

    if ( isNaN(a1) || a1 == ""){
        a1 = 0;
    }
    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=chk_dialog2_buy_cash]:checked').val() ){
        s1+=a2;
    }
    if( $('input[id=chk_dialog2_buy_cheque]:checked').val() ){
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
</script>