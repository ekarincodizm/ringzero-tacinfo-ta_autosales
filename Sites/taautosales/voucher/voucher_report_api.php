<?php
include_once("../include/config.php");
include_once("../include/function.php");

$date = $_GET['date'];

$arr_maker = array();

$qry=pg_query("select marker_id from account.\"VoucherDetails\" WHERE \"receipt_id\" is not null AND \"recp_date\"='$date' ORDER BY \"job_id\",\"vc_id\" ASC");
while($res=pg_fetch_array($qry)){
    $arr_maker[] = $res["marker_id"];
}

if(count($arr_maker) == 0){
    echo "ไม่พบข้อมูล";
    exit;
}
if(count($arr_maker) > 1){
    $arr_maker = array_unique($arr_maker);
}
?>


<script type="text/javascript">
$(function(){
    $("#tabs").tabs();
});
</script>

<div id="tabs">
    <ul>
<?php
foreach($arr_maker as $maker_id){
    $maker_name = GetUserName($maker_id);
    echo "<li><a href=\"#tabs-$maker_id\">$maker_name</a></li>";
}
?>
    </ul>

<?php
foreach($arr_maker as $maker_id){
?>
<div id="tabs-<?php echo $maker_id; ?>" style="padding:0px;">
<div align="right" style="padding: 5px 5px 5px 5px"><a href="voucher_report_pdf.php?date=<?php echo $date; ?>&maker=<?php echo $maker_id; ?>" target="_blank"><u>พิมพ์รายงาน</u></a></div>

<table cellpadding="5" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td>JobID</td>
    <td>รูปแบบ</td>
    <td>รหัส</td>
    <td>วันที่ทำรายการ</td>
    <td>รายละเอียด</td>
    <td width="70">abh id</td>
    <td width="90">ยอดเงินสด</td>
    <td width="90">รับ</td>
    <td width="90">จ่าย</td>
    <td width="90">ยอดเช็ค</td>
</tr>
<?php
$j = 0;

$old_job = "";
$sum_sum = "";
$all_sum_sum = "";
$sum_sub1_plus = "";
$sum_sub1_lob = "";

$sum_all1 = "";
$sum_sub2 = "";
$sum_all2 = "";

$begin_jobid = "";
$begin_chq_acc_no = "";
$begin_vc_id = "";
$begin_do_date = "";
$begin_vc_detail = "";
$begin_autoid_abh = "";
$begin_cash_amt = "";
$begin_Amount = "";
$begin_chq_acc_no2 = "";
$begin_chque_no2 = "";

$sum_j = "";
$sum_r = "";

$qry=pg_query("select * from account.\"VoucherDetails\" WHERE marker_id='$maker_id' AND \"receipt_id\" is not null AND \"recp_date\"='$date' ORDER BY \"job_id\",\"vc_id\" ASC");
while($res=pg_fetch_array($qry)){
    $j++;
    $vc_id = $res["vc_id"];
    $vc_detail = $res["vc_detail"];
    $do_date = $res["do_date"];
    $job_id = $res["job_id"];
    $cash_amt = $res["cash_amt"];
    $approve_id = $res["approve_id"];
    $chq_acc_no = $res["chq_acc_no"];
    $chque_no = $res["chque_no"];
    $autoid_abh = $res["acb_id"];

if($j > 1){

    $color_row = ($begin_autoid_abh != "ยังไม่ลงบัญชี" ? '#D2FFD2' : '#FFFFFF');
    
if($old_job == $job_id){
?>
<tr valign=top bgcolor="<?php echo $color_row; ?>">
    <td align="center"><?php echo "$begin_jobid"; ?></td>
    <td align="center"><?php echo "$begin_chq_acc_no"; ?></td>
    <td align="center"><?php echo "$begin_vc_id"; ?></td>
    <td align="center"><?php echo "$begin_do_date"; ?></td>
    <td><?php echo nl2br($begin_vc_detail); ?></td>
    <td align="center"><?php echo "$begin_autoid_abh"; ?></td>

    <td align="right"><?php echo number_format($begin_cash_amt,2); ?></td>
    <td></td>
    <td></td>
    <td align="right"><?php echo number_format($begin_Amount,2); ?></td>
</tr>
<?php
}else{
    
    $sum_sum = $sum_sub1_plus+$sum_sub1_lob;
?>
<tr valign=top bgcolor="<?php echo $color_row; ?>">
    <td align="center"><?php echo "$begin_jobid"; ?></td>
    <td align="center"><?php echo "$begin_chq_acc_no"; ?></td>
    <td align="center"><?php echo "$begin_vc_id"; ?></td>
    <td align="center"><?php echo "$begin_do_date"; ?></td>
    <td><?php echo nl2br($begin_vc_detail); ?></td>
    <td align="center"><?php echo "$begin_autoid_abh"; ?></td>

    <td align="right"><?php echo number_format($begin_cash_amt,2); ?></td>
<?php
if($sum_sum >= 0){
    $sum_j += $sum_sum;
    echo "<td></td>";
    echo "<td align=\"right\">".number_format($sum_sum,2)."</td>";
}else{
    $sum_r += $sum_sum;
    echo "<td align=\"right\">".number_format($sum_sum,2)."</td>";
    echo "<td></td>";
}
?>
    <td align="right"><?php echo number_format($begin_Amount,2); ?></td>
    <tr><td colspan="10"></td></tr>
</tr>
<?php

    $all_sum_sum += $sum_sum;

    $sum_sum = 0;
    $sum_sub1_plus = 0;
    $sum_sub1_lob = 0;
}

}

if(empty($chq_acc_no)){
    $chq_acc_no_text = "เงินสด";
}else{
    $chq_acc_no_text = "เช็ค";
}

if( empty($autoid_abh) ){
    $autoid_abh_text = "ยังไม่ลงบัญชี";
}else{
    $autoid_abh_text = "$autoid_abh";
}

$Amount = 0;

if(empty($chq_acc_no)){
    if($cash_amt >= 0){
        $sum_sub1_plus+=$cash_amt;
    }else{
        $sum_sub1_lob+=$cash_amt;
    }
    $sum_all1+=$cash_amt;
}else{
    $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
    if($res_chq=pg_fetch_array($qry_chq)){
        $Amount = $res_chq["amount"];
    }
    $sum_sub2+=$Amount;
    $sum_all2+=$Amount;
}

$begin_jobid = $job_id;
$begin_chq_acc_no = $chq_acc_no_text;
$begin_vc_id = $vc_id;
$begin_do_date = $do_date;
$begin_vc_detail = $vc_detail;
$begin_autoid_abh = $autoid_abh_text;
$begin_cash_amt = $cash_amt;
$begin_Amount = $Amount;

$begin_chq_acc_no2 = $chq_acc_no;
$begin_chque_no2 = $chque_no;

$old_job = $job_id;
}

//แสดงรายการสุดท้าย
$sum_sum = $sum_sub1_plus+$sum_sub1_lob;

    $color_row = ($begin_autoid_abh != "ยังไม่ลงบัญชี" ? '#D2FFD2' : '#FFFFFF');
?>
<tr valign=top bgcolor="<?php echo $color_row; ?>">
    <td align="center"><?php echo "$begin_jobid"; ?></td>
    <td align="center"><?php echo "$begin_chq_acc_no"; ?></td>
    <td align="center"><?php echo "$begin_vc_id"; ?></td>
    <td align="center"><?php echo "$begin_do_date"; ?></td>
    <td><?php echo nl2br($begin_vc_detail); ?></td>
    <td align="center"><?php echo "$begin_autoid_abh"; ?></td>

    <td align="right"><?php echo number_format($begin_cash_amt,2); ?></td>
<?php
if($sum_sum >= 0){
    $sum_j += $sum_sum;
    echo "<td></td>";
    echo "<td align=\"right\">".number_format($sum_sum,2)."</td>";
}else{
    $sum_r += $sum_sum;
    echo "<td align=\"right\">".number_format($sum_sum,2)."</td>";
    echo "<td></td>";
}
?>
    <td align="right"><?php echo number_format($begin_Amount,2); ?></td>
</tr>
<?php

//แสดงสรุปผลรวมทั้งหมด
if($j == 0){
    echo "<tr><td colspan=10 align=center>- ไม่พบข้อมูล -</td></tr>";
}else{
?>

<tr bgcolor="#FFDAB9">
    <td colspan="4" style="color:red; font-weight:bold">รวมยอดเงินทั้งสิ้น <?php echo number_format($sum_all1+$sum_all2,2); ?> บาท.</td>
    <td colspan="2" align=right style=font-weight:bold>ผลรวม</td>
    <td align="right" style="font-weight:bold"><?php echo number_format($sum_all1,2); ?></td>
    <td align="right" style="font-weight:bold"><?php echo number_format($sum_r,2); ?></td>
    <td align="right" style="font-weight:bold"><?php echo number_format($sum_j,2); ?></td>
    <td align="right" style="font-weight:bold"><?php echo number_format($sum_all2,2); ?></td>
</tr>

<?php
}
?>
</table>
</div>
<?php
}
?>
</div>