<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "divdetail"){
    $id = $_GET['id'];
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#D0D0D0" style="font-weight:bold; text-align:center">
    <td>Product ID</td>
    <td>Name</td>
    <td>Unit</td>
    <td>Cost</td>
    <td>Vat</td>
    <td>Total</td>
</tr>
<?php
$qry = pg_query("SELECT * FROM \"PurchaseOrderDetails\" WHERE po_id='$id' AND cancel='FALSE' ORDER BY auto_id ASC ");
while($res = pg_fetch_array($qry)){
    $j++;
    $product_id = $res['product_id'];
    $product_cost = $res['product_cost'];
    $vat = $res['vat'];
    $unit = $res['unit'];
    
    if(substr($product_id, 0, 1)=="P"){
        $product_name = GetProductName($product_id);
    }else{
        $product_name = GetRawMaterialName($product_id);
    }

    if($j%2==0){
        echo "<tr class=\"odd\">";
    }else{
        echo "<tr class=\"even\">";
    }
?>
    <td><?php echo $product_id; ?></td>
    <td><?php echo $product_name; ?></td>
    <td align="right"><?php echo $unit; ?></td>
    <td align="right"><?php echo number_format($product_cost,2); ?></td>
    <td align="right"><?php echo number_format($vat,2); ?></td>
    <td align="right"><?php echo number_format($product_cost+$vat,2); ?></td>
</tr>
<?php
}
?>
</table>

<?php
}

