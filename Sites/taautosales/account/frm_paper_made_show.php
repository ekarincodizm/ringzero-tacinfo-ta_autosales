<?php
include_once("../include/config.php");
include_once("../include/function.php");

if(!CheckAuth()){
    exit();
}

$yy = $_GET['yy'];
$ty = $_GET['ty'];
$mm = $_GET['mm'];
$trimas = $_GET['trimas'];
$month = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฏาคม', '08'=>'สิงหาคม' ,'09'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
$month_shot = array('1'=>'มกราคม', '2'=>'กุมภาพันธ์', '3'=>'มีนาคม', '4'=>'เมษายน', '5'=>'พฤษภาคม', '6'=>'มิถุนายน', '7'=>'กรกฏาคม', '8'=>'สิงหาคม' ,'9'=>'กันยายน' ,'10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');

if(empty($ty)){
    echo "กรุณาเลือกรายการแสดง !";
    exit;
}

if($ty == 1){
?>

<table width="100%" cellpadding="5" cellspacing="1" border="0" bgcolor="#D0D0D0">
<tr bgcolor="#6AB5FF" style="font-weight:bold">
    <td>รหัสบัญชี</td>
    <td>ยอดยกมา</td>
    <td>BAL (<?php echo "$month[$mm]"; ?>)</td>
</tr>
<?php
$qry = pg_query("SELECT * FROM account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res=pg_fetch_array($qry)){
    $AcID = $res['AcID'];
    $AcName = $res['AcName'];

    $set_mm = (int)$mm;
    for($i=1;$i<=$set_mm;$i++){

    $qry_view = pg_query("SELECT \"acb_date\",\"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' ORDER BY \"acb_date\" ASC ");
    while($res_view=pg_fetch_array($qry_view)){
        $acb_date = $res_view['acb_date'];
            if(strlen($i) == 1){ $i = "0".$i; }else{ $i = $i; }
            if(substr($acb_date,0,7) != "$yy-$i"){ continue; } //ตรวจสอบ หากไม่ใช่ เดือน/ปี ที่ต้องการ ให้ข้ามไป

            
        $type_acb = $res_view['type_acb'];
        $AmtDr = $res_view['AmtDr'];
        $AmtCr = $res_view['AmtCr'];
        
        if($i != "01" AND $type_acb == "AA"){ continue; }
        
        $sum += ($AmtDr-$AmtCr);
        $sum_all += ($AmtDr-$AmtCr);
        
        if( $type_acb == "AA" ){
            $sum_up += ($AmtDr-$AmtCr);
            $sum_up_all += ($AmtDr-$AmtCr);
        }

    }

    }
?>
<tr style="font-size:11px" bgcolor="#ffffff">
    <td><?php echo "$AcID : $AcName"; ?></td>
    <td align="right"><?php echo number_format($sum_up,2); ?></td>
    <td align="right"><?php echo number_format($sum,2); ?></td>
</tr>
<?php
    $sum = 0;
    $sum_up = 0;
}
?>
<tr bgcolor="#FFCECE">
    <td align="right"><b>ผลรวม</b></td>
    <td align="right"><?php echo number_format($sum_up_all,2); ?></td>
    <td align="right"><?php echo number_format($sum_all,2); ?></td>
</tr>
</table>

<?php
}elseif($ty == 2){
?>
<table width="100%" cellpadding="5" cellspacing="1" border="0" bgcolor="#D0D0D0">
<tr bgcolor="#6AB5FF" style="font-weight:bold; text-align:center">
    <td>รหัสบัญชี</td>
    <td>ยอดยกมา</td>
<?php
if($trimas == 1){
    echo "<td>BAL<br />(มกราคม)</td><td>BAL<br />(กุมภาพันธ์)</td><td>BAL<br />(มีนาคม)</td>";
}elseif($trimas == 2){
    echo "<td>BAL<br />(เมษายน)</td><td>BAL<br />(พฤษภาคม)</td><td>BAL<br />(มิถุนายน)</td>";
}elseif($trimas == 3){
    echo "<td>BAL<br />(กรกฏาคม)</td><td>BAL<br />(สิงหาคม)</td><td>BAL<br />(กันยายน)</td>";
}elseif($trimas == 4){
    echo "<td>BAL<br />(ตุลาคม)</td><td>BAL<br />(พฤศจิกายน)</td><td>BAL<br />(ธันวาคม)</td>";
}
?>
</tr>

<?php
$qry = pg_query("SELECT \"AcID\",\"AcName\" FROM account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res=pg_fetch_array($qry)){
    $AcID = $res['AcID'];
    $AcName = $res['AcName'];

    echo "<tr style=\"font-size:11px\" bgcolor=\"#ffffff\">";
    echo "<td>$AcID : $AcName</td>";

    if($trimas == 1){
        $k = 1; $j = 3;
    }elseif($trimas == 2){
        $k = 4; $j = 6;
    }elseif($trimas == 3){
        $k = 7; $j = 9;
    }elseif($trimas == 4){
        $k = 10; $j = 12;
    }
    
for($i=$k;$i<=$j;$i++){
    $qry_view = pg_query("SELECT SUM(\"AmtDr\") AS ssdr,SUM(\"AmtCr\") AS sscr FROM account.\"VAccountBook\" WHERE \"type_acb\" = 'AA' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='$i' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
    if($res_view=pg_fetch_array($qry_view)){
        $ssdr = $res_view['ssdr'];
        $sscr = $res_view['sscr'];
        $sum_up += ($ssdr-$sscr);
        $sum_up_all += ($ssdr-$sscr);
    }
}
    
    echo "<td align=right>".number_format($sum_up,2)."</td>";
    $sum_up = 0;

    for($i=$k;$i<=$j;$i++){
        $qry_view = pg_query("SELECT \"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='$i' AND EXTRACT(YEAR FROM \"acb_date\")='$yy'");
        while($res_view=pg_fetch_array($qry_view)){
            $type_acb = $res_view['type_acb'];
            $AmtDr = $res_view['AmtDr'];
            $AmtCr = $res_view['AmtCr'];
            
            if($i != $k AND $type_acb == "AA"){
                continue;
            }
            
            $sum += ($AmtDr-$AmtCr);
            $sum_all[$i] += ($AmtDr-$AmtCr);
        }
        echo "<td align=right>".number_format($sum,2)."</td>";
    }
    $sum = 0;
    echo "</tr>";
}
?>

<tr bgcolor="#FFCECE">
    <td align="right"><b>ผลรวม</b></td>
    <td align="right"><?php echo number_format($sum_up_all,2); ?></td>
<?php
    for($i=$k;$i<=$j;$i++){
?>
    <td align="right"><?php echo number_format($sum_all[$i],2); ?></td>
<?php
    }
?>
</tr>
</table>

<?php
}elseif($ty == 3){
?>

<table width="100%" cellpadding="5" cellspacing="1" border="0" bgcolor="#D0D0D0">
<tr bgcolor="#6AB5FF" style="font-weight:bold; text-align:center">
    <td>รหัสบัญชี</td>
    <td>ยอดยกมา</td>
<?php
for($i=1;$i<=12;$i++){
    echo "<td>BAL<br />($month_shot[$i])</td>";
}
?>
</tr>

<?php
$qry = pg_query("SELECT \"AcID\",\"AcName\" FROM account.\"AcTable\" ORDER BY \"AcID\" ASC");
while($res=pg_fetch_array($qry)){
    $AcID = $res['AcID'];
    $AcName = $res['AcName'];

    echo "<tr style=\"font-size:11px\" bgcolor=\"#ffffff\">";
    echo "<td>$AcID : $AcName</td>";
    
    $qry_view = pg_query("SELECT \"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" = 'AA' AND \"AcID\"='$AcID' AND EXTRACT(MONTH FROM \"acb_date\")='01' AND EXTRACT(YEAR FROM \"acb_date\")='$yy' ");
    if($res_view=pg_fetch_array($qry_view)){
        //$acb_date = $res_view['acb_date'];
        //if(substr($acb_date,0,4) != "$yy"){ continue; } //ตรวจสอบ หากไม่ใช่ เดือน/ปี ที่ต้องการ ให้ข้ามไป
        
        $ssdr = $res_view['AmtDr'];
        $sscr = $res_view['AmtCr'];
        $sum_up += ($ssdr-$sscr);
        $sum_up_all += ($ssdr-$sscr);
    }

    echo "<td align=right>".number_format($sum_up,2)."</td>";
    $sum_up = 0;
    
    for($i=1;$i<=12;$i++){
        $qry_view2 = pg_query("SELECT \"acb_date\",\"type_acb\",\"AmtDr\",\"AmtCr\" FROM account.\"VAccountBook\" WHERE \"type_acb\" <> 'ZZ' AND \"AcID\"='$AcID' ");
        while($res_view2=pg_fetch_array($qry_view2)){
            
            $acb_date2 = $res_view2['acb_date'];
            if(strlen($i) == 1){ $i2 = "0".$i; }else{ $i2 = $i; }
            if(substr($acb_date2,0,7) != "$yy-$i2"){ continue; } //ตรวจสอบ หากไม่ใช่ เดือน/ปี ที่ต้องการ ให้ข้ามไป
            
            $type_acb = $res_view2['type_acb'];
            $AmtDr = $res_view2['AmtDr'];
            $AmtCr = $res_view2['AmtCr'];
            
            if($i != 1 AND $type_acb == "AA"){ //ตรวจสอบหากเป็นยอดยกมาของเดือนอื่นๆ ที่ไม่ใช่เดือน มกราคม ให้ข้ามไป
                continue;
            }
            
            $sum += ($AmtDr-$AmtCr);
            $sum_all[$i] += ($AmtDr-$AmtCr);
        }
        echo "<td align=right>".number_format($sum,2)."</td>";
    }
    $sum = 0;
    echo "</tr>";
}
?>

<tr bgcolor="#FFCECE">
    <td align="right"><b>ผลรวม</b></td>
    <td align="right"><?php echo number_format($sum_up_all,2); ?></td>
<?php
    for($i=1;$i<=12;$i++){
?>
    <td align="right"><?php echo number_format($sum_all[$i],2); ?></td>
<?php
    }
?>
</tr>

</table>
<?php
}
?>


<div align="right" style="margin:5px 0px 5px 0px">
<input type="button" name="btn_print" id="btn_print" value="พิมพ์รายงาน PDF" onclick="window.open('frm_paper_made_pdf<?php echo $ty; ?>.php?yy=<?php echo $yy; ?>&ty=<?php echo $ty; ?>&mm=<?php echo $mm; ?>&trimas=<?php echo $trimas; ?>','1d411d78s4seksi837dsd1','')">
</div>