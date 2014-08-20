<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "divshow_content"){
?>

<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td>JobID</td>
    <td>รูปแบบ</td>
    <td>รหัส</td>
    <td>วันที่ทำรายการ</td>
    <td>รายละเอียด</td>
    <td>ยอดเงิน</td>
    <td></td>
    <td></td>
</tr>
<?php
$qry=pg_query("select A.*,B.* from account.\"VoucherDetails\" A LEFT OUTER JOIN account.\"Vouchers\" B on A.\"job_id\" = B.\"job_id\" WHERE \"approve_id\" is not null AND \"receipt_id\" is null ORDER BY A.\"job_id\",\"vc_id\" ASC");
while($res=pg_fetch_array($qry)){
    $j++;
    $vc_id = $res["vc_id"];
    $vc_detail = $res["vc_detail"];
    $do_date = $res["do_date"];
    $job_id = $res["job_id"];   if($j==1){ $old_job = $job_id; }
    $cash_amt = $res["cash_amt"];
    $approve_id = $res["approve_id"];
    $chq_acc_no = $res["chq_acc_no"];
    $chque_no = $res["chque_no"];

    if($old_job != $job_id){
        echo "<tr><td colspan=10><hr style=\"margin:0px; padding:0px; height: 1px\"></td></tr>";
    }
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $ChqID = $res_chq["chq_id"];
            $money = $res_chq["amount"];
        }
    }

?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><?php echo $job_id; ?></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
<?php
$arr_chk_approve = explode("#",$approve_id);
if( $arr_chk_approve[count($arr_chk_approve)-1] == "P" ){
?>
    <td align=center><input type="button" name="btn_add" id="btn_add" value="ทำรายการนี้" onclick="javascript:add_rc('<?php echo $vc_id; ?>','1')"></td>
    <td align=center><span style="color:#969696">รายการนี้ ถูกพิมพ์แล้ว</span></td>
<?php
}else{
?>
    <td align=center><input type="button" name="btn_add" id="btn_add" value="ทำรายการนี้" onclick="javascript:add_rc('<?php echo $vc_id; ?>','0')"></td>
    <td align=center><input type="button" class="abc111" name="btn_print" id="btn_print" value="พิมพ์โดยไม่มีผู้รับเงิน" onclick="javascript:window.open('voucher_print.php?id=<?php echo "$vc_id"; ?>' , 'fv45s7s8a4s5s4a<?php echo "$vp_id"; ?>','menuber=no,toolbar=yes,location=no,scrollbars=no, status=no,resizable=no,width=800,height=600')"></td>
<?php
}
?>
</tr>
<?php
    $old_job = $job_id;
    $sum_sub+=$money;
}

