<?php
include_once("../include/config.php");
include_once("../include/function.php");

$cmd = $_REQUEST['cmd'];

if($cmd == "showdivdialog"){

$id = $_GET['id'];
$type = $_GET['type'];

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
    <td><b>Job ID</b></td>
    <td><?php echo $job_id; ?></td>
</tr>
<tr>
    <td><b>VC ID</b></td>
    <td><?php echo $id; ?></td>
</tr>
<tr>
    <td><b>วันที่</b></td>
    <td><?php echo $do_date; ?></td>
</tr>
<tr>
    <td valign="top"><b>เรื่อง</b></td>
    <td><textarea name="detail" id="detail" rows="6" cols="60"><?php echo $vc_detail; ?></textarea></td>
</tr>

<?php
if(!empty($chq_acc_no)){
?>
<tr>
    <td><b>เลขที่เช็ค</b></td>
    <td><?php echo $ChqID; ?></td>
</tr>
<tr>
    <td><b>ยอดเงินในเช็ค</b></td>
    <td><?php echo number_format($money,2); ?> บาท.</td>
</tr>
<?php
}else{
?>
<tr>
    <td><b>ยอดเบิก</b></td>
    <td><?php echo number_format($money,2); ?> บาท.</td>
</tr>
<?php
}
?>
<tr>
    <td><b>ยอดใช้ไป</b></td>
    <td><input type="text" name="moneypay" id="moneypay" size="20"> บาท.</td>
</tr>
</table>

<div align="right"><input type="button" name="btnsave" id="btnsave" value="บันทึก"></div>

<script type="text/javascript">
$('#btnsave').click(function(){
    $.post('voucher_rub_in.php',{
        cmd: 'savedialog',
        cid: '<?php echo $id; ?>',
        jobid: <?php echo $job_id; ?>,
        moneyold: <?php echo $money; ?>,
        moneypay: $('#moneypay').val(),
        detail: $('#detail').val()
    },
    function(data){
        if(data.success){
            $('#dialogedit').html(data.message);
            <?php
            if($type == 1){
            ?>
                $('#divShow').load('voucher_rub_api.php?type=1');
            <?php
            }else{
            ?>
                $('#divDateShow').load('voucher_rub_api.php?date='+ $('#datepicker').val());
            <?php
            }
            ?>     
        }else{
            alert(data.message);
        }
    },'json');
});
</script>

<?php
}

elseif($cmd == "savedialog"){
    
$user_id = $_SESSION["ss_iduser"];

$cid = $_POST['cid'];
$jobid = $_POST['jobid'];
$moneyold = $_POST['moneyold'];
$moneypay = $_POST['moneypay'];
$detail = $_POST['detail'];

$qry_old=pg_query("select * from account.\"VoucherDetails\" WHERE \"vc_id\"='$cid' ");
if($res_old=pg_fetch_array($qry_old)){
    $approve_id = $res_old["approve_id"];
    $receipt_id = $res_old["receipt_id"];
    $appv_date = $res_old["appv_date"];

    if( substr($approve_id,strlen($approve_id)-2,2) == "#P" ){
        $approve_id = substr($approve_id,0,strlen($approve_id)-2);
    }
}

pg_query("BEGIN WORK");
$status=0;
$print_stat = 0;
$msg_error = array();

if($moneyold < $moneypay){
    //เบิกเพิ่ม
    $moneyrs = $moneypay-$moneyold;

    $rs=@pg_query("select account.\"gen_no\"('$nowdate','VP')");
    $vp_id=@pg_fetch_result($rs,0);
    if(empty($vp_id)){
        $msg_error[] = "Error : gen_no VP";
        $status++;
    }

    $insert="insert into account.\"VoucherDetails\" (\"vc_id\",\"vc_detail\",\"marker_id\",\"approve_id\",\"receipt_id\",\"cash_amt\",\"chq_acc_no\",\"chque_no\",\"do_date\",\"job_id\",\"vc_type\",\"acb_id\",\"appv_date\",\"recp_date\") values ('$vp_id','เบิกเพิ่มของ VC ID:$cid','$user_id','$approve_id','$receipt_id','$moneyrs',DEFAULT,DEFAULT,'$nowdate','$jobid','P',DEFAULT,'$appv_date','$nowdate')";
    $rs_voucher=@pg_query($insert);
    if(!$rs_voucher){
        $msg_error[] = "Error : insert voucher 1";
        $status++;
    }
    
    $print_stat = 1;
    
}elseif($moneyold > $moneypay){
    //ทอนเงิน
    $moneyrs = $moneypay-$moneyold;

    $rs=@pg_query("select account.\"gen_no\"('$nowdate','VR')");
    $vr_id=@pg_fetch_result($rs,0);
    if(empty($vr_id)){
        $msg_error[] = "Error : gen_no VR";
        $status++;
    }

    $insert="insert into account.\"VoucherDetails\" (\"vc_id\",\"vc_detail\",\"marker_id\",\"approve_id\",\"receipt_id\",\"cash_amt\",\"chq_acc_no\",\"chque_no\",\"do_date\",\"job_id\",\"vc_type\",\"acb_id\",\"appv_date\",\"recp_date\") values ('$vr_id','เงินทอนของ VC ID:$cid','$user_id','$approve_id','$receipt_id','$moneyrs',DEFAULT,DEFAULT,'$nowdate','$jobid','R',DEFAULT,'$appv_date','$nowdate')";
    $rs_voucher=@pg_query($insert);
    if(!$rs_voucher){
        $msg_error[] = "Error : insert voucher 2";
        $status++;
    }
    
    $print_stat = 2;
    
}

$up_sql=@pg_query("UPDATE account.\"Vouchers\" SET \"vcp_finish\"='TRUE',\"end_date\"='$nowdate' WHERE \"job_id\"='$jobid'");
if(!$up_sql){
    $msg_error[] = "Error : UPDATE Vouchers 1";
    $status++;
}

$up_sql=@pg_query("UPDATE account.\"VoucherDetails\" SET \"vc_detail\"='$detail' WHERE \"vc_id\"='$cid'");
if(!$up_sql){
    $msg_error[] = "Error : UPDATE Vouchers 2";
    $status++;
}


if($status == 0){
    //pg_query("ROLLBACK");
    pg_query("COMMIT");
    $data['success'] = true;
    if($print_stat == 0){
        $data['message'] = "<div align=center>บันทึกข้อมูลเรียบร้อยแล้ว</div>";
    }elseif($print_stat == 1){
        $data['message'] = "<div align=center>บันทึกข้อมูลเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnprint\" id=\"btnprint\" value=\"พิมพ์ $vp_id\" onclick=\"javascript:window.open('voucher_print.php?id=$vp_id' , '33ffsf4f7e$vp_id','menuber=no,toolbar=yes,location=no,scrollbars=no, status=no,resizable=no,width=800,height=600')\"></div>";
    }elseif($print_stat == 2){
        $data['message'] = "<div align=center>บันทึกข้อมูลเรียบร้อยแล้ว<br /><br /><input type=\"button\" name=\"btnprint\" id=\"btnprint\" value=\"พิมพ์ $vr_id\" onclick=\"javascript:window.open('voucher_print.php?id=$vr_id' , '33ffsf4f7e$vr_id','menuber=no,toolbar=yes,location=no,scrollbars=no, status=no,resizable=no,width=800,height=600')\"></div>";
    }
}else{
    pg_query("ROLLBACK");
    $data['success'] = false;
    $data['message'] = "ไม่สามารถบันทึกได้!\n $msg_error[0]";
}

echo json_encode($data);
    
}
?>