<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "addcheque"){
    $id = $_GET['id'];
?>
<div id="add_cheque_<?php echo $id; ?>" style="border: 1px dashed #D0D0D0; margin-top:10px; padding:3px; background-color:#E0E0E0">

<div><b>ข้อมูลเช็ค #<?php echo $id; ?></b></div>
<div style="margin-top:5px">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td align="right">เลขที่เช็ค&nbsp;</td><td><input type="text" name="txt_cheque_no<?php echo $id; ?>" id="txt_cheque_no<?php echo $id; ?>" style="width:60px"></td>
    <td align="right">ธนาคาร&nbsp;</td><td><input type="text" name="txt_cheque_bank<?php echo $id; ?>" id="txt_cheque_bank<?php echo $id; ?>" style="width:80px"></td>
    <td align="right">สาขา&nbsp;</td><td><input type="text" name="txt_cheque_branch<?php echo $id; ?>" id="txt_cheque_branch<?php echo $id; ?>" style="width:80px">
    <input type="checkbox" name="chk_cheque_prov<?php echo $id; ?>" id="chk_cheque_prov<?php echo $id; ?>"> ตจว.</td>
    <td align="right">วันที่บนเช็ค&nbsp;</td><td><input type="text" name="txt_cheque_date<?php echo $id; ?>" id="txt_cheque_date<?php echo $id; ?>" style="width:80px; text-align:center" value="<?php echo $nowdate; ?>"></td>
    <td align="right">จำนวนเงิน&nbsp;</td><td><input type="text" name="txt_cheque_money<?php echo $id; ?>" id="txt_cheque_money<?php echo $id; ?>" style="width:80px; text-align:right"></td>
</tr>
</table>
</div>

<div style="margin:10px 0 10px 10px">
เช็คสั่งจ่ายบริษัท :
<select name="cb_accno<?php echo $id; ?>" id="cb_accno<?php echo $id; ?>">
<?php
$qry = pg_query("SELECT * FROM \"BankEnter\" ORDER BY \"accname\" ASC");
while( $res = pg_fetch_array($qry) ){
    $accno = $res['accno'];
    $accname = $res['accname'];
?>
    <option value="<?php echo "$accno"; ?>"><?php echo "$accname"; ?></option>
<?php
}
?>
</select>
</div>

<div style="margin-top:10px"><b>รายการชำระ</b>
<input type="button" name="btn_add_sub" id="btn_add_sub" value="+ เพิ่ม" onclick="javascript:AddSubRows<?php echo $id; ?>(<?php echo $id; ?>)"><input type="button" name="btn_del_sub" id="btn_del_sub" value="- ลบ" onclick="javascript:DelSubRows<?php echo $id; ?>(<?php echo $id; ?>)">
<input type="hidden" name="txt_hid_count_<?php echo $id; ?>" id="txt_hid_count_<?php echo $id; ?>" value="1">
</div>

<div style="border: 1px dashed #D0D0D0; margin-top:5px; padding:3px; background-color:#F0F0F0">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">Invoice ID&nbsp;</td>
    <td width="300">
<select name="cb_inv<?php echo $id; ?>_1" id="cb_inv<?php echo $id; ?>_1" onchange="javascript:changeInv(<?php echo $id; ?>,1,'span_sub_money<?php echo $id; ?>_1')">
    <option value="">เลือก</option>
<?php
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE status IS NULL AND cancel = 'FALSE' ");
while( $res = pg_fetch_array($qry) ){
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    
    $name = implode(",", $arr_name);
?>
<option value="<?php echo "$inv_no#$cus_id#$IDNO#$res_id"; ?>"><?php echo "$inv_no : $name | เลขจอง : $res_id"; ?></option>
<?php
}
?>
</select></td>
    <td width="60">&nbsp;ยอดเงิน&nbsp;</td>
    <td><span id="span_sub_money<?php echo $id; ?>_1">0.00</span></td>
</tr>
</table>
</div>

<div id="DivSub<?php echo $id; ?>"></div>

<div style="border: 1px dashed #D0D0D0; margin:0px; padding:3px; background-color:#FFFFE1; text-align:right; font-weight:bold">รวม <span id="sumsub<?php echo $id; ?>">0.00</span></div>

<script>
$("#txt_cheque_date<?php echo $id; ?>").datepicker({
    showOn: 'button',
    buttonImage: '../images/calendar.gif',
    buttonImageOnly: true,
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd'
});

var counter_<?php echo $id; ?> = 1;

function AddSubRows<?php echo $id; ?>(id){
    counter_<?php echo $id; ?>++;
    $('#txt_hid_count_<?php echo $id; ?>').val(counter_<?php echo $id; ?>);
    var newTextBoxDiv = $(document.createElement('div')).attr("id", 'sub'+id+'_'+counter_<?php echo $id; ?>);

table = '<div style="border: 1px dashed #D0D0D0; margin:0px; padding:3px; background-color:#F0F0F0">'
+ '<table cellpadding="0" cellspacing="0" border="0" width="100%">'
+ '<tr>'
    + '<td width="100">Invoice ID&nbsp;</td>'
    +'<td width="300"><select name="cb_inv'+id+'_<?php echo $id; ?>" id="cb_inv'+id+'_<?php echo $id; ?>" onchange="javascript:changeInv('+id+',<?php echo $id; ?>,\'span_sub_money<?php echo $id; ?>_'+counter_<?php echo $id; ?>+'\')">'
    + '<option value="">เลือก</option>'
<?php
$qry = pg_query("SELECT * FROM \"Invoices\" WHERE status IS NULL AND cancel = 'FALSE' ");
while( $res = pg_fetch_array($qry) ){
    $inv_no = $res['inv_no'];
    $cus_id = $res['cus_id'];
    $IDNO = $res['IDNO'];
    $res_id = $res['res_id'];
    
    $arr_name = array();
    $qry2 = pg_query("SELECT * FROM \"InvoiceDetails\" WHERE inv_no='$inv_no' AND cancel = 'FALSE' ORDER BY service_id ASC ");
    while( $res2 = pg_fetch_array($qry2) ){
        $service_id = $res2['service_id'];
        $service_name = GetServicesName($service_id);
        $arr_name[] = $service_name;
    }
    
    $name = implode(",", $arr_name);
?>
    + '<option value="<?php echo "$inv_no#$cus_id#$IDNO#$res_id"; ?>"><?php echo "$inv_no : $name | เลขจอง : $res_id"; ?></option>'
<?php
}
?>
    + '</select></td>'
    + '<td width="60">&nbsp;ยอดเงิน&nbsp;</td>'
    +'<td><span id="span_sub_money'+id+'_'+counter_<?php echo $id; ?>+'">0.00</span></td>'
+ '</tr>'
+ '</table>'
+ '</div>';

    newTextBoxDiv.html(table);
    newTextBoxDiv.appendTo("#DivSub<?php echo $id; ?>");   
}

function DelSubRows<?php echo $id; ?>(id){
    if(counter_<?php echo $id; ?>==1){
        return false;
    }
    $('#sub'+id+'_'+counter_<?php echo $id; ?>).remove();
    counter_<?php echo $id; ?>--;
    $('#txt_hid_count_<?php echo $id; ?>').val(counter_<?php echo $id; ?>);
    Sum(<?php echo $id; ?>);
}

</script>

</div>
<?php
}