if($j == 0){
    echo "<tr><td colspan=10 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>


<script type="text/javascript">
$('.abc111').click(function(){
    $('#divshow').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
    $('#divshow').load('voucher_receipt_api.php?cmd=divshow_content');
});

function add_rc(id,bt){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
    $('#dialogedit').load('voucher_receipt_api.php?cmd=divshow_add&id='+id+'&bt='+bt);
    $('#dialogedit').dialog({
        title: 'บันทึกบัญชี VC ID : '+id,
        resizable: false,
        modal: true,  
        width: 700,
        height: 250,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}
</script>
<?php
}

elseif($cmd == "divshow_add"){

$id = $_GET['id'];
$bt = $_GET['bt'];

$qry=pg_query("select * from account.\"VoucherDetails\" WHERE \"vc_id\"='$id' ");
if($res=pg_fetch_array($qry)){
    $job_id = $res["job_id"];
    $do_date = $res["do_date"];
    $vc_detail = $res["vc_detail"];
    $chque_no = $res["chque_no"];
    $chq_acc_no = $res["chq_acc_no"];

    $money = $res["cash_amt"];
}

if(!empty($chq_acc_no)){
    $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
    if($res_chq=pg_fetch_array($qry_chq)){
        $ChqID = $res_chq["chq_id"];
        $money = $res_chq["amount"];
    }
}
?>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="20%"><b>ยอดเงิน</b></td>
    <td><?php echo number_format($money,2); ?> บาท.</td>
</tr>
<tr valign="top">
    <td><b>ผู้รับเงิน</b></td>
    <td>

<table cellpadding="5" cellspacing="0" border="0" width="100%">
<tr>
    <td width="30%"><input type="radio" name="chkradio" id="chkradio" value="1" class="aaaaa1" checked> พนักงานภายใน</td>
    <td>
        <select name="list_user" id="list_user">
    <?php
        $qry_user=pg_query("select * from \"fuser\" WHERE \"status_user\"='TRUE' ORDER BY \"fullname\" ASC ");
        while($res_user=pg_fetch_array($qry_user)){
            $fullname = $res_user["fullname"];
            $id_user = $res_user["id_user"];
    ?>
          <option value="<?php echo $id_user; ?>"><?php echo $fullname; ?></option>
    <?php } ?>
        </select>
    </td>
</tr>
<tr>
    <td><input type="radio" name="chkradio" id="chkradio" value="2" class="aaaaa2"> Vender</td>
    <td>
        <select name="list_vender" id="list_vender">
    <?php
        $qry_user=pg_query("select * from \"VVenders\" ORDER BY \"vender_id\" ASC ");
        while($res_user=pg_fetch_array($qry_user)){
            $VenderID = $res_user["vender_id"];
            $pre_name = $res_user["pre_name"];
            $cus_name = $res_user["cus_name"];
            $surname = $res_user["surname"];
            $vd_name = "$pre_name $cus_name $surname";
    ?>
          <option value="<?php echo $VenderID; ?>"><?php echo $vd_name; ?></option>
    <?php } ?>
        </select>&nbsp;<input type="text" name="vendertxt" id="vendertxt" size="20">
    </td>
</tr>
<tr>
    <td><input type="radio" name="chkradio" id="chkradio" value="3" class="aaaaa3"> เติมเอง</td>
    <td>
<input type="text" name="othertxt" id="othertxt" size="30">
    </td>
</tr>
</table>

</td>

</tr>
</table>

<div style="float:left"><input type="button" name="btnsave" id="btnsave" value=" บันทึก "></div>
<div style="float:right"><input type="button" name="btnprint" id="btnprint" value="พิมพ์ <?php echo $id; ?>" onclick="javascript:window.open('voucher_print.php?id=<?php echo "$id"; ?>' , 'fd22da4fsf4f7e<?php echo "$vp_id"; ?>','menuber=no,toolbar=yes,location=no,scrollbars=no, status=no,resizable=no,width=800,height=600')"></div>
<div style="clear:both"></div>

<script type="text/javascript">
$(document).ready(function(){
    $('#btnprint').attr('disabled',true);
    //$('#show_list_user').hide();
});

$('#btnsave').click(function(){
    $.post('voucher_receipt_api.php',{
        cmd: 'divshow_add_send',
        bt: '<?php echo "$bt"; ?>',
        vcid: '<?php echo "$id"; ?>',
        chkradio: $('input[id=chkradio]:checked').val(),
        list_vender: $('#list_vender').val(),
        list_user: $('#list_user').val(),
        othertxt: $('#othertxt').val(),
        vendertxt: $('#vendertxt').val()
    },
    function(data){
        if(data.success){
            //$('#dialogedit').remove();
            $('#list_user').attr('disabled',true);
            $('#list_vender').attr('disabled',true);
            $('#vendertxt').attr('disabled',true);
            $('#othertxt').attr('disabled',true);
            
            $('#btnsave').attr('disabled',true);
            <?php if($bt != 1){ ?>
            $('#btnprint').attr('disabled',false);
            <?php } ?>
            alert(data.message);
            $('#divshow').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
            $('#divshow').load('voucher_receipt_api.php?cmd=divshow_content');
        }else{
            alert(data.message);
        }
    },'json');
});

$('#list_user').click(function(){
    $(".aaaaa1").attr('checked',true);
});
$('#list_vender').click(function(){
    $(".aaaaa2").attr('checked',true);
});
$('#vendertxt').click(function(){
    $(".aaaaa2").attr('checked',true);
});
$('#othertxt').click(function(){
    $(".aaaaa3").attr('checked',true);
});
$('#btnprint').click(function(){
    $('#dialogedit').remove();
    //$('#btnprint').attr('disabled',true);
});
</script>
<?php
}

elseif($cmd == "divshow_add_send"){
    
$bt = $_POST['bt'];
$vcid = $_POST['vcid'];
$chkradio = $_POST['chkradio'];
$list_vender = $_POST['list_vender'];
$list_user = $_POST['list_user'];
$othertxt = $_POST['othertxt'];
$vendertxt = $_POST['vendertxt'];

$qry_name=pg_query("SELECT * FROM account.\"VoucherDetails\" WHERE \"vc_id\"='$vcid' ");
if($res_name=pg_fetch_array($qry_name)){
    $vc_detail = $res_name["vc_detail"];
    $cash_amt = $res_name["cash_amt"];
    $chq_acc_no = $res_name["chq_acc_no"];
    $chque_no = $res_name["chque_no"];
}

$txt_add_to_detail = "";
if(!empty($cash_amt)){
    $txt_add_to_detail .= "เบิกเงินสด : ".number_format($cash_amt,2)." บาท";
}

if(!empty($chq_acc_no)){
    $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
    if($res_chq=pg_fetch_array($qry_chq)){
        $AcID = $res_chq["ac_id"];
        $ChqID = $res_chq["chq_id"];
        $DateOnChq = $res_chq["date_on_chq"];
        $Amount = $res_chq["amount"];
        $TypeOfPay = $res_chq["type_pay"];
        $DoDate = $res_chq["do_date"];
        $PayTo = $res_chq["pay_to"];
    }

    $qry_chq2=pg_query("select * from account.\"ChequeAccs\" WHERE \"ac_id\"='$AcID' ");
    if($res_chq2=pg_fetch_array($qry_chq2)){
        $BankName = $res_chq2["bank_name"];
    }
    
    if(!empty($txt_add_to_detail)){
        $txt_add_to_detail .= "\n";
    }
    
    $txt_add_to_detail .= "เบิกเช็ค : เลขที่ $ChqID ธนาคาร $BankName วันที่บนเช็ค $DateOnChq ยอดเงิน ".number_format($Amount,2)." บาท";
}


if($chkradio == 1){
    $str_id = $list_user;
    $vc_detail = "$vc_detail";
}elseif($chkradio == 2){
    $str_id = $list_vender;
    $vc_detail = "$vc_detail\nREC#".$vendertxt;
}elseif($chkradio == 3){
    $str_id = "REC#";
    $vc_detail = "$vc_detail\nREC#".$othertxt;
}

if(!empty($vc_detail)){
    $vc_detail .= "\n$txt_add_to_detail";
}else{
    $vc_detail .= "$txt_add_to_detail";
}

$up_detail=pg_query("UPDATE account.\"VoucherDetails\" SET \"vc_detail\"='$vc_detail' WHERE \"vc_id\"='$vcid'");
if(!$up_detail){
    $status++;
}

$up_sql=pg_query("UPDATE account.\"VoucherDetails\" SET \"receipt_id\"='$str_id',\"recp_date\"='$nowdate' WHERE \"vc_id\"='$vcid'");
if(!$up_sql){
    $status++;
}

if($status == 0){
    pg_query("COMMIT");
    //pg_query("ROLLBACK");
    $data['success'] = true;
    if($bt == 1){
        $data['message'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
    }else{
        $data['message'] = "บันทึกข้อมูลเรียบร้อยแล้ว\nกดปุ่มพิมพ์ $vcid เพื่อพิมพ์ใบสำคัญจ่าย";
    }
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้!";
}

echo json_encode($data);
    
}
?>