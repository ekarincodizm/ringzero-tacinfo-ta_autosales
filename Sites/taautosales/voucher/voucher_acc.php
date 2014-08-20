<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    header("Refresh: 0; url=../index.php");
    exit();
}

$page_title = "Voucher - ลงบัญชี";
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

<style type="text/css">
.btline{
    font-weight: bold;
    border-style: dashed; border-width: 1px; border-color:#000000
}
</style>
    
</head>
<body>

<div class="roundedcornr_box" style="width:900px">
   <div class="roundedcornr_top"><div></div></div>
      <div class="roundedcornr_content">

<?php
include_once("../include/header_popup.php");
?>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">งานรอลงบัญชี</a></li>
        <li><a href="voucher_acc_api.php?cmd=div_finish">งานลงบัญชีแล้ว</a></li>
    </ul>
    <div id="tabs-1" style="padding:10px 5px 10px 5px;">

<div style="float:right">
<div style="font-size:10px; background-color:#D9FFD9; padding: 3px; width:100px; text-align:center; float:left">รายการรับเข้าแล้ว</div>
<div style="font-size:10px; background-color:#FFD5FF; padding: 3px; width:100px; text-align:center; float:left">รายการยังไม่รับเข้า</div>
<div style="font-size:10px; background-color:#FFFFCA; padding: 3px; width:100px; text-align:center; float:left">รายการลงบัญชีแล้ว</div>
</div>
<div style="clear:both"></div>

<form name="frm1" id="frm1" action="fvoucher_approve_insert.php" method="post">
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td>JobID</td>
    <td>รูปแบบ</td>
    <td>รหัส</td>
    <td>วันที่ทำรายการ</td>
    <td>รายละเอียด</td>
    <td>abh id</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$qry=pg_query("select * from account.\"VoucherDetails\" WHERE receipt_id is not null AND receipt_id <> 'cancel' AND acb_id is null ORDER BY \"job_id\",\"vc_id\" ASC");
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
    $autoid_abh = $res["acb_id"];
    
    $qry_finish=pg_query("select * from account.\"Vouchers\" WHERE \"job_id\"='$job_id' ");
    if($res_finish=pg_fetch_array($qry_finish)){
        $vcp_finish = $res_finish["vcp_finish"];
    }

    if($old_job != $job_id){
        if($old_vcp_finish=="t"){
            echo "<tr bgcolor=\"#D9FFD9\" class=\"btline\">";
        }else{
            echo "<tr bgcolor=\"#FFD5FF\" class=\"btline\">";
        }
        echo "<td colspan=6 align=right><input type=\"button\" name=\"btnadd\" id=\"btnadd\" value=\"ลงบัญชีรายการนี้\" onclick=\"javascript:add_ac('$old_job','$sum_sub','$str_vcid');\"> | ผลรวม JobID : $old_job</td>
        <td align=right>".number_format($sum_sub2,2)."</td>
        </tr>";
        $sum_sub = 0;
        $sum_sub2 = 0;
        $str_vcid = "";
    }
    /*
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
    */
    
    $money = $cash_amt;
    
    if( $chque_no != "" ){
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money += $res_chq["amount"];
        }
    }
    
$qry_ref=pg_query("select * from account.\"VoucherDetails\" WHERE \"approve_id\" is not null AND \"acb_id\" is not null AND \"job_id\"='$job_id' AND receipt_id <> 'cancel' ORDER BY \"vc_id\" ASC");
while($res_ref=pg_fetch_array($qry_ref)){
    $ref_vc_id = $res_ref["vc_id"];
    $ref_vc_detail = $res_ref["vc_detail"];
    $ref_do_date = $res_ref["do_date"];
    $ref_job_id = $res_ref["job_id"];
    $ref_cash_amt = $res_ref["cash_amt"];
    $ref_approve_id = $res_ref["approve_id"];
    $ref_chq_acc_no = $res_ref["chq_acc_no"];
    $ref_chque_no = $res_ref["chque_no"];
    $ref_autoid_abh = $res_ref["acb_id"];
    
    if(empty($ref_chq_acc_no)){
        $ref_money = $ref_cash_amt;
    }else{
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$ref_chq_acc_no' AND \"chq_id\"='$ref_chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $ref_money = $res_chq["amount"];
        }
    }
?>
<tr valign=top bgcolor="#FFFFCA">
    <td align="center"><?php echo "$ref_job_id"; ?></td>
    <td align="center"><?php if(empty($ref_chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $ref_vc_id; ?></td>
    <td align="center"><?php echo $ref_do_date; ?></td>
    <td><?php echo nl2br($ref_vc_detail); ?></td>
    <td align="center"><?php if( empty($ref_autoid_abh) ){ echo "ยังไม่ลงบัญชี"; }else{ echo "<a onclick=\"javascript:showdetail('$ref_autoid_abh')\" title=\"แสดงข้อมูล\"><b><u>$ref_autoid_abh</u></b></a>"; } ?></td>
    <td align="right"><?php echo number_format($ref_money,2); ?></td>
</tr>
<?php
    $sum_sub2+=$ref_money;
}
?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><?php echo "$job_id"; ?></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="center"><?php if( empty($autoid_abh) ){ echo "ยังไม่ลงบัญชี"; }else{ echo "$autoid_abh"; } ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
</tr>
<?php
    if( empty($autoid_abh) ){
        $str_vcid .= "$vc_id|";
        $sum_sub+=$money;
    }
    $old_job = $job_id;
    $old_vcp_finish = $vcp_finish;
    $sum_sub2+=$money;
}

if($j > 0){
    
        if($old_vcp_finish=="t"){
            echo "<tr bgcolor=\"#D9FFD9\" class=\"btline\">";
        }else{
            echo "<tr bgcolor=\"#FFD5FF\" class=\"btline\">";
        }
?>
    <td colspan=6 align=right><input type="button" name="btnadd" id="btnadd" value="ลงบัญชีรายการนี้" onclick="javascript:add_ac('<?php echo $old_job; ?>','<?php echo $sum_sub; ?>','<?php echo $str_vcid; ?>');"> | ผลรวม JobID : <?php echo $old_job; ?></td>
    <td align=right><?php echo number_format($sum_sub2,2); ?></td>
</tr>
<?php } ?>
</table>
</form>

    </div>
</div>
          
        </div>
   <div class="roundedcornr_bottom"><div></div></div>
</div>

<script type="text/javascript">

$(function(){
    $( "#tabs" ).tabs({
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html("ไม่สามารถโหลดเนื้อหาได้");
            }
        }
    });
});

function add_ac(jobid,money,vcid){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
    $('#dialogedit').load('voucher_acc_api.php?cmd=add_acc&jobid='+jobid+'&money='+money+'&vcid='+vcid);
    $('#dialogedit').dialog({
        title: 'บันทึกบัญชี JobID : '+jobid,
        resizable: false,
        modal: true,  
        width: 900,
        height: 450,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}

function showdetail(id){
    $('body').append('<div id="dialogedit"></div>');
    $('#dialogedit').html('<img src="../images/progress.gif" border="0" width="32" height="32" alt="กำลังโหลด...">');
    $('#dialogedit').load('voucher_acc_api.php?cmd=div_detail&id='+id);
    $('#dialogedit').dialog({
        title: 'แสดงบัญชี Abh ID : '+id,
        resizable: false,
        modal: false,  
        width: 600,
        height: 200,
        close: function(ev, ui){
            $('#dialogedit').remove();
        }
    });
}
</script>
    
</body>
</html>