elseif($cmd == "changeInv"){
    $inv = $_GET['inv'];
    $inv_arr = explode("#", $inv);
    
    if($inv_arr[0] == ""){
        echo 0;
        exit;
    }
    $amount = 0;
    $qry = pg_query("SELECT SUM(amount+vat) as amt FROM \"VInvDetail\" WHERE cancel='FALSE' AND inv_no='$inv_arr[0]' ");
    if( $res = pg_fetch_array($qry) ){
        echo $amount = $res['amt'];
    }else{
        echo 0;
    }
}

elseif($cmd == "savecheque"){
    $arr_cheque = json_decode(stripcslashes($_POST["arr_cheque"]));
    $arr_detail = json_decode(stripcslashes($_POST["arr_detail"]));
    
    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    $arr_running_no = array();

    foreach($arr_cheque as $key => $value){
        $cheque_no = $value->cheque_no;
        $cheque_bank = $value->cheque_bank;
        $cheque_branch = $value->cheque_branch;
        $cheque_prov = $value->cheque_prov;
        $cheque_date = $value->cheque_date;
        $cheque_money = $value->cheque_money;
        $cb_accno  = $value->cb_accno;
        
        if(empty($cheque_no) or empty($cheque_bank) or empty($cheque_branch) or empty($cheque_date) or empty($cheque_money) or empty($cb_accno) ){
            continue;
        }

        $generate_id=@pg_query("select generate_id('$nowdate',$_SESSION[ss_office_id],5)");
        $running_no=@pg_fetch_result($generate_id,0);
        
        $arr_running_no[$cheque_no] = $running_no;
        
        if($cheque_prov == 1){
            $cheque_prov = "TRUE";
        }else{
            $cheque_prov = "FALSE";
        }
        
        $qry = "INSERT INTO \"Cheques\" (running_no,cheque_no,bank_name,bank_branch,amt_on_cheque,receive_date,date_on_cheque,out_bangkok,acc_bank_enter) VALUES 
        ('$running_no','$cheque_no','$cheque_bank','$cheque_branch','$cheque_money','$nowdate','$cheque_date','$cheque_prov','$cb_accno')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT Cheques ไม่สำเร็จ $qry";
            $status++;
        }
    }
    
    foreach($arr_detail as $key => $value){
        $txt_cheque_no = $value->txt_cheque_no;
        $cb_inv = $value->cb_inv;
        $txt_sub_money = $value->txt_sub_money;
        
        if(empty($txt_cheque_no) or empty($cb_inv) or empty($txt_sub_money)){
            continue;
        }
        
        $arr_cb_inv = explode("#",$cb_inv); //$inv_no#$cus_id#$IDNO#$res_id

        $qry = "INSERT INTO \"ChequeDetails\" (inv_no,cheque_no,cus_id,\"IDNO\",service_id,cus_amount,running_no) VALUES 
        ('$arr_cb_inv[0]','$txt_cheque_no','$arr_cb_inv[1]','$arr_cb_inv[2]',DEFAULT,'$txt_sub_money','$arr_running_no[$txt_cheque_no]')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT ChequeDetails ไม่สำเร็จ $qry";
            $status++;
        }

        $in_qry="UPDATE \"Invoices\" SET status='OCCQ' WHERE inv_no='$arr_cb_inv[0]' ";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "UPDATE Invoices ไม่สำเร็จ $in_qry";
            $status++;
        }
        
        $qry = @pg_query("SELECT constant_var FROM \"VInvDetail\" WHERE inv_no = '$arr_cb_inv[0]' ");
        if($res = @pg_fetch_array($qry)){
            $constant_var = $res['constant_var'];
        }
        
        if( substr($constant_var, 0, 3) == "CAR" ){
            $in_qry="UPDATE \"ReserveDetails\" SET 
            cheque_no='$cheque_no',cheque_bank='$cheque_bank',cheque_branch='$cheque_branch',cheque_amt='$cheque_money',cheque_date='$cheque_date' 
            WHERE res_id='$arr_cb_inv[3]' AND do_date='$nowdate' AND cash_amt='0' AND cheque_amt='0' AND cancel='FALSE' ";
            if(!$res=@pg_query($in_qry)){
                $txt_error[] = "UPDATE ReserveDetails ไม่สำเร็จ $in_qry";
                $status++;
            }
        }
        
    }
    
    if($status == 0){
        //pg_query("ROLLBACK");
        pg_query("COMMIT");
        $data['success'] = true;
        $data['message'] = "บันทึกเรียบร้อยแล้ว";
    }else{
        pg_query("ROLLBACK");
        $data['success'] = false;
        $data['message'] = "ไม่สามารถบันทึกได้! $txt_error[0]";
    }
    echo json_encode($data);
}

?>