elseif($cmd == "divconfirm"){
    $m = $_GET['m'];
    $c = $_GET['c'];
    $vd = $_GET['vd'];
    
    if($m == 0 OR $m == "" OR $m == "0.00"){
        echo "ไม่สามารถทำรายการได้ เนื่องจากไม่พบยอดเงิน กรุณาลองใหม่อีกครั้งในภายหลัง";
        exit;
    }
    
    if(empty($vd)){
        echo "ไม่พบ Vender ID กรุณาลองใหม่อีกครั้ง";
        exit;
    }
?>

<div style="margin:0px">
<b>ยอดเงิน</b> : <?php echo number_format($m,2); ?> บาท<br />
<b>ชำระโดย</b><br />
<input type="checkbox" name="chk_buy_cash" id="chk_buy_cash"> เงินสด
<span id="divcash" style="display:none">&nbsp;ยอดเงินสด <input type="text" name="txt_cash" id="txt_cash" size="10"></span>
<br />
<input type="checkbox" name="chk_buy_cheque" id="chk_buy_cheque"> เช็ค
<div id="divcheque" style="margin-top:10px; margin-left:25px; display:none">
<table cellpadding="3" cellspacing="0" border="0" width="100%">
<tr>
    <td width="100">ธนาคาร</td>
    <td>
<select name="cb_bank" id="cb_bank">
<?php
$qry = pg_query("SELECT * FROM account.\"ChequeAccs\" ORDER BY \"bank_name\" ASC ");
while($res = pg_fetch_array($qry)){
    $ac_id = $res['ac_id'];
    $bank_name = $res['bank_name'];
    $bank_branch = $res['bank_branch'];

    echo "<option value=\"$ac_id\">$bank_name $bank_branch</option>";
}
?>
</select>
    </td>
</tr>
<tr>
    <td>ประเภท</td>
    <td>
<select name="cb_type" id="cb_type">
    <option value="0">cash</option>
    <option value="1" selected>payee only</option>
    <option value="2">account</option>
</select>
    </td>
</tr>
<tr>
    <td>เลขที่เช็ค</td>
    <td>
<select name="cb_cheque" id="cb_cheque">
<?php
$qry = pg_query("SELECT * FROM account.\"ChequeAccDetails\" WHERE pay_to IS NULL ORDER BY \"chq_id\" ASC ");
while($res = pg_fetch_array($qry)){
    $chq_id = $res['chq_id'];
    echo "<option value=\"$chq_id\">$chq_id</option>";
}
?>
</select>
    </td>
</tr>
<tr>
    <td>วันที่บนเช็ค</td><td><input type="text" name="txt_cheque_date" id="txt_cheque_date" size="10" value="<?php echo $nowdate; ?>" style="text-align:center"></td>
</tr>
<tr>
    <td>ยอดเงินบนเช็ค</td><td><input type="text" name="txt_cheque_money" id="txt_cheque_money" size="10" style="text-align:right"></td>
</tr>
</table>
</div>
</div>

<div class="linedotted"></div>

<div style="margin-top:5px; text-align:right">
<input type="button" name="btnSave" id="btnSave" value="บันทึก">
</div>

<script type="text/javascript">
$(document).ready(function(){
    
    $("#txt_cheque_date").datepicker({
        showOn: 'button',
        buttonImage: '../images/calendar.gif',
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    
    $("input[name='chk_buy_cash']").change(function(){
        if( $('input[id=chk_buy_cheque]:checked').val() ){
            var m1 = parseFloat( <?php echo $m; ?> );
            var m2 = parseFloat( $('#txt_cheque_money').val() );
            $('#txt_cash').val( m1-m2 );
        }else{
            $('#txt_cash').val( <?php echo $m; ?> );
        }

        if( $('input[id=chk_buy_cash]:checked').val() ){
            $('#divcash').show('fast');
        }else{
            $('#txt_cash').val('');
            $('#divcash').hide('fast');
        }
    });
    
    $("input[name='chk_buy_cheque']").change(function(){
        if( $('input[id=chk_buy_cash]:checked').val() ){
            var m1 = parseFloat( <?php echo $m; ?> );
            var m2 = parseFloat( $('#txt_cash').val() );
            $('#txt_cheque_money').val( m1-m2 );
        }else{
            $('#txt_cheque_money').val( <?php echo $m; ?> );
        }

        if( $('input[id=chk_buy_cheque]:checked').val() ){
            $('#divcheque').show('fast');
        }else{
            $('#txt_cheque_monny').val('');
            $('#divcheque').hide('fast');
        }
    });
    
    $('#btnSave').click(function(){
        
        if( !chkSum() ){
            alert('ยอดเงินสด/เช็ค ไม่ถูกต้อง ยอดต้องเท่ากับ ยอดเงินรวม');
            return false;
        }
        
        $.post('po_car_pay_api.php',{
            cmd: 'save',
            vender: '<?php echo $vd; ?>',
            money: '<?php echo $m ;?>',
            select: '<?php echo $c; ?>',
            chk_buy_cash: $('input[id=chk_buy_cash]:checked').val(),
            txt_cash: $('#txt_cash').val(),
            chk_buy_cheque: $('input[id=chk_buy_cheque]:checked').val(),
            cb_bank: $('#cb_bank').val(),
            cb_type: $('#cb_type').val(),
            cb_cheque: $('#cb_cheque').val(),
            txt_cheque_date: $('#txt_cheque_date').val(),
            txt_cheque_money: $('#txt_cheque_money').val()
        },
        function(data){
            if(data.success){
                alert(data.message);
                location.reload();
            }else{
                alert(data.message);
            }
        },'json');
    });
});

function chkSum(){
    var s1 = 0;
    var a1 = parseFloat(<?php echo $m; ?>);
    var a2 = parseFloat($('#txt_cash').val());
    var a3 = parseFloat($('#txt_cheque_money').val());

    if ( isNaN(a2) || a2 == ""){
        a2 = 0;
    }
    if ( isNaN(a3) || a3 == ""){
        a3 = 0;
    }
    
    if( $('input[id=chk_buy_cash]:checked').val() ){
        s1+=a2;
    }
    if( $('input[id=chk_buy_cheque]:checked').val() ){
        s1+=a3;
    }
    
    if(a1 != s1){
        return false;
    }else{
        return true;
    }
}
</script>
    
<?php
}

elseif($cmd == "save"){
    $vender = $_POST['vender'];
    $money = $_POST['money'];
    $select = $_POST['select'];
    $chk_buy_cash = $_POST['chk_buy_cash'];
    $txt_cash = $_POST['txt_cash'];
    $chk_buy_cheque = $_POST['chk_buy_cheque'];
    $cb_bank = $_POST['cb_bank'];
    $cb_type = $_POST['cb_type'];
    $cb_cheque = $_POST['cb_cheque'];
    $txt_cheque_date = $_POST['txt_cheque_date'];
    $txt_cheque_money = $_POST['txt_cheque_money'];

    pg_query("BEGIN WORK");
    $status = 0;
    $txt_error = array();
    
    $qry = "INSERT INTO account.\"Vouchers\" (st_date) VALUES ('$nowdate')";
    if(!$res=@pg_query($qry)){
        $txt_error[] = "INSERT Vouchers ไม่สำเร็จ $qry";
        $status++;
    }

    $cur_jobid=@pg_query("select currval('account.\"Vouchers_job_id_seq\"');");
    $rs_jobid=@pg_fetch_result($cur_jobid,0);
    if(empty($rs_jobid)){
        $txt_error[] = "ค้นหา JobId ล่าสุด ไม่สำเร็จ";
        $status++;
    }

    $generate_id=@pg_query("select account.gen_no('$nowdate','VP')");
    $vc_id=@pg_fetch_result($generate_id,0);
    if(empty($vc_id)){
        $txt_error[] = "gen vc_id ไม่สำเร็จ";
        $status++;
    }
    
    if($chk_buy_cheque == "on"){
        
        if($txt_cash == "" OR empty($txt_cash)){
            $txt_cash = 0;
        }
        
        $qry = "INSERT INTO account.\"VoucherDetails\" (vc_id,vc_detail,marker_id,cash_amt,chq_acc_no,chque_no,do_date,job_id,vc_type) VALUES 
        ('$vc_id','$select','$_SESSION[ss_iduser]','$txt_cash','$cb_bank','$cb_cheque','$nowdate','$rs_jobid','P')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT VoucherDetails (Cheque) ไม่สำเร็จ $qry";
            $status++;
        }

        $vender_name = GetVender($vender);

        $in_qry="UPDATE account.\"ChequeAccDetails\" SET ac_id='$cb_bank',date_on_chq='$txt_cheque_date',amount='$txt_cheque_money',type_pay='$cb_type',do_date='$nowdate',pay_to='$vender_name' WHERE chq_id='$cb_cheque' ";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "UPDATE ChequeAccDetails ไม่สำเร็จ $in_qry";
            $status++;
        }
    }else{
        $qry = "INSERT INTO account.\"VoucherDetails\" (vc_id,vc_detail,marker_id,cash_amt,chq_acc_no,chque_no,do_date,job_id,vc_type) VALUES 
        ('$vc_id','$select','$_SESSION[ss_iduser]','$txt_cash',DEFAULT,DEFAULT,'$nowdate','$rs_jobid','P')";
        if(!$res=@pg_query($qry)){
            $txt_error[] = "INSERT VoucherDetails (Cash) ไม่สำเร็จ $qry";
            $status++;
        }
    }
    
    $arr_select = explode(",",$select);
    foreach($arr_select as $v){
        $in_qry="UPDATE \"PurchaseOrders\" SET pay='TRUE',pay_id='$rs_jobid' WHERE po_id='$v' ";
        if(!$res=@pg_query($in_qry)){
            $txt_error[] = "UPDATE PurchaseOrders ไม่สำเร็จ $in_qry";
            $status++;
        }
    }
    
    if($status == 0){
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