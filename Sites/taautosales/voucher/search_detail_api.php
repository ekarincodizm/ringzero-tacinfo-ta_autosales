<?php
include_once("../include/config.php");
include_once("../include/function.php");

$txt_search = $_POST['txt_search'];

if(empty($txt_search)){
    echo "กรุณากรอกข้อมูล";
    exit;
}
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td>JobID</td>
    <td>รูปแบบ</td>
    <td>รหัส</td>
    <td>abh_id</td>
    <td>วันที่ทำรายการ</td>
    <td>รายละเอียด</td>
    <td>ยอดเงิน</td>
</tr>
<?php
$qry=pg_query("select * from account.\"VoucherDetails\" WHERE vc_detail like '%$txt_search%' ORDER BY job_id,vc_id ASC");
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
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
    
    if($old_job != $job_id){
        echo "<tr bgcolor=\"#D9FFD9\" class=\"btline\">
        <td colspan=6 align=right>ผลรวม JobID : $old_job</td>
        <td align=right>".number_format($sum_sub,2)."</td>
        </tr>";
        $sum_sub = 0;
    }
?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><?php echo $job_id; ?></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td align="center"><a href="javascript:showdetail('<?php echo $autoid_abh; ?>')" title="แสดงข้อมูล"><b><u><?php echo $autoid_abh; ?></u></b></a></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
</tr>
<?php
    $old_job = $job_id;
    $sum_sub+=$money;
}

if($j>0){
?>
<tr bgcolor="#D9FFD9" class="btline">
    <td colspan=6 align=right>ผลรวม JobID : <?php echo $old_job; ?></td>
    <td align=right><?php echo number_format($sum_sub,2); ?></td>
    
</tr>
<?php
}else{
    echo "<tr><td colspan=10 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>