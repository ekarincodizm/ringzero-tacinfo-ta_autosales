<?php
include_once("../include/config.php");
include_once("../include/function.php");

$type = $_GET['type'];

if($type == "1"){
?>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td width="10%">สถานะ</td>
    <td width="10%">รูปแบบ</td>
    <td width="10%">รหัส</td>
    <td width="30%">รายละเอียด</td>
    <td width="20%">ยอดเงิน</td>
    <td width="10%">วันทำรายการ</td>
    <td width="10%">JobID</td>
</tr>
<?php
$j=0;

$qry=pg_query("SELECT * FROM account.\"VoucherDetails\" INNER JOIN account.\"Vouchers\" 
ON account.\"Vouchers\".job_id = account.\"VoucherDetails\".job_id 
WHERE vcp_finish='false' AND receipt_id IS NOT NULL AND approve_id IS NOT NULL 
ORDER BY account.\"Vouchers\".job_id ASC");
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
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select \"amount\" from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><input type="button" name="btnEdit" id="btnEdit" value="ทำรายการ" onclick="javascript:editfill('<?php echo $vc_id; ?>');"></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td align="center"><?php echo $job_id; ?></td>
</tr>
<?php
}

if($j==0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>
<?php
}

else{
    $date = $_GET['date'];
?>
<div style="margin-top:10px; font-weight:bold">รายการ รออนุมัติ</div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td width="10%">สถานะ</td>
    <td width="10%">รูปแบบ</td>
    <td width="10%">รหัส</td>
    <td width="30%">รายละเอียด</td>
    <td width="20%">ยอดเงิน</td>
    <td width="10%">วันทำรายการ</td>
    <td width="10%">JobID</td>
</tr>
<?php
$j=0;

$qry=pg_query("select A.*,B.* from account.\"VoucherDetails\" A LEFT OUTER JOIN account.\"Vouchers\" B on A.\"job_id\" = B.\"job_id\" 
WHERE \"do_date\"='$date' AND \"approve_id\" IS NULL ORDER BY A.\"job_id\" ASC");
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
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select \"amount\" from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><span style="color:#FBCB0B">รออนุมัติ</span></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td align="center"><?php echo $job_id; ?></td>
</tr>
<?php
}

if($j==0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<div style="margin-top:10px; font-weight:bold">รายการ อนุมัติแล้วรอรับเงิน</div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td width="10%">สถานะ</td>
    <td width="10%">รูปแบบ</td>
    <td width="10%">รหัส</td>
    <td width="30%">รายละเอียด</td>
    <td width="20%">ยอดเงิน</td>
    <td width="10%">วันทำรายการ</td>
    <td width="10%">JobID</td>
</tr>
<?php
$j=0;

$qry=pg_query("select A.*,B.* from account.\"VoucherDetails\" A LEFT OUTER JOIN account.\"Vouchers\" B on A.\"job_id\" = B.\"job_id\" 
WHERE \"approve_id\" is not null AND \"receipt_id\" is null 
ORDER BY A.\"job_id\" ASC");
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
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select \"amount\" from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><span style="color:#FF9D9D">รอรับเงิน</span></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td align="center"><?php echo $job_id; ?></td>
</tr>
<?php
}

if($j==0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<div style="margin-top:10px; font-weight:bold">รายการ อนุมัติแล้วแต่ยังไม่จบ</div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td width="10%">สถานะ</td>
    <td width="10%">รูปแบบ</td>
    <td width="10%">รหัส</td>
    <td width="30%">รายละเอียด</td>
    <td width="20%">ยอดเงิน</td>
    <td width="10%">วันทำรายการ</td>
    <td width="10%">JobID</td>
</tr>
<?php
$j=0;
$qry=pg_query("select A.*,B.* from account.\"VoucherDetails\" A LEFT OUTER JOIN account.\"Vouchers\" B on A.\"job_id\" = B.\"job_id\" 
WHERE B.\"vcp_finish\"='false' AND \"receipt_id\" is not null AND \"do_date\"='$date' AND \"approve_id\" IS NOT NULL
ORDER BY A.\"job_id\" ASC");
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
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select \"amount\" from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
?>
<tr valign=top bgcolor="#FFFFFF">
    <td align="center"><input type="button" name="btnEdit" id="btnEdit" value="ทำรายการ" onclick="javascript:editfill('<?php echo $vc_id; ?>');"></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td align="center"><?php echo $job_id; ?></td>
</tr>
<?php
}

if($j==0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>


<div style="margin-top:10px; font-weight:bold">รายการ ที่ทำจบแล้ว</div>
<table cellpadding="3" cellspacing="1" border="0" width="100%" bgcolor="#F0F0F0">
<tr bgcolor="#6FB7FF" style="font-weight:bold; text-align:center">
    <td width="10%">สถานะ</td>
    <td width="10%">รูปแบบ</td>
    <td width="10%">รหัส</td>
    <td width="30%">รายละเอียด</td>
    <td width="20%">ยอดเงิน</td>
    <td width="10%">วันทำรายการ</td>
    <td width="10%">JobID</td>
</tr>
<?php
$j=0;
$qry=pg_query("select A.*,B.* from account.\"VoucherDetails\" A LEFT OUTER JOIN account.\"Vouchers\" B on A.\"job_id\" = B.\"job_id\" WHERE B.\"vcp_finish\"='true' AND \"receipt_id\" is not null AND \"do_date\"='$date' ORDER BY A.\"job_id\",A.\"vc_id\" ASC");
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
    $receipt_id = $res["receipt_id"];
    
    if(empty($chq_acc_no)){
        $money = $cash_amt;
    }else{
        $qry_chq=pg_query("select * from account.\"ChequeAccDetails\" WHERE \"ac_id\"='$chq_acc_no' AND \"chq_id\"='$chque_no'");
        if($res_chq=pg_fetch_array($qry_chq)){
            $money = $res_chq["amount"];
        }
    }
    
    if($receipt_id == 'cancel'){
        echo "<tr valign=top bgcolor=\"#FFCACA\">";
    }else{
        echo "<tr valign=top bgcolor=\"#FFFFFF\">";
    }
?>
    <td align="center"><span style="color:#008000">จบแล้ว</span></td>
    <td align="center"><?php if(empty($chq_acc_no)){ echo "เงินสด"; }else{ echo "เช็ค"; } ?></td>
    <td align="center"><?php echo $vc_id; ?></td>
    <td><?php echo nl2br($vc_detail); ?></td>
    <td align="right"><?php echo number_format($money,2); ?></td>
    <td align="center"><?php echo $do_date; ?></td>
    <td align="center"><?php echo $job_id; ?></td>
</tr>
<?php
}

if($j==0){
    echo "<tr><td colspan=7 align=center>- ไม่พบข้อมูล -</td></tr>";
}
?>
</table>

<?php
}
 
?>